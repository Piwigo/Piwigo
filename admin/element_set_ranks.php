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

$image_order_choices = array('default', 'rank', 'user_define');
$image_order_choice = 'default';

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

  $image_order = null;
  if (!empty($_POST['image_order_choice']) 
      && in_array($_POST['image_order_choice'], $image_order_choices))
  {
    $image_order_choice = $_POST['image_order_choice'];
  }
      
  if ($image_order_choice=='user_define')
  {
    for ($i=1; $i<=3; $i++)
    {
      if ( !empty($_POST['order_field_'.$i]) )
      {
        if (! empty($image_order) )
        {
          $image_order .= ',';
        }
        $image_order .= $_POST['order_field_'.$i];
        if ($_POST['order_direction_'.$i]=='DESC')
        {
          $image_order .= ' DESC';
        }
      }
    }
  }
  elseif ($image_order_choice=='rank')
  {
    $image_order = 'rank';
  }
  $query = '
UPDATE '.CATEGORIES_TABLE.' SET image_order=\''.$image_order.'\'
  WHERE id='.$page['category_id'];
  pwg_query($query);
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+
$template->set_filenames(
  array('element_set_ranks' => 'element_set_ranks.tpl')
  );

$base_url = get_root_url().'admin.php';

$query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$page['category_id'].'
;';
$category = pwg_db_fetch_assoc(pwg_query($query));

if ($category['image_order']=='rank')
{
  $image_order_choice = 'rank';
}
elseif ($category['image_order']!='') 
{
  $image_order_choice = 'user_define';
}

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

while ($row = pwg_db_fetch_assoc($result))
{
  $src = get_thumbnail_url($row);

  list($thumbnail_width, $thumbnail_height) = getimagesize($src);
  $thumbnail_x_center = $thumbnail_width/2;
  $thumbnail_y_center = $thumbnail_height/2;
  
  $template->append(
    'thumbnails',
    array(
      'ID' => $row['id'],
      'TN_SRC' => $src,
      'RANK' => $current_rank * 10,
      'CLIP_TOP' => round($thumbnail_y_center - 96/2),
      'CLIP_RIGHT' => round($thumbnail_x_center + 96/2),
      'CLIP_BOTTOM' => round($thumbnail_y_center + 96/2),
      'CLIP_LEFT' => round($thumbnail_x_center - 96/2)
      )
    );

  $current_rank++;
}

// image order management
$sort_fields = array(
  '' => '',
  'date_creation' => l10n('Creation date'),
  'date_available' => l10n('Post date'),
  'average_rate' => l10n('Average rate'),
  'hit' => l10n('Most visited'),
  'file' => l10n('File name'),
  'id' => 'Id',
  'rank' => l10n('Rank'),
  );

$sort_directions = array(
  'ASC' => l10n('ascending'),
  'DESC' => l10n('descending'),
  );

$template->assign('image_order_field_options', $sort_fields);
$template->assign('image_order_direction_options', $sort_directions);

$matches = array();
if ( !empty( $category['image_order'] ) )
{
  preg_match_all('/([a-z_]+) *(?:(asc|desc)(?:ending)?)? *(?:, *|$)/i',
    $category['image_order'], $matches);
}

for ($i=0; $i<3; $i++) // 3 fields
{
  $tpl_image_order_select = array(
      'ID' => $i+1,
      'FIELD' => array(''),
      'DIRECTION' => array('ASC'),
    );

  if ( isset($matches[1][$i]) )
  {
    $tpl_image_order_select['FIELD'] = array($matches[1][$i]);
  }

  if (isset($matches[2][$i]) and strcasecmp($matches[2][$i],'DESC')==0)
  {
    $tpl_image_order_select['DIRECTION'] = array('DESC');
  }
  $template->append( 'image_orders', $tpl_image_order_select);
}

$template->assign('image_order_choice', $image_order_choice);


// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_ranks');
?>
