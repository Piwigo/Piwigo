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

if( !defined("IN_ADMIN") )
{
  die ("Hacking attempt!");
}

include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

$userdata = array();
if ( isset( $_POST['submituser'] ) )
{
  $userdata = getuserdata($_POST['username']);
  if (!$userdata) echo "Utilisateur inexistant";
}

if ( isset( $_POST['submit'] ) )
{
  // cleaning the user_access table for this user
  $query = 'DELETE FROM '.USER_ACCESS_TABLE;
  $query.= ' WHERE user_id = '.$_GET['user_id'];
  $query.= ';';
  pwg_query( $query );
  // selecting all private categories
  $query = 'SELECT id FROM '.CATEGORIES_TABLE;
  $query.= " WHERE status = 'private'";
  $query.= ';';
  $result = pwg_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $radioname = $row['id'];
    if ( $_POST[$radioname] == 0 )
    {
      $query = 'INSERT INTO '.USER_ACCESS_TABLE;
      $query.= ' (user_id,cat_id) VALUES';
      $query.= ' ('.$_GET['user_id'].','.$row['id'].')';
      $query.= ';';
      pwg_query ( $query );
    }
  }
  check_favorites( $_GET['user_id'] );
}

$user_id = (!empty($userdata['id']))?$userdata['id']:'';
$template->set_filenames( array('user'=>'admin/user_perm.tpl') );
$template->assign_vars(array(
  'L_SELECT_USERNAME'=>$lang['Select_username'],
  'L_LOOKUP_USER'=>$lang['Look_up_user'],
  'L_FIND_USERNAME'=>$lang['Find_username'],
  'L_AUTH_USER'=>$lang['permuser_only_private'],
  'L_SUBMIT'=>$lang['submit'],
  'L_AUTHORIZED'=>$lang['authorized'],
  'L_FORBIDDEN'=>$lang['forbidden'],
  'L_PARENT_FORBIDDEN'=>$lang['permuser_parent_forbidden'],

  'F_SEARCH_USER_ACTION' => add_session_id(PHPWG_ROOT_PATH.'admin.php?page=profile'),
  'F_AUTH_ACTION' => add_session_id(PHPWG_ROOT_PATH.'admin.php?page=profile&amp;user_id='.$user_id),
  'U_SEARCH_USER' => add_session_id(PHPWG_ROOT_PATH.'admin/search.php')
  ));

if (!$userdata)
{
  $template->assign_block_vars('search',array());
}
else
{
  $template->assign_block_vars('permission',array());
  $restrictions = get_user_restrictions( $userdata['id'], $userdata['status'],
                                  false, false );

  // only private categories are listed
  $query = 'SELECT id FROM '.CATEGORIES_TABLE;
  $query.= " WHERE status = 'private';";
  $result = pwg_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $is_user_allowed = is_user_allowed( $row['id'], $restrictions );
    $url = PHPWG_ROOT_PATH.'admin.php?page=cat_perm&amp;cat_id='.$row['id'];
    $cat_infos = get_cat_info( $row['id'] );
    $template->assign_block_vars('permission.category',array(
      'CAT_NAME'=> get_cat_display_name($cat_infos['name'],' &gt; ', 'font-weight:bold;' ),
	  'CAT_ID'=>$row['id'],
	  'AUTH_YES'=>!$is_user_allowed?'checked="checked"':'',
	  'AUTH_NO' =>$is_user_allowed?'checked="checked"':'',
	  'CAT_URL'=>add_session_id($url)
	));

    // any subcat forbidden for this user ?
    if ( $is_user_allowed == 2 )
    {
      $template->assign_block_vars('permission.category.parent_forbidden',array());
    }
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'user');
?>
