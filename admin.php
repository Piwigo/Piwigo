<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                    synchronize user informations                      |
// +-----------------------------------------------------------------------+

sync_users();

// +-----------------------------------------------------------------------+
// |  Check configuration and add notes on problem                         |
// +-----------------------------------------------------------------------+

check_conf();

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
$opt_link = $link_start.'cat_options&amp;section=';
//----------------------------------------------------- template initialization
$title = l10n('PhpWebGallery Administration'); // for include/page_header.php
$page['page_banner'] = '<h1>'.l10n('PhpWebGallery Administration').'</h1>';
$page['body_id'] = 'theAdminPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('admin' => 'admin.tpl'));

$template->assign_vars(
  array(
    'U_SITE_MANAGER'=> $link_start.'site_manager',
    'U_HISTORY'=> $link_start.'stats',
    'U_FAQ'=> $link_start.'help',
    'U_SITES'=> $link_start.'remote_site',
    'U_MAINTENANCE'=> $link_start.'maintenance',
    'U_NOTIFICATION_BY_MAIL'=> $link_start.'notification_by_mail',
    'U_ADVANCED_FEATURE'=> $link_start.'advanced_feature',
    'U_CONFIG_GENERAL'=> $conf_link.'general',
    'U_CONFIG_COMMENTS'=> $conf_link.'comments',
    'U_CONFIG_DISPLAY'=> $conf_link.'default',
    'U_CATEGORIES'=> $link_start.'cat_list',
    'U_MOVE'=> $link_start.'cat_move',
    'U_CAT_UPLOAD'=> $opt_link.'upload',
    'U_CAT_COMMENTS'=> $opt_link.'comments',
    'U_CAT_VISIBLE'=> $opt_link.'visible',
    'U_CAT_STATUS'=> $opt_link.'status',
    'U_CAT_OPTIONS'=> $link_start.'cat_options',
    'U_CAT_UPDATE'=> $link_start.'site_update&amp;site=1',
    'U_WAITING'=> $link_start.'waiting',
    'U_COMMENTS'=> $link_start.'comments',
    'U_RATING'=> $link_start.'rating',
    'U_CADDIE'=> $link_start.'element_set&amp;cat=caddie',
    'U_TAGS'=> $link_start.'tags',
    'U_THUMBNAILS'=> $link_start.'thumbnail',
    'U_USERS'=> $link_start.'user_list',
    'U_GROUPS'=> $link_start.'group_list',
    'U_RETURN'=> make_index_url(),
    'U_ADMIN'=> PHPWG_ROOT_PATH.'admin.php',
    'L_ADMIN' => $lang['admin'],
    'L_ADMIN_HINT' => $lang['hint_admin']
    )
  );
if ($conf['allow_web_services'])
{
  $template->assign_block_vars(
    'web_services',
    array(
      'U_WS_CHECKER'=> $link_start.'ws_checker',
      )
    );
}
if ($conf['allow_random_representative'])
{
  $template->assign_block_vars(
    'representative',
    array(
      'URL' => $opt_link.'representative'
      )
    );
}

// required before plugin page inclusion
trigger_action('plugin_admin_menu');

include(PHPWG_ROOT_PATH.'admin/'.$page['page'].'.php');

//------------------------------------------------------------- content display
$template->assign_block_vars('plugin_menu.menu_item',
    array(
      'NAME' => l10n('admin'),
      'URL' => $link_start.'plugins'
    )
  );
if ( isset($page['plugin_admin_menu']) )
{
  $plug_base_url = $link_start.'plugin&amp;section=';
  foreach ($page['plugin_admin_menu'] as $menu)
  {
    $template->assign_block_vars('plugin_menu.menu_item',
        array(
          'NAME' => $menu['title'],
          'URL' => $plug_base_url.$menu['uid']
        )
      );
  }
}

// +-----------------------------------------------------------------------+
// |                            errors & infos                             |
// +-----------------------------------------------------------------------+

if (count($page['errors']) != 0)
{
  foreach ($page['errors'] as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}

if (count($page['infos']) != 0)
{
  foreach ($page['infos'] as $info)
  {
    $template->assign_block_vars('infos.info',array('INFO'=>$info));
  }
}

$template->parse('admin');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

// +-----------------------------------------------------------------------+
// |                     order permission refreshment                      |
// +-----------------------------------------------------------------------+

$query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\'
;';
pwg_query($query);
?>
