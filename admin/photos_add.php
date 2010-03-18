<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

define(
  'PHOTOS_ADD_BASE_URL',
  get_root_url().'admin.php?page=photos_add'
  );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                          Load configuration                           |
// +-----------------------------------------------------------------------+

// automatic fill of configuration parameters
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
    'error_message' => 'The websize maximum width must be a number between %d and %d',
    ),
  
  'websize_maxheight' => array(
    'default' => 600,
    'min' => 100,
    'max' => 1200,
    'pattern' => '/^\d+$/',
    'can_be_null' => true,
    'error_message' => 'The websize maximum height must be a number between %d and %d',
    ),
  
  'websize_quality' => array(
    'default' => 95,
    'min' => 50,
    'max' => 100,
    'pattern' => '/^\d+$/',
    'can_be_null' => false,
    'error_message' => 'The websize image quality must be a number between %d and %d',
    ),
  
  'thumb_maxwidth' => array(
    'default' => 128,
    'min' => 50,
    'max' => 300,
    'pattern' => '/^\d+$/',
    'can_be_null' => false,
    'error_message' => 'The thumbnail maximum width must be a number between %d and %d',
    ),
  
  'thumb_maxheight' => array(
    'default' => 96,
    'min' => 50,
    'max' => 300,
    'pattern' => '/^\d+$/',
    'can_be_null' => false,
    'error_message' => 'The thumbnail maximum height must be a number between %d and %d',
    ),
  
  'thumb_quality' => array(
    'default' => 95,
    'min' => 50,
    'max' => 100,
    'pattern' => '/^\d+$/',
    'can_be_null' => false,
    'error_message' => 'The thumbnail image quality must be a number between %d and %d',
    ),
  );

$inserts = array();

foreach ($upload_form_config as $param_shortname => $param)
{
  $param_name = 'upload_form_'.$param_shortname;
  
  if (!isset($conf[$param_name]))
  {
    $param_value = boolean_to_string($param['default']);
    
    array_push(
      $inserts,
      array(
        'param' => $param_name,
        'value' => $param_value,
        )
      );
    $conf[$param_name] = $param_value;
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

// +-----------------------------------------------------------------------+
// |                                 Tabs                                  |
// +-----------------------------------------------------------------------+

$tabs = array(
  array(
    'code' => 'direct',
    'label' => 'Upload Photos',
    ),
  array(
    'code' => 'settings',
    'label' => 'Settings',
    ),
  array(
    'code' => 'ploader',
    'label' => 'Piwigo Uploader',
    ),
  array(
    'code' => 'ftp',
    'label' => 'FTP + Synchronisation',
    ),
  );

$tab_codes = array_map(
  create_function('$a', 'return $a["code"];'),
  $tabs
  );

if (isset($_GET['section']) and in_array($_GET['section'], $tab_codes))
{
  $page['tab'] = $_GET['section'];
}
else
{
  $page['tab'] = $tabs[0]['code'];
}

$tabsheet = new tabsheet();
foreach ($tabs as $tab)
{
  $tabsheet->add(
    $tab['code'],
    l10n($tab['label']),
    PHOTOS_ADD_BASE_URL.'&amp;section='.$tab['code']
    );
}
$tabsheet->select($page['tab']);
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array(
    'plugin_admin_content' => 'photos_add_'.$page['tab'].'.tpl'
    )
  );

// $template->append(
//   'head_elements',
//   '<link rel="stylesheet" type="text/css" href="'.UPLOAD_FORM_PATH.'upload.css">'."\n"
//   );

// +-----------------------------------------------------------------------+
// |                             Load the tab                              |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'admin/photos_add_'.$page['tab'].'.php');
?>