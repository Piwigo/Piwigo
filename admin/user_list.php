<?php
/***************************************************************************
 *                 liste_users.php is a part of PhpWebGallery              *
 *                            -------------------                          *
 *   last update          : Tuesday, July 16, 2002                         *
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
include_once( './include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/user_list.vtp' );
// language
$vtp->setGlobalVar( $sub, 'listuser_confirm',  $lang['listuser_confirm'] );
$vtp->setGlobalVar( $sub, 'listuser_modify_hint',
                    $lang['listuser_modify_hint'] );
$vtp->setGlobalVar( $sub, 'listuser_modify', $lang['listuser_modify'] );
$vtp->setGlobalVar( $sub, 'listuser_permission',
                    $lang['listuser_permission'] );
$vtp->setGlobalVar( $sub, 'listuser_permission_hint',
                    $lang['listuser_permission_hint'] );
$vtp->setGlobalVar( $sub, 'listuser_delete_hint',
                    $lang['listuser_delete_hint'] );
$vtp->setGlobalVar( $sub, 'listuser_delete',   $lang['listuser_delete'] );
$vtp->setGlobalVar( $sub, 'yes',               $lang['yes'] );
$vtp->setGlobalVar( $sub, 'no',                $lang['no'] );
$vtp->setGlobalVar( $sub, 'listuser_button_all',
                    $lang['listuser_button_all'] );
$vtp->setGlobalVar( $sub, 'listuser_button_invert',
                    $lang['listuser_button_invert'] );
$vtp->setGlobalVar( $sub, 'listuser_button_create_address',
                    $lang['listuser_button_create_address'] );
//--------------------------------------------------------------- delete a user
if ( isset ( $_GET['delete'] ) && is_numeric( $_GET['delete'] ) )
{
  $query = 'select pseudo';
  $query.= ' from '.$prefixeTable.'users';
  $query.= ' where id = '.$_GET['delete'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  // confirm user deletion ?
  if ( $_GET['confirm'] != 1 )
  {
    $vtp->addSession( $sub, 'deletion' );
    $vtp->setVar( $sub, 'deletion.login', $row['pseudo'] );
    $yes_url = './admin.php?page=user_list&amp;delete='.$_GET['delete'];
    $yes_url.= '&amp;confirm=1';
    $vtp->setVar( $sub, 'deletion.yes_url', add_session_id( $yes_url ) );
    $no_url = './admin.php?page=user_list';
    $vtp->setVar( $sub, 'deletion.no_url', add_session_id( $no_url ) );
    $vtp->closeSession( $sub, 'deletion' );
  }
  // user deletion confirmed
  else
  {
    $vtp->addSession( $sub, 'confirmation' );
    if ( $row['pseudo'] != 'visiteur' && $row['pseudo'] != $conf['webmaster'] )
    {
      $query = 'select count(*) as nb_result';
      $query.= ' from '.$prefixeTable.'users';
      $query.= ' where id = '.$_GET['delete'];
      $query.= ';';
      $row2 = mysql_fetch_array( mysql_query( $query ) );
      if ( $row2['nb_result'] > 0 )
      {
        delete_user( $_GET['delete'] );
        $vtp->setVar( $sub, 'confirmation.class', 'info' );
        $info = '"'.$row['pseudo'].'" '.$lang['listuser_info_deletion'];
        $vtp->setVar( $sub, 'confirmation.info', $info );
      }
      else
      {
        $vtp->setVar( $sub, 'confirmation.class', 'erreur' );
        $vtp->setVar( $sub, 'confirmation.info', $lang['user_err_unknown'] );
      }
    }
    else
    {
      $vtp->setVar( $sub, 'confirmation.class', 'erreur' );
      $vtp->setVar( $sub, 'confirmation.info', $lang['user_err_modify'] );
    }
    $vtp->closeSession( $sub, 'confirmation' );
  }
}
//------------------------------------------------------------------ users list
else
{
  $vtp->addSession( $sub, 'users' );

  $action = './admin.php?'.$_SERVER['QUERY_STRING'];
  if ( !isset( $_GET['mail'] ) )
  {
    $action.= '&amp;mail=true';
  }
  $vtp->setVar( $sub, 'users.form_action', $action );

  $query = 'select id,pseudo,status,mail_address';
  $query.= ' from '.$prefixeTable.'users';
  $query.= ' order by status asc, pseudo asc';
  $query.= ';';
  $result = mysql_query( $query );

  $current_status = '';
  while ( $row = mysql_fetch_array( $result ) )
  {
    // display the line indicating the status of the next users
    if ( $row['status'] != $current_status )
    {
      if ( $current_status != '' )
      {
        $vtp->closeSession( $sub, 'category' );
      }
      $vtp->addSession( $sub, 'category' );
      $title = $lang['listuser_user_group'].' ';
      switch ( $row['status'] )
      {
      case 'admin' :
      {
        $title.= $lang['adduser_status_admin'];
        break;
      }
      case 'visiteur' :
      {
        $title.= $lang['adduser_status_guest'];
        break;
      }
      }
      $vtp->setVar( $sub, 'category.title', $title );
      $current_status = $row['status'];
    }
    $vtp->addSession( $sub, 'user' );
    // checkbox for mail management if the user has a mail address
    if ( $row['mail_address'] != '' && $row['pseudo'] != 'visiteur' )
    {
      $vtp->addSession( $sub, 'checkbox' );
      $vtp->setVar( $sub, 'checkbox.name', 'mail-'.$row['id'] );
      $vtp->closeSession( $sub, 'checkbox' );
    }
    // use a special color for the login of the user ?
    if ( $row['pseudo'] == $conf['webmaster'] )
    {
      $vtp->setVar( $sub, 'user.color', 'red' );
    }
    if ( $row['pseudo'] == "visiteur" )
    {
      $vtp->setVar( $sub, 'user.color', 'green' );
    }
    $vtp->setVar( $sub, 'user.login', $row['pseudo'] );
    // modify or not modify ?
    if ( $row['pseudo'] == "visiteur"
         || ( $row['pseudo'] == $conf['webmaster']
              && $user['pseudo'] != $conf['webmaster'] ) )
    {
      $vtp->addSession( $sub, 'not_modify' );
      $vtp->closeSession( $sub, 'not_modify' );
    }
    else
    {
      $vtp->addSession( $sub, 'modify' );
      $url = './admin.php?page=user_add&amp;mode=modif&amp;user_id=';
      $url.= $row['id'];
      $vtp->setVar( $sub, 'modify.url', add_session_id( $url ) );
      $vtp->setVar( $sub, 'modify.login', $row['pseudo'] );
      $vtp->closeSession( $sub, 'modify' );
    }
    // manage permission or not ?
    if ( $row['pseudo'] == $conf['webmaster'] )
    {
      $vtp->addSession( $sub, 'not_permission' );
      $vtp->closeSession( $sub, 'not_permission' );
    }
    else
    {
      $vtp->addSession( $sub, 'permission' );
      $url = './admin.php?page=perm&amp;user_id='.$row['id'];
      $vtp->setVar( $sub, 'permission.url', add_session_id( $url ) );
      $vtp->setVar( $sub, 'permission.login', $row['pseudo'] );
      $vtp->closeSession( $sub, 'permission' );
    }
    // is the user deletable or not ?
    if ( $row['pseudo'] == 'visiteur' || $row['pseudo'] == $conf['webmaster'] )
    {
      $vtp->addSession( $sub, 'not_delete' );
      $vtp->closeSession( $sub, 'not_delete' );
    }
    else
    {
      $vtp->addSession( $sub, 'delete' );
      $url = './admin.php?page=user_list&amp;delete='.$row['id'];
      $vtp->setVar( $sub, 'delete.url', add_session_id( $url ) );
      $vtp->setVar( $sub, 'delete.login', $row['pseudo'] );
      $vtp->closeSession( $sub, 'delete' );
    }
    $vtp->closeSession( $sub, 'user' );
  }
  $vtp->closeSession( $sub, 'category' );
  // mail management : creation of the mail address if asked by administrator
  if ( isset( $_GET['mail'] ) )
  {
    $mail_address = array();
    $i = 0;
    $query = 'select';
    $query.= ' id,mail_address';
    $query.= ' from '.$prefixeTable.'users';
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      $key = 'mail-'.$row['id'];
      if ( $_POST[$key] == 1 )
      {
        $mail_address[$i++] = $row['mail_address'];
      }
    }
    $mail_destination = '';
    for ( $i = 0; $i < sizeof( $mail_address ); $i++ )
    {
      $mail_destination.= $mail_address[$i];
      if ( sizeof( $mail_address ) > 1 )
      {
        $mail_destination.= ';';
      }
    }
    if ( sizeof( $mail_address ) > 0 )
    {
      $vtp->addSession( $sub, 'mail_link' );
      $vtp->setVar( $sub, 'mail_link.mailto', $mail_destination );
      $vtp->setVar( $sub, 'mail_link.mail_address_start',
                    substr( $mail_destination, 0, 50 ) );
      $vtp->closeSession( $sub, 'mail_link' );
    }
  }
  $vtp->closeSession( $sub, 'users' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>