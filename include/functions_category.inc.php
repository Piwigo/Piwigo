<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

/**
 * Provides functions to handle categories.
 *
 * 
 */

/**
 * Is the category accessible to the connected user ?
 *
 * Note : if the user is not authorized to see this category, page creation
 * ends (exit command in this function)
 *
 * @param int category id to verify
 * @return void
 */
function check_restrictions( $category_id )
{
  global $user,$lang;

  if ( in_array( $category_id, $user['restrictions'] ) )
  {
    echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
    echo '<a href="'.add_session_id( './category.php' ).'">';
    echo $lang['thumbnails'].'</a></div>';
    exit();
  }
}

/**
 * Checks whether the argument is a right parameter category id
 *
 * The argument is a right parameter if corresponds to one of these :
 *
 *  - is numeric and corresponds to a category in the database
 *  - equals 'fav' (for favorites)
 *  - equals 'search' (when the result of a search is displayed)
 *  - equals 'most_visited'
 *  - equals 'best_rated'
 *  - equals 'recent_pics'
 *  - equals 'recent_cats'
 *  - equals 'calendar'
 *  - equals 'list'
 *
 * The function fills the global var $page['cat'] and returns nothing
 *
 * @param mixed category id or special category name
 * @return void
 */
function check_cat_id( $cat )
{
  global $page;

  unset( $page['cat'] );
  if ( isset( $cat ) )
  {
    if ( isset( $page['plain_structure'][$cat] ) )
    {
      $page['cat'] = $cat;
    }
    else if ( is_numeric( $cat ) )
    {
      $query = 'SELECT id';
      $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id = '.$cat.';';
      $result = pwg_query( $query );
      if ( mysql_num_rows( $result ) != 0 )
      {
        $page['cat'] = $cat;
      }
    }
    if ( $cat == 'fav'
         or $cat == 'most_visited'
         or $cat == 'best_rated'
         or $cat == 'recent_pics'
         or $cat == 'recent_cats'
         or $cat == 'calendar' )
    {
      $page['cat'] = $cat;
    }
    if ($cat == 'search' and isset($_GET['search']))
    {
      $page['cat'] = $cat;
    }
    if ($cat == 'list'
        and isset($_GET['list'])
        and preg_match('/^\d+(,\d+)*$/', $_GET['list']))
    {
      $page['cat'] = 'list';
    }
  }
}

function get_categories_menu()
{
  global $page,$user;
  
  $infos = array('');
  
  $query = '
SELECT name,id,date_last,nb_images,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE 1 = 1'; // stupid but permit using AND after it !
  if (!$user['expand'])
  {
    $query.= '
    AND (id_uppercat is NULL';
    if (isset ($page['tab_expand']) and count($page['tab_expand']) > 0)
    {
      $query.= ' OR id_uppercat IN ('.implode(',',$page['tab_expand']).')';
    }
    $query.= ')';
  }
  if ($user['forbidden_categories'] != '')
  {
    $query.= '
    AND id NOT IN ('.$user['forbidden_categories'].')';
  }
  $query.= '
;';

  $result = pwg_query($query);
  $cats = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($cats, $row);
  }
  usort($cats, 'global_rank_compare');

  return get_html_menu_category($cats);
}

function count_user_total_images()
{
  global $user;

  $query = 'SELECT SUM(nb_images) AS total';
  $query.= ' FROM '.CATEGORIES_TABLE;
  if ( count( $user['restrictions'] ) > 0 )
    $query.= ' WHERE id NOT IN ('.$user['forbidden_categories'].')';
  $query.= ';';
 
  $row = mysql_fetch_array( pwg_query( $query ) );

  if ( !isset( $row['total'] ) ) $row['total'] = 0;
  return $row['total'];
}

/**
 * Retrieve informations about a category in the database
 *
 * Returns an array with following keys :
 *
 *  - comment
 *  - dir : directory, might be empty for virtual categories
 *  - name : an array with indexes from 0 (lowest cat name) to n (most
 *           uppercat name findable)
 *  - nb_images
 *  - id_uppercat
 *  - site_id
 *  - 
 *
 * @param int category id
 * @return array
 */
function get_cat_info( $id )
{
  $infos = array('nb_images','id_uppercat','comment','site_id'
                 ,'dir','date_last','uploadable','status','visible'
                 ,'representative_picture_id','uppercats','commentable');
  
  $query = '
SELECT '.implode(',', $infos).'
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$id.'
;';
  $row = mysql_fetch_array(pwg_query($query));

  $cat = array();
  foreach ($infos as $info)
  {
    if (isset($row[$info]))
    {
      $cat[$info] = $row[$info];
    }
    else
    {
      $cat[$info] = '';
    }
    // If the field is true or false, the variable is transformed into a
    // boolean value.
    if ($cat[$info] == 'true' or $cat[$info] == 'false')
    {
      $cat[$info] = get_boolean( $cat[$info] );
    }
  }
  $cat['comment'] = nl2br($cat['comment']);

  $cat['name'] = array();

  $query = '
SELECT name,id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.$cat['uppercats'].')
  ORDER BY id ASC
;';
  $result = pwg_query($query);
  while($row = mysql_fetch_array($result))
  {
    $cat['name'][$row['id']] = $row['name'];
  }
  
  return $cat;
}

// get_complete_dir returns the concatenation of get_site_url and
// get_local_dir
// Example : "pets > rex > 1_year_old" is on the the same site as the
// PhpWebGallery files and this category has 22 for identifier
// get_complete_dir(22) returns "./galleries/pets/rex/1_year_old/"
function get_complete_dir( $category_id )
{
  return get_site_url($category_id).get_local_dir($category_id);
}

// get_local_dir returns an array with complete path without the site url
// Example : "pets > rex > 1_year_old" is on the the same site as the
// PhpWebGallery files and this category has 22 for identifier
// get_local_dir(22) returns "pets/rex/1_year_old/"
function get_local_dir( $category_id )
{
  global $page;

  $uppercats = '';
  $local_dir = '';

  if ( isset( $page['plain_structure'][$category_id]['uppercats'] ) )
  {
    $uppercats = $page['plain_structure'][$category_id]['uppercats'];
  }
  else
  {
    $query = 'SELECT uppercats';
    $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id = '.$category_id;
    $query.= ';';
    $row = mysql_fetch_array( pwg_query( $query ) );
    $uppercats = $row['uppercats'];
  }

  $upper_array = explode( ',', $uppercats );

  $database_dirs = array();
  $query = 'SELECT id,dir';
  $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id IN ('.$uppercats.')';
  $query.= ';';
  $result = pwg_query( $query );
  while( $row = mysql_fetch_array( $result ) )
  {
    $database_dirs[$row['id']] = $row['dir'];
  }
  foreach ($upper_array as $id)
  {
    $local_dir.= $database_dirs[$id].'/';
  }

  return $local_dir;
}

// retrieving the site url : "http://domain.com/gallery/" or
// simply "./galleries/"
function get_site_url($category_id)
{
  global $page;

  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.' AS s,'.CATEGORIES_TABLE.' AS c
  WHERE s.id = c.site_id
    AND c.id = '.$category_id.'
;';
  $row = mysql_fetch_array(pwg_query($query));
  return $row['galleries_url'];
}

// initialize_category initializes ;-) the variables in relation
// with category :
// 1. calculation of the number of pictures in the category
// 2. determination of the SQL query part to ask to find the right category
//    $page['where'] is not the same if we are in
//       - simple category
//       - search result
//       - favorites displaying
//       - most visited pictures
//       - best rated pictures
//       - recent pictures
//       - defined list (used for random)
// 3. determination of the title of the page
// 4. creation of the navigation bar
function initialize_category( $calling_page = 'category' )
{
  pwg_debug( 'start initialize_category' );
  global $page,$lang,$user,$conf;

  if ( isset( $page['cat'] ) )
  {
    // $page['nb_image_page'] is the number of picture to display on this page
    // By default, it is the same as the $user['nb_image_page']
    $page['nb_image_page'] = $user['nb_image_page'];
    // $url is used to create the navigation bar
    $url = PHPWG_ROOT_PATH.'category.php?cat='.$page['cat'];
    if ( isset($page['expand']) ) $url.= '&amp;expand='.$page['expand'];
    // simple category
    if ( is_numeric( $page['cat'] ) )
    {
      $result = get_cat_info( $page['cat'] );
      $page['comment']        = $result['comment'];
      $page['cat_dir']        = $result['dir'];
      $page['cat_name']       = $result['name'];
      $page['cat_nb_images']  = $result['nb_images'];
      $page['cat_site_id']    = $result['site_id'];
      $page['cat_uploadable'] = $result['uploadable'];
      $page['cat_commentable'] = $result['commentable'];
      $page['uppercats']      = $result['uppercats'];
      $page['title'] = get_cat_display_name( $page['cat_name'],' - ','',false);
      $page['where'] = ' WHERE category_id = '.$page['cat'];
    }
    else
    {
      if ($page['cat'] == 'search'
          or $page['cat'] == 'most_visited'
          or $page['cat'] == 'recent_pics'
          or $page['cat'] == 'recent_cats'
          or $page['cat'] == 'best_rated'
          or $page['cat'] == 'calendar'
          or $page['cat'] == 'list')
      {
        // we must not show pictures of a forbidden category
        if ( $user['forbidden_categories'] != '' )
        {
          $forbidden = ' category_id NOT IN ';
          $forbidden.= '('.$user['forbidden_categories'].')';
        }
      }
      // search result
      if ( $page['cat'] == 'search' )
      {
        // analyze search string given in URL (created in search.php)
        $tokens = explode('|', $_GET['search']);

        if (isset($tokens[1]) and $tokens[1] == 'AND')
        {
          $search['mode'] = 'AND';
        }
        else
        {
          $search['mode'] = 'OR';
        }

        $search_tokens = explode(';', $tokens[0]);
        foreach ($search_tokens as $search_token)
        {
          $tokens = explode(':', $search_token);
          $field_name = $tokens[0];
          $field_content = $tokens[1];

          $tokens = explode('~', $tokens[1]);
          if (isset($tokens[1]))
          {
            $search['fields'][$field_name]['mode'] = $tokens[1];
          }
          else
          {
            $search['fields'][$field_name]['mode'] = '';
          }

          $search['fields'][$field_name]['words'] = array();
          $tokens = explode(',', $tokens[0]);
          foreach ($tokens as $token)
          {
            array_push($search['fields'][$field_name]['words'], $token);
          }
        }
        
        $page['title'] = $lang['search_result'];
        if ( $calling_page == 'picture' )
        {
          $page['title'].= ' : <span style="font-style:italic;">';
          $page['title'].= $_GET['search']."</span>";
        }

        // SQL where clauses are stored in $clauses array during query
        // construction
        $clauses = array();
        
        $textfields = array('file', 'name', 'comment', 'keywords', 'author');
        foreach ($textfields as $textfield)
        {
          if (isset($search['fields'][$textfield]))
          {
            $local_clauses = array();
            foreach ($search['fields'][$textfield]['words'] as $word)
            {
              array_push($local_clauses, $textfield." LIKE '%".$word."%'");
            }
            // adds brackets around where clauses
            array_walk($local_clauses,create_function('&$s','$s="(".$s.")";'));
            array_push($clauses,
                       implode(' '.$search['fields'][$textfield]['mode'].' ',
                               $local_clauses));
          }
        }

        if (isset($search['fields']['allwords']))
        {
          $fields = array('file', 'name', 'comment', 'keywords');
          // in the OR mode, request bust be :
          // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
          // OR (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
          //
          // in the AND mode :
          // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
          // AND (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
          $word_clauses = array();
          foreach ($search['fields']['allwords']['words'] as $word)
          {
            $field_clauses = array();
            foreach ($fields as $field)
            {
              array_push($field_clauses, $field." LIKE '%".$word."%'");
            }
            // adds brackets around where clauses
            array_push($word_clauses, implode(' OR ', $field_clauses));
          }
          array_walk($word_clauses, create_function('&$s','$s="(".$s.")";'));
          array_push($clauses,
                     implode(' '.$search['fields']['allwords']['mode'].' ',
                               $word_clauses));
        }

        $datefields = array('date_available', 'date_creation');
        foreach ($datefields as $datefield)
        {
          $key = $datefield;
          if (isset($search['fields'][$key]))
          {
            $local_clause = $datefield." = '";
            $local_clause.= str_replace('.', '-',
                                        $search['fields'][$key]['words'][0]);
            $local_clause.= "'";
            array_push($clauses, $local_clause);
          }

          foreach (array('after','before') as $suffix)
          {
            $key = $datefield.'-'.$suffix;
            if (isset($search['fields'][$key]))
            {
              $local_clause = $datefield;
              if ($suffix == 'after')
              {
                $local_clause.= ' >';
              }
              else
              {
                $local_clause.= ' <';
              }
              if (isset($search['fields'][$key]['mode'])
                  and $search['fields'][$key]['mode'] == 'inc')
              {
                $local_clause.= '=';
              }
              $local_clause.= " '";
              $local_clause.= str_replace('.', '-',
                                          $search['fields'][$key]['words'][0]);
              $local_clause.= "'";
              array_push($clauses, $local_clause);
            }
          }
        }

        if (isset($search['fields']['cat']))
        {
          if ($search['fields']['cat']['mode'] == 'sub_inc')
          {
            // searching all the categories id of sub-categories
            $search_cat_clauses = array();
            foreach ($search['fields']['cat']['words'] as $cat_id)
            {
              $local_clause = 'uppercats REGEXP \'(^|,)'.$cat_id.'(,|$)\'';
              array_push($search_cat_clauses, $local_clause);
            }
            array_walk($search_cat_clauses,
                       create_function('&$s', '$s = "(".$s.")";'));
            
            $query = '
SELECT DISTINCT(id) AS id
  FROM '.CATEGORIES_TABLE.'
  WHERE '.implode(' OR ', $search_cat_clauses).'
;';
            $result = pwg_query($query);
            $cat_ids = array();
            while ($row = mysql_fetch_array($result))
            {
              array_push($cat_ids, $row['id']);
            }
            $local_clause = 'category_id IN (';
            $local_clause.= implode(',',$cat_ids);
            $local_clause.= ')';
            array_push($clauses, $local_clause);
          }
          else
          {
            $local_clause = 'category_id IN (';
            $local_clause.= implode(',',$search['fields']['cat']['words']);
            $local_clause.= ')';
            array_push($clauses, $local_clause);
          }
        }

        // adds brackets around where clauses
        array_walk($clauses, create_function('&$s', '$s = "(".$s.")";'));
        $page['where'] = 'WHERE '.implode(' '.$search['mode'].' ', $clauses);
        if ( isset( $forbidden ) ) $page['where'].= ' AND '.$forbidden;

        $query = '
SELECT COUNT(DISTINCT(id)) AS nb_total_images
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  '.$page['where'].'
;';
        $url.= '&amp;search='.$_GET['search'];
      }
      // favorites displaying
      else if ( $page['cat'] == 'fav' )
      {
        $page['title'] = $lang['favorites'];

        $page['where'] = ', '.FAVORITES_TABLE.' AS fav';
        $page['where'].= ' WHERE user_id = '.$user['id'];
        $page['where'].= ' AND fav.image_id = id';
      
        $query = 'SELECT COUNT(*) AS nb_total_images';
        $query.= ' FROM '.FAVORITES_TABLE;
        $query.= ' WHERE user_id = '.$user['id'];
        $query.= ';';
      }
      // pictures within the short period
      else if ( $page['cat'] == 'recent_pics' )
      {
        $page['title'] = $lang['recent_pics_cat'];
        // We must find the date corresponding to :
        // today - $conf['periode_courte']
        $date = time() - 60*60*24*$user['recent_period'];
        $page['where'] = " WHERE date_available > '";
        $page['where'].= date( 'Y-m-d', $date )."'";
        if ( isset( $forbidden ) ) $page['where'].= ' AND '.$forbidden;

        $query = '
SELECT COUNT(DISTINCT(id)) AS nb_total_images
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
    ON id = ic.image_id
  '.$page['where'].'
;';
      }
      // categories containing recent pictures
      else if ( $page['cat'] == 'recent_cats' )
      {
        $page['title'] = $lang['recent_cats_cat'];
        $page['cat_nb_images'] = 0;
      }
      // most visited pictures
      else if ( $page['cat'] == 'most_visited' )
      {
        $page['title'] = $conf['top_number'].' '.$lang['most_visited_cat'];

        $page['where'] = 'WHERE hit > 0';
        if (isset($forbidden))
        {
          $page['where'] = "\n".'    AND '.$forbidden;
        }

        $conf['order_by'] = ' ORDER BY hit DESC, file ASC';
        $page['cat_nb_images'] = $conf['top_number'];
        if ( isset( $page['start'] )
             and ($page['start']+$user['nb_image_page']>=$conf['top_number']))
        {
          $page['nb_image_page'] = $conf['top_number'] - $page['start'];
        }
      }
      else if ( $page['cat'] == 'calendar' )
      {
        $page['cat_nb_images'] = 0;
        $page['title'] = $lang['calendar'];
        if (isset($_GET['year'])
            and preg_match('/^\d+$/', $_GET['year']))
        {
          $page['calendar_year'] = (int)$_GET['year'];
        }
        if (isset($_GET['month'])
            and preg_match('/^(\d+)\.(\d{2})$/', $_GET['month'], $matches))
        {
          $page['calendar_year'] = (int)$matches[1];
          $page['calendar_month'] = (int)$matches[2];
        }
        if (isset($_GET['day'])
            and preg_match('/^(\d+)\.(\d{2})\.(\d{2})$/',
                           $_GET['day'],
                           $matches))
        {
          $page['calendar_year'] = (int)$matches[1];
          $page['calendar_month'] = (int)$matches[2];
          $page['calendar_day'] = (int)$matches[3];
        }
        if (isset($page['calendar_year']))
        {
          $page['title'] .= ' (';
          if (isset($page['calendar_day']))
          {
            $unixdate = mktime(0,0,0,
                               $page['calendar_month'],
                               $page['calendar_day'],
                               $page['calendar_year']);
            $page['title'].= $lang['day'][date("w", $unixdate)];
            $page['title'].= ' '.$page['calendar_day'].', ';
          }
          if (isset($page['calendar_month']))
          {
            $page['title'] .= $lang['month'][$page['calendar_month']].' ';
          }
          $page['title'] .= $page['calendar_year'];
          $page['title'] .= ')';
        }
        
        $page['where'] = 'WHERE '.$conf['calendar_datefield'].' IS NOT NULL';
        if (isset($forbidden))
        {
          $page['where'].= ' AND '.$forbidden;
        }
      }
      else if ($page['cat'] == 'best_rated')
      {
        $page['title'] = $conf['top_number'].' '.$lang['best_rated_cat'];

        $page['where'] = ' WHERE average_rate IS NOT NULL';
        
        if (isset($forbidden))
        {
          $page['where'].= ' AND '.$forbidden;
        }

        $conf['order_by'] = ' ORDER BY average_rate DESC, id ASC';

        // $page['cat_nb_images'] equals $conf['top_number'] unless there
        // are less rated items
        $query ='
SELECT COUNT(1) AS count
  FROM '.IMAGES_TABLE.'
  '.$page['where'].'
;';
        $row = mysql_fetch_array(pwg_query($query));
        if ($row['count'] < $conf['top_number'])
        {
          $page['cat_nb_images'] = $row['count'];
        }
        else
        {
          $page['cat_nb_images'] = $conf['top_number'];
        }
        unset($query);
          

        if (isset($page['start'])
            and ($page['start']+$user['nb_image_page']>=$conf['top_number']))
        {
          $page['nb_image_page'] = $conf['top_number'] - $page['start'];
        }
      }
      else if ($page['cat'] == 'list')
      {
        $page['title'] = $lang['random_cat'];
          
        $page['where'] = 'WHERE 1=1';
        if (isset($forbidden))
        {
          $page['where'].= ' AND '.$forbidden;
        }
        $page['where'].= ' AND image_id IN ('.$_GET['list'].')';
        $page['cat_nb_images'] = count(explode(',', $_GET['list']));

        $url.= '&amp;list='.$_GET['list'];
      }

      if (isset($query))
      {
        $result = pwg_query( $query );
        $row = mysql_fetch_array( $result );
        $page['cat_nb_images'] = $row['nb_total_images'];
      }
    }
    if ( $calling_page == 'category' )
    {
      $page['navigation_bar'] =
        create_navigation_bar( $url, $page['cat_nb_images'], $page['start'],
                               $user['nb_image_page'], 'back' );
    }
  }
  else
  {
    $page['title'] = $lang['diapo_default_page_title'];
  }
  pwg_debug( 'end initialize_category' );
}

function display_select_categories($categories,
                                   $selecteds,
                                   $blockname,
                                   $fullname = true)
{
  global $template;

  foreach ($categories as $category)
  {
    $selected = '';
    if (in_array($category['id'], $selecteds))
    {
      $selected = ' selected="selected"';
    }

    if ($fullname)
    {
      $option = get_cat_display_name_cache($category['uppercats'],
                                           ' &rarr; ',
                                           '',
                                           false);
    }
    else
    {
      $option = str_repeat('&nbsp;',
                           (3 * substr_count($category['global_rank'], '.')));
      $option.= '- '.$category['name'];
    }
    
    $template->assign_block_vars(
      $blockname,
      array('SELECTED'=>$selected,
            'VALUE'=>$category['id'],
            'OPTION'=>$option
        ));
  }
}

function display_select_cat_wrapper($query, $selecteds, $blockname,
                                    $fullname = true)
{
  $result = pwg_query($query);
  $categories = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($categories, $row);
  }
  usort($categories, 'global_rank_compare');
  display_select_categories($categories, $selecteds, $blockname, $fullname);
}

/**
 * returns all subcategory identifiers of given category ids
 *
 * @param array ids
 * @return array
 */
function get_subcat_ids($ids)
{
  $query = '
SELECT DISTINCT(id)
  FROM '.CATEGORIES_TABLE.'
  WHERE ';
  foreach ($ids as $num => $category_id)
  {
    if ($num > 0)
    {
      $query.= '
    OR ';
    }
    $query.= 'uppercats REGEXP \'(^|,)'.$category_id.'(,|$)\'';
  }
  $query.= '
;';
  $result = pwg_query($query);

  $subcats = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($subcats, $row['id']);
  }
  return $subcats;
}

function global_rank_compare($a, $b)
{
  return strnatcasecmp($a['global_rank'], $b['global_rank']);
}
?>
