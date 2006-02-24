<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-01-15 08:45:42 -0500 (Sun, 15 Jan 2006) $
// | last modifier : $Author: nikrou $
// | revision      : $Revision: 1004 $
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
include_once(PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');


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

$display_filter= '';
if (isset($_GET['display_filter']))
{
  if ( $_GET['display_filter']=='user' )
  {
    $display_filter= ' AND r.user_id <> ' . $conf['guest_id'];
    $template->assign_vars( array(
        'DISPLAY_FILTER_USER_CHECKED'=>'checked="checked"'
        )
    );
  }
  elseif ( $_GET['display_filter']=='guest' )
  {
    $display_filter= ' AND r.user_id =' . $conf['guest_id'];
    $template->assign_vars( array(
        'DISPLAY_FILTER_GUEST_CHECKED'=>'checked="checked"'
        )
     );
  }
}
if ($display_filter=='')
{
    $template->assign_vars( array(
        'DISPLAY_FILTER_ALL_CHECKED'=>'checked="checked"'
        )
     );
}

if (isset($_GET['del']))
{
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
  update_average_rate( $vars['e'] );
}

$users = array();
$query = '
SELECT '.$conf['user_fields']['username'].' as username, '.$conf['user_fields']['id'].' as id
  FROM '.USERS_TABLE.'
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  $users[$row['id']]=$row['username'];
}


$query = 'SELECT COUNT(DISTINCT(i.id)) 
FROM '.RATE_TABLE.' AS r, '.IMAGES_TABLE.' AS i
WHERE r.element_id=i.id'. $display_filter .
';';
list($nb_images) = mysql_fetch_row(pwg_query($query));


// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('rating'=>'admin/rating.tpl'));

$navbar = create_navigation_bar(
           PHPWG_ROOT_PATH.'admin.php'.get_query_string_diff(array('start','del')),
                                $nb_images,
                                $start,
                                $elements_per_page,
                                '');
$template->assign_vars(array('NAVBAR' => $navbar));


$template->assign_vars(
  array(
    'F_ACTION' => PHPWG_ROOT_PATH.'admin.php',
    'DISPLAY' => $elements_per_page,
    'NB_ELEMENTS' => $nb_images
    )
  );

$available_order_by= array(
    array(l10n('Rate date'), 'recently_rated DESC'),
    array(l10n('Average rate'), 'average_rate DESC'), 
    array(l10n('Number of rates'), 'nb_rates DESC'), 
    array(l10n('Sum of rates'), 'sum_rates DESC'), 
    array(l10n('Controversy'), 'std_rates DESC'), 
    array(l10n('File name'), 'file DESC'), 
    array(l10n('Creation date'), 'date_creation DESC'),
    array(l10n('Availability date'), 'date_available DESC'),

  );

for ($i=0; $i<count($available_order_by); $i++)
{
  $template->assign_block_vars(
    'order_by',
    array(
      'VALUE' => $i,
      'CONTENT' => $available_order_by[$i][0],
      'SELECTED' => $i==$order_by_index ? 'SELECTED' : ''
      )
    );
}

$query = 'SELECT i.id, i.path, i.file, i.tn_ext, i.average_rate, i.storage_category_id, 
          MAX(r.date) as recently_rated, COUNT(r.rate) as nb_rates, 
          SUM(r.rate) as sum_rates, ROUND(STD(r.rate),2) as std_rates 
FROM '.RATE_TABLE.' AS r LEFT JOIN '.IMAGES_TABLE.' AS i
ON r.element_id=i.id
WHERE 1=1 ' . $display_filter . '
GROUP BY r.element_id
ORDER BY ' . $available_order_by[$order_by_index][1] .'
LIMIT '.$start.','.$elements_per_page .
';';

$images = array();
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  array_push($images, $row);
}

foreach ($images as $image)
{
  $thumbnail_src = get_thumbnail_src(
    $image['path'], $image['tn_ext']
    );

  $image_url = PHPWG_ROOT_PATH.'picture.php?'.
                'cat=' . $image['storage_category_id'].
                '&amp;image_id=' . $image['id'];
    
  $query = 'SELECT *
FROM '.RATE_TABLE.' AS r
WHERE r.element_id='.$image['id'] . '
ORDER BY date DESC;';
  $result = pwg_query($query);
  $nb_rates = mysql_num_rows($result);

  $template->assign_block_vars('image', 
     array(
       'U_THUMB' => $thumbnail_src,
       'U_URL' => $image_url,
       'AVG_RATE' => $image['average_rate'],
       'STD_RATE' => $image['std_rates'],
       'SUM_RATE' => $image['sum_rates'],
       'NB_RATES' =>  $image['nb_rates'],
       'NB_RATES_TOTAL' =>  $nb_rates,
       'FILE' => $image['file'],
       'NB_RATES_PLUS1' => $nb_rates+1,
     )
   );

  while ($row = mysql_fetch_array($result))
  {

    $url_del = PHPWG_ROOT_PATH.'admin.php'.
                get_query_string_diff(array('del'));
    
    $del_param = 'e='.$image['id'].
                 '&u='.$row['user_id'].
                 '&a='.$row['anonymous_id'];
    
    $url_del .= '&amp;del='.urlencode(urlencode($del_param));

    if ( isset($users[$row['user_id']]) )
    {
      $user = $users[$row['user_id']];
    }
    else
    {
      $user = '? '. $row['user_id'];
    }
    if ( strlen($row['anonymous_id'])>0 )
    {
      $user .= '('.$row['anonymous_id'].')';
    }
    
    $template->assign_block_vars('image.rate', 
       array(
         'DATE' => format_date($row['date']),
         'RATE' => $row['rate'],
         'USER' => $user,
         'U_DELETE' => $url_del
       )
     );
  }
}
//print_r($template->_tpldata);
// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'rating');
?>