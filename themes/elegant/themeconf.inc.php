<?php
/*
Theme Name: elegant
Version: 2.7.1
Description: Dark background, grayscale.
Theme URI: http://piwigo.org/ext/extension_view.php?eid=685
Author: Piwigo team
Author URI: http://piwigo.org
*/
$themeconf = array(
  'name'  => 'elegant',
  'parent' => 'default',
  'local_head'  => 'local_head.tpl'
);
// Need upgrade?
global $conf;
include(PHPWG_THEMES_PATH.'elegant/admin/upgrade.inc.php');
add_event_handler('loc_begin_picture', 'level_separator_elegant');
function level_separator_elegant()
{
  global $template;
  $template->assign( 'LEVEL_SEPARATOR', '#&$' );
}
add_event_handler('init', 'set_config_values_elegant');
function set_config_values_elegant()
{
  global $conf, $template;
  $config = unserialize( $conf['elegant'] );
  $template->assign( 'elegant', $config );
}

?>
