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
if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}

include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
//--------------------------------------------------------------------- updates
if (isset($_POST['falsify'])
         and isset($_POST['cat_true'])
         and count($_POST['cat_true']) > 0)
{
  // if you forbid access to a category, all sub-categories become
  // automatically forbidden
  $subcats = get_subcat_ids($_POST['cat_true']);
  $query = 'DELETE FROM '.GROUP_ACCESS_TABLE.'
    WHERE group_id = '.$_POST['group_id'].'
    AND cat_id IN ('.implode(',', $subcats).');';
  pwg_query($query);
}
else if (isset($_POST['trueify'])
         and isset($_POST['cat_false'])
         and count($_POST['cat_false']) > 0)
{
  $uppercats = get_uppercat_ids($_POST['cat_false']);
  $private_uppercats = array();

  $query = 'SELECT id
    FROM '.CATEGORIES_TABLE.'
    WHERE id IN ('.implode(',', $uppercats).')
    AND status = \'private\';';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($private_uppercats, $row['id']);
  }

  // retrying to authorize a category which is already authorized may cause
  // an error (in SQL statement), so we need to know which categories are
  // accesible
  $authorized_ids = array();
    
  $query = 'SELECT cat_id
  FROM '.GROUP_ACCESS_TABLE.'
  WHERE group_id = '.$_POST['group_id'].';';
  $result = pwg_query($query);
  
  while ($row = mysql_fetch_array($result))
  {
    array_push($authorized_ids, $row['cat_id']);
  }
  
  $inserts = array();
  $to_autorize_ids = array_diff($private_uppercats, $authorized_ids);
  foreach ($to_autorize_ids as $to_autorize_id)
  {
    array_push($inserts, array('group_id' => $_POST['group_id'],
                               'cat_id' => $to_autorize_id));
  }

  mass_inserts(GROUP_ACCESS_TABLE, array('group_id','cat_id'), $inserts);
}

//----------------------------------------------------- template initialization
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

$action = PHPWG_ROOT_PATH.'admin.php?page=group_perm';
$template->set_filenames( array('groups'=>'admin/group_perm.tpl') );
$template->assign_vars(array(
  'S_GROUP_SELECT'=>$groups_display,
  'L_GROUP_SELECT'=>$lang['group_list_title'],
  'L_LOOK_UP'=>$lang['edit'],
  'S_GROUP_ACTION'=>add_session_id($action)
  ));
  
if ($groups_nb) 
{
  $template->assign_block_vars('select_box',array());
}

if ( isset( $_POST['edit']) || isset($_POST['falsify']) || isset($_POST['trueify']))
{
  $template->set_filenames(array('groups_auth'=>'admin/cat_options.tpl'));
  $template->assign_vars(array(
      'L_RESET'=>$lang['reset'],
      'L_CAT_OPTIONS_TRUE'=>$lang['authorized'],
      'L_CAT_OPTIONS_FALSE'=>$lang['forbidden'],
      'L_CAT_OPTIONS_INFO'=>$lang['permuser_info'],
      
      'HIDDEN_NAME'=> 'group_id',
      'HIDDEN_VALUE'=>$_POST['group_id'],
      'F_ACTION' => add_session_id(PHPWG_ROOT_PATH.'admin.php?page=group_perm'),
  ));
  
  // only private categories are listed
  $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.GROUP_ACCESS_TABLE.' ON cat_id = id
  WHERE status = \'private\'
    AND group_id = '.$_POST['group_id'].'
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
  
  $template->assign_var_from_handle('ADMIN_CONTENT_2', 'groups_auth');
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'groups');

?>
