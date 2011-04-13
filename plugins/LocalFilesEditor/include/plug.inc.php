<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

$edited_file = PHPWG_PLUGINS_PATH . "PersonalPlugin/main.inc.php";

if (file_exists($edited_file))
{
  $content_file = file_get_contents($edited_file);
}
else
{
  $content_file = "<?php\n/*
Plugin Name: " . l10n('locfiledit_onglet_plug') . "
Version: 1.0
Description: " . l10n('locfiledit_onglet_plug') . "
Plugin URI: http://piwigo.org
Author:
Author URI:
*/\n\n\n\n\n?>";
}

$codemirror_mode = 'application/x-httpd-php';

?>