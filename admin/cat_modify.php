<?php
// +-----------------------------------------------------------------------+
// |                            cat_modify.php                             |
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

//---------------------------------------------------------------- verification
if ( !isset( $_GET['cat_id'] ) || !is_numeric( $_GET['cat_id'] ) )
{
  $_GET['cat_id'] = '-1';
}

$template->set_filenames( array('categories'=>'admin/cat_modify.tpl') );

//--------------------------------------------------------- form criteria check
if ( isset( $_POST['submit'] ) )
{
  // if new status is different from previous one, deletion of all related
  // links for access rights
  $query = 'SELECT status';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE id = '.$_GET['cat_id'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  
  $query = 'UPDATE '.CATEGORIES_TABLE;
  $query.= ' SET name = ';
  if ( empty($_POST['name']))
    $query.= 'NULL';
  else
    $query.= "'".htmlentities( $_POST['name'], ENT_QUOTES)."'";

  $query.= ', comment = ';
  if ( empty($_POST['comment']))
    $query.= 'NULL';
  else
    $query.= "'".htmlentities( $_POST['comment'], ENT_QUOTES )."'";

  $query.= ", status = '".$_POST['status']."'";
  $query.= ", visible = '".$_POST['visible']."'";
  if ( isset( $_POST['uploadable'] ) )
    $query.= ", uploadable = '".$_POST['uploadable']."'";

  if ( isset( $_POST['associate'] ) )
  {
    $query.= ', id_uppercat = ';
    if ( $_POST['associate'] == -1 or $_POST['associate'] == '' )
      $query.= 'NULL';
    else
      $query.= $_POST['associate'];
  }
  $query.= ' WHERE id = '.$_GET['cat_id'];
  $query.= ';';
  mysql_query( $query );

  if ( $_POST['status'] != $row['status'] )
  {
    // deletion of all access for groups concerning this category
    $query = 'DELETE';
    $query.= ' FROM '.GROUP_ACCESS_TABLE;
    $query.= ' WHERE cat_id = '.$_GET['cat_id'];
    mysql_query( $query );
    // deletion of all access for users concerning this category
    $query = 'DELETE';
    $query.= ' FROM '.USER_ACCESS_TABLE;
    $query.= ' WHERE cat_id = '.$_GET['cat_id'];
    mysql_query( $query );
  }

  // checking users favorites
  $query = 'SELECT id';
  $query.= ' FROM '.USERS_TABLE;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    check_favorites( $row['id'] );
  }
  $template->assign_block_vars('confirmation' ,array());
}

$query = 'SELECT a.*, b.*';
$query.= ' FROM '.CATEGORIES_TABLE.' as a, '.SITES_TABLE.' as b';
$query.= ' WHERE a.id = '.$_GET['cat_id'];
$query.= ' AND a.site_id = b.id';
$query.= ';';
$category = mysql_fetch_array( mysql_query( $query ) );
// nullable fields
foreach (array('comment','dir') as $nullable)
{
  if (!isset($category[$nullable]))
  {
    $category[$nullable] = '';
  }
}

// Navigation path
$current_category = get_cat_info($_GET['cat_id']);
$url = PHPWG_ROOT_PATH.'admin.php?page=cat_list&amp;parent_id=';
$navigation = '<a class="" href="'.add_session_id(PHPWG_ROOT_PATH.'admin.php?page=cat_list').'">';
$navigation.= $lang['gallery_index'].'</a>-&gt;';
$navigation.= get_cat_display_name($current_category['name'], '-&gt;', $url);

$form_action = PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id='.$_GET['cat_id'];
$access = ($category['status']=='public')?'ACCESS_FREE':'ACCESS_RESTRICTED'; 
$lock = ($category['visible']=='true')?'UNLOCKED':'LOCKED';

//----------------------------------------------------- template initialization
$template->assign_vars(array( 
  'CATEGORIES_NAV'=>$navigation,
  'CAT_NAME'=>$category['name'],
  'CAT_COMMENT'=>$category['comment'],
  'CATEGORY_DIR'=>$category['dir'],
  'SITE_URL'=>$category['galleries_url'],
  
  $access=>'checked="checked"',
  $lock=>'checked="checked"',
  
  'L_EDIT_CONFIRM'=>$lang['editcat_confirm'],
  'L_EDIT_NAME'=>$lang['description'],
  'L_STORAGE'=>$lang['storage'],
  'L_EDIT_COMMENT'=>$lang['comment'],
  'L_EDIT_STATUS'=>$lang['conf_access'],
  'L_EDIT_STATUS_INFO'=>$lang['cat_access_info'],
  'L_ACCESS_FREE'=>$lang['free'],
  'L_ACCESS_RESTRICTED'=>$lang['restricted'],
  'L_EDIT_LOCK'=>$lang['cat_lock'],
  'L_EDIT_LOCK_INFO'=>$lang['cat_lock_info'],
  'L_YES'=>$lang['yes'],
  'L_NO'=>$lang['no'],
  'L_SUBMIT'=>$lang['submit'],
   
  'F_ACTION'=>add_session_id($form_action)
  ));
  
if ( !empty($category['dir']))
{
  $template->assign_block_vars('storage' ,array());
}

if ( $category['site_id'] != 1 )
{
  $template->assign_block_vars('storage' ,array());
}

/*
// can the parent category be changed ? (is the category virtual ?)
if ( $row['dir'] == '' )
{
  $vtp->addSession( $sub, 'parent' );
  // We only show a List Of Values if the number of categories is less than
  // $conf['max_LOV_categories']
  $query = 'SELECT COUNT(id) AS nb_total_categories';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ';';
  $countrow = mysql_fetch_array( mysql_query( $query ) );
  if ( $countrow['nb_total_categories'] < $conf['max_LOV_categories'] )
  {
    $vtp->addSession( $sub, 'associate_LOV' );
    $vtp->addSession( $sub, 'associate_cat' );
    $vtp->setVar( $sub, 'associate_cat.value', '-1' );
    $vtp->setVar( $sub, 'associate_cat.content', '' );
    $vtp->closeSession( $sub, 'associate_cat' );
    $page['plain_structure'] = get_plain_structure( true );
    $structure = create_structure( '', array() );
    display_categories( $structure, '&nbsp;', $row['id_uppercat'],$row['id'] );
    $vtp->closeSession( $sub, 'associate_LOV' );
  }
  // else, we only display a small text field, we suppose the administrator
  // knows the id of its category
  else
  {
    $vtp->addSession( $sub, 'associate_text' );
    $vtp->setVar( $sub, 'associate_text.value', $row['id_uppercat'] );
    $vtp->closeSession( $sub, 'associate_text' );
  }
  $vtp->closeSession( $sub, 'parent' );
}
*/
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
