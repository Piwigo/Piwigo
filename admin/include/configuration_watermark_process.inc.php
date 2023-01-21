<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

if (!is_webmaster())
{
  return;
}

function get_watermark_filename($list, $candidate, $step = 0)
{
  global $change_name;
  $change_name = $candidate;
  if ($step != 0)
  {
    $change_name .= '-'.$step;
  }
  if (in_array($change_name, $list))
  {
    return get_watermark_filename($list, $candidate, $step+1);
  }
  return $change_name.'.png';
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
    if (mkgetdir($upload_dir, MKGETDIR_DEFAULT&~MKGETDIR_DIE_ON_ERROR))
    {
      // file name may include exotic chars like single quote, we need a safe name 
      $new_name = str2url(get_filename_wo_extension($_FILES['watermarkImage']['name']));

      // we need existing watermarks to avoid overwritting one 
      $watermark_files = array();
      if ( ($glob=glob(PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'watermarks/*.png')) !== false)
      {
        foreach ($glob as $file)
        {
          $watermark_files[] = get_filename_wo_extension(
            substr($file, strlen(PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'watermarks/'))
          );
        }
      }

      $file_path = $upload_dir.'/'.get_watermark_filename($watermark_files, $new_name);

      if (move_uploaded_file($_FILES['watermarkImage']['tmp_name'], $file_path))
      {
        $pwatermark['file'] = substr($file_path, strlen(PHPWG_ROOT_PATH));
      }
      else
      {
        $page['errors'][] = $errors['watermarkImage'] = "$file_path " .l10n('no write access');
      }
    }
    else
    {
      $page['errors'][] = $errors['watermarkImage'] = sprintf( l10n('Add write access to the "%s" directory'), $upload_dir);
    }
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
  $watermark->yrepeat = intval($pwatermark['yrepeat']);
  $watermark->opacity = intval($pwatermark['opacity']);
  $watermark->min_size = array(intval($pwatermark['minw']),intval($pwatermark['minh']));

  $old_watermark = ImageStdParams::get_watermark();
  $watermark_changed =
    $watermark->file != $old_watermark->file
    || $watermark->xpos != $old_watermark->xpos
    || $watermark->ypos != $old_watermark->ypos
    || $watermark->xrepeat != $old_watermark->xrepeat
    || $watermark->yrepeat != $old_watermark->yrepeat
    || $watermark->opacity != $old_watermark->opacity;

  // save the new watermark configuration
  ImageStdParams::set_watermark($watermark);

  // do we have to regenerate the derivatives (and which types)?
  $changed_types = array();

  foreach (ImageStdParams::get_defined_type_map() as $type => $params)
  {
    $old_use_watermark = $params->use_watermark;
    ImageStdParams::apply_global($params);

    $changed = $params->use_watermark != $old_use_watermark;
    if (!$changed and $params->use_watermark)
    {
      $changed = $watermark_changed;
    }
    if (!$changed and $params->use_watermark)
    {
      // if thresholds change and before/after the threshold is lower than the corresponding derivative side -> some derivatives might switch the watermark
      $changed |= $watermark->min_size[0]!=$old_watermark->min_size[0] and ($watermark->min_size[0]<$params->max_width() or $old_watermark->min_size[0]<$params->max_width());
      $changed |= $watermark->min_size[1]!=$old_watermark->min_size[1] and ($watermark->min_size[1]<$params->max_height() or $old_watermark->min_size[1]<$params->max_height());
    }

    if ($changed)
    {
      $params->last_mod_time = time();
      $changed_types[] = $type;
    }
  }

  ImageStdParams::save();

  if (count($changed_types))
  {
    clear_derivative_cache($changed_types);
  }

  $page['infos'][] = l10n('Your configuration settings are saved');
  pwg_activity('system', ACTIVITY_SYSTEM_CORE, 'config', array('config_section'=>'watermark'));
}
else
{
  $template->assign('watermark', $pwatermark);
  $template->assign('ferrors', $errors);
}
?>
