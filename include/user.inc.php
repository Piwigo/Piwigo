<?php
/***************************************************************************
 *                               user.inc.php                              *
 *                            ------------------                           *
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

// retrieving user informations
// $infos array is used to know the fields to retrieve in the table "users"
// Each field becomes an information of the array $user.
// Example :
//            status --> $user['status']
$infos = array( 'id', 'username', 'mail_address', 'nb_image_line',
                'nb_line_page', 'status', 'language', 'maxwidth',
                'maxheight', 'expand', 'show_nb_comments', 'short_period',
                'long_period', 'template', 'forbidden_categories' );

$query_user  = 'SELECT ';
foreach ( $infos as $i => $info ) {
  if ( $i > 0 ) $query_user.= ', ';
  $query_user.= $info;
}
$query_user.= ' FROM '.PREFIX_TABLE.'users';
$query_done = false;
$user['is_the_guest'] = false;

// cookie deletion if administrator don't authorize them anymore
if ( !$conf['authorize_cookies'] and isset( $_COOKIE['id'] ) )
{
  setcookie( 'id', '', 0, cookie_path() );
  $url = 'category.php';
  header( 'Request-URI: '.$url );  
  header( 'Content-Location: '.$url );  
  header( 'Location: '.$url );
  exit();
}

$user['has_cookie'] = false;
if     ( isset( $_GET['id']    ) ) $session_id = $_GET['id'];
elseif ( isset( $_COOKIE['id'] ) )
{
  $session_id = $_COOKIE['id'];
  $user['has_cookie'] = true;
}

if ( isset( $session_id )
     and ereg( "^[0-9a-zA-Z]{".$conf['session_id_size']."}$", $session_id ) )
{
  $page['session_id'] = $session_id;
  $query = 'SELECT user_id,expiration,ip';
  $query.= ' FROM '.PREFIX_TABLE.'sessions';
  $query.= " WHERE id = '".$page['session_id']."'";
  $query.= ';';
  $result = mysql_query( $query );
  if ( mysql_num_rows( $result ) > 0 )
  {
    $row = mysql_fetch_array( $result );
    if ( !$user['has_cookie'] )
    {
      if ( $row['expiration'] < time() )
      {
        // deletion of the session from the database,
        // because it is out-of-date
        $delete_query = 'DELETE FROM '.PREFIX_TABLE.'sessions';
        $delete_query.= " WHERE id = '".$page['session_id']."'";
        $delete_query.= ';';
        mysql_query( $delete_query );
      }
      else if ( $_SERVER['REMOTE_ADDR'] == $row['ip'] )
      {
        $query_user .= ' WHERE id = '.$row['user_id'];
        $query_done = true;
      }
    }
    else
    {
      $query_user .= ' WHERE id = '.$row['user_id'];
      $query_done = true;
    }
  }
}
if ( !$query_done )
{
  $query_user .= ' WHERE id = 2';
  $user['is_the_guest'] = true;
}
$query_user .= ';';
$row = mysql_fetch_array( mysql_query( $query_user ) );

// affectation of each value retrieved in the users table into a variable
// of the array $user.
foreach ( $infos as $info ) {
  // If the field is true or false, the variable is transformed into a
  // boolean value.
  if ( $row[$info] == 'true' or $row[$info] == 'false' )
  {
    $user[$info] = get_boolean( $row[$info] );
  }
  else if ( $info == 'forbidden_categories' )
  {
    $user[$info] = $row[$info];
    $user['restrictions'] = explode( ',', $row[$info] );
    if ( $user['restrictions'][0] == '' ) $user['restrictions'] = array();
  }
  else
  {
    $user[$info] = $row[$info];    
  }
}
?>