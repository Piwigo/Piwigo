<?php
// +-----------------------------------------------------------------------+
// |                        category_default.inc.php                       |
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
 * This file is included by category.php to show thumbnails for the default
 * case
 * 
 */

/**
 * $array_cat_directories is a cache hash associating category id with their
 * complete directory
 */
$array_cat_directories = array();
  
$query = '
SELECT DISTINCT(id),file,date_available
       ,tn_ext,name,filesize,storage_category_id
  FROM '.IMAGES_TABLE.' AS i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id=ic.image_id
  '.$page['where'].'
  '.$conf['order_by'].'
  LIMIT '.$page['start'].','.$page['nb_image_page'].'
;';
// echo '<pre>'.$query.'</pre>';
$result = mysql_query($query);

// template thumbnail initialization
if ( mysql_num_rows($result) > 0 )
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

while ($row = mysql_fetch_array($result))
{
  // retrieving the storage dir of the picture
  if (!isset($array_cat_directories[$row['storage_category_id']]))
  {
    $array_cat_directories[$row['storage_category_id']] =
      get_complete_dir($row['storage_category_id']);
  }
  $cat_directory = $array_cat_directories[$row['storage_category_id']];

  $file = get_filename_wo_extension($row['file']);
  // name of the picture
  if (isset($row['name']) and $row['name'] != '')
  {
    $name = $row['name'];
  }
  else
  {
    $name = str_replace('_', ' ', $file);
  }

  if ($page['cat'] == 'search')
  {
    $name = replace_search($name, $_GET['search']);
  }
  // thumbnail url
  $thumbnail_url = $cat_directory;
  $thumbnail_url.= 'thumbnail/'.$conf['prefix_thumbnail'];
  $thumbnail_url.= $file.'.'.$row['tn_ext'];
  // message in title for the thumbnail
  $thumbnail_title = $row['file'];
  if ($row['filesize'] == '')
  {
    $filesize = floor(filesize($cat_directory.$row['file']) / 1024);
  }
  else
  {
    $filesize = $row['filesize'];
  }
  $thumbnail_title .= ' : '.$filesize.' KB';
  // url link on picture.php page
  $url_link = PHPWG_ROOT_PATH.'picture.php?cat='.$page['cat'];
  $url_link.= '&amp;image_id='.$row['id'];
  if ($page['cat'] == 'search')
  {
    $url_link.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
    
  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'IMAGE'              => $thumbnail_url,
      'IMAGE_ALT'          => $row['file'],
      'IMAGE_TITLE'        => $thumbnail_title,
      'IMAGE_NAME'         => $name,
      'IMAGE_TS'           => get_icon($row['date_available']),
      'IMAGE_STYLE'        => 'thumb_picture',
      
      'U_IMG_LINK'         => add_session_id($url_link)
      )
    );
    
  if ($conf['show_comments'] and $user['show_nb_comments'])
  {
    $query = '
SELECT COUNT(*) AS nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$row['id'].'
    AND validated = \'true\'
;';
    $row = mysql_fetch_array(mysql_query($query));
    $template->assign_block_vars(
      'thumbnails.line.thumbnail.nb_comments',
      array('NB_COMMENTS'=>$row['nb_comments']));
  }

  // create a new line ?
  if (++$row_number == $user['nb_image_line'])
  {
    $template->assign_block_vars('thumbnails.line', array());
    $row_number = 0;
  }
}
?>