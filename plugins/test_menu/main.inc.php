<?php
/*
Plugin Name: Test Menu
Version: 1.0.0
Description: Plugin pour montrer l'usage de la classe Menu / Plugin to show usage of the class Menu 
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

--------------------------------------------------------------------------------
*/

// pour faciliter le debug :o)
 ini_set('error_reporting', E_ALL);
 ini_set('display_errors', true);

if(!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');


define('TEST_MENU_DIR' , basename(dirname(__FILE__)));
define('TEST_MENU_PATH' , PHPWG_PLUGINS_PATH . TEST_MENU_DIR . '/');

define('TEST_MENU_VERSION' , '1.0.0'); // => ne pas oublier la version dans l'entête !!

global $prefixeTable, $menu;

include_once("menu.class.inc.php");

function filemenu()
{
  return(TEST_MENU_PATH."menubar.inc.php");
}

$menu = new Menu();

if(basename($_SERVER["PHP_SELF"])!='admin.php')
{
  add_event_handler('menubar_file', 'filemenu');
}


?>
