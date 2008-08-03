<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include(AMM_PATH."amm_aip.class.inc.php");

global $prefixeTable;

load_language('plugin.lang', AMM_PATH);

$main_plugin_object = get_plugin_data($plugin_id);

$plugin_ai = new AMM_AIP($prefixeTable, $main_plugin_object->get_filelocation());
$plugin_ai->manage();

?>