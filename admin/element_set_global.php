<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// |                         deletion form submission                      |
// +-----------------------------------------------------------------------+

// the $_POST['selection'] was already checked in element_set.php
check_input_parameter('add_tags', @$_POST['add_tags'], true, PATTERN_ID);
check_input_parameter('del_tags', @$_POST['del_tags'], true, PATTERN_ID);
check_input_parameter('associate', @$_POST['associate'], false, PATTERN_ID);
check_input_parameter('dissociate', @$_POST['dissociate'], false, PATTERN_ID);

if (isset($_POST['delete']))
{
  if (isset($_POST['confirm_deletion']) and 1 == $_POST['confirm_deletion'])
  {
    $collection = array();

    switch ($_POST['target_deletion'])
    {
      case 'all' :
      {
        $collection = $page['cat_elements_id'];
        break;
      }
      case 'selection' :
      {
        if (!isset($_POST['selection']) or count($_POST['selection']) == 0)
        {
          array_push($page['errors'], l10n('Select at least one picture'));
        }
        else
        {
          $collection = $_POST['selection'];
        }
        break;
      }
    }

    // filter selection on photos that have no storage_category_id (ie that
    // were added via pLoader)
    if (count($collection) > 0)
    {
      $query = '
SELECT
    id
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
    AND storage_category_id IS NULL
;';
      $deletables = array_from_query($query, 'id');

      if (count($deletables) > 0)
      {
        $physical_deletion = true;
        delete_elements($deletables, $physical_deletion);

        array_push(
          $page['infos'],
          sprintf(
            l10n_dec(
              '%d photo was deleted',
              '%d photos were deleted',
              count($deletables)
              ),
            count($deletables)
            )
          );
      }
      else
      {
        array_push($page['errors'], l10n('No photo can be deleted'));
      }
    }
  }
  else
  {
    array_push($page['errors'], l10n('You need to confirm deletion'));
  }
}

// +-----------------------------------------------------------------------+
// |                       global mode form submission                     |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  $collection = array();

//   echo '<pre>';
//   print_r($_POST);
//   echo '</pre>';
//   exit();

  switch ($_POST['target'])
  {
    case 'all' :
    {
      $collection = $page['cat_elements_id'];
      break;
    }
    case 'selection' :
    {
      if (!isset($_POST['selection']) or count($_POST['selection']) == 0)
      {
        array_push($page['errors'], l10n('Select at least one picture'));
      }
      else
      {
        $collection = $_POST['selection'];
      }
      break;
    }
  }

  if (isset($_POST['add_tags']) and count($collection) > 0)
  {
    add_tags($_POST['add_tags'], $collection);
  }

  if (isset($_POST['del_tags']) and count($collection) > 0)
  {
    $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $collection).')
    AND tag_id IN ('.implode(',', $_POST['del_tags']).')
;';
    pwg_query($query);
  }

  if ($_POST['associate'] != 0 and count($collection) > 0)
  {
    associate_images_to_categories(
      $collection,
      array($_POST['associate'])
      );
  }

  if ($_POST['dissociate'] != 0 and count($collection) > 0)
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

      // we remove the dissociated images if we are currently displaying the
      // category to dissociate from.
      if (is_numeric($_GET['cat']) and $_POST['dissociate'] == $_GET['cat'])
      {
        $page['cat_elements_id'] = array_diff(
          $page['cat_elements_id'],
          $dissociables
          );
      }
    }

    update_category($_POST['dissociate']);
  }

  $datas = array();
  $dbfields = array('primary' => array('id'), 'update' => array());

  $formfields = array('author', 'name', 'date_creation', 'level');
  foreach ($formfields as $formfield)
  {
    if ($_POST[$formfield.'_action'] != 'leave')
    {
      array_push($dbfields['update'], $formfield);
    }
  }

  // updating elements is useful only if needed...
  if (count($dbfields['update']) > 0 and count($collection) > 0)
  {
    $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $collection).')
;';
    $result = pwg_query($query);

    while ($row = mysql_fetch_array($result))
    {
      $data = array();
      $data['id'] = $row['id'];

      if ('set' == $_POST['author_action'])
      {
        $data['author'] = $_POST['author'];
        if ('' == $data['author'])
        {
          unset($data['author']);
        }
      }

      if ('set' == $_POST['name_action'])
      {
        $data['name'] = $_POST['name'];
        if ('' == $data['name'])
        {
          unset($data['name']);
        }
      }

      if ('set' == $_POST['date_creation_action'])
      {
        $data['date_creation'] =
          $_POST['date_creation_year']
          .'-'.$_POST['date_creation_month']
          .'-'.$_POST['date_creation_day']
          ;
      }

      if ('set' == $_POST['level_action'])
      {
        $data['level'] = $_POST['level'];
      }

      array_push($datas, $data);
    }
    // echo '<pre>'; print_r($datas); echo '</pre>';
    mass_updates(IMAGES_TABLE, $dbfields, $datas);
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(
  array('element_set_global' => 'element_set_global.tpl'));

$base_url = get_root_url().'admin.php';

// $form_action = $base_url.'?page=element_set_global';

$template->assign(
  array(
    'CATEGORIES_NAV'=>$page['title'],

    'U_DISPLAY'=>$base_url.get_query_string_diff(array('display')),

    'U_UNIT_MODE'
    =>
    $base_url
    .get_query_string_diff(array('mode','display'))
    .'&amp;mode=unit',

    'F_ACTION'=>$base_url.get_query_string_diff(array()),
   )
 );

// +-----------------------------------------------------------------------+
// |                            caddie options                             |
// +-----------------------------------------------------------------------+

$template->assign('IN_CADDIE', 'caddie' == $_GET['cat'] ? true : false );

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
  list($counter) = mysql_fetch_row(pwg_query($query));

  if ($counter > 0)
  {
    $template->assign('show_delete_form', true);
  }
}

// +-----------------------------------------------------------------------+
// |                           global mode form                            |
// +-----------------------------------------------------------------------+

// Virtualy associate a picture to a category
$query = '
SELECT id,name,uppercats,global_rank
  FROM '.CATEGORIES_TABLE.'
;';
display_select_cat_wrapper($query, array(), 'associate_options', true);

// Dissociate from a category : categories listed for dissociation can
// only represent virtual links. Links to physical categories can't be
// broken
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

$all_tags = get_all_tags();

if (count($all_tags) > 0)
{// add tags
  $template->assign(
    array(
      'ADD_TAG_SELECTION' => get_html_tag_selection(
                              $all_tags,
                              'add_tags'
                              ),
      )
    );
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
$tpl_options = array();
foreach ($conf['available_permission_levels'] as $level)
{
  $tpl_options[$level] = l10n( sprintf('Level %d', $level) );
}
$template->assign(
    array(
      'level_options'=> $tpl_options,
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

if (count($page['cat_elements_id']) > 0)
{
  $nav_bar = create_navigation_bar(
    $base_url.get_query_string_diff(array('start')),
    count($page['cat_elements_id']),
    $page['start'],
    $page['nb_images']
    );
  $template->assign('NAV_BAR', $nav_bar);

  $query = '
SELECT id,path,tn_ext,file,filesize,level
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $page['cat_elements_id']).')
  '.$conf['order_by'].'
  LIMIT '.$page['start'].', '.$page['nb_images'].'
;';
  //echo '<pre>'.$query.'</pre>';
  $result = pwg_query($query);

  // template thumbnail initialization

  while ($row = mysql_fetch_assoc($result))
  {
    $src = get_thumbnail_url($row);

    $template->append(
      'thumbnails',
      array(
        'ID' => $row['id'],
        'TN_SRC' => $src,
        'FILE' => $row['file'],
        'TITLE' => get_thumbnail_title($row),
        'LEVEL' => $row['level']
        )
      );
  }
}

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_global');
?>
