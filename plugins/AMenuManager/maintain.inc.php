<?php

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

defined('AMM_DIR') || define('AMM_DIR' , basename(dirname(__FILE__)));
defined('AMM_PATH') || define('AMM_PATH' , PHPWG_PLUGINS_PATH . AMM_DIR . '/');
@include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/tables.class.inc.php');


global $gpc_installed, $lang; //needed for plugin manager compatibility

$gpc_installed=false;
if(file_exists(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/common_plugin.class.inc.php'))
{
  @include_once("amm_install.class.inc.php");
  $gpc_installed=true;
}

load_language('plugin.lang', AMM_PATH);

function plugin_install($plugin_id, $plugin_version, &$errors) 
{
  global $prefixeTable, $gpc_installed;
  if($gpc_installed)
  {
    //$menu->register('mbAMM_links', 'Links', 0, 'AMM');
    //$menu->register('mbAMM_randompict', 'Random pictures', 0, 'AMM');
    $amm=new AMM_install($prefixeTable, __FILE__);
    $result=$amm->install();
  }
  else
  {
    array_push($errors, l10n('Grum Plugin Classes is not installed'));
  }
}

function plugin_activate($plugin_id, $plugin_version, &$errors)
{
}

function plugin_deactivate($plugin_id)
{
}

function plugin_uninstall($plugin_id)
{
  global $prefixeTable;
  $amm=new AMM_install($prefixeTable, __FILE__);
  $result=$amm->uninstall();
}



?>
