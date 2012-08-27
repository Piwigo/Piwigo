<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

defined('PHPWG_ROOT_PATH') or trigger_error('Hacking attempt!', E_USER_ERROR);

// determine the initial instant to indicate the generation time of this page
$t2 = microtime(true);

@set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

//
// addslashes to vars if magic_quotes_gpc is off this is a security
// precaution to prevent someone trying to break out of a SQL statement.
//
if( !@get_magic_quotes_gpc() )
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
if ( !empty($_SERVER["PATH_INFO"]) )
{
  $_SERVER["PATH_INFO"] = addslashes($_SERVER["PATH_INFO"]);
}

//
// Define some basic configuration arrays this also prevents malicious
// rewriting of language and otherarray values via URI params
//
$conf = array();
$page = array(
  'infos' => array(),
  'errors' => array(),
  );
$user = array();
$lang = array();
$header_msgs = array();
$header_notes = array();
$filter = array();

foreach( array(
  'array_intersect_key', //PHP 5 >= 5.1.0RC1
  'hash_hmac', //(hash) - enabled by default as of PHP 5.1.2
  'preg_last_error', // PHP 5 >= 5.2.0
  'json_encode', // PHP 5 >= 5.2.0
  ) as $func)
{
  if (!function_exists($func))
  {
    include_once(PHPWG_ROOT_PATH . 'include/php_compat/'.$func.'.php');
  }
}

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');

defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');

@include(PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php');
if (!defined('PHPWG_INSTALLED'))
{
  header('Location: install.php');
  exit;
}
include(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');

if(isset($conf['show_php_errors']) && !empty($conf['show_php_errors']))
{
  @ini_set('error_reporting', $conf['show_php_errors']);
  @ini_set('display_errors', true);
}

include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');


// Database connection
try
{
  $pwg_db_link = pwg_db_connect($conf['db_host'], $conf['db_user'],
                                $conf['db_password'], $conf['db_base']);
}
catch (Exception $e)
{
  my_error(l10n($e->getMessage()), true);
}

pwg_db_check_charset();

load_conf_from_db();

if (!$conf['check_upgrade_feed'])
{
  if (!isset($conf['piwigo_db_version']) or $conf['piwigo_db_version'] != get_branch_from_version(PHPWG_VERSION))
  {
    redirect(get_root_url().'upgrade.php');
  }
}

ImageStdParams::load_from_db();

load_plugins();

// users can have defined a custom order pattern, incompatible with GUI form
if (isset($conf['order_by_custom']))
{
  $conf['order_by'] = $conf['order_by_custom'];
}
if (isset($conf['order_by_inside_category_custom']))
{
  $conf['order_by_inside_category'] = $conf['order_by_inside_category_custom'];
}

include(PHPWG_ROOT_PATH.'include/user.inc.php');

if (in_array( substr($user['language'],0,2), array('fr','it','de','es','pl','hu','ru','nl') ) )
{
  define('PHPWG_DOMAIN', substr($user['language'],0,2).'.piwigo.org');
}
elseif ('zh_CN' == $user['language']) {
  define('PHPWG_DOMAIN', 'cn.piwigo.org');
}
else {
  define('PHPWG_DOMAIN', 'piwigo.org');
}
define('PHPWG_URL', 'http://'.PHPWG_DOMAIN);

if(isset($conf['alternative_pem_url']) and $conf['alternative_pem_url']!='')
{
  define('PEM_URL', $conf['alternative_pem_url']);
}
else
{
  define('PEM_URL', 'http://'.PHPWG_DOMAIN.'/ext');
}

// language files
load_language('common.lang');
if ( is_admin() || (defined('IN_ADMIN') and IN_ADMIN) )
{
  load_language('admin.lang');
}
trigger_action('loading_lang');
load_language('lang', PHPWG_ROOT_PATH.PWG_LOCAL_DIR, array('no_fallback'=>true, 'local'=>true) );

// only now we can set the localized username of the guest user (and not in
// include/user.inc.php)
if (is_a_guest())
{
  $user['username'] = l10n('guest');
}

// template instance
if (defined('IN_ADMIN') and IN_ADMIN )
{// Admin template
  $template = new Template(PHPWG_ROOT_PATH.'admin/themes', $conf['admin_theme']);
}
else
{ // Classic template
  $theme = $user['theme'];
  if (script_basename() != 'ws' and mobile_theme())
  {
    $theme = $conf['mobile_theme'];
  }
  $template = new Template(PHPWG_ROOT_PATH.'themes', $theme );
}

if ( !isset($conf['no_photo_yet']) )
{
  include(PHPWG_ROOT_PATH.'include/no_photo_yet.inc.php');
}

if (isset($user['internal_status']['guest_must_be_guest'])
    and
    $user['internal_status']['guest_must_be_guest'] === true)
{
  $header_msgs[] = l10n('Bad status for user "guest", using default status. Please notify the webmaster.');
}

if ($conf['gallery_locked'])
{
  $header_msgs[] = l10n('The gallery is locked for maintenance. Please, come back later.');

  if ( script_basename() != 'identification' and !is_admin() )
  {
    set_status_header(503, 'Service Unavailable');
    @header('Retry-After: 900');
    header('Content-Type: text/html; charset='.get_pwg_charset());
    echo '<a href="'.get_absolute_root_url(false).'identification.php">'.l10n('The gallery is locked for maintenance. Please, come back later.').'</a>';
    echo str_repeat( ' ', 512); //IE6 doesn't error output if below a size
    exit();
  }
}

if ($conf['check_upgrade_feed'])
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');
  if (check_upgrade_feed())
  {
    $header_msgs[] = 'Some database upgrades are missing, '
      .'<a href="'.get_absolute_root_url(false).'upgrade_feed.php">upgrade now</a>';
  }
}

if (count($header_msgs) > 0)
{
  $template->assign('header_msgs', $header_msgs);
  $header_msgs=array();
}

if (!empty($conf['filter_pages']) and get_filter_page_value('used'))
{
  include(PHPWG_ROOT_PATH.'include/filter.inc.php');
}
else
{
  $filter['enabled'] = false;
}

if (isset($conf['header_notes']))
{
  $header_notes = array_merge($header_notes, $conf['header_notes']);
}

// default event handlers
add_event_handler('render_category_literal_description', 'render_category_literal_description');
if ( !$conf['allow_html_descriptions'] )
{
  add_event_handler('render_category_description', 'nl2br');
}
add_event_handler('render_comment_content', 'render_comment_content');
add_event_handler('render_comment_author', 'strip_tags');
add_event_handler('render_tag_url', 'str2url');
add_event_handler('blockmanager_register_blocks', 'register_default_menubar_blocks', EVENT_HANDLER_PRIORITY_NEUTRAL-1);
trigger_action('init');
?>
