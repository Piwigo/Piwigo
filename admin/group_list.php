<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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
if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

//-------------------------------------------------------------- delete a group
$error = array();
if ( isset( $_POST['delete'] ) && isset( $_POST['confirm_delete'] )  )
{
  // destruction of the access linked to the group
  $query = 'DELETE FROM '.GROUP_ACCESS_TABLE;
  $query.= ' WHERE group_id = '.$_POST['group_id'];
  $query.= ';';
  pwg_query( $query );
	
	// destruction of the users links for this group
  $query = 'DELETE FROM ' . USER_GROUP_TABLE; 
  $query.= ' WHERE group_id = '.$_POST['group_id'];
	pwg_query( $query );
	
	// destruction of the group
	$query = 'DELETE FROM ' . GROUPS_TABLE; 
  $query.= ' WHERE id = '.$_POST['group_id'];
	$query.= ';';
	pwg_query( $query );
}
//----------------------------------------------------------------- add a group
elseif ( isset( $_POST['new'] ) )
{
  if ( empty($_POST['newgroup']) || preg_match( "/'/", $_POST['newgroup'] )
       or preg_match( '/"/', $_POST['newgroup'] ) )
  {
    array_push( $error, $lang['group_add_error1'] );
  }
  if ( count( $error ) == 0 )
  {
    // is the group not already existing ?
    $query = 'SELECT id FROM '.GROUPS_TABLE;
    $query.= " WHERE name = '".$_POST['newgroup']."'";
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
    $query = ' INSERT INTO '.GROUPS_TABLE;
    $query.= " (name) VALUES ('".$_POST['newgroup']."')";
    $query.= ';';
    pwg_query( $query );
  }
}
//--------------------------------------------------------------- user management
elseif ( isset( $_POST['add'] ) )
{
  $userdata = getuserdata($_POST['username']);
  if (!$userdata) echo "Utilisateur inexistant";
	
	// create a new association between the user and a group
  $query = 'INSERT INTO '.USER_GROUP_TABLE;
  $query.= ' (user_id,group_id) VALUES';
  $query.= ' ('.$userdata['id'].','.$_POST['edit_group_id'].')';
  $query.= ';';
  pwg_query( $query );
}
elseif (isset( $_POST['deny_user'] ))
{
  $sql_in = '';
	$members = $_POST['members'];
	for($i = 0; $i < count($members); $i++)
  {
    $sql_in .= ( ( $sql_in != '' ) ? ', ' : '' ) . intval($members[$i]);
  }
  $query = 'DELETE FROM ' . USER_GROUP_TABLE; 
  $query.= ' WHERE user_id IN ('.$sql_in;
	$query.= ') AND group_id = '.$_POST['edit_group_id'];
	pwg_query( $query );
}
//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error[$i]));
  }
}
//----------------------------------------------------------------- groups list

$query = 'SELECT id,name FROM '.GROUPS_TABLE;
$query.= ' ORDER BY id ASC;';
$result = pwg_query( $query );
$groups_display = '<select name="group_id">';
$groups_nb=0;
while ( $row = mysql_fetch_array( $result ) )
{
  $groups_nb++;
	$selected = '';
	if (isset($_POST['group_id']) && $_POST['group_id']==$row['id'])
		$selected = 'selected';
  $groups_display .= '<option value="' . $row['id'] . '" '.$selected.'>' . $row['name']  . '</option>';
}
$groups_display .= '</select>';

$action = PHPWG_ROOT_PATH.'admin.php?page=group_list';
//----------------------------------------------------- template initialization
$template->set_filenames( array('groups'=>'admin/group_list.tpl') );
$tpl = array( 'group_add','add','listuser_permission','delete',
              'group_confirm','yes','no','group_list_title' );

$template->assign_vars(array(
  'S_GROUP_SELECT'=>$groups_display,
	
  'L_GROUP_SELECT'=>$lang['group_list_title'],
	'L_GROUP_CONFIRM'=>$lang['group_confirm_delete'],
	'L_LOOK_UP'=>$lang['edit'],
	'L_GROUP_DELETE'=>$lang['delete'],
  'L_CREATE_NEW_GROUP'=>$lang['group_add'],
  'L_GROUP_EDIT'=>$lang['group_edit'],
	'L_USER_NAME'=>$lang['login'],
	'L_USER_EMAIL'=>$lang['mail_address'],
	'L_USER_SELECT'=>$lang['Select'],
	'L_DENY_SELECTED'=>$lang['group_deny_user'],
	'L_ADD_MEMBER'=>$lang['group_add_user'],
  'L_FIND_USERNAME'=>$lang['Find_username'],
	
	'S_GROUP_ACTION'=>add_session_id($action),
	'U_SEARCH_USER' => add_session_id(PHPWG_ROOT_PATH.'admin/search.php')
	));

if ($groups_nb) 
{
  $template->assign_block_vars('select_box',array());
}

//----------------------------------------------------------------- add a group
if ( isset( $_POST['edit']) || isset( $_POST['add']) || isset( $_POST['deny_user'] ))
{
  // Retrieving the group name
	$query = 'SELECT id, name FROM '.GROUPS_TABLE;
  $query.= " WHERE id = '".$_POST['group_id']."'";
  $query.= ';';
  $result = mysql_fetch_array(pwg_query( $query ));
  $template->assign_block_vars('edit_group',array(
	  'GROUP_NAME'=>$result['name'],
		'GROUP_ID'=>$result['id']
		));
		
  // Retrieving all the users
	$query = 'SELECT id, username, mail_address';
	$query.= ' FROM ('.USERS_TABLE.' as u';
	$query.= ' LEFT JOIN '.USER_GROUP_TABLE.' as ug ON ug.user_id=u.id)';
  $query.= " WHERE ug.group_id = '".$_POST['group_id']."';";
	$result = pwg_query( $query );
	$i=0;
	while ( $row = mysql_fetch_array( $result ) )
	{
	  $class = ($i % 2)? 'row1':'row2'; $i++;
	  $template->assign_block_vars('edit_group.user',array(
		  'ID'=>$row['id'],
			'NAME'=>$row['username'],
			'EMAIL'=>$row['mail_address'],
			'T_CLASS'=>$class
		));
	}
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'groups');
?>
