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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
//---------------------------------------------------------------- verification
if ( !isset( $_GET['cat_id'] ) || !is_numeric( $_GET['cat_id'] ) )
{
  $_GET['cat_id'] = '-1';
}

$template->set_filenames( array('categories'=>'admin/cat_modify.tpl') );

//--------------------------------------------------------- form criteria check
if ( isset( $_POST['submit'] ) )
{
  $query = 'SELECT status';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE id = '.$_GET['cat_id'];
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );
  
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
  pwg_query( $query );

  set_cat_visible(array($_GET['cat_id']), $_POST['visible']);
  set_cat_status(array($_GET['cat_id']), $_POST['status']);

  $template->assign_block_vars('confirmation' ,array());
}
else if (isset($_POST['set_random_representant']))
{
  set_random_representant(array($_GET['cat_id']));
}

$query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_GET['cat_id'].'
;';
$category = mysql_fetch_array( pwg_query( $query ) );
// nullable fields
foreach (array('comment','dir','site_id') as $nullable)
{
  if (!isset($category[$nullable]))
  {
    $category[$nullable] = '';
  }
}

// Navigation path
$url = PHPWG_ROOT_PATH.'admin.php?page=cat_list&amp;parent_id=';
$navigation = '<a class="" href="'.add_session_id(PHPWG_ROOT_PATH.'admin.php?page=cat_list').'">';
$navigation.= $lang['home'].'</a>'.$conf['level_separator'];

$navigation.= get_cat_display_name_cache(
  $category['uppercats'],
  $url);

$form_action = PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id='.$_GET['cat_id'];
$status = ($category['status']=='public')?'STATUS_PUBLIC':'STATUS_PRIVATE'; 
$lock = ($category['visible']=='true')?'UNLOCKED':'LOCKED';

if ($category['commentable'] == 'true')
{
  $commentable = 'COMMENTABLE_TRUE';
}
else
{
  $commentable = 'COMMENTABLE_FALSE';
}
if ($category['uploadable'] == 'true')
{
  $uploadable = 'UPLOADABLE_TRUE';
}
else
{
  $uploadable = 'UPLOADABLE_FALSE';
}

//----------------------------------------------------- template initialization
$template->assign_vars(array( 
  'CATEGORIES_NAV'=>$navigation,
  'CAT_NAME'=>$category['name'],
  'CAT_COMMENT'=>$category['comment'],
  
  $status=>'checked="checked"',
  $lock=>'checked="checked"',
  $commentable=>'checked="checked"',
  $uploadable=>'checked="checked"',
  
  'L_EDIT_CONFIRM'=>$lang['editcat_confirm'],
  'L_EDIT_NAME'=>$lang['description'],
  'L_STORAGE'=>$lang['storage'],
  'L_REMOTE_SITE'=>$lang['remote_site'],
  'L_EDIT_COMMENT'=>$lang['comment'],
  'L_EDIT_CAT_OPTIONS'=>$lang['cat_options'],
  'L_EDIT_STATUS'=>$lang['conf_access'],
  'L_EDIT_STATUS_INFO'=>$lang['cat_access_info'],
  'L_STATUS_PUBLIC'=>$lang['public'],
  'L_STATUS_PRIVATE'=>$lang['private'],
  'L_EDIT_LOCK'=>$lang['lock'],
  'L_EDIT_LOCK_INFO'=>$lang['editcat_lock_info'],
  'L_EDIT_UPLOADABLE'=>$lang['editcat_uploadable'],
  'L_EDIT_UPLOADABLE_INFO'=>$lang['editcat_uploadable_info'],
  'L_EDIT_COMMENTABLE'=>$lang['comments'],
  'L_EDIT_COMMENTABLE_INFO'=>$lang['editcat_commentable_info'],
  'L_YES'=>$lang['yes'],
  'L_NO'=>$lang['no'],
  'L_SUBMIT'=>$lang['submit'],
  'L_SET_RANDOM_REPRESENTANT'=>$lang['cat_representant'],
   
  'F_ACTION'=>add_session_id($form_action)
  ));

if ($category['nb_images'] > 0)
{
  $query = '
SELECT tn_ext,path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$category['representative_picture_id'].'
;';
  $row = mysql_fetch_array(pwg_query($query));
  $src = get_thumbnail_src($row['path'], @$row['tn_ext']);
  $url = PHPWG_ROOT_PATH.'admin.php?page=picture_modify';
  $url.= '&amp;image_id='.$category['representative_picture_id'];
  $template->assign_block_vars('representant',
                               array('SRC' => $src,
                                     'URL' => $url));
}

if (!empty($category['dir']))
{
  $template->assign_block_vars(
    'storage',
    array('CATEGORY_DIR'=>preg_replace('/\/$/',
                                       '',
                                       get_complete_dir($category['id']))));
  $template->assign_block_vars('upload' ,array());
}

if (is_numeric($category['site_id']) and $category['site_id'] != 1)
{
  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = '.$category['site_id'].'
;';
  list($galleries_url) = mysql_fetch_array(pwg_query($query));
  $template->assign_block_vars('server', array('SITE_URL' => $galleries_url));
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
