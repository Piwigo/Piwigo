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

defined('PHPWG_ROOT_PATH') or trigger_error('Hacking attempt!', E_USER_ERROR);

$errors = array();

if ( isset($_POST['d']) )
{
  $pderivatives = $_POST['d'];
  $pwatermark = $_POST['w'];

  // step 1 - sanitize HTML input
  foreach($pderivatives as $type => &$pderivative)
  {
    if ($pderivative['must_square'] = ($type==IMG_SQUARE ? true : false))
    {
      $pderivative['h'] = $pderivative['w'];
      $pderivative['minh'] = $pderivative['minw'] = $pderivative['w'];
      $pderivative['crop'] = 100;
    }
    $pderivative['must_enable'] = ($type==IMG_SQUARE || $type==IMG_THUMB)? true : false;
    $pderivative['enabled'] = isset($pderivative['enabled']) || $pderivative['must_enable'] ? true : false;
  }
  unset($pderivative);

  // step 2 - check validity
  $prev_w = $prev_h = 0;
  foreach(ImageStdParams::get_all_types() as $type)
  {
    $pderivative = $pderivatives[$type];
    if (!$pderivative['enabled'])
      continue;

    $v = intval($pderivative['w']);
    if ($v<=0 || $v<=$prev_w)
    {
      $errors[$type]['w'] = '>'.$prev_w;
    }
    $v = intval($pderivative['h']);
    if ($v<=0 || $v<=$prev_h)
    {
      $errors[$type]['h'] = '>'.$prev_h;
    }
    $v = intval($pderivative['crop']);
    if ($v<0 || $v>100)
    {
      $errors[$type]['crop'] = '[0..100]';
    }
    
    if ($v!=0)
    {
      $v = intval($pderivative['minw']);
      if ($v<0 || $v>intval($pderivative['w']))
      {
        $errors[$type]['minw'] = '[0..'.intval($pderivative['w']).']';
      }
      $v = intval($pderivative['minh']);
      if ($v<0 || $v>intval($pderivative['h']))
      {
        $errors[$type]['minh'] = '[0..'.intval($pderivative['h']).']';
      }
    }
    
    if (count($errors)==0)
    {
      $prev_w = intval($pderivative['w']);
      $prev_h = intval($pderivative['h']);
    }
    
    $v = intval($pderivative['sharpen']);
    if ($v<0 || $v>100)
    {
      $errors[$type]['sharpen'] = '[0..100]';
    }
    $v = intval($pderivative['quality']);
    if ($v<=0 || $v>100)
    {
      $errors[$type]['quality'] = '(0..100]';
    }
  }
  $v = intval($pwatermark['xpos']);
  if ($v<0 || $v>100)
  {
    $errors['watermark']['xpos'] = '[0..100]';
  }
  $v = intval($pwatermark['ypos']);
  if ($v<0 || $v>100)
  {
    $errors['watermark']['ypos'] = '[0..100]';
  }
  $v = intval($pwatermark['opacity']);
  if ($v<=0 || $v>100)
  {
    $errors['watermark']['opacity'] = '(0..100]';
  }


  // step 3 - save data
  if (count($errors)==0)
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
    ImageStdParams::set_watermark($watermark);
    
    $enabled = ImageStdParams::get_defined_type_map();
    $disabled = @unserialize( @$conf['disabled_derivatives'] );
    if ($disabled===false)
    {
      $disabled = array();
    }
    $changed_types = array();
    
    foreach(ImageStdParams::get_all_types() as $type)
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
        $new_params->quality = intval($pderivative['quality']);
        ImageStdParams::apply_global($new_params);
        
        if (isset($enabled[$type]))
        {
          $old_params = $enabled[$type];
          $same = true;
          if ( !size_equals($old_params->sizing->ideal_size, $new_params->sizing->ideal_size)
            or $old_params->sizing->max_crop != $new_params->sizing->max_crop)
          {
            $same = false;
          }

          if ( $same && $new_params->sizing->max_crop != 0 
              && !size_equals($old_params->sizing->min_size, $new_params->sizing->min_size) )
          {
            $same = false;
          }

          if ( $same && 
              ( $new_params->sharpen != $old_params->sharpen
              || $new_params->quality > $old_params->quality)
             )
          {
            $same = false;
          }
          
          if ($same &&
            ( $new_params->use_watermark != $old_params->use_watermark 
             || $new_params->use_watermark && $watermark_changed )
            )
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
    ImageStdParams::set_and_save($enabled_by);
    if (count($disabled)==0)
    {
      $query='DELETE FROM '.CONFIG_TABLE.' WHERE param = \'disabled_derivatives\'';
      pwg_query($query);
    }
    else
    {
      conf_update_param('disabled_derivatives', addslashes(serialize($disabled)) );
    }
    $conf['disabled_derivatives']=serialize($disabled);
    
    if (count($changed_types))
    {
      clear_derivative_cache($changed_types);
    }
  }
  else
  {
    $template->assign('derivatives', $pderivatives);
    $template->assign('watermark', $pwatermark);
    $template->assign('ferrors', $errors);
  }
}

if (count($errors)==0)
{
  $enabled = ImageStdParams::get_defined_type_map();
  $disabled = @unserialize( @$conf['disabled_derivatives'] );
  if ($disabled===false)
  {
    $disabled = array();
  }

  $tpl_vars = array();
  foreach(ImageStdParams::get_all_types() as $type)
  {
    $tpl_var = array();

    $tpl_var['must_square'] = ($type==IMG_SQUARE ? true : false);
    $tpl_var['must_enable'] = ($type==IMG_SQUARE || $type==IMG_THUMB)? true : false;

    if ($params=@$enabled[$type])
    {
      $tpl_var['enabled']=true;
    }
    else
    {
      $tpl_var['enabled']=false;
      $params=@$disabled[$type];
    }

    if ($params)
    {
      list($tpl_var['w'],$tpl_var['h']) = $params->sizing->ideal_size;
      if ( ($tpl_var['crop'] = round(100*$params->sizing->max_crop)) > 0)
      {
        list($tpl_var['minw'],$tpl_var['minh']) = $params->sizing->min_size;
      }
      else
      {
        $tpl_var['minw'] = $tpl_var['minh'] = "";
      }
      $tpl_var['sharpen'] = $params->sharpen;
      $tpl_var['quality'] = $params->quality;
    }
    $tpl_vars[$type]=$tpl_var;
  }
  $template->assign('derivatives', $tpl_vars);
  
  $wm = ImageStdParams::get_watermark();
  $template->assign('watermark', array(
      'file' => $wm->file,
      'minw' => $wm->min_size[0],
      'minh' => $wm->min_size[1],
      'xpos' => $wm->xpos,
      'ypos' => $wm->ypos,
      'xrepeat' => $wm->xrepeat,
      'opacity' => $wm->opacity,
    ));
}

$watermark_files = array();
foreach (glob(PHPWG_ROOT_PATH.'themes/default/watermarks/*.png') as $file)
{
  $watermark_files[] = substr($file, strlen(PHPWG_ROOT_PATH));
}
$watermark_filemap = array( '' => '---' );
foreach( $watermark_files as $file)
{
  $display = basename($file);
  $watermark_filemap[$file] = $display;
}
$template->assign('watermark_files', $watermark_filemap);

$template->set_filename('derivatives', 'derivatives.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'derivatives');
?>