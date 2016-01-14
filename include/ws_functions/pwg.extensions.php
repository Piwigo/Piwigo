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

/**
 * API method
 * Returns the list of all plugins
 * @param mixed[] $params
 */
function ws_plugins_getList($params, $service)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

  $plugins = new plugins();
  $plugins->sort_fs_plugins('name');
  $plugin_list = array();

  foreach ($plugins->fs_plugins as $plugin_id => $fs_plugin)
  {
    if (isset($plugins->db_plugins_by_id[$plugin_id]))
    {
      $state = $plugins->db_plugins_by_id[$plugin_id]['state'];
    }
    else
    {
      $state = 'uninstalled';
    }

    $plugin_list[] = array(
      'id' => $plugin_id,
      'name' => $fs_plugin['name'],
      'version' => $fs_plugin['version'],
      'state' => $state,
      'description' => $fs_plugin['description'],
      );
  }

  return $plugin_list;
}

/**
 * API method
 * Performs an action on a plugin
 * @param mixed[] $params
 *    @option string action
 *    @option string plugin
 *    @option string pwg_token
 */
function ws_plugins_performAction($params, $service)
{
  global $template;

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  define('IN_ADMIN', true);
  include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

  $plugins = new plugins();
  $errors = $plugins->perform_action($params['action'], $params['plugin']);

  if (!empty($errors))
  {
    return new PwgError(500, $errors);
  }
  else
  {
    if (in_array($params['action'], array('activate', 'deactivate')))
    {
      $template->delete_compiled_templates();
    }
    return true;
  }
}

/**
 * API method
 * Performs an action on a theme
 * @param mixed[] $params
 *    @option string action
 *    @option string theme
 *    @option string pwg_token
 */
function ws_themes_performAction($params, $service)
{
  global $template;

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  define('IN_ADMIN', true);
  include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');

  $themes = new themes();
  $errors = $themes->perform_action($params['action'], $params['theme']);

  if (!empty($errors))
  {
    return new PwgError(500, $errors);
  }
  else
  {
    if (in_array($params['action'], array('activate', 'deactivate')))
    {
      $template->delete_compiled_templates();
    }
    return true;
  }
}

/**
 * API method
 * Updates an extension
 * @param mixed[] $params
 *    @option string type
 *    @option string id
 *    @option string revision
 *    @option string pwg_token
 *    @option bool reactivate (optional - undocumented)
 */
function ws_extensions_update($params, $service)
{
  if (!is_webmaster())
  {
    return new PwgError(401, l10n('Webmaster status is required.'));
  }

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if (!in_array($params['type'], array('plugins', 'themes', 'languages')))
  {
    return new PwgError(403, "invalid extension type");
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  include_once(PHPWG_ROOT_PATH.'admin/include/'.$params['type'].'.class.php');

  $type = $params['type'];
  $extension_id = $params['id'];
  $revision = $params['revision'];

  $extension = new $type();

  if ($type == 'plugins')
  {
    if (
      isset($extension->db_plugins_by_id[$extension_id])
      and $extension->db_plugins_by_id[$extension_id]['state'] == 'active'
      )
    {
      $extension->perform_action('deactivate', $extension_id);

      redirect(PHPWG_ROOT_PATH
        . 'ws.php'
        . '?method=pwg.extensions.update'
        . '&type=plugins'
        . '&id=' . $extension_id
        . '&revision=' . $revision
        . '&reactivate=true'
        . '&pwg_token=' . get_pwg_token()
        . '&format=json'
      );
    }

    list($upgrade_status) = $extension->perform_action('update', $extension_id, array('revision'=>$revision));
    $extension_name = $extension->fs_plugins[$extension_id]['name'];

    if (isset($params['reactivate']))
    {
      $extension->perform_action('activate', $extension_id);
    }
  }
  else if ($type == 'themes')
  {
    $upgrade_status = $extension->extract_theme_files('upgrade', $revision, $extension_id);
    $extension_name = $extension->fs_themes[$extension_id]['name'];
  }
  else if ($type == 'languages')
  {
    $upgrade_status = $extension->extract_language_files('upgrade', $revision, $extension_id);
    $extension_name = $extension->fs_languages[$extension_id]['name'];
  }

  global $template;
  $template->delete_compiled_templates();

  switch ($upgrade_status)
  {
    case 'ok':
      return l10n('%s has been successfully updated.', $extension_name);

    case 'temp_path_error':
      return new PwgError(null, l10n('Can\'t create temporary file.'));

    case 'dl_archive_error':
      return new PwgError(null, l10n('Can\'t download archive.'));

    case 'archive_error':
      return new PwgError(null, l10n('Can\'t read or extract archive.'));

    default:
      return new PwgError(null, l10n('An error occured during extraction (%s).', $upgrade_status));
  }
}

/**
 * API method
 * Ignore an update
 * @param mixed[] $params
 *    @option string type (optional)
 *    @option string id (optional)
 *    @option bool reset
 *    @option string pwg_token
 */
function ws_extensions_ignoreupdate($params, $service)
{
  global $conf;

  define('IN_ADMIN', true);
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  if (!is_webmaster())
  {
    return new PwgError(401, 'Access denied');
  }

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $conf['updates_ignored'] = unserialize($conf['updates_ignored']);

  // Reset ignored extension
  if ($params['reset'])
  {
    if (!empty($params['type']) and isset($conf['updates_ignored'][ $params['type'] ]))
    {
      $conf['updates_ignored'][$params['type']] = array();
    }
    else
    {
      $conf['updates_ignored'] = array(
        'plugins'=>array(),
        'themes'=>array(),
        'languages'=>array()
      );
    }

    conf_update_param('updates_ignored', pwg_db_real_escape_string(serialize($conf['updates_ignored'])));
    unset($_SESSION['extensions_need_update']);
    return true;
  }

  if (empty($params['id']) or empty($params['type']) or !in_array($params['type'], array('plugins', 'themes', 'languages')))
  {
    return new PwgError(403, 'Invalid parameters');
  }

  // Add or remove extension from ignore list
  if (!in_array($params['id'], $conf['updates_ignored'][ $params['type'] ]))
  {
    $conf['updates_ignored'][ $params['type'] ][] = $params['id'];
  }

  conf_update_param('updates_ignored', pwg_db_real_escape_string(serialize($conf['updates_ignored'])));
  unset($_SESSION['extensions_need_update']);
  return true;
}

/**
 * API method
 * Checks for updates (core and extensions)
 * @param mixed[] $params
 */
function ws_extensions_checkupdates($params, $service)
{
  global $conf;

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  include_once(PHPWG_ROOT_PATH.'admin/include/updates.class.php');

  $update = new updates();
  $result = array();

  if (!isset($_SESSION['need_update']))
  {
    $update->check_piwigo_upgrade();
  }

  $result['piwigo_need_update'] = $_SESSION['need_update'];

  $conf['updates_ignored'] = unserialize($conf['updates_ignored']);

  if (!isset($_SESSION['extensions_need_update']))
  {
    $update->check_extensions();
  }
  else
  {
    $update->check_updated_extensions();
  }

  if (!is_array($_SESSION['extensions_need_update']))
  {
    $result['ext_need_update'] = null;
  }
  else
  {
    $result['ext_need_update'] = !empty($_SESSION['extensions_need_update']);
  }

  return $result;
}

?>