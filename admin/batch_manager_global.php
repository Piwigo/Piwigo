<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

trigger_action('loc_begin_element_set_global');

// the $_POST['selection'] was already checked in element_set.php
check_input_parameter('del_tags', $_POST, true, PATTERN_ID);
check_input_parameter('associate', $_POST, false, PATTERN_ID);
check_input_parameter('dissociate', $_POST, false, PATTERN_ID);

// +-----------------------------------------------------------------------+
// |                            current selection                          |
// +-----------------------------------------------------------------------+

$collection = array();
if (isset($_POST['setSelected']))
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

if (isset($_POST['submit']))
{
  // if the user tries to apply an action, it means that there is at least 1
  // photo in the selection
  if (count($collection) == 0)
  {
    array_push($page['errors'], l10n('Select at least one picture'));
  }

  $action = $_POST['selectAction'];
  
  if ('remove_from_caddie' == $action)
  {
    $query = '
DELETE
  FROM '.CADDIE_TABLE.'
  WHERE element_id IN ('.implode(',', $collection).')
    AND user_id = '.$user['id'].'
;';
    pwg_query($query);

    // if we are here in the code, it means that the user is currently
    // displaying the caddie content, so we have to remove the current
    // selection from the current set
    $page['cat_elements_id'] = array_diff($page['cat_elements_id'], $collection);
  }

  if ('add_tags' == $action)
  {
    $tag_ids = get_fckb_tag_ids($_POST['add_tags']);
    add_tags($tag_ids, $collection);
  }

  if ('del_tags' == $action)
  {
    if (count($_POST['del_tags']) == 0)
    {
      array_push($page['errors'], l10n('Select at least one tag'));
    }
    
    $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $collection).')
    AND tag_id IN ('.implode(',', $_POST['del_tags']).')
;';
    pwg_query($query);
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
    $redirect_url = get_root_url().'admin.php?page='.$_GET['page'];
    redirect($redirect_url);
  }

  if ('dissociate' == $action)
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

      update_category($_POST['dissociate']);
      
      $_SESSION['page_infos'] = array(
        l10n('Information data registered in database')
        );
      
      // let's refresh the page because we the current set might be modified
      $redirect_url = get_root_url().'admin.php?page='.$_GET['page'];
      redirect($redirect_url);
    }
  }

  // author
  if ('author' == $action)
  {
    $datas = array();
    foreach ($collection as $image_id)
    {
      array_push(
        $datas,
        array(
          'id' => $image_id,
          'author' => $_POST['author']
          )
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('author')),
      $datas
      );
  }

  // title
  if ('title' == $action)
  {
    $datas = array();
    foreach ($collection as $image_id)
    {
      array_push(
        $datas,
        array(
          'id' => $image_id,
          'name' => $_POST['title']
          )
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('name')),
      $datas
      );
  }
  
  // date_creation
  if ('date_creation' == $action)
  {
    $date_creation = sprintf(
      '%u-%u-%u',
      $_POST['date_creation_year'],
      $_POST['date_creation_month'],
      $_POST['date_creation_day']
      );

    $datas = array();
    foreach ($collection as $image_id)
    {
      array_push(
        $datas,
        array(
          'id' => $image_id,
          'date_creation' => $date_creation
          )
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('date_creation')),
      $datas
      );
  }
  
  // privacy_level
  if ('level' == $action)
  {
    $datas = array();
    foreach ($collection as $image_id)
    {
      array_push(
        $datas,
        array(
          'id' => $image_id,
          'level' => $_POST['level']
          )
        );
    }

    mass_updates(
      IMAGES_TABLE,
      array('primary' => array('id'), 'update' => array('level')),
      $datas
      );
  }
  
  // add_to_caddie
  if ('add_to_caddie' == $action)
  {
    fill_caddie($collection);
  }
  
  // delete
  if ('delete' == $action)
  {
    if (isset($_POST['confirm_deletion']) and 1 == $_POST['confirm_deletion'])
    {
      $deleted_count = delete_elements($collection, true);
      if ($deleted_count > 0)
      {
        $_SESSION['page_infos'] = array(
          sprintf(
            l10n_dec(
              '%d photo was deleted',
              '%d photos were deleted',
              $deleted_count
              ),
            $deleted_count
            )
          );

        $redirect_url = get_root_url().'admin.php?page='.$_GET['page'];
        redirect($redirect_url);
      }
      else
      {
        array_push($page['errors'], l10n('No photo can be deleted'));
      }
    }
    else
    {
      array_push($page['errors'], l10n('You need to confirm deletion'));
    }
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('batch_manager_global' => 'batch_manager_global.tpl'));

$base_url = get_root_url().'admin.php';

$template->assign(
  array(
    'filter' => $_SESSION['bulk_manager_filter'],
    'selection' => $collection,
    'U_DISPLAY'=>$base_url.get_query_string_diff(array('display')),
    'F_ACTION'=>$base_url.get_query_string_diff(array('cat')),
   )
 );

// +-----------------------------------------------------------------------+
// |                            caddie options                             |
// +-----------------------------------------------------------------------+

$in_caddie = false;
if (isset($_SESSION['bulk_manager_filter']['prefilter'])
    and 'caddie' == $_SESSION['bulk_manager_filter']['prefilter'])
{
  $in_caddie = true;
}
$template->assign('IN_CADDIE', $in_caddie);

// +-----------------------------------------------------------------------+
// |                            deletion form                              |
// +-----------------------------------------------------------------------+

// we can only remove photos that have no storage_category_id, in other
// word, it currently (Butterfly) means that the photo was added with
// pLoader
if (count($page['cat_elements_id']) > 0)
{
  $query = '
SELECT
    COUNT(*)
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')
    AND storage_category_id IS NULL
;';
  list($counter) = pwg_db_fetch_row(pwg_query($query));

  if ($counter > 0)
  {
    $template->assign('show_delete_form', true);
  }
}

// +-----------------------------------------------------------------------+
// |                           global mode form                            |
// +-----------------------------------------------------------------------+

// privacy level
$template->assign(
    array(
      'filter_level_options'=> get_privacy_level_options(),
      'filter_level_options_selected' => isset($_SESSION['bulk_manager_filter']['level'])
        ? $_SESSION['bulk_manager_filter']['level']
        : 0,
    )
  );

// Virtualy associate a picture to a category
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper($query, array(), 'associate_options', true);

// in the filter box, which category to select by default
$selected_category = array();

if (isset($_SESSION['bulk_manager_filter']['category']))
{
  $selected_category = array($_SESSION['bulk_manager_filter']['category']);
}
else
{
  // we need to know the category in which the last photo was added
  $selected_category = array();

  $query = '
SELECT
    category_id,
    id_uppercat
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON image_id = i.id
    JOIN '.CATEGORIES_TABLE.' AS c ON category_id = c.id
  ORDER BY i.id DESC
  LIMIT 1
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) > 0)
  {
    $row = pwg_db_fetch_assoc($result);
  
    $selected_category = array($row['category_id']);
  }
}

$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper($query, $selected_category, 'filter_category_options', true);

// Dissociate from a category : categories listed for dissociation can only
// represent virtual links. We can't create orphans. Links to physical
// categories can't be broken.
if (count($page['cat_elements_id']) > 0)
{
  $query = '
SELECT
    DISTINCT(category_id) AS id,
    c.name,
    c.uppercats,
    c.global_rank
  FROM '.IMAGE_CATEGORY_TABLE.' AS ic
    JOIN '.CATEGORIES_TABLE.' AS c ON c.id = ic.category_id
    JOIN '.IMAGES_TABLE.' AS i ON i.id = ic.image_id
  WHERE ic.image_id IN ('.implode(',', $page['cat_elements_id']).')
    AND (
      ic.category_id != i.storage_category_id
      OR i.storage_category_id IS NULL
    )
;';
  display_select_cat_wrapper($query, array(), 'dissociate_options', true);
}

if (count($page['cat_elements_id']) > 0)
{
  // remove tags
  $tags = get_common_tags($page['cat_elements_id'], -1);

  $template->assign(
    array(
      'DEL_TAG_SELECTION' => get_html_tag_selection($tags, 'del_tags'),
      )
    );
}

// creation date
$day =
empty($_POST['date_creation_day']) ? date('j') : $_POST['date_creation_day'];

$month =
empty($_POST['date_creation_month']) ? date('n') : $_POST['date_creation_month'];

$year =
empty($_POST['date_creation_year']) ? date('Y') : $_POST['date_creation_year'];

$month_list = $lang['month'];
$month_list[0]='------------';
ksort($month_list);
$template->assign( array(
      'month_list'         => $month_list,
      'DATE_CREATION_DAY'  => (int)$day,
      'DATE_CREATION_MONTH'=> (int)$month,
      'DATE_CREATION_YEAR' => (int)$year,
    )
  );

// image level options
$template->assign(
    array(
      'level_options'=> get_privacy_level_options(),
      'level_options_selected' => 0,
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

  if (isset($_SESSION['bulk_manager_filter']['prefilter'])
      and 'duplicates' == $_SESSION['bulk_manager_filter']['prefilter'])
  {
    $conf['order_by'] = ' ORDER BY file, id';
  }


  $query = '
SELECT id,path,tn_ext,file,filesize,level,name
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

  // template thumbnail initialization
  while ($row = pwg_db_fetch_assoc($result))
  {
    $nb_thumbs_page++;
    $src = get_thumbnail_url($row);

    $title = $row['name'];
    if (empty($title))
    {      
      $title = get_name_from_file($row['file']);
    }

    $template->append(
      'thumbnails',
      array(
        'ID' => $row['id'],
        'TN_SRC' => $src,
        'FILE' => $row['file'],
        'TITLE' => $title,
        'LEVEL' => $row['level']
        )
      );
  }
}

$template->assign(
  array(
    'nb_thumbs_page' => $nb_thumbs_page,
    'nb_thumbs_set' => count($page['cat_elements_id']),
    )
  );

trigger_action('loc_end_element_set_global');

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'batch_manager_global');
?>
