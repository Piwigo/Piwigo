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
      activate_plugin($plugin_id, $errors);
      break;
    case 'deactivate':
      deactivate_plugin($plugin_id, $errors);
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
      if (!empty($crt_db_plugin))
      {
        array_push($errors, 'CANNOT delete - PLUGIN IS INSTALLED');
      }
      elseif (!deltree(PHPWG_PLUGINS_PATH . $plugin_id))
      {
        send_to_trash(PHPWG_PLUGINS_PATH . $plugin_id);
      }
      break;
  }
  if (empty($errors))
  {
    redirect(
        get_root_url()
        .'admin.php'
        .get_query_string_diff( array('action', 'plugin') )
      );
  }
  else
  {
     $page['errors'] = array_merge($page['errors'], $errors);
  }
}


$fs_plugins = get_fs_plugins();
$db_plugins = get_db_plugins();
$db_plugins_by_id = array();
foreach ($db_plugins as $db_plugin)
{
  $db_plugins_by_id[$db_plugin['id']] = $db_plugin;
}


// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('plugins' => 'admin/plugins_list.tpl'));

$base_url = get_root_url().'admin.php';

//----------------------------------------------------------------sort options
$selected_order = isset($_GET['order']) ? $_GET['order'] : 'name';

$url = $base_url.get_query_string_diff( array('action', 'plugin', 'order'));

$template->assign('order',
    array(
      $url.'&amp;order=name' => l10n('Name'),
      $url.'&amp;order=status' => l10n('Status'),
      $url.'&amp;order=author' => l10n('Author'),
      $url.'&amp;order=id' => l10n('Id'),
    )
  );

$template->assign('selected', $url.'&amp;order='.$selected_order);

switch ($selected_order)
{
  case 'name':
    uasort($fs_plugins, 'name_compare');
    break;
  case 'id':
    uksort($fs_plugins, 'strcasecmp');
    break;
  case 'author':
    uasort($fs_plugins, 'plugin_author_compare');
    break;
  case 'status':
    $fs_plugins = sort_plugins_by_state($fs_plugins, $db_plugins_by_id);
    break;
}


//--------------------------------------------------------------display plugins

$url = $base_url.get_query_string_diff( array('action', 'plugin') );

foreach($fs_plugins as $plugin_id => $fs_plugin)
{
  $display_name = $fs_plugin['name'];
  if (!empty($fs_plugin['uri']))
  {
    $display_name = '<a href="' . $fs_plugin['uri']
                    . '" onclick="window.open(this.href); return false;">'
                    . $display_name . '</a>';
  }
  $desc = $fs_plugin['description'];
  if (!empty($fs_plugin['author']))
  {
    $desc .= ' (<em>';
    if (!empty($fs_plugin['author uri']))
    {
      $desc .= '<a href="' . $fs_plugin['author uri'] . '">'
               . $fs_plugin['author'] . '</a>';
    }
    else
    {
      $desc .= $fs_plugin['author'];
    }
    $desc .= '</em>)';
  }
  $tpl_plugin =
    array('NAME' => $display_name,
          'VERSION' => $fs_plugin['version'],
          'DESCRIPTION' => $desc);

  $action_url = $url.'&amp;plugin='.$plugin_id;

  if (isset($db_plugins_by_id[$plugin_id]))
  {
    switch ($db_plugins_by_id[$plugin_id]['state'])
    {
      case 'active':
        $tpl_plugin['actions'][] =
            array('U_ACTION' => $action_url . '&amp;action=deactivate',
                  'L_ACTION' => l10n('Deactivate'));
        break;

      case 'inactive':
        $tpl_plugin['actions'][] =
            array('U_ACTION' => $action_url . '&amp;action=activate',
                  'L_ACTION' => l10n('Activate'));
        $tpl_plugin['actions'][] =
            array('U_ACTION' => $action_url . '&amp;action=uninstall',
                  'L_ACTION' => l10n('Uninstall'),
                  'CONFIRM' => l10n('Are you sure?'));
        break;
    }
  }
  else
  {
    $tpl_plugin['actions'][] =
        array('U_ACTION' => $action_url . '&amp;action=install',
              'L_ACTION' => l10n('Install'),
              'CONFIRM' => l10n('Are you sure?'));
    $tpl_plugin['actions'][] =
        array('U_ACTION' => $action_url . '&amp;action=delete',
                'L_ACTION' => l10n('plugins_delete'),
                'CONFIRM' => l10n('plugins_confirm_delete'));
  }
  $template->append('plugins', $tpl_plugin);
}

$missing_plugin_ids = array_diff(
    array_keys($db_plugins_by_id), array_keys($fs_plugins)
    );

foreach($missing_plugin_ids as $plugin_id)
{
  $action_url = $url.'&amp;plugin='.$plugin_id;

  $template->append( 'plugins',
      array(
        'NAME' => $plugin_id,
        'VERSION' => $db_plugins_by_id[$plugin_id]['version'],
        'DESCRIPTION' => "ERROR: THIS PLUGIN IS MISSING BUT IT IS INSTALLED! UNINSTALL IT NOW !",
        'actions' => array ( array (
              'U_ACTION' => $action_url . '&amp;action=uninstall',
              'L_ACTION' => l10n('Uninstall'),
          ) )
        )
     );
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>