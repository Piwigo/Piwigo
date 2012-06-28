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

// +-----------------------------------------------------------------------+
// |                           Image Interface                             |
// +-----------------------------------------------------------------------+

// Define all needed methods for image class
interface imageInterface
{
  function get_width();

  function get_height();

  function set_compression_quality($quality);

  function crop($width, $height, $x, $y);

  function strip();

  function rotate($rotation);

  function resize($width, $height);

  function sharpen($amount);

  function compose($overlay, $x, $y, $opacity);

  function write($destination_filepath);
}

// +-----------------------------------------------------------------------+
// |                          Main Image Class                             |
// +-----------------------------------------------------------------------+

class pwg_image
{
  var $image;
  var $library = '';
  var $source_filepath = '';

  function __construct($source_filepath, $library=null)
  {
    $this->source_filepath = $source_filepath;

    trigger_action('load_image_library', array(&$this) );

    if (is_object($this->image))
    {
      return; // A plugin may have load its own library
    }

    $extension = strtolower(get_extension($source_filepath));

    if (!in_array($extension, array('jpg', 'jpeg', 'png', 'gif')))
    {
      die('[Image] unsupported file extension');
    }

    if (!($this->library = self::get_library($library, $extension)))
    {
      die('No image library available on your server.');
    }

    $class = 'image_'.$this->library;
    $this->image = new $class($source_filepath);
  }

  // Unknow methods will be redirected to image object
  function __call($method, $arguments)
  {
    return call_user_func_array(array($this->image, $method), $arguments);
  }

  // Piwigo resize function
  function pwg_resize($destination_filepath, $max_width, $max_height, $quality, $automatic_rotation=true, $strip_metadata=false, $crop=false, $follow_orientation=true)
  {
    $starttime = get_moment();

    // width/height
    $source_width  = $this->image->get_width();
    $source_height = $this->image->get_height();

    $rotation = null;
    if ($automatic_rotation)
    {
      $rotation = self::get_rotation_angle($this->source_filepath);
    }
    $resize_dimensions = self::get_resize_dimensions($source_width, $source_height, $max_width, $max_height, $rotation, $crop, $follow_orientation);

    // testing on height is useless in theory: if width is unchanged, there
    // should be no resize, because width/height ratio is not modified.
    if ($resize_dimensions['width'] == $source_width and $resize_dimensions['height'] == $source_height)
    {
      // the image doesn't need any resize! We just copy it to the destination
      copy($this->source_filepath, $destination_filepath);
      return $this->get_resize_result($destination_filepath, $resize_dimensions['width'], $resize_dimensions['height'], $starttime);
    }

    $this->image->set_compression_quality($quality);

    if ($strip_metadata)
    {
      // we save a few kilobytes. For example a thumbnail with metadata weights 25KB, without metadata 7KB.
      $this->image->strip();
    }

    if (isset($resize_dimensions['crop']))
    {
      $this->image->crop($resize_dimensions['crop']['width'], $resize_dimensions['crop']['height'], $resize_dimensions['crop']['x'], $resize_dimensions['crop']['y']);
    }

    $this->image->resize($resize_dimensions['width'], $resize_dimensions['height']);

    if (isset($rotation))
    {
      $this->image->rotate($rotation);
    }

    $this->image->write($destination_filepath);

    // everything should be OK if we are here!
    return $this->get_resize_result($destination_filepath, $resize_dimensions['width'], $resize_dimensions['height'], $starttime);
  }

  static function get_resize_dimensions($width, $height, $max_width, $max_height, $rotation=null, $crop=false, $follow_orientation=true)
  {
    $rotate_for_dimensions = false;
    if (isset($rotation) and in_array(abs($rotation), array(90, 270)))
    {
      $rotate_for_dimensions = true;
    }

    if ($rotate_for_dimensions)
    {
      list($width, $height) = array($height, $width);
    }

    if ($crop)
    {
      $x = 0;
      $y = 0;

      if ($width < $height and $follow_orientation)
      {
        list($max_width, $max_height) = array($max_height, $max_width);
      }

      $img_ratio = $width / $height;
      $dest_ratio = $max_width / $max_height;

      if($dest_ratio > $img_ratio)
      {
        $destHeight = round($width * $max_height / $max_width);
        $y = round(($height - $destHeight) / 2 );
        $height = $destHeight;
      }
      elseif ($dest_ratio < $img_ratio)
      {
        $destWidth = round($height * $max_width / $max_height);
        $x = round(($width - $destWidth) / 2 );
        $width = $destWidth;
      }
    }

    $ratio_width  = $width / $max_width;
    $ratio_height = $height / $max_height;
    $destination_width = $width;
    $destination_height = $height;

    // maximal size exceeded ?
    if ($ratio_width > 1 or $ratio_height > 1)
    {
      if ($ratio_width < $ratio_height)
      {
        $destination_width = round($width / $ratio_height);
        $destination_height = $max_height;
      }
      else
      {
        $destination_width = $max_width;
        $destination_height = round($height / $ratio_width);
      }
    }

    if ($rotate_for_dimensions)
    {
      list($destination_width, $destination_height) = array($destination_height, $destination_width);
    }

    $result = array(
      'width' => $destination_width,
      'height'=> $destination_height,
      );

    if ($crop and ($x or $y))
    {
      $result['crop'] = array(
        'width' => $width,
        'height' => $height,
        'x' => $x,
        'y' => $y,
        );
    }
    return $result;
  }

  static function get_rotation_angle($source_filepath)
  {
    list($width, $height, $type) = getimagesize($source_filepath);
    if (IMAGETYPE_JPEG != $type)
    {
      return null;
    }

    if (!function_exists('exif_read_data'))
    {
      return null;
    }

    $rotation = 0;

    $exif = exif_read_data($source_filepath);

    if (isset($exif['Orientation']) and preg_match('/^\s*(\d)/', $exif['Orientation'], $matches))
    {
      $orientation = $matches[1];
      if (in_array($orientation, array(3, 4)))
      {
        $rotation = 180;
      }
      elseif (in_array($orientation, array(5, 6)))
      {
        $rotation = 270;
      }
      elseif (in_array($orientation, array(7, 8)))
      {
        $rotation = 90;
      }
    }

    return $rotation;
  }

  static function get_rotation_code_from_angle($rotation_angle)
  {
    switch($rotation_angle)
    {
      case 0:   return 0;
      case 90:  return 1;
      case 180: return 2;
      case 270: return 3;
    }
  }

  static function get_rotation_angle_from_code($rotation_code)
  {
    switch($rotation_code%4)
    {
      case 0: return 0;
      case 1: return 90;
      case 2: return 180;
      case 3: return 270;
    }
  }

  /** Returns a normalized convolution kernel for sharpening*/
  static function get_sharpen_matrix($amount)
  {
    // Amount should be in the range of 48-10
    $amount = round(abs(-48 + ($amount * 0.38)), 2);

    $matrix = array(
      array(-1,   -1,    -1),
      array(-1, $amount, -1),
      array(-1,   -1,    -1),
      );

    $norm = array_sum(array_map('array_sum', $matrix));

    for ($i=0; $i<3; $i++)
    {
      $line = & $matrix[$i];
      for ($j=0; $j<3; $j++)
      {
        $line[$j] /= $norm;
      }
    }

    return $matrix;
  }

  private function get_resize_result($destination_filepath, $width, $height, $time=null)
  {
    return array(
      'source'      => $this->source_filepath,
      'destination' => $destination_filepath,
      'width'       => $width,
      'height'      => $height,
      'size'        => floor(filesize($destination_filepath) / 1024).' KB',
      'time'        => $time ? number_format((get_moment() - $time) * 1000, 2, '.', ' ').' ms' : null,
      'library'     => $this->library,
    );
  }

  static function is_imagick()
  {
    return (extension_loaded('imagick') and class_exists('Imagick'));
  }

  static function is_ext_imagick()
  {
    global $conf;

    if (!function_exists('exec'))
    {
      return false;
    }
    @exec($conf['ext_imagick_dir'].'convert -version', $returnarray);
    if (is_array($returnarray) and !empty($returnarray[0]) and preg_match('/ImageMagick/i', $returnarray[0]))
    {
      return true;
    }
    return false;
  }

  static function is_gd()
  {
    return function_exists('gd_info');
  }

  static function get_library($library=null, $extension=null)
  {
    global $conf;

    if (is_null($library))
    {
      $library = $conf['graphics_library'];
    }

    // Choose image library
    switch (strtolower($library))
    {
      case 'auto':
      case 'imagick':
        if ($extension != 'gif' and self::is_imagick())
        {
          return 'imagick';
        }
      case 'ext_imagick':
        if ($extension != 'gif' and self::is_ext_imagick())
        {
          return 'ext_imagick';
        }
      case 'gd':
        if (self::is_gd())
        {
          return 'gd';
        }
      default:
        if ($library != 'auto')
        {
          // Requested library not available. Try another library
          return self::get_library('auto', $extension);
        }
    }
    return false;
  }

  function destroy()
  {
    if (method_exists($this->image, 'destroy'))
    {
      return $this->image->destroy();
    }
    return true;
  }
}

// +-----------------------------------------------------------------------+
// |                   Class for Imagick extension                         |
// +-----------------------------------------------------------------------+

class image_imagick implements imageInterface
{
  var $image;

  function __construct($source_filepath)
  {
    // A bug cause that Imagick class can not be extended
    $this->image = new Imagick($source_filepath);
  }

  function get_width()
  {
    return $this->image->getImageWidth();
  }

  function get_height()
  {
    return $this->image->getImageHeight();
  }

  function set_compression_quality($quality)
  {
    return $this->image->setImageCompressionQuality($quality);
  }

  function crop($width, $height, $x, $y)
  {
    return $this->image->cropImage($width, $height, $x, $y);
  }

  function strip()
  {
    return $this->image->stripImage();
  }

  function rotate($rotation)
  {
    $this->image->rotateImage(new ImagickPixel(), -$rotation);
    $this->image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
    return true;
  }

  function resize($width, $height)
  {
    $this->image->setInterlaceScheme(Imagick::INTERLACE_LINE);
    
    // TODO need to explain this condition
    if ($this->get_width()%2 == 0
        && $this->get_height()%2 == 0
        && $this->get_width() > 3*$width)
    {
      $this->image->scaleImage($this->get_width()/2, $this->get_height()/2);
    }

    return $this->image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 0.9);
  }

  function sharpen($amount)
  {
    $m = pwg_image::get_sharpen_matrix($amount);
    return  $this->image->convolveImage($m);
  }

  function compose($overlay, $x, $y, $opacity)
  {
    $ioverlay = $overlay->image->image;
    /*if ($ioverlay->getImageAlphaChannel() !== Imagick::ALPHACHANNEL_OPAQUE)
    {
      // Force the image to have an alpha channel
      $ioverlay->setImageAlphaChannel(Imagick::ALPHACHANNEL_OPAQUE);
    }*/

    global $dirty_trick_xrepeat;
    if ( !isset($dirty_trick_xrepeat) && $opacity < 100)
    {// NOTE: Using setImageOpacity will destroy current alpha channels!
      $ioverlay->evaluateImage(Imagick::EVALUATE_MULTIPLY, $opacity / 100, Imagick::CHANNEL_ALPHA);
      $dirty_trick_xrepeat = true;
    }

    return $this->image->compositeImage($ioverlay, Imagick::COMPOSITE_DISSOLVE, $x, $y);
  }

  function write($destination_filepath)
  {
    // use 4:2:2 chroma subsampling (reduce file size by 20-30% with "almost" no human perception)
    $this->image->setSamplingFactors( array(2,1) );
    return $this->image->writeImage($destination_filepath);
  }
}

// +-----------------------------------------------------------------------+
// |            Class for ImageMagick external installation                |
// +-----------------------------------------------------------------------+

class image_ext_imagick implements imageInterface
{
  var $imagickdir = '';
  var $source_filepath = '';
  var $width = '';
  var $height = '';
  var $commands = array();

  function __construct($source_filepath)
  {
    global $conf;
    $this->source_filepath = $source_filepath;
    $this->imagickdir = $conf['ext_imagick_dir'];

    if (strpos(@$_SERVER['SCRIPT_FILENAME'], '/kunden/') === 0)  // 1and1
    {
      @putenv('MAGICK_THREAD_LIMIT=1');
    }

    $command = $this->imagickdir.'identify -format "%wx%h" "'.realpath($source_filepath).'"';
    @exec($command, $returnarray);
    if(!is_array($returnarray) or empty($returnarray[0]) or !preg_match('/^(\d+)x(\d+)$/', $returnarray[0], $match))
    {
      die("[External ImageMagick] Corrupt image\n" . var_export($returnarray, true));
    }

    $this->width = $match[1];
    $this->height = $match[2];
  }

  function add_command($command, $params=null)
  {
    $this->commands[$command] = $params;
  }

  function get_width()
  {
    return $this->width;
  }

  function get_height()
  {
    return $this->height;
  }

  function crop($width, $height, $x, $y)
  {
    $this->add_command('crop', $width.'x'.$height.'+'.$x.'+'.$y);
    return true;
  }

  function strip()
  {
    $this->add_command('strip');
    return true;
  }

  function rotate($rotation)
  {
    if ($rotation==90 || $rotation==270)
    {
      $tmp = $this->width;
      $this->width = $this->height;
      $this->height = $tmp;
    }
    $this->add_command('rotate', -$rotation);
    $this->add_command('orient', 'top-left');
    return true;
  }

  function set_compression_quality($quality)
  {
    $this->add_command('quality', $quality);
    return true;
  }

  function resize($width, $height)
  {
    $this->add_command('filter', 'Lanczos');
    $this->add_command('resize', $width.'x'.$height.'!');
    return true;
  }

  function sharpen($amount)
  {
    $m = pwg_image::get_sharpen_matrix($amount);

    $param ='convolve "'.count($m).':';
    foreach ($m as $line)
    {
      $param .= ' ';
      $param .= implode(',', $line);
    }
    $param .= '"';
    $this->add_command('morphology', $param);
    return true;
  }

  function compose($overlay, $x, $y, $opacity)
  {
    $param = 'compose dissolve -define compose:args='.$opacity;
    $param .= ' '.escapeshellarg(realpath($overlay->image->source_filepath));
    $param .= ' -gravity NorthWest -geometry +'.$x.'+'.$y;
    $param .= ' -composite';
    $this->add_command($param);
    return true;
  }

  function write($destination_filepath)
  {
    $this->add_command('interlace', 'line'); // progressive rendering
    // use 4:2:2 chroma subsampling (reduce file size by 20-30% with "almost" no human perception)
    $this->add_command('sampling-factor', '4:2:2' );

    $exec = $this->imagickdir.'convert';
    $exec .= ' "'.realpath($this->source_filepath).'"';

    foreach ($this->commands as $command => $params)
    {
      $exec .= ' -'.$command;
      if (!empty($params))
      {
        $exec .= ' '.$params;
      }
    }

    $dest = pathinfo($destination_filepath);
    $exec .= ' "'.realpath($dest['dirname']).'/'.$dest['basename'].'" 2>&1';
    @exec($exec, $returnarray);

    if (function_exists('ilog')) ilog($exec);
    if (is_array($returnarray) && (count($returnarray)>0) )
    {
      if (function_exists('ilog')) ilog('ERROR', $returnarray);
      foreach($returnarray as $line)
        trigger_error($line, E_USER_WARNING);
    }
    return is_array($returnarray);
  }
}

// +-----------------------------------------------------------------------+
// |                       Class for GD library                            |
// +-----------------------------------------------------------------------+

class image_gd implements imageInterface
{
  var $image;
  var $quality = 95;

  function __construct($source_filepath)
  {
    $gd_info = gd_info();
    $extension = strtolower(get_extension($source_filepath));

    if (in_array($extension, array('jpg', 'jpeg')))
    {
      $this->image = imagecreatefromjpeg($source_filepath);
    }
    else if ($extension == 'png')
    {
      $this->image = imagecreatefrompng($source_filepath);
    }
    elseif ($extension == 'gif' and $gd_info['GIF Read Support'] and $gd_info['GIF Create Support'])
    {
      $this->image = imagecreatefromgif($source_filepath);
    }
    else
    {
      die('[Image GD] unsupported file extension');
    }
  }

  function get_width()
  {
    return imagesx($this->image);
  }

  function get_height()
  {
    return imagesy($this->image);
  }

  function crop($width, $height, $x, $y)
  {
    $dest = imagecreatetruecolor($width, $height);

    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    if (function_exists('imageantialias'))
    {
      imageantialias($dest, true);
    }

    $result = imagecopymerge($dest, $this->image, 0, 0, $x, $y, $width, $height, 100);

    if ($result !== false)
    {
      imagedestroy($this->image);
      $this->image = $dest;
    }
    else
    {
      imagedestroy($dest);
    }
    return $result;
  }

  function strip()
  {
    return true;
  }

  function rotate($rotation)
  {
    $dest = imagerotate($this->image, $rotation, 0);
    imagedestroy($this->image);
    $this->image = $dest;
    return true;
  }

  function set_compression_quality($quality)
  {
    $this->quality = $quality;
    return true;
  }

  function resize($width, $height)
  {
    $dest = imagecreatetruecolor($width, $height);

    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    if (function_exists('imageantialias'))
    {
      imageantialias($dest, true);
    }

    $result = imagecopyresampled($dest, $this->image, 0, 0, 0, 0, $width, $height, $this->get_width(), $this->get_height());

    if ($result !== false)
    {
      imagedestroy($this->image);
      $this->image = $dest;
    }
    else
    {
      imagedestroy($dest);
    }
    return $result;
  }

  function sharpen($amount)
  {
    $m = pwg_image::get_sharpen_matrix($amount);
    return imageconvolution($this->image, $m, 1, 0);
  }

  function compose($overlay, $x, $y, $opacity)
  {
    $ioverlay = $overlay->image->image;
    /* A replacement for php's imagecopymerge() function that supports the alpha channel
    See php bug #23815:  http://bugs.php.net/bug.php?id=23815 */

    $ow = imagesx($ioverlay);
    $oh = imagesy($ioverlay);

		// Create a new blank image the site of our source image
		$cut = imagecreatetruecolor($ow, $oh);

		// Copy the blank image into the destination image where the source goes
		imagecopy($cut, $this->image, 0, 0, $x, $y, $ow, $oh);

		// Place the source image in the destination image
		imagecopy($cut, $ioverlay, 0, 0, 0, 0, $ow, $oh);
		imagecopymerge($this->image, $cut, $x, $y, 0, 0, $ow, $oh, $opacity);
    imagedestroy($cut);
    return true;
  }

  function write($destination_filepath)
  {
    $extension = strtolower(get_extension($destination_filepath));

    if ($extension == 'png')
    {
      imagepng($this->image, $destination_filepath);
    }
    elseif ($extension == 'gif')
    {
      imagegif($this->image, $destination_filepath);
    }
    else
    {
      imagejpeg($this->image, $destination_filepath, $this->quality);
    }
  }

  function destroy()
  {
    imagedestroy($this->image);
  }
}

?>