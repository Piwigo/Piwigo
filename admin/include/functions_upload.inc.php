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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/image.class.php');

// add default event handler for image and thumbnail resize
add_event_handler('upload_image_resize', 'pwg_image_resize', EVENT_HANDLER_PRIORITY_NEUTRAL, 7);
add_event_handler('upload_thumbnail_resize', 'pwg_image_resize', EVENT_HANDLER_PRIORITY_NEUTRAL, 9);

function get_upload_form_config()
{
  // default configuration for upload
  $upload_form_config = array(
    'websize_resize' => array(
      'default' => true,
      'can_be_null' => false,
      ),
    
    'websize_maxwidth' => array(
      'default' => 800,
      'min' => 100,
      'max' => 1600,
      'pattern' => '/^\d+$/',
      'can_be_null' => true,
      'error_message' => l10n('The websize maximum width must be a number between %d and %d'),
      ),
  
    'websize_maxheight' => array(
      'default' => 600,
      'min' => 100,
      'max' => 1200,
      'pattern' => '/^\d+$/',
      'can_be_null' => true,
      'error_message' => l10n('The websize maximum height must be a number between %d and %d'),
      ),
  
    'websize_quality' => array(
      'default' => 95,
      'min' => 50,
      'max' => 100,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The websize image quality must be a number between %d and %d'),
      ),
  
    'thumb_maxwidth' => array(
      'default' => 128,
      'min' => 50,
      'max' => 300,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The thumbnail maximum width must be a number between %d and %d'),
      ),
  
    'thumb_maxheight' => array(
      'default' => 96,
      'min' => 50,
      'max' => 300,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The thumbnail maximum height must be a number between %d and %d'),
      ),
  
    'thumb_quality' => array(
      'default' => 95,
      'min' => 50,
      'max' => 100,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The thumbnail image quality must be a number between %d and %d'),
      ),

    'thumb_crop' => array(
      'default' => false,
      'can_be_null' => false,
      ),

    'thumb_follow_orientation' => array(
      'default' => true,
      'can_be_null' => false,
      ),
  
    'hd_keep' => array(
      'default' => true,
      'can_be_null' => false,
      ),
  
    'hd_resize' => array(
      'default' => false,
      'can_be_null' => false,
      ),
  
    'hd_maxwidth' => array(
      'default' => 2000,
      'min' => 500,
      'max' => 20000,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The high definition maximum width must be a number between %d and %d'),
      ),
  
    'hd_maxheight' => array(
      'default' => 2000,
      'min' => 500,
      'max' => 20000,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The high definition maximum height must be a number between %d and %d'),
      ),
  
    'hd_quality' => array(
      'default' => 95,
      'min' => 50,
      'max' => 100,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The high definition image quality must be a number between %d and %d'),
      ),
    );

  return $upload_form_config;
}

/*
 * automatic fill of configuration parameters
 */
function prepare_upload_configuration()
{
  global $conf;

  $inserts = array();
  
  foreach (get_upload_form_config() as $param_shortname => $param)
  {
    $param_name = 'upload_form_'.$param_shortname;
  
    if (!isset($conf[$param_name]))
    {
      $conf[$param_name] = $param['default'];
      
      array_push(
        $inserts,
        array(
          'param' => $param_name,
          'value' => boolean_to_string($param['default']),
          )
        );
    }
  }
  
  if (count($inserts) > 0)
  {
    mass_inserts(
      CONFIG_TABLE,
      array_keys($inserts[0]),
      $inserts
      );
  }
}

function save_upload_form_config($data, &$errors=array())
{
  if (!is_array($data) or empty($data))
  {
    return false;
  }

  $upload_form_config = get_upload_form_config();
  $updates = array();

  foreach ($data as $field => $value)
  {
    if (!isset($upload_form_config[$field]))
    {
      continue;
    }
    if (is_bool($upload_form_config[$field]['default']))
    {
      if (isset($value))
      {
        $value = true;
      }
      else
      {
        $value = false;
      }

      $updates[] = array(
        'param' => 'upload_form_'.$field,
        'value' => boolean_to_string($value)
        );
    }
    elseif ($upload_form_config[$field]['can_be_null'] and empty($value))
    {
      $updates[] = array(
        'param' => 'upload_form_'.$field,
        'value' => 'false'
        );
    }
    else
    {
      $min = $upload_form_config[$field]['min'];
      $max = $upload_form_config[$field]['max'];
      $pattern = $upload_form_config[$field]['pattern'];
      
      if (preg_match($pattern, $value) and $value >= $min and $value <= $max)
      {
         $updates[] = array(
          'param' => 'upload_form_'.$field,
          'value' => $value
          );
      }
      else
      {
        array_push(
          $errors,
          sprintf(
            $upload_form_config[$field]['error_message'],
            $min,
            $max
            )
          );
      }
    }
  }

  if (count($errors) == 0)
  {
    mass_updates(
      CONFIG_TABLE,
      array(
        'primary' => array('param'),
        'update' => array('value')
        ),
      $updates
      );
    return true;
  }

  return false;
}

function add_uploaded_file($source_filepath, $original_filename=null, $categories=null, $level=null, $image_id=null)
{
  // Here is the plan
  //
  // 1) move uploaded file to upload/2010/01/22/20100122003814-449ada00.jpg
  //
  // 2) if taller than max_height or wider than max_width, move to pwg_high
  //    + web sized creation
  //
  // 3) thumbnail creation from web sized
  //
  // 4) register in database
  
  // TODO
  // * check md5sum (already exists?)
  
  global $conf, $user;

  $md5sum = md5_file($source_filepath);
  $file_path = null;
  
  if (isset($image_id))
  {
    // we are performing an update
    $query = '
SELECT
    path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      $file_path = $row['path'];
    }
    
    if (!isset($file_path))
    {
      die('['.__FUNCTION__.'] this photo does not exist in the database');
    }

    // delete all physical files related to the photo (thumbnail, web site, HD)
    delete_element_files(array($image_id));
  }
  else
  {
    // this photo is new
    
    // current date
    list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));
    list($year, $month, $day) = preg_split('/[^\d]/', $dbnow, 4);
  
    // upload directory hierarchy
    $upload_dir = sprintf(
      PHPWG_ROOT_PATH.$conf['upload_dir'].'/%s/%s/%s',
      $year,
      $month,
      $day
      );

    // compute file path
    $date_string = preg_replace('/[^\d]/', '', $dbnow);
    $random_string = substr($md5sum, 0, 8);
    $filename_wo_ext = $date_string.'-'.$random_string;
    $file_path = $upload_dir.'/'.$filename_wo_ext.'.';

    list($width, $height, $type) = getimagesize($source_filepath);
    if (IMAGETYPE_PNG == $type)
    {
      $file_path.= 'png';
    }
    elseif (IMAGETYPE_GIF == $type)
    {
      $file_path.= 'gif';
    }
    else
    {
      $file_path.= 'jpg';
    }

    prepare_directory($upload_dir);
  }

  if (is_uploaded_file($source_filepath))
  {
    move_uploaded_file($source_filepath, $file_path);
  }
  else
  {
    copy($source_filepath, $file_path);
  }

  if ($conf['upload_form_websize_resize']
      and need_resize($file_path, $conf['upload_form_websize_maxwidth'], $conf['upload_form_websize_maxheight']))
  {
    $high_path = file_path_for_type($file_path, 'high');
    $high_dir = dirname($high_path);
    prepare_directory($high_dir);
    
    rename($file_path, $high_path);
    $high_infos = pwg_image_infos($high_path);
    
    $img = new pwg_image($high_path);

    $img->pwg_resize(
      $file_path,
      $conf['upload_form_websize_maxwidth'],
      $conf['upload_form_websize_maxheight'],
      $conf['upload_form_websize_quality'],
      $conf['upload_form_automatic_rotation'],
      false
      );

    if ($img->library != 'gd')
    {
      if ($conf['upload_form_hd_keep'])
      {
        if ($conf['upload_form_hd_resize'])
        {
          $need_resize = need_resize($high_path, $conf['upload_form_hd_maxwidth'], $conf['upload_form_hd_maxheight']);
        
          if ($need_resize)
          {
            $img->pwg_resize(
              $high_path,
              $conf['upload_form_hd_maxwidth'],
              $conf['upload_form_hd_maxheight'],
              $conf['upload_form_hd_quality'],
              $conf['upload_form_automatic_rotation'],
              false
              );
            $high_infos = pwg_image_infos($high_path);
          }
        }
      }
      else
      {
        unlink($high_path);
        $high_infos = null;
      }
    }
    $img->destroy();
  }

  $file_infos = pwg_image_infos($file_path);
  
  $thumb_path = file_path_for_type($file_path, 'thumb');
  $thumb_dir = dirname($thumb_path);
  prepare_directory($thumb_dir);

  $img = new pwg_image($file_path);
  $img->pwg_resize(
    $thumb_path,
    $conf['upload_form_thumb_maxwidth'],
    $conf['upload_form_thumb_maxheight'],
    $conf['upload_form_thumb_quality'],
    $conf['upload_form_automatic_rotation'],
    true
    );
  $img->destroy();
  
  $thumb_infos = pwg_image_infos($thumb_path);

  if (isset($image_id))
  {
    $update = array(
      'id' => $image_id,
      'file' => pwg_db_real_escape_string(isset($original_filename) ? $original_filename : basename($file_path)),
      'filesize' => $file_infos['filesize'],
      'width' => $file_infos['width'],
      'height' => $file_infos['height'],
      'md5sum' => $md5sum,
      'added_by' => $user['id'],
      );
    
    if (isset($high_infos))
    {
      $update['has_high'] = 'true';
      $update['high_filesize'] = $high_infos['filesize'];
      $update['high_width'] = $high_infos['width'];
      $update['high_height'] = $high_infos['height'];
    }
    else
    {
      $update['has_high'] = 'false';
      $update['high_filesize'] = null;
      $update['high_width'] = null;
      $update['high_height'] = null;
    }

    if (isset($level))
    {
      $update['level'] = $level;
    }

    mass_updates(
      IMAGES_TABLE,
      array(
        'primary' => array('id'),
        'update' => array_keys($update)
        ),
      array($update)
      );
  }
  else
  {
    // database registration
    $insert = array(
      'file' => pwg_db_real_escape_string(isset($original_filename) ? $original_filename : basename($file_path)),
      'date_available' => $dbnow,
      'tn_ext' => 'jpg',
      'path' => preg_replace('#^'.preg_quote(PHPWG_ROOT_PATH).'#', '', $file_path),
      'filesize' => $file_infos['filesize'],
      'width' => $file_infos['width'],
      'height' => $file_infos['height'],
      'md5sum' => $md5sum,
      'added_by' => $user['id'],
      );

    if (isset($high_infos))
    {
      $insert['has_high'] = 'true';
      $insert['high_filesize'] = $high_infos['filesize'];
      $insert['high_width'] = $high_infos['width'];
      $insert['high_height'] = $high_infos['height'];
    }

    if (isset($level))
    {
      $insert['level'] = $level;
    }
  
    mass_inserts(
      IMAGES_TABLE,
      array_keys($insert),
      array($insert)
      );
  
    $image_id = pwg_db_insert_id(IMAGES_TABLE);
  }

  if (isset($categories) and count($categories) > 0)
  {
    associate_images_to_categories(
      array($image_id),
      $categories
      );
  }
  
  // update metadata from the uploaded file (exif/iptc)
  if ($conf['use_exif'] and !function_exists('read_exif_data'))
  {
    $conf['use_exif'] = false;
  }
  update_metadata(array($image_id=>$file_path));

  invalidate_user_cache();

  return $image_id;
}

function prepare_directory($directory)
{
  if (!is_dir($directory)) {
    if (substr(PHP_OS, 0, 3) == 'WIN')
    {
      $directory = str_replace('/', DIRECTORY_SEPARATOR, $directory);
    }
    umask(0000);
    $recursive = true;
    if (!@mkdir($directory, 0777, $recursive))
    {
      die('[prepare_directory] cannot create directory "'.$directory.'"');
    }
  }

  if (!is_writable($directory))
  {
    // last chance to make the directory writable
    @chmod($directory, 0777);

    if (!is_writable($directory))
    {
      die('[prepare_directory] directory "'.$directory.'" has no write access');
    }
  }

  secure_directory($directory);
}

function need_resize($image_filepath, $max_width, $max_height)
{
  // TODO : the resize check should take the orientation into account. If a
  // rotation must be applied to the resized photo, then we should test
  // invert width and height.
  list($width, $height) = getimagesize($image_filepath);
  
  if ($width > $max_width or $height > $max_height)
  {
    return true;
  }

  return false;
}

function pwg_image_infos($path)
{
  list($width, $height) = getimagesize($path);
  $filesize = floor(filesize($path)/1024);
  
  return array(
    'width'  => $width,
    'height' => $height,
    'filesize' => $filesize,
    );
}

function is_valid_image_extension($extension)
{
  return in_array(strtolower($extension), array('jpg', 'jpeg', 'png', 'gif'));
}

function file_upload_error_message($error_code)
{
  switch ($error_code) {
    case UPLOAD_ERR_INI_SIZE:
      return sprintf(
        l10n('The uploaded file exceeds the upload_max_filesize directive in php.ini: %sB'),
        get_ini_size('upload_max_filesize', false)
        );
    case UPLOAD_ERR_FORM_SIZE:
      return l10n('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
    case UPLOAD_ERR_PARTIAL:
      return l10n('The uploaded file was only partially uploaded');
    case UPLOAD_ERR_NO_FILE:
      return l10n('No file was uploaded');
    case UPLOAD_ERR_NO_TMP_DIR:
      return l10n('Missing a temporary folder');
    case UPLOAD_ERR_CANT_WRITE:
      return l10n('Failed to write file to disk');
    case UPLOAD_ERR_EXTENSION:
      return l10n('File upload stopped by extension');
    default:
      return l10n('Unknown upload error');
  }
}

function get_ini_size($ini_key, $in_bytes=true)
{
  $size = ini_get($ini_key);

  if ($in_bytes)
  {
    $size = convert_shortand_notation_to_bytes($size);
  }
  
  return $size;
}

function convert_shortand_notation_to_bytes($value)
{
  $suffix = substr($value, -1);
  $multiply_by = null;
  
  if ('K' == $suffix)
  {
    $multiply_by = 1024;
  }
  else if ('M' == $suffix)
  {
    $multiply_by = 1024*1024;
  }
  else if ('G' == $suffix)
  {
    $multiply_by = 1024*1024*1024;
  }
  
  if (isset($multiply_by))
  {
    $value = substr($value, 0, -1);
    $value*= $multiply_by;
  }

  return $value;
}

function add_upload_error($upload_id, $error_message)
{
  if (!isset($_SESSION['uploads_error']))
  {
    $_SESSION['uploads_error'] = array();
  }
  if (!isset($_SESSION['uploads_error'][$upload_id]))
  {
    $_SESSION['uploads_error'][$upload_id] = array();
  }

  array_push($_SESSION['uploads_error'][$upload_id], $error_message);
}

function ready_for_upload_message()
{
  global $conf;

  $relative_dir = preg_replace('#^'.PHPWG_ROOT_PATH.'#', '', $conf['upload_dir']);

  if (!is_dir($conf['upload_dir']))
  {
    if (!is_writable(dirname($conf['upload_dir'])))
    {
      return sprintf(
        l10n('Create the "%s" directory at the root of your Piwigo installation'),
        $relative_dir
        );
    }
  }
  else
  {
    if (!is_writable($conf['upload_dir']))
    {
      @chmod($conf['upload_dir'], 0777);
      
      if (!is_writable($conf['upload_dir']))
      {
        return sprintf(
          l10n('Give write access (chmod 777) to "%s" directory at the root of your Piwigo installation'),
          $relative_dir
          );
      }
    }
  }

  return null;
}

function file_path_for_type($file_path, $type='thumb')
{
  // resolve the $file_path depending on the $type
  if ('thumb' == $type) {
    $file_path = get_thumbnail_location(
      array(
        'path' => $file_path,
        'tn_ext' => 'jpg',
        )
      );
  }

  if ('high' == $type) {
    @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
    $file_path = get_high_location(
      array(
        'path' => $file_path,
        'has_high' => 'true'
        )
      );
  }

  return $file_path;
}

?>