<?php
/***************************************************************************
 *                            identification.php                           *
 *                            ------------------                           *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

//----------------------------------------------------------- personnal include
include_once( "./include/init.inc.php" );
//-------------------------------------------------------------- identification
$errors = array();
if ( isset( $_POST['login'] ) )
{
  // retrieving the encrypted password of the login submitted
  $query = 'SELECT password';
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= " WHERE username = '".$_POST['login']."';";
  $row = mysql_fetch_array( mysql_query( $query ) );
  if( $row['password'] == md5( $_POST['pass'] ) )
  {
    $session_id = session_create( $_POST['login'] );
    $url = 'category.php?id='.$session_id;
    header( 'Request-URI: '.$url );
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
  else
  {
    array_push( $errors, $lang['invalid_pwd'] );
  }
}
//----------------------------------------------------- template initialization
//
// Start output of page
//
$title = $lang['ident_page_title'];
include('include/page_header.php');

$handle = $vtp->Open( './template/default/identification.vtp' );
// language
$vtp->setGlobalVar( $handle, 'ident_title',      $lang['ident_title'] );
$vtp->setGlobalVar( $handle, 'login',            $lang['login'] );
$vtp->setGlobalVar( $handle, 'password',         $lang['password'] );
$vtp->setGlobalVar( $handle, 'submit',           $lang['submit'] );
$vtp->setGlobalVar( $handle, 'ident_guest_visit',$lang['ident_guest_visit'] );
$vtp->setGlobalVar( $handle, 'ident_register',   $lang['ident_register'] );
$vtp->setGlobalVar( $handle, 'ident_forgotten_password',
                    $lang['ident_forgotten_password'] );
// conf
$vtp->setGlobalVar( $handle, 'mail_webmaster',   $conf['mail_webmaster'] );
// user
$vtp->setGlobalVar( $handle, 'user_template',    $user['template'] );
initialize_template();
//-------------------------------------------------------------- errors display
if ( sizeof( $errors ) != 0 )
{
  $vtp->addSession( $handle, 'errors' );
  foreach ( $errors as $error ) {
    $vtp->addSession( $handle, 'li' );
    $vtp->setVar( $handle, 'li.li', $error );
    $vtp->closeSession( $handle, 'li' );
  }
  $vtp->closeSession( $handle, 'errors' );
}
//------------------------------------------------------------------ users list
// retrieving all the users login
$query = 'select username from '.PREFIX_TABLE.'users;';
$result = mysql_query( $query );
if ( mysql_num_rows ( $result ) < $conf['max_user_listbox'] )
{
  $vtp->addSession( $handle, 'select_field' );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( $row['username'] != 'guest' )
    {
      $vtp->addSession( $handle, 'option' );
      $vtp->setVar( $handle, 'option.option', $row['username'] );
      $vtp->closeSession( $handle, 'option' );
    }
  }
  $vtp->closeSession( $handle, 'select_field' );
}
else
{
  $vtp->addSession( $handle, 'text_field' );
  $vtp->closeSession( $handle, 'text_field' );
}
//-------------------------------------------------------------- visit as guest
if ( $conf['access'] == 'free' )
{
  $vtp->addSession( $handle, 'guest_visit' );
  $vtp->closeSession( $handle, 'guest_visit' );
}
//---------------------------------------------------------------- registration
if ( $conf['access'] == 'free' )
{
  $vtp->addSession( $handle, 'register' );
  $vtp->closeSession( $handle, 'register' );
}
//----------------------------------------------------------- html code display
$output.= $vtp->Display( $handle, 0 );
include('include/page_tail.php');
?>
