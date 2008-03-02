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

$template->set_filenames(array('plugins' => 'admin/plugins_update.tpl'));


//-----------------------------------------------------------automatic upgrade
if (isset($_GET['upgrade']) and isset($_GET['plugin']) and !is_adviser())
{
  include(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');

  $upgrade_status  = extract_plugin_files('upgrade',
                      $_GET['upgrade'],
                      $_GET['plugin']);

  $my_base_url .= isset($_GET['reactivate']) ? '&action=activate' : '';

  redirect($my_base_url.'&plugin='.$_GET['plugin'].'&upgradestatus='.$upgrade_status);
}


//--------------------------------------------------------------upgrade result
if (isset($_GET['upgradestatus']) and isset($_GET['plugin']))
{
  switch ($_GET['upgradestatus'])
  {
  case 'ok':
    array_push($page['infos'],
               sprintf(l10n('plugins_upgrade_ok'),
               $fs_plugins[$_GET['plugin']]['name']));
    break;

  case 'temp_path_error':
    array_push($page['errors'], l10n('plugins_temp_path_error'));  
    break;
  
  case 'dl_archive_error':
    array_push($page['errors'], l10n('plugins_dl_archive_error'));  
    break;

  case 'archive_error':
    array_push($page['errors'], l10n('plugins_archive_error'));  
    break;

  default:
    array_push($page['errors'],
               sprintf(l10n('plugins_extract_error'),
               $_GET['upgradestatus']));  
  }
}


// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$plugins_infos = check_server_plugins();

if ($plugins_infos !== false)
{
  foreach($fs_plugins as $plugin_id => $fs_plugin)
  {
    if (isset($fs_plugin['extension'])
      and isset($plugins_infos[$fs_plugin['extension']]))
    {
      $plugin_info = $plugins_infos[$fs_plugin['extension']];
      
      $ext_desc = nl2br(htmlspecialchars(strip_tags(
                    utf8_encode($plugin_info['ext_description']))));
      
      $ver_desc = sprintf(l10n('plugins_description'),
              $plugin_info['version'],
              date('Y-m-d', $plugin_info['date']),
              nl2br(htmlspecialchars(strip_tags(
                utf8_encode($plugin_info['description'])))));
    
      if ($plugin_info['version'] == $fs_plugin['version'])
      {
        // Plugin is up to date
        $template->append('plugins_uptodate',
              array('URL' => $fs_plugin['uri'],
                'NAME' => $fs_plugin['name'],
                'EXT_DESC' => $ext_desc,
                'VERSION' => $fs_plugin['version'],
                'VER_DESC' => $ver_desc));
      }
      else
      {
        // Plugin need upgrade
        $url_auto_update = htmlentities($my_base_url)
          . '&amp;plugin=' . $plugin_id
          . (
              (isset($db_plugins_by_id[$plugin_id]) 
                and $db_plugins_by_id[$plugin_id]['state'] == 'active') ? 
                  '&amp;action=deactivate' : ''
            )
          . '&amp;upgrade=%2Fupload%2Fextension-' . $fs_plugin['extension']
          . '%2Frevision-' . $plugin_info['id_revision']
          . '%2F' . $plugin_info['url'];
        
        $url_download = PEM_URL.'/upload/extension-'. $fs_plugin['extension']
            . '/revision-' . $plugin_info['id_revision']
            . '/' . $plugin_info['url'];
              
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

?>