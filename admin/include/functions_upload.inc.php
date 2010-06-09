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
add_event_handler('upload_image_resize', 'pwg_image_resize', EVENT_HANDLER_PRIORITY_NEUTRAL, 6);
add_event_handler('upload_thumbnail_resize', 'pwg_image_resize', EVENT_HANDLER_PRIORITY_NEUTRAL, 6);

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
  $file_path = $upload_dir.'/'.$filename_wo_ext.'.jpg';

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
    
    trigger_event('upload_image_resize',
      false,
      $high_path,
      $file_path,
      $conf['upload_form_websize_maxwidth'],
      $conf['upload_form_websize_maxheight'],
      $conf['upload_form_websize_quality']
      );
  }

  $file_infos = pwg_image_infos($file_path);
  
  $thumb_path = file_path_for_type($file_path, 'thumb');
  $thumb_dir = dirname($thumb_path);
  prepare_directory($thumb_dir);
  
  trigger_event('upload_thumbnail_resize',
    false,
    $file_path,
    $thumb_path,
    $conf['upload_form_thumb_maxwidth'],
    $conf['upload_form_thumb_maxheight'],
    $conf['upload_form_thumb_quality']
    );
  
  $thumb_infos = pwg_image_infos($thumb_path);

  // database registration
  $insert = array(
    'file' => isset($original_filename) ? $original_filename : basename($file_path),
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
  
  $image_id = pwg_db_insert_id();

  if (isset($categories) and count($categories) > 0)
  {
    associate_images_to_categories(
      array($image_id),
      $categories
      );
  }
  
  // update metadata from the uploaded file (exif/iptc)
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
  list($width, $height) = getimagesize($image_filepath);
  
  if ($width > $max_width or $height > $max_height)
  {
    return true;
  }

  return false;
}

function pwg_image_resize($result, $source_filepath, $destination_filepath, $max_width, $max_height, $quality)
{
  if ($result !== false)
  {
    //someone hooked us - so we skip
    return $result;
  }

  if (!function_exists('gd_info'))
  {
    return false;
  }

  // extension of the picture filename
  $extension = strtolower(get_extension($source_filepath));

  $source_image = null;
  if (in_array($extension, array('jpg', 'jpeg')))
  {
    $source_image = @imagecreatefromjpeg($source_filepath);
  }
  else if ($extension == 'png')
  {
    $source_image = @imagecreatefrompng($source_filepath);
  }
  else
  {
    die('unsupported file extension');
  }
  
  // width/height
  $source_width  = imagesx($source_image); 
  $source_height = imagesy($source_image);
  
  $ratio_width  = $source_width / $max_width;
  $ratio_height = $source_height / $max_height;
  
  // maximal size exceeded ?
  if ($ratio_width > 1 or $ratio_height > 1)
  {
    if ($ratio_width < $ratio_height)
    { 
      $destination_width = ceil($source_width / $ratio_height);
      $destination_height = $max_height; 
    }
    else
    { 
      $destination_width = $max_width; 
      $destination_height = ceil($source_height / $ratio_width);
    }
  }
  else
  {
    // the image doesn't need any resize! We just copy it to the destination
    copy($source_filepath, $destination_filepath);
    return true;
  }
  
  $destination_image = imagecreatetruecolor($destination_width, $destination_height);
  
  imagecopyresampled(
    $destination_image,
    $source_image,
    0,
    0,
    0,
    0,
    $destination_width,
    $destination_height,
    $source_width,
    $source_height
    );
  
  imagejpeg($destination_image, $destination_filepath, $quality);
  // freeing memory ressources
  imagedestroy($source_image);
  imagedestroy($destination_image);

  // everything should be OK if we are here!
  return true;
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
?>