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

/**
 * Management of elements set. Elements can belong to a category or to the
 * user caddie.
 * 
 */

$user['nb_image_line'] = 6; // temporary
 
if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

// +-----------------------------------------------------------------------+
// |                              empty caddie                             |
// +-----------------------------------------------------------------------+
if (isset($_GET['empty']))
{
  $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  pwg_query($query);
}

// +-----------------------------------------------------------------------+
// |                       global mode form submission                     |
// +-----------------------------------------------------------------------+
$errors = array();

if (isset($_POST['submit']))
{
  $collection = array();
  
//   echo '<pre>';
//   print_r($_POST['selection']);
//   echo '</pre>';
//   exit();

  switch ($_POST['target'])
  {
    case 'all' :
    {
      $query = '
SELECT element_id
  FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
      $result = pwg_query($query);
      while ($row = mysql_fetch_array($result))
      {
        array_push($collection, $row['element_id']);
      }
      break;
    }
    case 'selection' :
    {
      $collection = $_POST['selection'];
      break;
    }
  }

  if ($_POST['associate'] != 0)
  {
    $datas = array();

    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['associate'].'
;';
    $associated = array_from_query($query, 'image_id');

    $associable = array_diff($collection, $associated);
    
    foreach ($associable as $item)
    {
      array_push($datas,
                 array('category_id'=>$_POST['associate'],
                       'image_id'=>$item));
    }
  
    mass_inserts(IMAGE_CATEGORY_TABLE,
                 array('image_id', 'category_id'),
                 $datas);
    update_category(array($_POST['associate']));
  }

  if ($_POST['dissociate'] != 0)
  {
    // physical links must be broken, so we must first retrieve image_id
    // which create virtual links with the category to "dissociate from".
    $query = '
SELECT id
  FROM '.IMAGE_CATEGORY_TABLE.' INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE category_id = '.$_POST['dissociate'].'
    AND category_id != storage_category_id
    AND id IN ('.implode(',', $collection).')
;';
    $dissociables = array_from_query($query, 'id');

    $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['dissociate'].'
  AND image_id IN ('.implode(',', $dissociables).')
';
    pwg_query($query);

    update_category(array($_POST['dissociate']));
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(
  array('element_set_global' => 'admin/element_set_global.tpl'));

$form_action = PHPWG_ROOT_PATH.'admin.php?page=element_set_global';

$template->assign_vars(
  array(
    'L_SUBMIT'=>$lang['submit'],

    'U_EMPTY_CADDIE'=>add_session_id($form_action.'&amp;empty=1'),
    
    'F_ACTION'=>add_session_id($form_action)
   )
 );
// +-----------------------------------------------------------------------+
// |                           global mode form                            |
// +-----------------------------------------------------------------------+

// Virtualy associate a picture to a category
$blockname = 'associate_option';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper($query, array(), $blockname, true);

// Dissociate from a category : categories listed for dissociation can
// only represent virtual links. Links to physical categories can't be
// broken
$blockname = 'dissociate_option';

$template->assign_block_vars(
  $blockname,
  array('SELECTED' => '',
        'VALUE'=> 0,
        'OPTION' => '------------'
    ));

$query = '
SELECT DISTINCT(category_id) AS id, c.name, uppercats, global_rank
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic,
       '.CADDIE_TABLE.' AS caddie,
       '.CATEGORIES_TABLE.' AS c,
       '.IMAGES_TABLE.' AS i
  WHERE ic.image_id = caddie.element_id
    AND ic.category_id = c.id
    AND ic.image_id = i.id
    AND ic.category_id != i.storage_category_id
    AND caddie.user_id = '.$user['id'].'
;';
display_select_cat_wrapper($query, array(), $blockname, true);

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

$query = '
SELECT element_id,path,tn_ext
  FROM '.IMAGES_TABLE.' INNER JOIN '.CADDIE_TABLE.' ON id=element_id
  WHERE user_id = '.$user['id'].'
  '.$conf['order_by'].'
;';
//echo '<pre>'.$query.'</pre>';
$result = pwg_query($query);

// template thumbnail initialization
if (mysql_num_rows($result) > 0)
{
  $template->assign_block_vars('thumbnails', array());
  // first line
  $template->assign_block_vars('thumbnails.line', array());
  // current row displayed
  $row_number = 0;
}

while ($row = mysql_fetch_array($result))
{
  $src = get_thumbnail_src($row['path'], @$row['tn_ext']);
      
  $template->assign_block_vars(
    'thumbnails.line.thumbnail',
    array(
      'ID' => $row['element_id'],
      'SRC' => $src,
      'ALT' => 'TODO',
      'TITLE' => 'TODO'
      )
    );
  
  // create a new line ?
  if (++$row_number == $user['nb_image_line'])
  {
    $template->assign_block_vars('thumbnails.line', array());
    $row_number = 0;
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_global');
?>
