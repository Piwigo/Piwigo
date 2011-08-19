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


// +-----------------------------------------------------------------------+
// | Uploaded photos                                                       |
// +-----------------------------------------------------------------------+

if (isset($page['thumbnails']))
{
  $template->assign(
    array(
      'thumbnails' => $page['thumbnails'],
      )
    );

  // only display the batch link if we have more than 1 photo
  if (count($page['thumbnails']) > 1)
  {
    $template->assign(
      array(
        'batch_link' => $page['batch_link'],
        'batch_label' => sprintf(
          l10n('Manage this set of %d photos'),
          count($page['thumbnails'])
          ),
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// | Photo selection                                                       |
// +-----------------------------------------------------------------------+

$uploadify_path = PHPWG_ROOT_PATH.'admin/include/uploadify';

$upload_max_filesize = min(
  get_ini_size('upload_max_filesize'),
  get_ini_size('post_max_size')
  );

if ($upload_max_filesize == get_ini_size('upload_max_filesize'))
{
  $upload_max_filesize_shorthand = get_ini_size('upload_max_filesize', false);
}
else
{
  $upload_max_filesize_shorthand = get_ini_size('post_max_filesize', false);
}

$template->assign(
    array(
      'F_ADD_ACTION'=> PHOTOS_ADD_BASE_URL,
      'uploadify_path' => $uploadify_path,
      'upload_max_filesize' => $upload_max_filesize,
      'upload_max_filesize_shorthand' => $upload_max_filesize_shorthand,
    )
  );

// what is the maximum number of pixels permitted by the memory_limit?
if (pwg_image::get_library() == 'gd')
{
  $fudge_factor = 1.7;
  $available_memory = get_ini_size('memory_limit') - memory_get_usage();
  $max_upload_width = round(sqrt($available_memory/(2 * $fudge_factor)));
  $max_upload_height = round(2 * $max_upload_width / 3);
  
  // we don't want dimensions like 2995x1992 but 3000x2000
  $max_upload_width = round($max_upload_width/100)*100;
  $max_upload_height = round($max_upload_height/100)*100;
  
  $max_upload_resolution = floor($max_upload_width * $max_upload_height / (1000000));

  // no need to display a limitation warning if the limitation is huge like 20MP
  if ($max_upload_resolution < 25)
  {
    $template->assign(
      array(
        'max_upload_width' => $max_upload_width,
        'max_upload_height' => $max_upload_height,
        'max_upload_resolution' => $max_upload_resolution,
        )
      );
  }
}

$upload_modes = array('html', 'multiple');
$upload_mode = isset($conf['upload_mode']) ? $conf['upload_mode'] : 'multiple';

if (isset($_GET['upload_mode']) and in_array($_GET['upload_mode'], $upload_modes))
{
  $upload_mode = $_GET['upload_mode'];
  conf_update_param('upload_mode', $upload_mode);
}

// what is the upload switch mode
$index_of_upload_mode = array_flip($upload_modes);
$upload_mode_index = $index_of_upload_mode[$upload_mode];
$upload_switch = $upload_modes[ ($upload_mode_index + 1) % 2 ];

$template->assign(
    array(
      'upload_mode' => $upload_mode,
      'form_action' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_mode.'&amp;processed=1',
      'switch_url' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_switch,
      'upload_id' => md5(rand()),
      'session_id' => session_id(),
      'pwg_token' => get_pwg_token(),
      'another_upload_link' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_mode,
    )
  );

$upload_file_types = 'jpeg, png, gif';
if ('html' == $upload_mode)
{
  $upload_file_types.= ', zip';
}
$template->assign(
  array(
    'upload_file_types' => $upload_file_types,
    )
  );

// +-----------------------------------------------------------------------+
// | Categories                                                            |
// +-----------------------------------------------------------------------+

// we need to know the category in which the last photo was added
$selected_category = array();
$selected_parent = array();

$query = '
SELECT
    category_id,
    id_uppercat
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON image_id = i.id
    JOIN '.CATEGORIES_TABLE.' AS c ON category_id = c.id
  ORDER BY i.id DESC
  LIMIT 1
;';
$result = pwg_query($query);
if (pwg_db_num_rows($result) > 0)
{
  $row = pwg_db_fetch_assoc($result);
  
  $selected_category = array($row['category_id']);

  if (!empty($row['id_uppercat']))
  {
    $selected_parent = array($row['id_uppercat']);
  }
}

// existing album
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';

display_select_cat_wrapper(
  $query,
  $selected_category,
  'category_options'
  );

// new category
display_select_cat_wrapper(
  $query,
  $selected_parent,
  'category_parent_options'
  );


// image level options
$selected_level = isset($_POST['level']) ? $_POST['level'] : 0;
$template->assign(
    array(
      'level_options'=> get_privacy_level_options(),
      'level_options_selected' => array($selected_level)
    )
  );

// +-----------------------------------------------------------------------+
// | Setup errors/warnings                                                 |
// +-----------------------------------------------------------------------+

// Errors
$setup_errors = array();

$error_message = ready_for_upload_message();
if (!empty($error_message))
{
  array_push($setup_errors, $error_message);
}

if (!function_exists('gd_info'))
{
  array_push($setup_errors, l10n('GD library is missing'));
}

$template->assign(
  array(
    'setup_errors'=> $setup_errors,
    )
  );

// Warnings
if (isset($_GET['hide_warnings']))
{
  $_SESSION['upload_hide_warnings'] = true;
}

if (!isset($_SESSION['upload_hide_warnings']))
{
  $setup_warnings = array();
  
  if ($conf['use_exif'] and !function_exists('read_exif_data'))
  {
    array_push(
      $setup_warnings,
      l10n('Exif extension not available, admin should disable exif use')
      );
  }

  if (get_ini_size('upload_max_filesize') > get_ini_size('post_max_size'))
  {
    array_push(
      $setup_warnings,
      sprintf(
        l10n('In your php.ini file, the upload_max_filesize (%sB) is bigger than post_max_size (%sB), you should change this setting'),
        get_ini_size('upload_max_filesize', false),
        get_ini_size('post_max_size', false)
        )
      );
  }

  $template->assign(
    array(
      'setup_warnings' => $setup_warnings,
      'hide_warnings_link' => PHOTOS_ADD_BASE_URL.'&amp;upload_mode='.$upload_mode.'&amp;hide_warnings=1'
      )
    );
}

?>