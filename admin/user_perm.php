<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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

if (!defined('IN_ADMIN'))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

$userdata = array();
if (isset($_POST['submituser']))
{
  $userdata = getuserdata($_POST['username']);
}
else if (isset($_GET['user_id']))
{
  $userdata = getuserdata(intval($_GET['user_id']));
}
else if (isset($_POST['falsify'])
         and isset($_POST['cat_true'])
         and count($_POST['cat_true']) > 0)
{
  $userdata = getuserdata(intval($_POST['userid']));
  // if you forbid access to a category, all sub-categories become
  // automatically forbidden
  $subcats = get_subcat_ids($_POST['cat_true']);
  $query = '
DELETE FROM '.USER_ACCESS_TABLE.'
  WHERE user_id = '.$userdata['id'].'
    AND cat_id IN ('.implode(',', $subcats).')
;';
  pwg_query($query);
}
else if (isset($_POST['trueify'])
         and isset($_POST['cat_false'])
         and count($_POST['cat_false']) > 0)
{
  $userdata = getuserdata(intval($_POST['userid']));
    
  $uppercats = get_uppercat_ids($_POST['cat_false']);
  $private_uppercats = array();

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $uppercats).')
    AND status = \'private\'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($private_uppercats, $row['id']);
  }

  // retrying to authorize a category which is already authorized may cause
  // an error (in SQL statement), so we need to know which categories are
  // accesible
  $authorized_ids = array();
    
  $query = '
SELECT cat_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE user_id = '.$userdata['id'].'
;';
  $result = pwg_query($query);
  
  while ($row = mysql_fetch_array($result))
  {
    array_push($authorized_ids, $row['cat_id']);
  }
  
  $inserts = array();
  $to_autorize_ids = array_diff($private_uppercats, $authorized_ids);
  foreach ($to_autorize_ids as $to_autorize_id)
  {
    array_push($inserts, array('user_id' => $userdata['id'],
                               'cat_id' => $to_autorize_id));
  }

  mass_inserts(USER_ACCESS_TABLE, array('user_id','cat_id'), $inserts);
}
//----------------------------------------------------- template initialization
if (empty($userdata))
{
  $template->set_filenames(array('user' => 'admin/user_perm.tpl'));

  $base_url = PHPWG_ROOT_PATH.'admin.php?page=';
  
  $template->assign_vars(array(
    'L_SELECT_USERNAME'=>$lang['Select_username'],
    'L_LOOKUP_USER'=>$lang['Look_up_user'],
    'L_FIND_USERNAME'=>$lang['Find_username'],
    'L_AUTH_USER'=>$lang['permuser_only_private'],
    'L_SUBMIT'=>$lang['submit'],

    'F_SEARCH_USER_ACTION' => add_session_id($base_url.'user_perm'),
    'U_SEARCH_USER' => add_session_id(PHPWG_ROOT_PATH.'admin/search.php')
    ));
}
else
{
  $template->set_filenames(array('user'=>'admin/cat_options.tpl'));
  $template->assign_vars(
    array(
      'L_RESET'=>$lang['reset'],
      'L_CAT_OPTIONS_TRUE'=>$lang['authorized'],
      'L_CAT_OPTIONS_FALSE'=>$lang['forbidden'],
      'L_CAT_OPTIONS_INFO'=>$lang['permuser_info'],
      
      'HIDDEN_NAME'=> 'userid',
      'HIDDEN_VALUE'=>$userdata['id'],
      'F_ACTION' => add_session_id(PHPWG_ROOT_PATH.'admin.php?page=user_perm'),
      ));

  // only private categories are listed
  $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.USER_ACCESS_TABLE.' ON cat_id = id
  WHERE status = \'private\'
    AND user_id = '.$userdata['id'].'
;';
  display_select_cat_wrapper($query_true,array(),'category_option_true');
  
  $result = pwg_query($query_true);
  $authorized_ids = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($authorized_ids, $row['id']);
  }
  
  $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'';
  if (count($authorized_ids) > 0)
  {
    $query_false.= '
    AND id NOT IN ('.implode(',', $authorized_ids).')';
  }
  $query_false.= '
;';
  display_select_cat_wrapper($query_false,array(),'category_option_false');
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'user');
?>
