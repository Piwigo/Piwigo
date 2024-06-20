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

trigger_notify('loc_begin_element_set_unit');

// +-----------------------------------------------------------------------+
// |                        unit mode form submission                      |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  check_pwg_token();
  check_input_parameter('element_ids', $_POST, false, '/^\d+(,\d+)*$/');
  $collection = explode(',', $_POST['element_ids']);

  $datas = array();

  $query = '
SELECT id, date_creation
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
;';
  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $data = array();

    $data['id'] = $row['id'];
    $data['name'] = $_POST['name-'.$row['id']];
    $data['author'] = $_POST['author-'.$row['id']];
    $data['level'] = $_POST['level-'.$row['id']];

    if ($conf['allow_html_descriptions'])
    {
      $data['comment'] = @$_POST['description-'.$row['id']];
    }
    else
    {
      $data['comment'] = strip_tags(@$_POST['description-'.$row['id']]);
    }

    if (!empty($_POST['date_creation-'.$row['id']]))
    {
      $data['date_creation'] = $_POST['date_creation-'.$row['id']];
    }
    else
    {
      $data['date_creation'] = null;
    }

    $datas[] = $data;

    // tags management
    $tag_ids = array();
    if (!empty($_POST[ 'tags-'.$row['id'] ]))
    {
      $tag_ids = get_tag_ids($_POST[ 'tags-'.$row['id'] ]);
    }
    set_tags($tag_ids, $row['id']);
  }

  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('name','author','level','comment','date_creation')
      ),
    $datas
    );

  $page['infos'][] = l10n('Photo informations updated');
  invalidate_user_cache();
}

//collection
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
  // Here we don't use check_input_parameter because preg_match has a limit in
  // the repetitive pattern. Found a limit to 3276 but may depend on memory.
  //
  // check_input_parameter('whole_set', $_POST, false, '/^\d+(,\d+)*$/');
  //
  // Instead, let's break the input parameter into pieces and check pieces one by one.
  $collection = explode(',', $_POST['whole_set']);

  foreach ($collection as $id)
  {
    if (!preg_match('/^\d+$/', $id))
    {
      fatal_error('[Hacking attempt] the input parameter "whole_set" is not valid');
    }
  }
}
else if (isset($_POST['selection']))
{
  $collection = $_POST['selection'];
}


// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array('batch_manager_unit' => 'batch_manager_unit.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php';

$template->assign(
  array(
    'U_ELEMENTS_PAGE' => $base_url.get_query_string_diff(array('display','start')),
    'level_options' => get_privacy_level_options(),
    'ADMIN_PAGE_TITLE' => l10n('Batch Manager'),
    'PWG_TOKEN' => get_pwg_token(),
    )
  );
      //prefilter
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
          'U_DISPLAY'=>$base_url.get_query_string_diff(array('display')),
          'F_ACTION'=>$base_url.get_query_string_diff(array('cat','start','tag','filter')),
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

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

// how many items to display on this page
if (!empty($_GET['display']))
{
  $page['nb_images'] = intval($_GET['display']);
}
elseif (in_array($conf['batch_manager_images_per_page_unit'], array(5, 10, 50)))
{
  $page['nb_images'] = $conf['batch_manager_images_per_page_unit'];
}
else
{
  $page['nb_images'] = 5;
}



if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images']
    );
  $template->assign(array('navbar' => $nav_bar));

  $element_ids = array();

  $is_category = false;
  if (isset($_SESSION['bulk_manager_filter']['category'])
      and !isset($_SESSION['bulk_manager_filter']['category_recursive']))
  {
    $is_category = true;
  }

  if (isset($_SESSION['bulk_manager_filter']['prefilter'])
      and 'duplicates' == $_SESSION['bulk_manager_filter']['prefilter'])
  {
    $conf['order_by'] = ' ORDER BY file, id';
  }


  $query = '
SELECT *
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
  
  $storage_category_id = null;
  if (!empty($row['storage_category_id']))
  {
    $storage_category_id = $row['storage_category_id'];
  }

  $level_convert = [
    "0" => "4",
    "1" => "3",
    "2" => "2",
    "4" => "1",
    "8" => "0",
];

  while ($row = pwg_db_fetch_assoc($result))
  {
    $element_ids[] = $row['id'];

    $src_image = new SrcImage($row);

    $image_file = $row['file'];



    $query = '
SELECT
    id,
    name
  FROM '.IMAGE_TAG_TABLE.' AS it
    JOIN '.TAGS_TABLE.' AS t ON t.id = it.tag_id
  WHERE image_id = '.$row['id'].'
;';

    $tag_selection = get_taglist($query);

    $legend = render_element_name($row);
    if ($legend != get_name_from_file($row['file']))
    {
      $legend.= ' ('.$row['file'].')';
    }
    $extTab = explode('.',$row['path']);

  

// represent

    // categories

    $query = '
    SELECT category_id, uppercats, dir
      FROM '.IMAGE_CATEGORY_TABLE.' AS ic
        INNER JOIN '.CATEGORIES_TABLE.' AS c
          ON c.id = ic.category_id
      WHERE image_id = '.$row['id'].'
    ;';

    $sub_result = pwg_query($query);
    $related_categories = array();
    $related_category_ids = array();
    $media['image'] = get_image_infos($row['id'], true);
    
    while ($item = pwg_db_fetch_assoc($sub_result))
    {
      $name =
        get_cat_display_name_cache(
          $item['uppercats'],
          get_root_url().'admin.php?page=album-'
          );
    
      if ($item['category_id'] == $storage_category_id)
      {
        $template->assign('STORAGE_CATEGORY', $name);
      }
    
      $related_categories[$item['category_id']] = array('name' => $name, 'unlinkable' => $item['category_id'] != $storage_category_id);
      $related_category_ids[] = $item['category_id'];
    }

    // jump to link
    $image_file = $row['file'];

    $query = '
    SELECT category_id
    FROM '.IMAGE_CATEGORY_TABLE.'
    WHERE image_id = '.$row['id'].'
    ;';
    $authorizeds = array_diff(
      array_from_query($query, 'category_id'),
      explode(
        ',',
        calculate_permissions($user['id'], $user['status'])
      )
    );

    if (isset($row['cat_id'])
    and in_array($row['cat_id'], $authorizeds))
    {
      $url_img = make_picture_url(
        array(
          'image_id' => $row['id'],
          'image_file' => $image_file,
          'category' => $cache['cat_names'][ $row['cat_id'] ],
          )
        );
    }
    else
    {
      foreach ($authorizeds as $category)
      {
        $url_img = make_picture_url(
          array(
            'image_id' => $row['id'], //utile ?
            'image_file' => $image_file,
            'category' => $cache['cat_names'][ $category ],
            )
          );
        break;
      }
    }
    $admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$row['id'];
    $admin_url_start = $admin_photo_base_url.'-properties';
    $admin_url_start.= isset($row['cat_id']) ? '&amp;cat_id='.$row['cat_id'] : '';
    $selected_level = isset($row['level']) ? $row['level'] : $row['level'];

    $template->append(
      'elements', array_merge($row,
      array(
        'ID' => $row['id'],
        'TN_SRC' => DerivativeImage::url(IMG_MEDIUM, $src_image),
        'FILE_SRC' => DerivativeImage::url(IMG_LARGE, $src_image),
        'LEGEND' => $legend,
        'U_EDIT' => get_root_url().'admin.php?page=photo-'.$row['id'],
        'NAME' => htmlspecialchars(isset($row['name']) ? $row['name'] : ""),
        'AUTHOR' => htmlspecialchars(isset($row['author']) ? $row['author'] : ""),
        'LEVEL' => !empty($row['level'])?$row['level']:'0',
        'DESCRIPTION' => htmlspecialchars(isset($row['comment']) ? $row['comment'] : ""),
        'DATE_CREATION' => $row['date_creation'],
        'TAGS' => $tag_selection,
        'is_svg' => (strtoupper(end($extTab)) == 'SVG'),
        'TITLE' => render_element_name($row),
        'DIMENSIONS' => @$row['width'].'x'.@$row['height'].' px',
        'FORMAT' => ($row['width'] >= $row['height'])? 1:0,//0:horizontal, 1:vertical
        'FILESIZE' => l10n('%.2f MB',$row['filesize']/1024),
        'REGISTRATION_DATE' => format_date($row['date_available']),
        'EXT' => l10n('%s file type',end($extTab)),
        'POST_DATE' => l10n('Posted the %s', format_date($row['date_available'], array('day', 'month', 'year'))),
        'AGE' => l10n(ucfirst(time_since($row['date_available'], 'year'))),
        'ADDED_BY' => l10n('Added by %s', $row['added_by']),
        'STATS' => l10n('Visited %d times', $row['hit']),
        'FILE' => l10n('%s', $row['file']),
        'related_categories' => $related_categories,
        'related_category_ids' => json_encode($related_category_ids,JSON_NUMERIC_CHECK),
        'U_JUMPTO' => (isset($url_img) and $user['level'] >= $media['image']['level']) ? $url_img : null,
        'tag_selection' => $tag_selection,
        'U_DOWNLOAD' => 'action.php?id='.$row['id'].'&amp;part=e&amp;pwg_token='.get_pwg_token().'&amp;download',
        'U_HISTORY' => get_root_url().'admin.php?page=history&amp;filter_image_id='.$row['id'],
        'U_DELETE' => $admin_url_start.'&amp;delete=1&amp;pwg_token='.get_pwg_token(),
        'U_SYNC' => $admin_url_start.'&amp;sync_metadata=1',
        'PATH'=>$row['path'],
        'LEVEL_CONVERT' => $level_convert[!empty($row['level'])?$row['level']:'0'],
        'level_options_selected' => array($selected_level)


        )
      ));
  }


  $template->assign(array(
    'ELEMENT_IDS' => implode(',', $element_ids),
    'CACHE_KEYS' => get_admin_client_cache_keys(array('tags')),
    
    ));
    
}

trigger_notify('loc_end_element_set_unit');

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'batch_manager_unit');
?>
