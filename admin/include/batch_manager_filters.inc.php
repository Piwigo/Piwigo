<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

$prefilters = array(
  array('ID' => 'caddie', 'NAME' => l10n('Caddie')),
  array('ID' => 'favorites', 'NAME' => l10n('Your favorites')),
  array('ID' => 'last_import', 'NAME' => l10n('Last import')),
  array('ID' => 'no_album', 'NAME' => l10n('With no album') . ' (' . l10n('Orphans') . ')'),
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
    'U_DISPLAY' => $base_url . get_query_string_diff(array('display')),
    'F_ACTION' => $base_url . get_query_string_diff(array('cat', 'start', 'tag', 'filter')),
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

// privacy level
foreach ($conf['available_permission_levels'] as $level)
{
  $level_options[$level] = l10n(sprintf('Level %d', $level));

  if (0 == $level) {
    $level_options[$level] = l10n('Everybody');
  }
}
$template->assign(
  array(
    'filter_level_options' => $level_options,
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
  FROM ' . TAGS_TABLE . '
  WHERE id IN (' . implode(',', $_SESSION['bulk_manager_filter']['tags']) . ')
;';

  $filter_tags = get_taglist($query);
}

$template->assign('filter_tags', $filter_tags);

// in the filter box, which category to select by default
$selected_category = null;
$selected_category_name = '';

if (isset($_SESSION['bulk_manager_filter']['category']))
{
  $selected_category = intval($_SESSION['bulk_manager_filter']['category']);
  $selected_category_name = get_cat_display_name_from_id($selected_category);
} 

$template->assign('filter_category_selected_name', strip_tags($selected_category_name));
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
  FROM ' . IMAGE_CATEGORY_TABLE . ' AS ic
    JOIN ' . IMAGES_TABLE . ' AS i ON i.id = ic.image_id
  WHERE ic.image_id IN (' . implode(',', $page['cat_elements_id']) . ')
    AND (
      ic.category_id != i.storage_category_id
      OR i.storage_category_id IS NULL
    )
;';

  $associated_categories = query2array($query, 'id', 'id');
}

$template->assign('associated_categories', $associated_categories);

load_language('help_quick_search.lang');