<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-01-27 02:11:43 +0100 (ven, 27 jan 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1014 $
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
 * This included page checks section related parameter and provides
 * following informations:
 *
 * - $page['title']
 *
 * - $page['items']: ordered list of items to display
 *
 * - $page['cat_nb_images']: number of items in the section (should be equal
 * to count($page['items']))
 *
 * - $page['thumbnails_include']: include page managing thumbnails to
 * display
 */

unset($page['cat']);

if (isset($_GET['cat']))
{
  if (is_numeric($_GET['cat']))
  {
    $page['cat'] = $_GET['cat'];
  }
  else if ($_GET['cat'] == 'fav'
           or $_GET['cat'] == 'most_visited'
           or $_GET['cat'] == 'best_rated'
           or $_GET['cat'] == 'recent_pics'
           or $_GET['cat'] == 'recent_cats'
           or $_GET['cat'] == 'calendar')
  {
    $page['cat'] = $_GET['cat'];
  }
  else if ($_GET['cat'] == 'search')
  {
    if (!isset($_GET['search']))
    {
      die('search GET parameter is missing');
    }
    else if (!is_numeric($_GET['search']))
    {
      die('wrong format on search GET parameter');
    }
    else
    {
      $page['cat'] = 'search';
    }
  }
  else if ($_GET['cat'] == 'list')
  {
    if (!isset($_GET['list']))
    {
      die('list GET parameter is missing');
    }
    else if (!preg_match('/^\d+(,\d+)*$/', $_GET['list']))
    {
      die('wrong format on list GET parameter');
    }
    else
    {
      $page['cat'] = 'list';
    }
  }
  else
  {
    die('unknown cat GET parameter value');
  }
}

// $page['nb_image_page'] is the number of picture to display on this page
// By default, it is the same as the $user['nb_image_page']
$page['nb_image_page'] = $user['nb_image_page'];

if (isset($page['cat']))
{
  if ($page['cat'] != 'most_visited' and $page['cat'] != 'best_rated')
  {
    if (isset($_COOKIE['pwg_image_order'])
        and is_numeric($_COOKIE['pwg_image_order'])
        and $_COOKIE['pwg_image_order'] > 0)
    {
      $orders = get_category_preferred_image_orders();

      $conf['order_by'] = str_replace(
        'ORDER BY ',
        'ORDER BY '.$orders[ $_COOKIE['pwg_image_order'] ][1].',', 
        $conf['order_by']
        );
    }
  }
  
// +-----------------------------------------------------------------------+
// |                              category                                 |
// +-----------------------------------------------------------------------+
  if (is_numeric($page['cat']))
  {
    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON id = image_id
  WHERE category_id = '.$page['cat'].'
  '.$conf['order_by'].'
;';

    $result = get_cat_info($page['cat']);
    
    $page = array_merge(
      $page,
      array(
        'comment'          => $result['comment'],
        'cat_dir'          => $result['dir'],
        'cat_name'         => $result['name'],
        'cat_nb_images'    => $result['nb_images'],
        'cat_site_id'      => $result['site_id'],
        'cat_uploadable'   => $result['uploadable'],
        'cat_commentable'  => $result['commentable'],
        'cat_id_uppercat'  => $result['id_uppercat'],
        'uppercats'        => $result['uppercats'],
                
        'title' => get_cat_display_name($result['name'], '', false),
        'items' => array_from_query($query, 'image_id'),
        'thumbnails_include' =>
          $result['nb_images'] > 0
          ? 'include/category_default.inc.php'
          : 'include/category_subcats.inc.php',
        )
      );
  }
  // special section
  else
  {
    if (!empty($user['forbidden_categories']))
    {
      $forbidden =
        ' category_id NOT IN ('.$user['forbidden_categories'].')';
    }
    else
    {
      $forbidden = ' 1=1';
    }
          
// +-----------------------------------------------------------------------+
// |                           search section                              |
// +-----------------------------------------------------------------------+
    if ( $page['cat'] == 'search' )
    {
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE '.get_sql_search_clause($_GET['search']).'
    AND '.$forbidden.'
  '.$conf['order_by'].'
;';

      $page = array_merge(
        $page,
        array(
          'title' => $lang['search_result'],
          'items' => array_from_query($query, 'id'),
          'thumbnails_include' => 'include/category_default.inc.php',
          )
        );
    }
// +-----------------------------------------------------------------------+
// |                           favorite section                            |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'fav')
    {
      check_user_favorites();

      $query = '
SELECT image_id
  FROM '.FAVORITES_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON image_id = id
  WHERE user_id = '.$user['id'].'
  '.$conf['order_by'].'
;';

      $page = array_merge(
        $page,
        array(
          'title' => $lang['favorites'],
          'items' => array_from_query($query, 'image_id'),
          'thumbnails_include' => 'include/category_default.inc.php',
          )
        );
    }
// +-----------------------------------------------------------------------+
// |                       recent pictures section                         |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'recent_pics')
    {
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE date_available > \''.
        date('Y-m-d', time() - 60*60*24*$user['recent_period']).'\'
    AND '.$forbidden.'
  '.$conf['order_by'].'
;';

      $page = array_merge(
        $page,
        array(
          'title' => $lang['recent_pics_cat'],
          'items' => array_from_query($query, 'id'),
          'thumbnails_include' => 'include/category_default.inc.php',
          )
        );
    }
// +-----------------------------------------------------------------------+
// |                 recently updated categories section                   |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'recent_cats')
    {
      $page = array_merge(
        $page,
        array(
          'title' => $lang['recent_cats_cat'],
          'cat_nb_images' => 0,
          'thumbnails_include' => 'include/category_recent_cats.inc.php',
          )
        );
    }
// +-----------------------------------------------------------------------+
// |                        most visited section                           |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'most_visited')
    {
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE hit > 0
    AND '.$forbidden.'
  ORDER BY hit DESC, file ASC
  LIMIT 0, '.$conf['top_number'].'
;';

      $page = array_merge(
        $page,
        array(
          'title' => $conf['top_number'].' '.$lang['most_visited_cat'],
          'items' => array_from_query($query, 'id'),
          'thumbnails_include' => 'include/category_default.inc.php',
          )
        );
    }
// +-----------------------------------------------------------------------+
// |                           calendar section                            |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'calendar')
    {
      $page['cat_nb_images'] = 0;
      $page['title'] = $lang['calendar'];
      if (isset($_GET['year'])
          and preg_match('/^\d+$/', $_GET['year']))
      {
        $page['calendar_year'] = (int)$_GET['year'];
      }
      if (isset($_GET['month'])
          and preg_match('/^(\d+)\.(\d{2})$/', $_GET['month'], $matches))
      {
        $page['calendar_year'] = (int)$matches[1];
        $page['calendar_month'] = (int)$matches[2];
      }
      if (isset($_GET['day'])
          and preg_match('/^(\d+)\.(\d{2})\.(\d{2})$/',
                         $_GET['day'],
                         $matches))
      {
        $page['calendar_year'] = (int)$matches[1];
        $page['calendar_month'] = (int)$matches[2];
        $page['calendar_day'] = (int)$matches[3];
      }
      if (isset($page['calendar_year']))
      {
        $page['title'] .= ' (';
        if (isset($page['calendar_day']))
        {
          if ($page['calendar_year'] >= 1970)
          {
            $unixdate = mktime(
              0,
              0,
              0,
              $page['calendar_month'],
              $page['calendar_day'],
              $page['calendar_year']
              );
            $page['title'].= $lang['day'][date("w", $unixdate)];
          }
          $page['title'].= ' '.$page['calendar_day'].', ';
        }
        if (isset($page['calendar_month']))
        {
          $page['title'] .= $lang['month'][$page['calendar_month']].' ';
        }
        $page['title'] .= $page['calendar_year'];
        $page['title'] .= ')';
      }
        
      $page['where'] = 'WHERE '.$conf['calendar_datefield'].' IS NOT NULL';
      if (isset($forbidden))
      {
        $page['where'].= ' AND '.$forbidden;
      }

      $page['thumbnails_include'] = 'include/category_calendar.inc.php';
    }
// +-----------------------------------------------------------------------+
// |                          best rated section                           |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'best_rated')
    {
      $query ='
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE average_rate IS NOT NULL
    AND '.$forbidden.'
  ORDER BY average_rate DESC, id ASC
  LIMIT 0, '.$conf['top_number'].'
;';
      $page = array_merge(
        $page,
        array(
          'title' => $conf['top_number'].' '.$lang['best_rated_cat'],
          'items' => array_from_query($query, 'id'),
          'thumbnails_include' => 'include/category_default.inc.php',
          )
        );
    }
// +-----------------------------------------------------------------------+
// |                             list section                              |
// +-----------------------------------------------------------------------+
    else if ($page['cat'] == 'list')
    {
      $query ='
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE image_id IN ('.$_GET['list'].')
    AND '.$forbidden.'
  '.$conf['order_by'].'
;';
      $page = array_merge(
        $page,
        array(
          'title' => $lang['random_cat'],
          'items' => array_from_query($query, 'id'),
          'thumbnails_include' => 'include/category_default.inc.php',
          )
        );
    }

    if (!isset($page['cat_nb_images']))
    {
      $page['cat_nb_images'] = count($page['items']);
    }
  }
}
// +-----------------------------------------------------------------------+
// |                            root category                              |
// +-----------------------------------------------------------------------+
else
{
  $page['title'] = $lang['no_category'];
  $page['thumbnails_include'] = 'include/category_subcats.inc.php';
}

if ( isset($_GET['calendar']) )
{
  include_once( PHPWG_ROOT_PATH.'include/functions_calendar.inc.php' );
  initialize_calendar();
}

?>