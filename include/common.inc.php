<?php
// +-----------------------------------------------------------------------+
// |                            common.inc.php                             |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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
if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
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


include(PHPWG_ROOT_PATH .'include/mysql.inc.php');
if( !defined("PHPWG_INSTALLED") )
{
  header( 'Location: install.php' );
  exit;
}

define( 'PREFIX_INCLUDE', '' );// en attendant la migration complète
include(PHPWG_ROOT_PATH . 'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/config.inc.php');
include(PHPWG_ROOT_PATH . 'include/functions.inc.php');
include(PHPWG_ROOT_PATH . 'include/template.php');
include(PHPWG_ROOT_PATH . 'include/vtemplate.class.php');

//
// Database connection
//

mysql_connect( $dbhost, $dbuser, $dbpasswd )
or die ( "Could not connect to server" );
mysql_select_db( $dbname )
or die ( "Could not connect to database" );
	
//
// Obtain and encode users IP
//
if ( getenv( 'HTTP_X_FORWARDED_FOR' ) != '' )
{
  $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR );

  if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/",
                  getenv('HTTP_X_FORWARDED_FOR'), $ip_list) )
  {
    $private_ip = array( '/^0\./'
                         ,'/^127\.0\.0\.1/'
                         ,'/^192\.168\..*/'
                         ,'/^172\.16\..*/'
                         ,'/^10.\.*/'
                         ,'/^224.\.*/'
                         ,'/^240.\.*/'
      );
    $client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
  }
}
else
{
  $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR );
}
$user_ip = encode_ip($client_ip);

//
// Setup forum wide options, if this fails then we output a CRITICAL_ERROR
// since basic forum information is not available
//
$sql = 'SELECT * FROM '.CONFIG_TABLE;
if( !($result = mysql_query($sql)) )
{
  die("Could not query config information");
}

$row =mysql_fetch_array($result);
// rertieving the configuration informations for site
// $infos array is used to know the fields to retrieve in the table "config"
// Each field becomes an information of the array $conf.
// Example :
//            prefix_thumbnail --> $conf['prefix_thumbnail']
$infos = array( 'prefix_thumbnail', 'webmaster', 'mail_webmaster', 'access',
                'session_id_size', 'session_keyword', 'session_time',
                'max_user_listbox', 'show_comments', 'nb_comment_page',
                'upload_available', 'upload_maxfilesize', 'upload_maxwidth',
                'upload_maxheight', 'upload_maxwidth_thumbnail',
                'upload_maxheight_thumbnail','log','comments_validation',
                'comments_forall','authorize_cookies','mail_notification' );
// affectation of each field of the table "config" to an information of the
// array $conf.
foreach ( $infos as $info ) {
  if ( isset( $row[$info] ) ) $conf[$info] = $row[$info];
  else                        $conf[$info] = '';
  // If the field is true or false, the variable is transformed into a boolean
  // value.
  if ( $conf[$info] == 'true' or $conf[$info] == 'false' )
  {
    $conf[$info] = get_boolean( $conf[$info] );
  }
}

//---------------
// A partir d'ici il faudra dispatcher le code dans d'autres fichiers
//---------------

include(PHPWG_ROOT_PATH . 'include/user.inc.php');

// displaying the username in the language of the connected user, instead of
// "guest" as you can find in the database
if ( $user['is_the_guest'] ) $user['username'] = $lang['guest'];
include_once( './template/'.$user['template'].'/htmlfunctions.inc.php' );
define('PREFIX_TABLE', $table_prefix);
?>
