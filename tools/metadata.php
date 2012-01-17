<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

$filename = 'sample.jpg';
echo 'Informations are read from '.$filename.'<br><br><br>';

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

  return $value;
}

$iptc_result = array();
$imginfo = array();
getimagesize($filename, $imginfo);
if (isset($imginfo['APP13']))
{
  $iptc = iptcparse($imginfo['APP13']);
  if (is_array($iptc))
  {
    foreach (array_keys($iptc) as $iptc_key)
    {
      if (isset($iptc[$iptc_key][0]))
      {
        if ($iptc_key == '2#025')
        {
          $value = implode(
            ',',
            array_map(
              'clean_iptc_value',
              $iptc[$iptc_key]
              )
            );
        }
        else
        {
          $value = clean_iptc_value($iptc[$iptc_key][0]);
        }
        
        $iptc_result[$iptc_key] = $value;
      }
    }
  }

  echo 'IPTC Fields in '.$filename.'<br>';
  $keys = array_keys($iptc_result);
  sort($keys);
  foreach ($keys as $key)
  {
    echo '<br>'.$key.' = '.$iptc_result[$key];
  }
}
else
{
  echo 'no IPTC information';
}

echo '<br><br><br>';
echo 'EXIF Fields in '.$filename.'<br>';
$exif = read_exif_data($filename);
echo '<pre>';
print_r($exif);
echo '</pre>';
?>