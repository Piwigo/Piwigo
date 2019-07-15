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

include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

$template->set_filenames(array('plugins' => 'plugins_new.tpl'));

$base_url = get_root_url().'admin.php?page='.$page['page'].'&tab='.$page['tab'];

$plugins = new plugins();

//------------------------------------------------------automatic installation
if (isset($_GET['revision']) and isset($_GET['extension']))
{
  if (!is_webmaster())
  {
    $page['errors'][] = l10n('Webmaster status is required.');
  }
  else
  {
    check_pwg_token();
    
    $install_status = $plugins->extract_plugin_files('install', $_GET['revision'], $_GET['extension'], $plugin_id);

    redirect($base_url.'&installstatus='.$install_status.'&plugin_id='.$plugin_id);
  }
}

//--------------------------------------------------------------install result
if (isset($_GET['installstatus']))
{
  switch ($_GET['installstatus'])
  {
    case 'ok':
      $activate_url = get_root_url().'admin.php?page=plugins'
        . '&amp;plugin=' . $_GET['plugin_id']
        . '&amp;pwg_token=' . get_pwg_token()
        . '&amp;action=activate';

      $page['infos'][] = l10n('Plugin has been successfully copied');
      $page['infos'][] = '<a href="'. $activate_url . '">' . l10n('Activate it now') . '</a>';
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
      $page['errors'][] = l10n('An error occured during extraction (%s).', htmlspecialchars($_GET['installstatus']));
      $page['errors'][] = l10n('Please check "plugins" folder and sub-folders permissions (CHMOD).');
  }  
}

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
  /* order plugins */
  if (pwg_get_session_var('plugins_new_order') != null)
  {
    $order_selected = pwg_get_session_var('plugins_new_order');
    $plugins->sort_server_plugins($order_selected);
    $template->assign('order_selected', $order_selected);
  }
  else
  {
    $plugins->sort_server_plugins('date');
    $template->assign('order_selected', 'date');
  }

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
  $page['errors'][] = l10n('Can\'t connect to server.');
}

$template->assign_var_from_handle('ADMIN_CONTENT', 'plugins');
?>