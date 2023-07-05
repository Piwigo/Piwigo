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

include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');


// get_complete_dir returns the concatenation of get_site_url and
// get_local_dir
// Example : "pets > rex > 1_year_old" is on the the same site as the
// Piwigo files and this category has 22 for identifier
// get_complete_dir(22) returns "./galleries/pets/rex/1_year_old/"
function get_complete_dir( $category_id )
{
  return get_site_url($category_id).get_local_dir($category_id);
}

// get_local_dir returns an array with complete path without the site url
// Example : "pets > rex > 1_year_old" is on the the same site as the
// Piwigo files and this category has 22 for identifier
// get_local_dir(22) returns "pets/rex/1_year_old/"
function get_local_dir( $category_id )
{
  global $page;

  $uppercats = '';
  $local_dir = '';

  if ( isset( $page['plain_structure'][$category_id]['uppercats'] ) )
  {
    $uppercats = $page['plain_structure'][$category_id]['uppercats'];
  }
  else
  {
    $query = 'SELECT uppercats';
    $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id = '.$category_id;
    $query.= ';';
    $row = pwg_db_fetch_assoc( pwg_query( $query ) );
    $uppercats = $row['uppercats'];
  }

  $upper_array = explode( ',', $uppercats );

  $database_dirs = array();
  $query = 'SELECT id,dir';
  $query.= ' FROM '.CATEGORIES_TABLE.' WHERE id IN ('.$uppercats.')';
  $query.= ';';
  $result = pwg_query( $query );
  while( $row = pwg_db_fetch_assoc( $result ) )
  {
    $database_dirs[$row['id']] = $row['dir'];
  }
  foreach ($upper_array as $id)
  {
    $local_dir.= $database_dirs[$id].'/';
  }

  return $local_dir;
}

// retrieving the site url : "http://domain.com/gallery/" or
// simply "./galleries/"
function get_site_url($category_id)
{
  global $page;

  $query = '
SELECT galleries_url
  FROM '.SITES_TABLE.' AS s,'.CATEGORIES_TABLE.' AS c
  WHERE s.id = c.site_id
    AND c.id = '.$category_id.'
;';
  $row = pwg_db_fetch_assoc(pwg_query($query));
  return $row['galleries_url'];
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

trigger_notify('loc_begin_cat_modify');

//---------------------------------------------------------------- verification
if ( !isset( $_GET['cat_id'] ) || !is_numeric( $_GET['cat_id'] ) )
{
  trigger_error( 'missing cat_id param', E_USER_ERROR);
}

//--------------------------------------------------------- form criteria check

if (isset($redirect))
{
  redirect($admin_album_base_url.'-properties');
}

// nullable fields
foreach (array('comment','dir','site_id', 'id_uppercat') as $nullable)
{
  if (!isset($category[$nullable]))
  {
    $category[$nullable] = '';
  }
}

$category['is_virtual'] = empty($category['dir']) ? true : false;

$query = 'SELECT DISTINCT category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$_GET['cat_id'].'
  LIMIT 1';
$result = pwg_query($query);
$category['has_images'] = pwg_db_num_rows($result)>0 ? true : false;

// number of sub-categories
$subcat_ids = get_subcat_ids(array($category['id']));

$category['nb_subcats'] = count($subcat_ids) - 1;

// Navigation path
$navigation = get_cat_display_name_cache(
  $category['uppercats'],
  get_root_url().'admin.php?page=album-'
);

// Parent navigation path
$uppercats_array = explode(',', $category['uppercats']);
if (count($uppercats_array) > 1)
{
  array_pop($uppercats_array);
  $parent_navigation = get_cat_display_name_cache(
    implode(',', $uppercats_array),
    get_root_url().'admin.php?page=album-'
  );
}
else
{
  $parent_navigation = l10n('Root');
}

//----------------------------------------------------- template initialization
$template->set_filename( 'album_properties', 'cat_modify.tpl');

$base_url = get_root_url().'admin.php?page=';
$cat_list_url = $base_url.'albums';

$self_url = $cat_list_url;
if (!empty($category['id_uppercat']))
{
  $self_url.= '&amp;parent_id='.$category['id_uppercat'];
}

// We show or hide this warning in JS
$page['warnings'][] = l10n('This album is currently locked, visible only to administrators.').'<span class="icon-key unlock-album">'.l10n('Unlock it').'</span>';

$template->assign(
  array(
    'CATEGORIES_NAV'     => preg_replace("# {2,}#"," ",preg_replace("#(\r\n|\n\r|\n|\r)#"," ",$navigation)),
    'CATEGORIES_PARENT_NAV' => preg_replace("# {2,}#"," ",preg_replace("#(\r\n|\n\r|\n|\r)#"," ",$parent_navigation)),
    'PARENT_CAT_ID'      => !empty($category['id_uppercat']) ? $category['id_uppercat'] : 0,
    'CAT_ID'             => $category['id'],
    'CAT_NAME'           => @htmlspecialchars($category['name']),
    'CAT_COMMENT'        => @htmlspecialchars($category['comment']),
    'IS_VISIBLE'          => boolean_to_string($category['visible']),

    'U_DELETE' => $base_url.'albums',

    'U_JUMPTO' => make_index_url(
      array(
        'category' => $category
        )
      ),

    'U_ADD_PHOTOS_ALBUM' => $base_url.'photos_add&amp;album='.$category['id'],
    'U_CHILDREN' => $cat_list_url.'&amp;parent_id='.$category['id'],
    'U_MOVE' => $base_url.'albums&amp;parent_id='.$category['id'].'#cat-'.$category['id'],
    )
  );
 
if ($conf['activate_comments'])
{
  $template->assign('CAT_COMMENTABLE', boolean_to_string($category['commentable']));
}

// manage album elements link
$image_count = 0;
$info_title = "";
if ($category['has_images'])
{
  $template->assign(
    'U_MANAGE_ELEMENTS',
    $base_url.'batch_manager&amp;filter=album-'.$category['id']
    );

  $query = '
SELECT
    COUNT(image_id),
    MIN(DATE(date_available)),
    MAX(DATE(date_available))
  FROM '.IMAGES_TABLE.'
    JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE category_id = '.$category['id'].'
;';
  list($image_count, $min_date, $max_date) = pwg_db_fetch_row(pwg_query($query));

  if ($min_date == $max_date)
  {
    $info_title = l10n(
      'This album contains %d photos, added on %s.',
      $image_count,
      format_date($min_date)
      );
  }
  else
  {
    $info_title = l10n(
      'This album contains %d photos, added between %s and %s.',
      $image_count,
      format_date($min_date),
      format_date($max_date)
      );
  }
  
}
$info_photos = l10n('%d photos', $image_count);

$template->assign(
  array(
    'INFO_PHOTO' => $info_photos,
    'INFO_TITLE' => $info_title
    )
  );

// total number of images under this category (including sub-categories)
  $query = '
SELECT DISTINCT
    (image_id)
  FROM 
    '.IMAGE_CATEGORY_TABLE.'
  WHERE 
    category_id IN ('.implode(',', $subcat_ids).')
  ;';
  $image_ids_recursive = query2array($query, null, 'image_id');

  $category['nb_images_recursive'] = count($image_ids_recursive);

// date creation
$query = '
SELECT occured_on
  FROM `'.ACTIVITY_TABLE.'`
  WHERE object_id = '.$category['id'].' 
    AND object = "album"
    AND action = "add"
';
$result = query2array($query);

if (count($result) > 0) {
  $template->assign(
    array(
      'INFO_CREATION_SINCE' => time_since($result[0]['occured_on'], 'day', $format=null, $with_text=true, $with_week=true, $only_last_unit=true),
      'INFO_CREATION' => format_date($result[0]['occured_on'], array('day', 'month','year'))
      )
    );
}

// Sub Albums
$query = '
SELECT COUNT(*)
  FROM `'.CATEGORIES_TABLE.'`
  WHERE id_uppercat = '.$category['id'].'
';
$result = query2array($query);


$template->assign(
  array(
    'INFO_DIRECT_SUB' => l10n(
      '%d sub-albums',
      $result[0]['COUNT(*)']
    ), 
    )
  );

$template->assign(array(
  'INFO_ID' => l10n('Numeric identifier : %d',$category['id']),
  'INFO_LAST_MODIFIED_SINCE' => time_since($category['lastmodified'], 'minute', $format=null, $with_text=true, $with_week=true, $only_last_unit=true),
  'INFO_LAST_MODIFIED'=> format_date($category['lastmodified'], array('day', 'month','year')),
  'INFO_IMAGES_RECURSIVE' => l10n(
    '%d including sub-albums',
    $category['nb_images_recursive']
  ),
  'INFO_SUBCATS' => l10n(
    '%d in whole branch',
    $category['nb_subcats']
  ),

  'NB_SUBCATS' => $category['nb_subcats'],
  ),
);

$template->assign(array(
  'U_MANAGE_RANKS' => $base_url.'element_set_ranks&amp;cat_id='.$category['id'],
  'CACHE_KEYS' => get_admin_client_cache_keys(array('categories')),
  ));

if (!$category['is_virtual'])
{
  $category['cat_full_dir'] = get_complete_dir($_GET['cat_id']);
  $template->assign(
    array(
      'CAT_FULL_DIR' => preg_replace('/\/$/', '', $category['cat_full_dir'])
      )
    );

  if ($conf['enable_synchronization'])
  {
    $template->assign(
      'U_SYNC',
      $base_url.'site_update&amp;site='.$category['site_id'].'&amp;cat_id='.$category['id']
      );
  }

}

// representant management
if ($category['has_images'] or !empty($category['representative_picture_id']))
{
  $tpl_representant = array();

  // picture to display : the identified representant or the generic random
  // representant ?
  if (!empty($category['representative_picture_id']))
  {
    $tpl_representant['picture'] = get_category_representant_properties($category['representative_picture_id'], IMG_SMALL);
  }

  // can the admin choose to set a new random representant ?
  $tpl_representant['ALLOW_SET_RANDOM'] = ($category['has_images'] ? true : false);

  // can the admin delete the current representant ?
  if (
    ($category['has_images']
     and $conf['allow_random_representative'])
    or
    (!$category['has_images']
     and !empty($category['representative_picture_id'])))
  {
    $tpl_representant['ALLOW_DELETE'] = true;
  }
  $template->assign('representant', $tpl_representant);
}

if ($category['is_virtual'])
{
  $template->assign('parent_category', empty($category['id_uppercat']) ? array() : array($category['id_uppercat']));
}

$template->assign('PWG_TOKEN', get_pwg_token());

trigger_notify('loc_end_cat_modify');

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'album_properties');
?>
