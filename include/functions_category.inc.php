<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
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
function check_restrictions($category_id)
{
  global $user;

  if (in_array($category_id, explode(',', $user['forbidden_categories'])))
  {
    access_denied();
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
    if (isset($page['category']))
    {
      $query.= ' OR id_uppercat IN ('.$page['uppercats'].')';
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
  if (empty($row))
    return null;

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
  global $conf;
  if ( !( $conf['allow_html_descriptions'] and
          preg_match('/<(div|br|img).*>/i', $cat['comment']) ) )
  {
    $cat['comment'] = nl2br($cat['comment']);
  }

  $names = array();
  $query = '
SELECT name,id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.$cat['uppercats'].')
;';
  $result = pwg_query($query);
  while($row = mysql_fetch_array($result))
  {
    $names[$row['id']] = $row['name'];
  }

  // category names must be in the same order than uppercats list
  $cat['name'] = array();
  foreach (explode(',', $cat['uppercats']) as $cat_id)
  {
    $cat['name'][$cat_id] = $names[$cat_id];
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

// returns an array of image orders available for users/visitors
function get_category_preferred_image_orders()
{
  global $conf;
  return array(
    array(l10n('default_sort'), '', true),
    array(l10n('Average rate'), 'average_rate DESC', $conf['rate']),
    array(l10n('most_visited_cat'), 'hit DESC', true),
    array(l10n('Creation date'), 'date_creation DESC', true),
    array(l10n('Post date'), 'date_available DESC', true),
    array(l10n('File name'), 'file ASC', true)
  );
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
                                           null,
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
  if (!empty($result))
  {
    while ($row = mysql_fetch_array($result))
    {
      array_push($categories, $row);
    }
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

function rank_compare($a, $b)
{
  if ($a['rank'] == $b['rank'])
  {
    return 0;
  }

  return ($a['rank'] < $b['rank']) ? -1 : 1;
}
?>