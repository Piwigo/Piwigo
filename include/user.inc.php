<?php
/***************************************************************************
 *                   user.inc.php is a part of PhpWebGallery               *
 *                            -------------------                          *
 *   last update          : Saturday, October 26, 2002                     *
 *   email                : pierrick@z0rglub.com                           *
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
$infos = array( 'id', 'pseudo', 'mail_address', 'nb_image_line',
                'nb_line_page', 'status', 'theme', 'language', 'maxwidth',
                'maxheight', 'expand', 'show_nb_comments', 'short_period',
                'long_period', 'template' );

$query_user  = 'select';
for ( $i = 0; $i < sizeof( $infos ); $i++ )
{
  if ( $i > 0 )
  {
    $query_user.= ',';
  }
  else
  {
    $query_user.= ' ';
  }
  $query_user.= $infos[$i];
}
$query_user.= ' from '.$prefixeTable.'users';
$query_done = false;
$user['is_the_guest'] = false;
if ( isset( $_GET['id'] )
     && ereg( "^[0-9a-zA-Z]{".$conf['session_id_size']."}$", $_GET['id'] ) )
{
  $page['session_id'] = $_GET['id'];
  $query = "select user_id, expiration, ip ";
  $query.= "from $prefixeTable"."sessions ";
  $query.= "where id = '".$_GET['id']."';";
  $result = mysql_query( $query );
  if ( mysql_num_rows( $result ) > 0 )
  {
    $row = mysql_fetch_array( $result );
    if ( $row['expiration'] < time() )
    {
      // deletion of the session from the database,
      // because it is out-of-date
      $delete_query = "delete from ".$prefixeTable."sessions";
      $delete_query.= " where id = ".$page['session_id'].";";
      mysql_query( $delete_query );
    }
    else
    {
      if ( $REMOTE_ADDR == $row['ip'] )
      {
        $query_user .= ' where id = '.$row['user_id'];
        $query_done = true;
      }
    }
  }
}
if ( !$query_done )
{
  $query_user .= " where pseudo = 'visiteur'";
  $user['is_the_guest'] = true;
}
$query_user .= ';';

$row = mysql_fetch_array( mysql_query( $query_user ) );

// affectation of each value retrieved in the users table into a variable
// of the array $user.
for ( $i = 0; $i < sizeof( $infos ); $i++ )
{
  $user[$infos[$i]] = $row[$infos[$i]];
  // If the field is true or false, the variable is transformed into a boolean
  // value.
  if ( $row[$infos[$i]] == 'true' || $row[$infos[$i]] == 'false' )
  {
    $user[$infos[$i]] = get_boolean( $row[$infos[$i]] );
  }
}
?>