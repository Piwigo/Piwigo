<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

$template->set_filenames(array('plugins' => 'plugins_installed.tpl'));

// should we display details on plugins?
if (isset($_GET['show_details']))
{
  if (1 == $_GET['show_details'])
  {
    $show_details = true;
  }
  else
  {
    $show_details = false;
  }

  pwg_set_session_var('plugins_show_details', $show_details);
}
elseif (null != pwg_get_session_var('plugins_show_details'))
{
  $show_details = pwg_get_session_var('plugins_show_details');
}
else
{
  $show_details = false;
}

$base_url = get_root_url().'admin.php?page='.$page['page'];
$pwg_token = get_pwg_token();
$action_url = $base_url.'&amp;plugin='.'%s'.'&amp;pwg_token='.$pwg_token;

$plugins = new plugins();

//--------------------------------------------------perform requested actions
if (isset($_GET['action']) and isset($_GET['plugin']))
{
  if (!is_webmaster())
  {
    $page['errors'][] = l10n('Webmaster status is required.');
  }
  else
  {
    check_pwg_token();

    $page['errors'] = $plugins->perform_action($_GET['action'], $_GET['plugin']);

    if (empty($page['errors']))
    {
      if ($_GET['action'] == 'activate' or $_GET['action'] == 'deactivate')
      {
        $template->delete_compiled_templates();
      }
      redirect($base_url);
    }
  }
}

//--------------------------------------------------------Incompatible Plugins
if (isset($_GET['incompatible_plugins']))
{
  $incompatible_plugins = array();
  foreach ($plugins->get_incompatible_plugins() as $plugin => $version)
  {
    if ($plugin == '~~expire~~') continue;
    $incompatible_plugins[] = $plugin;
    
  }
  echo json_encode($incompatible_plugins);
  exit;
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+

$plugins->sort_fs_plugins('name');
$merged_extensions = $plugins->get_merged_extensions();
$merged_plugins = false;
$tpl_plugins = array();
$active_plugins = 0;

foreach($plugins->fs_plugins as $plugin_id => $fs_plugin)
{
  if (isset($_SESSION['incompatible_plugins'][$plugin_id])
    and $fs_plugin['version'] != $_SESSION['incompatible_plugins'][$plugin_id])
  {
    // Incompatible plugins must be reinitilized
    unset($_SESSION['incompatible_plugins']);
  }

  $tpl_plugin = array(
    'ID' => $plugin_id,
    'NAME' => $fs_plugin['name'],
    'VISIT_URL' => $fs_plugin['uri'],
    'VERSION' => $fs_plugin['version'],
    'DESC' => $fs_plugin['description'],
    'AUTHOR' => $fs_plugin['author'],
    'AUTHOR_URL' => @$fs_plugin['author uri'],
    'U_ACTION' => sprintf($action_url, $plugin_id),
    );

  if (isset($plugins->db_plugins_by_id[$plugin_id]))
  {
    $tpl_plugin['STATE'] = $plugins->db_plugins_by_id[$plugin_id]['state'];
  }
  else
  {
    $tpl_plugin['STATE'] = 'inactive';
  }

  if (isset($fs_plugin['extension']) and isset($merged_extensions[$fs_plugin['extension']]))
  {
    // Deactivate manually plugin from database
    $query = 'UPDATE '.PLUGINS_TABLE.' SET state=\'inactive\' WHERE id=\''.$plugin_id.'\'';
    pwg_query($query);

    $tpl_plugin['STATE'] = 'merged';
    $tpl_plugin['DESC'] = l10n('THIS PLUGIN IS NOW PART OF PIWIGO CORE! DELETE IT NOW.');
    $merged_plugins = true;
  }
  
  if ($tpl_plugin['STATE'] == 'active')
  {
    $active_plugins++;
  }

  $tpl_plugins[] = $tpl_plugin;
}

$template->append('plugin_states', 'active');
$template->append('plugin_states', 'inactive');

if ($merged_plugins)
{
  $template->append('plugin_states', 'merged');
}

$missing_plugin_ids = array_diff(
  array_keys($plugins->db_plugins_by_id),
  array_keys($plugins->fs_plugins)
  );

if (count($missing_plugin_ids) > 0)
{
  foreach ($missing_plugin_ids as $plugin_id)
  {
    $tpl_plugins[] = array(
      'NAME' => $plugin_id,
      'VERSION' => $plugins->db_plugins_by_id[$plugin_id]['version'],
      'DESC' => l10n('ERROR: THIS PLUGIN IS MISSING BUT IT IS INSTALLED! UNINSTALL IT NOW.'),
      'U_ACTION' => sprintf($action_url, $plugin_id),
      'STATE' => 'missing',
      );
  }
  $template->append('plugin_states', 'missing');
}

// sort plugins by state then by name
function cmp($a, $b)
{ 
  $s = array('merged' => 0, 'missing' => 1, 'active' => 2, 'inactive' => 3);
  
  if($a['STATE'] == $b['STATE'])
    return strcasecmp($a['NAME'], $b['NAME']); 
  else
    return $s[$a['STATE']] >= $s[$b['STATE']]; 
}
usort($tpl_plugins, 'cmp');

$template->assign(
  array(
    'plugins' => $tpl_plugins,
    'active_plugins' => $active_plugins,
    'PWG_TOKEN' => $pwg_token,
    'base_url' => $base_url,
    'show_details' => $show_details,
    )
  );

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>