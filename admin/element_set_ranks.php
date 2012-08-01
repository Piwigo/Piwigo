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
  if (isset($_POST['rank_of_image']))
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

  if (!empty($_POST['image_order_choice'])
      && in_array($_POST['image_order_choice'], $image_order_choices))
  {
    $image_order_choice = $_POST['image_order_choice'];
  }

  $image_order = null;
  if ($image_order_choice=='user_define')
  {
    for ($i=0; $i<3; $i++)
    {
      if (!empty($_POST['image_order'][$i]))
      {
        if (!empty($image_order)) $image_order.= ',';
        $image_order.= $_POST['image_order'][$i];
      }
    }
  }
  elseif ($image_order_choice=='rank')
  {
    $image_order = 'rank';
  }
  $query = '
UPDATE '.CATEGORIES_TABLE.' 
  SET image_order=\''.$image_order.'\'
  WHERE id='.$page['category_id'];
  pwg_query($query);

  if (isset($_POST['image_order_subcats']))
  {
    $cat_info = get_cat_info($page['category_id']);

    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET image_order = '.(isset($image_order) ? '\''.$image_order.'\'' : 'NULL').'
  WHERE uppercats LIKE \''.$cat_info['uppercats'].',%\'';
    pwg_query($query);
  }

  array_push($page['infos'], l10n('Your configuration settings are saved'));
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
  get_root_url().'admin.php?page=album-'
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
    file,
    path,
    representative_ext,
    width, height, rotation,
    name,
    rank
  FROM '.IMAGES_TABLE.'
    JOIN '.IMAGE_CATEGORY_TABLE.' ON image_id = id
  WHERE category_id = '.$page['category_id'].'
  ORDER BY rank
;';
$result = pwg_query($query);
if (pwg_db_num_rows($result) > 0)
{
	// template thumbnail initialization
	$current_rank = 1;
	$thumbnail_info=array();
	$clipping=array();
  $derivativeParams = ImageStdParams::get_by_type(IMG_SQUARE);
	while ($row = pwg_db_fetch_assoc($result))
	{
    $derivative = new DerivativeImage($derivativeParams, new SrcImage($row));

		if ( !empty( $row['name'] ) )
		{
			$thumbnail_name = $row['name'];
		}
		else
		{
			$file_wo_ext = get_filename_wo_extension($row['file']);
			$thumbnail_name = str_replace('_', ' ', $file_wo_ext);
		}
		$current_rank++;
		$template->append(
			'thumbnails',
			array(
				'ID' => $row['id'],
				'NAME' => $thumbnail_name,
				'TN_SRC' => $derivative->get_url(),
				'RANK' => $current_rank * 10,
				'SIZE' => $derivative->get_size(),
				)
			);
	}
}
// image order management
$sort_fields = array(
  ''                    => '',
  'file'                => l10n('File name, A &rarr; Z'),
  'file DESC'           => l10n('File name, Z &rarr; A'),
  'name'                => l10n('Photo title, A &rarr; Z'),
  'name DESC'           => l10n('Photo title, Z &rarr; A'),
  'date_creation DESC'  => l10n('Date created, new &rarr; old'),
  'date_creation'       => l10n('Date created, old &rarr; new'),
  'date_available DESC' => l10n('Date posted, new &rarr; old'),
  'date_available'      => l10n('Date posted, old &rarr; new'),
  'rating_score DESC'   => l10n('Rating score, high &rarr; low'),
  'rating_score'        => l10n('Rating score, low &rarr; high'),
  'hit DESC'            => l10n('Visits, high &rarr; low'),
  'hit'                 => l10n('Visits, low &rarr; high'),
  'id'                  => l10n('Numeric identifier, 1 &rarr; 9'),
  'id DESC'             => l10n('Numeric identifier, 9 &rarr; 1'),
  'rank'                => l10n('Manual sort order'),
  );

$template->assign('image_order_options', $sort_fields);

$image_order = explode(',', $category['image_order']);

for ($i=0; $i<3; $i++) // 3 fields
{
  if ( isset($image_order[$i]) )
  {
    $template->append('image_order', $image_order[$i]);
  }
  else
  {
    $template->append('image_order', '');
  }
}

$template->assign('image_order_choice', $image_order_choice);


// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'element_set_ranks');
?>
