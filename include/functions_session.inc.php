<?php
/***************************************************************************
 *                         functions_session.inc.php                       *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
function generate_key()
{
  global $conf;
  $md5 = md5( substr( microtime(), 2, 6 ).$conf['session_keyword'] );
  $init = '';
  for ( $i = 0; $i < strlen( $md5 ); $i++ )
  {
    if ( is_numeric( $md5[$i] ) )
    {
      $init.= $md5[$i];
    }
  }
  $init = substr( $init, 0, 8 );
  mt_srand( $init );
  $key = '';
  for ( $i = 0; $i < $conf['session_id_size']; $i++ )
  {
    $c = mt_rand( 0, 2 );
    if ( $c == 0 )
    {
      $key .= chr( mt_rand( 65, 90 ) );
    }
    else if ( $c == 1 )
    {
      $key .= chr( mt_rand( 97, 122 ) );
    }
    else
    {
      $key .= mt_rand( 0, 9 );
    }
  }
  return $key;
}
        
function session_create( $username )
{
  global $conf;
  // 1. searching an unused sesison key
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
?>