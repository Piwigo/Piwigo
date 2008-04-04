<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}
// determine the initial instant to indicate the generation time of this page
$t1 = explode( ' ', microtime() );
$t2 = explode( '.', $t1[0] );
$t2 = $t1[1].'.'.$t2[1];

set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

//
// addslashes to vars if magic_quotes_gpc is off this is a security
// precaution to prevent someone trying to break out of a SQL statement.
//
if( !get_magic_quotes_gpc() )
{
  if( is_array( $_GET ) )
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
include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'include/template.class.php');

// Database connection
mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
or die ( "Could not connect to database server" );
mysql_select_db( $cfgBase )
or die ( "Could not connect to database" );

defined('PWG_CHARSET') and defined('DB_CHARSET')
  or die('PWG_CHARSET and/or DB_CHARSET is not defined');
if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') )
{
  if (DB_CHARSET!='')
  {
    pwg_query('SET NAMES "'.DB_CHARSET.'"');
  }
}
else
{
  if ( strtolower(PWG_CHARSET)!='iso-8859-1' )
  {
    die('PWG supports only iso-8859-1 charset on MySql version '.mysql_get_server_info());
  }
}

//
// Setup gallery wide options, if this fails then we output a CRITICAL_ERROR
// since basic gallery information is not available
//
load_conf_from_db();
load_plugins();

include(PHPWG_ROOT_PATH.'include/user.inc.php');


// language files
load_language('common.lang');
if (defined('IN_ADMIN') and IN_ADMIN)
{
  load_language('admin.lang');
}
trigger_action('loading_lang');
load_language('local.lang');

// only now we can set the localized username of the guest user (and not in
// include/user.inc.php)
if (is_a_guest())
{
  $user['username'] = l10n('guest');
}

// template instance
if
  (
      defined('IN_ADMIN') and IN_ADMIN and
      isset($user['admin_template']) and
      isset($user['admin_theme'])
  )
{
  // Admin template
  $template = new Template(PHPWG_ROOT_PATH.'template/'.$user['admin_template'], $user['admin_theme'] );
}
else
{
  // Classic template
  $template = new Template(PHPWG_ROOT_PATH.'template/'.$user['template'], $user['theme'] );
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
    echo l10n('gallery_locked_message')
      .'<a href="'.get_absolute_root_url(false).'identification.php">.</a>';
    exit();
  }
}

if ($conf['check_upgrade_feed']
    and defined('PHPWG_IN_UPGRADE')
    and PHPWG_IN_UPGRADE)
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
  if (count(array_diff($existing, $applied)) > 0)
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
  include(PHPWG_ROOT_PATH.'include/functions_filter.inc.php');
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
add_event_handler('render_category_description', 'render_category_description');
add_event_handler('render_comment_content', 'htmlspecialchars');
add_event_handler('render_comment_content', 'parse_comment_content');
add_event_handler('render_comment_author', 'strip_tags');
trigger_action('init');
?>
