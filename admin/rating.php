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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('display', $_GET, false, PATTERN_ID);

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('rating');
$tabsheet->select('rating');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                            initialization                             |
// +-----------------------------------------------------------------------+
if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $start = $_GET['start'];
}
else
{
  $start = 0;
}

$elements_per_page=10;
if (isset($_GET['display']) and is_numeric($_GET['display']))
{
  $elements_per_page = $_GET['display'];
}

$order_by_index=0;
if (isset($_GET['order_by']) and is_numeric($_GET['order_by']))
{
  $order_by_index = $_GET['order_by'];
}

$page['user_filter'] = '';
if (isset($_GET['users']))
{
  if ($_GET['users'] == 'user')
  {
    $page['user_filter'] = ' AND r.user_id <> '.$conf['guest_id'];
  }
  elseif ($_GET['users'] == 'guest')
  {
    $page['user_filter'] = ' AND r.user_id = '.$conf['guest_id'];
  }
}

$page['cat_filter'] = '';
if (isset($_GET['cat']) and is_numeric($_GET['cat']))
{
  $cat_ids = get_subcat_ids(array($_GET['cat']));

  if (count($cat_ids) > 0)
  {
    $page['cat_filter'] = ' AND ic.category_id IN ('.implode(',', $cat_ids).')';
  }
}

$users = array();
$query = '
SELECT '.$conf['user_fields']['username'].' as username, '.$conf['user_fields']['id'].' as id
  FROM '.USERS_TABLE.'
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $users[$row['id']]=stripslashes($row['username']);
}


$query = '
SELECT
    COUNT(DISTINCT(r.element_id))
  FROM '.RATE_TABLE.' AS r';

if (!empty($page['cat_filter']))
{
  $query.= '
    JOIN '.IMAGES_TABLE.' AS i ON r.element_id = i.id
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id';
}

$query.= '
WHERE 1=1'. $page['user_filter'];
list($nb_images) = pwg_db_fetch_row(pwg_query($query));

$query = '
SELECT
    COUNT(*)
  FROM '.RATE_TABLE.
';';
list($nb_elements) = pwg_db_fetch_row(pwg_query($query));

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filename('rating', 'rating.tpl');

$template->assign(
  array(
    'navbar' => create_navigation_bar(
      PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start','del')),
      $nb_images,
      $start,
      $elements_per_page
      ),
    'F_ACTION' => PHPWG_ROOT_PATH.'admin.php',
    'DISPLAY' => $elements_per_page,
    'NB_ELEMENTS' => $nb_elements,
    'category' => (isset($_GET['cat']) ? array($_GET['cat']) : array()),
    'CACHE_KEYS' => get_admin_client_cache_keys(array('categories')),
    )
  );



$available_order_by= array(
    array(l10n('Rate date'), 'recently_rated DESC'),
    array(l10n('Rating score'), 'score DESC'),
    array(l10n('Average rate'), 'avg_rates DESC'),
    array(l10n('Number of rates'), 'nb_rates DESC'),
    array(l10n('Sum of rates'), 'sum_rates DESC'),
    array(l10n('File name'), 'file DESC'),
    array(l10n('Creation date'), 'date_creation DESC'),
    array(l10n('Post date'), 'date_available DESC'),
  );

for ($i=0; $i<count($available_order_by); $i++)
{
  $template->append(
    'order_by_options',
    $available_order_by[$i][0]
    );
}
$template->assign('order_by_options_selected', array($order_by_index) );


$user_options = array(
  'all'   => l10n('all'),
  'user'  => l10n('Users'),
  'guest' => l10n('Guests'),
  );

$template->assign('user_options', $user_options );
$template->assign('user_options_selected', array(@$_GET['users']) );
$template->assign('ADMIN_PAGE_TITLE', l10n('Rating'));

$query = '
SELECT i.id,
    i.path,
    i.file,
    i.representative_ext,
    i.rating_score       AS score,
    MAX(r.date)          AS recently_rated,
    ROUND(AVG(r.rate),2) AS avg_rates,
    COUNT(r.rate)        AS nb_rates,
    SUM(r.rate)          AS sum_rates
  FROM '.RATE_TABLE.' AS r
    LEFT JOIN '.IMAGES_TABLE.' AS i ON r.element_id = i.id';

if (!empty($page['cat_filter']))
{
  $query.= '
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id';
}

$query.= '
  WHERE 1 = 1 ' . $page['user_filter'] . $page['cat_filter'] . '
  GROUP BY i.id,
        i.path,
        i.file,
        i.representative_ext,
        i.rating_score,
        r.element_id
  ORDER BY ' . $available_order_by[$order_by_index][1] .'
  LIMIT '.$elements_per_page.' OFFSET '.$start.'
;';

$images = array();
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $images[] = $row;
}

$template->assign( 'images', array() );
foreach ($images as $image)
{
  $thumbnail_src = DerivativeImage::thumb_url($image);

  $image_url = get_root_url().'admin.php?page=photo-'.$image['id'];

  $query = 'SELECT *
FROM '.RATE_TABLE.' AS r
WHERE r.element_id='.$image['id'] . '
ORDER BY date DESC;';
  $result = pwg_query($query);
  $nb_rates = pwg_db_num_rows($result);

  $tpl_image = 
    array(
      'id' => $image['id'],
      'U_THUMB' => $thumbnail_src,
      'U_URL' => $image_url,
      'SCORE_RATE' => $image['score'],
       'AVG_RATE' => $image['avg_rates'],
       'SUM_RATE' => $image['sum_rates'],
       'NB_RATES' => (int)$image['nb_rates'],
       'NB_RATES_TOTAL' => (int)$nb_rates,
       'FILE' => $image['file'],
       'rates'  => array()
   );

  while ($row = pwg_db_fetch_assoc($result))
  {
    if ( isset($users[$row['user_id']]) )
    {
      $user_rate = $users[$row['user_id']];
    }
    else
    {
      $user_rate = '? '. $row['user_id'];
    }
    if ( strlen($row['anonymous_id'])>0 )
    {
      $user_rate .= '('.$row['anonymous_id'].')';
    }

    $row['USER'] = $user_rate;
    $tpl_image['rates'][] = $row;
  }
  $template->append( 'images', $tpl_image );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'rating');
?>