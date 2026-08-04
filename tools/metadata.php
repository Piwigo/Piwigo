<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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
$exif = exif_read_data($filename);
echo '<pre>';
print_r($exif);
echo '</pre>';

#
#        Display XMP metadata using ImageMagick PHP extension
#

print "<h3>XMP data in '{$filename}'</h3><br />" ;
print ' (Requires Imagemagick PHP extension)<br />' ;
print '<pre>' ;

if( extension_loaded('imagick') && class_exists("Imagick") ){ //Check ImageMagick is installed

  //  create new Imagick object from image
  $sampleIM = new imagick($filename) ;

  //  get the XMP data
  $sampleXMP = $sampleIM -> getImageProperties("xmp:*") ;

  //  If there's data, then loop through the XMP array
  if ( count($sampleXMP) ) {
    foreach ($sampleXMP as $XMPname => $XMPproperty) {
      print "{$XMPname} => {$XMPproperty} <br />\n" ; 
    }
  }else{
    print 'No data <br /> ';
  }
  print '[end of XMP]' ;

}else{

  print 'ImageMagick not detected or disabled' ;
  
}

print '</pre>' ;


?>
