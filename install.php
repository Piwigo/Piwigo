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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');

include(PHPWG_ROOT_PATH . 'admin/include/functions_install.inc.php');

@set_magic_quotes_runtime(0); // Disable magic_quotes_runtime
//
// addslashes to vars if magic_quotes_gpc is off this is a security
// precaution to prevent someone trying to break out of a SQL statement.
//
if( !@get_magic_quotes_gpc() )
{
  if( is_array($_POST) )
  {
    while( list($k, $v) = each($_POST) )
    {
      if( is_array($_POST[$k]) )
      {
        while( list($k2, $v2) = each($_POST[$k]) )
        {
          $_POST[$k][$k2] = addslashes($v2);
        }
        @reset($_POST[$k]);
      }
      else
      {
        $_POST[$k] = addslashes($v);
      }
    }
    @reset($_POST);
  }

  if( is_array($_GET) )
  {
    while( list($k, $v) = each($_GET) )
    {
      if( is_array($_GET[$k]) )
      {
        while( list($k2, $v2) = each($_GET[$k]) )
        {
          $_GET[$k][$k2] = addslashes($v2);
        }
        @reset($_GET[$k]);
      }
      else
      {
        $_GET[$k] = addslashes($v);
      }
    }
    @reset($_GET);
  }

  if( is_array($_COOKIE) )
  {
    while( list($k, $v) = each($_COOKIE) )
    {
      if( is_array($_COOKIE[$k]) )
      {
        while( list($k2, $v2) = each($_COOKIE[$k]) )
        {
          $_COOKIE[$k][$k2] = addslashes($v2);
        }
        @reset($_COOKIE[$k]);
      }
      else
      {
        $_COOKIE[$k] = addslashes($v);
      }
    }
    @reset($_COOKIE);
  }
}

//----------------------------------------------------- variable initialization

define('DEFAULT_PREFIX_TABLE', 'piwigo_');

// Obtain various vars
$dbhost = (!empty($_POST['dbhost'])) ? $_POST['dbhost'] : 'localhost';
$dbuser = (!empty($_POST['dbuser'])) ? $_POST['dbuser'] : '';
$dbpasswd = (!empty($_POST['dbpasswd'])) ? $_POST['dbpasswd'] : '';
$dbname = (!empty($_POST['dbname'])) ? $_POST['dbname'] : '';
$dblayer = (!empty($_POST['dblayer'])) ? $_POST['dblayer'] : 'mysql';

if (isset($_POST['install']))
{
  $table_prefix = $_POST['prefix'];
}
else
{
  $table_prefix = DEFAULT_PREFIX_TABLE;
}

$admin_name = (!empty($_POST['admin_name'])) ? $_POST['admin_name'] : '';
$admin_pass1 = (!empty($_POST['admin_pass1'])) ? $_POST['admin_pass1'] : '';
$admin_pass2 = (!empty($_POST['admin_pass2'])) ? $_POST['admin_pass2'] : '';
$admin_mail = (!empty($_POST['admin_mail'])) ? $_POST['admin_mail'] : '';

$infos = array();
$errors = array();

// database config file migration : mysql.inc.php et config_database.inc.php
$old_config_file = PHPWG_ROOT_PATH . 'include/mysql.inc.php';
$config_file = PHPWG_ROOT_PATH . 'include/config_database.inc.php';
if (!file_exists($config_file) && file_exists($old_config_file))
{
  $step = 3;
  include $old_config_file;
  $file_content = '<?php
$conf[\'dblayer\'] = \'mysql\';
$conf[\'db_base\'] = \''.$cfgBase.'\';
$conf[\'db_user\'] = \''.$cfgUser.'\';
$conf[\'db_password\'] = \''.$cfgPassword.'\';
$conf[\'db_host\'] = \''.$cfgHote.'\';

$prefixeTable = \''.$prefixeTable.'\';

define(\'PHPWG_INSTALLED\', true);
define(\'PWG_CHARSET\', \''.PWG_CHARSET.'\');
define(\'DB_CHARSET\', \''.DB_CHARSET.'\');
define(\'DB_COLLATE\', \''.DB_COLLATE.'\');

?'.'>';
}
// Open config.php ... if it exists
elseif (@file_exists($config_file))
{
  include($config_file);
  // Is Piwigo already installed ?
  if (defined("PHPWG_INSTALLED"))
  {
    die('Piwigo is already installed');
  }
}

$prefixeTable = $table_prefix;
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');
include(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$dblayer.'.inc.php');
include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'admin/include/functions.php');
include(PHPWG_ROOT_PATH . 'admin/include/functions_upgrade.php');

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
else {
  define('PHPWG_DOMAIN', 'piwigo.org');
}
define('PHPWG_URL', 'http://'.PHPWG_DOMAIN);

if (empty($step) || ($step != 3))
{
  load_language('common.lang', '', array('language' => $language, 'target_charset'=>'utf-8'));
  load_language('admin.lang', '', array('language' => $language, 'target_charset'=>'utf-8'));
  load_language('install.lang', '', array('language' => $language, 'target_charset'=>'utf-8'));
}
header('Content-Type: text/html; charset=UTF-8');
//------------------------------------------------- check php version
if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '<'))
{
  include(PHPWG_ROOT_PATH.'install/php5_apache_configuration.php');
}

//----------------------------------------------------- template initialization
include( PHPWG_ROOT_PATH .'include/template.class.php');
$template = new Template(PHPWG_ROOT_PATH.'admin/template/goto', 'roma');
$template->set_filenames( array('install' => 'install.tpl') );
if (!isset($step))
{
  $step = 1;
}
//---------------------------------------------------------------- form analyze
if ( isset( $_POST['install'] ))
{
  if (($pwg_db_link = pwg_db_connect($_POST['dbhost'], $_POST['dbuser'], 
				     $_POST['dbpasswd'], $_POST['dbname']))!==false) 
  {

    array_push( $infos, l10n('step1_confirmation') );

    $required_version = constant('REQUIRED_'.strtoupper($dblayer).'_VERSION');
    if ( version_compare(pwg_get_db_version(), $required_version, '>=') )
    {
      $pwg_charset = 'utf-8';
      $pwg_db_charset = 'utf8';
      if ($dblayer=='mysql')
      {
	$install_charset_collate = "DEFAULT CHARACTER SET $pwg_db_charset";
      }
      else 
      {
	$install_charset_collate = '';
      }
    }
    else
    {
      $pwg_charset = 'iso-8859-1';
      $pwg_db_charset = 'latin1';
      $install_charset_collate = '';
      if ( !array_key_exists($language, get_languages($pwg_charset) ) )
      {
        $language='en_UK';
      }
    }
  }
  else
  {
    array_push( $errors, l10n('step1_err_server') );
  }

  $webmaster = trim(preg_replace( '/\s{2,}/', ' ', $admin_name ));
  if ( empty($webmaster))
    array_push( $errors, l10n('step2_err_login1') );
  else if ( preg_match( '/[\'"]/', $webmaster ) )
    array_push( $errors, l10n('step2_err_login3') );
  if ( $admin_pass1 != $admin_pass2 || empty($admin_pass1) )
    array_push( $errors, l10n('step2_err_pass') );
  if ( empty($admin_mail))
    array_push( $errors, l10n('reg_err_mail_address') );
  else
  {
    $error_mail_address = validate_mail_address(null, $admin_mail);
    if (!empty($error_mail_address))
      array_push( $errors, $error_mail_address );
  }

  if ( count( $errors ) == 0 )
  {
    $step = 2;
    $file_content = '<?php
$conf[\'dblayer\'] = \''.$dblayer.'\';
$conf[\'db_base\'] = \''.$dbname.'\';
$conf[\'db_user\'] = \''.$dbuser.'\';
$conf[\'db_password\'] = \''.$dbpasswd.'\';
$conf[\'db_host\'] = \''.$dbhost.'\';

$prefixeTable = \''.$table_prefix.'\';

define(\'PHPWG_INSTALLED\', true);
define(\'PWG_CHARSET\', \''.$pwg_charset.'\');
define(\'DB_CHARSET\', \''.$pwg_db_charset.'\');
define(\'DB_COLLATE\', \'\');

?'.'>';

    @umask(0111);
    // writing the configuration file
    if ( !($fp = @fopen( $config_file, 'w' )))
    {
      $html_content = htmlentities( $file_content, ENT_QUOTES );
      $html_content = nl2br( $html_content );
      $error_copy = l10n('step1_err_copy');
      $error_copy .= '<br>--------------------------------------------------------------------<br>';
      $error_copy .= '<span class="sql_content">' . $html_content . '</span>';
      $error_copy .= '<br>--------------------------------------------------------------------<br>';
    }
    @fputs($fp, $file_content, strlen($file_content));
    @fclose($fp);

    // Create empty local files to avoid log errors
    create_empty_local_files();

    // tables creation, based on piwigo_structure.sql
    execute_sqlfile(
      PHPWG_ROOT_PATH.'install/piwigo_structure-'.$dblayer.'.sql',
      DEFAULT_PREFIX_TABLE,
      $table_prefix
      );
    // We fill the tables with basic informations
    execute_sqlfile(
      PHPWG_ROOT_PATH.'install/config.sql',
      DEFAULT_PREFIX_TABLE,
      $table_prefix
      );

    $query = '
INSERT INTO '.$table_prefix.'config (param,value,comment) 
   VALUES (\'secret_key\',\'md5('.pwg_db_cast_to_text(DB_RANDOM_FUNCTION.'()').')\',
   \'a secret key specific to the gallery for internal use\');';
    pwg_query($query);

    // fill $conf global array
    load_conf_from_db();

    $insert = array(
      'id' => 1,
      'galleries_url' => PHPWG_ROOT_PATH.'galleries/',
      );
    mass_inserts(SITES_TABLE, array_keys($insert), array($insert));

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

    // Available upgrades must be ignored after a fresh installation. To
    // make PWG avoid upgrading, we must tell it upgrades have already been
    // made.
    list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));
    define('CURRENT_DATE', $dbnow);
    $datas = array();
    foreach (get_available_upgrade_ids() as $upgrade_id)
    {
      array_push(
        $datas,
        array(
          'id'          => $upgrade_id,
          'applied'     => CURRENT_DATE,
          'description' => 'upgrade included in installation',
          )
        );
    }
    mass_inserts(
      UPGRADE_TABLE,
      array_keys($datas[0]),
      $datas
      );
  }
}

//------------------------------------------------------ start template output
if ($step == 3)
{
  @umask(0111);
  // writing the new configuration file
  if ( !($fp = @fopen( $config_file, 'w' )))
  {
    $html_content = htmlentities( $file_content, ENT_QUOTES );
    $html_content = nl2br( $html_content );
    $error_copy = l10n('Copy the text in pink between hyphens and paste it into the file "include/config_database.inc.php"(Warning : config_database.inc.php must only contain what is in pink, no line return or space character)');
    $error_copy .= '<br>--------------------------------------------------------------------<br>';
    $error_copy .= '<span class="sql_content">' . $html_content . '</span>';
    $error_copy .= '<br>--------------------------------------------------------------------<br>';
  } 
  else 
  {
    @fputs($fp, $file_content, strlen($file_content));
    @fclose($fp);

    @unlink($old_config_file);
    header("Location: index.php");
    exit();
  }

  $template->assign(
    array(
      'T_CONTENT_ENCODING' => 'utf-8',
      'migration' => true
	  ));
}
else
{
  $dbengines = available_engines();

  foreach (get_languages('utf-8') as $language_code => $language_name)
  {
    if ($language == $language_code)
    {
      $template->assign('language_selection', $language_code);
    }
    $languages_options[$language_code] = $language_name;
  }
  $template->assign('language_options', $languages_options);

  $template->assign(
    array(
      'T_CONTENT_ENCODING' => 'utf-8',
      'RELEASE' => PHPWG_VERSION,
      'F_ACTION' => 'install.php?language=' . $language,
      'F_DB_ENGINES' => $dbengines,
      'F_DB_LAYER' => $dblayer,
      'F_DB_HOST' => $dbhost,
      'F_DB_USER' => $dbuser,
      'F_DB_NAME' => $dbname,
      'F_DB_PREFIX' => $table_prefix,
      'F_ADMIN' => $admin_name,
      'F_ADMIN_EMAIL' => $admin_mail,
      'L_INSTALL_HELP' => sprintf(l10n('install_help'), PHPWG_URL.'/forum'),
      ));
}

//------------------------------------------------------ errors & infos display
if ($step == 1)
{
  $template->assign('install', true);
}
elseif ($step == 3)
{
  if (isset($error_copy))
  {
    array_push($errors, $error_copy);
  }
}
else
{
  array_push($infos, l10n('install_end_message'));

  if (isset($error_copy))
  {
    array_push($errors, $error_copy);
  }
  else
  {
    session_set_save_handler('pwg_session_open',
      'pwg_session_close',
      'pwg_session_read',
      'pwg_session_write',
      'pwg_session_destroy',
      'pwg_session_gc'
    );
    if ( function_exists('ini_set') )
    {
      ini_set('session.use_cookies', $conf['session_use_cookies']);
      ini_set('session.use_only_cookies', $conf['session_use_only_cookies']);
      ini_set('session.use_trans_sid', intval($conf['session_use_trans_sid']));
      ini_set('session.cookie_httponly', 1);
    }
    session_name($conf['session_name']);
    session_set_cookie_params(0, cookie_path());
    $user = build_user(1, true);
    log_user($user['id'], false);
  }

  $template->assign(
    'SUBSCRIBE_BASE_URL',
    get_newsletter_subscribe_base_url($language)
    );
}
if (count($errors) != 0)
{
  $template->assign('errors', $errors);
}

if (count($infos) != 0 )
{
  $template->assign('infos', $infos);
}

//----------------------------------------------------------- html code display
$template->pparse('install');
?>