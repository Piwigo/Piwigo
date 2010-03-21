<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

@include(PHPWG_ROOT_PATH .'local/config/database.inc.php');
if (!defined('PHPWG_INSTALLED'))
{
  header('Location: install.php');
  exit;
}

foreach( array(
  'array_intersect_key', //PHP 5 >= 5.1.0RC1
  'hash_hmac', //(hash) - enabled by default as of PHP 5.1.2
  'preg_last_error', // PHP 5 >= 5.2.0
  ) as $func)
{
  if (!function_exists($func))
  {
    include_once(PHPWG_ROOT_PATH . 'include/php_compat/'.$func.'.php');
  }
}

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');
include(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');

if(isset($conf['show_php_errors']) && !empty($conf['show_php_errors']))
{
  @ini_set('error_reporting', $conf['show_php_errors']);
  @ini_set('display_errors', true);
}

include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include( PHPWG_ROOT_PATH .'include/template.class.php');

// Database connection
$pwg_db_link = pwg_db_connect($conf['db_host'], $conf['db_user'],
			      $conf['db_password'], $conf['db_base'])
  or my_error('pwg_db_connect', true);

pwg_db_check_charset();

load_conf_from_db();
load_plugins();

include(PHPWG_ROOT_PATH.'include/user.inc.php');

if (in_array( substr($user['language'],0,2), array('fr','de','es','pl') ) )
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
define('PEM_URL', 'http://'.PHPWG_DOMAIN.'/ext');


// language files
load_language('common.lang');
if ( is_admin() || (defined('IN_ADMIN') and IN_ADMIN) )
{
  load_language('admin.lang');
}
trigger_action('loading_lang');
load_language('lang', PHPWG_ROOT_PATH.'local/', array('no_fallback'=>true, 'local'=>true) );

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
  $template = new Template(PHPWG_ROOT_PATH.'admin/themes', $conf['admin_theme']);
}
else
{ // Classic template
  $template = new Template(PHPWG_ROOT_PATH.'themes', $user['theme'] );
}

// The "No Photo Yet" feature: if you have no photo yet in your gallery, the
// gallery displays only a big box to show you the way for adding your first
// photos
if (
  !isset($conf['no_photo_yet'])             // the message disappears at first photo
  and !(defined('IN_ADMIN') and IN_ADMIN)   // no message inside administration
  and script_basename() != 'identification' // keep the ability to login
  )
{
  $query = '
SELECT
    COUNT(*)
  FROM '.IMAGES_TABLE.'
;';
  list($nb_photos) = pwg_db_fetch_row(pwg_query($query));
  if (0 == $nb_photos)
  {
    $template->set_filenames(array('no_photo_yet'=>'no_photo_yet.tpl'));

    $url = $conf['no_photo_yet_url'];
    if (substr($url, 0, 4) != 'http')
    {
      $url = get_root_url().$url;
    }
    
    $template->assign(array('next_step_url' => $url));
    $template->pparse('no_photo_yet');
    exit();
  }
  else
  {
    conf_update_param('no_photo_yet', 'false');
  }
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

if (is_adviser())
{
  $header_msgs[] = l10n('Adviser mode enabled');
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