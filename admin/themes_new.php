<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

if (!$conf['enable_extensions_install'])
{
  die('Piwigo extensions install/update system is disabled');
}

include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');

$base_url = get_root_url().'admin.php?page='.$page['page'].'&tab='.$page['tab'];

$themes = new themes();

// +-----------------------------------------------------------------------+
// |                           setup check                                 |
// +-----------------------------------------------------------------------+

$themes_dir = PHPWG_ROOT_PATH.'themes';
if (!is_writable($themes_dir))
{
  $page['errors'][] = l10n('Add write access to the "%s" directory', 'themes');
}

// +-----------------------------------------------------------------------+
// |                       perform installation                            |
// +-----------------------------------------------------------------------+

if (isset($_GET['revision']) and isset($_GET['extension']))
{
  if (!is_webmaster())
  {
    $page['errors'][] = l10n('Webmaster status is required.');
  }
  else
  {
    check_pwg_token();

    $install_status = $themes->extract_theme_files(
      'install',
      $_GET['revision'],
      $_GET['extension'],
      $theme_id
      );
    
    redirect($base_url.'&installstatus='.$install_status.'&theme_id='.$theme_id);
  }
}

// +-----------------------------------------------------------------------+
// |                        installation result                            |
// +-----------------------------------------------------------------------+

if (isset($_GET['installstatus']))
{
  switch ($_GET['installstatus'])
  {
    case 'ok':
      $page['infos'][] = l10n('Theme has been successfully installed');

      if (isset($themes->fs_themes[$_GET['theme_id']]))
      {
        pwg_activity(
          'system',
          ACTIVITY_SYSTEM_THEME,
          'install',
          array(
            'theme_id' => $_GET['theme_id'],
            'version' => $themes->fs_themes[$_GET['theme_id']]['version'],
          )
        );
      }
      break;

    case 'temp_path_error':
      $page['errors'][] = l10n('Can\'t create temporary file.');
      break;

    case 'dl_archive_error':
      $page['errors'][] = l10n('Can\'t download archive.');
      break;

    case 'archive_error':
      $page['errors'][] = l10n('Can\'t read or extract archive.');
      break;

    default:
      $page['errors'][] = l10n(
        'An error occured during extraction (%s).',
        htmlspecialchars($_GET['installstatus'])
        );
  }  
}

// +-----------------------------------------------------------------------+
// |                          template output                              |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('themes' => 'themes_new.tpl'));

if ($themes->get_server_themes(true)) // only new themes
{
  foreach($themes->server_themes as $theme)
  {
    $url_auto_install = htmlentities($base_url)
      . '&amp;revision=' . $theme['revision_id']
      . '&amp;extension=' . $theme['extension_id']
      . '&amp;pwg_token='.get_pwg_token()
      ;

    $template->append(
      'new_themes',
      array(
        'name' => $theme['extension_name'],
        'thumbnail' => (key_exists('thumbnail_src', $theme)) ? $theme['thumbnail_src']:'',
        'screenshot' => (key_exists('screenshot_url', $theme)) ? $theme['screenshot_url']:'',
        'install_url' => $url_auto_install,
        )
      );
  }
}
else
{
  $page['errors'][] = l10n('Can\'t connect to server.');
}

$template->assign('default_screenshot',
  get_root_url().'admin/themes/'.userprefs_get_param('admin_theme', 'clear').'/images/missing_screenshot.png'
);
$template->assign('ADMIN_PAGE_TITLE', l10n('Themes'));

$template->assign_var_from_handle('ADMIN_CONTENT', 'themes');
?>
