<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

function plugin_uninstall($plugin_id)
{
  global $conf;
  @unlink( $conf['local_data_dir'].'/plugins/'.$plugin_id.'.dat' );
}
?>
