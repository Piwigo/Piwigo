<?php
/***************************************************************************
 *                               cat_perm.php                              *
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
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/cat_perm.vtp' );
$error = array();
$tpl = array( 'permuser_authorized','permuser_forbidden','menu_groups',
              'submit','menu_users','permuser_parent_forbidden' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//-------------------------------------------------------------- category infos
if ( isset( $_GET['cat_id'] ) )
{
  check_cat_id( $_GET['cat_id'] );
  if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
  {
    $result = get_cat_info( $page['cat'] );
    $page['cat_name']    = $result['name'];
    $page['id_uppercat'] = $result['id_uppercat'];
  }
}
//---------------------------------------------------------- permission updates
if ( isset( $_POST['submit'] ) )
{
  // groups access update
  $query = 'DELETE';
  $query.= ' FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE cat_id = '.$page['cat'];
  $query.= ';';
  mysql_query( $query );
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'groups';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $radioname = 'groupaccess-'.$row['id'];
    if ( $_POST[$radioname] == 0 )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'group_access';
      $query.= ' (cat_id,group_id) VALUES';
      $query.= ' ('.$page['cat'].','.$row['id'].')';
      $query.= ';';
      mysql_query( $query );
    }
  }
  // users access update
  $query = 'DELETE';
  $query.= ' FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE cat_id = '.$page['cat'];
  $query.= ';';
  mysql_query( $query );
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $radioname = 'useraccess-'.$row['id'];
    if ( $_POST[$radioname] == 0 )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'user_access';
      $query.= ' (cat_id,user_id) VALUES';
      $query.= ' ('.$page['cat'].','.$row['id'].')';
      $query.= ';';
      mysql_query( $query );
    }
    check_favorites( $row['id'] );
  }
  // echo "<div class=\"info\">".$lang['permuser_info_message']." [ <a href=\"".add_session_id_to_url( "./admin.php?page=cat" )."\">".$lang['editcat_back']."</a> ]</div>";
}
//---------------------------------------------------------------------- groups
$query = 'SELECT id,name';
$query.= ' FROM '.PREFIX_TABLE.'groups';
$query. ';';
$result = mysql_query( $query );
if ( mysql_num_rows( $result ) > 0 )
{
  $vtp->addSession( $sub, 'groups' );
  // creating an array with all authorized groups for this category
  $query = 'SELECT group_id';
  $query.= ' FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE cat_id = '.$_GET['cat_id'];
  $query.= ';';
  $subresult = mysql_query( $query );
  $authorized_groups = array();
  while ( $subrow = mysql_fetch_array( $subresult ) )
  {
    array_push( $authorized_groups, $subrow['group_id'] );
  }
  // displaying each group
  while( $row = mysql_fetch_array( $result ) )
  {
    $vtp->addSession( $sub, 'group' );
    if ( in_array( $row['id'], $authorized_groups ) )
    {
      $vtp->setVar( $sub, 'group.color', 'green' );
      $vtp->setVar( $sub, 'group.authorized_checked', ' checked="checked"' );
    }
    else
    {
      $vtp->setVar( $sub, 'group.color', 'red' );
      $vtp->setVar( $sub, 'group.forbidden_checked', ' checked="checked"' );
    }
    $vtp->setVar( $sub, 'group.groupname', $row['name'] );
    $vtp->setVar( $sub, 'group.id', $row['id'] );
    $url = './admin.php?page=group_perm&amp;group_id='.$row['id'];
    $vtp->setVar( $sub, 'group.group_perm_link', add_session_id( $url ) );
    $vtp->closeSession( $sub, 'group' );
  }
  $vtp->closeSession( $sub, 'groups' );
}
//----------------------------------------------------------------------- users
$query = 'SELECT id,username,status';
$query.= ' FROM '.PREFIX_TABLE.'users';
$query.= " WHERE username != '".$conf['webmaster']."'";
$query.= ';';
$result = mysql_query( $query );
while ( $row = mysql_fetch_array( $result ) )
{
  $vtp->addSession( $sub, 'user' );
  $vtp->setVar( $sub, 'user.id', $row['id'] );
  $url = add_session_id( './admin.php?page=user_perm&amp;user_id='.$row['id']);
  $vtp->setVar( $sub, 'user.user_perm_link', $url);
  if ( $row['username'] == 'guest' )
  {
    $row['username'] = $lang['guest'];
  }
  $vtp->setVar( $sub, 'user.username', $row['username'] );

  // for color of user : (red means access forbidden, green authorized) we
  // ask all forbidden categories, including the groups rights
  $restrictions = get_restrictions( $row['id'], $row['status'], false );
  $is_user_allowed = is_user_allowed( $page['cat'], $restrictions );
  if ( $is_user_allowed == 0 )
  {
    $vtp->setVar( $sub, 'user.color', 'green' );
  }
  else
  {
    $vtp->setVar( $sub, 'user.color', 'red' );
  }
  // for permission update button, we only ask forbidden categories for the
  // user, not taking into account the groups the user belongs to
  $restrictions = get_restrictions( $row['id'], $row['status'], false, false );
  $is_user_allowed = is_user_allowed( $page['cat'], $restrictions );
  if ( $is_user_allowed == 2 )
  {
    $vtp->addSession( $sub, 'parent_forbidden' );
    $url = './admin.php?page=cat_perm&amp;cat_id='.$page['id_uppercat'];
    $vtp->setVar( $sub, 'parent_forbidden.url', add_session_id( $url ) );
    $vtp->closeSession( $sub, 'parent_forbidden' );
  }
  if ( $is_user_allowed == 0 )
  {
    $vtp->setVar( $sub, 'user.authorized_checked', ' checked="checked"' );
  }
  else
  {
    $vtp->setVar( $sub, 'user.forbidden_checked', ' checked="checked"' );
  }
  // user's group(s)
  $query = 'SELECT g.name as groupname, g.id as groupid';
  $query.= ' FROM '.PREFIX_TABLE.'groups as g';
  $query.= ', '.PREFIX_TABLE.'user_group as ug';
  $query.= ' WHERE ug.group_id = g.id';
  $query.= ' AND ug.user_id = '.$row['id'];
  $query.= ';';
  $subresult = mysql_query( $query );
  if ( mysql_num_rows( $subresult ) > 0 )
  {
    $vtp->addSession( $sub, 'usergroups' );
    $i = 0;
    while( $subrow = mysql_fetch_array( $subresult ) )
    {
      $vtp->addSession( $sub, 'usergroup' );
      if ( in_array( $subrow['groupid'], $authorized_groups ) )
      {
        $vtp->setVar( $sub, 'usergroup.color', 'green' );
      }
      else
      {
        $vtp->setVar( $sub, 'usergroup.color', 'red' );
      }
      $vtp->setVar( $sub, 'usergroup.name', $subrow['groupname'] );
      if ( $i < mysql_num_rows( $subresult ) - 1 )
      {
        $vtp->setVar( $sub, 'usergroup.separation', ',' );
      }
      $vtp->closeSession( $sub, 'usergroup' );
      $i++;
    }
    $vtp->closeSession( $sub, 'usergroups' );
  }
  $vtp->closeSession( $sub, 'user' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>