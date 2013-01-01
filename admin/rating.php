<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);


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

if (isset($_GET['del']))
{
  include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
  $del_params = urldecode( $_GET['del'] );
  parse_str($del_params, $vars);
  if ( !is_numeric($vars['e']) or !is_numeric($vars['u']) )
  {
    die('Hacking attempt');
  }
  $query = '
DELETE FROM '. RATE_TABLE .'
WHERE element_id=' . $vars['e'] . '
AND user_id=' . $vars['u'] . '
AND anonymous_id=\'' . $vars['a'] . '\'
;';
  pwg_query($query);
  update_rating_score( $vars['e'] );
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


$query = 'SELECT COUNT(DISTINCT(r.element_id))
FROM '.RATE_TABLE.' AS r
WHERE 1=1'. $page['user_filter'];
list($nb_images) = pwg_db_fetch_row(pwg_query($query));


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
    'NB_ELEMENTS' => $nb_images,
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
    LEFT JOIN '.IMAGES_TABLE.' AS i ON r.element_id = i.id
  WHERE 1 = 1 ' . $page['user_filter'] . '
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
  array_push($images, $row);
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

    $url_del = PHPWG_ROOT_PATH.'admin.php'.
                get_query_string_diff(array('del'));

    $del_param = 'e='.$image['id'].
                 '&u='.$row['user_id'].
                 '&a='.$row['anonymous_id'];

    $url_del .= '&amp;del='.urlencode(urlencode($del_param));

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

    $tpl_image['rates'][] =
       array(
         'DATE' => /*format_date*/($row['date']),
         'RATE' => $row['rate'],
         'USER' => $user_rate,
         'U_DELETE' => $url_del
     );
  }
  $template->append( 'images', $tpl_image );
}

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'rating');
?>