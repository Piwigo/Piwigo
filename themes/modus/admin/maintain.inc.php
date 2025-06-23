<?php

function theme_activate($id, $version, &$errors)
{
  global $conf;

  include_once( dirname(dirname(__FILE__)).'/functions.inc.php');
  $default_conf = modus_get_default_config();

  $my_conf = @$conf['modus_theme'];
  $my_conf = @unserialize($my_conf);
  if (empty($my_conf))
    $my_conf = $default_conf;

  $my_conf = array_merge($default_conf, $my_conf);
  $my_conf = array_intersect_key($my_conf, $default_conf);
  conf_update_param('modus_theme', addslashes(serialize($my_conf)) );
}

function theme_delete()
{
  $query = 'DELETE FROM ' . CONFIG_TABLE . ' WHERE param="modus_theme"';
  pwg_query($query);
}

?>