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
SELECT id, name, date_last
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
// we must not show pictures of a forbidden category
if ($user['forbidden_categories'] != '')
{
  $query.= ' AND id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
  ORDER BY rank
;';
$result = pwg_query($query);

// template thumbnail initialization
if (mysql_num_rows($result) > 0)
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

while ($row = mysql_fetch_array($result))
{
  $query = '
SELECT path, tn_ext
  FROM '.CATEGORIES_TABLE.' AS c INNER JOIN '.IMAGES_TABLE.' AS i
    ON i.id = c.representative_picture_id
  WHERE uppercats REGEXP \'(^|,)'.$row['id'].'(,|$)\'';
  // we must not show pictures of a forbidden category
  if ($user['forbidden_categories'] != '')
  {
    $query.= '
    AND c.id NOT IN ('.$user['forbidden_categories'].')';
  }
  $query.= '
  ORDER BY RAND()
  LIMIT 0,1
;';
  $element_result = pwg_query($query);
  if (mysql_num_rows($element_result) == 0)
  {
    continue;
  }
  $element_row = mysql_fetch_array($element_result);

  $thumbnail_link = get_thumbnail_src($element_row['path'],
                                      @$element_row['tn_ext']);

  $thumbnail_title = $lang['hint_category'];

  $url_link = PHPWG_ROOT_PATH.'category.php?cat='.$row['id'];

  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'IMAGE'                 => $thumbnail_link,
      'IMAGE_ALT'             => $row['name'],
      'IMAGE_TITLE'           => $thumbnail_title,
      'IMAGE_NAME'            => '['.$row['name'].']',
      'IMAGE_TS'              => get_icon(@$row['date_last']),
      'IMAGE_STYLE'           => 'thumb_category',
        
      'U_IMG_LINK'            => add_session_id($url_link)
     )
   );
  $template->assign_block_vars('thumbnails.line.thumbnail.bullet',array());

  // create a new line ?
  if (++$row_number == $user['nb_image_line'])
  {
    $template->assign_block_vars('thumbnails.line', array());
    $row_number = 0;
  }
}
?>