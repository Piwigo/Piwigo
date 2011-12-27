<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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
if (($conf['show_exif']) and (function_exists('read_exif_data')))
{
  $exif_mapping = array();
  foreach ($conf['show_exif_fields'] as $field)
  {
    $exif_mapping[$field] = $field;
  }

  $exif = get_exif_data($picture['current']['src_image']->get_path(), $exif_mapping);
  
  if (count($exif) > 0)
  {
    $tpl_meta = array(
        'TITLE' => l10n('EXIF Metadata'),
        'lines' => array(),
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
          $tpl_meta['lines'][$key] = $exif[$field];
        }
      }
      else
      {
        $tokens = explode(';', $field);
        if (isset($exif[$field]))
        {
          $key = $tokens[1];
          if (isset($lang['exif_field_'.$key]))
          {
            $key = $lang['exif_field_'.$key];
          }
          $tpl_meta['lines'][$key] = $exif[$field];
        }
      }
    }
    $template->append('metadata', $tpl_meta);
  }
}

if ($conf['show_iptc'])
{
  $iptc = get_iptc_data($picture['current']['src_image']->get_path(), $conf['show_iptc_mapping']);

  if (count($iptc) > 0)
  {
    $tpl_meta = array(
        'TITLE' => l10n('IPTC Metadata'),
        'lines' => array(),
      );

    foreach ($iptc as $field => $value)
    {
      $key = $field;
      if (isset($lang[$field]))
      {
        $key = $lang[$field];
      }
      $tpl_meta['lines'][$key] = $value;
    }
    $template->append('metadata', $tpl_meta);
  }
}


?>