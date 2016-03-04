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

define('PHPWG_ROOT_PATH', './');

// load config file
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');

$config_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR.'config/database.inc.php';
$config_file_contents = @file_get_contents($config_file);
if ($config_file_contents === false)
{
  die('Cannot load '.$config_file);
}
$php_end_tag = strrpos($config_file_contents, '?'.'>');
if ($php_end_tag === false)
{
  die('Cannot find php end tag in '.$config_file);
}

include($config_file);

// $conf is not used for users tables - define cannot be re-defined
define('USERS_TABLE', $prefixeTable.'users');
include_once(PHPWG_ROOT_PATH.'include/constants.php');
define('PREFIX_TABLE', $prefixeTable);
define('UPGRADES_PATH', PHPWG_ROOT_PATH.'install/db');

include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH . 'include/template.class.php');

// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+

/**
 * list all tables in an array
 *
 * @return array
 */
function get_tables()
{
  $tables = array();

  $query = '
SHOW TABLES
;';
  $result = pwg_query($query);

  while ($row = pwg_db_fetch_row($result))
  {
    if (preg_match('/^'.PREFIX_TABLE.'/', $row[0]))
    {
      $tables[] = $row[0];
    }
  }

  return $tables;
}

/**
 * list all columns of each given table
 *
 * @return array of array
 */
function get_columns_of($tables)
{
  $columns_of = array();

  foreach ($tables as $table)
  {
    $query = '
DESC '.$table.'
;';
    $result = pwg_query($query);

    $columns_of[$table] = array();

    while ($row = pwg_db_fetch_row($result))
    {
      $columns_of[$table][] = $row[0];
    }
  }

  return $columns_of;
}

/**
 */
function print_time($message)
{
  global $last_time;

  $new_time = get_moment();
  echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
  echo ' '.$message;
  echo '</pre>';
  flush();
  $last_time = $new_time;
}

// +-----------------------------------------------------------------------+
// |                             playing zone                              |
// +-----------------------------------------------------------------------+

// echo implode('<br>', get_tables());
// echo '<pre>'; print_r(get_columns_of(get_tables())); echo '</pre>';

// foreach (get_available_upgrade_ids() as $upgrade_id)
// {
//   echo $upgrade_id, '<br>';
// }

// +-----------------------------------------------------------------------+
// |                             language                                  |
// +-----------------------------------------------------------------------+
include(PHPWG_ROOT_PATH . 'admin/include/languages.class.php');
$languages = new languages('utf-8');
if (isset($_GET['language']))
{
  $language = strip_tags($_GET['language']);

  if (!in_array($language, array_keys($languages->fs_languages)))
  {
    $language = PHPWG_DEFAULT_LANGUAGE;
  }
}
else
{
  $language = 'en_UK';
  // Try to get browser language
  foreach ($languages->fs_languages as $language_code => $fs_language)
  {
    if (substr($language_code,0,2) == @substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2))
    {
      $language = $language_code;
      break;
    }
  }
}

if ('fr_FR' == $language) {
  define('PHPWG_DOMAIN', 'fr.piwigo.org');
}
else if ('it_IT' == $language) {
  define('PHPWG_DOMAIN', 'it.piwigo.org');
}
else if ('de_DE' == $language) {
  define('PHPWG_DOMAIN', 'de.piwigo.org');
}
else if ('es_ES' == $language) {
  define('PHPWG_DOMAIN', 'es.piwigo.org');
}
else if ('pl_PL' == $language) {
  define('PHPWG_DOMAIN', 'pl.piwigo.org');
}
else if ('zh_CN' == $language) {
  define('PHPWG_DOMAIN', 'cn.piwigo.org');
}
else if ('hu_HU' == $language) {
  define('PHPWG_DOMAIN', 'hu.piwigo.org');
}
else if ('ru_RU' == $language) {
  define('PHPWG_DOMAIN', 'ru.piwigo.org');
}
else if ('nl_NL' == $language) {
  define('PHPWG_DOMAIN', 'nl.piwigo.org');
}
else if ('tr_TR' == $language) {
  define('PHPWG_DOMAIN', 'tr.piwigo.org');
}
else if ('da_DK' == $language) {
  define('PHPWG_DOMAIN', 'da.piwigo.org');
}
else if ('pt_BR' == $language) {
  define('PHPWG_DOMAIN', 'br.piwigo.org');
}
else {
  define('PHPWG_DOMAIN', 'piwigo.org');
}
define('PHPWG_URL', 'http://'.PHPWG_DOMAIN);

load_language( 'common.lang', '', array('language'=>$language, 'target_charset'=>'utf-8', 'no_fallback' => true) );
load_language( 'admin.lang', '', array('language'=>$language, 'target_charset'=>'utf-8', 'no_fallback' => true) );
load_language( 'install.lang', '', array('language'=>$language, 'target_charset'=>'utf-8', 'no_fallback' => true) );
load_language( 'upgrade.lang', '', array('language'=>$language, 'target_charset'=>'utf-8', 'no_fallback' => true) );
// check php version
if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '<'))
{
  include(PHPWG_ROOT_PATH.'install/php5_apache_configuration.php');
}

// +-----------------------------------------------------------------------+
// |                          database connection                          |
// +-----------------------------------------------------------------------+
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');
include(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');

upgrade_db_connect();
pwg_db_check_charset();

list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));
define('CURRENT_DATE', $dbnow);

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

$template = new Template(PHPWG_ROOT_PATH.'admin/themes', 'clear');
$template->set_filenames(array('upgrade'=>'upgrade.tpl'));
$template->assign(array(
  'RELEASE' => PHPWG_VERSION,
  'L_UPGRADE_HELP' => l10n('Need help ? Ask your question on <a href="%s">Piwigo message board</a>.', PHPWG_URL.'/forum'),
  )
);

// +-----------------------------------------------------------------------+
// | Remote sites are not compatible with Piwigo 2.4+                      |
// +-----------------------------------------------------------------------+

$has_remote_site = false;

$query = 'SELECT galleries_url FROM '.SITES_TABLE.';';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  if (url_is_remote($row['galleries_url']))
  {
    $has_remote_site = true;
  }
}

if ($has_remote_site)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/updates.class.php');
  include_once(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');

  $page['errors'] = array();
  $step = 3;
  updates::upgrade_to('2.3.4', $step, false);

  if (!empty($page['errors']))
  {
    echo '<ul>';
    foreach ($page['errors'] as $error)
    {
      echo '<li>'.$error.'</li>';
    }
    echo '</ul>';
  }

  exit();
}

// +-----------------------------------------------------------------------+
// |                            upgrade choice                             |
// +-----------------------------------------------------------------------+

$tables = get_tables();
$columns_of = get_columns_of($tables);

// find the current release
if (!in_array('param', $columns_of[PREFIX_TABLE.'config']))
{
  // we're in branch 1.3, important upgrade, isn't it?
  if (in_array(PREFIX_TABLE.'user_category', $tables))
  {
    $current_release = '1.3.1';
  }
  else
  {
    $current_release = '1.3.0';
  }
}
else if (!in_array(PREFIX_TABLE.'user_cache', $tables))
{
  $current_release = '1.4.0';
}
else if (!in_array(PREFIX_TABLE.'tags', $tables))
{
  $current_release = '1.5.0';
}
else if ( !in_array(PREFIX_TABLE.'plugins', $tables) )
{
  if (!in_array('auto_login_key', $columns_of[PREFIX_TABLE.'user_infos']))
  {
    $current_release = '1.6.0';
  }
  else
  {
    $current_release = '1.6.2';
  }
}
else if (!in_array('md5sum', $columns_of[PREFIX_TABLE.'images']))
{
  $current_release = '1.7.0';
}
else if (!in_array(PREFIX_TABLE.'themes', $tables))
{
  $current_release = '2.0.0';
}
else if (!in_array('added_by', $columns_of[PREFIX_TABLE.'images']))
{
  $current_release = '2.1.0';
}
else if (!in_array('rating_score', $columns_of[PREFIX_TABLE.'images']))
{
  $current_release = '2.2.0';
}
else if (!in_array('rotation', $columns_of[PREFIX_TABLE.'images']))
{
  $current_release = '2.3.0';
}
else if (!in_array('website_url', $columns_of[PREFIX_TABLE.'comments']))
{
  $current_release = '2.4.0';
}
else if (!in_array('nb_available_tags', $columns_of[PREFIX_TABLE.'user_cache']))
{
  $current_release = '2.5.0';
}
else if (!in_array('activation_key_expire', $columns_of[PREFIX_TABLE.'user_infos']))
{
  $current_release = '2.6.0';
}
else
{
  // retrieve already applied upgrades
  $query = '
SELECT id
  FROM '.PREFIX_TABLE.'upgrade
;';
  $applied_upgrades = array_from_query($query, 'id');

  if (!in_array(148, $applied_upgrades))
  {
    $current_release = '2.7.0';
  }
  else
  {
    // confirm that the database is in the same version as source code files
    conf_update_param('piwigo_db_version', get_branch_from_version(PHPWG_VERSION));

    header('Content-Type: text/html; charset='.get_pwg_charset());
    echo 'No upgrade required, the database structure is up to date';
    echo '<br><a href="index.php">← back to gallery</a>';
    exit();
  }
}

// +-----------------------------------------------------------------------+
// |                            upgrade launch                             |
// +-----------------------------------------------------------------------+
$page['infos'] = array();
$page['errors'] = array();
$mysql_changes = array();

check_upgrade_access_rights();

if ((isset($_POST['submit']) or isset($_GET['now']))
  and check_upgrade())
{
  $upgrade_file = PHPWG_ROOT_PATH.'install/upgrade_'.$current_release.'.php';
  if (is_file($upgrade_file))
  {
    // reset SQL counters
    $page['queries_time'] = 0;
    $page['count_queries'] = 0;
    
    $page['upgrade_start'] = get_moment();
    $conf['die_on_sql_error'] = false;
    include($upgrade_file);
    conf_update_param('piwigo_db_version', get_branch_from_version(PHPWG_VERSION));

    // Something to add in database.inc.php?
    if (!empty($mysql_changes))
    {
      $config_file_contents = 
        substr($config_file_contents, 0, $php_end_tag) . "\r\n"
        . implode("\r\n" , $mysql_changes) . "\r\n"
        . substr($config_file_contents, $php_end_tag);

      if (!@file_put_contents($config_file, $config_file_contents))
      {
        $page['infos'][] = l10n(
          'In <i>%s</i>, before <b>?></b>, insert:',
          PWG_LOCAL_DIR.'config/database.inc.php'
          )
        .'<p><textarea rows="4" cols="40">'
        .implode("\r\n" , $mysql_changes).'</textarea></p>';
      }
    }

    // Deactivate non standard extensions
    deactivate_non_standard_plugins();
    deactivate_non_standard_themes();
    deactivate_templates();

    $page['upgrade_end'] = get_moment();

    $template->assign(
      'upgrade',
      array(
        'VERSION' => $current_release,
        'TOTAL_TIME' => get_elapsed_time(
          $page['upgrade_start'],
          $page['upgrade_end']
          ),
        'SQL_TIME' => number_format(
          $page['queries_time'],
          3,
          '.',
          ' '
          ).' s',
        'NB_QUERIES' => $page['count_queries']
        )
      );

    $page['infos'][] = l10n('Perform a maintenance check in [Administration>Tools>Maintenance] if you encounter any problem.');

    // Save $page['infos'] in order to restore after maintenance actions
    $page['infos_sav'] = $page['infos'];
    $page['infos'] = array();

    $query = '
REPLACE INTO '.PLUGINS_TABLE.'
  (id, state)
  VALUES (\'TakeATour\', \'active\')
;';
    pwg_query($query);

    $template->assign(
      array(
        'button_label' => l10n('Home'),
        'button_link' => 'index.php',
        )
      );

    // if the webmaster has a session, let's give a link to discover new features
    if (!empty($_SESSION['pwg_uid']))
    {
      $version_ = str_replace('.', '_', get_branch_from_version(PHPWG_VERSION).'.0');
      
      if (file_exists(PHPWG_PLUGINS_PATH .'TakeATour/tours/'.$version_.'/config.inc.php'))
      {
        load_language(
          'plugin.lang',
          PHPWG_PLUGINS_PATH.'TakeATour/',
          array(
            'language' => $language,
            'force_fallback'=>'en_UK',
            )
          );

        // we need the secret key for get_pwg_token()
        load_conf_from_db();
        
        $template->assign(
          array(
            // TODO find a better way to do that, with a core string in English
            'button_label' => str_replace('2.7', get_branch_from_version(PHPWG_VERSION), l10n('2_7_0_descrp')),
            'button_link' => 'admin.php?submited_tour_path=tours/'.$version_.'&amp;pwg_token='.get_pwg_token(),
            )
          );
      }
    }

    // Delete cache data
    invalidate_user_cache(true);
    $template->delete_compiled_templates();

    // Restore $page['infos'] in order to hide informations messages from functions calles
    // errors messages are not hide
    $page['infos'] = $page['infos_sav'];

  }
}

// +-----------------------------------------------------------------------+
// |                          start template output                        |
// +-----------------------------------------------------------------------+
else
{
  if (!defined('PWG_CHARSET'))
  {
    define('PWG_CHARSET', 'utf-8');
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/languages.class.php');
  $languages = new languages();
  
  foreach ($languages->fs_languages as $language_code => $fs_language)
  {
    if ($language == $language_code)
    {
      $template->assign('language_selection', $language_code);
    }
    $languages_options[$language_code] = $fs_language['name'];
  }
  $template->assign('language_options', $languages_options);

  $template->assign('introduction', array(
    'CURRENT_RELEASE' => $current_release,
    'F_ACTION' => 'upgrade.php?language=' . $language));

  if (!check_upgrade())
  {
    $template->assign('login', true);
  }
}

if (count($page['errors']) != 0)
{
  $template->assign('errors', $page['errors']);
}

if (count($page['infos']) != 0)
{
  $template->assign('infos', $page['infos']);
}

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->pparse('upgrade');
?>
