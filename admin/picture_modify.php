<?php
// +-----------------------------------------------------------------------+
// |                          picture_modify.php                           |
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
  mysql_query($query);
  // make the picture representative of a category ?
  $query = '
SELECT DISTINCT(category_id) as category_id,representative_picture_id
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic, '.CATEGORIES_TABLE.' AS c
  WHERE c.id = ic.category_id
    AND image_id = '.$_GET['image_id'].'
;';
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result))
  {
    // if the user ask the picture to be the representative picture of its
    // category, the category is updated in the database (without wondering
    // if this picture was already the representative one)
    if (isset($_POST['representative-'.$row['category_id']]))
    {
      $query = 'UPDATE '.CATEGORIES_TABLE;
      $query.= ' SET representative_picture_id = '.$_GET['image_id'];
      $query.= ' WHERE id = '.$row['category_id'];
      $query.= ';';
      mysql_query($query);
    }
    // if the user ask this picture to be not any more the representative,
    // we have to set the representative_picture_id of this category to NULL
    else if (isset($row['representative_picture_id'])
             and $row['representative_picture_id'] == $_GET['image_id'])
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id = '.$row['category_id'].'
;';
      mysql_query($query);
    }
  }
  $associate_or_dissociate = false;
  // associate with a new category ?
  if ($_POST['associate'] != '-1' and $_POST['associate'] != '')
  {
    // does the uppercat id exists in the database ?
    if (!is_numeric($_POST['associate']))
    {
      array_push($errors, $lang['cat_unknown_id']);
    }
    else
    {
      $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_POST['associate'].'
;';
      if (mysql_num_rows(mysql_query($query)) == 0)
        array_push($errors, $lang['cat_unknown_id']);
    }
  }
  if ($_POST['associate'] != '-1'
       and $_POST['associate'] != ''
       and count($errors) == 0)
  {
    $query = '
INSERT INTO '.IMAGE_CATEGORY_TABLE.'
  (category_id,image_id)
  VALUES
  ('.$_POST['associate'].','.$_GET['image_id'].')
;';
    mysql_query($query);
    $associate_or_dissociate = true;
    update_category($_POST['associate']);
  }
  // dissociate any category ?
  // retrieving all the linked categories
  $query = '
SELECT DISTINCT(category_id) as category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
;';
  $result = mysql_query($query);
  while ($row = mysql_fetch_array($result))
  {
    if (isset($_POST['dissociate-'.$row['category_id']]))
    {
      $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
  AND category_id = '.$row['category_id'].'
;';
      mysql_query($query);
      $associate_or_dissociate = true;
      update_category($row['category_id']);
    }
  }
  if ($associate_or_dissociate)
  {
    synchronize_all_users();
  }
}

// retrieving direct information about picture
$query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$_GET['image_id'].'
;';
$row = mysql_fetch_array(mysql_query($query));

// some fields are nullable in the images table
$nullables = array('name','author','keywords','date_creation','comment',
                   'width','height');
foreach ($nullables as $field)
{
  if (!isset($row[$field]))
  {
    $row[$field] = '';
  }
}

if (empty($row['name']))
{
  $title = str_replace('_', ' ',get_filename_wo_extension($row['file']));
}
else
{
  $title = $row['name'];
}
// Navigation path
$current_category = get_cat_info($row['storage_category_id']);
$dir_path = get_cat_display_name($current_category['name'], '-&gt;', '');

// thumbnail url
if (isset($row['tn_ext']) and $row['tn_ext'] != '')
{
  $thumbnail_url = get_complete_dir($row['storage_category_id']);
  $thumbnail_url.= 'thumbnail/'.$conf['prefix_thumbnail'];
  $thumbnail_url.= get_filename_wo_extension($row['file']);
  $thumbnail_url.= '.'.$row['tn_ext'];
}
else
{
  $thumbnail_url = PHPWG_ROOT_PATH;
  $thumbnail_url = 'template/'.$user['template'].'/mimetypes/';
  $thumbnail_url.= strtolower(get_extension($row['file'])).'.png';
}

$url_img = PHPWG_ROOT_PATH.'picture.php?image_id='.$_GET['image_id'];
$url_img .= '&amp;cat='.$row['storage_category_id'];
$date = isset($_POST['date_creation']) && empty($errors)
          ?$_POST['date_creation']:date_convert_back($row['date_creation']);

// retrieving all the linked categories
$query = '
SELECT DISTINCT(category_id) AS category_id,status,visible
       ,representative_picture_id
  FROM '.IMAGE_CATEGORY_TABLE.','.CATEGORIES_TABLE.'
  WHERE image_id = '.$_GET['image_id'].'
    AND category_id = id
;';
$result = mysql_query($query);
$categories = '';
while ($cat_row = mysql_fetch_array($result))
{
  $cat_infos = get_cat_info($cat_row['category_id']);
  $cat_name = get_cat_display_name($cat_infos['name'], ' &gt; ', '');
  $categories.='<option value="'.$cat_row['category_id'].'">'.$cat_name.'</option>';
}

//----------------------------------------------------- template initialization
$template->set_filenames(array('picture_modify'=>'admin/picture_modify.tpl'));
$template->assign_vars(array(
  'TITLE_IMG'=>$title,
  'DIR_IMG'=>$dir_path,
  'FILE_IMG'=>$row['file'],
  'TN_URL_IMG'=>$thumbnail_url,
  'URL_IMG'=>add_session_id($url_img),
  'DEFAULT_NAME_IMG'=>str_replace('_',' ',get_filename_wo_extension($row['file'])),
  'FILE_IMG'=>$row['file'],
  'NAME_IMG'=>isset($_POST['name'])?$_POST['name']:$row['name'],
  'SIZE_IMG'=>$row['width'].' * '.$row['height'],
  'FILESIZE_IMG'=>$row['filesize'].' KB',
  'REGISTRATION_DATE_IMG'=> format_date($row['date_available']),
  'AUTHOR_IMG'=>isset($_POST['author'])?$_POST['author']:$row['author'],
  'CREATION_DATE_IMG'=>$date,
  'KEYWORDS_IMG'=>isset($_POST['keywords'])?$_POST['keywords']:$row['keywords'],
  'COMMENT_IMG'=>isset($_POST['comment'])?$_POST['comment']:$row['comment'],
  'ASSOCIATED_CATEGORIES'=>$categories,
  
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
  
  'F_ACTION'=>add_session_id(PHPWG_ROOT_PATH.'admin.php?'.$_SERVER['QUERY_STRING'])
 ));
  
//-------------------------------------------------------------- errors display
if (sizeof($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  for ($i = 0; $i < sizeof($errors); $i++)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}

// if there are linked category other than the storage category, we show
// propose the dissociate text
if (mysql_num_rows($result) > 0)
{
  //$vtp->addSession($sub, 'dissociate');
  //$vtp->closeSession($sub, 'dissociate');
}
// associate to another category ?
//
// We only show a List Of Values if the number of categories is less than
// $conf['max_LOV_categories']
$query = 'SELECT COUNT(id) AS nb_total_categories';
$query.= ' FROM '.CATEGORIES_TABLE.';';
$row = mysql_fetch_array(mysql_query($query));
if ($row['nb_total_categories'] < $conf['max_LOV_categories'])
{
  $template->assign_block_vars('associate_LOV',array());
  $template->assign_block_vars('associate_LOV.associate_cat',array(
	));
  /*$vtp->addSession($sub, 'associate_LOV');
  $vtp->addSession($sub, 'associate_cat');
  $vtp->setVar($sub, 'associate_cat.value', '-1');
  $vtp->setVar($sub, 'associate_cat.content', '');
  $vtp->closeSession($sub, 'associate_cat');
  $page['plain_structure'] = get_plain_structure(true);
  $structure = create_structure('', array());
  display_categories($structure, '&nbsp;');
  $vtp->closeSession($sub, 'associate_LOV');*/
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'picture_modify');
?>
