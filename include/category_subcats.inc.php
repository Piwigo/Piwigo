<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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
 * This file is included by the main page to show thumbnails for a category
 * that have only subcategories
 * 
 */

$query = '
SELECT id, name, date_last, representative_picture_id, comment, nb_images
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.
  (!isset($page['category']) ? 'is NULL' : '= '.$page['category']).'
    AND id NOT IN ('.$user['forbidden_categories'].')
  ORDER BY rank
;';
$result = pwg_query($query);

// $conf['allow_random_representative']

$cat_thumbnails = array();

while ($row = mysql_fetch_array($result))
{
  if (isset($row['representative_picture_id'])
      and is_numeric($row['representative_picture_id']))
  {
    // if a representative picture is set, it has priority
    $image_id = $row['representative_picture_id'];
  }
  else if ($conf['allow_random_representative'])
  {
    // searching a random representant among elements in sub-categories
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
    if (mysql_num_rows($result) > 0)
    {
      list($image_id) = mysql_fetch_row($subresult);
    }
  }
  else
  {
    // searching a random representant among representant of sub-categories
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
    array_push(
      $cat_thumbnails,
      array(
        'category' => $row['id'],
        'picture' => $image_id,
        'name' => $row['name'],
        'date_last' => @$row['date_last'],
        'comment' => @$row['comment'],
        'nb_images' => $row['nb_images'],
        )
      );
  }

  unset($image_id);
}

if (count($cat_thumbnails) > 0)
{
  $images = array();
  
  foreach ($cat_thumbnails as $item)
  {
    $images[$item['picture']] = '';
  }

  $query = '
SELECT id, path, tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($images)).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $images[$row['id']] = get_thumbnail_src($row['path'], @$row['tn_ext']);
  }

  $template->assign_block_vars('categories', array());
  
  foreach ($cat_thumbnails as $item)
  {
    $template->assign_block_vars(
      'categories.category',
      array(
        'SRC'   => $images[$item['picture']],
        'ALT'   => $item['name'],
        'TITLE' => $lang['hint_category'],
        'ICON'  => get_icon(@$item['date_last']),
        
        'URL' => make_index_url(
          array(
            'category' => $item['category'],
            )
          ),
        'NAME' => $item['name'],
        'NB_IMAGES' => $item['nb_images'],
        'DESCRIPTION' => @$item['comment'],
        )
      );
  }
}
?>
