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
 * This file is included by category.php to show thumbnails for the default
 * case
 * 
 */

$page['rank_of'] = array_flip($page['items']);

$pictures = array();

$selection = array_slice(
  $page['items'],
  $page['start'],
  $page['nb_image_page']
  );

if (count($selection) > 0)
{
  $query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $selection).')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $row['rank'] = $page['rank_of'][ $row['id'] ];
    
    array_push($pictures, $row);
  }

  usort($pictures, 'rank_compare');
}

// template thumbnail initialization
if (count($pictures) > 0)
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

foreach ($pictures as $row)
{
  $thumbnail_url = get_thumbnail_src($row['path'], @$row['tn_ext']);
  
  // message in title for the thumbnail
  $thumbnail_title = $row['file'];
  if (isset($row['filesize']))
  {
    $thumbnail_title .= ' : '.$row['filesize'].' KB';
  }
  
  // url link on picture.php page
  $url_link = PHPWG_ROOT_PATH.'picture.php?image_id='.$row['id'];

  if (isset($page['cat']))
  {
    $url_link.= 'cat='.$page['cat'].'&amp;';

    if ($page['cat'] == 'search')
    {
      $url_link.= '&amp;search='.$_GET['search'];
    }
    else if ($page['cat'] == 'list')
    {
      $url_link.= '&amp;list='.$_GET['list'];
    }
  }
  
  if (isset($_GET['calendar']))
  {
    $url_link.= '&amp;calendar='.$_GET['calendar'];
  }
    
  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'IMAGE'              => $thumbnail_url,
      'IMAGE_ALT'          => $row['file'],
      'IMAGE_TITLE'        => $thumbnail_title,
      'IMAGE_TS'           => get_icon($row['date_available']),
      
      'U_IMG_LINK'         => $url_link
      )
    );

  if ($conf['show_thumbnail_caption'])
  {
    // name of the picture
    if (isset($row['name']) and $row['name'] != '')
    {
      $name = $row['name'];
    }
    else
    {
      $name = str_replace('_', ' ', get_filename_wo_extension($row['file']));
    }
    if ($page['cat'] == 'best_rated')
    {
      $name = '('.$row['average_rate'].') '.$name;
    }
    else
    if ($page['cat'] == 'most_visited')
    {
      $name = '('.$row['hit'].') '.$name;
    }
    
    if ($page['cat'] == 'search')
    {
      $name = replace_search($name, $_GET['search']);
    }
  
    $template->assign_block_vars(
      'thumbnails.line.thumbnail.element_name',
      array(
        'NAME' => $name
        )
      );
  }
    
  if ($user['show_nb_comments']
      and is_numeric($page['cat'])
      and $page['cat_commentable'])
  {
    $query = '
SELECT COUNT(*) AS nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$row['id'].'
    AND validated = \'true\'
;';
    $row = mysql_fetch_array(pwg_query($query));
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

pwg_debug('end include/category_default.inc.php');
?>