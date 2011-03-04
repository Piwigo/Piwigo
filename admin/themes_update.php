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

include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');

$base_url = get_root_url().'admin.php?page='.$page['page'];

$themes = new themes();

$themes->set_tabsheet($page['page']);

//-----------------------------------------------------------automatic upgrade
if (isset($_GET['theme']) and isset($_GET['revision']))
{
  if (!is_webmaster())
  {
    array_push($page['errors'], l10n('Webmaster status is required.'));
  }
  else
  {
    check_pwg_token();
    
    $theme_id = $_GET['theme'];
    $revision = $_GET['revision'];

    $upgrade_status = $themes->extract_theme_files('upgrade', $revision, $theme_id);

    switch ($upgrade_status)
    {
      case 'ok':
        array_push($page['infos'],
           sprintf(
              l10n('%s has been successfully upgraded.'),
              $themes->fs_themes[$_GET['theme']]['name']));
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
          sprintf(l10n('An error occured during extraction (%s).'), $upgrade_status)
        );
    }

    $themes->themes();
    $template->delete_compiled_templates();
  }
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('themes' => 'themes_update.tpl'));

if ($themes->get_server_themes())
{
  foreach($themes->fs_themes as $theme_id => $fs_theme)
  {
    if (isset($fs_theme['extension'])
      and isset($themes->server_themes[$fs_theme['extension']]))
    {
      $theme_info = $themes->server_themes[$fs_theme['extension']];

      if (!$themes->theme_version_compare($fs_theme['version'], $theme_info['revision_name']))
      {
        $url_auto_update = $base_url
          . '&amp;revision=' . $theme_info['revision_id']
          . '&amp;theme=' . $theme_id
          . '&amp;pwg_token='.get_pwg_token()
          ;

        $template->append('update_themes', array(
          'ID' => $theme_info['extension_id'],
          'EXT_NAME' => $fs_theme['name'],
          'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$theme_info['extension_id'],
          'EXT_DESC' => trim($theme_info['extension_description'], " \n\r"),
          'REV_DESC' => trim($theme_info['revision_description'], " \n\r"),
          'CURRENT_VERSION' => $fs_theme['version'],
          'NEW_VERSION' => $theme_info['revision_name'],
          'AUTHOR' => $theme_info['author_name'],
          'DOWNLOADS' => $theme_info['extension_nb_downloads'],
          'URL_UPDATE' => $url_auto_update,
          'URL_DOWNLOAD' => $theme_info['download_url'] . '&amp;origin=piwigo_download'
          )
        );
      }
    }
  }
}
else
{
  $template->assign('SERVER_ERROR', true);
  array_push($page['errors'], l10n('Can\'t connect to server.'));
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'themes');
?>