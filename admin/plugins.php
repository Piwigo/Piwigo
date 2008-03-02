<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
check_status(ACCESS_ADMINISTRATOR);

$my_base_url = PHPWG_ROOT_PATH.'admin.php?page=plugins';


// +-----------------------------------------------------------------------+
// |                     perform requested actions                         |
// +-----------------------------------------------------------------------+
if (isset($_GET['action']) and isset($_GET['plugin']) and !is_adviser())
{
  $plugin_id = $_GET['plugin'];
  $crt_db_plugin = get_db_plugins('', $plugin_id);
  if (!empty($crt_db_plugin))
  {
      $crt_db_plugin = $crt_db_plugin[0];
  }
  else
  {
    unset($crt_db_plugin);
  }

  $errors = array();
  $file_to_include = PHPWG_PLUGINS_PATH . $plugin_id . '/maintain.inc.php';

  switch ($_GET['action'])
  {
    case 'install':
      if (!empty($crt_db_plugin))
      {
        array_push($errors, 'CANNOT install - ALREADY INSTALLED');
        break;
      }
      $fs_plugins = get_fs_plugins();
      if (!isset($fs_plugins[$plugin_id]))
      {
        array_push($errors, 'CANNOT install - NO SUCH PLUGIN');
        break;
      }
      if (file_exists($file_to_include))
      {
        include_once($file_to_include);
        if (function_exists('plugin_install'))
        {
          plugin_install($plugin_id, $fs_plugins[$plugin_id]['version'], $errors);
        }
      }
      if (empty($errors))
      {
        $query = '
INSERT INTO ' . PLUGINS_TABLE . ' (id,version) VALUES ("'
. $plugin_id . '","' . $fs_plugins[$plugin_id]['version'] . '"
)';
        pwg_query($query);
      }
      break;

    case 'activate':
      if (!isset($crt_db_plugin))
      {
        array_push($errors, 'CANNOT ' . $_GET['action'] . ' - NOT INSTALLED');
        break;
      }
      if ($crt_db_plugin['state'] != 'inactive')
      {
        array_push($errors, 'invalid current state ' . $crt_db_plugin['state']);
        break;
      }
      if (file_exists($file_to_include))
      {
        include_once($file_to_include);
        if (function_exists('plugin_activate'))
        {
          plugin_activate($plugin_id, $crt_db_plugin['version'], $errors);
        }
      }
      if (empty($errors))
      {
        $query = '
UPDATE ' . PLUGINS_TABLE . ' SET state="active" WHERE id="' . $plugin_id . '"';
        pwg_query($query);
      }
      break;

    case 'deactivate':
      if (!isset($crt_db_plugin))
      {
        die ('CANNOT ' . $_GET['action'] . ' - NOT INSTALLED');
      }
      if ($crt_db_plugin['state'] != 'active')
      {
        die('invalid current state ' . $crt_db_plugin['state']);
      }
      $query = '
UPDATE ' . PLUGINS_TABLE . ' SET state="inactive" WHERE id="' . $plugin_id . '"';
      pwg_query($query);
      if (file_exists($file_to_include))
      {
        include_once($file_to_include);
        if (function_exists('plugin_deactivate'))
        {
          plugin_deactivate($plugin_id);
        }
      }
      break;

    case 'uninstall':
      if (!isset($crt_db_plugin))
      {
        die ('CANNOT ' . $_GET['action'] . ' - NOT INSTALLED');
      }
      $query = '
DELETE FROM ' . PLUGINS_TABLE . ' WHERE id="' . $plugin_id . '"';
      pwg_query($query);
      if (file_exists($file_to_include))
      {
        include_once($file_to_include);
        if (function_exists('plugin_uninstall'))
        {
          plugin_uninstall($plugin_id);
        }
      }
        break;
			
    case 'delete':
			if (!pm_deltree(PHPWG_PLUGINS_PATH . $plugin_id))
      {
        send_pm_trash(PHPWG_PLUGINS_PATH . $plugin_id);
      }
      break;
  }
  if (empty($errors))
	{
		$my_base_url .= isset($_GET['upgrade']) ?
      '&plugin='.$plugin_id.'&upgrade='.$_GET['upgrade'].'&reactivate=true':'';

		$my_base_url .= isset($_GET['upgradestatus']) ?
      '&plugin='.$plugin_id.'&upgradestatus='.$_GET['upgradestatus']:'';

		redirect($my_base_url);
    }
	else
	{
    $page['errors'] = array_merge($page['errors'], $errors);
  }
}


// +-----------------------------------------------------------------------+
// |                     Sections definitions                              |
// +-----------------------------------------------------------------------+
if (empty($_GET['section']))
{
  $page['section'] = 'list';
}
else
{
  $page['section'] = $_GET['section'];
}

$tab_link = $my_base_url . '&amp;section=';

// TabSheet
$tabsheet = new tabsheet();
// TabSheet initialization
$tabsheet->add('list', l10n('plugins_tab_list'), $tab_link.'list');
$tabsheet->add('update', l10n('plugins_tab_update'), $tab_link.'update');
$tabsheet->add('new', l10n('plugins_tab_new'), $tab_link.'new');
// TabSheet selection
$tabsheet->select($page['section']);
// Assign tabsheet to template
$tabsheet->assign();

$my_base_url .= '&section=' . $page['section'];


// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$fs_plugins = get_fs_plugins();
uasort($fs_plugins, 'name_compare');
$db_plugins = get_db_plugins();
$db_plugins_by_id = array();
foreach ($db_plugins as $db_plugin) {
    $db_plugins_by_id[$db_plugin['id']] = $db_plugin;
}

include(PHPWG_ROOT_PATH.'admin/plugins_'.$page['section'].'.php');

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');

?>