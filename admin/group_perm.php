<?php
// +-----------------------------------------------------------------------+
// |                            group_perm.php                             |
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
$sub = $vtp->Open( './template/'.$user['template'].'/admin/group_perm.vtp' );
$error = array();
$tpl = array( 'permuser_authorized','permuser_forbidden','submit',
              'permuser_parent_forbidden','permuser_info_message',
              'adduser_info_back','permuser_only_private' );
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
  // checking users favorites
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    check_favorites( $row['id'] );
  }
  // synchronization of calculated data
  synchronize_group( $_GET['group_id'] );
  // confirmation display
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
  $url = './admin.php?page=cat_perm&amp;cat_id='.$row['id'];
  $vtp->setVar( $sub, 'category.cat_perm_link', add_session_id( $url ) );
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
