<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

function check_upgrade()
{
  if (defined('PHPWG_IN_UPGRADE'))
  {
    return PHPWG_IN_UPGRADE;
  }
  return false;
}

// concerning upgrade, we use the default tables
function prepare_conf_upgrade()
{
  global $prefixeTable;

  // $conf is not used for users tables
  // define cannot be re-defined
  define('CATEGORIES_TABLE', $prefixeTable.'categories');
  define('COMMENTS_TABLE', $prefixeTable.'comments');
  define('CONFIG_TABLE', $prefixeTable.'config');
  define('FAVORITES_TABLE', $prefixeTable.'favorites');
  define('GROUP_ACCESS_TABLE', $prefixeTable.'group_access');
  define('GROUPS_TABLE', $prefixeTable.'groups');
  define('HISTORY_TABLE', $prefixeTable.'history');
  define('HISTORY_SUMMARY_TABLE', $prefixeTable.'history_summary');
  define('IMAGE_CATEGORY_TABLE', $prefixeTable.'image_category');
  define('IMAGES_TABLE', $prefixeTable.'images');
  define('SESSIONS_TABLE', $prefixeTable.'sessions');
  define('SITES_TABLE', $prefixeTable.'sites');
  define('USER_ACCESS_TABLE', $prefixeTable.'user_access');
  define('USER_GROUP_TABLE', $prefixeTable.'user_group');
  define('USERS_TABLE', $prefixeTable.'users');
  define('USER_INFOS_TABLE', $prefixeTable.'user_infos');
  define('USER_FEED_TABLE', $prefixeTable.'user_feed');
  define('RATE_TABLE', $prefixeTable.'rate');
  define('USER_CACHE_TABLE', $prefixeTable.'user_cache');
  define('USER_CACHE_CATEGORIES_TABLE', $prefixeTable.'user_cache_categories');
  define('CADDIE_TABLE', $prefixeTable.'caddie');
  define('UPGRADE_TABLE', $prefixeTable.'upgrade');
  define('SEARCH_TABLE', $prefixeTable.'search');
  define('USER_MAIL_NOTIFICATION_TABLE', $prefixeTable.'user_mail_notification');
  define('TAGS_TABLE', $prefixeTable.'tags');
  define('IMAGE_TAG_TABLE', $prefixeTable.'image_tag');
  define('PLUGINS_TABLE', $prefixeTable.'plugins');
  define('OLD_PERMALINKS_TABLE', $prefixeTable.'old_permalinks');
  define('THEMES_TABLE', $prefixeTable.'themes');
  define('LANGUAGES_TABLE', $prefixeTable.'languages');
}

// Deactivate all non-standard plugins
function deactivate_non_standard_plugins()
{
  global $page;

  $standard_plugins = array(
    'AdminTools',
    'TakeATour',
    'language_switch',
    'LocalFilesEditor'
    );

  $query = '
SELECT id
FROM '.PREFIX_TABLE.'plugins
WHERE state = \'active\'
AND id NOT IN (\'' . implode('\',\'', $standard_plugins) . '\')
;';

  $result = pwg_query($query);
  $plugins = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $plugins[] = $row['id'];
  }

  if (!empty($plugins))
  {
    $query = '
UPDATE '.PREFIX_TABLE.'plugins
SET state=\'inactive\'
WHERE id IN (\'' . implode('\',\'', $plugins) . '\')
;';
    pwg_query($query);

    $page['infos'][] = l10n('As a precaution, following plugins have been deactivated. You must check for plugins upgrade before reactiving them:')
                        .'<p><i>'.implode(', ', $plugins).'</i></p>';
  }
}

// Deactivate all non-standard themes
function deactivate_non_standard_themes()
{
  global $page, $conf;

  $standard_themes = array(
    'modus',
    'elegant',
    'smartpocket',
    );

  $query = '
SELECT
    id,
    name
  FROM '.PREFIX_TABLE.'themes
  WHERE id NOT IN (\''.implode("','", $standard_themes).'\')
;';
  $result = pwg_query($query);
  $theme_ids = array();
  $theme_names = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $theme_ids[] = $row['id'];
    $theme_names[] = $row['name'];
  }

  if (!empty($theme_ids))
  {
    $query = '
DELETE
  FROM '.PREFIX_TABLE.'themes
  WHERE id IN (\''.implode("','", $theme_ids).'\')
;';
    pwg_query($query);

    $page['infos'][] = l10n('As a precaution, following themes have been deactivated. You must check for themes upgrade before reactiving them:')
                        .'<p><i>'.implode(', ', $theme_names).'</i></p>';

    // what is the default theme?
    $query = '
SELECT theme
  FROM '.PREFIX_TABLE.'user_infos
  WHERE user_id = '.$conf['default_user_id'].'
;';
    list($default_theme) = pwg_db_fetch_row(pwg_query($query));

    // if the default theme has just been deactivated, let's set another core theme as default
    if (in_array($default_theme, $theme_ids))
    {
      // make sure default Piwigo theme is active
      $query = '
SELECT
    COUNT(*)
  FROM '.PREFIX_TABLE.'themes
  WHERE id = \''.PHPWG_DEFAULT_TEMPLATE.'\'
;';
      list($counter) = pwg_db_fetch_row(pwg_query($query));
      if ($counter < 1)
      {
        // we need to activate theme first
        include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
        $themes = new themes();
        $themes->perform_action('activate', PHPWG_DEFAULT_TEMPLATE);
      }

      // then associate it to default user
      $query = '
UPDATE '.PREFIX_TABLE.'user_infos
  SET theme = \''.PHPWG_DEFAULT_TEMPLATE.'\'
  WHERE user_id = '.$conf['default_user_id'].'
;';
      pwg_query($query);
    }
  }
}

// Deactivate all templates
function deactivate_templates()
{
  conf_update_param('extents_for_templates', array());
}

// Check access rights
function check_upgrade_access_rights()
{
  global $conf, $page, $current_release;

  if (version_compare($current_release, '2.0', '>=') and isset($_COOKIE[session_name()]))
  {
    // Check if user is already connected as webmaster
    session_start();
    if (!empty($_SESSION['pwg_uid']))
    {
      $query = '
SELECT status
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$_SESSION['pwg_uid'].'
;';
      pwg_query($query);

      $row = pwg_db_fetch_assoc(pwg_query($query));
      if (isset($row['status']) and $row['status'] == 'webmaster')
      {
        define('PHPWG_IN_UPGRADE', true);
        return;
      }
    }
  }

  if (!isset($_POST['username']) or !isset($_POST['password']))
  {
    return;
  }

  $username = $_POST['username'];
  $password = $_POST['password'];

  if(function_exists('get_magic_quotes_gpc') && !@get_magic_quotes_gpc() )
  {
    $username = pwg_db_real_escape_string($username);
  }

  if (version_compare($current_release, '2.0', '<'))
  {
    $username = utf8_decode($username);
    $password = utf8_decode($password);
  }

  if (version_compare($current_release, '1.5', '<'))
  {
    $query = '
SELECT password, status
FROM '.USERS_TABLE.'
WHERE username = \''.$username.'\'
;';
  }
  else
  {
    $query = '
SELECT u.password, ui.status
FROM '.USERS_TABLE.' AS u
INNER JOIN '.USER_INFOS_TABLE.' AS ui
ON u.'.$conf['user_fields']['id'].'=ui.user_id
WHERE '.$conf['user_fields']['username'].'=\''.$username.'\'
;';
  }
  $row = pwg_db_fetch_assoc(pwg_query($query));

  if (!$conf['password_verify']($password, $row['password']))
  {
    $page['errors'][] = l10n('Invalid password!');
  }
  elseif ($row['status'] != 'admin' and $row['status'] != 'webmaster')
  {
    $page['errors'][] = l10n('You do not have access rights to run upgrade');
  }
  else
  {
    define('PHPWG_IN_UPGRADE', true);
  }
}

/**
 * which upgrades are available ?
 *
 * @return array
 */
function get_available_upgrade_ids()
{
  $upgrades_path = PHPWG_ROOT_PATH.'install/db';

  $available_upgrade_ids = array();

  if ($contents = opendir($upgrades_path))
  {
    while (($node = readdir($contents)) !== false)
    {
      if (is_file($upgrades_path.'/'.$node)
          and preg_match('/^(.*?)-database\.php$/', $node, $match))
      {
        $available_upgrade_ids[] = $match[1];
      }
    }
  }
  natcasesort($available_upgrade_ids);

  return $available_upgrade_ids;
}


/**
 * returns true if there are available upgrade files
 */
function check_upgrade_feed()
{
  // retrieve already applied upgrades
  $query = '
SELECT id
  FROM '.UPGRADE_TABLE.'
;';
  $applied = array_from_query($query, 'id');

  // retrieve existing upgrades
  $existing = get_available_upgrade_ids();

  // which upgrades need to be applied?
  return (count(array_diff($existing, $applied)) > 0);
}

function upgrade_db_connect()
{
  global $conf;

  try
  {
    pwg_db_connect($conf['db_host'], $conf['db_user'],
                   $conf['db_password'], $conf['db_base']);
    pwg_db_check_version();
  }
  catch (Exception $e)
  {
    my_error(l10n($e->getMessage()), true); 
  }
}
?>
