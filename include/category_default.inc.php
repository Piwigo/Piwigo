<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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

  // define category slideshow url
  $row = reset($pictures);
  $page['cat_slideshow_url'] =
    add_url_params(
      duplicate_picture_url(
        array(
          'image_id' => $row['id'],
          'image_file' => $row['file']
        ),
        array('start')
      ),
      array('slideshow' =>
        (isset($_GET['slideshow']) ? $_GET['slideshow']
                                   : '' ))
    );
}

trigger_action('loc_begin_index_thumbnails', $pictures);

foreach ($pictures as $row)
{
  $thumbnail_url = get_thumbnail_url($row);

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
      'IMAGE_TITLE'        => get_thumbnail_title($row),
      'IMAGE_TS'           => get_icon($row['date_available']),

      'U_IMG_LINK'         => $url,

      'CLASS'              => 'thumbElmt',
      )
    );
  if ($user['show_nb_hits'])
  {
    $template->assign_block_vars(
      'thumbnails.line.thumbnail.nb_hits',
      array(
      'HITS'=> l10n_dec('%d hit', '%d hits', $row['hit']),
      'CLASS'=> set_span_class($row['hit']) . ' nb-hits',
      )
    );

  }

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
        if ( !$user['show_nb_hits']) {
          $name = '('.$row['hit'].') '.$name;
        }
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

  if ($user['show_nb_comments'])
  {
    $query = '
SELECT COUNT(*) AS nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$row['id'].'
    AND validated = \'true\'
;';
    list($row['nb_comments']) = mysql_fetch_array(pwg_query($query));
    $template->assign_block_vars(
      'thumbnails.line.thumbnail.nb_comments',
      array(
        'NB_COMMENTS'=> l10n_dec('%d comment', '%d comments',
                        $row['nb_comments']),
        'CLASS'=> set_span_class($row['nb_comments']) . ' nb-comments',
      )
    );
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
