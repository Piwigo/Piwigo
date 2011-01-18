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
 * returns informations from IPTC metadata, mapping is done at the beginning
 * of the function
 *
 * @param string $filename
 * @return array
 */
function get_iptc_data($filename, $map)
{
  $result = array();

  $imginfo = array();
  if (false == @getimagesize($filename, $imginfo) )
  {
    return $result;
  }

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
  while ( isset($value[0]) and $value[0] == chr(0))
  {
    $value = substr($value, 1);
  }
  // remove binary nulls
  $value = str_replace(chr(0x00), ' ', $value);

  if ( preg_match('/[\x80-\xff]/', $value) )
  {
    // apparently mac uses some MacRoman crap encoding. I don't know
    // how to detect it so a plugin should do the trick.
    $value = trigger_event('clean_iptc_value', $value);
    $is_utf8 = seems_utf8($value);
    $value = convert_charset( $value,
      $is_utf8 ? 'utf-8' : 'iso-8859-1',
      get_pwg_charset() );
  }
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
    $exif = trigger_event('format_exif_data', $exif, $filename );
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