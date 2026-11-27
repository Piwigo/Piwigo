<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');

if (isset($_GET['configuration']))
{
  include_once(PHPWG_ROOT_PATH.'install/install_extra.php');
  exit();
}

// copied from include/common.inc.php
if (!function_exists('get_magic_quotes_gpc') or !@get_magic_quotes_gpc() )
{
  function sanitize_mysql_kv(&$v, $k)
  {
    $v = addslashes($v);
  }

  if( is_array( $_GET ) )
  {
    array_walk_recursive( $_GET, 'sanitize_mysql_kv' );
  }
  if( is_array( $_POST ) )
  {
    array_walk_recursive( $_POST, 'sanitize_mysql_kv' );
  }
  if( is_array( $_COOKIE ) )
  {
    array_walk_recursive( $_COOKIE, 'sanitize_mysql_kv' );
  }
}

//----------------------------------------------------- variable initialization

define('DEFAULT_PREFIX_TABLE', 'piwigo_');

if (isset($_POST['install']) && isset($_POST['prefix']) && is_string($_POST['prefix']))
{
  $prefixeTable = $_POST['prefix'];
}
else
{
  $prefixeTable = DEFAULT_PREFIX_TABLE;
}

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');

include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'include/template.class.php');

$config_file = PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php';
if (@file_exists($config_file))
{
  include($config_file);
  // Is Piwigo already installed ?
  if (defined("PHPWG_INSTALLED"))
  {
    die('Piwigo is already installed');
  }
}

include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'admin/include/functions.php');

include(PHPWG_ROOT_PATH . 'admin/include/languages.class.php');
include(PHPWG_ROOT_PATH . 'install/install.class.php');

//--------------------------------------------------------------------- language
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

$piwigo_domains = array(
  'fr_FR' => 'fr.piwigo.org',
  'it_IT' => 'it.piwigo.org',
  'de_DE' => 'de.piwigo.org',
  'es_ES' => 'es.piwigo.org',
  'pl_PL' => 'pl.piwigo.org',
  'zh_CN' => 'cn.piwigo.org',
  'ru_RU' => 'ru.piwigo.org',
  'nl_NL' => 'nl.piwigo.org',
  'tr_TR' => 'tr.piwigo.org',
  'da_DK' => 'da.piwigo.org',
  'pt_BR' => 'br.piwigo.org',
  );
define('PHPWG_DOMAIN', $piwigo_domains[$language] ?? 'piwigo.org');
define('PHPWG_URL', 'https://'.PHPWG_DOMAIN);

load_language('common.lang', '', array('language' => $language, 'target_charset'=>'utf-8'));
load_language('admin.lang', '', array('language' => $language, 'target_charset'=>'utf-8'));
load_language('install.lang', '', array('language' => $language, 'target_charset'=>'utf-8'));

$language_options = array();
foreach ($languages->fs_languages as $language_code => $fs_language)
{
  $language_options[$language_code] = $fs_language['name'];
}

//--------------------------------------------------------------------- template
$conf['gallery_title'] = 'Piwigo';

$template = new Template(PHPWG_ROOT_PATH.'themes', 'standard_pages');
$template->set_filenames(
  array(
    'header' => 'header.tpl',
    'install' => 'install.tpl',
    'tail' => 'footer.tpl',
    )
  );

//------------------------------------------------------------------------- form
$is_submitted = isset($_POST['install']);

$params = array('language' => $language);
if ($is_submitted)
{
  foreach (array('dbhost', 'dbuser', 'dbpasswd', 'dbname', 'prefix', 'admin_name', 'admin_pass1', 'admin_pass2', 'admin_mail') as $field)
  {
    if (isset($_POST[$field]) && is_string($_POST[$field]))
    {
      $params[$field] = $_POST[$field];
    }
  }
}

$install = new PwgInstall($params);

$is_installed = false;
if ($is_submitted && $install->check() && $install->connect())
{
  $is_installed = $install->create_database()
    && $install->create_users()
    && $install->mark_upgrades_applied()
    && $install->write_config();
}

if ($is_installed)
{
  pwg_activity('system', ACTIVITY_SYSTEM_CORE, 'install', array('version'=>PHPWG_VERSION));
  conf_update_param('setup_step', 'extensions');

  // See include/functions_session.inc.php
  session_set_save_handler(new PwgSession());
  if ( function_exists('ini_set') )
  {
    ini_set('session.use_cookies', $conf['session_use_cookies']);
    ini_set('session.use_only_cookies', $conf['session_use_only_cookies']);
    ini_set('session.use_trans_sid', intval($conf['session_use_trans_sid']));
    ini_set('session.cookie_httponly', 1);
  }
  session_name($conf['session_name']);
  session_set_cookie_params(0, cookie_path());
  register_shutdown_function('session_write_close');

  // we don't load user cache because since Piwigo 15.4.0 the calculation of user
  // cache requires $logger which is not instanciated
  $user = build_user(1, false);
  log_user($user['id'], false);
  $_SESSION['connected_with'] = 'pwg_ui';

  $user['preferences']['show_whats_new_'.get_branch_from_version(PHPWG_VERSION)] = false;

  // newsletter subscription
  if (isset($_POST['newsletter_subscribe']))
  {
    fetchRemote(
      get_newsletter_subscribe_base_url($language).$install->params['admin_mail'],
      $result,
      array(),
      array('origin' => 'installation')
      );

    $user['preferences']['show_newsletter_subscription'] = false;
  }

  userprefs_save();

  // email notification
  if (isset($_POST['send_credentials_by_mail']))
  {
    include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

    $keyargs_content = array(
      get_l10n_args('Hello %s,', $install->params['admin_name']),
      get_l10n_args('Welcome to your new installation of Piwigo!', ''),
      get_l10n_args('', ''),
      get_l10n_args('Here are your connection settings', ''),
      get_l10n_args('', ''),
      get_l10n_args('Link: %s', get_absolute_root_url()),
      get_l10n_args('Username: %s', $install->params['admin_name']),
      get_l10n_args('Password: ********** (no copy by email)', ''),
      get_l10n_args('Email: %s', $install->params['admin_mail']),
      get_l10n_args('', ''),
      get_l10n_args('Don\'t hesitate to consult our forums for any help: %s', PHPWG_URL),
      );

    pwg_mail(
      $install->params['admin_mail'],
      array(
        'subject' => l10n('Just another Piwigo gallery'),
        'content' => l10n_args($keyargs_content),
        'content_format' => 'text/plain',
        )
      );
  }

  header('Location: install.php?configuration');
  exit();
}

//----------------------------------------------------------------------- output
$help_link = 'https://doc.piwigo.org';
if ('fr' == substr($language, 0, 2))
{
  $help_link = 'https://doc-fr.piwigo.org';
}

$forum_urls = array(
  'fr' => 'https://fr.piwigo.org/forum',
  'de' => 'https://de.piwigo.org/forum',
  );
$forum_url = $forum_urls[substr($language, 0, 2)] ?? 'https://piwigo.org/forum';

$template->assign(
  array(
    'CONTENT_ENCODING' => 'utf-8',
    'PAGE_TITLE' => l10n('Installation'),
    'SHOW_MOBILE_APP_BANNER' => false,
    'BODY_ID' => 'theInstallPage',
    'BODY_CLASSES' => array(),
    'BODY_DATA' => json_encode(array()),
    'COOKIE_PATH' => cookie_path(),
    'HELP_LINK' => $help_link,
    'RELEASE' => PHPWG_VERSION,
    'F_ACTION' => 'install.php?language='.$language,
    'language_options' => $language_options,
    'language_selection' => $language,
    'requirements' => $install->check_requirements(),
    'CAN_INSTALL' => $install->can_install(),
    'errors' => $install->errors,
    'IS_FIRST_DISPLAY' => !$is_submitted && !isset($_GET['language']),
    'F_DB_HOST' => $install->params['dbhost'],
    'F_DB_USER' => $install->params['dbuser'],
    'F_DB_NAME' => $install->params['dbname'],
    'F_DB_PREFIX' => $install->params['prefix'],
    'F_ADMIN' => $install->params['admin_name'],
    'F_ADMIN_EMAIL' => $install->params['admin_mail'],
    'F_NEWSLETTER_SUBSCRIBE' => $is_submitted ? isset($_POST['newsletter_subscribe']) : true,
    'F_SEND_CREDENTIALS' => $is_submitted ? isset($_POST['send_credentials_by_mail']) : true,
    'EMAIL' => '<span class="adminEmail">'.htmlspecialchars($install->params['admin_mail']).'</span>',
    'L_INSTALL_HELP' => l10n('Need help ? Ask your question on <a href="%s">Piwigo message board</a>.', $forum_url),
    )
  );

header('Content-Type: text/html; charset=UTF-8');
$template->parse('header');
$template->parse('install');
$template->parse('tail');
$template->p();
?>
