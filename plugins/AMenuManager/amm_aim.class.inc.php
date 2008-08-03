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

  AMM_AIM : classe to manage plugin integration into plugin menu

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once('amm_root.class.inc.php');

class AMM_AIM extends AMM_root
{ 
  function AMM_AIM($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);
  }

  /*
    initialize events call for the plugin
  */
  function init_events()
  {
    add_event_handler('get_admin_plugin_menu_links', array(&$this, 'plugin_admin_menu') );
  }

} // amm_aim  class


?>
