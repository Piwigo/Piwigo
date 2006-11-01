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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_plugins.inc.php');
check_status(ACCESS_ADMINISTRATOR);

$template->set_filenames(array('plugin' => 'admin/plugin.tpl'));

trigger_action('plugin_admin_menu');


if ( isset($page['plugin_admin_menu']) )
{
  $template->assign_block_vars('plugin_menu.menu_item',
      array(
        'NAME' => l10n('Plugins'),
        'URL' => PHPWG_ROOT_PATH.'admin.php?page=plugins'
      )
    );

  $plug_base_url = PHPWG_ROOT_PATH.'admin.php?page=plugin&amp;section=';
  foreach ($page['plugin_admin_menu'] as $menu)
  {
    if (isset($_GET['section']) and $menu['uid']==$_GET['section'])
    {
      $found_menu=$menu;
    }
    $template->assign_block_vars('plugin_menu.menu_item',
        array(
          'NAME' => $menu['title'],
          'URL' => $plug_base_url.$menu['uid']
        )
      );
  }
}
if ( isset($found_menu) )
{
  call_user_func(
    $found_menu['function'],
    PHPWG_ROOT_PATH.'admin.php?page=plugin&amp;section='.$found_menu['uid'] );
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin');
?>
