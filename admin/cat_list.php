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
// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+
$errors = array();
$infos = array();
$categories = array();
$navigation = $lang['home'];
// +-----------------------------------------------------------------------+
// |                    virtual categories management                      |
// +-----------------------------------------------------------------------+
// request to delete a virtual category
if (isset($_GET['delete']) and is_numeric($_GET['delete']))
{
  delete_categories(array($_GET['delete']));
  array_push($infos, $lang['cat_virtual_deleted']);
  ordering();
  update_global_rank();
}
// request to add a virtual category
else if (isset($_POST['submit']))
{
  // is the given category name only containing blank spaces ?
  if (preg_match('/^\s*$/', $_POST['virtual_name']))
  {
    array_push($errors, $lang['cat_error_name']);
  }
	
  if (!count($errors))
  {
    $parent_id = !empty($_GET['parent_id'])?$_GET['parent_id']:'NULL';
    
    if ($parent_id != 'NULL')
    {
      $query = '
SELECT id,uppercats,global_rank,visible,status
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$parent_id.'
;';
      $row = mysql_fetch_array(pwg_query($query));
      $parent = array('id' => $row['id'],
                      'uppercats' => $row['uppercats'],
                      'visible' => $row['visible'],
                      'status' => $row['status'],
                      'global_rank' => $row['global_rank']);
    }

    // what will be the inserted id ?
    $query = '
SELECT MAX(id)+1
  FROM '.CATEGORIES_TABLE.'
;';
    list($next_id) = mysql_fetch_array(pwg_query($query));
    
    $insert = array();
    $insert{'id'} = $next_id++;
    $insert{'name'} = $_POST['virtual_name'];
    $insert{'rank'} = $_POST['rank'];
    $insert{'commentable'} = $conf['newcat_default_commentable'];

    // a virtual category can't be uploadable
    $insert{'uploadable'} = 'false';
    
    if (isset($parent))
    {
      $insert{'id_uppercat'} = $parent{'id'};
      $insert{'uppercats'}   = $parent{'uppercats'}.','.$insert{'id'};
      $insert{'global_rank'} = $parent{'global_rank'}.'.'.$insert{'rank'};
      // at creation, must a category be visible or not ? Warning : if
      // the parent category is invisible, the category is automatically
      // create invisible. (invisible = locked)
      if ('false' == $parent['visible'])
      {
        $insert{'visible'} = 'false';
      }
      else
      {
        $insert{'visible'} = $conf['newcat_default_visible'];
      }
      // at creation, must a category be public or private ? Warning :
      // if the parent category is private, the category is
      // automatically create private.
      if ('private' == $parent['status'])
      {
        $insert{'status'} = 'private';
      }
      else
      {
        $insert{'status'} = $conf['newcat_default_status'];
      }
    }
    else
    {
      $insert{'visible'} = $conf['newcat_default_visible'];
      $insert{'status'} = $conf['newcat_default_status'];
      $insert{'uppercats'} = $insert{'id'};
      $insert{'global_rank'} = $insert{'rank'};
    }

    $inserts = array($insert);
    
    // we have then to add the virtual category
    $dbfields = array('id','site_id','name','id_uppercat','rank',
                      'commentable','uploadable','visible','status',
                      'uppercats','global_rank');
    mass_inserts(CATEGORIES_TABLE, $dbfields, $inserts);

    array_push($infos, $lang['cat_virtual_added']);
  }
}
// +-----------------------------------------------------------------------+
// |                           Cache management                            |
// +-----------------------------------------------------------------------+
$query = '
SELECT *
  FROM '.CATEGORIES_TABLE;
if (!isset($_GET['parent_id']))
{
  $query.= '
  WHERE id_uppercat IS NULL';
}
else
{
  $query.= '
  WHERE id_uppercat = '.$_GET['parent_id'];
}
$query.= '
  ORDER BY rank ASC
;';
$result = pwg_query($query);
while ($row = mysql_fetch_assoc($result))
{
  $categories[$row['rank']] = $row;
  $categories[$row['rank']]['nb_subcats'] = 0;
}
// +-----------------------------------------------------------------------+
// |                            Navigation path                            |
// +-----------------------------------------------------------------------+
if (isset($_GET['parent_id']))
{
  $base_url = PHPWG_ROOT_PATH.'admin.php?page=cat_list';
  
  $navigation = '<a class="" href="'.add_session_id($base_url).'">';
  $navigation.= $lang['home'];
  $navigation.= '</a>';
  $navigation.= $conf['level_separator'];

  $current_category = get_cat_info($_GET['parent_id']);
  $navigation.= get_cat_display_name($current_category['name'],
                                     $base_url.'&amp;parent_id=',
                                     false);
}
// +-----------------------------------------------------------------------+
// |                               rank updates                            |
// +-----------------------------------------------------------------------+
$current_rank = 0;
if (isset($_GET['up']) and is_numeric($_GET['up']))
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
  if ($current_rank > 1)
  {
    // 2. Exchanging ranks between the two categories
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = '.($current_rank-1).'
  WHERE id = '.$_GET['up'].'
;';
    pwg_query($query);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = '.$current_rank.'
  WHERE id = '.$categories[($current_rank-1)]['id'].'
;';
    pwg_query($query);
    // 3. Updating the cache array
    $categories[$current_rank] = $categories[($current_rank-1)];
    $categories[($current_rank-1)] = $current;
  }
  else
  {
    // 2. Updating the rank of our category to be after the previous max rank
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = '.(count($categories) + 1).'
  WHERE id = '.$_GET['up'].'
;';
    pwg_query($query);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = rank-1
  WHERE id_uppercat ';
    if (empty($_GET['parent_id']))
    {
      $query.= 'IS NULL';
    }
    else
    {
      $query.= '= '.$_GET['parent_id'];
    }
    $query.= '
;';
    pwg_query($query);
    // 3. Updating the cache array
    array_push($categories, $current);
    array_shift($categories);
  }
  update_global_rank(@$_GET['parent_id']);
}
else if (isset($_GET['down']) and is_numeric($_GET['down']))
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
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = '.($current_rank+1).'
  WHERE id = '.$_GET['down'].'
;';
    pwg_query($query);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = '.$current_rank.'
  WHERE id = '.$categories[($current_rank+1)]['id'].'
;';
    pwg_query($query);
    // 3. Updating the cache array
    $categories[$current_rank]=$categories[($current_rank+1)];
    $categories[($current_rank+1)] = $current;
  }
  else 
  {
    // 2. updating the rank of our category to be the first one
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = 0
  WHERE id = '.$_GET['down'].'
;';
    pwg_query($query);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET rank = rank+1
  WHERE id_uppercat ';
    if (empty($_GET['parent_id']))
    {
      $query.= 'IS NULL';
    }
    else
    {
      $query.= '= '.$_GET['parent_id'];
    }
    $query.= '
;';
    pwg_query($query);
    // 3. Updating the cache array
    array_unshift($categories, $current);
    array_pop($categories);
  }
  update_global_rank(@$_GET['parent_id']);
}
reset($categories);
// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('categories'=>'admin/cat_list.tpl'));

$form_action = PHPWG_ROOT_PATH.'admin.php?page=cat_list';
if (isset($_GET['parent_id']))
{
  $form_action.= '&amp;parent_id='.$_GET['parent_id'];
}

if (count($categories) > 0)
{
  $next_rank = max(array_keys($categories)) + 1;
}
else
{
  $next_rank = 1;
}

$template->assign_vars(array(
  'CATEGORIES_NAV'=>$navigation,
  'NEXT_RANK'=>$next_rank,
  'F_ACTION'=>$form_action,
  
  'L_ADD_VIRTUAL'=>$lang['cat_add'],
  'L_SUBMIT'=>$lang['submit'],
  'L_STORAGE'=>$lang['storage'],
  'L_NB_IMG'=>$lang['pictures'],
  'L_MOVE_UP'=>$lang['up'],
  'L_MOVE_DOWN'=>$lang['down'],
  'L_EDIT'=>$lang['edit'],
  'L_INFO_IMG'=>$lang['cat_image_info'],
  'L_DELETE'=>$lang['delete'],
 ));
  
$tpl = array('cat_first','cat_last');
// +-----------------------------------------------------------------------+
// |                            errors & infos                             |
// +-----------------------------------------------------------------------+
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}
if (count($infos) != 0)
{
  $template->assign_block_vars('infos',array());
  foreach ($infos as $info)
  {
    $template->assign_block_vars('infos.info',array('INFO'=>$info));
  }
}
// +-----------------------------------------------------------------------+
// |                          Categories display                           |
// +-----------------------------------------------------------------------+
$ranks = array();

if (count($categories) > 0)
{
  foreach ($categories as $category)
  {
    $ranks[$category['id']] = $category['rank'];
  }

  $query = '
SELECT id_uppercat, COUNT(*) AS nb_subcats
  FROM '. CATEGORIES_TABLE.'
  WHERE id_uppercat IN ('.implode(',', array_keys($ranks)).')
  GROUP BY id_uppercat
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $categories[$ranks[$row['id_uppercat']]]['nb_subcats']
      = $row['nb_subcats'];
  }
}

foreach ($categories as $category)
{
  $images_folder = PHPWG_ROOT_PATH.'template/';
  $images_folder.= $user['template'].'/admin/images';
  
  if ($category['visible'] == 'false')
  {
    $image_src = $images_folder.'/icon_folder_lock.gif';
    $image_alt = $lang['cat_private'];
    $image_title = $lang['cat_private'];
  }
  else if (empty($category['dir']))
  {
    $image_src = $images_folder.'/icon_folder_link.gif';
    $image_alt = $lang['cat_virtual'];
    $image_title = $lang['cat_virtual'];
  }
  else
  {
    if ($category['nb_subcats'] > 0)
    {
      $image_src = $images_folder.'/icon_subfolder.gif';
    }
    else
    {
      $image_src = $images_folder.'/icon_folder.gif';
    }
    $image_alt = '';
    $image_title = '';
  }

  $base_url = PHPWG_ROOT_PATH.'admin.php?page=';
  $cat_list_url = $base_url.'cat_list';
  
  $self_url = $cat_list_url;
  if (isset($_GET['parent_id']))
  {
    $self_url.= '&amp;parent_id='.$_GET['parent_id'];
  }

  $template->assign_block_vars(
    'category',
    array(
      'CATEGORY_IMG_SRC'=>$image_src,
      'CATEGORY_IMG_ALT'=>$image_alt,
      'CATEGORY_IMG_TITLE'=>$image_title,
      'CATEGORY_NAME'=>$category['name'],
      'CATEGORY_DIR'=>@$category['dir'],
      'CATEGORY_NB_IMG'=>$category['nb_images'],
      
      'U_CATEGORY'=>
      add_session_id($cat_list_url.'&amp;parent_id='.$category['id']),
      
      'U_MOVE_UP'=>add_session_id($self_url.'&amp;up='.$category['id']),
      
      'U_MOVE_DOWN'=>add_session_id($self_url.'&amp;down='.$category['id']),
      
      'U_CAT_EDIT'=>
      add_session_id($base_url.'cat_modify&amp;cat_id='.$category['id']),
      
      'U_CAT_DELETE'=>add_session_id($self_url.'&amp;delete='.$category['id']),
      
      'U_INFO_IMG'
      => add_session_id($base_url.'infos_images&amp;cat_id='.$category['id'])
      ));
  
  if (!empty($category['dir']))
  {
    $template->assign_block_vars('category.storage' ,array());
  }
  else
  {
    $template->assign_block_vars('category.virtual' ,array());
  }
  
  if ($category['nb_images'] > 0)
  {
    $template->assign_block_vars('category.image_info' ,array());
  }
  else
  {
    $template->assign_block_vars('category.no_image_info' ,array()); 
  }
}
// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
