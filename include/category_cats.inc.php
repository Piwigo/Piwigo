<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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
 * This file is included by the main page to show thumbnails for a category
 * that have only subcategories or to show recent categories
 *
 */

if ($page['section']=='recent_cats')
{
  // $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
  $query = '
SELECT
  id, name, permalink, representative_picture_id, comment, nb_images, uppercats,
  date_last, max_date_last, count_images, count_categories, global_rank
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'].'
  WHERE date_last >= SUBDATE(
    CURRENT_DATE,INTERVAL '.$user['recent_period'].' DAY
  )
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'id',
      ),
    'AND'
  ).'
;';
}
else
{
  // $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
  $query = '
SELECT
  id, name, permalink, representative_picture_id, comment, nb_images,
  date_last, max_date_last, count_images, count_categories
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'].'
  WHERE id_uppercat '.
  (!isset($page['category']) ? 'is NULL' : '= '.$page['category']['id']).'
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'id',
      ),
    'AND'
  ).'
  ORDER BY rank
;';
}

$result = pwg_query($query);
$categories = array();
$image_ids = array();

while ($row = mysql_fetch_assoc($result))
{
  $row['is_child_date_last'] = @$row['max_date_last']>@$row['date_last'];

  if (isset($row['representative_picture_id'])
      and is_numeric($row['representative_picture_id']))
  { // if a representative picture is set, it has priority
    $image_id = $row['representative_picture_id'];
  }
  else if ($conf['allow_random_representative'])
  {// searching a random representant among elements in sub-categories
    $query = '
SELECT image_id
  FROM '.CATEGORIES_TABLE.' AS c INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
    ON ic.category_id = c.id';
    $query.= '
  WHERE uppercats REGEXP \'(^|,)'.$row['id'].'(,|$)\'
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'c.id',
        'visible_categories' => 'c.id',
        'visible_images' => 'image_id'
      ),
    'AND'
  ).'
  ORDER BY RAND()
  LIMIT 0,1
;';
    $subresult = pwg_query($query);
    if (mysql_num_rows($subresult) > 0)
    {
      list($image_id) = mysql_fetch_row($subresult);
    }
  }
  else
  { // searching a random representant among representant of sub-categories
    $query = '
SELECT representative_picture_id
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
  ON id = cat_id and user_id = '.$user['id'].'
  WHERE uppercats REGEXP \'(^|,)'.$row['id'].'(,|$)\'
    AND representative_picture_id IS NOT NULL
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'id',
      ),
    'AND'
  ).'
  ORDER BY RAND()
  LIMIT 0,1
;';
    $subresult = pwg_query($query);
    if (mysql_num_rows($subresult) > 0)
    {
      list($image_id) = mysql_fetch_row($subresult);
    }
  }

  if (isset($image_id))
  {
    $row['representative_picture_id'] = $image_id;
    array_push($image_ids, $image_id);
    array_push($categories, $row);
  }
  unset($image_id);
}

if ($page['section']=='recent_cats')
{
  usort($categories, 'global_rank_compare');
}
if (count($categories) > 0)
{
  $thumbnail_src_of = array();

  $query = '
SELECT id, path, tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $thumbnail_src_of[$row['id']] = get_thumbnail_url($row);
  }
}

if (count($categories) > 0)
{
  // Update filtered data
  if (function_exists('update_cats_with_filtered_data'))
  {
    update_cats_with_filtered_data($categories);
  }

  trigger_action('loc_begin_index_category_thumbnails', $categories);
  if ($conf['subcatify'])
  {
    $template->set_filename('mainpage_categories', 'mainpage_categories.tpl');

    foreach ($categories as $category)
    {
      if ($page['section']=='recent_cats')
      {
        $name = get_cat_display_name_cache($category['uppercats'], null, false);
      }
      else
      {
        $name = $category['name'];
      }

      $icon_ts = get_icon($category['max_date_last'], $category['is_child_date_last']);

      $template->assign_block_vars(
        'categories.category',
        array(
          'SRC'   => $thumbnail_src_of[$category['representative_picture_id']],
          'ALT'   => $category['name'],
          'TITLE' => $lang['hint_category'],
          'ICON'  => $icon_ts,

          'URL'   => make_index_url(
            array(
              'category' => $category
              )
            ),
          'CAPTION_NB_IMAGES' => get_display_images_count
                                  (
                                    $category['nb_images'],
                                    $category['count_images'],
                                    $category['count_categories'],
                                    true,
                                    '<br />'
                                  ),
          'DESCRIPTION' =>
            trigger_event('render_category_literal_description',
              trigger_event('render_category_description',
                @$category['comment'])),
          'NAME'  => $name,
          )
        );

      //plugins need to add/modify sth in this loop ?
      trigger_action('loc_index_category_thumbnail',
        $category, 'categories.category' );
    }

    $template->assign_var_from_handle('CATEGORIES', 'mainpage_categories');
  }
  else
  {
    $template->set_filename( 'thumbnails', 'thumbnails.tpl');
    // first line
    $template->assign_block_vars('thumbnails.line', array());
    // current row displayed
    $row_number = 0;

    if ($page['section']=='recent_cats')
    {
      $old_level_separator = $conf['level_separator'];
      $conf['level_separator'] = '<br />';
    }

    foreach ($categories as $category)
    {
      $template->assign_block_vars(
        'thumbnails.line.thumbnail',
        array(
          'IMAGE'       => $thumbnail_src_of[ $category['representative_picture_id'] ],
          'IMAGE_ALT'   => $category['name'],
          'IMAGE_TITLE' => get_display_images_count
                                  (
                                    $category['nb_images'],
                                    $category['count_images'],
                                    $category['count_categories'],
                                    true,
                                    ' / '
                                  ),

          'U_IMG_LINK'  => make_index_url(
            array(
              'category' => $category
              )
            ),
          'CLASS'       => 'thumbCat',
          )
        );
      if ($page['section']=='recent_cats')
      {
        $name = get_cat_display_name_cache($category['uppercats'], null, false);
      }
      else
      {
        $name = $category['name'];
        $template->merge_block_vars(
          'thumbnails.line.thumbnail',
          array(
            'IMAGE_TS'    => get_icon($category['max_date_last'], $category['is_child_date_last']),
           )
         );
      }
      $template->assign_block_vars(
        'thumbnails.line.thumbnail.category_name',
        array(
          'NAME' => $name
          )
        );

      //plugins need to add/modify sth in this loop ?
      trigger_action('loc_index_category_thumbnail',
        $category, 'thumbnails.line.thumbnail' );

      // create a new line ?
      if (++$row_number == $user['nb_image_line'])
      {
        $template->assign_block_vars('thumbnails.line', array());
        $row_number = 0;
      }
    }

    if ( isset($old_level_separator) )
    {
      $conf['level_separator']=$old_level_separator;
    }

    $template->assign_var_from_handle('CATEGORIES', 'thumbnails');
    $template->delete_block_vars('thumbnails', true); // category_default reuse them
  }
  trigger_action('loc_end_index_category_thumbnails', $categories);
}
?>