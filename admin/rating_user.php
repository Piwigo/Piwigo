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

defined('PHPWG_ROOT_PATH') or die ("Hacking attempt!");

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('rating');
$tabsheet->select('rating_user');
$tabsheet->assign();

$filter_min_rates = 2;
if (isset($_GET['f_min_rates']))
{
  $filter_min_rates = (int)$_GET['f_min_rates'];
}

$consensus_top_number = $conf['top_number'];
if (isset($_GET['consensus_top_number']))
{
  $consensus_top_number = (int)$_GET['consensus_top_number'];
}

// build users
global $conf;
$query = 'SELECT DISTINCT
  u.'.$conf['user_fields']['id'].' AS id,
  u.'.$conf['user_fields']['username'].' AS name,
  ui.status
  FROM '.USERS_TABLE.' AS u INNER JOIN '.USER_INFOS_TABLE.' AS ui
    ON u.'.$conf['user_fields']['id'].' = ui.user_id';

$users_by_id = array();
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $users_by_id[(int)$row['id']] = array(
    'name' => $row['name'],
    'anon' => is_autorize_status(ACCESS_CLASSIC, $row['status']) ? false : true
  );
}

$by_user_rating_model = array( 'rates' => array() );
foreach($conf['rate_items'] as $rate)
{
  $by_user_rating_model['rates'][$rate] = array();
}

// by user aggregation
$image_ids = array();
$by_user_ratings = array();
$query = '
SELECT * FROM '.RATE_TABLE.' ORDER by date DESC';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  if (!isset($users_by_id[$row['user_id']]))
  {
    $users_by_id[$row['user_id']] = array('name' => '???'.$row['user_id'], 'anon' => false);
  }
  $usr = $users_by_id[$row['user_id']];
  if ($usr['anon'])
  {
    $user_key = $usr['name'].'('.$row['anonymous_id'].')';
  }
  else
  {
    $user_key = $usr['name'];
  }
  $rating = & $by_user_ratings[$user_key];
  if ( is_null($rating) )
  {
    $rating = $by_user_rating_model;
    $rating['uid'] = (int)$row['user_id'];
    $rating['aid'] = $usr['anon'] ? $row['anonymous_id'] : '';
    $rating['last_date'] = $rating['first_date'] = $row['date'];
  }
  else
    $rating['first_date'] = $row['date'];

  $rating['rates'][$row['rate']][] = array(
    'id' => $row['element_id'],
    'date' => $row['date'],
  );
  $image_ids[$row['element_id']] = 1;
  unset($rating);
}

// get image tn urls
$image_urls = array();
if (count($image_ids) > 0 )
{
  $query = 'SELECT id, name, file, path, representative_ext, level
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($image_ids)).')';
  $result = pwg_query($query);
  $params = ImageStdParams::get_by_type(IMG_SQUARE);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $image_urls[ $row['id'] ] = array(
      'tn' => DerivativeImage::url($params, $row),
      'page' => make_picture_url( array('image_id'=>$row['id'], 'image_file'=>$row['file']) ),
    );
  }
}

//all image averages
$query='SELECT element_id,
    AVG(rate) AS avg
  FROM '.RATE_TABLE.'
  GROUP BY element_id';
$all_img_sum = array();
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $all_img_sum[(int)$row['element_id']] = array( 'avg'=>(float)$row['avg'] );
}

$query='SELECT id
  FROM '.IMAGES_TABLE.'
  ORDER by rating_score DESC
  LIMIT '.$consensus_top_number;
$best_rated = array_flip( array_from_query($query, 'id'));

// by user stats
foreach($by_user_ratings as $id => &$rating)
{
  $c=0; $s=0; $ss=0; $consensus_dev=0; $consensus_dev_top=0; $consensus_dev_top_count=0;
  foreach($rating['rates'] as $rate => $rates)
  {
    $ct = count($rates);
    $c += $ct;
    $s += $ct * $rate;
    $ss += $ct * $rate * $rate;
    foreach($rates as $id_date)
    {
      $dev = abs($rate - $all_img_sum[$id_date['id']]['avg']);
      $consensus_dev += $dev;
      if (isset($best_rated[$id_date['id']]))
      {
        $consensus_dev_top += $dev;
        $consensus_dev_top_count++;
      }
    }
  }

  $consensus_dev /= $c;
  if ($consensus_dev_top_count)
    $consensus_dev_top /= $consensus_dev_top_count;

  $var = ($ss - $s*$s/$c)/$c;
  $rating += array(
    'id' => $id,
    'count' => $c,
    'avg' => $s/$c,
    'cv'  => $s==0 ? -1 : sqrt($var)/($s/$c), // http://en.wikipedia.org/wiki/Coefficient_of_variation
    'cd'  => $consensus_dev,
    'cdtop'  => $consensus_dev_top_count ? $consensus_dev_top : '',
  );
}
unset($rating);

// filter
foreach($by_user_ratings as $id => $rating)
{
  if ($rating['count'] <= $filter_min_rates)
  {
    unset($by_user_ratings[$id]);
  }
}


function avg_compare($a, $b)
{
  $d = $a['avg'] - $b['avg'];
  return ($d==0) ? 0 : ($d<0 ? -1 : 1);
}

function count_compare($a, $b)
{
  $d = $a['count'] - $b['count'];
  return ($d==0) ? 0 : ($d<0 ? -1 : 1);
}

function cv_compare($a, $b)
{
  $d = $b['cv'] - $a['cv']; //desc
  return ($d==0) ? 0 : ($d<0 ? -1 : 1);
}

function consensus_dev_compare($a, $b)
{
  $d = $b['cd'] - $a['cd']; //desc
  return ($d==0) ? 0 : ($d<0 ? -1 : 1);
}

function last_rate_compare($a, $b)
{
  return -strcmp( $a['last_date'], $b['last_date']);
}

$order_by_index=4;
if (isset($_GET['order_by']) and is_numeric($_GET['order_by']))
{
  $order_by_index = $_GET['order_by'];
}

$available_order_by= array(
    array(l10n('Average rate'), 'avg_compare'),
    array(l10n('Number of rates'), 'count_compare'),
    array(l10n('Variation'), 'cv_compare'),
    array(l10n('Consensus deviation'), 'consensus_dev_compare'),
    array(l10n('Last'), 'last_rate_compare'),
  );

for ($i=0; $i<count($available_order_by); $i++)
{
  $template->append(
    'order_by_options',
    $available_order_by[$i][0]
    );
}
$template->assign('order_by_options_selected', array($order_by_index) );

$x = uasort($by_user_ratings, $available_order_by[$order_by_index][1] );

$template->assign( array(
  'F_ACTION' => get_root_url().'admin.php',
  'F_MIN_RATES' => $filter_min_rates,
  'CONSENSUS_TOP_NUMBER' => $consensus_top_number,
  'available_rates' => $conf['rate_items'],
  'ratings' => $by_user_ratings,
  'image_urls' => $image_urls,
  'TN_WIDTH' => ImageStdParams::get_by_type(IMG_SQUARE)->sizing->ideal_size[0],
  ) );
$template->set_filename('rating', 'rating_user.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'rating');

?>