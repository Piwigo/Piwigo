<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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
$config_file = PHPWG_ROOT_PATH.'include/config_database.inc.php';
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

include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');

include(PHPWG_ROOT_PATH.'include/config_database.inc.php');
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');
include(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');

prepare_conf_upgrade();

include_once(PHPWG_ROOT_PATH.'include/constants.php');
define('PREFIX_TABLE', $prefixeTable);

// Database connection
$pwg_db_link = pwg_db_connect($conf['db_host'], $conf['db_user'], 
			      $conf['db_password'], $conf['db_base']) 
  or my_error('pwg_db_connect', true);

pwg_db_check_charset();

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
      array_push($tables, $row[0]);
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
      array_push($columns_of[$table], $row[0]);
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
if (isset($_GET['language']))
{
  $language = strip_tags($_GET['language']);
}
else
{
  $language = 'en_UK';
  // Try to get browser language
  foreach (get_languages('utf-8') as $language_code => $language_name)
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
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

include( PHPWG_ROOT_PATH .'include/template.class.php');
$template = new Template(PHPWG_ROOT_PATH.'admin/template/goto', 'roma');
$template->set_filenames(array('upgrade'=>'upgrade.tpl'));
$template->assign(array(
  'RELEASE' => PHPWG_VERSION,
  'L_UPGRADE_HELP' => sprintf(l10n('install_help'), PHPWG_URL.'/forum'),
  )
);

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
else
{
  die('No upgrade required, the database structure is up to date');
}

// +-----------------------------------------------------------------------+
// |                            upgrade launch                             |
// +-----------------------------------------------------------------------+
$page['infos'] = array();
$page['errors'] = array();
$mysql_changes = array();

if (isset($_POST['username']) and isset($_POST['password']))
{
  check_upgrade_access_rights($current_release, $_POST['username'], $_POST['password']);
}

if (isset($_POST['submit']) and check_upgrade())
{
  $upgrade_file = PHPWG_ROOT_PATH.'install/upgrade_'.$current_release.'.php';
  if (is_file($upgrade_file))
  {
    $page['upgrade_start'] = get_moment();
    $conf['die_on_sql_error'] = false;
    include($upgrade_file);

    // Something to add in config_database.inc.php?
    if (!empty($mysql_changes))
    {
      $config_file_contents = 
        substr($config_file_contents, 0, $php_end_tag) . "\r\n"
        . implode("\r\n" , $mysql_changes) . "\r\n"
        . substr($config_file_contents, $php_end_tag);

      if (!@file_put_contents($config_file, $config_file_contents))
      {
        array_push($page['infos'],
          l10n('in include/config_database.inc.php, before ?>, insert:') . '
<p><textarea rows="4" cols="40">'.implode("\r\n" , $mysql_changes).'</textarea></p>'
          );
      }
    }

    // Plugins deactivation
    if (in_array(PREFIX_TABLE.'plugins', $tables))
    {
      deactivate_non_standard_plugins();
    }

    // Create empty local files to avoid log errors
    create_empty_local_files();

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

    array_push($page['infos'],
      l10n('perform a maintenance check')
      );

    // Save $page['infos'] in order to restore after maintenance actions
    $page['infos_sav'] = $page['infos'];
    $page['infos'] = array();

    // c13y_upgrade plugin means "check integrity after upgrade", so it
    // becomes useful just after an upgrade
    $query = '
REPLACE INTO '.PLUGINS_TABLE.'
  (id, state)
  VALUES (\'c13y_upgrade\', \'active\')
;';
    pwg_query($query);

    // Delete cache data
    invalidate_user_cache(true);
    $template->delete_compiled_templates();

    // Tables Maintenance
    do_maintenance_all_tables();

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
  foreach (get_languages('utf-8') as $language_code => $language_name)
  {
    if ($language == $language_code)
    {
      $template->assign('language_selection', $language_code);
    }
    $languages_options[$language_code] = $language_name;
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
