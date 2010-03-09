<?php
if (!function_exists('selected_admin_menu')) 
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions_themes.inc.php');
}
$themeconf = array(
  'template' => 'goto',
  'theme' => 'clear',
  'icon_dir' => 'template/yoga/icon',
  'admin_icon_dir' => 'admin/template/goto/theme/clear/icon',
  'mime_icon_dir' => 'template/yoga/icon/mimetypes',
  'selected_admin_menu' => selected_admin_menu(),
  'local_head' => '',
);
?>
