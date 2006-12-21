<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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
 * This file is included by the main page to show thumbnails for the default
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
  while ($row = mysql_fetch_assoc($result))
  {
    $row['rank'] = $page['rank_of'][ $row['id'] ];

    array_push($pictures, $row);
  }

  usort($pictures, 'rank_compare');
}

// template thumbnail initialization
$template->set_filenames( array( 'thumbnails' => 'thumbnails.tpl',));
if (count($pictures) > 0)
{
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

trigger_action('loc_begin_index_thumbnails', $pictures);

foreach ($pictures as $row)
{
  $thumbnail_url = get_thumbnail_url($row);

  // message in title for the thumbnail
  $thumbnail_title = $row['file'];
  if (isset($row['filesize']))
  {
    $thumbnail_title .= ' : '.$row['filesize'].' KB';
  }

  // link on picture.php page
  $url = duplicate_picture_url(
        array(
          'image_id' => $row['id'],
          'image_file' => $row['file']
        ),
        array('start')
      );

  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'IMAGE'              => $thumbnail_url,
      'IMAGE_ALT'          => $row['file'],
      'IMAGE_TITLE'        => $thumbnail_title,
      'IMAGE_TS'           => get_icon($row['date_available']),

      'U_IMG_LINK'         => $url,

      'CLASS'              => 'thumbElmt',
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

    switch ($page['section'])
    {
      case 'best_rated' :
      {
        $name = '('.$row['average_rate'].') '.$name;
        break;
      }
      case 'most_visited' :
      {
        $name = '('.$row['hit'].') '.$name;
        break;
      }
      case 'search' :
      {
        $name = replace_search($name, $page['search']);
        break;
      }
    }

    $template->assign_block_vars(
      'thumbnails.line.thumbnail.element_name',
      array(
        'NAME' => $name
        )
      );
  }

  if ($user['show_nb_comments']
      and isset($page['category'])
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

  //plugins need to add/modify sth in this loop ?
  trigger_action('loc_index_thumbnail', $row, 'thumbnails.line.thumbnail' );

  // create a new line ?
  if (++$row_number == $user['nb_image_line'])
  {
    $template->assign_block_vars('thumbnails.line', array());
    $row_number = 0;
  }
}

trigger_action('loc_end_index_thumbnails', $pictures);
$template->assign_var_from_handle('THUMBNAILS', 'thumbnails');

pwg_debug('end include/category_default.inc.php');
?>
