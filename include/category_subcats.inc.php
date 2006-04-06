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

$categories = array();
$image_ids = array();

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

  $comment = null;
  if (isset($row['comment']))
  {
    $comment = strip_tags($row['comment']);
  }

  if (isset($image_id))
  {
    array_push(
      $categories,
      array(
        'category'    => $row['id'],
        'picture'     => $image_id,
        'name'        => $row['name'],
        'date_last'   => @$row['date_last'],
        'comment'     => $comment,
        'nb_images'   => $row['nb_images'],
        )
      );

    array_push($image_ids, $image_id);
  }

  unset($image_id);
}

if (count($image_ids) > 0)
{
  $thumbnail_src_of = array();

  $query = '
SELECT id, path, tn_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $thumbnail_src_of[$row['id']] =
      get_thumbnail_src($row['path'], @$row['tn_ext']);
  }
  
  if ($conf['subcatify'])
  {
    $template->set_filenames(
      array(
        'mainpage_categories' => 'mainpage_categories.tpl',
        )
      );

    $template->assign_block_vars('categories', array());
    
    foreach ($categories as $category)
    {
      $template->assign_block_vars(
        'categories.category',
        array(
          'SRC'   => $thumbnail_src_of[ $category['picture'] ],
          'ALT'   => $category['name'],
          'TITLE' => $lang['hint_category'],
          'ICON'  => get_icon(@$category['date_last']),
          
          'URL' => make_index_url(
            array(
              'category' => $category['category'],
              'cat_name' => $category['name'],
              )
            ),
          'NAME' => $category['name'],
          'NB_IMAGES' => $category['nb_images'],
          'DESCRIPTION' => @$category['comment'],
          )
        );
    }
  
    $template->assign_var_from_handle('CATEGORIES', 'mainpage_categories');
  }
  else
  {
    $template->assign_block_vars('thumbnails', array());
    // first line
    $template->assign_block_vars('thumbnails.line', array());
    // current row displayed
    $row_number = 0;
    
    foreach ($categories as $category)
    {
      $template->assign_block_vars(
        'thumbnails.line.thumbnail',
        array(
          'IMAGE'       => $thumbnail_src_of[ $category['picture'] ],
          'IMAGE_ALT'   => $category['name'],
          'IMAGE_TITLE' => $lang['hint_category'],
          'IMAGE_TS'    => get_icon(@$category['date_last']),
          
          'U_IMG_LINK'  => make_index_url(
            array(
              'category' => $category['category'],
              )
            ),
          'CLASS'       => 'thumbCat',
          )
        );

      $template->assign_block_vars(
        'thumbnails.line.thumbnail.category_name',
        array(
          'NAME' => $category['name']
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
}
?>