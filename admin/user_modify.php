<?php
/***************************************************************************
 *                              user_modify.php                            *
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
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/user_modify.vtp' );
$error = array();
$tpl = array( 'adduser_info_message', 'adduser_info_back', 'adduser_fill_form',
              'login', 'new', 'password', 'mail_address', 'adduser_status',
              'submit', 'adduser_info_password_updated' );
templatize_array( $tpl, 'lang', $sub );
//--------------------------------------------------------- form criteria check
$error = array();
$display_form = true;

// retrieving information in the database about the user identified by its
// id in $_GET['user_id']
$query = 'select';
$query.= ' username,status,mail_address';
$query.= ' from '.PREFIX_TABLE.'users';
$query.= ' where id = '.$_GET['user_id'];
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );

// user is not modifiable if :
//   1. the selected user is the user "guest"
//   2. the selected user is the webmaster and the user making the modification
//      is not the webmaster
if ( $row['username'] == 'guest'
     or ( $row['username'] == $conf['webmaster']
          and $user['username'] != $conf['webmaster'] ) )
{
  array_push( $error, $lang['user_err_modify'] );
  $display_form = false;
}
// if the user was not found in the database, no modification possible
if ( $row['username'] == '' )
{
  array_push( $error, $lang['user_err_unknown'] );
  $display_form = false;
}

if ( sizeof( $error ) == 0 and isset( $_POST['submit'] ) )
{
  // shall we use a new password and overwrite the old one ?
  $use_new_password = false;
  if ( $_POST['use_new_pwd'] == 1)
  {
    $use_new_password = true;
  }
  $error = array_merge( $error, update_user(
                          $_GET['user_id'], $_POST['mail_address'],
                          $_POST['status'], $use_new_password,
                          $_POST['password'] ) );
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
  $vtp->setVar( $sub, 'confirmation.username', $row['username'] );
  $url = add_session_id( './admin.php?page=user_list' );
  $vtp->setVar( $sub, 'confirmation.url', $url );
  $vtp->closeSession( $sub, 'confirmation' );
  if ( $use_new_pwd )
  {
    $vtp->addSession( $sub, 'password_updated' );
    $vtp->closeSession( $sub, 'password_updated' );
  }
  $display_form = false;
}
//------------------------------------------------------------------------ form
if ( $display_form )
{
  $vtp->addSession( $sub, 'form' );
  $action = './admin.php?page=user_modify&amp;user_id='.$_GET['user_id'];
  $vtp->setVar( $sub, 'form.form_action', add_session_id( $action ) );
  $vtp->setVar( $sub, 'form.user:username',     $row['username'] );
  $vtp->setVar( $sub, 'form.user:password',     $_POST['password'] );
  $vtp->setVar( $sub, 'form.user:mail_address', $_POST['mail_address'] );

  if ( !isset( $_POST['status'] ) )
  {
    $_POST['status'] = 'guest';
  }
  $option = get_enums( PREFIX_TABLE.'users', 'status' );
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
  $url = add_session_id( './admin.php?page=user_list' );
  $vtp->setVar( $sub, 'form.url_back', $url );
  $vtp->closeSession( $sub, 'form' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>