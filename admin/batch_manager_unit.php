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

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(
  array('batch_manager_unit' => 'batch_manager_unit.tpl'));

$base_url = PHPWG_ROOT_PATH.'admin.php';

$template->assign(
  array(
    'U_ELEMENTS_PAGE' => $base_url.get_query_string_diff(array('display','start')),
    'F_ACTION' => $base_url.get_query_string_diff(array()),
    'level_options' => get_privacy_level_options(),
    )
  );

// +-----------------------------------------------------------------------+
// |                        global mode thumbnails                         |
// +-----------------------------------------------------------------------+

// how many items to display on this page
if (!empty($_GET['display']))
{
  $page['nb_images'] = intval($_GET['display']);
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

  while ($row = pwg_db_fetch_assoc($result))
  {
    $element_ids[] = $row['id'];

    $src_image = new SrcImage($row);

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

    $template->append(
      'elements', array_merge($row,
      array(
        'ID' => $row['id'],
        'TN_SRC' => DerivativeImage::url(IMG_THUMB, $src_image),
        'FILE_SRC' => DerivativeImage::url(IMG_LARGE, $src_image),
        'LEGEND' => $legend,
        'U_EDIT' => get_root_url().'admin.php?page=photo-'.$row['id'],
        'NAME' => htmlspecialchars(@$row['name']),
        'AUTHOR' => htmlspecialchars(@$row['author']),
        'LEVEL' => !empty($row['level'])?$row['level']:'0',
        'DESCRIPTION' => htmlspecialchars(@$row['comment']),
        'DATE_CREATION' => $row['date_creation'],
        'TAGS' => $tag_selection,
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