<?php
// +-----------------------------------------------------------------------+
// |                            group_list.php                             |
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
$sub = $vtp->Open( './template/'.$user['template'].'/admin/group_list.vtp' );
$tpl = array( 'group_add','add','listuser_permission','delete',
              'group_confirm','yes','no','group_list_title' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//-------------------------------------------------------------- delete a group
$error = array();
if ( isset ( $_GET['delete'] ) and is_numeric( $_GET['delete'] ) )
{
  $query = 'SELECT name';
  $query.= ' FROM '.PREFIX_TABLE.'groups';
  $query.= ' WHERE id = '.$_GET['delete'];
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );
  // confirm group deletion ?
  if ( !isset( $_GET['confirm'] ) or $_GET['confirm'] != 1 )
  {
    $vtp->addSession( $sub, 'deletion' );
    $vtp->setVar( $sub, 'deletion.name', $row['name'] );
    $yes_url = './admin.php?page=group_list&amp;delete='.$_GET['delete'];
    $yes_url.= '&amp;confirm=1';
    $vtp->setVar( $sub, 'deletion.yes_url', add_session_id( $yes_url ) );
    $no_url = './admin.php?page=group_list';
    $vtp->setVar( $sub, 'deletion.no_url', add_session_id( $no_url ) );
    $vtp->closeSession( $sub, 'deletion' );
  }
  // group deletion confirmed
  else
  {
    $vtp->addSession( $sub, 'confirmation' );
    $query = 'SELECT COUNT(*) AS nb_result';
    $query.= ' FROM '.PREFIX_TABLE.'groups';
    $query.= ' WHERE id = '.$_GET['delete'];
    $query.= ';';
    $row2 = mysql_fetch_array( pwg_query( $query ) );
    if ( $row2['nb_result'] > 0 )
    {
      delete_group( $_GET['delete'] );
      $vtp->setVar( $sub, 'confirmation.class', 'info' );
      $info = '"'.$row['name'].'" '.$lang['listuser_info_deletion'];
      $vtp->setVar( $sub, 'confirmation.info', $info );
    }
    else
    {
      $vtp->setVar( $sub, 'confirmation.class', 'erreur' );
      $vtp->setVar( $sub, 'confirmation.info', $lang['group_err_unknown'] );
    }
    $vtp->closeSession( $sub, 'confirmation' );
  }
}
//----------------------------------------------------------------- add a group
if ( isset( $_POST['submit'] ) )
{
  if ( preg_match( "/'/", $_POST['name'] )
       or preg_match( '/"/', $_POST['name'] ) )
  {
    array_push( $error, $lang['group_add_error1'] );
  }
  if ( count( $error ) == 0 )
  {
    // is the group not already existing ?
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'groups';
    $query.= " WHERE name = '".$_POST['name']."'";
    $query.= ';';
    $result = pwg_query( $query );
    if ( mysql_num_rows( $result ) > 0 )
    {
      array_push( $error, $lang['group_add_error2'] );
    }
  }
  if ( count( $error ) == 0 )
  {
    // creating the group
    $query = ' INSERT INTO '.PREFIX_TABLE.'groups';
    $query.= " (name) VALUES ('".$_POST['name']."')";
    $query.= ';';
    pwg_query( $query );
  }
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
//----------------------------------------------------------------- groups list
$vtp->addSession( $sub, 'groups' );

$query = 'SELECT id,name';
$query.= ' FROM '.PREFIX_TABLE.'groups';
$query.= ' ORDER BY id ASC';
$query.= ';';
$result = pwg_query( $query );
while ( $row = mysql_fetch_array( $result ) )
{
  $vtp->addSession( $sub, 'group' );
  $vtp->setVar( $sub, 'group.name', $row['name'] );
  $url = './admin.php?page=group_perm&amp;group_id='.$row['id'];
  $vtp->setVar( $sub, 'group.permission_url', add_session_id( $url ) );
  $url = './admin.php?page=group_list&amp;delete='.$row['id'];
  $vtp->setVar( $sub, 'group.deletion_url', add_session_id( $url ) );
  $vtp->closeSession( $sub, 'group' );
}

$vtp->closeSession( $sub, 'groups' );
//------------------------------------------------------- create new group form
$action = './admin.php?'.$_SERVER['QUERY_STRING'];
$vtp->setVar( $sub, 'form_action', $action );
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>
