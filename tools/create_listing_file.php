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

// +-----------------------------------------------------------------------+
// |                                User configuration                     |
// +-----------------------------------------------------------------------+

// Srcipt version
$conf['version'] = 'Alligator';

// prefix for thumbnails in "thumbnail" sub directories
$conf['prefix_thumbnail'] = 'TN-';

// $conf['file_ext'] lists all extensions (case insensitive) allowed for your PhpWebGallery installation
$conf['file_ext'] = array('jpg','JPG','png','PNG','gif','GIF','mpg','zip', 'avi','mp3','ogg');

// $conf['picture_ext'] must be a subset of $conf['file_ext']
$conf['picture_ext'] = array('jpg','JPG','png','PNG','gif','GIF');

// URL of main gallery
$conf['gallery'] = 'http://';

// max excution time before refresh in seconds
$conf['max_execution_time'] = (5*ini_get('max_execution_time'))/6; // 25 seconds with default PHP configuration

// refresh delay is seconds
$conf['refresh_delay'] = 0;

// $conf['file_ext'] lists all extensions (case insensitive) allowed for your PhpWebGallery installation
$conf['file_ext'] = array('jpg','JPG','jpeg','JPEG','png','PNG','gif','GIF','mpg','zip', 'avi','mp3','ogg');

// $conf['use_exif'] set to true if you want to use Exif information
$conf['use_exif'] = true;

// use_exif_mapping: same behaviour as use_iptc_mapping
$conf['use_exif_mapping'] = array(
  'date_creation' => 'DateTimeOriginal'
  );

// $conf['use_iptc'] set to true if you want to use IPTC informations of the
// element according to get_sync_iptc_data function mapping, otherwise, set
// to false
$conf['use_iptc'] = false;

// use_iptc_mapping : in which IPTC fields will PhpWebGallery find image
// information ? This setting is used during metadata synchronisation. It
// associates a phpwebgallery_images column name to a IPTC key
$conf['use_iptc_mapping'] = array(
  'keywords'        => '2#025',
  'date_creation'   => '2#055',
  'author'          => '2#122',
  'name'            => '2#005',
  'comment'         => '2#120');

// index.php content for command 'protect'
$conf['protect_content'] = '<?php header("Location: '.$conf['gallery'].'") ?>';

// directories names 
$conf['thumbs'] = 'thumbnail'; // thumbnails
$conf['high'] = 'pwg_high'; // high resolution
$conf['represent'] = 'pwg_representative'; // non pictures representative files

// +-----------------------------------------------------------------------+
// |                                Advanced script configuration          |
// +-----------------------------------------------------------------------+

// url of icon directory in yoga template
$pwg_conf['icon_dir'] = $conf['gallery'].'/template/yoga/icon/';

// list of actions managed by this script
$pwg_conf['scan_action'] = array('clean', 'test', 'generate', 'protect');

// url of this script
$pwg_conf['this_url'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

// list of reserved directory names
$pwg_conf['reserved_directory_names'] = array($conf['thumbs'], $conf['high'], $conf['represent'], ".", "..", ".svn");

// content of index.php generated in protect action
$pwg_conf['protect_content'] = '<?php header("Location: '.$conf['gallery'].'") ?>';

// backup of PHP safe_mode INI parameter (used for time limitation)
$pwg_conf['safe_mode'] = (ini_get('safe_mode') == '1') ? true : false;

// Error level management
// true  : show warnings
// false : hide warnings
$pwg_conf['warning']['protect'] = true;

// +-----------------------------------------------------------------------+
// |                               functions                               |
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

  $iptc['keywords'] = implode(',', array_unique(explode(',', $iptc['keywords'])));

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
 * return extension of the thumbnail
 *
 * @param string $file_dir
 * @param string $file_short
 * @return string
 */
function pwg_get_thumbnail_ext($file_dir, $file_short)
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
  
  //~ pwg_log('<<<<< pwg_get_thumbnail_ext() returns '.var_export($thumb_ext, TRUE).' <<<<<'."\n");
  return $thumb_ext; 
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
  
  $file_base  = basename($file_full);
  $file_short = pwg_get_filename_wo_extension($file_base);
  $file_ext   = pwg_get_file_extension($file_base);
  $file_dir   = dirname($file_full);

  $element['file'] = $file_base;
  $element['path'] = 'http://'.dirname($_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']).substr($file_dir, 1).'/'.$file_base;
  
  if (in_array($file_ext, $conf['picture_ext']))
  {
    // Here we scan a picture : thumbnail is mandatory, high is optionnal, representative is not scanned
    $element['tn_ext'] = pwg_get_thumbnail_ext($file_dir, $file_short);
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
    $ext = pwg_get_representative_ext($file_dir, $file_short);
    if ($ext != '')
    {
      $element['representative_ext'] = $ext;
    }
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
  global $conf, $pwg_conf;
  
  //~ pwg_log('>>>>> pwg_protect_directories($directory = '.var_export($directory, true).') >>>>>'."\n");
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
          $_SESSION['scan_cnt_fold']++;
        }
        else
        {
          $error_log .= '          <code class="failure">Failure -</code> Can not create index.php in directory <code>'.$dir."</code><br />\n";
        }
      }
      else
      {
        if ($pwg_conf['warning']['protect'])
        {
          $error_log .= '          <code class="warning">Warning -</code> index.php already exists in directory <a href="'.$dir.'">'.$dir."</a><br />\n";
          $_SESSION['scan_cnt_fold']++;
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
  //~ pwg_log('>>>>> pwg_referer_is_me() >>>>>'."\n");
  
  $response = false;
  $server = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
  $caller = $_SERVER['HTTP_REFERER'];

  if (strcasecmp($server, $caller) == 0) {
    $response = true;
  }

  //~ pwg_log('<<<<< pwg_referer_is_me() returns '.var_export($response, true).' <<<<<'."\n");
  return $response;
}

// +-----------------------------------------------------------------------+
// |                                pwg_<ACTION>_<STEP> FUNCTIONS          |
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
    $g_message = '        This script is tagged : <code class="failure">'.$conf['version'].'</code>'."\n";
    $g_footer  = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>'."\n";
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
      exit('<pre>PWG-ERROR-4: PhpWebGallery versions differs</pre>');
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

function pwg_protect_start()
{
  //~ pwg_log('>>>>> pwg_protect_start() >>>>>'."\n");
  
  $_SESSION['scan_logs'] = pwg_get_file_list('.');
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
  
  //~ pwg_log('<<<<< pwg_protect_start() <<<<<'."\n");
}

function pwg_protect_list()
{
  //~ pwg_log('>>>>> pwg_protect_list() >>>>>'."\n");

  // Get list of files and directories
  $_SESSION['scan_logs'] .= pwg_get_file_list($_SESSION['scan_list_fold'][0]);
  sort($_SESSION['scan_list_fold']);
  
  // Delete unused file list
  $_SESSION['scan_list_file'] = array();
  
  // Position next step
  $_SESSION['scan_step'] = 'scan';
  
  //~ pwg_log('<<<<< pwg_protect_list() <<<<<'."\n");
}

function pwg_protect_scan()
{
  //~ pwg_log('>>>>> pwg_protect_scan() >>>>>'."\n");
  
  $_SESSION['scan_logs'] .= pwg_protect_directories($_SESSION['scan_list_fold'][0]);
  
  if (isset($_SESSION['scan_list_fold'][1]))
  {
    array_shift($_SESSION['scan_list_fold']);
    $_SESSION['scan_step'] = 'list';
  }
  else
  {
    $_SESSION['scan_step'] = 'stop';
  }
  
  //~ pwg_log('<<<<< pwg_protect_scan() <<<<<'."\n");
}

function pwg_protect_stop()
{
  //~ pwg_log('>>>>> pwg_protect_stop() >>>>>'."\n");
  
  global $g_header, $g_message, $g_footer, $pwg_conf;
  
  $time_elapsed = number_format(pwg_get_moment() - $_SESSION['scan_time'], 3, '.', ' ');

  $g_header   = ' : <span class="success">Protect</span>';
  $g_message  = '        <div>'."\n".$_SESSION['scan_logs'].'        </div>'."\n";
  $g_message .= '        <div><code class="success">'.$_SESSION['scan_cnt_fold'].'</code> directories protected</div>'."\n";
  $g_message .= '        <div style="{text-align: right;}">Gallery protected in : <code>'.$time_elapsed.' s</code></div>';
  $g_footer   = '<a href="'.$pwg_conf['this_url'].'" title="Main menu"><img src="'.$pwg_conf['icon_dir'].'up.png" /></a>';
  
  // What are we doing at next step
  $_SESSION['scan_step'] = 'exit';
  
  //~ pwg_log('<<<<< pwg_protect_stop() <<<<<'."\n");
}

function pwg_generate_start()
{
  //~ pwg_log('>>>>> pwg_generate_start() >>>>>'."\n");
  //~ pwg_log("GENARATE start >>>\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE start >>>\n");

  global $g_listing, $conf;
  
  // Flush line <informations>
  $xml_header_url = 'http://'.dirname($_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']).'/';
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
  $xml_header .= ' url="'.$xml_header_url.'"';
  $xml_header .= '>'."\n";
  
  fwrite($g_listing, $xml_header);
  
  // Initialization of directory and file lists
  $_SESSION['scan_list_fold'] = array();
  $_SESSION['scan_list_file'] = array();
  $_SESSION['scan_logs'] = pwg_get_file_list('.');
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
  
  global $g_listing;
  
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
    // Flush line </root>
    $line = pwg_get_indent('root').'</root>'."\n";
    fwrite($g_listing, $line);
    
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
  }
  
  //~ pwg_log("GENERATE scan <<<\n".var_export($_SESSION['scan_list_fold'], true)."\n".var_export($_SESSION['scan_list_file'], true)."\nGENERATE scan <<<\n");
  //~ pwg_log('<<<<< pwg_generate_scan() <<<<<'."\n");
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
    if ($pwg_conf['safe_mode'])
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
    $g_message .= '          <li><a href="'.$pwg_conf['this_url'].'?action=protect" title="Protect all directories from this point with index.php">Protect</a></li>'."\n";
    $g_message .= '        </ul>'."\n";
    $g_footer   = '<a href="'.$conf['gallery'].'/admin.php?page=site_manager" title="Main gallery :: site manager">';
    $g_footer  .= '<img src="'.$pwg_conf['icon_dir'].'home.png" /></a>'."\n";
    $_SESSION['scan_step'] = 'exit';
  }
  
  // Open listing.xml
  if ($_SESSION['scan_action'] == 'generate')
  {
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
    $_SESSION['scan_logs'] = '';
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


  // Call specific action post process
  if (is_callable('pwg_'.$local_action.'_exit'))
  {
    call_user_func('pwg_'.$local_action.'_exit');
  }
  
  //~ pwg_log('<<<<< pwg_exit() <<<<<'."\n");
}

// +-----------------------------------------------------------------------+
// |                                script                                 |
// +-----------------------------------------------------------------------+
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
  <title>Manage distant gallery</title>
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
        <span class="p">Php</span>
        <span class="w">Web</span>
        <span class="g">Gallery</span>
        &nbsp;distant site<? echo $g_header; ?>
      </fieldset>
      <fieldset>
<?php echo $g_message; ?>
      </fieldset>
      <fieldset class="footer">
        <div class="pwg_block">
          Powered by <a href="http://www.phpwebgallery.net" class="pwg"><span class="p">Php</span><span class="w">Web</span><span class="g">Gallery</span></a>
        </div>
        <?php echo $g_footer; ?>
      </fieldset>
    </div>
  </body>
</html>
