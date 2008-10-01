<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

$template->set_filenames(array('plugins' => 'plugins_list.tpl'));

$order = isset($_GET['order']) ? $_GET['order'] : 'name';
$base_url = get_root_url().'admin.php?page='.$page['page'].'&amp;order='.$order;

$plugins = new plugins();

//--------------------------------------------------perform requested actions
if (isset($_GET['action']) and isset($_GET['plugin']) and !is_adviser())
{
  $page['errors'] =
    $plugins->perform_action($_GET['action'], $_GET['plugin']);

  if (empty($page['errors'])) redirect($base_url);
}

//--------------------------------------------------------------------Tabsheet
set_plugins_tabsheet($page['page']);

//---------------------------------------------------------------Order options
$link = get_root_url().'admin.php?page='.$page['page'].'&amp;order=';
$template->assign('order_options',
  array(
    $link.'name' => l10n('Name'),
    $link.'status' => l10n('Status'),
    $link.'author' => l10n('Author'),
    $link.'id' => 'Id'));
$template->assign('order_selected', $link.$order);

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$plugins->sort_fs_plugins($order);

foreach($plugins->fs_plugins as $plugin_id => $fs_plugin)
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
          'DESCRIPTION' => $desc,
          'U_ACTION' => $base_url.'&amp;plugin='.$plugin_id);

  if (isset($plugins->db_plugins_by_id[$plugin_id]))
  {
    $tpl_plugin['STATE'] = $plugins->db_plugins_by_id[$plugin_id]['state'];
  }
  else
  {
    $tpl_plugin['STATE'] = 'uninstalled';
  }
  $template->append('plugins', $tpl_plugin);
}

$missing_plugin_ids = array_diff(
    array_keys($plugins->db_plugins_by_id), array_keys($plugins->fs_plugins)
    );

foreach($missing_plugin_ids as $plugin_id)
{
  $action_url = $base_url.'&amp;plugin='.$plugin_id;

  $template->append( 'plugins',
      array(
        'NAME' => $plugin_id,
        'VERSION' => $plugins->db_plugins_by_id[$plugin_id]['version'],
        'DESCRIPTION' => "ERROR: THIS PLUGIN IS MISSING BUT IT IS INSTALLED! UNINSTALL IT NOW !",
        'U_ACTION' => $base_url.'&amp;plugin='.$plugin_id,
        'STATE' => 'missing'
      )
    );
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>