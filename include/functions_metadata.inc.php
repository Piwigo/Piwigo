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
 * returns informations from IPTC metadata, mapping is done at the beginning
 * of the function
 *
 * @param string $filename
 * @return array
 */
function get_iptc_data($filename, $map)
{
  $result = array();
  
  // Read IPTC data
  $iptc = array();
  
  $imginfo = array();
  getimagesize($filename, $imginfo);
  
  if (isset($imginfo['APP13']))
  {
    $iptc = iptcparse($imginfo['APP13']);
    if (is_array($iptc))
    {
      $rmap = array_flip($map);
      foreach (array_keys($rmap) as $iptc_key)
      {
        if (isset($iptc[$iptc_key][0]))
        {
          if ($iptc_key == '2#025')
          {
            $value = implode(',',
                             array_map('clean_iptc_value',$iptc[$iptc_key]));
          }
          else
          {
            $value = clean_iptc_value($iptc[$iptc_key][0]);
          }

          foreach (array_keys($map, $iptc_key) as $pwg_key)
          {
            $result[$pwg_key] = $value;
          }
        }
      }
    }
  }
  return $result;
}

/**
 * return a cleaned IPTC value
 *
 * @param string value
 * @return string
 */
function clean_iptc_value($value)
{
  // strip leading zeros (weird Kodak Scanner software)
  while ($value[0] == chr(0))
  {
    $value = substr($value, 1);
  }
  // remove binary nulls
  $value = str_replace(chr(0x00), ' ', $value);
  
  return $value;
}

/**
 * returns informations from EXIF metadata, mapping is done at the beginning
 * of the function
 *
 * @param string $filename
 * @return array
 */
function get_exif_data($filename, $map)
{
  $result = array();

  if (!function_exists('read_exif_data'))
  {
    die('Exif extension not available, admin should disable exif use');
  }
  
  // Read EXIF data
  if ($exif = @read_exif_data($filename))
  {
    foreach ($map as $key => $field)
    {
      if (strpos($field, ';') === false)
      {
        if (isset($exif[$field]))
        {
          $result[$key] = $exif[$field];
        }
      }
      else
      {
        $tokens = explode(';', $field);
        if (isset($exif[$tokens[0]][$tokens[1]]))
        {
          $result[$key] = $exif[$tokens[0]][$tokens[1]];
        }
      }
    }
  }

  return $result;
}
?>