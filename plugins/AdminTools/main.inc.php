<?php
/*
Plugin Name: Admin Tools
Version: 2.7.3
Description: Do some admin task from the public pages
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=720
Author: Piwigo team
Author URI: http://piwigo.org
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

define('ADMINTOOLS_ID',       basename(dirname(__FILE__)));
define('ADMINTOOLS_PATH' ,    PHPWG_PLUGINS_PATH . ADMINTOOLS_ID . '/');
define('ADMINTOOLS_ADMIN',    get_root_url() . 'admin.php?page=plugin-' . ADMINTOOLS_ID);

include_once(ADMINTOOLS_PATH . 'include/events.inc.php');
include_once(ADMINTOOLS_PATH . 'include/MultiView.class.php');


global $MultiView;
$MultiView = new MultiView();

add_event_handler('init', 'admintools_init');

add_event_handler('user_init', array(&$MultiView, 'user_init'));
add_event_handler('init', array(&$MultiView, 'init'));

add_event_handler('ws_add_methods', array('MultiView', 'register_ws'));
add_event_handler('delete_user', array('MultiView', 'invalidate_cache'));
add_event_handler('register_user', array('MultiView', 'invalidate_cache'));

if (!defined('IN_ADMIN'))
{
  add_event_handler('loc_after_page_header', 'admintools_add_public_controller');
  add_event_handler('loc_begin_picture', 'admintools_save_picture');
  add_event_handler('loc_begin_index', 'admintools_save_category');
}
else
{
  add_event_handler('loc_begin_page_header', 'admintools_add_admin_controller_setprefilter');
  add_event_handler('loc_after_page_header', 'admintools_add_admin_controller');
  add_event_handler('get_admin_plugin_menu_links', 'admintools_admin_link');
}


function admintools_init()
{
  global $conf;
  $conf['AdminTools'] = safe_unserialize($conf['AdminTools']);

  load_language('plugin.lang', ADMINTOOLS_PATH);
}

function admintools_admin_link($menu) 
{
  $menu[] = array(
    'NAME' => 'Admin Tools',
    'URL' => ADMINTOOLS_ADMIN,
    );

  return $menu;
}
