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

/**
 * create a new session and returns the session identifier
 *
 * - find a non-already-used session key
 * - create a session in database
 * - return session identifier
 *
 * @param int userid
 * @param int session_lentgh : in seconds
 * @return string
 */
function session_create($userid, $session_length)
{
  global $conf;

  // 1. searching an unused session key
  $id_found = false;
  while (!$id_found)
  {
    $generated_id = generate_key($conf['session_id_size']);
    $query = '
SELECT id
  FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$generated_id.'\'
;';
    $result = pwg_query($query);
    if (mysql_num_rows($result) == 0)
    {
      $id_found = true;
    }
  }
  // 3. inserting session in database
  $query = '
INSERT INTO '.SESSIONS_TABLE.'
  (id,user_id,expiration)
  VALUES
  (\''.$generated_id.'\','.$userid.',
   ADDDATE(NOW(), INTERVAL '.$session_length.' SECOND))
;';
  pwg_query($query);

  $expiration = $session_length + time();
  setcookie('id', $generated_id, $expiration, cookie_path());
                
  return $generated_id;
}

// add_session_id adds the id of the session to the string given in
// parameter as $url. If the session id is the first parameter to the url,
// it is preceded by a '?', else it is preceded by a '&amp;'. If the
// parameter $redirect is set to true, '&' is used instead of '&'.
function add_session_id( $url, $redirect = false )
{
  global $page, $user, $conf;

  if ($user['is_the_guest']
      or $user['has_cookie']
      or $conf['apache_authentication'])
  {
    return $url;
  }

  if (preg_match('/\.php\?/', $url))
  {
    $separator = $redirect ? '&' : '&amp;';
  }
  else
  {
    $separator = '?';
  }

  return $url.$separator.'id='.$page['session_id'];
}

// cookie_path returns the path to use for the PhpWebGallery cookie.
// If PhpWebGallery is installed on :
// http://domain.org/meeting/gallery/category.php
// cookie_path will return : "/meeting/gallery"
function cookie_path()
{
  return substr($_SERVER['PHP_SELF'],0,strrpos( $_SERVER['PHP_SELF'],'/'));
}
?>
