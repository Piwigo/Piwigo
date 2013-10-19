<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

// original resize
$original_fields = array(
  'original_resize',
  'original_resize_maxwidth',
  'original_resize_maxheight',
  'original_resize_quality',
  );

$updates = array();

foreach ($original_fields as $field)
{
  $value = !empty($_POST[$field]) ? $_POST[$field] : null;
  $updates[$field] = $value;
}

save_upload_form_config($updates, $page['errors'], $errors);

if ($_POST['resize_quality'] < 50 or $_POST['resize_quality'] > 98)
{
  $errors['resize_quality'] = '[50..98]';
}

$pderivatives = $_POST['d'];

// step 1 - sanitize HTML input
foreach ($pderivatives as $type => &$pderivative)
{
  if ($pderivative['must_square'] = ($type==IMG_SQUARE ? true : false))
  {
    $pderivative['h'] = $pderivative['w'];
    $pderivative['minh'] = $pderivative['minw'] = $pderivative['w'];
    $pderivative['crop'] = 100;
  }
  $pderivative['must_enable'] = ($type==IMG_SQUARE || $type==IMG_THUMB)? true : false;
  $pderivative['enabled'] = isset($pderivative['enabled']) || $pderivative['must_enable'] ? true : false;

  if (isset($pderivative['crop']))
  {
    $pderivative['crop'] = 100;
    $pderivative['minw'] = $pderivative['w'];
    $pderivative['minh'] = $pderivative['h'];
  }
  else
  {
    $pderivative['crop'] = 0;
    $pderivative['minw'] = null;
    $pderivative['minh'] = null;
  }
}
unset($pderivative);

// step 2 - check validity
$prev_w = $prev_h = 0;
foreach(ImageStdParams::get_all_types() as $type)
{
  $pderivative = $pderivatives[$type];
  if (!$pderivative['enabled'])
  {
    continue;
  }

  if ($type == IMG_THUMB)
  {
    $w = intval($pderivative['w']);
    if ($w <= 0)
    {
      $errors[$type]['w'] = '>0';
    }

    $h = intval($pderivative['h']);
    if ($h <= 0)
    {
      $errors[$type]['h'] = '>0';
    }

    if (max($w,$h) <= $prev_w)
    {
      $errors[$type]['w'] = $errors[$type]['h'] = '>'.$prev_w;
    }
  }
  else
  {
    $v = intval($pderivative['w']);
    if ($v <= 0 or $v <= $prev_w)
    {
      $errors[$type]['w'] = '>'.$prev_w;
    }

    $v = intval($pderivative['h']);
    if ($v <= 0 or $v <= $prev_h)
    {
      $errors[$type]['h'] = '>'.$prev_h;
    }
  }

  if (count($errors) == 0)
  {
    $prev_w = intval($pderivative['w']);
    $prev_h = intval($pderivative['h']);
  }

  $v = intval($pderivative['sharpen']);
  if ($v<0 || $v>100)
  {
    $errors[$type]['sharpen'] = '[0..100]';
  }
}

// step 3 - save data
if (count($errors) == 0)
{
  $quality_changed = ImageStdParams::$quality != intval($_POST['resize_quality']);
  ImageStdParams::$quality = intval($_POST['resize_quality']);

  $enabled = ImageStdParams::get_defined_type_map();
  $disabled = @unserialize( @$conf['disabled_derivatives'] );
  if ($disabled === false)
  {
    $disabled = array();
  }
  $changed_types = array();

  foreach (ImageStdParams::get_all_types() as $type)
  {
    $pderivative = $pderivatives[$type];

    if ($pderivative['enabled'])
    {
      $new_params = new DerivativeParams(
        new SizingParams(
          array(intval($pderivative['w']), intval($pderivative['h'])),
          round($pderivative['crop'] / 100, 2),
          array(intval($pderivative['minw']), intval($pderivative['minh']))
          )
        );
      $new_params->sharpen = intval($pderivative['sharpen']);

      ImageStdParams::apply_global($new_params);

      if (isset($enabled[$type]))
      {
        $old_params = $enabled[$type];
        $same = true;
        if (!size_equals($old_params->sizing->ideal_size, $new_params->sizing->ideal_size)
            or $old_params->sizing->max_crop != $new_params->sizing->max_crop)
        {
          $same = false;
        }

        if ($same
            and $new_params->sizing->max_crop != 0
            and !size_equals($old_params->sizing->min_size, $new_params->sizing->min_size))
        {
          $same = false;
        }

        if ($quality_changed
            || $new_params->sharpen != $old_params->sharpen)
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
        $enabled[$type] = $new_params;
      }
      else
      {// now enabled, before was disabled
        $enabled[$type] = $new_params;
        unset($disabled[$type]);
      }
    }
    else
    {// disabled
      if (isset($enabled[$type]))
      {// now disabled, before was enabled
        $changed_types[] = $type;
        $disabled[$type] = $enabled[$type];
        unset($enabled[$type]);
      }
    }
  }

  $enabled_by = array(); // keys ordered by all types
  foreach(ImageStdParams::get_all_types() as $type)
  {
    if (isset($enabled[$type]))
    {
      $enabled_by[$type] = $enabled[$type];
    }
  }

  foreach( array_keys(ImageStdParams::$custom) as $custom)
  {
    if (isset($_POST['delete_custom_derivative_'.$custom]))
    {
      $changed_types[] = $custom;
      unset(ImageStdParams::$custom[$custom]);
    }
  }

  ImageStdParams::set_and_save($enabled_by);
  if (count($disabled) == 0)
  {
    $query='DELETE FROM '.CONFIG_TABLE.' WHERE param = \'disabled_derivatives\'';
    pwg_query($query);
  }
  else
  {
    conf_update_param('disabled_derivatives', addslashes(serialize($disabled)) );
  }
  $conf['disabled_derivatives'] = serialize($disabled);

  if (count($changed_types))
  {
    clear_derivative_cache($changed_types);
  }

  $page['infos'][] = l10n('Your configuration settings are saved');
}
else
{
  foreach ($original_fields as $field)
  {
    if (isset($_POST[$field]))
    {
      $template->append(
        'sizes',
        array(
          $field => $_POST[$field]
          ),
        true
        );
    }
  }

  $template->assign('derivatives', $pderivatives);
  $template->assign('ferrors', $errors);
  $template->assign('resize_quality', $_POST['resize_quality']);
  $page['sizes_loaded_in_tpl'] = true;
}
?>