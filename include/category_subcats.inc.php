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
 * This file is included by category.php to show thumbnails for a category
 * that have only subcategories
 * 
 */

$query = '
SELECT id, name, date_last, representative_picture_id
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat ';
if (!isset($page['cat']) or !is_numeric($page['cat']))
{
  $query.= 'is NULL';
}
else
{
  $query.= '= '.$page['cat'];
}
  $query.= '
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
        'date_last' => @$row['date_last']
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

  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
  
  foreach ($cat_thumbnails as $item)
  {
    $url_link = PHPWG_ROOT_PATH.'category.php?cat='.$row['id'];

    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'IMAGE' => $images[$item['picture']],
        'IMAGE_ALT' => $item['name'],
        'IMAGE_TITLE' => $lang['hint_category'],
        'IMAGE_TS' => get_icon(@$item['date_last']),
        'U_IMG_LINK' =>
          add_session_id(PHPWG_ROOT_PATH.'category.php?cat='.$item['category'])
        )
      );
    
    $template->assign_block_vars(
      'thumbnails.line.thumbnail.category_name',
      array(
        'NAME' => $item['name']
        )
      );
    
    // create a new line ?
    if (++$row_number == $user['nb_image_line'])
    {
      $template->assign_block_vars('thumbnails.line', array());
      $row_number = 0;
    }
  }
}
?>