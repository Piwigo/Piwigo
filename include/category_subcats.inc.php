<?php
// +-----------------------------------------------------------------------+
// |                        category_subcats.inc.php                       |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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

$subcats = array();
if (isset($page['cat']))
{
  $subcats = get_non_empty_subcat_ids($page['cat']);
}
else
{
  $subcats = get_non_empty_subcat_ids('');
}

// template thumbnail initialization
if (count($subcats) > 0)
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}
  
foreach ($subcats as $subcat_id => $non_empty_id) 
{
  $name = $page['plain_structure'][$subcat_id]['name'];

  // searching the representative picture of the category
  $query = '
SELECT representative_picture_id
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$non_empty_id.'
;';
  $row = mysql_fetch_array(mysql_query($query));
    
  $query = '
SELECT file,tn_ext,storage_category_id
  FROM '.IMAGES_TABLE.', '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$non_empty_id.'
    AND id = image_id';
  // if the category has a representative picture, this is its thumbnail
  // that will be displayed !
  if (isset($row['representative_picture_id']))
  {   
    $query.= '
    AND id = '.$row['representative_picture_id'];
  }
  else
  {
    $query.= '
  ORDER BY RAND()
  LIMIT 0,1';
  }
  $query.= '
;';
  $image_result = mysql_query($query);
  $image_row    = mysql_fetch_array($image_result);

  $file = get_filename_wo_extension($image_row['file']);

  // creating links for thumbnail and associated category
  if (isset($image_row['tn_ext']) and $image_row['tn_ext'] != '')
  {
    $thumbnail_link = get_complete_dir($image_row['storage_category_id']);
    $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $thumbnail_link.= $file.'.'.$image_row['tn_ext'];
  }
  else
  {
    $thumbnail_link = './template/'.$user['template'].'/mimetypes/';
    $thumbnail_link.= strtolower(get_extension($image_row['file'])).'.png';
  }

  $thumbnail_title = $lang['hint_category'];

  $url_link = PHPWG_ROOT_PATH.'category.php?cat='.$subcat_id;

  $date = $page['plain_structure'][$subcat_id]['date_last'];

  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'IMAGE'                 => $thumbnail_link,
      'IMAGE_ALT'             => $image_row['file'],
      'IMAGE_TITLE'           => $thumbnail_title,
      'IMAGE_NAME'            => '['.$name.']',
      'IMAGE_TS'              => get_icon($date),
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