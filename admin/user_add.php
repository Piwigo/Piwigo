<?php
/***************************************************************************
 *                               user_add.php                              *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include_once( './include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/user_add.vtp' );
$tpl = array( 'adduser_info_message', 'adduser_info_back',
              'adduser_fill_form', 'login', 'password', 'mail_address',
              'adduser_status', 'submit' );
templatize_array( $tpl, 'lang', $sub );
//--------------------------------------------------------- form criteria check
$error = array();
if ( isset( $_POST['submit'] ) )
{
  $error = register_user(
    $_POST['username'], $_POST['password'], $_POST['password'],
    $_POST['mail_address'], $_POST['status'] );
}
//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $vtp->addSession( $sub, 'errors' );
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $vtp->addSession( $sub, 'li' );
    $vtp->setVar( $sub, 'li.li', $error[$i] );
    $vtp->closeSession( $sub, 'li' );
  }
  $vtp->closeSession( $sub, 'errors' );
}
//---------------------------------------------------------------- confirmation
if ( sizeof( $error ) == 0 and isset( $_POST['submit'] ) )
{
  $vtp->addSession( $sub, 'confirmation' );
  $vtp->setVar( $sub, 'confirmation.username', $_POST['username'] );
  $url = add_session_id( './admin.php?page=user_list' );
  $vtp->setVar( $sub, 'confirmation.url', $url );
  $vtp->closeSession( $sub, 'confirmation' );
  // reset all values
  unset( $_POST );
}
//------------------------------------------------------------------------ form
$action = add_session_id( './admin.php?page=user_add' );
$vtp->setVar( $sub, 'form_action', $action );
$vtp->setVar( $sub, 'user:username',     $_POST['username'] );
$vtp->setVar( $sub, 'user:password',     $_POST['password'] );
$vtp->setVar( $sub, 'user:mail_address', $_POST['mail_address'] );

if ( !isset( $_POST['status'] ) )
{
  $_POST['status'] = 'guest';
}
$option = get_enums( $prefixeTable.'users', 'status' );
for ( $i = 0; $i < sizeof( $option ); $i++ )
{
  $vtp->addSession( $sub, 'status_option' );
  $vtp->setVar( $sub, 'status_option.value', $option[$i] );
  $vtp->setVar( $sub, 'status_option.option',
                $lang['adduser_status_'.$option[$i]] );
  if( $option[$i] == $_POST['status'] )
  {
    $vtp->setVar( $sub, 'status_option.selected', ' selected="selected"' );
  }
  $vtp->closeSession( $sub, 'status_option' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>