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
  die ("Hacking attempt!");
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
// +-----------------------------------------------------------------------+
// |                       modification registration                       |
// +-----------------------------------------------------------------------+
// print '<pre>';
// print_r($_POST);
// print '</pre>';
if (isset($_POST['submit']) and count($_POST['cat']) > 0)
{
  switch ($_GET['section'])
  {
    case 'upload' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET uploadable = \''.$_POST['option'].'\'
  WHERE id IN ('.implode(',', $_POST['cat']).')
;';
      pwg_query($query);
      break;
    }
    case 'comments' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET commentable = \''.$_POST['option'].'\'
  WHERE id IN ('.implode(',', $_POST['cat']).')
;';
      pwg_query($query);
      break;
    }
    case 'visible' :
    {
      // locking a category   => all its child categories become locked
      if ($_POST['option'] == 'false')
      {
        $subcats = get_subcat_ids($_POST['cat']);
        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'false\'
  WHERE id IN ('.implode(',', $subcats).')
;';
        pwg_query($query);
      }
      // unlocking a category => all its parent categories become unlocked
      if ($_POST['option'] == 'true')
      {
        $uppercats = array();
        $query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $_POST['cat']).')
;';
        $result = pwg_query($query);
        while ($row = mysql_fetch_array($result))
        {
          $uppercats = array_merge($uppercats,
                                   explode(',', $row['uppercats']));
        }
        $uppercats = array_unique($uppercats);

        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'true\'
  WHERE id IN ('.implode(',', $uppercats).')
;';
        pwg_query($query);
      }
      break;
    }
    case 'status' :
    {
      // make a category private => all its child categories become private
      if ($_POST['option'] == 'false')
      {
        $subcats = get_subcat_ids($_POST['cat']);
        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'private\'
  WHERE id IN ('.implode(',', $subcats).')
;';
        pwg_query($query);
      }
      // make public a category => all its parent categories become public
      if ($_POST['option'] == 'true')
      {
        $uppercats = array();
        $query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $_POST['cat']).')
;';
        $result = pwg_query($query);
        while ($row = mysql_fetch_array($result))
        {
          $uppercats = array_merge($uppercats,
                                   explode(',', $row['uppercats']));
        }
        $uppercats = array_unique($uppercats);

        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'public\'
  WHERE id IN ('.implode(',', $uppercats).')
;';
        pwg_query($query);
      }
      break;
    }
  }
}
// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('cat_options'=>'admin/cat_options.tpl'));

if (!isset($_GET['section']))
{
  $page['section'] = 'upload';
}
else
{
  $page['section'] = $_GET['section'];
}

$base_url = PHPWG_ROOT_PATH.'admin.php?page=cat_options&amp;section=';
$template->assign_vars(
  array(
    'L_SUBMIT'=>$lang['submit'],
    'L_RESET'=>$lang['reset'],
    'L_CAT_OPTIONS_MENU_UPLOAD'=>$lang['cat_options_menu_upload'],
    'L_CAT_OPTIONS_MENU_VISIBLE'=>$lang['cat_options_menu_visible'],
    'L_CAT_OPTIONS_MENU_COMMENTS'=>$lang['cat_options_menu_comments'],
    'L_CAT_OPTIONS_MENU_STATUS'=>$lang['cat_options_menu_status'],
    'L_CAT_OPTIONS_UPLOAD_INFO'=>$lang['cat_options_upload_info'],
    'L_CAT_OPTIONS_UPLOAD_TRUE'=>$lang['cat_options_upload_true'],
    'L_CAT_OPTIONS_UPLOAD_FALSE'=>$lang['cat_options_upload_false'],
    'L_CAT_OPTIONS_COMMENTS_INFO'=>$lang['cat_options_comments_info'],
    'L_CAT_OPTIONS_COMMENTS_TRUE'=>$lang['cat_options_comments_true'],
    'L_CAT_OPTIONS_COMMENTS_FALSE'=>$lang['cat_options_comments_false'],
    'L_CAT_OPTIONS_VISIBLE_INFO'=>$lang['cat_options_visible_info'],
    'L_CAT_OPTIONS_VISIBLE_TRUE'=>$lang['cat_options_visible_true'],
    'L_CAT_OPTIONS_VISIBLE_FALSE'=>$lang['cat_options_visible_false'],
    'L_CAT_OPTIONS_STATUS_INFO'=>$lang['cat_options_status_info'],
    'L_CAT_OPTIONS_STATUS_TRUE'=>$lang['cat_options_status_true'],
    'L_CAT_OPTIONS_STATUS_FALSE'=>$lang['cat_options_status_false'],
    
    'U_UPLOAD'=>add_session_id($base_url.'upload'),
    'U_VISIBLE'=>add_session_id($base_url.'visible'),
    'U_COMMENTS'=>add_session_id($base_url.'comments'),
    'U_STATUS'=>add_session_id($base_url.'status'),
    
    'F_ACTION'=>add_session_id($base_url.$page['section'])
   )
 );

$template->assign_vars(array(strtoupper($page['section']).'_CLASS'=>'opened'));
// +-----------------------------------------------------------------------+
// |                              form display                             |
// +-----------------------------------------------------------------------+

// for each section, categories in the multiselect field can be :
//
// - true : uploadable for upload section
// - false : un-uploadable for upload section
// - NA : (not applicable) for virtual categories
//
// for true and false status, we associates an array of category ids,
// function display_select_categories will use the given CSS class for each
// option
$cats_true = array();
$cats_false = array();
switch ($page['section'])
{
  case 'upload' :
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE uploadable = \'true\'
    AND dir IS NOT NULL
    AND site_id = 1
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_true, $row['id']);
    }
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE uploadable = \'false\'
    AND dir IS NOT NULL
    AND site_id = 1
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_false, $row['id']);
    }
    
    $template->assign_block_vars('upload', array());

    break;
  }
  case 'comments' :
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE commentable = \'true\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_true, $row['id']);
    }
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE commentable = \'false\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_false, $row['id']);
    }
    
    $template->assign_block_vars('comments', array());
    
    break;
  }
  case 'visible' :
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'true\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_true, $row['id']);
    }
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'false\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_false, $row['id']);
    }
    
    $template->assign_block_vars('visible', array());
    
    break;
  }
  case 'status' :
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'public\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_true, $row['id']);
    }
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($cats_false, $row['id']);
    }
    
    $template->assign_block_vars('status', array());
    
    break;
  }
}
$CSS_classes = array('optionTrue'=>$cats_true,
                     'optionFalse'=>$cats_false);
  
$user['expand'] = true;
$structure = create_user_structure('');
display_select_categories($structure,
                          '&nbsp;',
                          array(),
                          'category_option',
                          $CSS_classes);
// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_options');
?>