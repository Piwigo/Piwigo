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
$tpl = array( 'user_err_modify', 'user_err_unknown' );
templatize_array( $tpl, 'lang' );
//--------------------------------------------------------- form criteria check
$display_form = true;

$query = 'select';
$query.= ' username,status,mail_address';
$query.= ' from '.$prefixeTable.'users';
$query.= ' where id = '.$_GET['user_id'];
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );

$username     = $row['username'];
$status       = $row['status'];
$mail_address = $row['mail_address'];

if ( $username == 'guest'
     or ( $username == $conf['webmaster']
          and $user['username'] != $conf['webmaster'] ) )
{
  $vtp->addSession( $sub, 'err_modify' );
  $vtp->closeSession( $sub, 'err_modify' );
  $display_form = false;
}
if ( $username == '' )
{
  $vtp->addSession( $sub, 'err_unknown' );
  $vtp->closeSession( $sub, 'err_unknown' );
  $display_form = false;
}

if ( $display_form and isset( $_POST['submit'] ) )
{
  $use_new_password = false;
  if ( $_POST['use_new_pwd'] == 1)
  {
    $use_new_password = true;
  }
  $error = update_user(
    $_GET['user_id'], $_POST['mail_address'], $_POST['status'],
    $use_new_password, $POST['password'] );
}
?>