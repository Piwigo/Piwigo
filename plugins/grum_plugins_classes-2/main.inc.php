<?php
/*
Plugin Name: Grum Plugins Classes.2
Version: 2.0
Description: Collection de classes partagées entre mes plugins (existants, ou à venir) / Partaged classes between my plugins (actuals or futures)
Plugin URI: http://piwigo.org
Author: Piwigo team
Author URI: http://piwigo.org
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
  2.0.0       - 20/07/08  +convert classes for piwigo 2.0

:: TO DO

:: WHAT ? WHY ?
This plugin doesn't do anything itself. It just provide classes for others plugins.

Classes version for this package
    ajax.class.php -v2.0 + ajax.js -v1.0.1
    common_plugin.class.php -v2.0
    css.class.php -v2.0
    pages_navigation.class.php -v1.0
    public_integration.class.php -v1.0
    tables.class.php -v1.3
    tabsheets.class.inc.php -v1.1
    translate.class.inc.php -v2.0.0 + google_translate.js -v2.0.0
    users_groups.class.inc.php -v1.0
    genericjs.class.inc.php -v1.0 + genericjs.js -v1.0

See each file to know more about them
--------------------------------------------------------------------------------
*/

if(!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

define('GPC_DIR' , basename(dirname(__FILE__)));
define('GPC_PATH' , PHPWG_PLUGINS_PATH . GPC_DIR . '/');

define('GPC_VERSION' , '2.0.0');

?>
