<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

// display mode
$plugin_display = isset($_GET['plugin_display']) ? $_GET['plugin_display'] : (pwg_get_session_var('plugin_display') != null ? pwg_get_session_var('plugin_display') : 'compact');
pwg_set_session_var('plugin_display', $plugin_display);

$base_url = get_root_url().'admin.php?page='.$page['page'];
$pwg_token = get_pwg_token();
$action_url = $base_url.'&amp;plugin='.'%s'.'&amp;pwg_token='.$pwg_token;

$plugins = new plugins();

//--------------------------------------------------perform requested actions
if (isset($_GET['action']) and isset($_GET['plugin']))
{
  if (in_array($_GET['action'], array('install', 'uninstall')) AND !is_webmaster())
  {
    array_push($page['errors'], l10n('Webmaster status is required.'));
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

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+

$plugins->sort_fs_plugins('name');
$plugins->get_merged_extensions();
$plugins->get_incompatible_plugins();
$merged_plugins = false;
$tpl_plugins = array();

foreach($plugins->fs_plugins as $plugin_id => $fs_plugin)
{
  if (isset($_SESSION['incompatible_plugins'][$plugin_id])
    and $fs_plugin['version'] != $_SESSION['incompatible_plugins'][$plugin_id])
  {
    // Incompatible plugins must be reinitilized
    $plugins->get_incompatible_plugins(true);
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
    'INCOMPATIBLE' => isset($_SESSION['incompatible_plugins'][$plugin_id]),
    );

  if (isset($plugins->db_plugins_by_id[$plugin_id]))
  {
    $tpl_plugin['STATE'] = $plugins->db_plugins_by_id[$plugin_id]['state'];
  }
  else
  {
    $tpl_plugin['STATE'] = 'inactive';
  }

  if (isset($fs_plugin['extension']) and in_array($fs_plugin['extension'], $_SESSION['merged_extensions']))
  {
    $plugins->perform_action('uninstall', $plugin_id);
    $tpl_plugin['STATE'] = 'merged';
    $tpl_plugin['DESC'] = l10n('THIS PLUGIN IS NOW PART OF PIWIGO CORE! DELETE IT NOW.');
    $merged_plugins = true;
  }

  array_push($tpl_plugins, $tpl_plugin);
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
  foreach($missing_plugin_ids as $plugin_id)
  {
    array_push(
      $tpl_plugins,
      array(
        'NAME' => $plugin_id,
        'VERSION' => $plugins->db_plugins_by_id[$plugin_id]['version'],
        'DESC' => l10n('ERROR: THIS PLUGIN IS MISSING BUT IT IS INSTALLED! UNINSTALL IT NOW.'),
        'U_ACTION' => sprintf($action_url, $plugin_id),
        'STATE' => 'missing',
        )
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

$template->assign(array(
  'plugin_display' => $plugin_display,
  'plugins' => $tpl_plugins,
  'PWG_TOKEN' => $pwg_token,
));

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>