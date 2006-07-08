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

// The function generate_key creates a string with pseudo random characters.
// the size of the string depends on the $conf['session_id_size'].
// Characters used are a-z A-Z and numerical values. Examples :
//                    "Er4Tgh6", "Rrp08P", "54gj"
// input  : none (using global variable)
// output : $key
function generate_key($size)
{
  global $conf;

  $md5 = md5(substr(microtime(), 2, 6));
  $init = '';
  for ( $i = 0; $i < strlen( $md5 ); $i++ )
  {
    if ( is_numeric( $md5[$i] ) ) $init.= $md5[$i];
  }
  $init = substr( $init, 0, 8 );
  mt_srand( $init );
  $key = '';
  for ( $i = 0; $i < $size; $i++ )
  {
    $c = mt_rand( 0, 2 );
    if ( $c == 0 )      $key .= chr( mt_rand( 65, 90 ) );
    else if ( $c == 1 ) $key .= chr( mt_rand( 97, 122 ) );
    else                $key .= mt_rand( 0, 9 );
  }
  return $key;
}

if (isset($conf['session_save_handler'])
  and ($conf['session_save_handler'] == 'db')
  and defined('PHPWG_INSTALLED'))
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
  }
  session_name( $conf['session_name'] );
  session_set_cookie_params(
      ini_get('session.cookie_lifetime'),
      cookie_path()
    );
}

// cookie_path returns the path to use for the PhpWebGallery cookie.
// If PhpWebGallery is installed on :
// http://domain.org/meeting/gallery/category.php
// cookie_path will return : "/meeting/gallery"
function cookie_path()
{
  if ( isset($_SERVER['REDIRECT_SCRIPT_NAME']) and 
       !empty($_SERVER['REDIRECT_SCRIPT_NAME']) )
  {
    $scr = $_SERVER['REDIRECT_SCRIPT_NAME'];
  }
  else if ( isset($_SERVER['REDIRECT_URL']) )
  { // mod_rewrite is activated for upper level directories. we must set the
    // cookie to the path shown in the browser otherwise it will be discarded.
    if ( isset($_SERVER['PATH_INFO']) and !empty($_SERVER['PATH_INFO']) )
    {
      $idx = strpos( $_SERVER['REDIRECT_URL'], $_SERVER['PATH_INFO'] );
      if ($idx !== false)
      {
        $scr = substr($_SERVER['REDIRECT_URL'], 0, $idx);
      }
      else
      {//this should never happen
        $scr='//';
      }
    }
    else
    {
      $scr = $_SERVER['REDIRECT_URL'];
    }
  }
  else
  {
    $scr = $_SERVER['SCRIPT_NAME'];
  }
  $scr = substr($scr,0,strrpos( $scr,'/'));
  // add a trailing '/' if needed
  return ($scr{strlen($scr)-1} == '/') ? $scr : $scr . '/';
}

/**
 * returns true; used when the session_start() function is called
 *
 * @params not use but useful for php engine
 */
function pwg_session_open($path, $name)
{
  return true;
}

/**
 * returns true; used when the session is closed (unset($_SESSION))
 *
 */
function pwg_session_close()
{
  return true;
}

/**
 * this function returns
 * a string corresponding to the value of the variable save in the session
 * or an empty string when the variable doesn't exist
 *
 * @param string session id
 */
function pwg_session_read($session_id)
{
  $query = '
SELECT data
  FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$session_id.'\'
;';
  $result = pwg_query($query);
  if ($result)
  {
    $row = mysql_fetch_assoc($result);
    return $row['data'];
  }
  else
  {
    return '';
  }
}

/**
 * returns true; writes set a variable in the active session
 *
 * @param string session id
 * @data string value of date to be saved
 */
function pwg_session_write($session_id, $data)
{
  $query = '
UPDATE '.SESSIONS_TABLE.'
  SET expiration = now(),
  data = \''.$data.'\'
  WHERE id = \''.$session_id.'\'
;';
  pwg_query($query);
  if ( mysql_affected_rows()>0 )
  {
    return true;
  }
  $query = '
INSERT INTO '.SESSIONS_TABLE.'
  (id,data,expiration)
  VALUES(\''.$session_id.'\',\''.$data.'\',now())
;';
  mysql_query($query);
  return true;
}

/**
 * returns true; delete the active session
 *
 * @param string session id
 */
function pwg_session_destroy($session_id)
{
  $query = '
DELETE
  FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$session_id.'\'
;';
  pwg_query($query);
  return true;
}

/**
 * returns true; delete expired sessions
 * called each time a session is closed.
 */
function pwg_session_gc()
{
  global $conf;

  $query = '
DELETE
  FROM '.SESSIONS_TABLE.'
  WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(expiration) > '
  .$conf['session_length'].'
;';
  pwg_query($query);
  return true;
}
?>
