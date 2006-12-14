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
 * This file is included by the picture page to manage picture metadata
 *
 */

include_once(PHPWG_ROOT_PATH.'/include/functions_metadata.inc.php');
$template->assign_block_vars('metadata', array());
if ($conf['show_exif'])
{
  if (!function_exists('read_exif_data'))
  {
    die('Exif extension not available, admin should disable exif display');
  }

  if ($exif = @read_exif_data($picture['current']['image_path']))
  {
    $exif = trigger_event('format_exif_data', $exif, $picture['current'] );
    $template->assign_block_vars(
      'metadata.headline',
      array('TITLE' => 'EXIF Metadata')
      );

    foreach ($conf['show_exif_fields'] as $field)
    {
      if (strpos($field, ';') === false)
      {
        if (isset($exif[$field]))
        {
          $key = $field;
          if (isset($lang['exif_field_'.$field]))
          {
            $key = $lang['exif_field_'.$field];
          }

          $template->assign_block_vars(
            'metadata.line',
            array(
              'KEY' => $key,
              'VALUE' => $exif[$field]
              )
            );
        }
      }
      else
      {
        $tokens = explode(';', $field);
        if (isset($exif[$tokens[0]][$tokens[1]]))
        {
          $key = $tokens[1];
          if (isset($lang['exif_field_'.$tokens[1]]))
          {
            $key = $lang['exif_field_'.$tokens[1]];
          }

          $template->assign_block_vars(
            'metadata.line',
            array(
              'KEY' => $key,
              'VALUE' => $exif[$tokens[0]][$tokens[1]]
              )
            );
        }
      }
    }
  }
}
if ($conf['show_iptc'])
{
  $iptc = get_iptc_data($picture['current']['image_path'],
                        $conf['show_iptc_mapping']);

  if (count($iptc) > 0)
  {
    $template->assign_block_vars(
      'metadata.headline',
      array('TITLE' => 'IPTC Metadata')
      );
  }

  foreach ($iptc as $field => $value)
  {
    $key = $field;
    if (isset($lang[$field]))
    {
      $key = $lang[$field];
    }

    $template->assign_block_vars(
      'metadata.line',
      array(
        'KEY' => $key,
        'VALUE' => $value
        )
      );
  }
}


?>