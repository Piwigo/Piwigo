<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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
 * This file is included by the main page to show thumbnails for a category
 * that have only subcategories or to show recent categories
 *
 */

// $user['forbidden_categories'] including with USER_CACHE_CATEGORIES_TABLE
$query = '
SELECT
    c.*,
    user_representative_picture_id,
    nb_images,
    date_last,
    max_date_last,
    count_images,
    count_categories
  FROM '.CATEGORIES_TABLE.' c
    INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ucc ON id = cat_id AND user_id = '.$user['id'];

if ('recent_cats' == $page['section'])
{
  $query.= '
  WHERE date_last >= '.pwg_db_get_recent_period_expression($user['recent_period']);
}
else
{
  $query.= '
  WHERE id_uppercat '.(!isset($page['category']) ? 'is NULL' : '= '.$page['category']['id']);
}

$query.= '
    '.get_sql_condition_FandF(
  array(
    'visible_categories' => 'id',
    ),
  'AND'
  );

if ('recent_cats' != $page['section'])
{
  $query.= '
  ORDER BY rank';
}

$query.= '
;';

$result = pwg_query($query);
$categories = array();
$category_ids = array();
$image_ids = array();
$user_representative_updates_for = array();

while ($row = pwg_db_fetch_assoc($result))
{
  $row['is_child_date_last'] = @$row['max_date_last']>@$row['date_last'];

  if (!empty($row['user_representative_picture_id']))
  {
    $image_id = $row['user_representative_picture_id'];
  }
  else if (!empty($row['representative_picture_id']))
  { // if a representative picture is set, it has priority
    $image_id = $row['representative_picture_id'];
  }
  else if ($conf['allow_random_representative'])
  {
    // searching a random representant among elements in sub-categories
    $image_id = get_random_image_in_category($row);
  }
  else
  { // searching a random representant among representant of sub-categories
    if ($row['count_categories']>0 and $row['count_images']>0)
    {
      $query = '
  SELECT representative_picture_id
    FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.'
    ON id = cat_id and user_id = '.$user['id'].'
    WHERE uppercats LIKE \''.$row['uppercats'].',%\'
      AND representative_picture_id IS NOT NULL'
    .get_sql_condition_FandF
    (
      array
        (
          'visible_categories' => 'id',
        ),
      "\n  AND"
    ).'
    ORDER BY '.DB_RANDOM_FUNCTION.'()
    LIMIT 1
  ;';
      $subresult = pwg_query($query);
      if (pwg_db_num_rows($subresult) > 0)
      {
        list($image_id) = pwg_db_fetch_row($subresult);
      }
    }
  }

  if (isset($image_id))
  {
    if ($conf['representative_cache_on_subcats'] and $row['user_representative_picture_id'] != $image_id)
    {
      $user_representative_updates_for[ $user['id'].'#'.$row['id'] ] = $image_id;
    }
    
    $row['representative_picture_id'] = $image_id;
    array_push($image_ids, $image_id);
    array_push($categories, $row);
    array_push($category_ids, $row['id']);
  }
  unset($image_id);
}

if ($conf['display_fromto'])
{
  $dates_of_category = array();
  if (count($category_ids) > 0)
  {
    $query = '
SELECT
    category_id,
    MIN(date_creation) AS date_creation_min,
    MAX(date_creation) AS date_creation_max
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE category_id IN ('.implode(',', $category_ids).')
'.get_sql_condition_FandF
  (
    array
      (
        'visible_categories' => 'category_id',
        'visible_images' => 'id'
      ),
    'AND'
  ).'
  GROUP BY category_id
;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $dates_of_category[ $row['category_id'] ] = array(
        'from' => $row['date_creation_min'],
        'to'   => $row['date_creation_max'],
        );
    }
  }
}

if ($page['section']=='recent_cats')
{
  usort($categories, 'global_rank_compare');
}
if (count($categories) > 0)
{
  $infos_of_image = array();
  $new_image_ids = array();

  $query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if ($row['level'] <= $user['level'])
    {
      $row['tn_src'] = DerivativeImage::thumb_url($row);
      $infos_of_image[$row['id']] = $row;
    }
    else
    {
      // problem: we must not display the thumbnail of a photo which has a
      // higher privacy level than user privacy level
      //
      // * what is the represented category?
      // * find a random photo matching user permissions
      // * register it at user_representative_picture_id
      // * set it as the representative_picture_id for the category

      foreach ($categories as &$category)
      {
        if ($row['id'] == $category['representative_picture_id'])
        {
          // searching a random representant among elements in sub-categories
          $image_id = get_random_image_in_category($category);

          if (isset($image_id) and !in_array($image_id, $image_ids))
          {
            array_push($new_image_ids, $image_id);
          }

          if ($conf['representative_cache_on_level'])
          {
            $user_representative_updates_for[ $user['id'].'#'.$category['id'] ] = $image_id;
          }
          
          $category['representative_picture_id'] = $image_id;
        }
      }
      unset($category);
    }
  }

  if (count($new_image_ids) > 0)
  {
    $query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $new_image_ids).')
;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $row['tn_src'] =  DerivativeImage::thumb_url($row);
      $infos_of_image[$row['id']] = $row;
    }
  }
}

if (count($user_representative_updates_for))
{
  $updates = array();
  
  foreach ($user_representative_updates_for as $user_cat => $image_id)
  {
    list($user_id, $cat_id) = explode('#', $user_cat);
    
    array_push(
      $updates,
      array(
        'user_id' => $user_id,
        'cat_id' => $cat_id,
        'user_representative_picture_id' => $image_id,
        )
      );
  }

  mass_updates(
    USER_CACHE_CATEGORIES_TABLE,
    array(
      'primary' => array('user_id', 'cat_id'),
      'update'  => array('user_representative_picture_id')
      ),
    $updates
    );
}

if (count($categories) > 0)
{
  // Update filtered data
  if (function_exists('update_cats_with_filtered_data'))
  {
    update_cats_with_filtered_data($categories);
  }

  $template->set_filename('index_category_thumbnails', 'mainpage_categories.tpl');

  trigger_action('loc_begin_index_category_thumbnails', $categories);

  $tpl_thumbnails_var = array();

  foreach ($categories as $category)
  {
    if (0 == $category['count_images'])
    {
      continue;
    }
    
    $category['name'] = trigger_event(
        'render_category_name',
        $category['name'],
        'subcatify_category_name'
        );

    if ($page['section']=='recent_cats')
    {
      $name = get_cat_display_name_cache($category['uppercats'], null, false);
    }
    else
    {
      $name = $category['name'];
    }

    $representative_infos = $infos_of_image[ $category['representative_picture_id'] ];

    $tpl_var =
        array(
          'ID'    => $category['id'],
          'TN_SRC'   => $representative_infos['tn_src'],
          'TN_ALT'   => strip_tags($category['name']),

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
                                    '<br>'
                                  ),
          'DESCRIPTION' =>
            trigger_event('render_category_literal_description',
              trigger_event('render_category_description',
                @$category['comment'],
                'subcatify_category_description')),
          'NAME'  => $name,
          
          // Extra fields for usage in extra themes
          'FILE_PATH' => $representative_infos['path'],
          'FILE_POSTED' => $representative_infos['date_available'],
          'FILE_CREATED' => $representative_infos['date_creation'],
          'FILE_DESC' => $representative_infos['comment'],
          'FILE_AUTHOR' => $representative_infos['author'],
          'FILE_HIT' => $representative_infos['hit'],
          'FILE_SIZE' => $representative_infos['filesize'],
          'FILE_WIDTH' => $representative_infos['width'],
          'FILE_HEIGHT' => $representative_infos['height'],
          'FILE_METADATE' => $representative_infos['date_metadata_update'],
          'FILE_HAS_HD' => $representative_infos['has_high'],
          'FILE_HD_WIDTH' => $representative_infos['high_width'],
          'FILE_HD_HEIGHT' => $representative_infos['high_height'],
          'FILE_HD_FILESIZE' => $representative_infos['high_filesize'],
          'FILE_RATING_SCORE' => $representative_infos['rating_score'],
        );
    if ($conf['index_new_icon'])
    {
      $tpl_var['icon_ts'] = get_icon($category['max_date_last'], $category['is_child_date_last']);
    }

    if ($conf['display_fromto'])
    {
      if (isset($dates_of_category[ $category['id'] ]))
      {
        $from = $dates_of_category[ $category['id'] ]['from'];
        $to   = $dates_of_category[ $category['id'] ]['to'];

        if (!empty($from))
        {
          $info = '';

          if ($from == $to)
          {
            $info = format_date($from);
          }
          else
          {
            $info = sprintf(
              l10n('from %s to %s'),
              format_date($from),
              format_date($to)
              );
          }
          $tpl_var['INFO_DATES'] = $info;
        }
      }
    }//fromto

    $tpl_thumbnails_var[] = $tpl_var;
  }

  $tpl_thumbnails_var = trigger_event('loc_end_index_category_thumbnails', $tpl_thumbnails_var, $categories);
  $template->assign( 'category_thumbnails', $tpl_thumbnails_var);

  $template->assign_var_from_handle('CATEGORIES', 'index_category_thumbnails');
}
pwg_debug('end include/category_cats.inc.php');
?>