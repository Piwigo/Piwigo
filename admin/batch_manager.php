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
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('selection', $_POST, true, PATTERN_ID);
check_input_parameter('display', $_REQUEST, false, '/^(\d+|all)$/');

// +-----------------------------------------------------------------------+
// | specific actions                                                      |
// +-----------------------------------------------------------------------+

if (isset($_GET['action']))
{
  if ('empty_caddie' == $_GET['action'])
  {
    $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
    pwg_query($query);

    $_SESSION['page_infos'] = array(
      l10n('Information data registered in database')
      );
    
    redirect(get_root_url().'admin.php?page='.$_GET['page']);
  }

  if ('delete_orphans' == $_GET['action'] and isset($_GET['nb_orphans_deleted']))
  {
    check_input_parameter('nb_orphans_deleted', $_GET, false, '/^\d+$/');

    if ($_GET['nb_orphans_deleted'] > 0)
    {
      $_SESSION['page_infos'][] = l10n_dec(
        '%d photo was deleted', '%d photos were deleted',
        $_GET['nb_orphans_deleted']
        );

      redirect(get_root_url().'admin.php?page='.$_GET['page']);
    }
  }

  if ('sync_md5sum' == $_GET['action'] and isset($_GET['nb_md5sum_added']))
  {
    check_input_parameter('nb_md5sum_added', $_GET, false, '/^\d+$/');
    if ($_GET['nb_md5sum_added'] > 0)
    {
      $_SESSION['page_infos'][] = l10n_dec(
        '%d checksums were added', '%d checksums were added',
        $_GET['nb_md5sum_added']
      );

      redirect(get_root_url().'admin.php?page='.$_GET['page']);
    }
  }
}
// +-----------------------------------------------------------------------+
// |                      initialize current set                           |
// +-----------------------------------------------------------------------+

// filters from form
if (isset($_POST['submitFilter']))
{
  // echo '<pre>'; print_r($_POST); echo '</pre>';
  unset($_REQUEST['start']); // new photo set must reset the page
  $_SESSION['bulk_manager_filter'] = array();

  if (isset($_POST['filter_prefilter_use']))
  {
    $_SESSION['bulk_manager_filter']['prefilter'] = $_POST['filter_prefilter'];

    if ('duplicates' == $_POST['filter_prefilter'])
    {
      $has_options = false;

      if (isset($_POST['filter_duplicates_checksum']))
      {
        $_SESSION['bulk_manager_filter']['duplicates_checksum'] = true;
        $has_options = true;
      }

      if (isset($_POST['filter_duplicates_date']))
      {
        $_SESSION['bulk_manager_filter']['duplicates_date'] = true;
        $has_options = true;
      }
      
      if (isset($_POST['filter_duplicates_dimensions']))
      {
        $_SESSION['bulk_manager_filter']['duplicates_dimensions'] = true;
        $has_options = true;
      }

      if (!$has_options or isset($_POST['filter_duplicates_filename']))
      {
        $_SESSION['bulk_manager_filter']['duplicates_filename'] = true;
      }
    }
  }

  if (isset($_POST['filter_category_use']))
  {
    check_input_parameter('filter_category', $_POST, false, PATTERN_ID);

    $_SESSION['bulk_manager_filter']['category'] = $_POST['filter_category'];

    if (isset($_POST['filter_category_recursive']))
    {
      $_SESSION['bulk_manager_filter']['category_recursive'] = true;
    }
  }

  if (isset($_POST['filter_tags_use']))
  {
    $_SESSION['bulk_manager_filter']['tags'] = get_tag_ids($_POST['filter_tags'], false);

    if (isset($_POST['tag_mode']) and in_array($_POST['tag_mode'], array('AND', 'OR')))
    {
      $_SESSION['bulk_manager_filter']['tag_mode'] = $_POST['tag_mode'];
    }
  }

  if (isset($_POST['filter_level_use']))
  {
    check_input_parameter('filter_level', $_POST, false, '/^\d+$/');
    
    if (in_array($_POST['filter_level'], $conf['available_permission_levels']))
    {
      $_SESSION['bulk_manager_filter']['level'] = $_POST['filter_level'];

      if (isset($_POST['filter_level_include_lower']))
      {
        $_SESSION['bulk_manager_filter']['level_include_lower'] = true;
      }
    }
  }

  if (isset($_POST['filter_dimension_use']))
  {
    foreach (array('min_width','max_width','min_height','max_height') as $type)
    {
      if (filter_var($_POST['filter_dimension_'.$type], FILTER_VALIDATE_INT) !== false)
      {
        $_SESSION['bulk_manager_filter']['dimension'][$type] = $_POST['filter_dimension_'. $type ];
      }
    }
    foreach (array('min_ratio','max_ratio') as $type)
    {
      if (filter_var($_POST['filter_dimension_'.$type], FILTER_VALIDATE_FLOAT) !== false)
      {
        $_SESSION['bulk_manager_filter']['dimension'][$type] = $_POST['filter_dimension_'. $type ];
      }
    }
  }

  if (isset($_POST['filter_filesize_use']))
  {
    foreach (array('min','max') as $type)
    {
      if (filter_var($_POST['filter_filesize_'.$type], FILTER_VALIDATE_FLOAT) !== false)
      {
        $_SESSION['bulk_manager_filter']['filesize'][$type] = $_POST['filter_filesize_'. $type ];
      }
    }
  }

  if (isset($_POST['filter_search_use']))
  {
    $_SESSION['bulk_manager_filter']['search']['q'] = $_POST['q'];
  }

  $_SESSION['bulk_manager_filter'] = trigger_change('batch_manager_register_filters', $_SESSION['bulk_manager_filter']);
}
// filters from url
elseif (isset($_GET['filter']))
{
  if (!is_array($_GET['filter']))
  {
    $_GET['filter'] = explode(',', $_GET['filter']);
  }

  $_SESSION['bulk_manager_filter'] = array();

  foreach ($_GET['filter'] as $filter)
  {
    list($type, $value) = explode('-', $filter, 2);

    switch ($type)
    {
    case 'prefilter':
      if (preg_match('/^duplicates-?/', $value))
      {
        list(, $duplicate_field) = explode('-', $value, 2);
        $_SESSION['bulk_manager_filter']['prefilter'] = 'duplicates';

        if (in_array($duplicate_field, array('filename', 'checksum', 'date', 'dimensions')))
        {
          $_SESSION['bulk_manager_filter']['duplicates_'.$duplicate_field] = true;
        }
      }
      else
      {
        $_SESSION['bulk_manager_filter']['prefilter'] = $value;
      }
      break;

    case 'album': case 'category': case 'cat':
      if (is_numeric($value))
      {
        $_SESSION['bulk_manager_filter']['category'] = $value;
      }
      break;

    case 'tag':
      if (is_numeric($value))
      {
        $_SESSION['bulk_manager_filter']['tags'] = array($value);
        $_SESSION['bulk_manager_filter']['tag_mode'] = 'AND';
      }
      break;

    case 'level':
      if (is_numeric($value) && in_array($value, $conf['available_permission_levels']))
      {
        $_SESSION['bulk_manager_filter']['level'] = $value;
      }
      break;

    case 'search':
      $_SESSION['bulk_manager_filter']['search']['q'] = $value;
      break;

    case 'dimension':
      $dim_map = array('w'=>'width','h'=>'height','r'=>'ratio');
      foreach (explode('-', $value) as $part)
      {
        $values = explode('..', substr($part, 1));
        if (isset($dim_map[$part[0]]))
        {
          $type = $dim_map[$part[0]];
          list(
            $_SESSION['bulk_manager_filter']['dimension']['min_'.$type],
            $_SESSION['bulk_manager_filter']['dimension']['max_'.$type]
          ) = $values;
        }
      }
      break;

    case 'filesize':
      list(
        $_SESSION['bulk_manager_filter']['filesize']['min'],
        $_SESSION['bulk_manager_filter']['filesize']['max']
      ) = explode('..', $value);
      break;

    default:
      $_SESSION['bulk_manager_filter'] = trigger_change('batch_manager_url_filter', $_SESSION['bulk_manager_filter'], $filter);
      break;
    }
  }
}

if (empty($_SESSION['bulk_manager_filter']))
{
  $_SESSION['bulk_manager_filter'] = array(
    'prefilter' => 'caddie'
    );
}

// echo '<pre>'; print_r($_SESSION['bulk_manager_filter']); echo '</pre>';

// depending on the current filter (in session), we find the appropriate photos
$filter_sets = array();
if (isset($_SESSION['bulk_manager_filter']['prefilter']))
{
  switch ($_SESSION['bulk_manager_filter']['prefilter'])
  {
  case 'caddie':
    $query = '
SELECT element_id
  FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
    $filter_sets[] = query2array($query, null, 'element_id');

    break;

  case 'favorites':
    $query = '
SELECT image_id
  FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
    $filter_sets[] = query2array($query, null, 'image_id');

    break;

  case 'last_import':
    $query = '
SELECT MAX(date_available) AS date
  FROM '.IMAGES_TABLE.'
;';
    $row = pwg_db_fetch_assoc(pwg_query($query));
    if (!empty($row['date']))
    {
      $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE date_available BETWEEN '.pwg_db_get_recent_period_expression(1, $row['date']).' AND \''.$row['date'].'\'
;';
      $filter_sets[] = query2array($query, null, 'id');
    }

    break;

  case 'no_virtual_album':
    // we are searching elements not linked to any virtual category
    $query = '
 SELECT id
   FROM '.IMAGES_TABLE.'
 ;';
    $all_elements = query2array($query, null, 'id');

    $linked_to_virtual = array();

    $query = '
 SELECT id
   FROM '.CATEGORIES_TABLE.'
   WHERE dir IS NULL
 ;';
    $virtual_categories = query2array($query, null, 'id');
    if (!empty($virtual_categories))
    {
      $query = '
 SELECT DISTINCT(image_id)
   FROM '.IMAGE_CATEGORY_TABLE.'
   WHERE category_id IN ('.implode(',', $virtual_categories).')
 ;';
      $linked_to_virtual = query2array($query, null, 'image_id');
    }

    $filter_sets[] = array_diff($all_elements, $linked_to_virtual);

    break;

  case 'no_album':
    $filter_sets[] = get_orphans();
    break;
  case 'no_sync_md5sum':
    $filter_sets[] = get_photos_no_md5sum();
    break;

  case 'no_tag':
    $query = '
SELECT
    id
  FROM '.IMAGES_TABLE.'
    LEFT JOIN '.IMAGE_TAG_TABLE.' ON id = image_id
  WHERE tag_id is null
;';
    $filter_sets[] = query2array($query, null, 'id');

    break;


  case 'duplicates':
    $duplicates_on_fields = array();

    if (isset($_SESSION['bulk_manager_filter']['duplicates_filename']))
    {
      $duplicates_on_fields[] = 'file';
    }

    if (isset($_SESSION['bulk_manager_filter']['duplicates_checksum']))
    {
      $duplicates_on_fields[] = 'md5sum';
    }

    if (isset($_SESSION['bulk_manager_filter']['duplicates_date']))
    {
      $duplicates_on_fields[] = 'date_creation';
    }
    
    if (isset($_SESSION['bulk_manager_filter']['duplicates_dimensions']))
    {
      $duplicates_on_fields[] = 'width';
      $duplicates_on_fields[] = 'height';
    }

    // TODO improve this algorithm, because GROUP_CONCAT is truncated at
    // 1024 chars. So if you have more than ~250 duplicates for a given
    // combination of "duplicates_on_fields" you won't get all the
    // duplicates.

    $query = '
SELECT
    GROUP_CONCAT(id) AS ids
  FROM '.IMAGES_TABLE;

    if (in_array('md5sum', $duplicates_on_fields))
    {
      $query.= '
  WHERE md5sum IS NOT NULL
';
    }

    $query.= '
  GROUP BY '.implode(',', $duplicates_on_fields).'
  HAVING COUNT(*) > 1
;';
    $array_of_ids_string = query2array($query, null, 'ids');

    $ids = array();
    
    foreach ($array_of_ids_string as $ids_string)
    {
      $ids_string = rtrim($ids_string,',');
      $ids = array_merge($ids, explode(',', $ids_string));
    }
    
    $filter_sets[] = $ids;

    break;

  case 'all_photos':
    if ( count($_SESSION['bulk_manager_filter']) == 1 )
    {// make the query only if this is the only filter
      $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  '.$conf['order_by'];

      $filter_sets[] = query2array($query, null, 'id');
    }
    break;

  default:
    $filter_sets = trigger_change('perform_batch_manager_prefilters', $filter_sets, $_SESSION['bulk_manager_filter']['prefilter']);
    break;
  }
}

if (isset($_SESSION['bulk_manager_filter']['category']))
{
  $categories = array();

  // we need to check the category still exists (it may have been deleted since it was added in the session)
  $query = '
SELECT COUNT(*)
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_SESSION['bulk_manager_filter']['category'].'
;';
  list($counter) = pwg_db_fetch_row(pwg_query($query));
  if (0 == $counter)
  {
    unset($_SESSION['bulk_manager_filter']);
    redirect(get_root_url().'admin.php?page='.$_GET['page']);
  }

  if (isset($_SESSION['bulk_manager_filter']['category_recursive']))
  {
    $categories = get_subcat_ids(array($_SESSION['bulk_manager_filter']['category']));
  }
  else
  {
    $categories = array($_SESSION['bulk_manager_filter']['category']);
  }

  $query = '
 SELECT DISTINCT(image_id)
   FROM '.IMAGE_CATEGORY_TABLE.'
   WHERE category_id IN ('.implode(',', $categories).')
 ;';
  $filter_sets[] = query2array($query, null, 'image_id');
}

if (isset($_SESSION['bulk_manager_filter']['level']))
{
  $operator = '=';
  if (isset($_SESSION['bulk_manager_filter']['level_include_lower']))
  {
    $operator = '<=';
  }

  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE level '.$operator.' '.$_SESSION['bulk_manager_filter']['level'].'
  '.$conf['order_by'];

  $filter_sets[] = query2array($query, null, 'id');
}

if (!empty($_SESSION['bulk_manager_filter']['tags']))
{
  $filter_sets[] = get_image_ids_for_tags(
    $_SESSION['bulk_manager_filter']['tags'],
    $_SESSION['bulk_manager_filter']['tag_mode'],
    null,
    null,
    false // we don't apply permissions in administration screens
    );
}

if (isset($_SESSION['bulk_manager_filter']['dimension']))
{
  $where_clauses = array();
  if (isset($_SESSION['bulk_manager_filter']['dimension']['min_width']))
  {
    $where_clause[] = 'width >= '.$_SESSION['bulk_manager_filter']['dimension']['min_width'];
  }
  if (isset($_SESSION['bulk_manager_filter']['dimension']['max_width']))
  {
    $where_clause[] = 'width <= '.$_SESSION['bulk_manager_filter']['dimension']['max_width'];
  }
  if (isset($_SESSION['bulk_manager_filter']['dimension']['min_height']))
  {
    $where_clause[] = 'height >= '.$_SESSION['bulk_manager_filter']['dimension']['min_height'];
  }
  if (isset($_SESSION['bulk_manager_filter']['dimension']['max_height']))
  {
    $where_clause[] = 'height <= '.$_SESSION['bulk_manager_filter']['dimension']['max_height'];
  }
  if (isset($_SESSION['bulk_manager_filter']['dimension']['min_ratio']))
  {
    $where_clause[] = 'width/height >= '.$_SESSION['bulk_manager_filter']['dimension']['min_ratio'];
  }
  if (isset($_SESSION['bulk_manager_filter']['dimension']['max_ratio']))
  {
    // max_ratio is a floor value, so must be a bit increased
    $where_clause[] = 'width/height < '.($_SESSION['bulk_manager_filter']['dimension']['max_ratio']+0.01);
  }

  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE '.implode(' AND ',$where_clause).'
  '.$conf['order_by'];

  $filter_sets[] = query2array($query, null, 'id');
}

if (isset($_SESSION['bulk_manager_filter']['filesize']))
{
  $where_clauses = array();
  
  if (isset($_SESSION['bulk_manager_filter']['filesize']['min']))
  {
    $where_clause[] = 'filesize >= '.$_SESSION['bulk_manager_filter']['filesize']['min']*1024;
  }
  
  if (isset($_SESSION['bulk_manager_filter']['filesize']['max']))
  {
    $where_clause[] = 'filesize <= '.$_SESSION['bulk_manager_filter']['filesize']['max']*1024;
  }

  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE '.implode(' AND ',$where_clause).'
  '.$conf['order_by'];

  $filter_sets[] = query2array($query, null, 'id');
}

if (isset($_SESSION['bulk_manager_filter']['search']) && 
    strlen($_SESSION['bulk_manager_filter']['search']['q']))
{
  include_once( PHPWG_ROOT_PATH .'include/functions_search.inc.php' );
  $res = get_quick_search_results_no_cache($_SESSION['bulk_manager_filter']['search']['q'], array('permissions'=>false));
  if (!empty($res['items']) && !empty($res['qs']['unmatched_terms']))
  {
    $template->assign('no_search_results', array_map('htmlspecialchars', $res['qs']['unmatched_terms']) );
  }
  $filter_sets[] = $res['items'];
}

$filter_sets = trigger_change('batch_manager_perform_filters', $filter_sets, $_SESSION['bulk_manager_filter']);

$current_set = array_shift($filter_sets);
foreach ($filter_sets as $set)
{
  $current_set = array_intersect($current_set, $set);
}
$page['cat_elements_id'] = empty($current_set) ? array() : $current_set;


// +-----------------------------------------------------------------------+
// |                       first element to display                        |
// +-----------------------------------------------------------------------+

// $page['start'] contains the number of the first element in its
// category. For exampe, $page['start'] = 12 means we must show elements #12
// and $page['nb_images'] next elements

if (!isset($_REQUEST['start'])
    or !is_numeric($_REQUEST['start'])
    or $_REQUEST['start'] < 0
    or (isset($_REQUEST['display']) and 'all' == $_REQUEST['display']))
{
  $page['start'] = 0;
}
else
{
  $page['start'] = $_REQUEST['start'];
}


// +-----------------------------------------------------------------------+
// |                                 Tabs                                  |
// +-----------------------------------------------------------------------+
$manager_link = get_root_url().'admin.php?page=batch_manager&amp;mode=';

if (isset($_GET['mode']))
{
  check_input_parameter('mode', $_GET, false, '/^(global|unit)$/');
  $page['tab'] = $_GET['mode'];
}
else
{
  $page['tab'] = 'global';
}

$tabsheet = new tabsheet();
$tabsheet->set_id('batch_manager');
$tabsheet->select($page['tab']);
$tabsheet->assign();


// +-----------------------------------------------------------------------+
// |                              dimensions                               |
// +-----------------------------------------------------------------------+

$widths = array();
$heights = array();
$ratios = array();
$dimensions = array();

// get all width, height and ratios
$query = '
SELECT
  DISTINCT width, height
  FROM '.IMAGES_TABLE.'
  WHERE width IS NOT NULL
    AND height IS NOT NULL
;';
$result = pwg_query($query);

if (pwg_db_num_rows($result))
{
  while ($row = pwg_db_fetch_assoc($result))
  {
    if ($row['width']>0 && $row['height']>0)
    {
      $widths[] = $row['width'];
      $heights[] = $row['height'];
      $ratios[] = floor($row['width'] / $row['height'] * 100) / 100;
    }
  }
}
if (empty($widths))
{ // arbitrary values, only used when no photos on the gallery
  $widths = array(600, 1920, 3500);
  $heights = array(480, 1080, 2300);
  $ratios = array(1.25, 1.52, 1.78);
}

foreach (array('widths','heights','ratios') as $type)
{
  ${$type} = array_unique(${$type});
  sort(${$type});
  $dimensions[$type] = implode(',', ${$type});
}

$dimensions['bounds'] = array(
  'min_width' => $widths[0],
  'max_width' => end($widths),
  'min_height' => $heights[0],
  'max_height' => end($heights),
  'min_ratio' => $ratios[0],
  'max_ratio' => end($ratios),
  );

// find ratio categories
$ratio_categories = array(
  'portrait' => array(),
  'square' => array(),
  'landscape' => array(),
  'panorama' => array(),
  );

foreach ($ratios as $ratio)
{
  if ($ratio < 0.95)
  {
    $ratio_categories['portrait'][] = $ratio;
  }
  else if ($ratio >= 0.95 and $ratio <= 1.05)
  {
    $ratio_categories['square'][] = $ratio;
  }
  else if ($ratio > 1.05 and $ratio < 2)
  {
    $ratio_categories['landscape'][] = $ratio;
  }
  else if ($ratio >= 2)
  {
    $ratio_categories['panorama'][] = $ratio;
  }
}

foreach (array_keys($ratio_categories) as $type)
{
  if (count($ratio_categories[$type]) > 0)
  {
    $dimensions['ratio_'.$type] = array(
      'min' => $ratio_categories[$type][0],
      'max' => end($ratio_categories[$type]),
      );
  }
}

// selected=bound if nothing selected
foreach (array_keys($dimensions['bounds']) as $type)
{
  $dimensions['selected'][$type] = isset($_SESSION['bulk_manager_filter']['dimension'][$type])
    ? $_SESSION['bulk_manager_filter']['dimension'][$type]
    : $dimensions['bounds'][$type]
  ;
}

$template->assign('dimensions', $dimensions);

// +-----------------------------------------------------------------------+
// | filesize                                                              |
// +-----------------------------------------------------------------------+

$filesizes = array();
$filesize = array();

$query = '
SELECT
  filesize
  FROM '.IMAGES_TABLE.'
  WHERE filesize IS NOT NULL
  GROUP BY filesize
;';
$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  $filesizes[] = sprintf('%.1f', $row['filesize']/1024);
}

if (empty($filesizes))
{ // arbitrary values, only used when no photos on the gallery
  $filesizes = array(0, 1, 2, 5, 8, 15);
}

$filesizes = array_unique($filesizes);
sort($filesizes);

// add 0.1MB to the last value, to make sure the heavier photo will be in
// the result
$filesizes[count($filesizes)-1]+= 0.1;

$filesize['list'] = implode(',', $filesizes);

$filesize['bounds'] = array(
  'min' => $filesizes[0],
  'max' => end($filesizes),
  );

// selected=bound if nothing selected
foreach (array_keys($filesize['bounds']) as $type)
{
  $filesize['selected'][$type] = isset($_SESSION['bulk_manager_filter']['filesize'][$type])
    ? $_SESSION['bulk_manager_filter']['filesize'][$type]
    : $filesize['bounds'][$type]
  ;
}

$template->assign('filesize', $filesize);

// +-----------------------------------------------------------------------+
// |                         open specific mode                            |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'admin/batch_manager_'.$page['tab'].'.php');
?>