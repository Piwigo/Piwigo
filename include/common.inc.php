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

defined('PHPWG_ROOT_PATH') or trigger_error('Hacking attempt!', E_USER_ERROR);

// determine the initial instant to indicate the generation time of this page
$t1 = explode( ' ', microtime() );
$t2 = explode( '.', $t1[0] );
$t2 = $t1[1].'.'.$t2[1];

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
$page = array();
$user = array();
$lang = array();
$header_msgs = array();
$header_notes = array();
$filter = array();

@include(PHPWG_ROOT_PATH .'include/mysql.inc.php');
if (!defined('PHPWG_INSTALLED'))
{
  header('Location: install.php');
  exit;
}

foreach( array(
  'array_intersect_key', //PHP 5 >= 5.1.0RC1
  'hash_hmac', //(hash) - enabled by default as of PHP 5.1.2
  'preg_last_error', // PHP 5 >= 5.2.0
  'file_put_contents', //PHP5
  ) as $func)
{
  if (!function_exists($func))
  {
    include_once(PHPWG_ROOT_PATH . 'include/php_compat/'.$func.'.php');
  }
}

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');

if(isset($conf['show_php_errors']) && !empty($conf['show_php_errors']))
{
  @ini_set('error_reporting', $conf['show_php_errors']);
  @ini_set('display_errors', true);
}

include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include( PHPWG_ROOT_PATH .'include/template.class.php');

// Database connection
@mysql_connect( $cfgHote, $cfgUser, $cfgPassword ) or my_error( 'mysql_connect', true );
@mysql_select_db( $cfgBase ) or my_error( 'mysql_select_db', true );

defined('PWG_CHARSET') and defined('DB_CHARSET')
  or fatal_error('PWG_CHARSET and/or DB_CHARSET is not defined');
if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') )
{
  if (DB_CHARSET!='')
  {
    pwg_query('SET NAMES "'.DB_CHARSET.'"');
  }
}
elseif ( strtolower(PWG_CHARSET)!='iso-8859-1' )
{
  fatal_error('PWG supports only iso-8859-1 charset on MySql version '.mysql_get_server_info());
}

load_conf_from_db();
load_plugins();

include(PHPWG_ROOT_PATH.'include/user.inc.php');

if ('fr_FR' == $user['language']) {
  define('PHPWG_DOMAIN', 'fr.piwigo.org');
}
else if ('it_IT' == $user['language']) {
  define('PHPWG_DOMAIN', 'it.piwigo.org');
}
else if ('de_DE' == $user['language']) {
  define('PHPWG_DOMAIN', 'de.piwigo.org');
}
else if ('es_ES' == $user['language']) {
  define('PHPWG_DOMAIN', 'es.piwigo.org');
}
else if ('pl_PL' == $user['language']) {
  define('PHPWG_DOMAIN', 'pl.piwigo.org');
}
else if ('zh_CN' == $user['language']) {
  define('PHPWG_DOMAIN', 'cn.piwigo.org');
}
else {
  define('PHPWG_DOMAIN', 'piwigo.org');
}
define('PHPWG_URL', 'http://'.PHPWG_DOMAIN);
define('PEM_URL', 'http://'.PHPWG_DOMAIN.'/ext');


// language files
load_language('common.lang');
if ( is_admin() || (defined('IN_ADMIN') and IN_ADMIN) )
{
  load_language('admin.lang');
}
trigger_action('loading_lang');
load_language('local.lang', '', array('no_fallback'=>true) );

// only now we can set the localized username of the guest user (and not in
// include/user.inc.php)
if (is_a_guest())
{
  $user['username'] = l10n('guest');
}

// template instance
if (( defined('IN_ADMIN') and IN_ADMIN )
    or (defined('PWG_HELP') and PWG_HELP))
{// Admin template
  list($user['admin_template'], $user['admin_theme']) =
    explode ('/', $conf['admin_layout']);
  $template = new Template(PHPWG_ROOT_PATH.'admin/template/'
    . $user['admin_template'], $user['admin_theme'] );
}
else
{ // Classic template
  $template = new Template(PHPWG_ROOT_PATH.'template/'
    . $user['template'], $user['theme'] );
}

if (isset($user['internal_status']['guest_must_be_guest'])
    and
    $user['internal_status']['guest_must_be_guest'] === true)
{
  $header_msgs[] = l10n('guest_must_be_guest');
}

if ($conf['gallery_locked'])
{
  $header_msgs[] = l10n('gallery_locked_message');

  if ( script_basename() != 'identification' and !is_admin() )
  {
    set_status_header(503, 'Service Unavailable');
    @header('Retry-After: 900');
    header('Content-Type: text/html; charset='.get_pwg_charset());
    echo '<a href="'.get_absolute_root_url(false).'identification.php">'.l10n('gallery_locked_message').'</a>';
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

if (is_adviser())
{
  $header_msgs[] = l10n('adviser_mode_enabled');
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
add_event_handler('render_comment_content', 'htmlspecialchars');
add_event_handler('render_comment_content', 'parse_comment_content');
add_event_handler('render_comment_author', 'strip_tags');
add_event_handler('blockmanager_register_blocks', 'register_default_menubar_blocks', EVENT_HANDLER_PRIORITY_NEUTRAL-1);
trigger_action('init');
?>
