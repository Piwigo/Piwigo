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
 * This file is included by the main page to show thumbnails for recent_cats
 * category
 *
 */

// FIXME: categories having no representant
// ($conf['allow_random_representative'] = true) are not displayed :-/

// retrieving categories recently update, ie containing pictures added
// recently. The calculated table field categories.date_last will be
// easier to use
$query = '
SELECT c.id AS category_id
       , uppercats
       , representative_picture_id
       , path
       , file
       , c.comment
       , tn_ext
       , nb_images
       , c.name AS cat_name
  FROM '.CATEGORIES_TABLE.' AS c
    INNER JOIN '.IMAGES_TABLE.' AS i ON i.id = c.representative_picture_id
  WHERE date_last > SUBDATE(
    CURRENT_DATE,INTERVAL '.$user['recent_period'].' DAY
  )';
if ( $user['forbidden_categories'] != '' )
{
  $query.= '
    AND c.id NOT IN ('.$user['forbidden_categories'].')';
}
$query.= '
;';
$result = pwg_query( $query );

if ($conf['subcatify'])
{
  $template->set_filenames(
    array(
      'mainpage_categories' => 'mainpage_categories.tpl',
      )
    );

  // template thumbnail initialization
  if (mysql_num_rows($result) > 0)
  {
    $template->assign_block_vars('categories', array());
  }

  $comment = null;
  if (isset($row['comment']))
  {
    $comment = strip_tags($row['comment'], '<a><br><p><b><i><small><strong><font>');
  }

  // for each category, we have to search a recent picture to display and
  // the name to display
  while ( $row = mysql_fetch_array( $result ) )
  {
    $template->assign_block_vars(
      'categories.category',
      array(
        'SRC'       => get_thumbnail_src($row['path'], @$row['tn_ext']),
        'ALT'   => $row['file'],
        'TITLE' => $lang['hint_category'],

        'URL'  => make_index_url(
          array(
            'category' => $row['category_id'],
            'cat_name' => $row['cat_name'],
            )
          ),
        'NAME' => get_cat_display_name_cache($row['uppercats'], null, false),
        'CAPTION_NB_IMAGES' => (($row['nb_images'] == 0) ? '' : sprintf("%d ".l10n('pictures'), $row['nb_images'])),
        'DESCRIPTION' => $comment,
        )
      );
  }

  $template->assign_var_from_handle('CATEGORIES', 'mainpage_categories');
}
else
{
  // template thumbnail initialization
  $template->set_filenames( array( 'thumbnails' => 'thumbnails.tpl',));
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
    $template->assign_block_vars(
      'thumbnails.line.thumbnail',
      array(
        'IMAGE'       => get_thumbnail_src($row['path'], @$row['tn_ext']),
        'IMAGE_ALT'   => $row['file'],
        'IMAGE_TITLE' => $lang['hint_category'],

        'U_IMG_LINK'  => make_index_url(
          array(
            'category' => $row['category_id'],
            'cat_name' => $row['cat_name'],
            )
          ),
        )
      );

    $template->assign_block_vars(
      'thumbnails.line.thumbnail.category_name',
      array(
        'NAME' => get_cat_display_name_cache($row['uppercats'], null, false),
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
  $template->assign_var_from_handle('THUMBNAILS', 'thumbnails');
}
?>