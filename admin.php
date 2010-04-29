<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
define('IN_ADMIN', true);
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_plugins.inc.php');

trigger_action('loc_begin_admin');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// tags
if (isset($_GET['fckb_tags']))
{
  $query = '
SELECT
    id AS tag_id,
    name AS tag_name
  FROM '.TAGS_TABLE.'
;';
  echo json_encode(get_fckb_taglist($query));
  exit();
}

// theme changer
if (isset($_GET['change_theme']))
{
  $admin_themes = array('roma', 'clear');

  $new_admin_theme = array_pop(
    array_diff(
      $admin_themes,
      array($conf['admin_theme'])
      )
    );

  conf_update_param('admin_theme', $new_admin_theme);

  redirect('admin.php');
}

// +-----------------------------------------------------------------------+
// |                    synchronize user informations                      |
// +-----------------------------------------------------------------------+

sync_users();

// +-----------------------------------------------------------------------+
// |                            variables init                             |
// +-----------------------------------------------------------------------+

if (isset($_GET['page'])
    and preg_match('/^[a-z_]*$/', $_GET['page'])
    and is_file(PHPWG_ROOT_PATH.'admin/'.$_GET['page'].'.php'))
{
  $page['page'] = $_GET['page'];
}
else
{
  $page['page'] = 'intro';
}

$page['errors'] = array();
$page['infos']  = array();

$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
$conf_link = $link_start.'configuration&amp;section=';
//----------------------------------------------------- template initialization
$title = l10n('Piwigo Administration'); // for include/page_header.php
$page['page_banner'] = '<h1>'.l10n('Piwigo Administration').'</h1>';
$page['body_id'] = 'theAdminPage';

$template->set_filenames(array('admin' => 'admin.tpl'));

$template->assign(
  array(
    'USERNAME' => $user['username'],
    'U_SITE_MANAGER'=> $link_start.'site_manager',
    'U_HISTORY_STAT'=> $link_start.'stats',
    'U_FAQ'=> $link_start.'help',
    'U_SITES'=> $link_start.'remote_site',
    'U_MAINTENANCE'=> $link_start.'maintenance',
    'U_NOTIFICATION_BY_MAIL'=> $link_start.'notification_by_mail',
    'U_CONFIG_GENERAL'=> $link_start.'configuration',
    'U_CONFIG_DISPLAY'=> $conf_link.'default',
    'U_CONFIG_EXTENTS'=> $link_start.'extend_for_templates',
    'U_CONFIG_MENUBAR'=> $link_start.'menubar',
    'U_CONFIG_LANGUAGES' => $link_start.'languages_installed',
    'U_CONFIG_THEMES'=> $link_start.'themes_installed',
    'U_CATEGORIES'=> $link_start.'cat_list',
    'U_MOVE'=> $link_start.'cat_move',
    'U_CAT_OPTIONS'=> $link_start.'cat_options',
    'U_CAT_UPDATE'=> $link_start.'site_update&amp;site=1',
    'U_WAITING'=> $link_start.'upload',
    'U_RATING'=> $link_start.'rating',
    'U_CADDIE'=> $link_start.'element_set&amp;cat=caddie',
    'U_RECENT_SET'=> $link_start.'element_set&amp;cat=recent',
    'U_TAGS'=> $link_start.'tags',
    'U_THUMBNAILS'=> $link_start.'thumbnail',
    'U_USERS'=> $link_start.'user_list',
    'U_GROUPS'=> $link_start.'group_list',
    'U_PERMALINKS'=> $link_start.'permalinks',
    'U_RETURN'=> make_index_url(),
    'U_ADMIN'=> PHPWG_ROOT_PATH.'admin.php',
    'U_LOGOUT'=> PHPWG_ROOT_PATH.'index.php?act=logout',
    'U_PLUGINS'=> $link_start.'plugins_list',
    'U_ADD_PHOTOS' => $link_start.'photos_add',
    'U_CHANGE_THEME' => PHPWG_ROOT_PATH.'admin.php?change_theme=1',
    'U_PENDING_COMMENTS' => $link_start.'comments',
    )
  );

//---------------------------------------------------------------- plugin menus
$plugin_menu_links = trigger_event('get_admin_plugin_menu_links', array() );

function UC_name_compare($a, $b)
{
  return strcmp(strtolower($a['NAME']), strtolower($b['NAME']));
}
usort($plugin_menu_links, 'UC_name_compare');
$template->assign('plugin_menu_items', $plugin_menu_links);

include(PHPWG_ROOT_PATH.'admin/'.$page['page'].'.php');

//------------------------------------------------------------- content display

// +-----------------------------------------------------------------------+
// |                            errors & infos                             |
// +-----------------------------------------------------------------------+

$template->assign('ACTIVE_MENU', get_active_menu($page['page']));

if (count($page['errors']) != 0)
{
  $template->assign('errors', $page['errors']);
}

if (count($page['infos']) != 0)
{
  $template->assign('infos', $page['infos']);
}

// Add the Piwigo Official menu
  $template->assign( 'pwgmenu', pwg_URL() );

include(PHPWG_ROOT_PATH.'include/page_header.php');

trigger_action('loc_end_admin');

$template->pparse('admin');

// +-----------------------------------------------------------------------+
// |                     order permission refreshment                      |
// +-----------------------------------------------------------------------+
// Only for pages witch change permissions
if (
    in_array($page['page'],
      array(
        'site_manager', // delete site
        'site_update',  // ?only POST
        'cat_list',     // delete cat
        'cat_modify',   // delete cat; public/private; lock/unlock
        'cat_move',     // ?only POST
        'cat_options',  // ?only POST; public/private; lock/unlock
        'cat_perm',     // ?only POST
        'element_set',  // ?only POST; associate/dissociate
        'picture_modify', // ?only POST; associate/dissociate
        'user_list',    // ?only POST; group assoc
        'user_perm',
        'group_perm',
        'group_list',   // delete group
      )
    )
  )
{
  invalidate_user_cache();
}

include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
