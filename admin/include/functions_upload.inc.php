<?php
// TODO
// * check md5sum (already exists?)

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'include/ws_functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

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

// add default event handler for image and thumbnail resize
add_event_handler('upload_image_resize', 'pwg_image_resize', EVENT_HANDLER_PRIORITY_NEUTRAL, 7);
add_event_handler('upload_thumbnail_resize', 'pwg_image_resize', EVENT_HANDLER_PRIORITY_NEUTRAL, 7);

function add_uploaded_file($source_filepath, $original_filename=null, $categories=null, $level=null)
{
  global $conf;

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
  $md5sum = md5_file($source_filepath);
  $date_string = preg_replace('/[^\d]/', '', $dbnow);
  $random_string = substr($md5sum, 0, 8);
  $filename_wo_ext = $date_string.'-'.$random_string;
  $file_path = $upload_dir.'/'.$filename_wo_ext.'.';

  list($width, $height, $type) = getimagesize($source_filepath);
  if (IMAGETYPE_PNG == $type)
  {
    $file_path.= 'png';
  }
  else
  {
    $file_path.= 'jpg';
  }

  prepare_directory($upload_dir);
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
    
    trigger_event(
      'upload_image_resize',
      false,
      $high_path,
      $file_path,
      $conf['upload_form_websize_maxwidth'],
      $conf['upload_form_websize_maxheight'],
      $conf['upload_form_websize_quality'],
      false
      );

    if (is_imagick())
    {
      if ($conf['upload_form_hd_keep'])
      {
        $need_resize = need_resize($high_path, $conf['upload_form_hd_maxwidth'], $conf['upload_form_hd_maxheight']);
        
        if ($conf['upload_form_hd_resize'] and $need_resize)
        {
          pwg_image_resize(
            false,
            $high_path,
            $high_path,
            $conf['upload_form_hd_maxwidth'],
            $conf['upload_form_hd_maxheight'],
            $conf['upload_form_hd_quality'],
            false
            );
          $high_infos = pwg_image_infos($high_path);
        }
      }
      else
      {
        unlink($high_path);
        $high_infos = null;
      }
    }
  }

  $file_infos = pwg_image_infos($file_path);
  
  $thumb_path = file_path_for_type($file_path, 'thumb');
  $thumb_dir = dirname($thumb_path);
  prepare_directory($thumb_dir);
  
  trigger_event(
    'upload_thumbnail_resize',
    false,
    $file_path,
    $thumb_path,
    $conf['upload_form_thumb_maxwidth'],
    $conf['upload_form_thumb_maxheight'],
    $conf['upload_form_thumb_quality'],
    true
    );
  
  $thumb_infos = pwg_image_infos($thumb_path);

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
    );

  if (isset($high_infos))
  {
    $insert['has_high'] = 'true';
    $insert['high_filesize'] = $high_infos['filesize'];
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

function get_resize_dimensions($width, $height, $max_width, $max_height, $rotation=null)
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
  
  $ratio_width  = $width / $max_width;
  $ratio_height = $height / $max_height;
  
  // maximal size exceeded ?
  if ($ratio_width > 1 or $ratio_height > 1)
  {
    if ($ratio_width < $ratio_height)
    { 
      $destination_width = ceil($width / $ratio_height);
      $destination_height = $max_height;
    }
    else
    { 
      $destination_width = $max_width; 
      $destination_height = ceil($height / $ratio_width);
    }
  }

  if ($rotate_for_dimensions)
  {
    list($destination_width, $destination_height) = array($destination_height, $destination_width);
  }
  
  return array(
    'width' => $destination_width,
    'height'=> $destination_height,
    );
}

function pwg_image_resize($result, $source_filepath, $destination_filepath, $max_width, $max_height, $quality, $strip_metadata=false)
{
  if ($result !== false)
  {
    //someone hooked us - so we skip
    return $result;
  }

  if (is_imagick())
  {
    return pwg_image_resize_im($source_filepath, $destination_filepath, $max_width, $max_height, $quality, $strip_metadata);
  }
  else
  {
    return pwg_image_resize_gd($source_filepath, $destination_filepath, $max_width, $max_height, $quality);
  }
}

function pwg_image_resize_gd($source_filepath, $destination_filepath, $max_width, $max_height, $quality)
{
  if (!function_exists('gd_info'))
  {
    return false;
  }

  // extension of the picture filename
  $extension = strtolower(get_extension($source_filepath));

  $source_image = null;
  if (in_array($extension, array('jpg', 'jpeg')))
  {
    $source_image = imagecreatefromjpeg($source_filepath);
  }
  else if ($extension == 'png')
  {
    $source_image = imagecreatefrompng($source_filepath);
  }
  else
  {
    die('unsupported file extension');
  }

  $rotation = null;
  if (function_exists('imagerotate'))
  {
    $rotation = get_rotation_angle($source_filepath);
  }
  
  // width/height
  $source_width  = imagesx($source_image); 
  $source_height = imagesy($source_image);
  
  $resize_dimensions = get_resize_dimensions($source_width, $source_height, $max_width, $max_height, $rotation);

  // testing on height is useless in theory: if width is unchanged, there
  // should be no resize, because width/height ratio is not modified.
  if ($resize_dimensions['width'] == $source_width and $resize_dimensions['height'] == $source_height)
  {
    // the image doesn't need any resize! We just copy it to the destination
    copy($source_filepath, $destination_filepath);
    return true;
  }
  
  $destination_image = imagecreatetruecolor($resize_dimensions['width'], $resize_dimensions['height']);
  
  imagecopyresampled(
    $destination_image,
    $source_image,
    0,
    0,
    0,
    0,
    $resize_dimensions['width'],
    $resize_dimensions['height'],
    $source_width,
    $source_height
    );

  // rotation occurs only on resized photo to avoid useless memory use
  if (isset($rotation))
  {
    $destination_image = imagerotate($destination_image, $rotation, 0);
  }
  
  $extension = strtolower(get_extension($destination_filepath));
  if ($extension == 'png')
  {
    imagepng($destination_image, $destination_filepath);
  }
  else
  {
    imagejpeg($destination_image, $destination_filepath, $quality);
  }
  // freeing memory ressources
  imagedestroy($source_image);
  imagedestroy($destination_image);

  // everything should be OK if we are here!
  return true;
}

function pwg_image_resize_im($source_filepath, $destination_filepath, $max_width, $max_height, $quality, $strip_metadata=false)
{
  // extension of the picture filename
  $extension = strtolower(get_extension($source_filepath));
  if (!in_array($extension, array('jpg', 'jpeg', 'png')))
  {
    die('[Imagick] unsupported file extension');
  }

  $image = new Imagick($source_filepath);

  $rotation = null;
  if (function_exists('imagerotate'))
  {
    $rotation = get_rotation_angle($source_filepath);
  }
  
  // width/height
  $source_width  = $image->getImageWidth();
  $source_height = $image->getImageHeight();
  
  $resize_dimensions = get_resize_dimensions($source_width, $source_height, $max_width, $max_height, $rotation);

  // testing on height is useless in theory: if width is unchanged, there
  // should be no resize, because width/height ratio is not modified.
  if ($resize_dimensions['width'] == $source_width and $resize_dimensions['height'] == $source_height)
  {
    // the image doesn't need any resize! We just copy it to the destination
    copy($source_filepath, $destination_filepath);
    return true;
  }

  $image->setImageCompressionQuality($quality);
  $image->setInterlaceScheme(Imagick::INTERLACE_LINE);
  
  if ($strip_metadata)
  {
    // we save a few kilobytes. For example a thumbnail with metadata
    // weights 25KB, without metadata 7KB.
    $image->stripImage();
  }
  
  $image->resizeImage($resize_dimensions['width'], $resize_dimensions['height'], Imagick::FILTER_LANCZOS, 0.9);

  if (isset($rotation))
  {
    $image->rotateImage(new ImagickPixel(), -$rotation);
    $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
  }

  $image->writeImage($destination_filepath);
  $image->destroy();

  // everything should be OK if we are here!
  return true;
}

function get_rotation_angle($source_filepath)
{
  $rotation = null;
  
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
  return in_array(strtolower($extension), array('jpg', 'jpeg', 'png'));
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

function is_imagick()
{
  if (extension_loaded('imagick'))
  {
    return true;
  }

  return false;
}
?>