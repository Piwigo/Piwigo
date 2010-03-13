<?php
if (!function_exists('selected_admin_menu')) 
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions_themes.inc.php');
}
$themeconf = array(
  'theme'    => 'clear',
  'parent'   => 'default',
  'admin_icon_dir' => 'admin/themes/clear/icon',
  'selected_admin_menu' => selected_admin_menu(),
);
?>
