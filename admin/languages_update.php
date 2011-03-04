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

include_once(PHPWG_ROOT_PATH.'admin/include/languages.class.php');

$base_url = get_root_url().'admin.php?page='.$page['page'];

$languages = new languages();

$languages->set_tabsheet($page['page']);

//-----------------------------------------------------------automatic upgrade
if (isset($_GET['language']) and isset($_GET['revision']))
{
  if (!is_webmaster())
  {
    array_push($page['errors'], l10n('Webmaster status is required.'));
  }
  else
  {
    check_pwg_token();
    
    $language_id = $_GET['language'];
    $revision = $_GET['revision'];

    $upgrade_status = $languages->extract_language_files('upgrade', $revision, $language_id);

    switch ($upgrade_status)
    {
      case 'ok':
        array_push($page['infos'],
           sprintf(
              l10n('%s has been successfully upgraded.'),
              $languages->fs_languages[$_GET['language']]['name']));
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

    $languages->languages();
    $template->delete_compiled_templates();
  }
}

// +-----------------------------------------------------------------------+
// |                     start template output                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('languages' => 'languages_update.tpl'));

if ($languages->get_server_languages())
{
  foreach($languages->fs_languages as $language_id => $fs_language)
  {
    if (isset($fs_language['extension'])
      and isset($languages->server_languages[$fs_language['extension']]))
    {
      $language_info = $languages->server_languages[$fs_language['extension']];

      if (!$languages->language_version_compare($fs_language['version'], $language_info['revision_name']))
      {
        $url_auto_update = $base_url
          . '&amp;revision=' . $language_info['revision_id']
          . '&amp;language=' . $language_id
          . '&amp;pwg_token='.get_pwg_token()
          ;

        $template->append('update_languages', array(
          'ID' => $language_info['extension_id'],
          'EXT_NAME' => $fs_language['name'],
          'EXT_URL' => PEM_URL.'/extension_view.php?eid='.$language_info['extension_id'],
          'EXT_DESC' => trim($language_info['extension_description'], " \n\r"),
          'REV_DESC' => trim($language_info['revision_description'], " \n\r"),
          'CURRENT_VERSION' => $fs_language['version'],
          'NEW_VERSION' => $language_info['revision_name'],
          'AUTHOR' => $language_info['author_name'],
          'DOWNLOADS' => $language_info['extension_nb_downloads'],
          'URL_UPDATE' => $url_auto_update,
          'URL_DOWNLOAD' => $language_info['download_url'] . '&amp;origin=piwigo_download'
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

$template->assign_var_from_handle('ADMIN_CONTENT', 'languages');
?>