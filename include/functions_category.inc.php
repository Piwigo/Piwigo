<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

  // $filter['visible_categories'] and $filter['visible_images']
  // are not used because it's not necessary (filter <> restriction)
  if (in_array($category_id, explode(',', $user['forbidden_categories'])))
  {
    access_denied();
  }
}

function get_categories_menu()
{
  global $page, $user, $filter, $conf;

  $query = '
SELECT ';
  // From CATEGORIES_TABLE
  $query.= '
  id, name, permalink, nb_images, global_rank,';
  // From USER_CACHE_CATEGORIES_TABLE
  $query.= '
  date_last, max_date_last, count_images, count_categories';

  // $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
  $query.= '
FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'];

  // Always expand when filter is activated
  if (!$user['expand'] and !$filter['enabled'])
  {
    $where = '
(id_uppercat is NULL';
    if (isset($page['category']))
    {
      $where .= ' OR id_uppercat IN ('.$page['category']['uppercats'].')';
    }
    $where .= ')';
  }
  else
  {
    $where = '
  '.get_sql_condition_FandF
    (
      array
        (
          'visible_categories' => 'id',
        ),
      null,
      true
    );
  }

  $where = trigger_event('get_categories_menu_sql_where',
    $where, $user['expand'], $filter['enabled'] );

  $query.= '
WHERE '.$where.'
;';

  $result = pwg_query($query);
  $cats = array();
  $selected_category = isset($page['category']) ? $page['category'] : null;
  while ($row = pwg_db_fetch_assoc($result))
  {
    $child_date_last = @$row['max_date_last']> @$row['date_last'];
    $row = array_merge($row,
      array(
        'NAME' => trigger_event(
          'render_category_name',
          $row['name'],
          'get_categories_menu'
          ),
        'TITLE' => get_display_images_count(
          $row['nb_images'],
          $row['count_images'],
          $row['count_categories'],
          false,
          ' / '
          ),
        'URL' => make_index_url(array('category' => $row)),
        'LEVEL' => substr_count($row['global_rank'], '.') + 1,
        'SELECTED' => $selected_category['id'] == $row['id'] ? true : false,
        'IS_UPPERCAT' => $selected_category['id_uppercat'] == $row['id'] ? true : false,
        )
      );
    if ($conf['index_new_icon'])
    {
      $row['icon_ts'] = get_icon($row['max_date_last'], $child_date_last);
    }
    array_push($cats, $row);
    if ($row['id']==@$page['category']['id']) //save the number of subcats for later optim
      $page['category']['count_categories'] = $row['count_categories'];
  }
  usort($cats, 'global_rank_compare');

  // Update filtered data
  if (function_exists('update_cats_with_filtered_data'))
  {
    update_cats_with_filtered_data($cats);
  }

  return $cats;
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
  $query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$id.'
;';
  $cat = pwg_db_fetch_assoc(pwg_query($query));
  if (empty($cat))
    return null;

  foreach ($cat as $k => $v)
  {
    // If the field is true or false, the variable is transformed into a
    // boolean value.
    if ($cat[$k] == 'true' or $cat[$k] == 'false')
    {
      $cat[$k] = get_boolean( $cat[$k] );
    }
  }

  $upper_ids = explode(',', $cat['uppercats']);
  if ( count($upper_ids)==1 )
  {// no need to make a query for level 1
    $cat['upper_names'] = array(
        array(
          'id' => $cat['id'],
          'name' => $cat['name'],
          'permalink' => $cat['permalink'],
          )
      );
  }
  else
  {
    $names = array();
    $query = '
  SELECT id, name, permalink
    FROM '.CATEGORIES_TABLE.'
    WHERE id IN ('.$cat['uppercats'].')
  ;';
    $names = hash_from_query($query, 'id');

    // category names must be in the same order than uppercats list
    $cat['upper_names'] = array();
    foreach ($upper_ids as $cat_id)
    {
      array_push( $cat['upper_names'], $names[$cat_id]);
    }
  }
  return $cat;
}



// returns an array of image orders available for users/visitors
function get_category_preferred_image_orders()
{
  global $conf, $page;

  return trigger_event('get_category_preferred_image_orders',
    array(
    array(l10n('Default'), '', true),
    array(l10n('Rating score'), 'rating_score DESC', $conf['rate']),
    array(l10n('Most visited'), 'hit DESC', true),
    array(l10n('Creation date'), 'date_creation DESC', true),
    array(l10n('Post date'), 'date_available DESC', true),
    array(l10n('File name'), 'file ASC', true),
    array(
      l10n('Rank'),
      'rank ASC',
      ('categories' == @$page['section'] and !isset($page['flat']) and !isset($page['chronology_field']) )
      ),
    array( l10n('Permissions'), 'level DESC', is_admin() )
    ));
}

function display_select_categories($categories,
                                   $selecteds,
                                   $blockname,
                                   $fullname = true)
{
  global $template;

  $tpl_cats = array();
  foreach ($categories as $category)
  {
    if (!empty($category['permalink']))
    {
      $category['name'] .= ' &radic;';
    }
    if ($fullname)
    {
      $option = strip_tags(
        get_cat_display_name_cache(
          $category['uppercats'],
          null,
          false
          )
        );
    }
    else
    {
      $option = str_repeat('&nbsp;',
                           (3 * substr_count($category['global_rank'], '.')));
      $option.= '- ';
      $option.= strip_tags(
        trigger_event(
          'render_category_name',
          $category['name'],
          'display_select_categories'
          )
        );
    }
    $tpl_cats[ $category['id'] ] = $option;
  }

  $template->assign( $blockname, $tpl_cats);
  $template->assign( $blockname.'_selected', $selecteds);
}

function display_select_cat_wrapper($query, $selecteds, $blockname,
                                    $fullname = true)
{
  $result = pwg_query($query);
  $categories = array();
  if (!empty($result))
  {
    while ($row = pwg_db_fetch_assoc($result))
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
    is_numeric($category_id)
      or trigger_error(
        'get_subcat_ids expecting numeric, not '.gettype($category_id),
        E_USER_WARNING
      );
    if ($num > 0)
    {
      $query.= '
    OR ';
    }
    $query.= 'uppercats '.DB_REGEX_OPERATOR.' \'(^|,)'.$category_id.'(,|$)\'';
  }
  $query.= '
;';
  $result = pwg_query($query);

  $subcats = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($subcats, $row['id']);
  }
  return $subcats;
}

/** finds a matching category id from a potential list of permalinks
 * @param array permalinks example: holiday holiday/france holiday/france/paris
 * @param int idx - output of the index in $permalinks that matches
 * return category id or null if no match
 */
function get_cat_id_from_permalinks( $permalinks, &$idx )
{
  $in = '';
  foreach($permalinks as $permalink)
  {
    if ( !empty($in) ) $in.=', ';
    $in .= '\''.$permalink.'\'';
  }
  $query ='
SELECT cat_id AS id, permalink, 1 AS is_old
  FROM '.OLD_PERMALINKS_TABLE.'
  WHERE permalink IN ('.$in.')
UNION
SELECT id, permalink, 0 AS is_old
  FROM '.CATEGORIES_TABLE.'
  WHERE permalink IN ('.$in.')
;';
  $perma_hash = hash_from_query($query, 'permalink');

  if ( empty($perma_hash) )
    return null;
  for ($i=count($permalinks)-1; $i>=0; $i--)
  {
    if ( isset( $perma_hash[ $permalinks[$i] ] ) )
    {
      $idx = $i;
      $cat_id = $perma_hash[ $permalinks[$i] ]['id'];
      if ($perma_hash[ $permalinks[$i] ]['is_old'])
      {
        $query='
UPDATE '.OLD_PERMALINKS_TABLE.' SET last_hit=NOW(), hit=hit+1
  WHERE permalink=\''.$permalinks[$i].'\' AND cat_id='.$cat_id.'
  LIMIT 1';
        pwg_query($query);
      }
      return $cat_id;
    }
  }
  return null;
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

/**
 * returns display text for information images of category
 *
 * @param array categories
 * @return string
 */
function get_display_images_count($cat_nb_images, $cat_count_images, $cat_count_categories, $short_message = true, $Separator = '\n')
{
  $display_text = '';

  if ($cat_count_images > 0)
  {
    if ($cat_nb_images > 0 and $cat_nb_images < $cat_count_images)
    {
      $display_text.= get_display_images_count($cat_nb_images, $cat_nb_images, 0, $short_message, $Separator).$Separator;
      $cat_count_images-= $cat_nb_images;
      $cat_nb_images = 0;
    }

    //at least one image direct or indirect
    $display_text.= l10n_dec('%d photo', '%d photos', $cat_count_images);

    if ($cat_count_categories == 0 or $cat_nb_images == $cat_count_images)
    {
      //no descendant categories or descendants do not contain images
      if (! $short_message)
      {
        $display_text.= ' '.l10n('in this album');
      }
    }
    else
    {
      $display_text.= ' '.l10n_dec('in %d sub-album', 'in %d sub-albums', $cat_count_categories);
    }
  }

  return $display_text;
}

/**
 * Find a random photo among all photos below a given album in the tree (not
 * only photo directly associated to the album but also to sub-albums)
 *
 * we need $category['uppercats'], $category['id'], $category['count_images']
 */
function get_random_image_in_category($category, $recursive=true)
{
  $image_id = null;
  if ($category['count_images']>0)
  {
    $query = '
SELECT image_id
  FROM '.CATEGORIES_TABLE.' AS c
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.category_id = c.id
  WHERE ';
    if ($recursive)
    {
      $query.= '
    (c.id='.$category['id'].' OR uppercats LIKE \''.$category['uppercats'].',%\')';
    }
    else
    {
      $query.= '
    c.id='.$category['id'];
    }
    $query.= '
    '.get_sql_condition_FandF
    (
      array
        (
          'forbidden_categories' => 'c.id',
          'visible_categories' => 'c.id',
          'visible_images' => 'image_id',
        ),
      "\n  AND"
    ).'
  ORDER BY '.DB_RANDOM_FUNCTION.'()
  LIMIT 1
;';
    $result = pwg_query($query);
    if (pwg_db_num_rows($result) > 0)
    {
      list($image_id) = pwg_db_fetch_row($result);
    }
  }

  return $image_id;
}


?>