<?php
// +-----------------------------------------------------------------------+
// |                            user_modify.php                            |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+
include_once( './admin/include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( './template/'.$user['template'].'/admin/user_modify.vtp' );
$error = array();
$tpl = array( 'adduser_info_message', 'adduser_info_back', 'adduser_fill_form',
              'login', 'new', 'password', 'mail_address', 'adduser_status',
              'submit', 'adduser_info_password_updated','menu_groups',
              'dissociate','adduser_associate' );
templatize_array( $tpl, 'lang', $sub );
//--------------------------------------------------------- form criteria check
$error = array();
$display_form = true;

// retrieving information in the database about the user identified by its
// id in $_GET['user_id']
$query = 'select';
$query.= ' username,status,mail_address';
$query.= ' from '.USERS_TABLE;
$query.= ' where id = '.$_GET['user_id'];
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );
$page['username'] = $row['username'];
$page['status'] = $row['status'];
if ( !isset( $row['mail_address'] ) ) $row['mail_address'] = '';
$page['mail_address'] = $row['mail_address'];
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
  if ( isset( $_POST['use_new_pwd'] ) ) $use_new_password = true;
  // if we try to update the webmaster infos, we have to set the status to
  // 'admin'
  if ( $row['username'] == $conf['webmaster'] )
    $_POST['status'] = 'admin';

  $error = array_merge( $error, update_user(
                          $_GET['user_id'], $_POST['mail_address'],
                          $_POST['status'], $use_new_password,
                          $_POST['password'] ) );
}
// association with groups management
if ( isset( $_POST['submit'] ) )
{
  // deletion of checked groups
  $query = 'SELECT id,name';
  $query.= ' FROM '.PREFIX_TABLE.'groups';
  $query.= ' ORDER BY id ASC';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $dissociate = 'dissociate-'.$row['id'];
    if ( isset( $_POST[$dissociate] ) )
    {
      $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
      $query.= ' WHERE user_id = '.$_GET['user_id'];
      $query.= ' AND group_id ='.$row['id'];
      $query.= ';';
      mysql_query( $query );
    }
  }
  // create a new association between the user and a group
  $query = 'INSERT INTO '.PREFIX_TABLE.'user_group';
  $query.= ' (user_id,group_id) VALUES';
  $query.= ' ('.$_GET['user_id'].','.$_POST['associate'].')';
  $query.= ';';
  mysql_query( $query );
  // synchronize category informations for this user
  synchronize_user( $_GET['user_id'] );
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
  $vtp->setVar( $sub, 'confirmation.username', $page['username'] );
  $url = add_session_id( './admin.php?page=user_list' );
  $vtp->setVar( $sub, 'confirmation.url', $url );
  $vtp->closeSession( $sub, 'confirmation' );
  if ( $use_new_password )
  {
    $vtp->addSession( $sub, 'password_updated' );
    $vtp->closeSession( $sub, 'password_updated' );
  }
}
//------------------------------------------------------------------------ form
if ( $display_form )
{
  $vtp->addSession( $sub, 'form' );
  $action = './admin.php?page=user_modify&amp;user_id='.$_GET['user_id'];
  $vtp->setVar( $sub, 'form.form_action', add_session_id( $action ) );
  $vtp->setVar( $sub, 'form.user:username',     $page['username'] );
  if ( isset( $_POST['mail_address'] ) )
  {
    $page['mail_address'] = $_POST['mail_address'];
  }
  $vtp->setVar( $sub, 'form.user:mail_address', $page['mail_address'] );
  // change status only if the user is not the webmaster
  if ( $page['username'] != $conf['webmaster'] )
  {
    $vtp->addSession( $sub, 'status' );
    if ( isset( $_POST['status'] ) )
    {
      $page['status'] = $_POST['status'];
    }
    $option = get_enums( PREFIX_TABLE.'users', 'status' );
    for ( $i = 0; $i < sizeof( $option ); $i++ )
    {
      $vtp->addSession( $sub, 'status_option' );
      $vtp->setVar( $sub, 'status_option.value', $option[$i] );
      $vtp->setVar( $sub, 'status_option.option',
                    $lang['adduser_status_'.$option[$i]] );
      if( $option[$i] == $page['status'] )
      {
        $vtp->setVar( $sub, 'status_option.selected', ' selected="selected"' );
      }
      $vtp->closeSession( $sub, 'status_option' );
    }
    $vtp->closeSession( $sub, 'status' );
  }
  // groups linked with this user
  $query = 'SELECT id,name';
  $query.= ' FROM '.PREFIX_TABLE.'user_group, '.PREFIX_TABLE.'groups';
  $query.= ' WHERE group_id = id';
  $query.= ' AND user_id = '.$_GET['user_id'];
  $query.= ';';
  $result = mysql_query( $query );
  $user_groups = array();
  if ( mysql_num_rows( $result ) > 0 )
  {
    $vtp->addSession( $sub, 'groups' );
    while ( $row = mysql_fetch_array( $result ) )
    {
      $vtp->addSession( $sub, 'group' );
      $vtp->setVar( $sub, 'group.name', $row['name'] );
      $vtp->setVar( $sub, 'group.dissociate_id', $row['id'] );
      $vtp->closeSession( $sub, 'group' );
      array_push( $user_groups, $row['id'] );
    }
    $vtp->closeSession( $sub, 'groups' );
  }
  // empty group not to take into account
  $vtp->addSession( $sub, 'associate_group' );
  $vtp->setVar( $sub, 'associate_group.value', 'undef' );
  $vtp->setVar( $sub, 'associate_group.option', '' );
  $vtp->closeSession( $sub, 'associate_group' );
  // groups not linked yet to the user
  $query = 'SELECT id,name';
  $query.= ' FROM '.PREFIX_TABLE.'groups';
  $query.= ' ORDER BY id ASC';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( !in_array( $row['id'], $user_groups ) )
    {
      $vtp->addSession( $sub, 'associate_group' );
      $vtp->setVar( $sub, 'associate_group.value', $row['id'] );
      $vtp->setVar( $sub, 'associate_group.option', $row['name'] );
      $vtp->closeSession( $sub, 'associate_group' );
    }
  }

  $url = add_session_id( './admin.php?page=user_list' );
  $vtp->setVar( $sub, 'form.url_back', $url );
  $vtp->closeSession( $sub, 'form' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>
