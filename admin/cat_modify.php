<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

trigger_action('loc_begin_cat_modify');

//---------------------------------------------------------------- verification
if ( !isset( $_GET['cat_id'] ) || !is_numeric( $_GET['cat_id'] ) )
{
  trigger_error( 'missing cat_id param', E_USER_ERROR);
}

//--------------------------------------------------------- form criteria check
if (isset($_POST['submit']))
{
  $data =
    array(
      'id' => $_GET['cat_id'],
      'name' => @$_POST['name'],
      'comment' =>
        $conf['allow_html_descriptions'] ?
          @$_POST['comment'] : strip_tags(@$_POST['comment']),
      );
     
  if ($conf['activate_comments'])
  {
    $data['commentable'] = isset($_POST['commentable'])?$_POST['commentable']:'false';
  }

  mass_updates(
    CATEGORIES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array_diff(array_keys($data), array('id'))
      ),
    array($data)
    );

  // retrieve cat infos before continuing (following updates are expensive)
  $cat_info = get_cat_info($_GET['cat_id']);

  if ($cat_info['visible'] != get_boolean( $_POST['visible'] ) )
  {
    set_cat_visible(array($_GET['cat_id']), $_POST['visible']);
  }

  // in case the use moves his album to the gallery root, we force
  // $_POST['parent'] from 0 to null to be compared with
  // $cat_info['id_uppercat']
  if (empty($_POST['parent']))
  {
    $_POST['parent'] = null;
  }

  // only move virtual albums
  if (empty($cat_info['dir']) and $cat_info['id_uppercat'] != $_POST['parent'])
  {
    move_categories( array($_GET['cat_id']), $_POST['parent'] );
  }

  $_SESSION['page_infos'][] = l10n('Album updated successfully');
  $redirect = true;
}
elseif (isset($_POST['set_random_representant']))
{
  set_random_representant(array($_GET['cat_id']));
  $redirect = true;
}
elseif (isset($_POST['delete_representant']))
{
  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id = '.$_GET['cat_id'].'
;';
  pwg_query($query);
  $redirect = true;
}

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

// Navigation path
$navigation = get_cat_display_name_cache(
  $category['uppercats'],
  get_root_url().'admin.php?page=album-'
  );

$form_action = $admin_album_base_url.'-properties';

//----------------------------------------------------- template initialization
$template->set_filename( 'album_properties', 'cat_modify.tpl');

$base_url = get_root_url().'admin.php?page=';
$cat_list_url = $base_url.'cat_list';

$self_url = $cat_list_url;
if (!empty($category['id_uppercat']))
{
  $self_url.= '&amp;parent_id='.$category['id_uppercat'];
}

$template->assign(
  array(
    'CATEGORIES_NAV'     => $navigation,
    'CAT_ID'             => $category['id'],
    'CAT_NAME'           => @htmlspecialchars($category['name']),
    'CAT_COMMENT'        => @htmlspecialchars($category['comment']),
    'CAT_VISIBLE'       => boolean_to_string($category['visible']),

    'U_JUMPTO' => make_index_url(
      array(
        'category' => $category
        )
      ),

    'U_CHILDREN' => $cat_list_url.'&amp;parent_id='.$category['id'],
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=cat_modify',

    'F_ACTION' => $form_action,
    )
  );
 
if ($conf['activate_comments'])
{
  $template->assign('CAT_COMMENTABLE', boolean_to_string($category['commentable']));
}

// manage album elements link
if ($category['has_images'])
{
  $template->assign(
    'U_MANAGE_ELEMENTS',
    $base_url.'batch_manager&amp;cat='.$category['id']
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
    $intro = sprintf(
      l10n('This album contains %d photos, added on %s.'),
      $image_count,
      format_date($min_date)
      );
  }
  else
  {
    $intro = sprintf(
      l10n('This album contains %d photos, added between %s and %s.'),
      $image_count,
      format_date($min_date),
      format_date($max_date)
      );
  }
}
else
{
  $intro = l10n('This album contains no photo.');
}

$template->assign('INTRO', $intro);

$template->assign(
  'U_MANAGE_RANKS',
  $base_url.'element_set_ranks&amp;cat_id='.$category['id']
  );

if ($category['is_virtual'])
{
  $template->assign(
    array(
      'U_DELETE' => $self_url.'&amp;delete='.$category['id'].'&amp;pwg_token='.get_pwg_token(),
      )
    );
}
else
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
      $base_url.'site_update&amp;site=1&amp;cat_id='.$category['id']
      );
  }

}

// representant management
if ($category['has_images']
    or !empty($category['representative_picture_id']))
{
  $tpl_representant = array();

  // picture to display : the identified representant or the generic random
  // representant ?
  if (!empty($category['representative_picture_id']))
  {
    $query = '
SELECT id,representative_ext,path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$category['representative_picture_id'].'
;';
    $row = pwg_db_fetch_assoc(pwg_query($query));
    $src = DerivativeImage::thumb_url($row);
    $url = get_root_url().'admin.php?page=photo-'.$category['representative_picture_id'];

    $tpl_representant['picture'] =
      array(
        'SRC' => $src,
        'URL' => $url
      );
  }

  // can the admin choose to set a new random representant ?
  $tpl_representant['ALLOW_SET_RANDOM'] = ($category['has_images']) ? true : false;

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
  // the category can be moved in any category but in itself, in any
  // sub-category
  $unmovables = get_subcat_ids(array($category['id']));

  $query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
  WHERE id NOT IN ('.implode(',', $unmovables).')
;';

  display_select_cat_wrapper(
    $query,
    empty($category['id_uppercat']) ? array() : array($category['id_uppercat']),
    'move_cat_options'
    );
}

trigger_action('loc_end_cat_modify');

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'album_properties');
?>
