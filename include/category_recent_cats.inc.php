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
 * This file is included by category.php to show thumbnails for recent_cats
 * category
 * 
 */

// retrieving categories recently update, ie containing pictures added
// recently. The calculated table field categories.date_last will be
// easier to use
$query = '
SELECT c.id AS category_id,uppercats,representative_picture_id,path,file,tn_ext
  FROM '.CATEGORIES_TABLE.' AS c INNER JOIN '.IMAGES_TABLE.' AS i
    ON i.id = c.representative_picture_id
  WHERE date_last > SUBDATE(CURRENT_DATE
                            ,INTERVAL '.$user['recent_period'].' DAY)';
if ( $user['forbidden_categories'] != '' )
{
  $query.= '
    AND c.id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
;';
$result = pwg_query( $query );

// template thumbnail initialization
if (mysql_num_rows($result) > 0)
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

$old_level_separator = $conf['level_separator'];
$conf['level_separator'] = '<br />';
// for each category, we have to search a recent picture to display and
// the name to display
while ( $row = mysql_fetch_array( $result ) )
{
  $name = get_cat_display_name_cache($row['uppercats'], '', false);

  $thumbnail_src = get_thumbnail_src($row['path'], @$row['tn_ext']);
  
  $url_link = PHPWG_ROOT_PATH.'category.php?cat='.$row['category_id'];
  
  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'IMAGE'                   => $thumbnail_src,
      'IMAGE_ALT'               => $row['file'],
      'IMAGE_TITLE'             => $lang['hint_category'],
        
      'U_IMG_LINK'              => $url_link
      )
    );

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
$conf['level_separator'] = $old_level_separator;
?>