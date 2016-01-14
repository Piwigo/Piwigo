<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

$conf['updates_ignored'] = unserialize($conf['updates_ignored']);

include_once(PHPWG_ROOT_PATH.'admin/include/updates.class.php');
$autoupdate = new updates($page['page']);

$show_reset = false;
if (!$autoupdate->get_server_extensions())
{
  $page['errors'][] = l10n('Can\'t connect to server.');
  return; // TODO: remove this return and add a proper "page killer"
}

foreach ($autoupdate->types as $type)
{
  $fs = 'fs_'.$type;
  $server = 'server_'.$type;
  $server_ext = $autoupdate->$type->$server;
  $fs_ext = $autoupdate->$type->$fs;

  if (empty($server_ext))
  {
    continue;
  }

  foreach($fs_ext as $ext_id => $fs_ext)
  {
    if (!isset($fs_ext['extension']) or !isset($server_ext[$fs_ext['extension']]))
    {
      continue;
    }

    $ext_info = $server_ext[$fs_ext['extension']];

    if (!safe_version_compare($fs_ext['version'], $ext_info['revision_name'], '>='))
    {
      $template->append('update_'.$type, array(
        'ID' => $ext_info['extension_id'],
        'REVISION_ID' => $ext_info['revision_id'],
        'EXT_ID' => $ext_id,
        'EXT_NAME' => $fs_ext['name'],
        'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$ext_info['extension_id'],
        'EXT_DESC' => trim($ext_info['extension_description'], " \n\r"),
        'REV_DESC' => trim($ext_info['revision_description'], " \n\r"),
        'CURRENT_VERSION' => $fs_ext['version'],
        'NEW_VERSION' => $ext_info['revision_name'],
        'AUTHOR' => $ext_info['author_name'],
        'DOWNLOADS' => $ext_info['extension_nb_downloads'],
        'URL_DOWNLOAD' => $ext_info['download_url'] . '&amp;origin=piwigo_download',
        'IGNORED' => in_array($ext_id, $conf['updates_ignored'][$type]),
        )
      );
    }
  }

  if (!empty($conf['updates_ignored'][$type]))
  {
    $show_reset = true;
  }
}

$template->assign('SHOW_RESET', $show_reset);
$template->assign('PWG_TOKEN', get_pwg_token());
$template->assign('EXT_TYPE', $page['page'] == 'updates' ? 'extensions' : $page['page']);
$template->set_filename('plugin_admin_content', 'updates_ext.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

?>