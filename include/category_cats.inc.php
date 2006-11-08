<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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
 * This file is included by the main page to show thumbnails for a category
 * that have only subcategories or to show recent categories
 *
 */

if ($page['section']=='recent_cats')
{
  $query = '
SELECT id,name,date_last,representative_picture_id,comment,nb_images,uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE date_last > SUBDATE(
    CURRENT_DATE,INTERVAL '.$user['recent_period'].' DAY
  )
  AND id NOT IN ('.$user['forbidden_categories'].')';
}
else
{
  $query = '
SELECT id,name,date_last,representative_picture_id,comment,nb_images
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.
  (!isset($page['category']) ? 'is NULL' : '= '.$page['category']).'
    AND id NOT IN ('.$user['forbidden_categories'].')
  ORDER BY rank
;';
}

$result = pwg_query($query);
$categories = array();
$image_ids = array();

while ($row = mysql_fetch_assoc($result))
{
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
    ON ic.category_id = c.id
  WHERE uppercats REGEXP \'(^|,)'.$row['id'].'(,|$)\'
    AND c.id NOT IN ('.$user['forbidden_categories'].')
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
  FROM '.CATEGORIES_TABLE.'
  WHERE uppercats REGEXP \'(^|,)'.$row['id'].'(,|$)\'
    AND id NOT IN ('.$user['forbidden_categories'].')
    AND representative_picture_id IS NOT NULL
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
  if ($conf['subcatify'])
  {
    $template->set_filenames(
      array(
        'mainpage_categories' => 'mainpage_categories.tpl',
        )
      );

    foreach ($categories as $category)
    {
      $comment = strip_tags(@$category['comment'], '<a><br><p><b><i><small><strong><font>');
      if ($page['section']=='recent_cats')
      {
        $name = get_cat_display_name_cache($category['uppercats'], null, false);
        $icon_ts = '';
      }
      else
      {
        $name = $category['name'];
        $icon_ts = get_icon(@$category['date_last']);
      }

      $template->assign_block_vars(
        'categories.category',
        array(
          'SRC'   => $thumbnail_src_of[$category['representative_picture_id']],
          'ALT'   => $category['name'],
          'TITLE' => $lang['hint_category'],
          'ICON'  => $icon_ts,

          'URL'   => make_index_url(
            array(
              'category' => $category['id'],
              'cat_name' => $category['name'],
              )
            ),
          'CAPTION_NB_IMAGES' => (($category['nb_images'] == 0) ? '' : sprintf("%d ".l10n('pictures'), $category['nb_images'])),
          'DESCRIPTION' => @$comment,
          'NAME'  => $name,
          )
        );
    }

    $template->assign_var_from_handle('CATEGORIES', 'mainpage_categories');
  }
  else
  {
    $template->set_filenames( array( 'thumbnails' => 'thumbnails.tpl',));
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
          'IMAGE_TITLE' => $lang['hint_category'],

          'U_IMG_LINK'  => make_index_url(
            array(
              'category' => $category['id'],
              'cat_name' => $category['name'],
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
            'IMAGE_TS'    => get_icon(@$category['date_last']),
           )
         );
      }
      $template->assign_block_vars(
        'thumbnails.line.thumbnail.category_name',
        array(
          'NAME' => $name
          )
        );

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

    $template->assign_var_from_handle('THUMBNAILS', 'thumbnails');
  }
}
?>
