<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

$query = '
SELECT
    COUNT(*)
  FROM '.CATEGORIES_TABLE.'
;';
list($albums_counter) = pwg_db_fetch_row(pwg_query($query));

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'list';
include(PHPWG_ROOT_PATH.'admin/include/albums_tab.inc.php');

// +-----------------------------------------------------------------------+
// |                         categories auto order                         |
// +-----------------------------------------------------------------------+

$open_cat = -1;

$sort_orders = array(
  'name ASC',
  'name DESC',
  'date_creation DESC',
  'date_creation ASC',
  'date_available DESC',
  'date_available ASC',
  'natural_order DESC',
  'natural_order ASC'
);

if (isset($_POST['simpleAutoOrder']) || isset($_POST['recursiveAutoOrder']) )
{

  if (!in_array($_POST['order'],$sort_orders))
  {
    die('Invalid sort order');
  }
  check_input_parameter('id', $_POST, false, '/^-?\d+$/');
  
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.
    (($_POST['id'] === '-1') ? 'IS NULL' : '= '.$_POST['id']).'
;';
  $category_ids = array_from_query($query, 'id');

  if (isset($_POST['recursiveAutoOrder']))
  {
    $category_ids = get_subcat_ids($category_ids);
  }
  
  $categories = array();
  $sort = array();

  list($order_by_field, $order_by_asc) = explode(' ', $_POST['order']);
  
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
    $row['name'] = trigger_change('render_category_name', $row['name'], 'admin_cat_list');

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
    $order_by_field === "natural_order" ? SORT_NATURAL : SORT_REGULAR,
    'ASC' == $order_by_asc ? SORT_ASC : SORT_DESC,
    $categories
    );
  
  save_categories_order($categories);

  $open_cat = $_POST['id'];
}

$template->assign('open_cat', $open_cat);

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filename('albums', 'albums.tpl');

$template->assign(
  array(
    'F_ACTION' => get_root_url().'admin.php?page=albums',
    )
  );

$template->assign('delay_before_autoOpen', $conf['album_move_delay_before_auto_opening']);

$template->assign("POS_PREF", $conf['newcat_default_position']); //TODO use user pref if it exists

// +-----------------------------------------------------------------------+
// |                          Album display                                |
// +-----------------------------------------------------------------------+

//Get all albums
$query = '
SELECT id,name,`rank`,status, uppercats, lastmodified
  FROM '.CATEGORIES_TABLE.'
;';

$allAlbum = query2array($query);

//Make an id tree
$associatedTree = array();

foreach ($allAlbum as $album) 
{
  $album['name'] = trigger_change('render_category_name', $album['name'], 'admin_cat_list');
  $album['lastmodified'] = time_since($album['lastmodified'], 'year');

  $parents = explode(',',$album['uppercats']);
  $the_place = &$associatedTree[strval($parents[0])];
  for ($i=1; $i < count($parents); $i++) 
  { 
    $the_place = &$the_place['children'][strval($parents[$i])];
  }
  $the_place['cat'] = $album;
}

$is_forbidden = array_fill_keys(@explode(',', $user['forbidden_categories']), 1);

//Make an ordered tree
function cmpCat($a, $b) 
{
  if ($a['rank'] == $b['rank']) 
  {
    return 0;
  }
  return ($a['rank'] < $b['rank']) ? -1 : 1;
}

function assocToOrderedTree($assocT) 
{
  global $nb_photos_in, $nb_sub_photos, $is_forbidden;

  $orderedTree = array();

  foreach($assocT as $cat) 
  {
    $orderedCat = array();
    $orderedCat['rank'] = $cat['cat']['rank'];
    $orderedCat['name'] = $cat['cat']['name'];
    $orderedCat['status'] = $cat['cat']['status'];
    $orderedCat['id'] = $cat['cat']['id'];
    $orderedCat['nb_images'] = isset($nb_photos_in[$cat['cat']['id']]) ? $nb_photos_in[$cat['cat']['id']] : 0;
    $orderedCat['last_updates'] = $cat['cat']['lastmodified'];
    $orderedCat['has_not_access'] = isset($is_forbidden[$cat['cat']['id']]);
    $orderedCat['nb_sub_photos'] = isset($nb_sub_photos[$cat['cat']['id']]) ? $nb_sub_photos[$cat['cat']['id']] : 0;
    if (isset($cat['children'])) 
    {
      //Does not update when moving a node
      $orderedCat['nb_subcats'] = count($cat['children']);
      $orderedCat['children'] = assocToOrderedTree($cat['children']);
    }
    array_push($orderedTree, $orderedCat);
  }
  usort($orderedTree, 'cmpCat');
  return $orderedTree;
}

$query = '
SELECT
    category_id,
    COUNT(*) AS nb_photos
  FROM '.IMAGE_CATEGORY_TABLE.'
  GROUP BY category_id
;';

$nb_photos_in = query2array($query, 'category_id', 'nb_photos');

$query = '
SELECT
    id,
    uppercats
  FROM '.CATEGORIES_TABLE.'
;';
$all_categories = query2array($query, 'id', 'uppercats');

$subcats_of = array();

foreach ($all_categories as $id => $uppercats)
{
  foreach (array_slice(explode(',', $uppercats), 0, -1) as $uppercat_id)
  {
    @$subcats_of[$uppercat_id][] = $id;
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

$template->assign(
  array(
    'album_data' => assocToOrderedTree($associatedTree),
    'PWG_TOKEN' => get_pwg_token(),
    'nb_albums' => count($allAlbum),
    'ADMIN_PAGE_TITLE' => l10n('Albums'),
    'light_album_manager' => ($albums_counter > $conf['light_album_manager_threshold']) ? 1 : 0,
  )
);

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'albums');

// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+
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
?>
