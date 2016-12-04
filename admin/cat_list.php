<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

trigger_notify('loc_begin_cat_list');

if (!empty($_POST) or isset($_GET['delete']))
{
  check_pwg_token();
}

$sort_orders = array(
  'name ASC' => l10n('Album name, A &rarr; Z'),
  'name DESC' => l10n('Album name, Z &rarr; A'),
  'date_creation DESC' => l10n('Date created, new &rarr; old'),
  'date_creation ASC' => l10n('Date created, old &rarr; new'),
  'date_available DESC' => l10n('Date posted, new &rarr; old'),
  'date_available ASC' => l10n('Date posted, old &rarr; new'),
  );

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
    
    $datas[] = array('id' => $id, 'rank' => $current_rank);
  }
  $fields = array('primary' => array('id'), 'update' => array('rank'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);

  update_global_rank();
}

function get_categories_ref_date($ids, $field='date_available', $minmax='max')
{
  // we need to work on the whole tree under each category, even if we don't
  // want to sort sub categories
  $category_ids = get_subcat_ids($ids);
  
  // search for the reference date of each album
  $query = '
SELECT
    category_id,
    '.$minmax.'('.$field.') as ref_date
  FROM '.IMAGE_CATEGORY_TABLE.'
    JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE category_id IN ('.implode(',', $category_ids).')
  GROUP BY category_id
;';
  $ref_dates = query2array($query, 'category_id', 'ref_date');

  // the iterate on all albums (having a ref_date or not) to find the
  // reference_date, with a search on sub-albums
  $query = '
SELECT
    id,
    uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  $uppercats_of = query2array($query, 'id', 'uppercats');

  foreach (array_keys($uppercats_of) as $cat_id)
  {
    // find the subcats
    $subcat_ids = array();
    
    foreach ($uppercats_of as $id => $uppercats)
    {
      if (preg_match('/(^|,)'.$cat_id.'(,|$)/', $uppercats))
      {
        $subcat_ids[] = $id;
      }
    }

    $to_compare = array();
    foreach ($subcat_ids as $id)
    {
      if (isset($ref_dates[$id]))
      {
        $to_compare[] = $ref_dates[$id];
      }
    }

    if (count($to_compare) > 0)
    {
      $ref_dates[$cat_id] = 'max' == $minmax ? max($to_compare) : min($to_compare);
    }
    else
    {
      $ref_dates[$cat_id] = null;
    }
  }

  // only return the list of $ids, not the sub-categories
  $return = array();
  foreach ($ids as $id)
  {
    $return[$id] = $ref_dates[$id];
  }
  
  return $return;
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
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'list';
include(PHPWG_ROOT_PATH.'admin/include/albums_tab.inc.php');

// +-----------------------------------------------------------------------+
// |                    virtual categories management                      |
// +-----------------------------------------------------------------------+
// request to delete a virtual category
if (isset($_GET['delete']) and is_numeric($_GET['delete']))
{
  $photo_deletion_mode = 'no_delete';
  if (isset($_GET['photo_deletion_mode']))
  {
    $photo_deletion_mode = $_GET['photo_deletion_mode'];
  }
  delete_categories(array($_GET['delete']), $photo_deletion_mode);

  $_SESSION['page_infos'] = array(l10n('Virtual album deleted'));
  update_global_rank();
  invalidate_user_cache();

  $redirect_url = get_root_url().'admin.php?page=cat_list';
  if (isset($_GET['parent_id']))
  {
    $redirect_url.= '&parent_id='.$_GET['parent_id'];
  }
  redirect($redirect_url);
}
// request to add a virtual category
elseif (isset($_POST['submitAdd']))
{
  $output_create = create_virtual_category(
    $_POST['virtual_name'],
    @$_GET['parent_id']
    );

  invalidate_user_cache();
  if (isset($output_create['error']))
  {
    $page['errors'][] = $output_create['error'];
  }
  else
  {
    $edit_url = get_root_url().'admin.php?page=album-'.$output_create['id'];
    $page['infos'][] = $output_create['info'].' <a class="icon-pencil" href="'.$edit_url.'">'.l10n('Edit album').'</a>';
  }
}
// save manual category ordering
elseif (isset($_POST['submitManualOrder']))
{
  asort($_POST['catOrd'], SORT_NUMERIC);
  save_categories_order(array_keys($_POST['catOrd']));

  $page['infos'][] = l10n('Album manual order was saved');
}
elseif (isset($_POST['submitAutoOrder']))
{
  if (!isset($sort_orders[ $_POST['order_by'] ]))
  {
    die('Invalid sort order');
  }

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
  $sort = array();

  list($order_by_field, $order_by_asc) = explode(' ', $_POST['order_by']);
  
  $order_by_date = false;
  if (strpos($order_by_field, 'date_') === 0)
  {
    $order_by_date = true;
    
    $ref_dates = get_categories_ref_date(
      $category_ids,
      $order_by_field,
      'ASC' == $order_by_asc ? 'min' : 'max'
      );
  }

  $query = '
SELECT id, name, id_uppercat
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if ($order_by_date)
    {
      $sort[] = $ref_dates[ $row['id'] ];
    }
    else
    {
      $sort[] = remove_accents($row['name']);
    }
    
    $categories[] = array(
      'id' => $row['id'],
      'id_uppercat' => $row['id_uppercat'],
      );
  }

  array_multisort(
    $sort,
    SORT_REGULAR,
    'ASC' == $order_by_asc ? SORT_ASC : SORT_DESC,
    $categories
    );
  
  save_categories_order($categories);

  $page['infos'][] = l10n('Albums automatically sorted');
}

// +-----------------------------------------------------------------------+
// |                            Navigation path                            |
// +-----------------------------------------------------------------------+

if (isset($_GET['parent_id']))
{
  $navigation.= $conf['level_separator'];

  $navigation.= get_cat_display_name_from_id(
    $_GET['parent_id'],
    $base_url.'&amp;parent_id='
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
$sort_orders_checked = array_keys($sort_orders);

$template->assign(array(
  'CATEGORIES_NAV'=>$navigation,
  'F_ACTION'=>$form_action,
  'PWG_TOKEN' => get_pwg_token(),
  'sort_orders' => $sort_orders,
  'sort_order_checked' => array_shift($sort_orders_checked),
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
if (count($categories))
{
  $query = '
SELECT
    category_id,
    COUNT(*) AS nb_photos
  FROM '.IMAGE_CATEGORY_TABLE.'
  GROUP BY category_id
;';
  // WHERE category_id IN ('.implode(',', array_keys($categories)).')

  $nb_photos_in = query2array($query, 'category_id', 'nb_photos');

  $query = '
SELECT
    id,
    uppercats
  FROM '.CATEGORIES_TABLE.'
;';
  $all_categories = query2array($query, 'id', 'uppercats');
  $subcats_of = array();

  foreach (array_keys($categories) as $cat_id)
  {
    foreach ($all_categories as $id => $uppercats)
    {
      if (preg_match('/(^|,)'.$cat_id.',/', $uppercats))
      {
        @$subcats_of[$cat_id][] = $id;
      }
    }
  }

  $nb_sub_photos = array();
  foreach ($subcats_of as $cat_id => $subcat_ids)
  {
    $nb_photos = 0;
    foreach ($subcat_ids as $id)
    {
      if (isset($nb_photos_in[$id]))
      {
        $nb_photos+= $nb_photos_in[$id];
      }
    }

    $nb_sub_photos[$cat_id] = $nb_photos;
  }
}

$template->assign('categories', array());
$base_url = get_root_url().'admin.php?page=';

if (isset($_GET['parent_id']))
{
  $template->assign(
    'PARENT_EDIT',
    $base_url.'album-'.$_GET['parent_id']
    );
}

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
        trigger_change(
          'render_category_name',
          $category['name'],
          'admin_cat_list'
          ),
      'NB_PHOTOS' => isset($nb_photos_in[$category['id']]) ? $nb_photos_in[$category['id']] : 0,
      'NB_SUB_PHOTOS' => isset($nb_sub_photos[$category['id']]) ? $nb_sub_photos[$category['id']] : 0,
      'NB_SUB_ALBUMS' => isset($subcats_of[$category['id']]) ? count($subcats_of[$category['id']]) : 0,
      'ID'         => $category['id'],
      'RANK'       => $category['rank']*10,

      'U_JUMPTO'   => make_index_url(
        array(
          'category' => $category
          )
        ),

      'U_CHILDREN' => $cat_list_url.'&amp;parent_id='.$category['id'],
      'U_EDIT'     => $base_url.'album-'.$category['id'],

      'IS_VIRTUAL' => empty($category['dir'])
    );

  if (empty($category['dir']))
  {
    $tpl_cat['U_DELETE'] = $self_url.'&amp;delete='.$category['id'];
    $tpl_cat['U_DELETE'].= '&amp;pwg_token='.get_pwg_token();
  }
  else
  {
    if ($conf['enable_synchronization'])
    {
      $tpl_cat['U_SYNC'] = $base_url.'site_update&amp;site=1&amp;cat_id='.$category['id'];
    }
  }

  $template->append('categories', $tpl_cat);
}

trigger_notify('loc_end_cat_list');

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'categories');
?>
