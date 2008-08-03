<?php
/*
Plugin Name: Advanced Menu Manager
Version: 1.0.0
Description: Gestion avancée du menu / Advanced management of menu
Plugin URI: http://phpwebgallery.net/ext/extension_view.php?eid=
*/

/*
--------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
--------------------------------------------------------------------------------

:: HISTORY

1.0.0       - 27/07/08  -

:: TO DO

--------------------------------------------------------------------------------

:: NFO
  AMM_AIM : classe to manage plugin integration into plugin menu
  AMM_AIP : classe to manage plugin admin pages
  AMM_PIP : classe to manage plugin public integration

--------------------------------------------------------------------------------
*/

// pour faciliter le debug :o)
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('AMM_DIR' , basename(dirname(__FILE__)));
define('AMM_PATH' , PHPWG_PLUGINS_PATH . AMM_DIR . '/');

define('AMM_VERSION' , '1.0.0'); // => ne pas oublier la version dans l'entête !!

global $prefixeTable, $menu;

if(basename($_SERVER["PHP_SELF"])=='admin.php')
{
  //AMM admin part loaded and active only if in admin page
  include_once("amm_aim.class.inc.php");

  $obj = new AMM_AIM($prefixeTable, __FILE__);
  $obj->init_events();
  set_plugin_data($plugin['id'], $obj);
}
else
{
  //AMM public part loaded and active only if in admin page
  include_once("amm_pip.class.inc.php");

  $obj = new AMM_PIP($prefixeTable, __FILE__);
  set_plugin_data($plugin['id'], $obj);
}

?>
