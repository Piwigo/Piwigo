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
}
elseif (isset($_POST['falsify']) || isset($_POST['trueify']))
{
  $userdata = getuserdata(intval($_POST['userid']));
  // cleaning the user_access table for this user
  if (isset($_POST['cat_true']) && count($_POST['cat_true']) > 0)
  {
    foreach ($_POST['cat_true'] as $auth_cat)
	{
  	  $query = 'DELETE FROM '.USER_ACCESS_TABLE;
      $query.= ' WHERE user_id = '.$userdata['id'];
      $query.= ' AND cat_id='.$auth_cat.';';
      pwg_query ( $query );
	}
  }
  
  if (isset($_POST['cat_false']) && count($_POST['cat_false']) > 0)
  {
    foreach ($_POST['cat_false'] as $auth_cat)
	{
  	  $query = 'INSERT INTO '.USER_ACCESS_TABLE;
      $query.= ' (user_id,cat_id) VALUES';
      $query.= ' ('.$userdata['id'].','.$auth_cat.')';
      $query.= ';';
      pwg_query ( $query );
	}
  }
}

//----------------------------------------------------- template initialization

if ( empty($userdata))
{
  $template->set_filenames( array('user'=>'admin/user_perm.tpl') );
  $template->assign_vars(array(
    'L_SELECT_USERNAME'=>$lang['Select_username'],
    'L_LOOKUP_USER'=>$lang['Look_up_user'],
    'L_FIND_USERNAME'=>$lang['Find_username'],
    'L_AUTH_USER'=>$lang['permuser_only_private'],
    'L_SUBMIT'=>$lang['submit'],

    'F_SEARCH_USER_ACTION' => add_session_id(PHPWG_ROOT_PATH.'admin.php?page=user_perm'),
    'U_SEARCH_USER' => add_session_id(PHPWG_ROOT_PATH.'admin/search.php')
    ));
}
else
{
  $cat_url = '<a href="'.add_session_id(PHPWG_ROOT_PATH.'admin.php?page=cat_options&section=status');
  $cat_url .= '">'.$lang['permuser_info_link'].'</a>';
  $template->set_filenames( array('user'=>'admin/cat_options.tpl') );
  $template->assign_vars(array(
    'L_RESET'=>$lang['reset'],
    'L_CAT_OPTIONS_TRUE'=>$lang['authorized'],
    'L_CAT_OPTIONS_FALSE'=>$lang['forbidden'],
    'L_CAT_OPTIONS_INFO'=>$lang['permuser_info'].'&nbsp;'.$cat_url,
	
	'HIDDEN_NAME'=> 'userid',
	'HIDDEN_VALUE'=>$userdata['id'],
    'F_ACTION' => add_session_id(PHPWG_ROOT_PATH.'admin.php?page=user_perm'),
    ));


  // only private categories are listed
  $query_true = 'SELECT id,name,uppercats,global_rank FROM '.CATEGORIES_TABLE;
  $query_true.= ' LEFT JOIN '.USER_ACCESS_TABLE.' as u';
  $query_true.= ' ON u.cat_id=id';
  $query_true.= ' WHERE status = \'private\' AND u.user_id='.$userdata['id'].';';
  $result = pwg_query($query_true);
  $categorie_true = array();
  while (!empty($result) && $row = mysql_fetch_array($result))
  {
    array_push($categorie_true, $row);
  }
  
  $query = 'SELECT id,name,uppercats,global_rank FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE status = \'private\'';
  $result = pwg_query($query);
  $categorie_false = array();
  while ($row = mysql_fetch_array($result))
  {
    if (!in_array($row,$categorie_true))
	  array_push($categorie_false, $row);
  }
  usort($categorie_true, 'global_rank_compare');
  usort($categorie_false, 'global_rank_compare');
  display_select_categories($categorie_true, array(), 'category_option_true', true);
  display_select_categories($categorie_false, array(), 'category_option_false', true);
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'user');
?>
