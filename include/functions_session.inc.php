<?php
/***************************************************************************
 *                         functions_session.inc.php                       *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

// The function generate_key creates a string with pseudo random characters.
// the size of the string depends on the $conf['session_id_size'].
// Characters used are a-z A-Z and numerical values. Examples :
//                    "Er4Tgh6", "Rrp08P", "54gj"
// input  : none (using global variable)
// output : $key
function generate_key()
{
  global $conf;

  $md5 = md5( substr( microtime(), 2, 6 ).$conf['session_keyword'] );
  $init = '';
  for ( $i = 0; $i < strlen( $md5 ); $i++ )
  {
    if ( is_numeric( $md5[$i] ) ) $init.= $md5[$i];
  }
  $init = substr( $init, 0, 8 );
  mt_srand( $init );
  $key = '';
  for ( $i = 0; $i < $conf['session_id_size']; $i++ )
  {
    $c = mt_rand( 0, 2 );
    if ( $c == 0 )      $key .= chr( mt_rand( 65, 90 ) );
    else if ( $c == 1 ) $key .= chr( mt_rand( 97, 122 ) );
    else                $key .= mt_rand( 0, 9 );
  }
  return $key;
}

// The function create_session finds a non-already-used session key and
// returns it once found for the given user.
function session_create( $username )
{
  global $conf;
  // 1. searching an unused session key
  $id_found = false;
  while ( !$id_found )
  {
    $generated_id = generate_key();
    $query = 'select id';
    $query.= ' from '.PREFIX_TABLE.'sessions';
    $query.= " where id = '".$generated_id."';";
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) == 0 )
    {
      $id_found = true;
    }
  }
  // 2. retrieving id of the username given in parameter
  $query = 'select id';
  $query.= ' from '.PREFIX_TABLE.'users';
  $query.= " where username = '".$username."';";
  $row = mysql_fetch_array( mysql_query( $query ) );
  $user_id = $row['id'];
  // 3. inserting session in database
  $expiration = $conf['session_time'] * 60 + time();
  $query = 'insert into '.PREFIX_TABLE.'sessions';
  $query.= ' (id,user_id,expiration,ip) values';
  $query.= "('".$generated_id."','".$user_id;
  $query.= "','".$expiration."','".$_SERVER['REMOTE_ADDR']."');";
  mysql_query( $query );
                
  return $generated_id;
}

// add_session_id adds the id of the session to the string given in
// parameter as $url. If the session id is the first parameter to the url,
// it is preceded by a '?', else it is preceded by a '&amp;'. If the
// parameter $redirect is set to true, '&' is used instead of '&'.
function add_session_id( $url, $redirect = false )
{
  global $page, $user;

  if ( $user['has_cookie'] ) return $url;

  $amp = '&amp;';
  if ( $redirect )
  {
    $amp = '&';
  }
  if ( !$user['is_the_guest'] )
  {
    if ( preg_match( '/\.php\?/',$url ) )
    {
      return $url.$amp.'id='.$page['session_id'];
    }
    else
    {
      return $url.'?id='.$page['session_id'];
    }
  }
  else
  {
    return $url;
  }
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