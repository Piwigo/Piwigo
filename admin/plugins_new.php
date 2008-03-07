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

$template->set_filenames(array('plugins' => 'admin/plugins_new.tpl'));

$order = isset($_GET['order']) ? $_GET['order'] : 'date';

$plugins = new plugins($page['page'], $order);

//------------------------------------------------------automatic installation
if (isset($_GET['install']) and isset($_GET['extension']) and !is_adviser())
{
  $plugins->install($_GET['install'], $_GET['extension']);
}

//--------------------------------------------------------------install result
if (isset($_GET['installstatus']))
{
  $plugins->get_result($_GET['installstatus']);
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$plugins->tabsheet();
$plugins->check_server_plugins();
$plugins->set_order_options(array(
    'date' => l10n('Post date'),
    'name' => l10n('Name'),
    'author' => l10n('Author')));

if ($plugins->server_plugins !== false)
{
  foreach($plugins->server_plugins as $plugin)
  {
    $ext_desc = nl2br(htmlspecialchars(strip_tags(
                  utf8_encode($plugin['ext_description']))));

    $ver_desc = sprintf(l10n('plugins_description'),
            $plugin['version'],
            date('Y-m-d', $plugin['date']),
            nl2br(htmlspecialchars(strip_tags(
              utf8_encode($plugin['description'])))));

    $url_auto_install = $plugins->html_base_url
      . '&amp;extension=' . $plugin['id_extension']
      . '&amp;install=%2Fupload%2Fextension-' . $plugin['id_extension']
      . '%2Frevision-' . $plugin['id_revision'] . '%2F'
      .  str_replace(' ', '%20',$plugin['url']);

    $url_download = PEM_URL .'/upload/extension-'.$plugin['id_extension']
      . '/revision-' . $plugin['id_revision']
      . '/' . str_replace(' ', '%20',$plugin['url']);

    $template->append('plugins',
        array('EXT_NAME' => $plugin['ext_name'],
          'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$plugin['id_extension'],
          'EXT_DESC' => $ext_desc,
          'VERSION' => $plugin['version'],
          'VERSION_URL' => PEM_URL.'/revision_view.php?rid='.$plugin['id_revision'],
          'VER_DESC' => $ver_desc,
          'AUTHOR' => $plugin['author'],
          'URL_INSTALL' => $url_auto_install,
          'URL_DOWNLOAD' => $url_download));
  }
}
else
{
  array_push($page['errors'], l10n('plugins_server_error'));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>