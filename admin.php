<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// | Basic constants and includes                                          |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
define('IN_ADMIN', true);

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_plugins.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/add_core_tabs.inc.php');

trigger_notify('loc_begin_admin');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Direct actions                                                        |
// +-----------------------------------------------------------------------+

// save plugins_new display order (AJAX action)
if (isset($_GET['plugins_new_order']))
{
  pwg_set_session_var('plugins_new_order', $_GET['plugins_new_order']);
  exit;
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

  $url_params = array();
  foreach (array('page', 'tab', 'section') as $url_param)
  {
    if (isset($_GET[$url_param]))
    {
      $url_params[] = $url_param.'='.$_GET[$url_param];
    }
  }
  
  $redirect_url = 'admin.php';
  if (count($url_params) > 0)
  {
    $redirect_url.= '?'.implode('&amp;', $url_params);
  }

  redirect($redirect_url);
}

// +-----------------------------------------------------------------------+
// | Synchronize user informations                                         |
// +-----------------------------------------------------------------------+

// sync_user() is only useful when external authentication is activated
if ($conf['external_authentification'])
{
  sync_users();
}

// +-----------------------------------------------------------------------+
// | Variables init                                                        |
// +-----------------------------------------------------------------------+

$change_theme_url = PHPWG_ROOT_PATH.'admin.php?';
$test_get = $_GET;
unset($test_get['page']);
unset($test_get['section']);
unset($test_get['tag']);
if (count($test_get) == 0 and !empty($_SERVER['QUERY_STRING']))
{
  $change_theme_url.= str_replace('&', '&amp;', $_SERVER['QUERY_STRING']).'&amp;';
}
$change_theme_url.= 'change_theme=1';

// ?page=plugin-community-pendings is an clean alias of
// ?page=plugin&section=community/admin.php&tab=pendings
if (isset($_GET['page']) and preg_match('/^plugin-([^-]*)(?:-(.*))?$/', $_GET['page'], $matches))
{
  $_GET['page'] = 'plugin';
  $_GET['section'] = $matches[1].'/admin.php';
  if (isset($matches[2]))
  {
    $_GET['tab'] = $matches[2];
  }
}

// ?page=album-134-properties is an clean alias of
// ?page=album&cat_id=134&tab=properties
if (isset($_GET['page']) and preg_match('/^album-(\d+)(?:-(.*))?$/', $_GET['page'], $matches))
{
  $_GET['page'] = 'album';
  $_GET['cat_id'] = $matches[1];
  if (isset($matches[2]))
  {
    $_GET['tab'] = $matches[2];
  }
}

// ?page=photo-1234-properties is an clean alias of
// ?page=photo&image_id=1234&tab=properties
if (isset($_GET['page']) and preg_match('/^photo-(\d+)(?:-(.*))?$/', $_GET['page'], $matches))
{
  $_GET['page'] = 'photo';
  $_GET['image_id'] = $matches[1];
  if (isset($matches[2]))
  {
    $_GET['tab'] = $matches[2];
  }
}

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

$link_start = PHPWG_ROOT_PATH.'admin.php?page=';
$conf_link = $link_start.'configuration&amp;section=';

// +-----------------------------------------------------------------------+
// | Template init                                                         |
// +-----------------------------------------------------------------------+

$title = l10n('Piwigo Administration'); // for include/page_header.php
$page['page_banner'] = '<h1>'.l10n('Piwigo Administration').'</h1>';
$page['body_id'] = 'theAdminPage';

$template->set_filenames(array('admin' => 'admin.tpl'));

$template->assign(
  array(
    'USERNAME' => $user['username'],
    'ENABLE_SYNCHRONIZATION' => $conf['enable_synchronization'],
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
    'U_CONFIG_LANGUAGES' => $link_start.'languages',
    'U_CONFIG_THEMES'=> $link_start.'themes',
    'U_CATEGORIES'=> $link_start.'cat_list',
    'U_CAT_OPTIONS'=> $link_start.'cat_options',
    'U_CAT_UPDATE'=> $link_start.'site_update&amp;site=1',
    'U_RATING'=> $link_start.'rating',
    'U_RECENT_SET'=> $link_start.'batch_manager&amp;filter=prefilter-last_import',
    'U_BATCH'=> $link_start.'batch_manager',
    'U_TAGS'=> $link_start.'tags',
    'U_USERS'=> $link_start.'user_list',
    'U_GROUPS'=> $link_start.'group_list',
    'U_RETURN'=> get_gallery_home_url(),
    'U_ADMIN'=> PHPWG_ROOT_PATH.'admin.php',
    'U_LOGOUT'=> PHPWG_ROOT_PATH.'index.php?act=logout',
    'U_PLUGINS'=> $link_start.'plugins',
    'U_ADD_PHOTOS' => $link_start.'photos_add',
    'U_CHANGE_THEME' => $change_theme_url,
    'U_UPDATES' => $link_start.'updates',
    )
  );
  
if ($conf['activate_comments'])
{
  $template->assign('U_COMMENTS', $link_start.'comments');
  
  // pending comments
  $query = '
SELECT COUNT(*)
  FROM '.COMMENTS_TABLE.'
  WHERE validated=\'false\'
;';
  list($nb_comments) = pwg_db_fetch_row(pwg_query($query));

  if ($nb_comments > 0)
  {
    $template->assign('NB_PENDING_COMMENTS', $nb_comments);
    $page['nb_pending_comments'] = $nb_comments;
  }
}

// any photo in the caddie?
$query = '
SELECT COUNT(*)
  FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
list($nb_photos_in_caddie) = pwg_db_fetch_row(pwg_query($query));

if ($nb_photos_in_caddie > 0)
{
  $template->assign(
    array(
      'NB_PHOTOS_IN_CADDIE' => $nb_photos_in_caddie,
      'U_CADDIE' => $link_start.'batch_manager&amp;filter=prefilter-caddie',
      )
    );
}

// any orphan photo?
$nb_orphans = count(get_orphans());

if ($nb_orphans > 0)
{
  $template->assign(
    array(
      'NB_ORPHANS' => $nb_orphans,
      'U_ORPHANS' => $link_start.'batch_manager&amp;filter=prefilter-no_album',
      )
    );
}

// +-----------------------------------------------------------------------+
// | Plugin menu                                                           |
// +-----------------------------------------------------------------------+

$plugin_menu_links = trigger_change('get_admin_plugin_menu_links', array() );

function UC_name_compare($a, $b)
{
  return strcmp(strtolower($a['NAME']), strtolower($b['NAME']));
}
usort($plugin_menu_links, 'UC_name_compare');
$template->assign('plugin_menu_items', $plugin_menu_links);

// +-----------------------------------------------------------------------+
// | Refresh permissions                                                   |
// +-----------------------------------------------------------------------+

// Only for pages witch change permissions
if (
    in_array($page['page'],
      array(
        'site_manager', // delete site
        'site_update',  // ?only POST
      )
    )
    or ( !empty($_POST) and in_array($page['page'],
        array(
          'album',        // public/private; lock/unlock, permissions
          'cat_move',
          'cat_options',  // public/private; lock/unlock
          'user_list',    // group assoc; user level
          'user_perm',
        )
      )
    )
  )
{
  invalidate_user_cache();
}

// +-----------------------------------------------------------------------+
// | Include specific page                                                 |
// +-----------------------------------------------------------------------+

trigger_notify('loc_begin_admin_page');
include(PHPWG_ROOT_PATH.'admin/'.$page['page'].'.php');

$template->assign('ACTIVE_MENU', get_active_menu($page['page']));

// +-----------------------------------------------------------------------+
// | Sending html code                                                     |
// +-----------------------------------------------------------------------+

// Add the Piwigo Official menu
$template->assign( 'pwgmenu', pwg_URL() );

include(PHPWG_ROOT_PATH.'include/page_header.php');

trigger_notify('loc_end_admin');

flush_page_messages();

$template->pparse('admin');

include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
