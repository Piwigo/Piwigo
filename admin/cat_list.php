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
  die('Hacking attempt!');
}
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * save the rank depending on given categories order
 *
 * The list of ordered categories id is supposed to be in the same parent
 * category
 *
 * @param array categories
 * @return void
 */
function save_categories_order($categories)
{
  $current_rank = 0;
  $datas = array();
  foreach ($categories as $id)
  {
    array_push($datas, array('id' => $id, 'rank' => ++$current_rank));
  }
  $fields = array('primary' => array('id'), 'update' => array('rank'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);

  update_global_rank(@$_GET['parent_id']);
}

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+

$categories = array();

$base_url = PHPWG_ROOT_PATH.'admin.php?page=cat_list';
$navigation = '<a class="" href="'.add_session_id($base_url).'">';
$navigation.= $lang['home'];
$navigation.= '</a>';

// +-----------------------------------------------------------------------+
// |                    virtual categories management                      |
// +-----------------------------------------------------------------------+
// request to delete a virtual category
if (isset($_GET['delete']) and is_numeric($_GET['delete']))
{
  delete_categories(array($_GET['delete']));
  array_push($page['infos'], $lang['cat_virtual_deleted']);
  ordering();
  update_global_rank();
}
// request to add a virtual category
else if (isset($_POST['submitAdd']))
{
  // is the given category name only containing blank spaces ?
  if (preg_match('/^\s*$/', $_POST['virtual_name']))
  {
    array_push($page['errors'], $lang['cat_error_name']);
  }
	
  if (!count($page['errors']))
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
SELECT IF(MAX(id)+1 IS NULL, 1, MAX(id)+1)
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

    array_push($page['infos'], $lang['cat_virtual_added']);
  }
}
else if (isset($_POST['submitOrder']))
{
  asort($_POST['catOrd'], SORT_NUMERIC);
  save_categories_order(array_keys($_POST['catOrd']));
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
  $navigation.= $conf['level_separator'];

  $current_category = get_cat_info($_GET['parent_id']);
  $navigation.= get_cat_display_name($current_category['name'],
                                     $base_url.'&amp;parent_id=',
                                     false);
}
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
// |                          Categories display                           |
// +-----------------------------------------------------------------------+

$categories = array();

$query = '
SELECT id, name, dir, rank, nb_images, status
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
while ($row = mysql_fetch_array($result))
{
  $categories[$row['id']] = $row;
  // by default, let's consider there is no sub-categories. This will be
  // calculated after.
  $categories[$row['id']]['nb_subcats'] = 0;
}

if (count($categories) > 0)
{
  $query = '
SELECT id_uppercat, COUNT(*) AS nb_subcats
  FROM '. CATEGORIES_TABLE.'
  WHERE id_uppercat IN ('.implode(',', array_keys($categories)).')
  GROUP BY id_uppercat
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $categories[$row['id_uppercat']]['nb_subcats'] = $row['nb_subcats'];
  }
}

foreach ($categories as $category)
{
  $images_folder = PHPWG_ROOT_PATH.'template/';
  $images_folder.= $user['template'].'/admin/images';
  
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
      'NAME'=>$category['name'],
      'ID'=>$category['id'],
      'RANK'=>$category['rank']*10,

      'U_JUMPTO'=>
      add_session_id(PHPWG_ROOT_PATH.'category.php?cat='.$category['id']),
      
      'U_CHILDREN'=>
      add_session_id($cat_list_url.'&amp;parent_id='.$category['id']),
      
      'U_EDIT'=>
      add_session_id($base_url.'cat_modify&amp;cat_id='.$category['id'])
      )
    );
  
  if (empty($category['dir']))
  {
    $template->assign_block_vars(
      'category.delete',
      array(
        'URL'=>add_session_id($self_url.'&amp;delete='.$category['id'])
        )
      );
  }
  
  if ($category['nb_images'] > 0)
  {
    $template->assign_block_vars(
      'category.elements',
      array(
        'URL'=>add_session_id($base_url.'element_set&amp;cat='.$category['id'])
        )
      );
  }

  if ('private' == $category['status'])
  {
    $template->assign_block_vars(
      'category.permissions',
      array(
        'URL'=>add_session_id($base_url.'cat_perm&amp;cat='.$category['id'])
        )
      );
  }
}
// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
