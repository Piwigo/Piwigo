<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * Management of elements set. Elements can belong to a category or to the
 * user caddie.
 *
 */

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

if (!empty($_POST))
{
  check_pwg_token();
}

trigger_notify('loc_begin_element_set_global');

check_input_parameter('del_tags', $_POST, true, PATTERN_ID);
check_input_parameter('associate', $_POST, false, PATTERN_ID);
check_input_parameter('dissociate', $_POST, false, PATTERN_ID);

// +-----------------------------------------------------------------------+
// |                            current selection                          |
// +-----------------------------------------------------------------------+

$collection = array();
if (isset($_POST['nb_photos_deleted']))
{
  check_input_parameter('nb_photos_deleted', $_POST, false, '/^\d+$/');

  // let's fake a collection (we don't know the image_ids so we use "null", we only
  // care about the number of items here)
  $collection = array_fill(0, $_POST['nb_photos_deleted'], null);
}
else if (isset($_POST['setSelected']))
{
  $collection = $page['cat_elements_id'];
}
else if (isset($_POST['selection']))
{
  $collection = $_POST['selection'];
}

// +-----------------------------------------------------------------------+
// |                       global mode form submission                     |
// +-----------------------------------------------------------------------+

// $page['prefilter'] is a shortcut to test if the current filter contains a
// given prefilter. The idea is to make conditions simpler to write in the
// code.
$page['prefilter'] = 'none';
if (isset($_SESSION['bulk_manager_filter']['prefilter']))
{
  $page['prefilter'] = $_SESSION['bulk_manager_filter']['prefilter'];
}

$redirect_url = get_root_url().'admin.php?page='.$_GET['page'];

if (isset($_POST['submit']))
{
  // if the user tries to apply an action, it means that there is at least 1
  // photo in the selection
  if (count($collection) == 0)
  {
    $page['errors'][] = l10n('Select at least one photo');
  }

  $action = $_POST['selectAction'];
  $redirect = false;

  if ('remove_from_caddie' == $action)
  {
    $query = '
DELETE
  FROM '.CADDIE_TABLE.'
  WHERE element_id IN ('.implode(',', $collection).')
    AND user_id = '.$user['id'].'
;';
    pwg_query($query);

    // remove from caddie action available only in caddie so reload content
    $redirect = true;
  }

  else if ('add_tags' == $action)
  {
    if (empty($_POST['add_tags']))
    {
      $page['errors'][] = l10n('Select at least one tag');
    }
    else
    {
      $tag_ids = get_tag_ids($_POST['add_tags']);
      add_tags($tag_ids, $collection);

      if ('no_tag' == $page['prefilter'])
      {
        $redirect = true;
      }
    }
  }

  else if ('del_tags' == $action)
  {
    if (isset($_POST['del_tags']) and count($_POST['del_tags']) > 0)
    {
      $taglist_before = get_image_tag_ids($collection);

      $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $collection).')
    AND tag_id IN ('.implode(',', $_POST['del_tags']).')
;';
      pwg_query($query);

      $taglist_after = get_image_tag_ids($collection);
      $images_to_update = compare_image_tag_lists($taglist_before, $taglist_after);
      update_images_lastmodified($images_to_update);

      if (isset($_SESSION['bulk_manager_filter']['tags']) &&
        count(array_intersect($_SESSION['bulk_manager_filter']['tags'], $_POST['del_tags'])))
      {
        $redirect = true;
      }
    }
    else
    {
      $page['errors'][] = l10n('Select at least one tag');
    }
  }

  if ('associate' == $action)
  {
    associate_images_to_categories(
      $collection,
      array($_POST['associate'])
      );

    $_SESSION['page_infos'] = array(
      l10n('Information data registered in database')
      );

    // let's refresh the page because we the current set might be modified
    if ('no_album' == $page['prefilter'])
    {
      $redirect = true;
    }

    else if ('no_virtual_album' == $page['prefilter'])
    {
      $category_info = get_cat_info($_POST['associate']);
      if (empty($category_info['dir']))
      {
        $redirect = true;
      }
    }
  }

  else if ('move' == $action)
  {
    move_images_to_categories($collection, array($_POST['associate']));

    $_SESSION['page_infos'] = array(
      l10n('Information data registered in database')
      );

    // let's refresh the page because we the current set might be modified
    if ('no_album' == $page['prefilter'])
    {
      $redirect = true;
    }

    else if ('no_virtual_album' == $page['prefilter'])
    {
      $category_info = get_cat_info($_POST['associate']);
      if (empty($category_info['dir']))
      {
        $redirect = true;
      }
    }

    else if (isset($_SESSION['bulk_manager_filter']['category'])
        and $_POST['move'] != $_SESSION['bulk_manager_filter']['category'])
    {
      $redirect = true;
    }
  }

  else if ('dissociate' == $action)
  {
    // physical links must not be broken, so we must first retrieve image_id
    // which create virtual links with the category to "dissociate from".
    $query = '
SELECT id
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE category_id = '.$_POST['dissociate'].'
    AND id IN ('.implode(',', $collection).')
    AND (
      category_id != storage_category_id
      OR storage_category_id IS NULL
    )
;';
    $dissociables = array_from_query($query, 'id');

    if (!empty($dissociables))
    {
      $query = '
DELETE
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_POST['dissociate'].'
    AND image_id IN ('.implode(',', $dissociables).')
';
      pwg_query($query);

      $_SESSION['page_infos'] = array(
        l10n('Information data registered in database')
        );

      // let's refresh the page because the current set might be modified
      $redirect = true;
    }
  }

  // author
  else if ('author' == $action)
  {
    if (isset($_POST['remove_author']))
    {
      $_POST['author'] = null;
    }

    $datas = array();
    foreach ($collection as $image_id)
    {
      $datas[] = array(
        'id' => $image_id,
        'author' => $_POST['author']
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('author')),
      $datas
      );

    pwg_activity('photo', $collection, 'edit', array('action'=>'author'));
  }

  // title
  else if ('title' == $action)
  {
    if (isset($_POST['remove_title']))
    {
      $_POST['title'] = null;
    }

    $datas = array();
    foreach ($collection as $image_id)
    {
      $datas[] = array(
        'id' => $image_id,
        'name' => $_POST['title']
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('name')),
      $datas
      );

    pwg_activity('photo', $collection, 'edit', array('action'=>'title'));
  }

  // date_creation
  else if ('date_creation' == $action)
  {
    if (isset($_POST['remove_date_creation']) || empty($_POST['date_creation']))
    {
      $date_creation = null;
    }
    else
    {
      $date_creation = $_POST['date_creation'];
    }

    $datas = array();
    foreach ($collection as $image_id)
    {
      $datas[] = array(
        'id' => $image_id,
        'date_creation' => $date_creation
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('date_creation')),
      $datas
      );

    pwg_activity('photo', $collection, 'edit', array('action'=>'date_creation'));
  }

  // privacy_level
  else if ('level' == $action)
  {
    $datas = array();
    foreach ($collection as $image_id)
    {
      $datas[] = array(
        'id' => $image_id,
        'level' => $_POST['level']
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('level')),
      $datas
      );

    pwg_activity('photo', $collection, 'edit', array('action'=>'privacy_level'));

    if (isset($_SESSION['bulk_manager_filter']['level']))
    {
      if ($_POST['level'] < $_SESSION['bulk_manager_filter']['level'])
      {
        $redirect = true;
      }
    }
  }

  // add_to_caddie
  else if ('add_to_caddie' == $action)
  {
    fill_caddie($collection);
  }

  // delete
  else if ('delete' == $action)
  {
    if (isset($_POST['confirm_deletion']) and 1 == $_POST['confirm_deletion'])
    {
      // now done with ajax calls, with blocks
      // $deleted_count = delete_elements($collection, true);
      if (count($collection) > 0)
      {
        $_SESSION['page_infos'][] = l10n_dec(
          '%d photo was deleted', '%d photos were deleted',
          count($collection)
          );

        $redirect_url = get_root_url().'admin.php?page='.$_GET['page'];
        $redirect = true;
      }
      else
      {
        $page['errors'][] = l10n('No photo can be deleted');
      }
    }
    else
    {
      $page['errors'][] = l10n('You need to confirm deletion');
    }
  }

  // synchronize metadata
  else if ('metadata' == $action)
  {
    $page['infos'][] = l10n('Metadata synchronized from file').' <span class="badge">'.count($collection).'</span>';
  }

  else if ('delete_derivatives' == $action && !empty($_POST['del_derivatives_type']))
  {
    $query='SELECT path,representative_ext FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')';
    $result = pwg_query($query);
    while ($info = pwg_db_fetch_assoc($result))
    {
      foreach( $_POST['del_derivatives_type'] as $type)
      {
        delete_element_derivatives($info, $type);
      }
    }
  }

  else if ('generate_derivatives' == $action)
  {
    if ($_POST['regenerateSuccess'] != '0')
    {
      $page['infos'][] = l10n('%s photos have been regenerated', $_POST['regenerateSuccess']);
    }
    if ($_POST['regenerateError'] != '0')
    {
      $page['warnings'][] = l10n('%s photos can not be regenerated', $_POST['regenerateError']);
    }
  }

  if (!in_array($action, array('remove_from_caddie','add_to_caddie','delete_derivatives','generate_derivatives')))
  {
    invalidate_user_cache();
  }

  trigger_notify('element_set_global_action', $action, $collection);

  if ($redirect)
  {
    redirect($redirect_url);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('batch_manager_global' => 'batch_manager_global.tpl'));

$base_url = get_root_url().'admin.php';

$prefilters = array(
  array('ID' => 'caddie', 'NAME' => l10n('Caddie')),
  array('ID' => 'favorites', 'NAME' => l10n('Your favorites')),
  array('ID' => 'last_import', 'NAME' => l10n('Last import')),
  array('ID' => 'no_album', 'NAME' => l10n('With no album').' ('.l10n('Orphans').')'),
  array('ID' => 'no_tag', 'NAME' => l10n('With no tag')),
  array('ID' => 'duplicates', 'NAME' => l10n('Duplicates')),
  array('ID' => 'all_photos', 'NAME' => l10n('All'))
);

if ($conf['enable_synchronization'])
{
  $prefilters[] = array('ID' => 'no_virtual_album', 'NAME' => l10n('With no virtual album'));
  $prefilters[] = array('ID' => 'no_sync_md5sum', 'NAME' => l10n('With no checksum'));
}

function UC_name_compare($a, $b)
{
  return strcmp(strtolower($a['NAME']), strtolower($b['NAME']));
}

$prefilters = trigger_change('get_batch_manager_prefilters', $prefilters);

// Sort prefilters by localized name.
usort($prefilters, function ($a, $b) {
  return strcmp(strtolower($a['NAME']), strtolower($b['NAME']));
});

$template->assign(
  array(
    'conf_checksum_compute_blocksize' => $conf['checksum_compute_blocksize'],
    'prefilters' => $prefilters,
    'filter' => $_SESSION['bulk_manager_filter'],
    'selection' => $collection,
    'all_elements' => $page['cat_elements_id'],
    'START' => $page['start'],
    'PWG_TOKEN' => get_pwg_token(),
    'U_DISPLAY'=>$base_url.get_query_string_diff(array('display')),
    'F_ACTION'=>$base_url.get_query_string_diff(array('cat','start','tag','filter')),
    'ADMIN_PAGE_TITLE' => l10n('Batch Manager'),
   )
 );

if (isset($page['no_md5sum_number']))
{
  $template->assign(
    array(
      'NB_NO_MD5SUM' => $page['no_md5sum_number'],
    )
  );
} else {
  $template->assign('NB_NO_MD5SUM', '');
}

// +-----------------------------------------------------------------------+
// |                            caddie options                             |
// +-----------------------------------------------------------------------+
$template->assign('IN_CADDIE', 'caddie' == $page['prefilter']);


// +-----------------------------------------------------------------------+
// |                           global mode form                            |
// +-----------------------------------------------------------------------+

// privacy level
foreach ($conf['available_permission_levels'] as $level)
{
  $level_options[$level] = l10n(sprintf('Level %d', $level));

  if (0 == $level)
  {
    $level_options[$level] = l10n('Everybody');
  }
}
$template->assign(
  array(
    'filter_level_options'=> $level_options,
    'filter_level_options_selected' => isset($_SESSION['bulk_manager_filter']['level'])
    ? $_SESSION['bulk_manager_filter']['level']
    : 0,
    )
  );

// tags
$filter_tags = array();

if (!empty($_SESSION['bulk_manager_filter']['tags']))
{
  $query = '
SELECT
    id,
    name
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $_SESSION['bulk_manager_filter']['tags']).')
;';

  $filter_tags = get_taglist($query);
}

$template->assign('filter_tags', $filter_tags);

// in the filter box, which category to select by default
$selected_category = array();

if (isset($_SESSION['bulk_manager_filter']['category']))
{
  $selected_category = array($_SESSION['bulk_manager_filter']['category']);
}
else
{
  // we need to know the category in which the last photo was added
  $query = '
SELECT category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  ORDER BY image_id DESC
  LIMIT 1
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) > 0)
  {
    $row = pwg_db_fetch_assoc($result);
    $selected_category[] = $row['category_id'];
  }
}

$template->assign('filter_category_selected', $selected_category);

// Dissociate from a category : categories listed for dissociation can only
// represent virtual links. We can't create orphans. Links to physical
// categories can't be broken.
$associated_categories = array();

if (count($page['cat_elements_id']) > 0)
{
  $query = '
SELECT
    DISTINCT(category_id) AS id
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    JOIN '.IMAGES_TABLE.' AS i ON i.id = ic.image_id
  WHERE ic.image_id IN ('.implode(',', $page['cat_elements_id']).')
    AND (
      ic.category_id != i.storage_category_id
      OR i.storage_category_id IS NULL
    )
;';

  $associated_categories = query2array($query, 'id', 'id');
}

$template->assign('associated_categories', $associated_categories);

if (count($page['cat_elements_id']) > 0)
{
  // remove tags
  $template->assign('associated_tags', get_common_tags($page['cat_elements_id'], -1));
}

// creation date
$template->assign('DATE_CREATION',
  empty($_POST['date_creation']) ? date('Y-m-d').' 00:00:00' : $_POST['date_creation']
  );

// image level options
$template->assign(
    array(
      'level_options'=> get_privacy_level_options(),
      'level_options_selected' => 0,
    )
  );

// metadata
include_once( PHPWG_ROOT_PATH.'admin/site_reader_local.php');
$site_reader = new LocalSiteReader('./');
$used_metadata = implode( ', ', $site_reader->get_metadata_attributes());

$template->assign(
    array(
      'used_metadata' => $used_metadata,
    )
  );

//derivatives
$del_deriv_map = array();
foreach(ImageStdParams::get_defined_type_map() as $params)
{
  $del_deriv_map[$params->type] = l10n($params->type);
}
$gen_deriv_map = $del_deriv_map;
$del_deriv_map[IMG_CUSTOM] = l10n(IMG_CUSTOM);
$template->assign(
    array(
      'del_derivatives_types' => $del_deriv_map,
      'generate_derivatives_types' => $gen_deriv_map,
    )
  );

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

// how many items to display on this page
if (!empty($_GET['display']))
{
  if ('all' == $_GET['display'])
  {
    $page['nb_images'] = count($page['cat_elements_id']);
  }
  else
  {
    $page['nb_images'] = intval($_GET['display']);
  }
}
elseif (in_array($conf['batch_manager_images_per_page_global'], array(20, 50, 100)))
{
  $page['nb_images'] = $conf['batch_manager_images_per_page_global'];
}
else
{
  $page['nb_images'] = 20;
}

$nb_thumbs_page = 0;

if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images']
    );
  $template->assign('navbar', $nav_bar);

  $is_category = false;
  if (isset($_SESSION['bulk_manager_filter']['category'])
      and !isset($_SESSION['bulk_manager_filter']['category_recursive']))
  {
    $is_category = true;
  }

  // If using the 'duplicates' filter,
  // order by the fields that are used to find duplicates.
  if (isset($_SESSION['bulk_manager_filter']['prefilter'])
      and 'duplicates' === $_SESSION['bulk_manager_filter']['prefilter']
      and isset($duplicates_on_fields))
  {
    // The $duplicates_on_fields variable is defined in ./batch_manager.php
    $order_by_fields = array_merge( $duplicates_on_fields, array( 'id' ) );
    $conf['order_by'] = ' ORDER BY '.join(', ', $order_by_fields);
  }

  $query = '
SELECT id,path,representative_ext,file,filesize,level,name,width,height,rotation
  FROM '.IMAGES_TABLE;

  if ($is_category)
  {
    $category_info = get_cat_info($_SESSION['bulk_manager_filter']['category']);

    $conf['order_by'] = $conf['order_by_inside_category'];
    if (!empty($category_info['image_order']))
    {
      $conf['order_by'] = ' ORDER BY '.$category_info['image_order'];
    }

    $query.= '
    JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';
  }

  $query.= '
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')';

  if ($is_category)
  {
    $query.= '
    AND category_id = '.$_SESSION['bulk_manager_filter']['category'];
  }

  $query.= '
  '.$conf['order_by'].'
  LIMIT '.$page['nb_images'].' OFFSET '.$page['start'].'
;';
  $result = pwg_query($query);

  $thumb_params = ImageStdParams::get_by_type(IMG_SQUARE);
  // template thumbnail initialization
  while ($row = pwg_db_fetch_assoc($result))
  {
    $nb_thumbs_page++;
    $src_image = new SrcImage($row);

    $ttitle = render_element_name($row);
    if ($ttitle != get_name_from_file($row['file']))
    {
      $ttitle.= ' ('.$row['file'].')';
    }

    $ttitle.= '<br>'.$row['width'].'&times;'.$row['height'].' pixels, '.sprintf('%.2f', $row['filesize']/1024).'MB';

    $template->append(
      'thumbnails', array_merge($row,
      array(
        'thumb' => new DerivativeImage($thumb_params, $src_image),
        'TITLE' => $ttitle,
        'FILE_SRC' => DerivativeImage::url(IMG_LARGE, $src_image),
        'U_EDIT' => get_root_url().'admin.php?page=photo-'.$row['id'],
        )
      ));
  }
  $template->assign('thumb_params', $thumb_params);
}

$template->assign(array(
  'nb_thumbs_page' => $nb_thumbs_page,
  'nb_thumbs_set' => count($page['cat_elements_id']),
  'CACHE_KEYS' => get_admin_client_cache_keys(array('tags', 'categories')),
  ));

trigger_notify('loc_end_element_set_global');

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'batch_manager_global');
?>
