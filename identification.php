<?php
/***************************************************************************
 *             identification.php is a part of PhpWebGallery               *
 *                            -------------------                          *
 *   last update          : Thursday, December 26, 2002                    *
 *   email                : pierrick@z0rglub.com                           *
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
$error = array();
if ( isset( $_POST['login'] ) )
{
  $i = 0;
  // retrieving the encrypted password of the login submitted
  $query = 'select password';
  $query.= ' from '.$prefixeTable.'users';
  $query.= " where pseudo = '".$_POST['login']."';";
  $row = mysql_fetch_array( mysql_query( $query ) );
  if( $row['password'] == md5( $_POST['pass'] ) )
  {
    $session_id = session_create( $_POST['login'] );
    $url = 'category.php?id='.$session_id;
    header( "Request-URI: $url" );
    header( "Content-Location: $url" );  
    header( "Location: $url" );
    exit();
  }
  else
  {
    $error[$i++] = $lang['invalid_pwd'];
  }
}
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/default/identification.vtp' );
// language
$vtp->setGlobalVar( $handle, 'ident_page_title', $lang['ident_page_title'] );
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
$vtp->setGlobalVar( $handle, 'page_style',       $user['style'] );
$vtp->setGlobalVar( $handle, 'user_theme',       $user['theme'] );
// structure
$vtp->setGlobalVar( $handle, 'frame_start',      get_frame_start() );
$vtp->setGlobalVar( $handle, 'frame_begin',      get_frame_begin() );
$vtp->setGlobalVar( $handle, 'frame_end',        get_frame_end() );
//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $vtp->addSession( $handle, 'errors' );
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $vtp->addSession( $handle, 'li' );
    $vtp->setVar( $handle, 'li.li', $error[$i] );
    $vtp->closeSession( $handle, 'li' );
  }
  $vtp->closeSession( $handle, 'errors' );
}
//------------------------------------------------------------------ users list
// retrieving all the users login
$query = 'select pseudo from '.$prefixeTable.'users;';
$result = mysql_query( $query );
if ( mysql_num_rows ( $result ) < $conf['max_user_listbox'] )
{
  $vtp->addSession( $handle, 'select_field' );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( $row['pseudo'] != 'visiteur' )
    {
      $vtp->addSession( $handle, 'option' );
      $vtp->setVar( $handle, 'option.option', $row['pseudo'] );
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
if ( $conf['acces'] == "libre" )
{
  $vtp->addSession( $handle, 'guest_visit' );
  $vtp->closeSession( $handle, 'guest_visit' );
}
//---------------------------------------------------------------- registration
if ( $conf['acces'] == "libre" )
{
  $vtp->addSession( $handle, 'register' );
  $vtp->closeSession( $handle, 'register' );
}
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
//------------------------------------------------------------ log informations
$query = 'insert into '.$prefixeTable.'history';
$query.= '(date,login,IP,page) values';
$query.= "('".time()."', '".$user['pseudo'];
$query.= "','$REMOTE_ADDR','identification');";
$result = mysql_query( $query );
?>