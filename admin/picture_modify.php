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

if(!defined("PHPWG_ROOT_PATH"))
{
  die ("Hacking attempt!");
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
//--------------------------------------------------------- update informations
$errors = array();
// first, we verify whether there is a mistake on the given creation date
if (isset($_POST['date_creation']) and !empty($_POST['date_creation']))
{
  if (!check_date_format($_POST['date_creation']))
  {
    array_push($errors, $lang['err_date']);
  }
}
if (isset($_POST['submit']))
{
  $query = 'UPDATE '.IMAGES_TABLE.' SET name = ';
  if ($_POST['name'] == '')
    $query.= 'NULL';
  else
    $query.= "'".htmlentities($_POST['name'], ENT_QUOTES)."'";
  
  $query.= ', author = ';
  if ($_POST['author'] == '')
    $query.= 'NULL';
  else
    $query.= "'".htmlentities($_POST['author'],ENT_QUOTES)."'";

  $query.= ', comment = ';
  if ($_POST['comment'] == '')
    $query.= 'NULL';
  else
    $query.= "'".htmlentities($_POST['comment'],ENT_QUOTES)."'";

  $query.= ', date_creation = ';
  if (check_date_format($_POST['date_creation']))
    $query.= "'".date_convert($_POST['date_creation'])."'";
  else if ($_POST['date_creation'] == '')
    $query.= 'NULL';

  $query.= ', keywords = ';
  $keywords_array = get_keywords($_POST['keywords']);
  if (count($keywords_array) == 0)
    $query.= 'NULL';
  else
  {
    $query.= "'";
    foreach ($keywords_array as $i => $keyword) {
      if ($i > 0) $query.= ',';
      $query.= $keyword;
    }
    $query.= "'";
  }

  $query.= ' WHERE id = '.$_GET['image_id'];
  $query.= ';';
  pwg_query($query);
}
// associate the element to other categories than its storage category
if (isset($_POST['associate'])
    and isset($_POST['cat_dissociated'])
    and count($_POST['cat_dissociated']) > 0)
{
  $datas = array();
  foreach ($_POST['cat_dissociated'] as $category_id)
  {
    array_push($datas, array('image_id' => $_GET['image_id'],
                             'category_id' => $category_id));
  }
  mass_inserts(IMAGE_CATEGORY_TABLE, array('image_id', 'category_id'), $datas);

  update_category($_POST['cat_dissociated']);
}
// dissociate the element from categories (but not from its storage category)
if (isset($_POST['dissociate'])
    and isset($_POST['cat_associated'])
    and count($_POST['cat_associated']) > 0)
{
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
    AND category_id IN ('.implode(',',$_POST['cat_associated'] ).')
';
  pwg_query($query);
  update_category($_POST['cat_associated']);
}
// elect the element to represent the given categories
if (isset($_POST['elect'])
    and isset($_POST['cat_dismissed'])
    and count($_POST['cat_dismissed']) > 0)
{
  $datas = array();
  foreach ($_POST['cat_dismissed'] as $category_id)
  {
    array_push($datas,
               array('id' => $category_id,
                     'representative_picture_id' => $_GET['image_id']));
  }
  $fields = array('primary' => array('id'),
                  'update' => array('representative_picture_id'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
}
// dismiss the element as representant of the given categories
if (isset($_POST['dismiss'])
    and isset($_POST['cat_elected'])
    and count($_POST['cat_elected']) > 0)
{
  set_random_representant($_POST['cat_elected']);
}

// retrieving direct information about picture
$query = '
SELECT i.*, c.uppercats
  FROM '.IMAGES_TABLE.' AS i
   INNER JOIN '.CATEGORIES_TABLE.' AS c ON i.storage_category_id = c.id
  WHERE i.id = '.$_GET['image_id'].'
;';
$row = mysql_fetch_array(pwg_query($query));

$storage_category_id = $row['storage_category_id'];

if (empty($row['name']))
{
  $title = str_replace('_', ' ',get_filename_wo_extension($row['file']));
}
else
{
  $title = $row['name'];
}
// Navigation path
$thumbnail_url = get_thumbnail_src($row['path'], @$row['tn_ext']);

$url_img = PHPWG_ROOT_PATH.'picture.php?image_id='.$_GET['image_id'];
$url_img .= '&amp;cat='.$row['storage_category_id'];
$date = isset($_POST['date_creation']) && empty($errors)
?$_POST['date_creation']:date_convert_back(@$row['date_creation']);

$url = PHPWG_ROOT_PATH.'admin.php?page=cat_modify&amp;cat_id=';
$storage_category = get_cat_display_name_cache($row['uppercats'],
                                               $url,
                                               false);
//----------------------------------------------------- template initialization
$template->set_filenames(array('picture_modify'=>'admin/picture_modify.tpl'));
$template->assign_vars(array(
  'TITLE_IMG'=>$title,
  'STORAGE_CATEGORY_IMG'=>$storage_category,
  'PATH_IMG'=>$row['path'],
  'FILE_IMG'=>$row['file'],
  'TN_URL_IMG'=>$thumbnail_url,
  'URL_IMG'=>add_session_id($url_img),
  'DEFAULT_NAME_IMG'=>str_replace('_',' ',get_filename_wo_extension($row['file'])),
  'FILE_IMG'=>$row['file'],
  'NAME_IMG'=>isset($_POST['name'])?$_POST['name']:@$row['name'],
  'SIZE_IMG'=>@$row['width'].' * '.@$row['height'],
  'FILESIZE_IMG'=>@$row['filesize'].' KB',
  'REGISTRATION_DATE_IMG'=> format_date($row['date_available']),
  'AUTHOR_IMG'=>isset($_POST['author'])?$_POST['author']:@$row['author'],
  'CREATION_DATE_IMG'=>$date,
  'KEYWORDS_IMG'=>isset($_POST['keywords'])?$_POST['keywords']:@$row['keywords'],
  'COMMENT_IMG'=>isset($_POST['comment'])?$_POST['comment']:@$row['comment'],
  
  'L_UPLOAD_NAME'=>$lang['upload_name'],
  'L_DEFAULT'=>$lang['default'],
  'L_FILE'=>$lang['file'],
  'L_SIZE'=>$lang['size'],
  'L_FILESIZE'=>$lang['filesize'],
  'L_REGISTRATION_DATE'=>$lang['registration_date'],
  'L_AUTHOR'=>$lang['author'],
  'L_CREATION_DATE'=>$lang['creation_date'],
  'L_KEYWORDS'=>$lang['keywords'],
  'L_COMMENT'=>$lang['comment'],
  'L_CATEGORIES'=>$lang['categories'],
  'L_DISSOCIATE'=>$lang['dissociate'],
  'L_INFOIMAGE_ASSOCIATE'=>$lang['infoimage_associate'],
  'L_SUBMIT'=>$lang['submit'],
  'L_RESET'=>$lang['reset'],
  'L_CAT_ASSOCIATED'=>$lang['infoimage_associated'],
  'L_CAT_DISSOCIATED'=>$lang['infoimage_dissociated'],
  'L_PATH'=>$lang['path'],
  'L_STORAGE_CATEGORY'=>$lang['storage_category'],
  'L_REPRESENTS'=>$lang['represents'],
  'L_DOESNT_REPRESENT'=>$lang['doesnt_represent'],
  
  'F_ACTION'=>add_session_id(PHPWG_ROOT_PATH.'admin.php?'.$_SERVER['QUERY_STRING'])
 ));
  
//-------------------------------------------------------------- errors display
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}

// associate to another category ?
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = category_id
  WHERE image_id = '.$_GET['image_id'].'
    AND id != '.$storage_category_id.'
;';
display_select_cat_wrapper($query,array(),'associated_option');

$result = pwg_query($query);
$associateds = array($storage_category_id);
while ($row = mysql_fetch_array($result))
{
  array_push($associateds, $row['id']);
}
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $associateds).')
;';
display_select_cat_wrapper($query,array(),'dissociated_option');
// representing
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id = '.$_GET['image_id'].'
;';
display_select_cat_wrapper($query,array(),'elected_option');

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $associateds).')
    AND representative_picture_id != '.$_GET['image_id'].'
;';
display_select_cat_wrapper($query,array(),'dismissed_option');
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_modify');
?>
