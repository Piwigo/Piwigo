<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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
add_event_handler('upload_image_resize', 'pwg_image_resize');
add_event_handler('upload_thumbnail_resize', 'pwg_image_resize');

function get_upload_form_config()
{
  // default configuration for upload
  $upload_form_config = array(
    'original_resize' => array(
      'default' => false,
      'can_be_null' => false,
      ),

    'original_resize_maxwidth' => array(
      'default' => 2000,
      'min' => 500,
      'max' => 20000,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The original maximum width must be a number between %d and %d'),
      ),

    'original_resize_maxheight' => array(
      'default' => 2000,
      'min' => 300,
      'max' => 20000,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The original maximum height must be a number between %d and %d'),
      ),

    'original_resize_quality' => array(
      'default' => 95,
      'min' => 50,
      'max' => 98,
      'pattern' => '/^\d+$/',
      'can_be_null' => false,
      'error_message' => l10n('The original image quality must be a number between %d and %d'),
      ),
    );

  return $upload_form_config;
}

function save_upload_form_config($data, &$errors=array(), &$form_errors=array())
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
        'param' => $field,
        'value' => boolean_to_string($value)
        );
    }
    elseif ($upload_form_config[$field]['can_be_null'] and empty($value))
    {
      $updates[] = array(
        'param' => $field,
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
          'param' => $field,
          'value' => $value
          );
      }
      else
      {
        $errors[] = sprintf(
          $upload_form_config[$field]['error_message'],
          $min, $max
          );

        $form_errors[$field] = '['.$min.' .. '.$max.']';
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

function add_uploaded_file($source_filepath, $original_filename=null, $categories=null, $level=null, $image_id=null, $original_md5sum=null)
{
  // 1) move uploaded file to upload/2010/01/22/20100122003814-449ada00.jpg
  //
  // 2) keep/resize original
  //
  // 3) register in database

  // TODO
  // * check md5sum (already exists?)

  global $conf, $user;

  if (isset($original_md5sum))
  {
    $md5sum = $original_md5sum;
  }
  else
  {
    $md5sum = md5_file($source_filepath);
  }

  $file_path = null;
  $is_tiff = false;

  if (isset($image_id))
  {
    // this photo already exists, we update it
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
    elseif (IMAGETYPE_TIFF_MM == $type or IMAGETYPE_TIFF_II == $type)
    {
      $is_tiff = true;
      $file_path.= 'tif';
    }
    elseif (IMAGETYPE_JPEG == $type)
    {
      $file_path.= 'jpg';
    }
    elseif (isset($conf['upload_form_all_types']) and $conf['upload_form_all_types'])
    {
      $original_extension = strtolower(get_extension($original_filename));

      if (in_array($original_extension, $conf['file_ext']))
      {
        $file_path.= $original_extension;
      }
      else
      {
        die('unexpected file type');
      }
    }
    else
    {
      die('forbidden file type');
    }

    prepare_directory($upload_dir);
  }

  if (is_uploaded_file($source_filepath))
  {
    move_uploaded_file($source_filepath, $file_path);
  }
  else
  {
    rename($source_filepath, $file_path);
  }
  @chmod($file_path, 0644);

  // handle the uploaded file type by potentially making a
  // pwg_representative file.
  $representative_ext = trigger_change('upload_file', null, $file_path);

  global $logger;
  $logger->info("Handling " . (string)$file_path . " got " . (string)$representative_ext);
  
  // If it is set to either true (the file didn't need a
  // representative generated) or false (the generation of the
  // representative failed), set it to null because we have no
  // representative file.
  if (is_bool($representative_ext)) {
    $representative_ext = null;
  }
  
  if (pwg_image::get_library() != 'gd')
  {
    if ($conf['original_resize'])
    {
      $need_resize = need_resize($file_path, $conf['original_resize_maxwidth'], $conf['original_resize_maxheight']);

      if ($need_resize)
      {
        $img = new pwg_image($file_path);

        $img->pwg_resize(
          $file_path,
          $conf['original_resize_maxwidth'],
          $conf['original_resize_maxheight'],
          $conf['original_resize_quality'],
          $conf['upload_form_automatic_rotation'],
          false
          );

        $img->destroy();
      }
    }
  }

  // we need to save the rotation angle in the database to compute
  // width/height of "multisizes"
  $rotation_angle = pwg_image::get_rotation_angle($file_path);
  $rotation = pwg_image::get_rotation_code_from_angle($rotation_angle);

  $file_infos = pwg_image_infos($file_path);

  if (isset($image_id))
  {
    $update = array(
      'file' => pwg_db_real_escape_string(isset($original_filename) ? $original_filename : basename($file_path)),
      'filesize' => $file_infos['filesize'],
      'width' => $file_infos['width'],
      'height' => $file_infos['height'],
      'md5sum' => $md5sum,
      'added_by' => $user['id'],
      'rotation' => $rotation,
      );

    if (isset($level))
    {
      $update['level'] = $level;
    }

    single_update(
      IMAGES_TABLE,
      $update,
      array('id' => $image_id)
      );
  }
  else
  {
    // database registration
    $file = pwg_db_real_escape_string(isset($original_filename) ? $original_filename : basename($file_path));
    $insert = array(
      'file' => $file,
      'name' => get_name_from_file($file),
      'date_available' => $dbnow,
      'path' => preg_replace('#^'.preg_quote(PHPWG_ROOT_PATH).'#', '', $file_path),
      'filesize' => $file_infos['filesize'],
      'width' => $file_infos['width'],
      'height' => $file_infos['height'],
      'md5sum' => $md5sum,
      'added_by' => $user['id'],
      'rotation' => $rotation,
      );

    if (isset($level))
    {
      $insert['level'] = $level;
    }

    if (isset($representative_ext))
    {
      $insert['representative_ext'] = $representative_ext;
    }

    single_insert(IMAGES_TABLE, $insert);

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
  sync_metadata(array($image_id));

  invalidate_user_cache();

  // cache thumbnail
  $query = '
SELECT
    id,
    path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
  $image_infos = pwg_db_fetch_assoc(pwg_query($query));

  set_make_full_url();
  // in case we are on uploadify.php, we have to replace the false path
  $thumb_url = preg_replace('#admin/include/i#', 'i', DerivativeImage::thumb_url($image_infos));
  unset_make_full_url();

  fetchRemote($thumb_url, $dest);


  return $image_id;
}

add_event_handler('upload_file', 'upload_file_pdf');
function upload_file_pdf($representative_ext, $file_path)
{
  global $logger, $conf;

  $logger->info(__FUNCTION__.', $file_path = '.$file_path.', $representative_ext = '.$representative_ext);

  if (isset($representative_ext))
  {
    return $representative_ext;
  }

  if (pwg_image::get_library() != 'ext_imagick')
  {
    return $representative_ext;
  }

  if (!in_array(strtolower(get_extension($file_path)), array('pdf')))
  {
    return $representative_ext;
  }

  $ext = conf_get_param('pdf_representative_ext', 'jpg');
  $jpg_quality = conf_get_param('pdf_jpg_quality', 90);

  // move the uploaded file to pwg_representative sub-directory
  $representative_file_path = original_to_representative($file_path, $ext);
  prepare_directory(dirname($representative_file_path));

  $exec = $conf['ext_imagick_dir'].'convert';
  if ('jpg' == $ext)
  {
    $exec.= ' -quality '.$jpg_quality;
  }
  $exec.= ' "'.realpath($file_path).'"[0]';
  $exec.= ' "'.$representative_file_path.'"';
  $exec.= ' 2>&1';
  @exec($exec, $returnarray);

  // Return the extension (if successful) or false (if failed)
  if (file_exists($representative_file_path))
  {
    $representative_ext = $ext;
  }

  return $representative_ext;
}

add_event_handler('upload_file', 'upload_file_tiff');
function upload_file_tiff($representative_ext, $file_path)
{
  global $logger, $conf;

  $logger->info(__FUNCTION__.', $file_path = '.$file_path.', $representative_ext = '.$representative_ext);

  if (isset($representative_ext))
  {
    return $representative_ext;
  }

  if (pwg_image::get_library() != 'ext_imagick')
  {
    return $representative_ext;
  }

  if (!in_array(strtolower(get_extension($file_path)), array('tif', 'tiff')))
  {
    return $representative_ext;
  }

  // move the uploaded file to pwg_representative sub-directory
  $representative_file_path = dirname($file_path).'/pwg_representative/';
  $representative_file_path.= get_filename_wo_extension(basename($file_path)).'.';

  $representative_ext = $conf['tiff_representative_ext'];
  $representative_file_path.= $representative_ext;

  prepare_directory(dirname($representative_file_path));

  $exec = $conf['ext_imagick_dir'].'convert';

  if ('jpg' == $conf['tiff_representative_ext'])
  {
    $exec .= ' -quality 98';
  }

  $exec .= ' "'.realpath($file_path).'"';

  $dest = pathinfo($representative_file_path);
  $exec .= ' "'.realpath($dest['dirname']).'/'.$dest['basename'].'"';

  $exec .= ' 2>&1';
  @exec($exec, $returnarray);

  // sometimes ImageMagick creates file-0.jpg (full size) + file-1.jpg
  // (thumbnail). I don't know how to avoid it.
  $representative_file_abspath = realpath($dest['dirname']).'/'.$dest['basename'];
  if (!file_exists($representative_file_abspath))
  {
    $first_file_abspath = preg_replace(
      '/\.'.$representative_ext.'$/',
      '-0.'.$representative_ext,
      $representative_file_abspath
      );

    if (file_exists($first_file_abspath))
    {
      rename($first_file_abspath, $representative_file_abspath);
    }
  }

  return get_extension($representative_file_abspath);
}

add_event_handler('upload_file', 'upload_file_video');
function upload_file_video($representative_ext, $file_path)
{
  global $logger, $conf;

  $logger->info(__FUNCTION__.', $file_path = '.$file_path.', $representative_ext = '.$representative_ext);

  if (isset($representative_ext))
  {
    return $representative_ext;
  }

  $ffmpeg_video_exts = array( // extensions tested with FFmpeg
    'wmv','mov','mkv','mp4','mpg','flv','asf','xvid','divx','mpeg',
    'avi','rm', 'm4v', 'ogg', 'ogv', 'webm', 'webmv',
    );

  if (!in_array(strtolower(get_extension($file_path)), $ffmpeg_video_exts))
  {
    return $representative_ext;
  }

  $representative_file_path = dirname($file_path).'/pwg_representative/';
  $representative_file_path.= get_filename_wo_extension(basename($file_path)).'.';

  $representative_ext = 'jpg';
  $representative_file_path.= $representative_ext;

  prepare_directory(dirname($representative_file_path));

  $second = 1;

  $ffmpeg = $conf['ffmpeg_dir'].'ffmpeg';
  $ffmpeg.= ' -i "'.$file_path.'"';
  $ffmpeg.= ' -an -ss '.$second;
  $ffmpeg.= ' -t 1 -r 1 -y -vcodec mjpeg -f mjpeg';
  $ffmpeg.= ' "'.$representative_file_path.'"';

  @exec($ffmpeg);

  if (!file_exists($representative_file_path))
  {
    return null;
  }

  return $representative_ext;
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
  global $conf;
  
  if (isset($conf['upload_form_all_types']) and $conf['upload_form_all_types'])
  {
    $extensions = $conf['file_ext'];
  }
  else
  {
    $extensions = $conf['picture_ext'];
  }

  return array_unique(array_map('strtolower', $extensions));
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
    $size = convert_shorthand_notation_to_bytes($size);
  }

  return $size;
}

function convert_shorthand_notation_to_bytes($value)
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
  $_SESSION['uploads_error'][$upload_id][] = $error_message;
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
?>