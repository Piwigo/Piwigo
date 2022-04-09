<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\admin\install
 */


/**
 * Loads a SQL file and executes all queries.
 * Before executing a query, $replaced is... replaced by $replacing. This is
 * useful when the SQL file contains generic words. Drop table queries are
 * not executed.
 *
 * @param string $filepath
 * @param string $replaced
 * @param string $replacing
 */

function execute_sqlfile($filepath, $replaced, $replacing, $dblayer)
{
  $sql_lines = file($filepath);
  $query = '';
  foreach ($sql_lines as $sql_line)
  {
    $sql_line = trim($sql_line);
    if (preg_match('/(^--|^$)/', $sql_line))
    {
      continue;
    }
    $query.= ' '.$sql_line;
    // if we reached the end of query, we execute it and reinitialize the
    // variable "query"
    if (preg_match('/;$/', $sql_line))
    {
      $query = trim($query);
      $query = str_replace($replaced, $replacing, $query);
      // we don't execute "DROP TABLE" queries
      if (!preg_match('/^DROP TABLE/i', $query))
      {
        if ('mysql' == $dblayer)
        {
          if ( preg_match('/^(CREATE TABLE .*)[\s]*;[\s]*/im', $query, $matches) )
          {
            $query = $matches[1].' DEFAULT CHARACTER SET utf8'.';';
          }
        }
        pwg_query($query);
      }
      $query = '';
    }
  }
}

/**
 * Automatically activate all core themes in the "themes" directory.
 */
function activate_core_themes()
{
  include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
  $themes = new themes();
  foreach ($themes->fs_themes as $theme_id => $fs_theme)
  {
    if (in_array($theme_id, array('modus', 'smartpocket')))
    {
      $themes->perform_action('activate', $theme_id);
    }
  }
}

/**
 * Automatically activate some core plugins
 */
function activate_core_plugins()
{
  include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');
  
  $plugins = new plugins();

  foreach($plugins->fs_plugins as $plugin_id => $fs_plugin)
  {
    if (in_array($plugin_id, array()))
    {
      $plugins->perform_action('activate', $plugin_id);
    }
  }
}

/**
 * Connect to database during installation.
 *
 * @param array &$errors - populated with errors
 */
function install_db_connect($dbhost, $dbuser, $dbpasswd, $dbname, &$errors)
{
  try
  {
    pwg_db_connect($dbhost, $dbuser, $dbpasswd, $dbname);
    pwg_db_check_version();
  }
  catch (Exception $e)
  {
    $errors[] = l10n($e->getMessage());
  }
}

/**
 * Create and initialize database
 *
 * @param object languages - languages informations
 * @param string language - default language
 * @param string prefixeTable - prefix of database names
 */
function initialize_db($languages, $language, $prefixeTable)
{
  include_once(PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php');
  // tables creation, based on piwigo_structure.sql
  execute_sqlfile(
    PHPWG_ROOT_PATH.'install/piwigo_structure-mysql.sql',
    DEFAULT_PREFIX_TABLE,
    $prefixeTable,
    'mysql'
    );
  // We fill the tables with basic informations
  execute_sqlfile(
    PHPWG_ROOT_PATH.'install/config.sql',
    DEFAULT_PREFIX_TABLE,
    $prefixeTable,
    'mysql'
    );

  $query = '
INSERT INTO '.$prefixeTable.'config (param,value,comment)
 VALUES (\'secret_key\',md5('.pwg_db_cast_to_text(DB_RANDOM_FUNCTION.'()').'),
 \'a secret key specific to the gallery for internal use\');';
  pwg_query($query);

  conf_update_param('piwigo_db_version', get_branch_from_version(PHPWG_VERSION));
  conf_update_param('gallery_title', pwg_db_real_escape_string(l10n('Just another Piwigo gallery')));

  conf_update_param(
    'page_banner',
    '<h1>%gallery_title%</h1>'."\n\n<p>".pwg_db_real_escape_string(l10n('Welcome to my photo gallery')).'</p>'
    );

  // fill languages table, only activate the current language
  $languages->perform_action('activate', $language);

  // fill $conf global array
  load_conf_from_db();

  // PWG_CHARSET is required for building the fs_themes array in the
  // themes class
  if (!defined('PWG_CHARSET'))
  {
    define('PWG_CHARSET', 'utf-8');
  }
  activate_core_themes();
  activate_core_plugins();

  $insert = array(
    'id' => 1,
    'galleries_url' => PHPWG_ROOT_PATH.'galleries/',
    );
  mass_inserts(SITES_TABLE, array_keys($insert), array($insert));

}

/**
 * Add first admin in database
 *
 * @param string admin_name - admin name
 * @param string admin_pass1 - admin password
 * @param string admin_main - admin email
 * @param string admin_language - language of admin
 */
function add_admin($admin_name, $admin_pass1, $admin_mail, $language)
{
  // webmaster admin user
  $inserts = array(
    array(
      'id'           => 1,
      'username'     => $admin_name,
      'password'     => md5($admin_pass1),
      'mail_address' => $admin_mail,
      ),
    array(
      'id'           => 2,
      'username'     => 'guest',
      ),
    );
  mass_inserts(USERS_TABLE, array_keys($inserts[0]), $inserts);

  create_user_infos(array(1,2), array('language' => $language));

}

?>
