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
      $_GET['extension']
      );
    
    redirect($base_url.'&installstatus='.$install_status);
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
        'thumbnail' => PEM_URL.'/upload/extension-'.$theme['extension_id'].'/thumbnail.jpg',
        'screenshot' => PEM_URL.'/upload/extension-'.$theme['extension_id'].'/screenshot.jpg',
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
  get_root_url().'admin/themes/'.$conf['admin_theme'].'/images/missing_screenshot.png'
);

$template->assign_var_from_handle('ADMIN_CONTENT', 'themes');
?>