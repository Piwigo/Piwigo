<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

$template->set_filenames(array('plugins' => 'plugins_new.tpl'));

$order = isset($_GET['order']) ? $_GET['order'] : 'date';
$base_url = get_root_url().'admin.php?page='.$page['page'].'&order='.$order;

$plugins = new plugins();

//------------------------------------------------------automatic installation
if (isset($_GET['revision']) and isset($_GET['extension']) and !is_adviser())
{
  check_pwg_token();
  
  $install_status = $plugins->extract_plugin_files('install', $_GET['revision'], $_GET['extension']);

  redirect($base_url.'&installstatus='.$install_status);
}

//--------------------------------------------------------------install result
if (isset($_GET['installstatus']))
{
  switch ($_GET['installstatus'])
  {
    case 'ok':
      array_push($page['infos'],
        l10n('plugins_install_ok'),
        l10n('plugins_install_need_activate'));
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
        sprintf(l10n('plugins_extract_error'), $_GET['installstatus']),
        l10n('plugins_check_chmod'));
  }  
}

//--------------------------------------------------------------------Tabsheet
set_plugins_tabsheet($page['page']);

//---------------------------------------------------------------Order options
$link = get_root_url().'admin.php?page='.$page['page'].'&amp;order=';
$template->assign('order_options',
  array(
    $link.'date' => l10n('Post date'),
    $link.'revision' => l10n('plugins_revisions'),
    $link.'name' => l10n('Name'),
    $link.'author' => l10n('Author'),
    $link.'downloads' => l10n('Number of downloads')));
$template->assign('order_selected', $link.$order);

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
if ($plugins->get_server_plugins(true))
{
  $plugins->sort_server_plugins($order);

  foreach($plugins->server_plugins as $plugin)
  {
    list($date, ) = explode(' ', $plugin['revision_date']);

    $ext_desc = '<i>'.l10n('Downloads').':</i> '.$plugin['extension_nb_downloads']."\r\n"
      ."\r\n"
      .$plugin['extension_description'];

    $rev_desc = '<i>'.l10n('Version').':</i> '.$plugin['revision_name']."\r\n"
      .'<i>'.l10n('Released on').':</i> '.$date."\r\n"
      .'<i>'.l10n('Downloads').':</i> '.$plugin['revision_nb_downloads']."\r\n"
      ."\r\n"
      .$plugin['revision_description'];

    $url_auto_install = htmlentities($base_url)
      . '&amp;revision=' . $plugin['revision_id']
      . '&amp;extension=' . $plugin['extension_id']
      . '&amp;pwg_token='.get_pwg_token()
    ;

    $template->append('plugins', array(
      'EXT_NAME' => $plugin['extension_name'],
      'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$plugin['extension_id'],
      'EXT_DESC' => $ext_desc,
      'VERSION' => $plugin['revision_name'],
      'DATE' => $date,
      'VER_DESC' => $rev_desc,
      'AUTHOR' => $plugin['author_name'],
      'URL_INSTALL' => $url_auto_install,
      'URL_DOWNLOAD' => $plugin['download_url'] . '&amp;origin=piwigo_download'));
  }
}
else
{
  array_push($page['errors'], l10n('plugins_server_error'));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>