<?php

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

define('TEST_MENU_DIR' , basename(dirname(__FILE__)));
define('TEST_MENU_PATH' , PHPWG_PLUGINS_PATH . TEST_MENU_DIR . '/');

include_once("menu.class.inc.php");

function plugin_install($plugin_id, $plugin_version, &$errors)
{
/* -[note]----------------------------------------------------------------------
In normal case, piwigo's registered items of the $menu object are not
initialized by a plugin, but by piwigo (while the install process).
Piwigo's default registered items are initialized here to give a "how to"
example of the classe
----------------------------------------------------------------------------- */
    $menu=new Menu();
    $menu->register('mbLinks', 'Links', 1, 'piwigo');
    $menu->register('mbCategories', 'Categories', 2, 'piwigo');
    $menu->register('mbTags', 'Related tags', 3, 'piwigo');
    $menu->register('mbSpecial', 'special_categories', 4, 'piwigo');
    $menu->register('mbMenu', 'title_menu', 5, 'piwigo');
    $menu->register('mbIdentification', 'identification', 6, 'piwigo');
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
}

function plugin_deactivate($plugin_id)
{
}

function plugin_uninstall($plugin_id)
{
}



?>
