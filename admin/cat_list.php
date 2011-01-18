<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

trigger_action('loc_begin_cat_list');

if (!empty($_POST) or isset($_GET['delete']))
{
  check_pwg_token();
}

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
  $current_rank_for_id_uppercat = array();
  $current_rank = 0;
  
  $datas = array();
  foreach ($categories as $category)
  {
    if (is_array($category))
    {
      $id = $category['id'];
      $id_uppercat = $category['id_uppercat'];

      if (!isset($current_rank_for_id_uppercat[$id_uppercat]))
      {
        $current_rank_for_id_uppercat[$id_uppercat] = 0;
      }
      $current_rank = ++$current_rank_for_id_uppercat[$id_uppercat];
    }
    else
    {
      $id = $category;
      $current_rank++;
    }
    
    array_push($datas, array('id' => $id, 'rank' => $current_rank));
  }
  $fields = array('primary' => array('id'), 'update' => array('rank'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);

  update_global_rank();
}

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+

check_input_parameter('parent_id', $_GET, false, PATTERN_ID);

$categories = array();

$base_url = get_root_url().'admin.php?page=cat_list';
$navigation = '<a href="'.$base_url.'">';
$navigation.= l10n('Home');
$navigation.= '</a>';

// +-----------------------------------------------------------------------+
// |                    virtual categories management                      |
// +-----------------------------------------------------------------------+
// request to delete a virtual category
if (isset($_GET['delete']) and is_numeric($_GET['delete']))
{
  delete_categories(array($_GET['delete']));
  $_SESSION['page_infos'] = array(l10n('Virtual album deleted'));
  update_global_rank();

  $redirect_url = get_root_url().'admin.php?page=cat_list';
  if (isset($_GET['parent_id']))
  {
    $redirect_url.= '&parent_id='.$_GET['parent_id'];
  }  
  redirect($redirect_url);
}
// request to add a virtual category
else if (isset($_POST['submitAdd']))
{
  $output_create = create_virtual_category(
    $_POST['virtual_name'],
    @$_GET['parent_id']
    );

  if (isset($output_create['error']))
  {
    array_push($page['errors'], $output_create['error']);
  }
  else
  {
    array_push($page['infos'], $output_create['info']);
  }
}
// save manual category ordering
else if (isset($_POST['submitOrder']))
{
  if ('manual' == $_POST['order_type'])
  {
    asort($_POST['catOrd'], SORT_NUMERIC);
    save_categories_order(array_keys($_POST['catOrd']));

    array_push(
      $page['infos'],
      l10n('Album manual order was saved')
      );
  }
  else
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.
    (!isset($_GET['parent_id']) ? 'IS NULL' : '= '.$_GET['parent_id']).'
;';
    $category_ids = array_from_query($query, 'id');

    if (isset($_POST['recursive']))
    {
      $category_ids = get_subcat_ids($category_ids);
    }

    $categories = array();
    $names = array();
    $id_uppercats = array();
  
    $query = '
SELECT id, name, id_uppercat
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      array_push(
        $categories,
        array(
          'id' => $row['id'],
          'id_uppercat' => $row['id_uppercat'],
          )
        );
      array_push(
        $names,
        $row['name']
        );
    }

    array_multisort(
      $names,
      SORT_REGULAR,
      'asc' == $_POST['ascdesc'] ? SORT_ASC : SORT_DESC,
      $categories
      );
    save_categories_order($categories);

    array_push(
      $page['infos'],
      l10n('Albums automatically sorted')
      );
  }
}

// +-----------------------------------------------------------------------+
// |                            Navigation path                            |
// +-----------------------------------------------------------------------+

if (isset($_GET['parent_id']))
{
  $navigation.= $conf['level_separator'];

  $navigation.= get_cat_display_name_from_id(
    $_GET['parent_id'],
    $base_url.'&amp;parent_id=',
    false
    );
}
// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filename('categories', 'cat_list.tpl');

$form_action = PHPWG_ROOT_PATH.'admin.php?page=cat_list';
if (isset($_GET['parent_id']))
{
  $form_action.= '&amp;parent_id='.$_GET['parent_id'];
}

$template->assign(array(
  'CATEGORIES_NAV'=>$navigation,
  'F_ACTION'=>$form_action,
  'PWG_TOKEN' => get_pwg_token(),
 ));

// +-----------------------------------------------------------------------+
// |                          Categories display                           |
// +-----------------------------------------------------------------------+

$categories = array();

$query = '
SELECT id, name, permalink, dir, rank, status
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
$categories = hash_from_query($query, 'id');

// get the categories containing images directly 
$categories_with_images = array();
if ( count($categories) )
{
  $query = '
SELECT DISTINCT category_id 
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', array_keys($categories)).')';
  $categories_with_images = array_flip( array_from_query($query, 'category_id') );
}

$template->assign('categories', array());
$base_url = get_root_url().'admin.php?page=';
foreach ($categories as $category)
{
  $cat_list_url = $base_url.'cat_list';

  $self_url = $cat_list_url;
  if (isset($_GET['parent_id']))
  {
    $self_url.= '&amp;parent_id='.$_GET['parent_id'];
  }

  $tpl_cat =
    array(
      'NAME'       => 
        trigger_event(
          'render_category_name',
          $category['name'],
          'admin_cat_list'
          ),
      'ID'         => $category['id'],
      'RANK'       => $category['rank']*10,

      'U_JUMPTO'   => make_index_url(
        array(
          'category' => $category
          )
        ),

      'U_CHILDREN' => $cat_list_url.'&amp;parent_id='.$category['id'],
      'U_EDIT'     => $base_url.'cat_modify&amp;cat_id='.$category['id'],

      'IS_VIRTUAL' => empty($category['dir'])
    );

  if (empty($category['dir']))
  {
    $tpl_cat['U_DELETE'] = $self_url.'&amp;delete='.$category['id'];
    $tpl_cat['U_DELETE'].= '&amp;pwg_token='.get_pwg_token();
  }

  if ( array_key_exists($category['id'], $categories_with_images) )
  {
    $tpl_cat['U_MANAGE_ELEMENTS']=
      $base_url.'batch_manager&amp;cat='.$category['id'];
  }

  if ('private' == $category['status'])
  {
    $tpl_cat['U_MANAGE_PERMISSIONS']=
      $base_url.'cat_perm&amp;cat='.$category['id'];
  }
  $template->append('categories', $tpl_cat);
}

trigger_action('loc_end_cat_list');

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
