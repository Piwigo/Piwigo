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

$template->set_filenames(array('plugins' => 'plugins_new.tpl'));

$base_url = get_root_url().'admin.php?page='.$page['page'];

$plugins = new plugins();

//------------------------------------------------------automatic installation
if (isset($_GET['revision']) and isset($_GET['extension']))
{
  if (!is_webmaster())
  {
    array_push($page['errors'], l10n('Webmaster status is required.'));
  }
  else
  {
    check_pwg_token();
    
    $install_status = $plugins->extract_plugin_files('install', $_GET['revision'], $_GET['extension']);

    redirect($base_url.'&installstatus='.$install_status);
  }
}

//--------------------------------------------------------------install result
if (isset($_GET['installstatus']))
{
  switch ($_GET['installstatus'])
  {
    case 'ok':
      array_push($page['infos'],
        l10n('Plugin has been successfully copied'),
        l10n('You might go to plugin list to install and activate it.'));
      break;

    case 'temp_path_error':
      array_push($page['errors'], l10n('Can\'t create temporary file.'));
      break;

    case 'dl_archive_error':
      array_push($page['errors'], l10n('Can\'t download archive.'));
      break;

    case 'archive_error':
      array_push($page['errors'], l10n('Can\'t read or extract archive.'));
      break;

    default:
      array_push($page['errors'],
        sprintf(l10n('An error occured during extraction (%s).'), $_GET['installstatus']),
        l10n('Please check "plugins" folder and sub-folders permissions (CHMOD).'));
  }  
}

//--------------------------------------------------------------------Tabsheet
$plugins->set_tabsheet($page['page']);

//---------------------------------------------------------------Order options
$template->assign('order_options',
  array(
    'date' => l10n('Post date'),
    'revision' => l10n('Last revisions'),
    'name' => l10n('Name'),
    'author' => l10n('Author'),
    'downloads' => l10n('Number of downloads')));

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
if ($plugins->get_server_plugins(true))
{
  $plugins->sort_server_plugins('date');

  foreach($plugins->server_plugins as $plugin)
  {
    $ext_desc = trim($plugin['extension_description'], " \n\r");
    list($small_desc) = explode("\n", wordwrap($ext_desc, 200));

    $url_auto_install = htmlentities($base_url)
      . '&amp;revision=' . $plugin['revision_id']
      . '&amp;extension=' . $plugin['extension_id']
      . '&amp;pwg_token='.get_pwg_token()
    ;

    $template->append('plugins', array(
      'ID' => $plugin['extension_id'],
      'EXT_NAME' => $plugin['extension_name'],
      'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$plugin['extension_id'],
      'SMALL_DESC' => trim($small_desc, " \r\n"),
      'BIG_DESC' => $ext_desc,
      'VERSION' => $plugin['revision_name'],
      'REVISION_DATE' => preg_replace('/[^0-9]/', '', $plugin['revision_date']),
      'AUTHOR' => $plugin['author_name'],
      'DOWNLOADS' => $plugin['extension_nb_downloads'],
      'URL_INSTALL' => $url_auto_install,
      'URL_DOWNLOAD' => $plugin['download_url'] . '&amp;origin=piwigo_download'));
  }
}
else
{
  array_push($page['errors'], l10n('Can\'t connect to server.'));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>