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
 * Change rank of images inside a category
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

if (!isset($_GET['cat_id']) or !is_numeric($_GET['cat_id']))
{
  trigger_error('missing cat_id param', E_USER_ERROR);
}

$page['category_id'] = $_GET['cat_id'];

// +-----------------------------------------------------------------------+
// |                               functions                               |
// +-----------------------------------------------------------------------+

/**
 * save the rank depending on given images order
 *
 * The list of ordered images id is supposed to be in the same parent
 * category
 *
 * @param array categories
 * @return void
 */
function save_images_order($category_id, $images)
{
  $current_rank = 0;
  $datas = array();
  foreach ($images as $id)
  {
    array_push(
      $datas,
      array(
        'category_id' => $category_id,
        'image_id' => $id,
        'rank' => ++$current_rank,
        )
      );
  }
  $fields = array(
    'primary' => array('image_id', 'category_id'),
    'update' => array('rank')
    );
  mass_updates(IMAGE_CATEGORY_TABLE, $fields, $datas);
}

// +-----------------------------------------------------------------------+
// |                       global mode form submission                     |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit']))
{
  asort($_POST['rank_of_image'], SORT_NUMERIC);
  
  save_images_order(
    $page['category_id'],
    array_keys($_POST['rank_of_image'])
    );

  array_push(
    $page['infos'],
    l10n('Images manual order was saved')
    );
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(
  array('element_set_ranks' => 'element_set_ranks.tpl')
  );

$base_url = get_root_url().'admin.php';

// $form_action = $base_url.'?page=element_set_global';

$query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$page['category_id'].'
;';
$category = mysql_fetch_assoc(pwg_query($query));

// Navigation path
$navigation = get_cat_display_name_cache(
  $category['uppercats'],
  get_root_url().'admin.php?page=cat_modify&amp;cat_id='
  );

$template->assign(
  array(
    'CATEGORIES_NAV' => $navigation,
    'F_ACTION' => $base_url.get_query_string_diff(array()),
   )
 );

// +-----------------------------------------------------------------------+
// |                              thumbnails                               |
// +-----------------------------------------------------------------------+

$query = '
SELECT
    id,
    path,
    tn_ext,
    rank
  FROM '.IMAGES_TABLE.'
    JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE category_id = '.$page['category_id'].'
  ORDER BY rank
;';
$result = pwg_query($query);

// template thumbnail initialization
$current_rank = 1;

while ($row = mysql_fetch_assoc($result))
{
  $src = get_thumbnail_url($row);
  
  $template->append(
    'thumbnails',
    array(
      'ID' => $row['id'],
      'TN_SRC' => $src,
      'RANK' => $current_rank * 10,
      )
    );

  $current_rank++;
}

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_ranks');
?>
