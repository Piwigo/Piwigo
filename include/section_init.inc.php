<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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
 * This included page checks section related parameter and provides
 * following informations:
 *
 * - $page['title']
 *
 * - $page['items']: ordered list of items to display
 *
 */

// "index.php?/category/12-foo/start-24" or
// "index.php/category/12-foo/start-24"
// must return :
//
// array(
//   'section'  => 'categories',
//   'category' => array('id'=>12, ...),
//   'start'    => 24
//   );


$page['items'] = array();
$page['start'] = $page['startcat'] = 0;

// some ISPs set PATH_INFO to empty string or to SCRIPT_FILENAME while in the
// default apache implementation it is not set
if ( $conf['question_mark_in_urls']==false and
     isset($_SERVER["PATH_INFO"]) and !empty($_SERVER["PATH_INFO"]) )
{
  $rewritten = $_SERVER["PATH_INFO"];
  $rewritten = str_replace('//', '/', $rewritten);
  $path_count = count( explode('/', $rewritten) );
  $page['root_path'] = PHPWG_ROOT_PATH.str_repeat('../', $path_count-1);
}
else
{
  $rewritten = '';
  foreach (array_keys($_GET) as $keynum => $key)
  {
    $rewritten = $key;
    break;
  }

  // the $_GET keys are not protected in include/common.inc.php, only the values
  $rewritten = pwg_db_real_escape_string($rewritten);
  $page['root_path'] = PHPWG_ROOT_PATH;
}

if ( strncmp($page['root_path'], './', 2) == 0 )
{
  $page['root_path'] = substr($page['root_path'], 2);
}

// deleting first "/" if displayed
$tokens = explode('/', ltrim($rewritten, '/') );
// $tokens = array(
//   0 => category,
//   1 => 12-foo,
//   2 => start-24
//   );

$next_token = 0;

// +-----------------------------------------------------------------------+
// |                             picture page                              |
// +-----------------------------------------------------------------------+
// the first token must be the identifier for the picture
if (script_basename() == 'picture')
{
  // url compatibility with versions below 1.6
  if ( isset($_GET['image_id'])
       and isset($_GET['cat']) 
       and is_numeric($_GET['cat']) )
  {
    $url = make_picture_url( array(
        'section' => 'categories',
        'category' => get_cat_info($_GET['cat']),
        'image_id' => $_GET['image_id']
      ) );
    redirect($url);
  }
  
  $token = $tokens[$next_token];
  $next_token++;
  if ( is_numeric($token) )
  {
    $page['image_id'] = $token;
    if ($page['image_id']==0)
    {
      bad_request('invalid picture identifier');
    }
  }
  else
  {
    preg_match('/^(\d+-)?(.*)?$/', $token, $matches);
    if (isset($matches[1]) and is_numeric($matches[1]=rtrim($matches[1],'-')) )
    {
      $page['image_id'] = $matches[1];
      if ( !empty($matches[2]) )
      {
        $page['image_file'] = $matches[2];
      }
    }
    else
    {
      $page['image_id'] = 0; // more work in picture.php
      if ( !empty($matches[2]) )
      {
        $page['image_file'] = $matches[2];
      }
      else
      {
        bad_request('picture identifier is missing');
      }
    }
  }
}


$page = array_merge( $page, parse_section_url( $tokens, $next_token) );

if ( !isset($page['section']) )
{
  $page['section'] = 'categories';

  switch (script_basename())
  {
    case 'picture':
      break;
    case 'index':
    {
      // No section defined, go to random url
      if ( !empty($conf['random_index_redirect']) and empty($tokens[$next_token]) )
      {
        $random_index_redirect = array();
        foreach ($conf['random_index_redirect'] as $random_url => $random_url_condition)
        {
          if (empty($random_url_condition) or eval($random_url_condition))
          {
            $random_index_redirect[] = $random_url;
          }
        }
        if (!empty($random_index_redirect))
        {
          redirect($random_index_redirect[mt_rand(0, count($random_index_redirect)-1)]);
        }
      }
      $page['is_homepage'] = true;
      break;
    }
    default:
      trigger_error('script_basename "'.script_basename().'" unknown',
        E_USER_WARNING);
  }
}

$page = array_merge( $page, parse_well_known_params_url( $tokens, $next_token) );

//access a picture only by id, file or id-file without given section
if ( script_basename()=='picture' and 'categories'==$page['section'] and
      !isset($page['category']) and !isset($page['chronology_field']) )
{
  $page['flat'] = true;
}

// $page['nb_image_page'] is the number of picture to display on this page
// By default, it is the same as the $user['nb_image_page']
$page['nb_image_page'] = $user['nb_image_page'];

// if flat mode is active, we must consider the image set as a standard set
// and not as a category set because we can't use the #image_category.rank :
// displayed images are not directly linked to the displayed category
if ('categories' == $page['section'] and !isset($page['flat']))
{
  $conf['order_by'] = $conf['order_by_inside_category'];
}

if (pwg_get_session_var('image_order',0) > 0)
{
  $image_order_id = pwg_get_session_var('image_order');

  $orders = get_category_preferred_image_orders();

  // the current session stored image_order might be not compatible with
  // current image set, for example if the current image_order is the rank
  // and that we are displaying images related to a tag.
  //
  // In case of incompatibility, the session stored image_order is removed.
  if ($orders[$image_order_id][2])
  {
    $conf['order_by'] = str_replace(
        'ORDER BY ',
        'ORDER BY '.$orders[$image_order_id][1].',',
        $conf['order_by']
        );
    $page['super_order_by'] = true;
  }
  else
  {
    pwg_unset_session_var('image_order');
    $page['super_order_by'] = false;
  }
}

$forbidden = get_sql_condition_FandF(
      array(
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'id'
        ),
      'AND'
  );

// +-----------------------------------------------------------------------+
// |                              category                                 |
// +-----------------------------------------------------------------------+
if ('categories' == $page['section'])
{
  if (isset($page['category']))
  {
    $page = array_merge(
      $page, 
      array(
        'comment' => trigger_event(
            'render_category_description',
            $page['category']['comment'],
            'main_page_category_description'
            ),
        'title'   => get_cat_display_name($page['category']['upper_names'], '', false),
        )
      );
  }
  else
  {
    $page['title'] = ''; // will be set later
  }

  // GET IMAGES LIST
  if
    (
      $page['startcat'] == 0 and
      (!isset($page['chronology_field'])) and // otherwise the calendar will requery all subitems
      (
        (isset($page['category'])) or
        (isset($page['flat']))
      )
    )
  {
    if ( !empty($page['category']['image_order']) and !isset($page['super_order_by']) )
    {
      $conf[ 'order_by' ] = ' ORDER BY '.$page['category']['image_order'];
    }

    // flat categories mode
    if (isset($page['flat']))
    {
      // get all allowed sub-categories
      if ( isset($page['category']) )
      {
        $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE
    uppercats LIKE \''.$page['category']['uppercats'].',%\' '
    .get_sql_condition_FandF(
        array(
          'forbidden_categories' => 'id',
          'visible_categories' => 'id',
          ),
        "\n  AND"
        );
        
        $subcat_ids = array_from_query($query, 'id');
        $subcat_ids[] = $page['category']['id'];
        $where_sql = 'category_id IN ('.implode(',',$subcat_ids).')';
        // remove categories from forbidden because just checked above
        $forbidden = get_sql_condition_FandF(
              array( 'visible_images' => 'id' ),
              'AND'
          );
      }
      else
      {
        unset($page['is_homepage']);
        $where_sql = '1=1';
      }
    }
    // normal mode
    else
    {
      $where_sql = 'category_id = '.$page['category']['id'];
    }

    // main query
    $query = '
SELECT DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON id = image_id
  WHERE
    '.$where_sql.'
'.$forbidden.'
  '.$conf['order_by'].'
;';

    $page['items'] = array_from_query($query, 'image_id');
  }
}
// special sections
else
{
// +-----------------------------------------------------------------------+
// |                            tags section                               |
// +-----------------------------------------------------------------------+
  if ($page['section'] == 'tags')
  {
    $page['tag_ids'] = array();
    foreach ($page['tags'] as $tag)
    {
      array_push($page['tag_ids'], $tag['id']);
    }

    $items = get_image_ids_for_tags($page['tag_ids']);

    $page = array_merge(
      $page,
      array(
        'title' => get_tags_content_title(),
        'items' => $items,
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                           search section                              |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'search')
  {
    include_once( PHPWG_ROOT_PATH .'include/functions_search.inc.php' );

    $search_result = get_search_results($page['search'], @$page['super_order_by'] );
    //save the details of the query search
    if ( isset($search_result['qs']) )
    {
      $page['qsearch_details'] = $search_result['qs'];
    }

    $page = array_merge(
      $page,
      array(
        'items' => $search_result['items'],
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                    .l10n('Search results').'</a>',
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                           favorite section                            |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'favorites')
  {
    check_user_favorites();

    $page = array_merge(
      $page,
      array(
        'title' => l10n('Favorites')
      )
    );

    if (!empty($_GET['action']) && ($_GET['action'] == 'remove_all_from_favorites'))
    {
      $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
      pwg_query($query);
      redirect(make_index_url( array('section'=>'favorites') ));
    }
    else
    {
      $query = '
SELECT image_id
  FROM '.FAVORITES_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE user_id = '.$user['id'].'
'.get_sql_condition_FandF(
      array(
        'visible_images' => 'id'
        ),
      'AND'
      ).'
  '.$conf['order_by'].'
;';
      $page = array_merge(
        $page,
        array(
          'items' => array_from_query($query, 'image_id'),
          )
        );

      if (count($page['items'])>0)
      {
        $template->assign(
            'favorite',
            array(
              'U_FAVORITE' => add_url_params(
                  make_index_url( array('section'=>'favorites') ),
                  array('action'=>'remove_all_from_favorites')
                  ),
              )
            );
      }
    }
  }
// +-----------------------------------------------------------------------+
// |                       recent pictures section                         |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'recent_pics')
  {
    if ( !isset($page['super_order_by']) )
    {
      $conf['order_by'] = str_replace(
          'ORDER BY ',
          'ORDER BY date_available DESC,',
          $conf['order_by']
          );
    }

    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '
  .get_recent_photos_sql('date_available').'
  '.$forbidden
  .$conf['order_by'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                    .l10n('Recent photos').'</a>',
        'items' => array_from_query($query, 'id'),
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                 recently updated categories section                   |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'recent_cats')
  {
    $page = array_merge(
      $page,
      array(
        'title' => l10n('Recent albums'),
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                        most visited section                           |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'most_visited')
  {
    $page['super_order_by'] = true;
    $conf['order_by'] = ' ORDER BY hit DESC, id DESC';
    
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE hit > 0
    '.$forbidden.'
    '.$conf['order_by'].'
  LIMIT '.$conf['top_number'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                    .$conf['top_number'].' '.l10n('Most visited').'</a>',
        'items' => array_from_query($query, 'id'),
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                          best rated section                           |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'best_rated')
  {
    $page['super_order_by'] = true;
    $conf['order_by'] = ' ORDER BY rating_score DESC, id DESC';

    $query ='
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE rating_score IS NOT NULL
    '.$forbidden.'
    '.$conf['order_by'].'
  LIMIT '.$conf['top_number'].'
;';
    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                    .$conf['top_number'].' '.l10n('Best rated').'</a>',
        'items' => array_from_query($query, 'id'),
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                             list section                              |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'list')
  {
    $query ='
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE image_id IN ('.implode(',', $page['list']).')
    '.$forbidden.'
  '.$conf['order_by'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                    .l10n('Random photos').'</a>',
        'items' => array_from_query($query, 'id'),
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                             chronology                                |
// +-----------------------------------------------------------------------+
if (isset($page['chronology_field']))
{
  unset($page['is_homepage']);
  include_once( PHPWG_ROOT_PATH.'include/functions_calendar.inc.php' );
  initialize_calendar();
}

// title update
if (isset($page['title']))
{
  $page['section_title'] = '<a href="'.get_gallery_home_url().'">'.l10n('Home').'</a>';
  if (!empty($page['title']))
  {
    $page['section_title'] .= $conf['level_separator'].$page['title'];
  }
  else
  {
    $page['title'] = $page['section_title'];
  }
}

// add meta robots noindex, nofollow to avoid unnecesary robot crawls
$page['meta_robots']=array();
if ( isset($page['chronology_field'])
      or ( isset($page['flat']) and isset($page['category']) )
      or 'list'==$page['section'] or 'recent_pics'==$page['section'] )
{
  $page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
}
elseif ('tags'==$page['section'])
{
  if ( count($page['tag_ids'])>1 )
  {
    $page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
  }
}
elseif ('recent_cats'==$page['section'])
{
  $page['meta_robots']['noindex']=1;
}
elseif ('search'==$page['section'])
{
  $page['meta_robots']['nofollow']=1;
}
if ( $filter['enabled'] )
{
  $page['meta_robots']['noindex']=1;
}

// see if we need a redirect because of a permalink
if ( 'categories'==$page['section'] and isset($page['category']) )
{
  $need_redirect=false;
  if ( empty($page['category']['permalink']) )
  {
    if ( $conf['category_url_style'] == 'id-name' and
        @$page['hit_by']['cat_url_name'] !== str2url($page['category']['name']) )
    {
      $need_redirect=true;
    }
  }
  else
  {
    if ( $page['category']['permalink'] !== @$page['hit_by']['cat_permalink'] )
    {
      $need_redirect=true;
    }
  }

  if ($need_redirect)
  {
    $redirect_url = script_basename()=='picture' ? duplicate_picture_url() : duplicate_index_url();
    
    if (!headers_sent())
    { // this is a permanent redirection
      set_status_header(301);
      redirect_http( $redirect_url );
    }
    redirect( $redirect_url );
  }
  unset( $need_redirect, $page['hit_by'] );
}

trigger_action('loc_end_section_init');
?>