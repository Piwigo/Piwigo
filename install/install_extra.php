<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

if (!isset($conf['setup_step']) || !is_webmaster())
{
  die('Piwigo is already installed');
}

if (isset($_POST['skip_all']))
{
  check_pwg_token();
  setup_unlock();
  redirect(get_gallery_home_url());
}

load_language('admin.lang');
load_language('install.lang');

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

$setup_extensions = setup_get_recommended_extensions();

//-------------------------------------------------------------------- functions

/**
 * Ends the setup: it can not be opened again.
 */
function setup_unlock()
{
  unset($_SESSION['setup_server_extensions'], $_SESSION['setup_recommended_extensions']);
  conf_delete_param('setup_step');
}

/**
 * Asks piwigo.org for the recommended extensions, once per session.
 *
 * @return array themes and plugins, each one as piwigo.org id => directory name
 */
function setup_get_recommended_extensions()
{
  global $conf;

  if (isset($_SESSION['setup_recommended_extensions']))
  {
    return $_SESSION['setup_recommended_extensions'];
  }

  $extensions = array(
    'themes' => array(),
    'plugins' => array(),
    );

  $url = PHPWG_URL.'/download/all_versions.php';
  $url.= '?recommended_extensions&format=php';
  $url.= '&origin_hash='.sha1($conf['secret_key'].get_absolute_root_url());

  if (!@fetchRemote($url, $result))
  {
    return $extensions;
  }

  $recommended = @unserialize($result, array('allowed_classes' => false));
  if (!is_array($recommended))
  {
    return $extensions;
  }

  foreach (array_keys($extensions) as $type)
  {
    if (!isset($recommended[$type]) || !is_array($recommended[$type]))
    {
      continue;
    }

    foreach ($recommended[$type] as $extension_id => $directory)
    {
      if (is_int($extension_id) && is_string($directory))
      {
        $extensions[$type][$extension_id] = $directory;
      }
    }
  }

  if (count($extensions['themes']) + count($extensions['plugins']) > 0)
  {
    $_SESSION['setup_recommended_extensions'] = $extensions;
  }

  return $extensions;
}

/**
 * @param string $type themes or plugins
 * @return themes|plugins
 */
function setup_get_manager($type)
{
  return ('themes' == $type) ? new themes() : new plugins();
}

/**
 * @param themes|plugins $manager
 * @return array extensions found in the themes or plugins directory
 */
function setup_get_fs_extensions($manager)
{
  return ($manager instanceof themes) ? $manager->fs_themes : $manager->fs_plugins;
}

/**
 * Finds an extension on the disk, by the piwigo.org id of its header first,
 * then by its directory name.
 *
 * @param array $fs_extensions
 * @param string $extension_id id on piwigo.org
 * @param string $directory
 * @return string|null directory name of the extension, null when it is not on the disk
 */
function setup_get_local_id($fs_extensions, $extension_id, $directory)
{
  foreach ($fs_extensions as $local_id => $fs_extension)
  {
    if (isset($fs_extension['extension']) && $fs_extension['extension'] == $extension_id)
    {
      return $local_id;
    }
  }

  if (isset($fs_extensions[$directory]))
  {
    return $directory;
  }

  return null;
}

/**
 * Asks piwigo.org for the extensions of the setup, once per session.
 *
 * @param string $type themes or plugins
 * @param array $extensions piwigo.org id => directory name
 * @return array extension id => infos sent by piwigo.org, empty when it can not be reached
 */
function setup_get_server_extensions($type, $extensions)
{
  if (isset($_SESSION['setup_server_extensions'][$type]))
  {
    return $_SESSION['setup_server_extensions'][$type];
  }

  $manager = setup_get_manager($type);
  if ('themes' == $type)
  {
    $manager->get_server_themes(true);
    $manager->get_server_themes(false);
    $server_extensions = $manager->server_themes;
  }
  else
  {
    $manager->get_server_plugins(true);
    $manager->get_server_plugins(false);
    if (0 == count($manager->server_plugins))
    {
      $manager->get_server_plugins(true, true);
      $manager->get_server_plugins(false, true);
    }
    $server_extensions = $manager->server_plugins;
  }

  $server_extensions = array_intersect_key($server_extensions, $extensions);

  if (count($server_extensions) > 0)
  {
    $_SESSION['setup_server_extensions'][$type] = $server_extensions;
  }

  return $server_extensions;
}

/**
 * @param string $type themes or plugins
 * @param array $extensions piwigo.org id => directory name
 * @param array $server_extensions
 * @return array one card per extension, in the order of $extensions
 */
function setup_get_cards($type, $extensions, $server_extensions)
{
  $manager = setup_get_manager($type);
  $fs_extensions = setup_get_fs_extensions($manager);
  $default_theme = get_default_theme();

  $cards = array();
  foreach ($extensions as $extension_id => $directory)
  {
    $local_id = setup_get_local_id($fs_extensions, $extension_id, $directory);
    if (null === $local_id && !isset($server_extensions[$extension_id]))
    {
      continue;
    }

    $card = array(
      'id' => $extension_id,
      'name' => '',
      'description' => '',
      'screenshot' => '',
      'state' => 'remote',
      );

    if (isset($server_extensions[$extension_id]))
    {
      $server_extension = $server_extensions[$extension_id];
      $card['name'] = stripslashes($server_extension['extension_name']);
      $card['description'] = stripslashes($server_extension['extension_description']);
      $card['screenshot'] = $server_extension['screenshot_url'] ?? '';
    }

    if (null !== $local_id)
    {
      $fs_extension = $fs_extensions[$local_id];
      $card['name'] = $fs_extension['name'];
      $card['state'] = 'inactive';
      if ('' == $card['description'])
      {
        $card['description'] = $fs_extension['description'] ?? '';
      }

      if ('themes' == $type)
      {
        if ('' == $card['screenshot'])
        {
          $card['screenshot'] = $fs_extension['screenshot'];
        }
        if (isset($manager->db_themes_by_id[$local_id]))
        {
          $card['state'] = ($local_id == $default_theme) ? 'default' : 'active';
        }
      }
      else if (isset($manager->db_plugins_by_id[$local_id]) && 'active' == $manager->db_plugins_by_id[$local_id]['state'])
      {
        $card['state'] = 'active';
      }
    }

    $cards[] = $card;
  }

  return $cards;
}

/**
 * Downloads the extension when it is not on the disk, then activates it.
 * A theme also becomes the default theme.
 *
 * @param string $type themes or plugins
 * @param string $extension_id id on piwigo.org
 * @param array $extensions piwigo.org id => directory name
 * @return array errors, empty on success
 */
function setup_install_extension($type, $extension_id, $extensions)
{
  global $conf;

  $manager = setup_get_manager($type);
  $local_id = setup_get_local_id(setup_get_fs_extensions($manager), $extension_id, $extensions[$extension_id]);

  if (null === $local_id)
  {
    if (!$conf['enable_extensions_install'])
    {
      return array('Piwigo extensions install/update system is disabled');
    }

    $server_extensions = setup_get_server_extensions($type, $extensions);
    if (!isset($server_extensions[$extension_id]))
    {
      return array(l10n('an error happened'));
    }
    $revision = $server_extensions[$extension_id]['revision_id'];

    if ('themes' == $type)
    {
      $status = $manager->extract_theme_files('install', $revision, $extension_id, $local_id);
    }
    else
    {
      $status = $manager->extract_plugin_files('install', $revision, $extension_id, $local_id);
    }

    if ('ok' != $status)
    {
      return array(l10n('an error happened').' ('.$status.')');
    }

    $manager = setup_get_manager($type);
  }

  $errors = $manager->perform_action('activate', $local_id);

  if (empty($errors) && 'themes' == $type)
  {
    $errors = $manager->perform_action('set_default', $local_id);
  }

  return $errors;
}

//----------------------------------------------------------- install extension
if (isset($_POST['install_extension']))
{
  check_pwg_token();

  $type = (isset($_POST['type']) && 'themes' == $_POST['type']) ? 'themes' : 'plugins';
  $extension_id = $_POST['install_extension'];

  $errors = array(l10n('an error happened'));
  if (
    is_string($extension_id)
    && preg_match('/^\d+$/', $extension_id)
    && isset($setup_extensions[$type][$extension_id])
    )
  {
    $errors = setup_install_extension($type, $extension_id, $setup_extensions[$type]);
  }

  header('Content-Type: application/json; charset=utf-8');
  if (empty($errors))
  {
    echo json_encode(array('status' => 'ok'));
  }
  else
  {
    echo json_encode(array('status' => 'error', 'message' => implode(' ', $errors)));
  }
  exit();
}

//------------------------------------------------------------------------ steps
$steps = array(
  'extensions' => array('title' => l10n('Gallery setup')),
  );
$steps = trigger_change('get_setup_steps', $steps);

$step_ids = array_keys($steps);
$step_index = array_search($conf['setup_step'], $step_ids);
if (false === $step_index)
{
  $step_index = 0;
}

$is_complete = false;

if (isset($_POST['next']) || isset($_POST['skip']))
{
  check_pwg_token();

  if (isset($step_ids[$step_index + 1]))
  {
    conf_update_param('setup_step', $step_ids[$step_index + 1]);
    redirect(get_root_url().'install.php?configuration');
  }

  setup_unlock();
  $is_complete = true;
}

//------------------------------------------------------------------- extensions
if (!$is_complete && 'extensions' == $step_ids[$step_index])
{
  $server_themes = array();
  $server_plugins = array();

  if (count($setup_extensions['themes']) + count($setup_extensions['plugins']) > 0)
  {
    $server_themes = setup_get_server_extensions('themes', $setup_extensions['themes']);
    $server_plugins = setup_get_server_extensions('plugins', $setup_extensions['plugins']);
  }

  if (0 == count($server_themes) && 0 == count($server_plugins))
  {
    setup_unlock();
    $is_complete = true;
  }
  else
  {
    $setup_themes = setup_get_cards('themes', $setup_extensions['themes'], $server_themes);
    $setup_plugins = setup_get_cards('plugins', $setup_extensions['plugins'], $server_plugins);
  }
}

//--------------------------------------------------------------------- template
$template = new Template(PHPWG_ROOT_PATH.'themes', 'standard_pages');
$template->set_filenames( array('setup'=>'setup.tpl') );

$title = l10n('Gallery setup');
$page['body_id'] = 'theSetupPage';

$help_link = 'https://doc.piwigo.org';
if ('fr' == substr($user['language'], 0, 2))
{
  $help_link = 'https://doc-fr.piwigo.org';
}

$forum_urls = array(
  'fr' => 'https://fr.piwigo.org/forum',
  'de' => 'https://de.piwigo.org/forum',
  );
$forum_url = $forum_urls[substr($user['language'], 0, 2)] ?? 'https://piwigo.org/forum';

$template->assign(
  array(
    'IS_COMPLETE' => $is_complete,
    'U_GALLERY' => get_gallery_home_url(),
    'L_INSTALL_HELP' => l10n('Need help ? Ask your question on <a href="%s">Piwigo message board</a>.', $forum_url),
    'HELP_LINK' => $help_link,
    'COOKIE_PATH' => cookie_path(),
    'RELEASE' => PHPWG_VERSION,
    'F_ACTION' => get_root_url().'install.php?configuration',
    'PWG_TOKEN' => get_pwg_token(),
    'STEP_ID' => $step_ids[$step_index],
    'STEP_TITLE' => $steps[ $step_ids[$step_index] ]['title'],
    'NB_STEPS_LEFT' => count($step_ids) - $step_index,
    'setup_themes' => $setup_themes ?? array(),
    'setup_plugins' => $setup_plugins ?? array(),
    'THEMES_WRITABLE' => is_writable(PHPWG_THEMES_PATH),
    'PLUGINS_WRITABLE' => is_writable(PHPWG_PLUGINS_PATH),
    'CAN_DOWNLOAD' => $conf['enable_extensions_install'],
    )
  );

//----------------------------------------------------------- html code display
include(PHPWG_ROOT_PATH.'include/page_header.php');
flush_page_messages();
$template->pparse('setup');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
exit();
?>
