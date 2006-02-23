<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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


@include(PHPWG_ROOT_PATH .'include/mysql.inc.php');
if (!defined('PHPWG_INSTALLED'))
{
  header('Location: install.php');
  exit;
}

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');
include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'include/template.php');

// Database connection
mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
or die ( "Could not connect to database server" );
mysql_select_db( $cfgBase )
or die ( "Could not connect to database" );

if ($conf['check_upgrade_feed'])
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
    ob_start();// buffer output so that cookies work
    echo
      '<p>'
      .'Some database upgrades are missing, '
      .'<a href="'.PHPWG_ROOT_PATH.'upgrade_feed.php">upgrade now</a>'
      .'</p>'
      ;
  }
}

//
// Setup gallery wide options, if this fails then we output a CRITICAL_ERROR
// since basic gallery information is not available
//
$query = '
SELECT param,value
 FROM '.CONFIG_TABLE.'
;';
if (!($result = pwg_query($query)))
{
  die("Could not query config information");
}

while ( $row =mysql_fetch_array( $result ) )
{
  if ( isset( $row['value'] ) )
  {
    $conf[$row['param']] = $row['value'];
  }
  else
  {
    $conf[$row['param']] = '';
  }
  // If the field is true or false, the variable is transformed into a
  // boolean value.
  if ( $conf[$row['param']] == 'true' or $conf[$row['param']] == 'false' )
  {
    $conf[$row['param']] = get_boolean( $conf[$row['param']] );
  }
}

include(PHPWG_ROOT_PATH.'include/user.inc.php');

// language files
include_once(get_language_filepath('common.lang.php'));

if (defined('IN_ADMIN') and IN_ADMIN)
{
  include_once(get_language_filepath('admin.lang.php'));
}

if ($conf['gallery_locked'])
{
  ob_start(); // make sure we can send cookies
  echo
    '<div style="text-align:center;">'
    .$lang['gallery_locked_message'];
  echo '<a href="'.PHPWG_ROOT_PATH.'identification.php">.</a>';
  echo '</div>';

  if ( basename($_SERVER["PHP_SELF"]) != 'identification.php'
      and $user['status'] != 'admin' )
  {
    exit();
  }
}

// only now we can set the localized username of the guest user (and not in
// include/user.inc.php)
if ($user['is_the_guest'])
{
  $user['username'] = $lang['guest'];
}

// include template/theme configuration
list($user['template'], $user['theme']) = explode('/', $user['template']);
// TODO : replace initial $user['template'] by $user['layout']

include(
  PHPWG_ROOT_PATH
  .'template/'.$user['template']
  .'/theme/'.$user['theme']
  .'/themeconf.inc.php'
  );

// template instance
$template = new Template(PHPWG_ROOT_PATH.'template/'.$user['template']);
?>
