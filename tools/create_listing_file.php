<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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
// |                                User configuration                     |
// +-----------------------------------------------------------------------+

// ****** Gallery configuration ****** //
// Script version
$conf['version'] = '2.1.0RC3';

// URL of main gallery
// Example : http://www.my.domain/my/directory
$conf['gallery'] = 'http://piwigo.org/demo';

// prefix for thumbnails in "thumbnail" sub directories
$conf['prefix_thumbnail'] = 'TN-';

// $conf['file_ext'] lists all extensions (case insensitive) allowed
// for your Piwigo installation
$conf['file_ext'] = array('jpg','JPG','jpeg','JPEG',
                          'png','PNG','gif','GIF','mpg','zip',
                          'avi','mp3','ogg');


// $conf['picture_ext'] must be a subset of $conf['file_ext']
$conf['picture_ext'] = array('jpg','JPG','jpeg','JPEG',
                             'png','PNG','gif','GIF');

// ****** Time limitation functionality ****** //
// max execution time before refresh in seconds
$conf['max_execution_time'] = (5*ini_get('max_execution_time'))/6; // 25 seconds with default PHP configuration
// force the use of refresh method
// in order to have live informations
// or
// to fix system witch are not safe mode but not autorized set_time_limit
$conf['force_refresh_method'] =  true;

// refresh delay is seconds
$conf['refresh_delay'] = 0;

// ****** EXIF support functionality ****** //
// $conf['use_exif'] set to true if you want to use Exif information
$conf['use_exif'] = true;

// use_exif_mapping: same behaviour as use_iptc_mapping
$conf['use_exif_mapping'] = array(
  'date_creation' => 'DateTimeOriginal'
  );

// ****** IPTC support functionality ****** //
// $conf['use_iptc'] set to true if you want to use IPTC informations of the
// element according to get_sync_iptc_data function mapping, otherwise, set
// to false
$conf['use_iptc'] = false;

// use_iptc_mapping : in which IPTC fields will Piwigo find image
// information ? This setting is used during metadata synchronisation. It
// associates a piwigo_images column name to a IPTC key
$conf['use_iptc_mapping'] = array(
  'keywords'        => '2#025',
  'date_creation'   => '2#055',
  'author'          => '2#122',
  'name'            => '2#005',
  'comment'         => '2#120');

// ****** Directory protection functionality ****** //
// Define if directories have to be protected if they are not
$conf['protect'] = false;

// true/false : show/hide warnings
$conf['protect_warnings'] = true;

// ****** Thumbnails generation functionality ****** //
// Define if images have to be reduced if they are not
$conf['thumbnail'] = false;

// Define method to generate thumbnails : 
// - fixed (width and height required);
// - width (only width required);
// - height (only height required);
// - ratio (only ratio is required)
// - exif (no other parameter required)
$conf['thumbnail_method'] = 'ratio';

// Height in pixels (greater than 0)
$conf['thumbnail_height'] = 128;

// Width in pixels (greater than 0)
$conf['thumbnail_width'] = 128;

// Ratio between original and thumbnail size (strictly between 0 and 1)
$conf['thumbnail_ratio'] = 0.2;

// Define thumbnail format : jpeg, png or gif (will be verified)
$conf['thumbnail_format'] = 'jpeg';

// ****** Directory mapping ****** //
// directories names 
$conf['thumbs'] = 'thumbnail'; // thumbnails
$conf['high'] = 'pwg_high'; // high resolution
$conf['represent'] = 'pwg_representative'; // non pictures representative files


// +-----------------------------------------------------------------------+
// | Overload configurations                                               |
// +-----------------------------------------------------------------------+
@include(dirname(__FILE__).'/'.basename(__FILE__, '.php').'_local.inc.php');


// +-----------------------------------------------------------------------+
// |                                Advanced script configuration          |
// +-----------------------------------------------------------------------+

// url of icon directory in yoga template
$pwg_conf['icon_dir'] = $conf['gallery'].'/template/yoga/icon/';

// list of actions managed by this script
$pwg_conf['scan_action'] = array('clean', 'test', 'generate');

// url of this script
$pwg_conf['this_url'] = 
    (empty($_SERVER['HTTPS']) ? 'http://' : 'https://')
    .str_replace(':'.$_SERVER['SERVER_PORT'], '', $_SERVER['HTTP_HOST'])
    .($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '')
    .$_SERVER['PHP_SELF'];

// list of reserved directory names
$pwg_conf['reserved_directory_names'] = array($conf['thumbs'], $conf['high'], $conf['represent'], ".", "..", ".svn");

// content of index.php generated in protect action
$pwg_conf['protect_content'] = '<?php header("Location: '.$conf['gallery'].'") ?>';

// backup of PHP safe_mode INI parameter (used for time limitation)
$pwg_conf['safe_mode'] = (ini_get('safe_mode') == '1') ? true : false;

// This parameter will be fixed in pwg_init()
$pwg_conf['gd_version_major'] = '';
$pwg_conf['gd_version_full'] = '';
$pwg_conf['gd_supported_format'] = array();

// +-----------------------------------------------------------------------+
// |                               Functions                               |
// +-----------------------------------------------------------------------+

/**
 * write line in log file
 *
 * @param string line
 * @return string
 */
function pwg_log($line)
{
  $log_file = fopen(__FILE__.'.log', 'a');
  fwrite($log_file, $line);
  fclose($log_file);
}

/**
 * Check web server graphical capabilities
 *
 * @return string
 */
function pwg_check_graphics()
{
  //~ pwg_log('>>>>> pwg_check_graphics() >>>>>'."\n");
  
  global $conf, $pwg_conf;
  $log = '';
  
  // Verify gd library for thumbnail generation
  if ($conf['thumbnail'] and !is_callable('gd_info'))
  {
    $log .= '          <code class="warning">Warning -</code> Your server can not generate thumbnails. Thumbnail creation switched off.<br />'."\n";
    // Switch off thumbnail generation
    $conf['thumbnail'] = false;
    return $log;
  }
  
  // Verify thumnail format
  if ($conf['thumbnail'])
  {
    $info = gd_info();
    
    // Backup GD major version
    $pwg_conf['gd_version_full'] = preg_replace('/[[:alpha:][:space:]()]+/', '', $info['GD Version']);
    list($pwg_conf['gd_version_major']) = preg_split('/[.]+/', $pwg_conf['gd_version_full']);
    
    // Backup input/output format support
    array_push($pwg_conf['gd_supported_format'], (isset($info['JPG Support']) and $info['JPG Support']) or (isset($info['JPEG Support']) and $info['JPEG Support']) ? 'jpeg' : NULL);
    array_push($pwg_conf['gd_supported_format'], $info['PNG Support'] ? 'png' : NULL);
    array_push($pwg_conf['gd_supported_format'], ($info['GIF Read Support'] and $info['GIF Create Support']) ? 'gif' : NULL);
    
    // Check output format support
    if (!in_array($conf['thumbnail_format'], $pwg_conf['gd_supported_format']))
    {
      $log .= '          <code class="warning">Warning -</code> Your server does not support thumbnail\'s <code>';
      $log .= $conf['thumbnail_format'].'</code> format. Thumbnail creation switched off.<br />'."\n";
    }
    
    switch ($conf['thumbnail_method'])
    {
      case 'exif':
      {
        // exif_thumbnail() must be callable
        if (!is_callable('exif_thumbnail'))
        {
          $log .= '          <code class="warning">Warning -</code> Your server does not support thumbnail creation through EXIF datas. Thumbnail creation switched off.<br />'."\n";
        }
        break;
      }
      case 'fixed':
      {
        // $conf['thumbnail_width'] > 0
        if (!is_numeric($conf['thumbnail_width']) or $conf['thumbnail_width'] <= 0)
        {
          $log .= '          <code class="failure">Failure -</code> Bad value <code>thumbnail_width = ';
          $log .= var_export($conf['thumbnail_width'], true).'</code>. Thumbnail creation switched off.<br />'."\n";
        }
        // $conf['thumbnail_height'] > 0
        if (!is_numeric($conf['thumbnail_height']) or $conf['thumbnail_height'] <= 0)
        {
          $log .= '          <code class="failure">Failure -</code> Bad value <code>thumbnail_height = ';
          $log .= var_export($conf['thumbnail_height'], true).'</code>. Thumbnail creation switched off.<br />'."\n";
        }
        break;
      }
      case 'ratio':
      {
        // 0 < $conf['thumbnail_ratio'] < 1
        if (!is_numeric($conf['thumbnail_ratio']) or $conf['thumbnail_ratio'] <= 0 or $conf['thumbnail_ratio'] >= 1)
        {
          $log .= '          <code class="failure">Failure -</code> Bad value <code>thumbnail_ratio = ';
          $log .= var_export($conf['thumbnail_ratio'], true).'</code>. Thumbnail creation switched off.<br />'."\n";
        }
        break;
      }
      case 'width':
      {
        // $conf['thumbnail_width'] > 0
        if (!is_numeric($conf['thumbnail_width']) or $conf['thumbnail_width'] <= 0)
        {
          $log .= '          <code class="failure">Failure -</code> Bad value <code>thumbnail_width = ';
          $log .= var_export($conf['thumbnail_width'], true).'</code>. Thumbnail creation switched off.<br />'."\n";
        }
        break;
      }
      case 'height':
      {
        // $conf['thumbnail_height'] > 0
        if (!is_numeric($conf['thumbnail_height']) or $conf['thumbnail_height'] <= 0)
        {
          $log .= '          <code class="failure">Failure -</code> Bad value <code>thumbnail_height = ';
          $log .= var_export($conf['thumbnail_height'], true).'</code>. Thumbnail creation switched off.<br />'."\n";
        }
        break;
      }
      default:
      {
        // unknown method
          $log .= '          <code class="failure">Failure -</code> Bad value <code>thumbnail_method = ';
          $log .= var_export($conf['thumbnail_method'], true).'</code>. Thumbnail creation switched off.<br />'."\n";
        break;
      }
    }
    
    if (strlen($log))
    {
      $conf['thumbnail'] = false;
    }
  }
  
  //~ pwg_log('<<<<< pwg_check_graphics() returns '.var_export($log, TRUE).' <<<<<'."\n");
  return $log;
}

/**
 * returns xml </dirX> lines
 *
 * @param integer $dir_start
 * @param integer $dir_number
 * @return string
 */
function pwg_close_level($dir_start, $dir_number)
{
  //~ pwg_log('>>>>> pwg_close_level($dir_start = '.var_export($dir_start, TRUE).', $dir_number = '.var_export($dir_number, TRUE).') >>>>>'."\n");
  
  $lines ='';
  do
  {
    $lines .= str_repeat(' ', 2*$dir_start).'</dir'.$dir_start.">\n";
    $dir_number--;
    $dir_start--;
  }
  while(($dir_number > 0) && ($dir_start >= 0));
  
  //~ pwg_log('<<<<< pwg_close_level returns '.var_export($lines, TRUE).' <<<<<'."\n");
  return $lines;
}

/**
 * return a cleaned IPTC value
 *
 * @param string value
 * @return string
 */
function pwg_clean_iptc_value($value)
{
  //~ pwg_log('>>>>> pwg_clean_iptc_value ($value = '.var_export($value, TRUE).') >>>>>'."\n");
  
  // strip leading zeros (weird Kodak Scanner software)
  while (isset($value[0]) and $value[0] == chr(0))
  {
    $value = substr($value, 1);
  }
  // remove binary nulls
  $value = str_replace(chr(0x00), ' ', $value);

  //~ pwg_log('<<<<< pwg_clean_iptc_value() returns '.var_export($value, TRUE).' <<<<<'."\n");
  return $value;
}

/**
 * returns informations from IPTC metadata, mapping is done at the beginning
 * of the function
 *
 * @param string $filename
 * @param string $map
 * @return array
 */
function pwg_get_iptc_data($filename, $map)
{
  //~ pwg_log('>>>>> pwg_get_iptc_data ($filename = '.var_export($filename, TRUE).', $map = '.var_export($map, TRUE).') >>>>>'."\n");
  
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
            $value = implode(',', array_map('pwg_clean_iptc_value', $iptc[$iptc_key]));
          }
          else
          {
            $value = pwg_clean_iptc_value($iptc[$iptc_key][0]);
          }

          foreach (array_keys($map, $iptc_key) as $pwg_key)
          {
            $result[$pwg_key] = $value;
          }
        }
      }
    }
  }
  
  //~ pwg_log('<<<<< pwg_get_iptc_data() returns '.var_export($result, TRUE).' <<<<<'."\n");
  return $result;
}

/**
 * returns informations from IPTC metadata
 *
 * @param string $file
 * @return array iptc
 */
function pwg_get_sync_iptc_data($file)
{
  //~ pwg_log('>>>>> pwg_get_sync_iptc_data ($file = '.var_export($file, TRUE).') >>>>>'."\n");

  global $conf;

  $map = $conf['use_iptc_mapping'];
  $datefields = array('date_creation', 'date_available');

  $iptc = pwg_get_iptc_data($file, $map);

  foreach ($iptc as $pwg_key => $value)
  {
    if (in_array($pwg_key, $datefields))
    {
      if ( preg_match('/(\d{4})(\d{2})(\d{2})/', $value, $matches))
      {
        $value = $matches[1].'-'.$matches[2].'-'.$matches[3];
      }
    }
    if ($pwg_key == 'keywords')
    {
      // official keywords separator is the comma
      $value = preg_replace('/[.;]/', ',', $value);
      $value = preg_replace('/^,+|,+$/', '', $value);
    }
    $iptc[$pwg_key] = htmlentities($value);
  }

  $iptc['keywords'] = isset($iptc['keywords']) ? implode(',', array_unique(explode(',', $iptc['keywords']))) : NULL;

  //~ pwg_log('<<<<< pwg_get_sync_iptc_data() returns '.var_export($iptc, TRUE).' <<<<<'."\n");
  return $iptc;
}

/**
 * return extension of the representative file
 *
 * @param string $file_dir
 * @param string $file_short
 * @return string
 */
function pwg_get_representative_ext($file_dir, $file_short)
{
  //~ pwg_log('>>>>> pwg_get_representative_ext($file_dir = '.var_export($file_dir, TRUE).', $file_short = '.var_export($file_short, TRUE).') >>>>>'."\n");
  
  global $conf;
  
  $rep_ext = '';
  foreach ($conf['picture_ext'] as $ext)
  {
    if (file_exists($file_dir.'/'.$conf['represent'].'/'.$file_short.'.'.$ext))
    {
      $rep_ext = $ext;
      break;
    }
  }
  
  //~ pwg_log('<<<<< pwg_get_representative_ext() returns '.var_export($rep_ext, TRUE).' <<<<<'."\n");
  return $rep_ext; 
}

/**
 * return 'true' if high resolution picture exists else ''
 *
 * @param string $file_dir
 * @param string $file_base
 * @return boolean
 */
function pwg_get_high($file_dir, $file_base)
{
  //~ pwg_log('>>>>> pwg_get_high($file = '.var_export($file_dir, TRUE).', $line = '.var_export($file_base, TRUE).') >>>>>'."\n");
  
  global $conf;
  
  $high = false;
  if (file_exists($file_dir.'/'.$conf['high'].'/'.$file_base))
  {
    $high = true;
  }
  
  //~ pwg_log('<<<<< pwg_get_high() returns '.var_export($high, TRUE).' <<<<<'."\n");
  return $high; 
}

/**
 * return filename without extension
 *
 * @param string $filename
 * @return string
 */
function pwg_get_filename_wo_extension($filename)
{
  //~ pwg_log('>>>>> _get_filename_wo_extension($filename = '.var_export($filename, TRUE).') >>>>>'."\n");
  
  $short_name = substr($filename, 0, strrpos($filename, '.'));
  
  //~ pwg_log('<<<<< _get_filename_wo_extension() returns '.var_export($short_name, TRUE).' <<<<<'."\n");
  return $short_name;
}

/**
 * return extension of the thumbnail and complete error_log
 *
 * @param string $file_dir
 * @param string $file_short
 * @param string $file_ext
 * @param string &$error_log
 * @return string
 */
function pwg_get_thumbnail_ext($file_dir, $file_short, $file_ext, &$error_log, &$icon_log)
{
  //~ pwg_log('>>>>> pwg_get_thumbnail_ext($file_dir = '.var_export($file_dir, TRUE).', $file_short = '.var_export($file_short, TRUE).') >>>>>'."\n");
  
  global $conf;
  
  $thumb_ext = '';
  foreach ($conf['picture_ext'] as $ext)
  {
    if (file_exists($file_dir.'/'.$conf['thumbs'].'/'.$conf['prefix_thumbnail'].$file_short.'.'.$ext))
    {
      $thumb_ext = $ext;
      break;
    }
  }
  
  if ($thumb_ext == '')
  {
    if ($conf['thumbnail'])
    {
      $log = pwg_icon_file($file_dir, $file_short, $file_ext);
      if (strpos($log, 'success'))
      {
        $thumb_ext = $conf['thumbnail_format'];
      }
      $icon_log .= $log;  
    }
  }
  
  //~ pwg_log('<<<<< pwg_get_thumbnail_ext() returns '.var_export($thumb_ext, TRUE).' <<<<<'."\n");
  return $thumb_ext; 
}


/**
 * return error logs
 *
 * @param string $file_dir
 * @param string $file_short
 * @param string $file_ext
 * @return string
 */
function pwg_icon_file($file_dir, $file_short, $file_ext)
{
  //~ pwg_log('>>>>> pwg_icon_file($file_dir = '.var_export($file_dir, TRUE).', $file_short = '.var_export($file_short, TRUE).') >>>>>'."\n");
  
  global $conf, $pwg_conf;
  
  $error_log = '';
  
  // Create thumbnail directory if not exists
  if (!file_exists($file_dir.'/'.$conf['thumbs']))
  {
    mkdir($file_dir.'/'.$conf['thumbs']);
  }

  // Get original properties (width, height)
  if ($image_size = getimagesize($file_dir.'/'.$file_short.'.'.$file_ext))
  {
    $src_width = $image_size[0];
    $src_height = $image_size[1];
  }
  else
  {
    $error_log .= '          <code class="failure">Failure -</code> Can not generate icon for <code>';
    $error_log .= $file_dir.'/'.$file_short.'.'.$file_ext.'</code>';
    $error_log .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png" title="width/height are unreadable" /><br />'."\n";
    return $error_log;
  }
  
  // Check input format
  $dst_format = $conf['thumbnail_format'];
  $src_format = ($file_ext == 'jpg' or $file_ext == 'JPG') ? 'jpeg' : strtolower($file_ext);
  if (!in_array($src_format, $pwg_conf['gd_supported_format']))
  {
    $error_log .= '          <code class="failure">Failure -</code> Can not generate icon for <code>';
    $error_log .= $file_dir.'/'.$file_short.'.'.$file_ext.'</code>';
    $error_log .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png" title="format not supported" /><br />'."\n";
    return $error_log;
  }
  
  // Calculate icon properties (width, height)
  switch ($conf['thumbnail_method'])
  {
    case 'fixed':
    {
      $dst_width  = $conf['thumbnail_width'];
      $dst_height = $conf['thumbnail_height'];
      break;
    }
    case 'width':
    {
      $dst_width  = $conf['thumbnail_width'];
      $dst_height = $dst_width * $src_height / $src_width;
      break;
    }
    case 'height':
    {
      $dst_height = $conf['thumbnail_height'];
      $dst_width  = $dst_height * $src_width / $src_height;
      break;
    }
    case 'ratio':
    {
      $dst_width  = round($src_width * $conf['thumbnail_ratio']);
      $dst_height = round($src_height * $conf['thumbnail_ratio']);
      break;
    }
    case 'exif':
    default:
    {
      // Nothing to do
    }
  }
  
  // Creating icon
  if ($conf['thumbnail_method'] == 'exif')
  {
    $src = exif_thumbnail($file_dir.'/'.$file_short.'.'.$file_ext, $width, $height, $imagetype);
    if ($src === false)
    {
      $error_log .= '          <code class="failure">Failure -</code> No EXIF thumbnail in <code>';
      $error_log .= $file_dir.'/'.$file_short.'.'.$file_ext.'</code><br />'."\n";
      return $error_log;
    }
    $dst = imagecreatefromstring($src);
    if ($src === false)
    {
      $error_log .= '          <code class="failure">Failure -</code> EXIF thumbnail format not supported in <code>';
      $error_log .= $file_dir.'/'.$file_short.'.'.$file_ext.'</code><br />'."\n";
      return $error_log;
    }
  }
  else
  {
    if (($pwg_conf['gd_version_major'] != 2)) // or ($conf['thumbnail_format'] == 'gif'))
    {
      $dst = imagecreate($dst_width, $dst_height);
    }
    else
    {
      $dst = imagecreatetruecolor($dst_width, $dst_height);
    }
    $src = call_user_func('imagecreatefrom'.$src_format, $file_dir.'/'.$file_short.'.'.$file_ext);
    if (!$src)
    {
      $error_log .= '          <code class="failure">Failure -</code> Internal error for <code>imagecreatefrom'.$src_format.'()</code>';
      $error_log .= 'with <code>'.$file_dir.'/'.$file_short.'.'.$file_ext.'</code><br />'."\n";
      return $error_log;
    }
     
    if (($pwg_conf['gd_version_major'] != 2))
    {
      if (!imagecopyresized($dst, $src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height))
      {
        $error_log .= '          <code class="failure">Failure -</code> Internal error for <code>imagecopyresized()</code>';
        $error_log .= 'with <code>'.$file_dir.'/'.$file_short.'.'.$file_ext.'</code><br />'."\n";
        return $error_log;
      }
    }
    else
    {
      if (!imagecopyresampled($dst, $src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height))
      {
        $error_log .= '          <code class="failure">Failure -</code> Internal error for <code>imagecopyresampled()</code>';
        $error_log .= 'with <code>'.$file_dir.'/'.$file_short.'.'.$file_ext.'</code><br />'."\n";
        return $error_log;
      }
    }
  }
  
  if (!call_user_func('image'.$dst_format, $dst, $file_dir.'/'.$conf['thumbs'].'/'.$conf['prefix_thumbnail'].$file_short.'.'.$conf['thumbnail_format']))
  {
    $error_log .= '          <code class="failure">Failure -</code> Can not write <code>';
    $error_log .= $file_dir.'/'.$conf['thumbs'].'/'.$conf['prefix_thumbnail'].$file_short.'.'.$file_ext.'</code> to generate thumbnail<br />'."\n";
    return $error_log;
  }
  
  $error_log .= '          <code class="success">Success -</code> Thumbnail generated for <code>';
  $error_log .= $file_dir.'/'.$file_short.'.'.$file_ext.'</code><br />'."\n";

  //~ pwg_log('<<<<< pwg_icon_file() returns '.var_export($error_log, TRUE).' <<<<<'."\n");
  return $error_log; 
}

/**
 * completes xml line <element .../> and returns error log
 *
 * @param string $file
 * @param string &$line
 * @return string
 */
function pwg_scan_file($file_full, &$line)
{
  //~ pwg_log('>>>>> pwg_scan_file($file = '.var_export($file_full, TRUE).', $line = '.var_export($line, TRUE).') >>>>>'."\n");
  
  global $conf, $pwg_conf;
  
  $error_log ='';
  $icon_log = '';
  
  $file_base  = basename($file_full);
  $file_short = pwg_get_filename_wo_extension($file_base);
  $file_ext   = pwg_get_file_extension($file_base);
  $file_dir   = dirname($file_full);

  $element['file'] = $file_base;
  $element['path'] = dirname($pwg_conf['this_url']).substr($file_dir, 1).'/'.$file_base;
  
  if (in_array($file_ext, $conf['picture_ext']))
  {
    // Here we scan a picture : thumbnail is mandatory, high is optionnal, representative is not scanned
    $element['tn_ext'] = pwg_get_thumbnail_ext($file_dir, $file_short, $file_ext, $error_log, $icon_log);
    if ($element['tn_ext'] != '')
    {
      // picture has a thumbnail, get image width, heigth, size in Mo
      $element['filesize'] = floor(filesize($file_full) / 1024);
      if ($image_size = getimagesize($file_full))
      {
        $element['width'] = $image_size[0];
        $element['height'] = $image_size[1];
      }
      
      // get high resolution
      if (pwg_get_high($file_dir, $file_base))
      {
        $element['has_high'] = 'true';
        
        $high_file = $file_dir.'/'.$conf['high'].'/'.$file_base;
        $element['high_filesize'] = floor(filesize($high_file) / 1024);
      }
      
      // get EXIF meta datas
      if ($conf['use_exif'])
      {
        // Verify activation of exif module
        if (extension_loaded('exif'))
        {
          if ($exif = read_exif_data($file_full))
          {
            foreach ($conf['use_exif_mapping'] as $pwg_key => $exif_key )
            {
              if (isset($exif[$exif_key]))
              {
                if ( in_array($pwg_key, array('date_creation','date_available') ) )
                {
                  if (preg_match('/^(\d{4}):(\d{2}):(\d{2})/', $exif[$exif_key], $matches))
                    {
                      $element[$pwg_key] = $matches[1].'-'.$matches[2].'-'.$matches[3];
                    }
                }
                else
                {
                  $element[$pwg_key] = $exif[$exif_key];
                }
              }
            }
          }
        }
      }
      
      // get IPTC meta datas
      if ($conf['use_iptc'])
      {
        $iptc = pwg_get_sync_iptc_data($file_full);
        if (count($iptc) > 0)
        {
          foreach (array_keys($iptc) as $key)
          {
            $element[$key] = addslashes($iptc[$key]);
          }
        }
      }
      
    }
    else
    {
      $error_log .= '          <code class="failure">Failure -</code> Thumbnail is missing for <code>'.$file_dir.'/'.$file_base.'</code>';
      $error_log .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png" title="'.$file_dir.'/thumbnail/'.$conf['prefix_thumbnail'].$file_short;
      $error_log .= '.xxx ('.implode(', ', $conf['picture_ext']).')" /><br />'."\n";
    }
  }
  else
  {
    // Here we scan a non picture file : thumbnail and high are unused, representative is optionnal
    $element['tn_ext'] = pwg_get_thumbnail_ext($file_dir, $file_short, $file_ext, $log);
    $ext = pwg_get_representative_ext($file_dir, $file_short);
    if ($ext != '')
    {
      $element['representative_ext'] = $ext;
    }
    $element['filesize'] = floor(filesize($file_full) / 1024);
  }
  
  if (strlen($error_log) == 0)
  {
    $line = pwg_get_indent('element').'<element ';
    foreach($element as $key => $value)
    {
      $line .= $key.'="'.$value.'" ';
    }
    $line .= '/>'."\n";
  }

  // Adding Icon generation log to message
  $error_log .= $icon_log;

  //~ pwg_log('<<<<< pwg_scan_file() returns '.var_export($error_log, TRUE).' <<<<<'."\n");
  return $error_log;
}

/**
 * returns current level in tree
 *
 * @return integer
 */
function pwg_get_level($dir)
{
  //~ pwg_log('>>>>> pwg_get_level($dir = '.var_export($dir, TRUE).') >>>>>'."\n");
  
  $level = substr_count($dir, '/') - 1; // -1 because of ./ at the beginning of path
  
  //~ pwg_log('<<<<< pwg_get_level() returns '.var_export($level, TRUE).' <<<<<'."\n");
  return $level;
}

/**
 * returns indentation of element 
 *
 * @param string $element_type : 'root', 'element', 'dir'
 * @return string
 */
function pwg_get_indent($element_type)
{
  //~ pwg_log('>>>>> pwg_get_indent($element_type = '.var_export($element_type, TRUE).') >>>>>'."\n");
  
  $level = substr_count($_SESSION['scan_list_fold'][0], '/') - 1; // because of ./ at the beginning
  switch($element_type)
  {
    case 'dir' :
    {
      $indent = str_repeat(' ', 2*pwg_get_level($_SESSION['scan_list_fold'][0]));
      break;
    }
    case 'root' :
    {
      $indent = str_repeat(' ', 2*pwg_get_level($_SESSION['scan_list_fold'][0])+2);
      break;
    }
    case 'element' :
    {
      $indent = str_repeat(' ', 2*pwg_get_level($_SESSION['scan_list_fold'][0])+4);
      break;
    }
    default :
    {
      $indent = '';
      break;
    }
  }
  
  //~ pwg_log('<<<<< pwg_get_indent() returns '.var_export(strlen($indent), TRUE).' spaces <<<<<'."\n");
  return $indent;
}

/**
 * create index.php in directory and reserved sub_directories, return logs
 *
 * @param string dir
 * @return string
 */
function pwg_protect_directories($directory)
{
  //~ pwg_log('>>>>> pwg_protect_directories($directory = '.var_export($directory, true).') >>>>>'."\n");
  
  global $conf;
  
  $error_log = '';
  $dirlist = array($directory, $directory.'/'.$conf['thumbs'], $directory.'/'.$conf['high'], $directory.'/'.$conf['represent']);
  
  foreach ($dirlist as $dir)
  {
    if (file_exists($dir))
    {
      if (!file_exists($dir.'/index.php'))
      {
        $file = @fopen($dir.'/index.php', 'w');
        if ($file != false)
        {
          fwrite($file, $pwg_conf['protect_content']); // the return code should be verified
          $error_log .= '          <code class="success">Success -</code> index.php created in directory <a href="'.$dir.'">'.$dir."</a><br />\n";
        }
        else
        {
          $error_log .= '          <code class="failure">Failure -</code> Can not create index.php in directory <code>'.$dir."</code><br />\n";
        }
      }
      else
      {
        if ($conf['protect_warnings'])
        {
          $error_log .= '          <code class="warning">Warning -</code> index.php already exists in directory <a href="'.$dir.'">'.$dir."</a><br />\n";
        }
      }
    }
  }
  
  //~ pwg_log('<<<<< pwg_protect_directories() returns '.var_export($error_log, true).' <<<<<'."\n");
  return $error_log;
}

/**
 * returns file extension (.xxx)
 *
 * @param string $file
 * @return string
 */
function pwg_get_file_extension($file)
{
  //~ pwg_log('>>>>> pwg_get_file_extension($file = '.var_export($file, true).') >>>>>'."\n");
  
  $ext = substr(strrchr($file, '.'), 1, strlen ($file));
  
  //~ pwg_log('<<<<< pwg_get_file_extension() returns '.var_export($ext, true).' <<<<<'."\n");
  return $ext;
}

/**
 * completes directory list of supported files and returns error logs
 *
 * @param string $directory
 * @return string
 */
function pwg_get_file_list($directory)
{
  //~ pwg_log('>>>>> pwg_get_file_list($directory = '.var_export($directory, true).') >>>>>'."\n");
  
  global $conf, $pwg_conf;

  $errorLog = '';
  $dir = opendir($directory);
  while (($file = readdir($dir)) !== false)
  {
    switch (filetype($directory."/".$file))
    {
      case 'file' :
      {
        if (in_array(pwg_get_file_extension($file), $conf['file_ext']))
        {
          // The file pointed is a regular file with a supported extension
          array_push($_SESSION['scan_list_file'], $directory.'/'.$file);
          //~ pwg_log('--->> Push in $_SESSION[scan_list_file] value "'.$directory.'/'.$file.'"'."\n");
          if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $file))
          {
            $errorLog .= '          <code class="failure">Failure -</code> Invalid file name for <code>'.$file.'</code> in <code>'.$directory.'</code>';
            $errorLog .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png"';
            $errorLog .= ' title="Name should be composed of letters, figures, -, _ or . ONLY" /><br />'."\n";
          }
        }
        break; // End of filetype FILE
      }
      case 'dir' :
      {
        if(!in_array($file, $pwg_conf['reserved_directory_names']))
        {
          // The file pointed is a directory but neither system directory nor reserved by PWG
          array_push($_SESSION['scan_list_fold'], $directory.'/'.$file);
          //~ pwg_log('--->> Push in $_SESSION[scan_list_fold] value "'.$directory.'/'.$file.'"'."\n");
          if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $file))
          {
            $errorLog .= '          <code class="failure">Failure -</code> Invalid directory name for <code>'.$directory.'/'.$file.'</code>';
            $errorLog .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png"';
            $errorLog .= ' title="Name should be composed of letters, figures, -, _ or . ONLY" /><br />'."\n";
          }
        }
        break; // End of filetype DIR
      }
      case 'fifo' :
      case 'char' :
      case 'block' :
      case 'link' :
      case 'unknown':
      default :
      {
        // PWG does not manage these cases
        break;
      }
    }
  }
  closedir($dir);
  
  //~ pwg_log('<<<<< pwg_get_file_list() returns '.var_export($errorLog, true).' <<<<<'."\n");

  return $errorLog;
}

/**
 * returns a float value coresponding to the number of seconds since the
 * unix epoch (1st January 1970) and the microseconds are precised :
 * e.g. 1052343429.89276600
 *
 * @return float
 */
function pwg_get_moment()
{
  //~ pwg_log('>>>>> pwg_get_moment() >>>>>'."\n");
  
  $t1 = explode(' ', microtime());
  $t2 = explode('.', $t1[0]);
  $t2 = $t1[1].'.'.$t2[1];

  //~ pwg_log('<<<<< pwg_get_moment() returns '.var_export($t2, true).' <<<<<'."\n");
  return $t2;
}

/**
 * return true if HTTP_REFERER and PHP_SELF are similar
 *
 * return boolean
 */
function pwg_referer_is_me()
{
  global $pwg_conf;

  //~ pwg_log('>>>>> pwg_referer_is_me() >>>>>'."\n");
  
  $response = false;
  $caller = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

  if (strcasecmp($pwg_conf['this_url'], $caller) == 0) {
    $response = true;
  }

  //~ pwg_log('<<<<< pwg_referer_is_me() returns '.var_export($response, true).' <<<<<'."\n");
  return $response;
}

// +-----------------------------------------------------------------------+
// |                                pwg_<ACTION>_<STEP> Functions          |
// +-----------------------------------------------------------------------+

function pwg_test_start()
{
  //~ pwg_log('>>>>> pwg_test_start() >>>>>'."\n");

  global $g_message, $conf;
  
  if (isset($_REQUEST['version']))
  {
    if ($_REQUEST['version'] != $conf['version'])
    {
      $g_message = '0';
    }
    else
    {
      $g_message = '1';
    }
  }
  else
  {
    $g_message  = '1';
  }
  $_SESSION['scan_step'] = 'exit';
  
  //~ pwg_log('<<<<< pwg_test_start() <<<<<'."\n");
}

function pwg_test_exit()
{
  //~ pwg_log('>>>>> pwg_test_exit() >>>>>'."\n");

  global $g_header, $g_message, $g_footer, $conf, $pwg_conf;
 
  if (pwg_referer_is_me())
  {
    $g_header  = ' : <span class="success">Test</span>'."\n";
    $g_footer  = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
    
    // Write version
    $g_message  = '        <h3>Script version</h3>'."\n";
    $g_message .= '        This script is tagged : <code class="failure">'.$conf['version'].'</code>'."\n";
    // write GD support
    if (!is_callable('gd_info'))
    {
      $g_message .= '        <code class="failure">Failure -</code> Your server can not generate imagess<br />'."\n";
    }
    else
    {
      $info = gd_info();
      $gd_full_version = preg_replace('/[[:alpha:][:space:]()]+/', '', $info['GD Version']);
      list($gd_version) = preg_split('/[.]+/', $gd_full_version);
      
      $g_message .= '        <h3>Image generation</h3>'."\n";
      $g_message .= '        <code class="success">Success -</code> Your server can generate images<br />'."\n";
      $g_message .= '        <code class="warning">Warning -</code> Your server support GD'.$gd_version.' (v'.$gd_full_version.')<br />'."\n";
      $format_list = array();
      $format = ($info['GIF Create Support']) ? '<code>gif</code>' : NULL;
      array_push($format_list, $format);
      $format = ((isset($info['JPG Support']) and $info['JPG Support']) or (isset($info['JPEG Support']) and $info['JPEG Support'])) ? '<code>jpg</code>' : NULL;
      array_push($format_list, $format);
      $format = ($info['PNG Support']) ? '<code>png</code>' : NULL;
      array_push($format_list, $format);
      $g_message .= '        <code class="warning">Warning -</code> Your server support format: '.implode(', ', $format_list)."\n";
    }
    
    $g_message .= '        <h3>Directory parsing</h3>'."\n";
    if ($pwg_conf['safe_mode'])
    {
      $g_message .= '        <code class="warning">Warning -</code> Your server does not support to resize execution time'."\n";
    }
    else
    {
      $g_message .= '        <code class="success">Success -</code> Your server supports to resize execution time'."\n";
    }
  }
  else
  {
    // compare version in GET parameter with $conf['version'] 
    if ($g_message == '1')
    {
      exit('<pre>PWG-INFO-2: test successful</pre>');
    }
    else
    {
      exit('<pre>PWG-ERROR-4: Piwigo versions differs</pre>');
    }
  }
  
  //~ pwg_log('<<<<< pwg_test_exit() <<<<<'."\n");
}

function pwg_clean_start()
{
  //~ pwg_log('>>>>> pwg_clean_start() >>>>>'."\n");
  
  global $g_message;
  
  if(@unlink('./listing.xml'))
  {
    $g_message = '1';
  }
  else
  {
    $g_message = '0';
  }

  $_SESSION['scan_step'] = 'exit';
  
  //~ pwg_log('<<<<< pwg_clean_start() <<<<<'."\n");
}

function pwg_clean_exit()
{
  //~ pwg_log('>>>>> pwg_clean_exit() >>>>>'."\n");

  global $g_header, $g_message, $g_footer, $conf, $pwg_conf;
  
  if(pwg_referer_is_me())
  {
    $g_header = ' : <span class="success">Clean</span>';
    if ($g_message == '1')
    {
      $g_message = '        <code class="success">Success -</code> <code>listing.xml</code> file deleted'."\n";
    }
    else
    {
      $g_message = '        <code class="failure">Failure -</code> <code>listing.xml</code> does not exist or is read only'."\n";
    }
    $g_footer = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>';
  }
  else
  {
    if ($g_message == '1')
    {
      exit('<pre>PWG-INFO-3 : listing.xml file deleted</pre>');
    }
    else
    {
      exit('<pre>PWG-ERROR-3 : listing.xml does not exist</pre>');
    }
  }
  
  //~ pwg_log('<<<<< pwg_clean_exit() <<<<<'."\n");
}

function pwg_generate_start()
{
  //~ pwg_log('>>>>> pwg_generate_start() >>>>>'."\n");
  //~ pwg_log("GENARATE start >>>\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE start >>>\n");

  global $g_listing, $pwg_conf, $conf;
  
  // Flush line <informations>
  $xml_header_url = dirname($pwg_conf['this_url']);
  $xml_header_date = date('Y-m-d');
  $xml_header_version = htmlentities($conf['version']);
  
  $attrs = array();
  if ($conf['use_iptc'])
  {
    $attrs = array_merge($attrs, array_keys($conf['use_iptc_mapping']) );
  }
  if ($conf['use_exif'])
  {
    $attrs = array_merge($attrs, array_keys($conf['use_exif_mapping']) );
  }
  $xml_header_metadata = implode(',',array_unique($attrs));
  
  $xml_header = '<informations';
  $xml_header .= ' generation_date="'.$xml_header_date.'"';
  $xml_header .= ' phpwg_version="'.$xml_header_version.'"';
  $xml_header .= ' metadata="'.$xml_header_metadata.'"';
  $xml_header .= ' url="'.$xml_header_url.'/"';
  $xml_header .= '>'."\n";
  
  fwrite($g_listing, $xml_header);
  
  // Initialization of directory and file lists
  $_SESSION['scan_list_fold'] = array();
  $_SESSION['scan_list_file'] = array();
  $_SESSION['scan_logs'] .= pwg_get_file_list('.');
  sort($_SESSION['scan_list_fold']);
          
  // Erase first file list because root directory does not contain images.
  $_SESSION['scan_list_file'] = array();
            
  // What are we doing at next step
  if(count($_SESSION['scan_list_fold']) > 0)
  {
    $_SESSION['scan_step'] = 'list';
  }
  else
  {
    $_SESSION['scan_step'] = 'stop';
  }
  
  //~ pwg_log("GENARATE start <<<\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE start <<<\n");
  //~ pwg_log('<<<<< pwg_generate_start() <<<<<'."\n");
}

function pwg_generate_list()
{
  //~ pwg_log('>>>>> pwg_generate_list() >>>>>'."\n");
  //~ pwg_log("GENARATE list >>>\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE list >>>\n");
  
  global $g_listing;
  
  // Flush line <dirX name=""> in xml file
  $dirname = basename($_SESSION['scan_list_fold'][0]);
  $line = pwg_get_indent('dir').'<dir'.pwg_get_level($_SESSION['scan_list_fold'][0]).' name="'.$dirname.'">'."\n";
  fwrite($g_listing, $line);
  
  // Get list of files and directories
  $_SESSION['scan_logs'] .= pwg_get_file_list($_SESSION['scan_list_fold'][0]);
  sort($_SESSION['scan_list_fold']); // Mandatory to keep the tree order
  sort($_SESSION['scan_list_file']); // Easier to read when sorted
  
  // Flush line <root>
  $line = pwg_get_indent('root').'<root>'."\n";
  fwrite($g_listing, $line);
  
  // What are we doing at next step
  $_SESSION['scan_step'] = 'scan';
  
  //~ pwg_log("GENARATE list <<<\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE list <<<\n");
  //~ pwg_log('<<<<< pwg_generate_list() <<<<<'."\n");
}

function pwg_generate_scan()
{
  //~ pwg_log('>>>>> pwg_generate_scan() >>>>>'."\n");
  //~ pwg_log("GENARATE scan >>>\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE scan >>>\n");
  
  global $g_listing, $conf;
  
  while (pwg_continue() and count($_SESSION['scan_list_file']) > 0)
  {
    $line = '';
    $_SESSION['scan_logs'] .= pwg_scan_file($_SESSION['scan_list_file'][0], $line);
    
    if (strlen($line) > 0)
    {
      fwrite($g_listing, $line);
    }
    //~ pwg_log('---<< Pull of $_SESSION[scan_list_file] value "'.$_SESSION['scan_list_file'][0].'"'."\n");
    array_shift($_SESSION['scan_list_file']);
    $_SESSION['scan_cnt_file']++;
  }
          
  if (count($_SESSION['scan_list_file']) <= 0)
  {
    $_SESSION['scan_step'] = 'prot';
  }
  
  //~ pwg_log("GENERATE scan <<<\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE scan <<<\n");
  //~ pwg_log('<<<<< pwg_generate_scan() <<<<<'."\n");
}

function pwg_generate_prot()
{
  //~ pwg_log('>>>>> pwg_generate_prot() >>>>>'."\n");
  //~ pwg_log("GENARATE prot >>>\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE prot >>>\n");
  
  global $conf, $g_listing;
  
  // Flush line </root>
  $line = pwg_get_indent('root').'</root>'."\n";
  fwrite($g_listing, $line);
  
  if ($conf['protect'])
  {
    $_SESSION['scan_logs'] .= pwg_protect_directories($_SESSION['scan_list_fold'][0]);
  }
  
  // How many directories to close
  $current_level = pwg_get_level($_SESSION['scan_list_fold'][0]);
  if (isset($_SESSION['scan_list_fold'][1]))
  {
    //~ pwg_log('---<< Pull of $_SESSION[scan_list_fold] value "'.$_SESSION['scan_list_fold'][0].'"'."\n");
    array_shift($_SESSION['scan_list_fold']);
    $_SESSION['scan_cnt_fold']++;
    $next_level = pwg_get_level($_SESSION['scan_list_fold'][0]);
    $_SESSION['scan_step'] = 'list';
  }
  else
  {
    $next_level = -1;
    $_SESSION['scan_cnt_fold']++;
    $_SESSION['scan_step'] = 'stop';
  }
  
  if ($current_level == $next_level)
  {
    fwrite($g_listing, pwg_close_level($current_level, 1));
  }
  else
  {
    if (($current_level > $next_level))
    {
      fwrite($g_listing, pwg_close_level($current_level, $current_level-$next_level+1));
    } // Nothing to do if current_level < next_level
  }

  //~ pwg_log("GENERATE prot <<<\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE prot <<<\n");
  //~ pwg_log('<<<<< pwg_generate_prot() <<<<<'."\n");
}

function pwg_generate_stop()
{
  //~ pwg_log('>>>>> pwg_generate_stop() >>>>>'."\n");
  //~ pwg_log("GENARATE stop >>>\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE stop >>>\n");
  
  global $pwg_conf, $g_listing, $g_header, $g_message, $g_footer;
  
  // Flush line </informations>
  fwrite($g_listing, '</informations>'."\n");
  
  // backup error log before cleaning session
  $time_elapsed = number_format(pwg_get_moment() - $_SESSION['scan_time'], 3, '.', ' ');
  
  $g_header   = ' : <span class="success">Generate</span>';
  $g_message  = '        <div>'."\n".$_SESSION['scan_logs'].'        </div>'."\n";
  $g_message .= '        <div><code class="success">'.$_SESSION['scan_cnt_fold'].'</code> directories parsed<br />'."\n";
  $g_message .= '        <code class="success">'.$_SESSION['scan_cnt_file'].'</code> files scanned</div>'."\n";
  $g_message .= '        <div>View <a href="listing.xml">listing.xml</a></div>'."\n";
  $g_message .= '        <div style="{text-align: right;}">Listing generated in : <code>'.$time_elapsed.' s</code></div>';
  $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>';
      
  // What are we doing at next step
  $_SESSION['scan_step'] = 'exit';
  
  //~ pwg_log("GENARATE stop <<<\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE stop <<<\n");
  //~ pwg_log('<<<<< pwg_generate_stop() <<<<<'."\n");
}

// +-----------------------------------------------------------------------+
// |                                ALWAYS CALLED FUNCTIONS                |
// +-----------------------------------------------------------------------+

/**
 * This function check step and time ellapsed to determine end of loop
 *
 * @return bool
 */
function pwg_continue()
{
  //~ pwg_log('>>>>> pwg_continue() >>>>>'."\n");
  
  global $conf, $pwg_conf, $g_refresh, $g_header, $g_message, $g_footer, $start_time;
  
  if (!isset($_SESSION['scan_step']) or $_SESSION['scan_step'] == 'exit')
  {
    // evident end of process
    $return = false;
  }
  else
  {
    if ($pwg_conf['safe_mode'] or $conf['force_refresh_method'])
    {
      // can not reset the time
      $time_elapsed = pwg_get_moment() - $start_time;
      if ($time_elapsed < $conf['max_execution_time'])
      {
        $return = true;
      }
      else
      {
        $start_time = $_SESSION['scan_time'];
        $formated_time = number_format(pwg_get_moment() - $start_time, 3, '.', ' ');

        $g_refresh = '<meta http-equiv="Refresh" content="'.$conf['refresh_delay'].'">'."\n";
        $g_header  = ' : <span class="success">'.ucfirst($_SESSION['scan_action']).'</span>';
        $g_message = '';
        if ($_SESSION['scan_cnt_fold'] != 0)
        {
          $g_message .= '<code class="success">'.$_SESSION['scan_cnt_fold'].'</code> directories scanned<br />'."\n";
        }
        if ($_SESSION['scan_cnt_file'] != 0)
        {
          $g_message .= '<code class="success">'.$_SESSION['scan_cnt_file'].'</code> files scanned<br />'."\n";
        }
        $nb = count($_SESSION['scan_list_fold']);
        if ($nb > 0)
        {
          $g_message .= '<code class="warning">'.$nb.'</code> directories to scan<br />'."\n";
        }
        $nb = count($_SESSION['scan_list_file']);
        if ($nb > 0)
        {
          $g_message .= '<code class="warning">'.$nb.'</code> files to scan<br />'."\n";
        }
        $g_message .= '        <div style="{text-align: right;}">Time elapsed : <code>'.$formated_time.' s</code></div>';
        $g_footer  = '<a href="'.$pwg_conf['this_url'].'?action='.$_SESSION['scan_action'].'" title="Continue"><img src="'.$pwg_conf['icon_dir'].'right.png" /></a>'."\n";
        
        $return = false;
      }
    }
    else
    {
      // reset the time
      set_time_limit(intval(ini_get('max_execution_time')));
      $return = true;
    }
  }
  //~ pwg_log('<<<<< pwg_continue() returns '.var_export($return, true).' <<<<<'."\n");
  
  return $return;
}

/**
 * This function :
 * -> Verify the script call
 * -> Lock the script
 * -> Open listing.xml if action is 'generate'
 * -> Initialize output and session variables
 *
 * @return nothing
 */
function pwg_init()
{
  //~ pwg_log('>>>>> pwg_init() >>>>>'."\n");

  global $g_message, $g_listing, $g_footer, $conf, $pwg_conf, $start_time;
  $init_message = '';
  
  // Lock other script sessions, this lock will be remove during 'exit' step
  if (!isset($_SESSION['scan_step']))
  {
    $fp = @fopen(__FILE__.'.lock', 'x+'); // return false if __FILE__.lock exists or if cannot create
    if ($fp == false)
    {
      $g_header   = $_SESSION['scan_action'];
      $g_message  = '        <code class="failure">Failure -</code> Another script is running';
      $g_message .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png" title="Delete file '.__FILE__.'.lock and retry" />';
      $g_message .= "\n";
      $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
      $_SESSION['scan_step'] = 'exit';
      //~ pwg_log('<<<<< pwg_init() failure <<<<<'."\n");
      return;
    }
    else
    {
      fwrite($fp, session_id()); // Writing session_id to trace lock
      fclose($fp);
      $_SESSION['scan_step'] = 'init';
    }
  }
  
  // Verify and backup parameter action. This backup will be removed during step 'exit'
  if (isset($_REQUEST['action']))
  {
    if (in_array($_REQUEST['action'], $pwg_conf['scan_action']))
    {
      if (isset($_SESSION['scan_action']))
      {
        if ($_SESSION['scan_action'] != $_REQUEST['action'])
        {
          // Fatal error
          $g_message  = '        <code class="failure">Failure -</code> Parameter <code>action</code> differs between url and session';
          $g_message .= "\n";
          $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
          $_SESSION['scan_step'] = 'exit';
          //~ pwg_log('<<<<< pwg_init() failure <<<<<'."\n");
          return;
        }
      }
      else
      {
        $_SESSION['scan_action'] = $_REQUEST['action'];
      }
    }
    else
    {
      // Fatal error
      $g_message  = '        <code class="failure">Failure -</code> Problem with <code>action</code> parameter';
      $g_message .= ' <img src="'.$pwg_conf['icon_dir'].'add_tag.png" title="empty, '.implode(', ', $pwg_conf['scan_action']).'" />';
      $g_message .= "\n";
      $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
      $_SESSION['scan_step'] = 'exit';
      //~ pwg_log('<<<<< pwg_init() failure <<<<<'."\n");
      return;
    }
  }
  else
  {
    // Here we are on welcome page
    $g_message  = '        <ul>'."\n";
    $g_message .= '          <li><a href="'.$pwg_conf['this_url'].'?action=test" title="Display/Compare script version">Test</a></li>'."\n";
    $g_message .= '          <li><a href="'.$pwg_conf['this_url'].'?action=clean" title="Delete listing.xml if exists">Clean</a></li>'."\n";
    $g_message .= '          <li><a href="'.$pwg_conf['this_url'].'?action=generate" title="Scan all images from this directory and write informations in listing.xml">Listing</a></li>'."\n";
    $g_message .= '        </ul>'."\n";
    $g_footer   = '<a href="'.$conf['gallery'].'/admin.php?page=site_manager" title="Main gallery :: site manager">';
    $g_footer  .= '<img src="'.$pwg_conf['icon_dir'].'home.png" /></a>'."\n";
    $_SESSION['scan_step'] = 'exit';
    $_SESSION['scan_action'] = '';
  }
  
  // Actions to do at the init of generate
  if ($_SESSION['scan_action'] == 'generate')
  {
    // Open XML file
    $mode = ($_SESSION['scan_step'] == 'init') ? 'w' : 'a'; // Erase old listing.xml at the beginning of generation (mode w)
    $g_listing = @fopen('listing.xml', $mode);
    if ($g_listing === false)
    {
      $g_header   = $_SESSION['scan_action'];
      $g_message  = '        <code class="failure">Failure -</code> Can not write file <code>listing.xml</code>';
      $g_message .= "\n";
      $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
      $_SESSION['scan_step'] = 'exit';
      //~ pwg_log('<<<<< pwg_init() failure <<<<<'."\n");
      return;
    }
    
    // Check graphical capabilities
    $init_message = pwg_check_graphics();
  }
    
  // Initializing session counters. This counters will be completely unset during step 'exit'
  if ($_SESSION['scan_step'] == 'init')
  {
    $_SESSION['scan_list_file'] = array();
    $_SESSION['scan_list_fold'] = array();
    $_SESSION['scan_cnt_file'] = 0;
    $_SESSION['scan_cnt_fold'] = 0;
    $_SESSION['scan_time'] = $start_time;
    $_SESSION['scan_step'] = 'start';
    $_SESSION['scan_logs'] = $init_message;
  }
  
  //~ pwg_log('<<<<< pwg_init() success <<<<<'."\n");
}

/**
 * This function :
 * -> Close listing.xml if action is 'generate'
 * -> Unlock the script
 * -> Erase session variables
 *
 * @return nothing
 */
function pwg_exit()
{
  //~ pwg_log('>>>>> pwg_exit() >>>>>'."\n");
  
  global $g_listing;
  
  // Close XML file
  if ($_SESSION['scan_action'] == 'generate' and $g_listing != false)
  {
    fclose($g_listing);
  }
  
  // Unlock script
  unlink(__FILE__.'.lock');
  
  // Erase session counters
  unset($_SESSION['scan_list_file']);
  unset($_SESSION['scan_list_fold']);
  unset($_SESSION['scan_cnt_file']);
  unset($_SESSION['scan_cnt_fold']);
  unset($_SESSION['scan_time']);
  unset($_SESSION['scan_step']);
  $local_action = $_SESSION['scan_action'];
  unset($_SESSION['scan_action']);
  unset($_SESSION['scan_logs']);
  session_destroy();
  
  // Call specific action post process
  if (is_callable('pwg_'.$local_action.'_exit'))
  {
    call_user_func('pwg_'.$local_action.'_exit');
  }
  
  //~ pwg_log('<<<<< pwg_exit() <<<<<'."\n");
}

// +-----------------------------------------------------------------------+
// |                                Script                                 |
// +-----------------------------------------------------------------------+
session_save_path('.');
session_start();

$start_time = pwg_get_moment();

// Initializing message for web page
$g_refresh = '';
$g_header  = '';
$g_message = '';
$g_footer  = '';
$g_listing = '';

pwg_init();

while(pwg_continue())
{
  if (is_callable('pwg_'.$_SESSION['scan_action'].'_'.$_SESSION['scan_step']))
  {
    call_user_func('pwg_'.$_SESSION['scan_action'].'_'.$_SESSION['scan_step']); // Run the step : start, list, scan, stop are available
  }
  else
  {
    $g_header   = $_SESSION['scan_action'];
    $g_message  = '        <code class="failure">Failure -</code> INTERNAL STEP ERROR : <code>pwg_'.$_SESSION['scan_action'].'_'.$_SESSION['scan_step'].'()</code> undefined';
    $g_message .= "\n";
    $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
    $_SESSION['scan_step'] = 'exit';
  }
}

if ($_SESSION['scan_step'] == 'exit')
{
  pwg_exit();
}

?>
<html>
  <head>
  <?php echo $g_refresh; ?>
  <title>Manage remote gallery</title>
  </head>
    <style type="text/css">
      code {font-weight: bold}
      img {border-style: none; vertical-align: middle}
      ul {list-style-image: url(<?php echo $pwg_conf['icon_dir']; ?>add_tag.png)}
      .success {color: green}
      .warning {color: orange}
      .failure {color: red}
      .header {text-align: center; font-variant: small-caps; font-weight: bold;}
      .p {color: #F93;}
      .w {color: #ccc;}
      .g {color: #69C;}
      .pwg {text-decoration: none; border-bottom-style: dotted; border-bottom-width: 1px;}
      .content {width: 75%; position: absolute; top: 10%; left: 12%;}
      .footer {text-align: right;}
      .pwg_block {float: left;}
    </style>
  <body>
    <div class="content">
      <fieldset class="header">
        <span class="p">Pi</span>
        <span class="w">wi</span>
        <span class="g">go</span>
        &nbsp;remote site<? echo $g_header; ?>
      </fieldset>
      <fieldset>
<?php echo $g_message; ?>
      </fieldset>
      <fieldset class="footer">
        <div class="pwg_block">
          Powered by <a href="http://piwigo.org" class="pwg">Piwigo</a>
        </div>
        <?php echo $g_footer; ?>
      </fieldset>
    </div>
  </body>
</html>
