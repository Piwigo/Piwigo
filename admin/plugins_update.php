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

include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

$template->set_filenames(array('plugins' => 'admin/plugins_update.tpl'));

$plugins = new plugins($page['page']);

//-----------------------------------------------------------automatic upgrade
if (isset($_GET['upgrade']) and isset($_GET['plugin']) and !is_adviser())
{
  $plugins->upgrade($_GET['upgrade'], $_GET['plugin']);
}

//--------------------------------------------------------------upgrade result
if (isset($_GET['upgradestatus']) and isset($_GET['plugin']))
{
  $plugins->get_result($_GET['upgradestatus'], $_GET['plugin']);
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$plugins->tabsheet();
$plugins->check_server_plugins();

if ($plugins->server_plugins !== false)
{
  foreach($plugins->fs_plugins as $plugin_id => $fs_plugin)
  {
    if (isset($fs_plugin['extension'])
      and isset($plugins->server_plugins[$fs_plugin['extension']]))
    {
      $plugin_info = $plugins->server_plugins[$fs_plugin['extension']];

      $ext_desc = nl2br(htmlspecialchars(strip_tags(
                    utf8_encode($plugin_info['ext_description']))));

      $ver_desc = sprintf(l10n('plugins_description'),
          $plugin_info['version'],
          date('Y-m-d', $plugin_info['date']),
          nl2br(htmlspecialchars(strip_tags(
              utf8_encode($plugin_info['description'])))));

      if ($plugins->plugin_version_compare($fs_plugin, $plugin_info) >= 0)
      {
        // Plugin is up to date
        $template->append('plugins_uptodate',
              array('URL' => $fs_plugin['uri'],
                'NAME' => $fs_plugin['name'],
                'EXT_DESC' => $ext_desc,
                'VERSION' => $fs_plugin['version']));
      }
      else
      {
        // Plugin need upgrade
        $url_auto_update = $plugins->html_base_url
          . '&amp;plugin=' . $plugin_id
          . '&amp;upgrade=%2Fupload%2Fextension-' . $fs_plugin['extension']
          . '%2Frevision-' . $plugin_info['id_revision']
          . '%2F' . str_replace(' ', '%20',$plugin_info['url']);

        $url_download = PEM_URL.'/upload/extension-'. $fs_plugin['extension']
          . '/revision-' . $plugin_info['id_revision']
          . '/' . str_replace(' ', '%20',$plugin_info['url']);

        $template->append('plugins_not_uptodate',
          array('EXT_NAME' => $fs_plugin['name'],
            'EXT_URL' => $fs_plugin['uri'],
            'EXT_DESC' => $ext_desc,
            'VERSION' => $fs_plugin['version'],
            'VERSION_URL' => PEM_URL.'/revision_view.php?rid='.$plugin_info['id_revision'],
            'NEW_VERSION' => $plugin_info['version'],
            'NEW_VER_DESC' => $ver_desc,
            'URL_UPDATE' => $url_auto_update,
            'URL_DOWNLOAD' => $url_download));
      }
    }
    else
    {
      // Can't check plugin
      $template->append('plugins_cant_check',
          array('NAME' => $fs_plugin['name'],
            'VERSION' => $fs_plugin['version']));
    }
  }
}
else
{
  array_push($page['errors'], l10n('plugins_server_error'));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>