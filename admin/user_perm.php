<?php
/***************************************************************************
 *                               user_perm.php                             *
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
include_once( './include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/user_perm.vtp' );
$error = array();
$tpl = array( 'permuser_authorized','permuser_forbidden','submit',
              'permuser_parent_forbidden','permuser_info_message',
              'adduser_info_back','permuser_only_private' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//--------------------------------------------------------------------- updates
if ( isset( $_POST['submit'] ) )
{
  // cleaning the user_access table for this user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE user_id = '.$_GET['user_id'];
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
      $query = 'INSERT INTO '.PREFIX_TABLE.'user_access';
      $query.= ' (user_id,cat_id) VALUES';
      $query.= ' ('.$_GET['user_id'].','.$row['id'].')';
      $query.= ';';
      mysql_query ( $query );
    }
  }
  check_favorites( $_GET['user_id'] );
  synchronize_user( $_GET['user_id'] );
  $vtp->addSession( $sub, 'confirmation' );
  $url = './admin.php?page=user_list';
  $vtp->setVar( $sub, 'confirmation.back_url', add_session_id( $url ) );
  $vtp->closeSession( $sub, 'confirmation' );
}
//---------------------------------------------------------------- form display
$restrictions = get_restrictions( $_GET['user_id'], $page['user_status'],
                                  false, false );
$action = './admin.php?page=user_perm&amp;user_id='.$_GET['user_id'];
$vtp->setVar( $sub, 'action', add_session_id( $action ) );
// Association of group_ids with group_names -> caching informations
$query = 'SELECT id,name';
$query.= ' FROM '.PREFIX_TABLE.'groups';
$query.= ';';
$result = mysql_query( $query );
$groups = array();
while ( $row = mysql_fetch_array( $result ) )
{
  $groups[$row['id']] = $row['name'];
}
// Listing of groups the user belongs to
$query = 'SELECT ug.group_id as groupid';
$query.= ' FROM '.PREFIX_TABLE.'user_group as ug';
$query.= ' WHERE user_id = '.$_GET['user_id'];
$query.= ';';
$result = mysql_query( $query );
$usergroups = array();
while ( $row = mysql_fetch_array( $result ) )
{
  array_push( $usergroups, $row['groupid'] );
}
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
  // we have to know whether the user is authorized to access this
  // category. The category can be accessible for this user thanks to his
  // personnal access rights OR thanks to the access rights of a group he
  // belongs to.
  // 1. group access :
  //    retrieving all authorized groups for this category and for this user
  $query = 'SELECT ga.group_id as groupid';
  $query.= ' FROM '.PREFIX_TABLE.'group_access as ga';
  $query.= ', '.PREFIX_TABLE.'user_group as ug';
  $query.= ' WHERE ga.group_id = ug.group_id';
  $query.= ' AND ug.user_id = '.$_GET['user_id'];
  $query.= ' AND cat_id = '.$row['id'];
  $query.= ';';
  $subresult = mysql_query( $query );
  $authorized_groups = array();
  while ( $subrow = mysql_fetch_array( $subresult ) )
  {
    array_push( $authorized_groups, $subrow['groupid'] );
  }
  // 2. personnal access
  $is_user_allowed = is_user_allowed( $row['id'], $restrictions );
  // link to the category permission management
  $url = './admin.php?page=cat_perm&amp;cat_id='.$row['id'];
  $vtp->setVar( $sub, 'category.cat_perm_link', add_session_id( $url ) );
  // color of the category : green if the user is allowed by himself or
  // thanks to a group he belongs to
  if ( $is_user_allowed == 0 or count( $authorized_groups ) > 0 )
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
  // usergroups
  if ( count( $usergroups ) > 0 )
  {
    $vtp->addSession( $sub, 'usergroups' );
    foreach ( $usergroups as $i => $usergroup ) {
      $vtp->addSession( $sub, 'usergroup' );
      $vtp->setVar( $sub, 'usergroup.name', $groups[$usergroup] );
      $url = './admin.php?page=group_perm&amp;group_id='.$usergroup;
      $vtp->setVar( $sub, 'usergroup.url', add_session_id( $url ) );
      if ( in_array( $usergroup, $authorized_groups ) )
      {
        $vtp->setVar( $sub, 'usergroup.color', 'green' );
      }
      else
      {
        $vtp->setVar( $sub, 'usergroup.color', 'red' );
      }
      if ( $i < count( $usergroups ) - 1 )
      {
        $vtp->setVar( $sub, 'usergroup.separation', ',' );
      }
      $vtp->closeSession( $sub, 'usergroup' );
    }
    $vtp->closeSession( $sub, 'usergroups' );
  }
  // any subcat forbidden for this user ?
  if ( $is_user_allowed == 2 )
  {
    $vtp->addSession( $sub, 'parent_forbidden' );
    $vtp->closeSession( $sub, 'parent_forbidden' );
  }
  // personnal forbidden or authorized access ?
  if ( $is_user_allowed == 0 )
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