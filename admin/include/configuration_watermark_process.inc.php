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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

$errors = array();
$pwatermark = $_POST['w'];

// step 0 - manage upload if any
if (isset($_FILES['watermarkImage']) and !empty($_FILES['watermarkImage']['tmp_name']))
{
  list($width, $height, $type) = getimagesize($_FILES['watermarkImage']['tmp_name']);
  if (IMAGETYPE_PNG != $type)
  {
    $errors['watermarkImage'] = sprintf(
      l10n('Allowed file types: %s.'),
      'PNG'
      );
  }
  else
  {
    $upload_dir = PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'watermarks';

    include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');
    prepare_directory($upload_dir);

    $new_name = get_filename_wo_extension($_FILES['watermarkImage']['name']).'.png';
    $file_path = $upload_dir.'/'.$new_name; 
  
    move_uploaded_file($_FILES['watermarkImage']['tmp_name'], $file_path);
    
    $pwatermark['file'] = substr($file_path, strlen(PHPWG_ROOT_PATH));
  }
}

// step 1 - sanitize HTML input
switch ($pwatermark['position'])
{
  case 'topleft':
  {
    $pwatermark['xpos'] = 0;
    $pwatermark['ypos'] = 0;
    break;
  }
  case 'topright':
  {
    $pwatermark['xpos'] = 100;
    $pwatermark['ypos'] = 0;
    break;
  }
  case 'middle':
  {
    $pwatermark['xpos'] = 50;
    $pwatermark['ypos'] = 50;
    break;
  }
  case 'bottomleft':
  {
    $pwatermark['xpos'] = 0;
    $pwatermark['ypos'] = 100;
    break;
  }
  case 'bottomright':
  {
    $pwatermark['xpos'] = 100;
    $pwatermark['ypos'] = 100;
    break;
  }
}

// step 2 - check validity
$v = intval($pwatermark['xpos']);
if ($v < 0 or $v > 100)
{
  $errors['watermark']['xpos'] = '[0..100]';
}

$v = intval($pwatermark['ypos']);
if ($v < 0 or $v > 100)
{
  $errors['watermark']['ypos'] = '[0..100]';
}

$v = intval($pwatermark['opacity']);
if ($v <= 0 or $v > 100)
{
  $errors['watermark']['opacity'] = '(0..100]';
}

// step 3 - save data
if (count($errors) == 0)
{
  $watermark = new WatermarkParams();
  $watermark->file = $pwatermark['file'];
  $watermark->xpos = intval($pwatermark['xpos']);
  $watermark->ypos = intval($pwatermark['ypos']);
  $watermark->xrepeat = intval($pwatermark['xrepeat']);
  $watermark->opacity = intval($pwatermark['opacity']);
  $watermark->min_size = array(intval($pwatermark['minw']),intval($pwatermark['minh']));

  $old_watermark = ImageStdParams::get_watermark();
  $watermark_changed =
    $watermark->file != $old_watermark->file
    || $watermark->xpos != $old_watermark->xpos
    || $watermark->ypos != $old_watermark->ypos
    || $watermark->xrepeat != $old_watermark->xrepeat
    || $watermark->opacity != $old_watermark->opacity;

  // do we have to regenerate the derivatives?
  $old_enabled = ImageStdParams::get_defined_type_map();
  // $disabled = @unserialize( @$conf['disabled_derivatives'] );
  // if ($disabled===false)
  // {
  //   $disabled = array();
  // }

  // save the new watermark configuration
  ImageStdParams::set_watermark($watermark);

  $new_enabled = ImageStdParams::get_defined_type_map();
  
  $changed_types = array();

  foreach(ImageStdParams::get_all_types() as $type)
  {
    if (isset($old_enabled[$type]))
    {
      $old_params = $old_enabled[$type];
      // echo '<pre>old '.$type."\n"; print_r($old_params); echo '</pre>';
     
      $new_params = $new_enabled[$type];
      ImageStdParams::apply_global($new_params);
      // echo '<pre>new '.$type."\n"; print_r($old_params); echo '</pre>';
      
      $same = true;
          
      if ($new_params->use_watermark != $old_params->use_watermark
          or $new_params->use_watermark and $watermark_changed)
      {
        $same = false;
      }

      if (!$same)
      {
        $new_params->last_mod_time = time();
        $changed_types[] = $type;
      }
      else
      {
        $new_params->last_mod_time = $old_params->last_mod_time;
      }
      $new_enabled[$type] = $new_params;
    }
  }

  $enabled_by = array(); // keys ordered by all types
  foreach(ImageStdParams::get_all_types() as $type)
  {
    if (isset($new_enabled[$type]))
    {
      $enabled_by[$type] = $new_enabled[$type];
    }
  }
  ImageStdParams::set_and_save($enabled_by);

  if (count($changed_types))
  {
    clear_derivative_cache($changed_types);
  }

  array_push(
    $page['infos'],
    l10n('Your configuration settings are saved')
    );
}
else
{
  $template->assign('watermark', $pwatermark);
  $template->assign('ferrors', $errors);
}
?>