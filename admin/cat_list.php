<?php
// +-----------------------------------------------------------------------+
// |                             cat_list.php                              |
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

if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );

$errors = array();
$categories=array();
$navigation=$lang['gallery_index'];

//---------------------------------------------------  virtual categories
if ( isset( $_GET['delete'] ) && is_numeric( $_GET['delete'] ) )
{
  delete_category( $_GET['delete'] );
  synchronize_all_users();
}
elseif ( isset( $_POST['submit'] ) )
{
  // is the given category name only containing blank spaces ?
  if ( preg_match( '/^\s*$/', $_POST['virtual_name'] ) )
    array_push( $errors, $lang['cat_error_name'] );
	
  if ( !count( $errors ))
  {
    // we have then to add the virtual category
	$parent_id = !empty($_GET['parent_id'])?$_GET['parent_id']:'NULL'; 
    $query = 'INSERT INTO '.CATEGORIES_TABLE;
    $query.= ' (name,id_uppercat,rank) VALUES ';
    $query.= " ('".$_POST['virtual_name']."',".$parent_id.",".$_POST['rank'].")";
    $query.= ';';
    mysql_query( $query );
    synchronize_all_users();
  }
}

// Cache management

$query = 'SELECT * FROM '.CATEGORIES_TABLE;
if ( !isset($_GET['parent_id']))
{
  $query.= ' WHERE id_uppercat IS NULL';
}
else
{
  $query.= ' WHERE id_uppercat = '.$_GET['parent_id'];
}
$query.= ' ORDER BY rank ASC';
$query.= ';';
$result = mysql_query( $query );
while ( $row = mysql_fetch_assoc( $result ) )
{
  $categories[$row['rank']]=$row;
}

// Navigation path
if (isset($_GET['parent_id']))
{
  $current_category = get_cat_info($_GET['parent_id']);
  $url = PHPWG_ROOT_PATH.'admin.php?page=cat_list&amp;parent_id=';
  $navigation = '<a class="" href="'.add_session_id(PHPWG_ROOT_PATH.'admin.php?page=cat_list').'">';
  $navigation.= $lang['gallery_index'].'</a>-&gt;';
  $navigation.= get_cat_display_name($current_category['name'], '-&gt;', $url);
}

//---------------------------------------------------------------  rank updates
$current_rank=0;
if ( isset( $_GET['up'] ) && is_numeric( $_GET['up'] ))
{
  // 1. searching the id of the category just above at the same level
  while (list ($id,$current) = each($categories))
  {
    if ($current['id'] == $_GET['up'])
	{
	  $current_rank = $current['rank'];
	  break;
    }
  }
  if ($current_rank>1)
  {
    // 2. Exchanging ranks between the two categories
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = '.($current_rank-1);
    $query.= ' WHERE id = '.$_GET['up'];
    $query.= ';';
    mysql_query( $query );
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = '.$current_rank;
    $query.= ' WHERE id = '.$categories[($current_rank-1)]['id'];
    $query.= ';';
    mysql_query( $query );
	// 3. Updating the cache array
	$categories[$current_rank]=$categories[($current_rank-1)];
	$categories[($current_rank-1)] = $current;
  }
  else
  {
    // 2. Updating the rank of our category to be after the previous max rank
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = '.(count($categories) + 1);
    $query.= ' WHERE id = '.$_GET['up'];
    $query.= ';';
    mysql_query( $query );
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = rank-1';
	$query.= ' WHERE id_uppercat ';
	$query.= empty($_GET['parent_id'])?'IS NULL':('= '.$_GET['parent_id']);
	$query.= ';';
    mysql_query( $query );
	// 3. Updating the cache array
	array_push($categories, $current);
	array_shift($categories);
  }
}
elseif ( isset( $_GET['down'] ) && is_numeric( $_GET['down'] ) )
{
  // 1. searching the id of the category just above at the same level
  while (list ($id,$current) = each($categories))
  {
    if ($current['id'] == $_GET['down'])
	{
	  $current_rank = $current['rank'];
	  break;
	}
  }
  if ($current_rank < count($categories))
  {
    // 2. Exchanging ranks between the two categories
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = '.($current_rank+1);
    $query.= ' WHERE id = '.$_GET['down'];
    $query.= ';';
    mysql_query( $query );
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = '.$current_rank;
    $query.= ' WHERE id = '.$categories[($current_rank+1)]['id'];
    $query.= ';';
    mysql_query( $query );
	// 3. Updating the cache array
	$categories[$current_rank]=$categories[($current_rank+1)];
	$categories[($current_rank+1)] = $current;
  }
  else 
  {
    // 2. updating the rank of our category to be the first one
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = 0';
    $query.= ' WHERE id = '.$_GET['down'];
    $query.= ';';
    mysql_query( $query );
    $query = 'UPDATE '.CATEGORIES_TABLE;
    $query.= ' SET rank = (rank+1)';
	$query.= ' WHERE id_uppercat ';
	$query.= empty($_GET['parent_id'])?'IS NULL':('= '.$_GET['parent_id']);
	$query.= ';';
    mysql_query( $query );
	// 3. Updating the cache array
	array_unshift($categories, $current);
	array_pop($categories);
  }
}
reset($categories);

//----------------------------------------------------- template initialization
$template->set_filenames( array('categories'=>'admin/cat_list.tpl') );

$template->assign_vars(array(
  'CATEGORIES_NAV'=>$navigation,
  'NEXT_RANK'=>count($categories)+1,
  
  'L_ADD_VIRTUAL'=>$lang['cat_add'],
  'L_SUBMIT'=>$lang['submit'],
  'L_STORAGE'=>$lang['storage'],
  'L_NB_IMG'=>$lang['pictures'],
  'L_MOVE_UP'=>$lang['cat_up'],
  'L_MOVE_DOWN'=>$lang['cat_down'],
  'L_EDIT'=>$lang['edit'],
  'L_INFO_IMG'=>$lang['cat_image_info'],
  'L_DELETE'=>$lang['delete']
  ));
  
$tpl = array( 'cat_first','cat_last');
			  
//-------------------------------------------------------------- errors display
if ( sizeof( $errors ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $errors ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}
//----------------------------------------------------------- Categories display
while (list ($id,$category) = each($categories))
{

  if ($category['status'] == 'private')
  {
    $category_image = '<img src="'.PHPWG_ROOT_PATH.'template/'.$user['template'].'/admin/images/icon_folder_lock.gif" 
	  width="46" height="25" alt="'.$lang['cat_private'].'" title="'.$lang['cat_private'].'"/>';
  }
  elseif (empty($category['dir']))
  {
    $category_image = '<img src="'.PHPWG_ROOT_PATH.'template/'.$user['template'].'/admin/images/icon_folder_link.gif"
	  width="46" height="25" alt="'.$lang['cat_virtual'].'" title="'.$lang['cat_virtual'].'"/>';
  }
  else
  {
	// May be should we have to introduce a computed field in the table to avoid this query
    $query = 'SELECT COUNT(id) as sub_cats FROM ' . CATEGORIES_TABLE . ' WHERE id_uppercat = '.$category['id'];
    $result = mysql_fetch_array(mysql_query( $query ));
	$category_image = ($result['sub_cats']) ? 
	  '<img src="'.PHPWG_ROOT_PATH.'template/'.$user['template'].'/admin/images/icon_subfolder.gif" width="46" height="25" alt="" />' : 
	  '<img src="'.PHPWG_ROOT_PATH.'template/'.$user['template'].'/admin/images/icon_folder.gif" width="46" height="25" alt="" />';
  }
  
  if ( !isset( $category['dir'] ) ) $category['dir'] = '';
  $simple_url = PHPWG_ROOT_PATH.'admin.php?page=cat_list&amp;';
  $url = $simple_url;
  if (isset($_GET['parent_id']))
    $url = $simple_url.'parent_id='.$_GET['parent_id'].'&amp;';

  $template->assign_block_vars('category' ,array(
    'CATEGORY_IMG'=>$category_image,
    'CATEGORY_NAME'=>$category['name'],
	'CATEGORY_DIR'=>$category['dir'],
	'CATEGORY_NB_IMG'=>$category['nb_images'],
	
	'U_CATEGORY'=>add_session_id( $simple_url.'parent_id='.$category['id']),
	'U_MOVE_UP'=>add_session_id( $url.'up='.$category['id'] ),
	'U_MOVE_DOWN'=>add_session_id( $url.'down='.$category['id'] ),
	'U_CAT_EDIT'=>add_session_id( PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id='.$row['id'] ),
	'U_CAT_DELETE'=>add_session_id( $url.'delete='.$category['id'] ),
	'U_INFO_IMG'=>add_session_id( PHPWG_ROOT_PATH.'admin.php?page=infos_images&amp;cat_id='.$row['id'] ),
	'U_CAT_UPDATE'=>add_session_id( PHPWG_ROOT_PATH.'admin.php?page=update&amp;update='.$row['id'] )
	));
	
  if ( !empty($category['dir']))
  {
    $template->assign_block_vars('category.storage' ,array());
  }
  else
  {
	$template->assign_block_vars('category.virtual' ,array());
  }
  $url = add_session_id( './admin.php?page=cat_modify&amp;cat='.$row['id'] );
  if ( $category['nb_images'] > 0 )
  {
    $template->assign_block_vars('category.image_info' ,array());
  }
  else
  {
    $template->assign_block_vars('category.no_image_info' ,array()); 
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
