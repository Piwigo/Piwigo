<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2010      Pierrick LE GALL             http://piwigo.org |
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

if (!defined('PHOTOS_ADD_BASE_URL'))
{
  die ("Hacking attempt!");
}

// by default, the form values are the current configuration
// we may overwrite them with the current form values
$form_values = array();

foreach ($upload_form_config as $param_shortname => $param)
{
  $param_name = 'upload_form_'.$param_shortname;
  $form_values[$param_shortname] = $conf[$param_name];
}

// +-----------------------------------------------------------------------+
// |                             process form                              |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $updates = array();

  // let's care about the specific checkbox that disable/enable other
  // settings
  $field = 'websize_resize';
  $fields[] = $field;
  
  if (!empty($_POST[$field]))
  {
    $fields[] = 'websize_maxwidth';
    $fields[] = 'websize_maxheight';
    $fields[] = 'websize_quality';
  }

  // hd_keep
  $field = 'hd_keep';
  $fields[] = $field;
  
  if (!empty($_POST[$field]))
  {
    $field = 'hd_resize';
    $fields[] = $field;
    
    if (!empty($_POST[$field]))
    {
      $fields[] = 'hd_maxwidth';
      $fields[] = 'hd_maxheight';
      $fields[] = 'hd_quality';
    }
  }
  
  // and now other fields, processed in a generic way
  $fields[] = 'thumb_maxwidth';
  $fields[] = 'thumb_maxheight';
  $fields[] = 'thumb_quality';
  $fields[] = 'thumb_crop';
  $fields[] = 'thumb_follow_orientation';

  foreach ($fields as $field)
  {
    $value = !empty($_POST[$field]) ? $_POST[$field] : null;
    $form_values[$field] = $value;
    $updates[$field] = $value;
  }

  save_upload_form_config($updates, $page['errors']);

  if (count($page['errors']) == 0)
  {
    array_push(
      $page['infos'],
      l10n('Your configuration settings are saved')
      );
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

foreach (array_keys($upload_form_config) as $field)
{
  if (is_bool($upload_form_config[$field]['default']))
  {
    $form_values[$field] = $form_values[$field] ? 'checked="checked"' : '';
  }
}

$template->assign(
    array(
      'F_ADD_ACTION'=> PHOTOS_ADD_BASE_URL,
      'MANAGE_HD' => (pwg_image::is_imagick() or pwg_image::is_ext_imagick()),
      'values' => $form_values
    )
  );


// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'photos_add');
?>