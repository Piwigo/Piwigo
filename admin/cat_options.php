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
if (isset($_POST['falsify'])
    and isset($_POST['cat_true'])
    and count($_POST['cat_true']) > 0)
{
  switch ($_GET['section'])
  {
    case 'upload' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET uploadable = \'false\'
  WHERE id IN ('.implode(',', $_POST['cat_true']).')
;';
      pwg_query($query);
      break;
    }
    case 'comments' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET commentable = \'false\'
  WHERE id IN ('.implode(',', $_POST['cat_true']).')
;';
      pwg_query($query);
      break;
    }
    case 'visible' :
    {
      set_cat_visible($_POST['cat_true'], 'false');
      break;
    }
    case 'status' :
    {
      set_cat_status($_POST['cat_true'], 'private');
      break;
    }
  }
}
else if (isset($_POST['trueify'])
         and isset($_POST['cat_false'])
         and count($_POST['cat_false']) > 0)
{
  switch ($_GET['section'])
  {
    case 'upload' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET uploadable = \'true\'
  WHERE id IN ('.implode(',', $_POST['cat_false']).')
;';
      pwg_query($query);
      break;
    }
    case 'comments' :
    {
      $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET commentable = \'true\'
  WHERE id IN ('.implode(',', $_POST['cat_false']).')
;';
      pwg_query($query);
      break;
    }
    case 'visible' :
    {
      set_cat_visible($_POST['cat_false'], 'true');
      break;
    }
    case 'status' :
    {
      set_cat_status($_POST['cat_false'], 'public');
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
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE uploadable = \'true\'
    AND dir IS NOT NULL
    AND site_id = 1
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE uploadable = \'false\'
    AND dir IS NOT NULL
    AND site_id = 1
;';
    $template->assign_vars(
      array(
        'L_CAT_TITLE' => $lang['cat_upload_title'],
        'L_CAT_OPTIONS_TRUE' => $lang['authorized'],
        'L_CAT_OPTIONS_FALSE' => $lang['forbidden'],
        'L_CAT_OPTIONS_INFO' => $lang['cat_upload_info'],
        )
      );
    $template->assign_block_vars('upload', array());
    break;
  }
  case 'comments' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE commentable = \'true\'
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE commentable = \'false\'
;';
    $template->assign_vars(
      array(
        'L_CAT_TITLE' => $lang['cat_comments_title'],
        'L_CAT_OPTIONS_TRUE' => $lang['authorized'],
        'L_CAT_OPTIONS_FALSE' => $lang['forbidden'],
        'L_CAT_OPTIONS_INFO' => $lang['cat_comments_info'],
        )
      );
    $template->assign_block_vars('comments', array());
    break;
  }
  case 'visible' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'true\'
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'false\'
;';
    $template->assign_vars(
      array(
        'L_CAT_TITLE' => $lang['cat_lock_title'],
        'L_CAT_OPTIONS_TRUE' => $lang['unlocked'],
        'L_CAT_OPTIONS_FALSE' => $lang['locked'],
        'L_CAT_OPTIONS_INFO' => $lang['cat_lock_info'],
        )
      );
    $template->assign_block_vars('visible', array());
    break;
  }
  case 'status' :
  {
    $query_true = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'public\'
;';
    $query_false = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
;';
    $template->assign_vars(
      array(
        'L_CAT_TITLE' => $lang['cat_status_title'],
        'L_CAT_OPTIONS_TRUE' => $lang['cat_public'],
        'L_CAT_OPTIONS_FALSE' => $lang['cat_private'],
        'L_CAT_OPTIONS_INFO' => $lang['cat_status_info'],
        )
      );
    $template->assign_block_vars('status', array());
    break;
  }
}
display_select_cat_wrapper($query_true,array(),'category_option_true');
display_select_cat_wrapper($query_false,array(),'category_option_false');
// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_options');
?>