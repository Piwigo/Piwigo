<?php
/***************************************************************************
 *                               group_perm.php                             *
 *                            ------------------                           *
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
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/group_perm.vtp' );
$error = array();
$tpl = array( 'permuser_authorized','permuser_forbidden','submit',
              'permuser_parent_forbidden','permuser_info_message',
              'adduser_info_back' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//--------------------------------------------------------------------- updates
if ( isset( $_POST['submit'] ) )
{
  // cleaning the user_access table for this group
  $query = 'DELETE FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE group_id = '.$_GET['group_id'];
  $query.= ';';
  mysql_query( $query );
  // selecting all private categories
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= " WHERE status = 'private'";
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $radioname = 'access-'.$row['id'];
    if ( $_POST[$radioname] == 0 )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'group_access';
      $query.= ' (group_id,cat_id) VALUES';
      $query.= ' ('.$_GET['group_id'].','.$row['id'].')';
      $query.= ';';
      mysql_query ( $query );
    }
  }
  $vtp->addSession( $sub, 'confirmation' );
  $url = './admin.php?page=group_list';
  $vtp->setVar( $sub, 'confirmation.back_url', add_session_id( $url ) );
  $vtp->closeSession( $sub, 'confirmation' );
}
//---------------------------------------------------------------- form display
$restrictions = get_group_restrictions( $_GET['group_id'] );
$action = './admin.php?page=group_perm&amp;group_id='.$_GET['group_id'];
$vtp->setVar( $sub, 'action', add_session_id( $action ) );
// only private categories are listed
$query = 'SELECT id';
$query.= ' FROM '.PREFIX_TABLE.'categories';
$query.= " WHERE status = 'private'";
$query.= ';';
$result = mysql_query( $query );
while ( $row = mysql_fetch_array( $result ) )
{
  $vtp->addSession( $sub, 'category' );
  $vtp->setVar( $sub, 'category.id', $row['id'] );
  // Is the group allowed to access this category
  $is_group_allowed = is_group_allowed( $row['id'], $restrictions );
  if ( $is_group_allowed == 0 )
  {
    $vtp->setVar( $sub, 'category.color', 'green' );
  }
  else
  {
    $vtp->setVar( $sub, 'category.color', 'red' );
  }
  // category name
  $cat_infos = get_cat_info( $row['id'] );
  $name = get_cat_display_name( $cat_infos['name'],' &gt; ',
                                'font-weight:bold;' );
  $vtp->setVar( $sub, 'category.name', $name );
  // any subcat forbidden for this group ?
  if ( $is_group_allowed == 2 )
  {
    $vtp->addSession( $sub, 'parent_forbidden' );
    $vtp->closeSession( $sub, 'parent_forbidden' );
  }
  // forbidden or authorized access ?
  if ( $is_group_allowed == 0 or $is_group_allowed == 2 )
  {
    $vtp->setVar( $sub, 'category.authorized_checked', ' checked="checked"' );
  }
  else
  {
    $vtp->setVar( $sub, 'category.forbidden_checked', ' checked="checked"' );
  }
  $vtp->closeSession( $sub, 'category' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>