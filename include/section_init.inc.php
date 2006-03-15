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

// "index.php?/category/12-foo/start-24&action=fill_caddie" must return :
//
// array(
//   'section'  => 'categories',
//   'category' => 12,
//   'start'    => 24
//   'action'   => 'fill_caddie'
//   );

$page['section'] = 'categories';

foreach (array_keys($_GET) as $keynum => $key)
{
  if (0 == $keynum)
  {
    // deleting first "/" if displayed
    $tokens = explode(
      '/',
      preg_replace('#^/#', '', $key)
      );

    // $tokens = array(
    //   0 => category,
    //   1 => 12-foo,
    //   2 => start-24
    //   );

    $next_token = 0;

    if (basename($_SERVER['PHP_SELF']) == 'picture.php')
    {
      // the first token must be the numeric identifier of the picture
      preg_match('/(\d+)/', $tokens[$next_token], $matches);
      if (!isset($matches[1]))
      {
        die('Fatal: picture identifier is missing');
      }
      $page['image_id'] = $matches[1];
      
      $next_token++;
    }
    
    if (0 === strpos($tokens[$next_token], 'cat'))
    {
      $page['section'] = 'categories';
      $next_token++;
      
      if (isset($tokens[$next_token])
          and preg_match('/(\d+)/', $tokens[$next_token], $matches))
      {
        $page['category'] = $matches[1];
        $next_token++;
      }
    }
    else if (0 === strpos($tokens[$next_token], 'tag'))
    {
      $page['section'] = 'tags';
      $page['tags'] = array();
      
      $next_token++;
      
      for ($i = $next_token; ; $i++)
      {
        if (!isset($tokens[$i]))
        {
          break;
        }
        
        preg_match('/^(\d+)/', $tokens[$i], $matches);
        if (!isset($matches[1]))
        {
          if (0 == count($page['tags']))
          {
            die('Fatal: at least one tag required');
          }
          else
          {
            break;
          }
        }
        array_push($page['tags'], $matches[1]);
      }
      
      $next_token = $i;
    }
    else if (0 === strpos($tokens[$next_token], 'fav'))
    {
      $page['section'] = 'favorites';
      $next_token++;
    }
    else if ('most_visited' == $tokens[$next_token])
    {
      $page['section'] = 'most_visited';
      $next_token++;
    }
    else if ('best_rated' == $tokens[$next_token])
    {
      $page['section'] = 'best_rated';
      $next_token++;
    }
    else if ('recent_pics' == $tokens[$next_token])
    {
      $page['section'] = 'recent_pics';
      $next_token++;
    }
    else if ('recent_cats' == $tokens[$next_token])
    {
      $page['section'] = 'recent_cats';
      $next_token++;
    }
    else if ('search' == $tokens[$next_token])
    {
      $page['section'] = 'search';
      $next_token++;
      
      preg_match('/(\d+)/', $tokens[$next_token], $matches);
      if (!isset($matches[1]))
      {
        die('Fatal: search identifier is missing');
      }
      $page['search'] = $matches[1];
      $next_token++;
    }
    else if ('list' == $tokens[$next_token])
    {
      $page['section'] = 'list';
      $next_token++;

      $page['list'] = array();
      if (!preg_match('/^\d+(,\d+)*$/', $tokens[$next_token]))
      {
        die('wrong format on list GET parameter');
      }
      foreach (explode(',', $tokens[$next_token]) as $image_id)
      {
        array_push($page['list'], $image_id);
      }
      $next_token++;
    }
    else
    {
      $page['section'] = 'categories';
      $next_token++;
    }
    
    for ($i = $next_token; ; $i++)
    {
      if (!isset($tokens[$i]))
      {
        break;
      }
      
      if (preg_match('/^start-(\d+)/', $tokens[$i], $matches))
      {
        $page['start'] = $matches[1];
      }

      if (preg_match('/^calendar-(.+)$/', $tokens[$i], $matches))
      {
        // TODO: decide with rvelices how we name calendar/chronology is the
        // URL
        $_GET['calendar'] = $matches[1];
      }
    }
  }
}

// $page['nb_image_page'] is the number of picture to display on this page
// By default, it is the same as the $user['nb_image_page']
$page['nb_image_page'] = $user['nb_image_page'];

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
  $page['super_order_by'] = true;
}

// +-----------------------------------------------------------------------+
// |                              category                                 |
// +-----------------------------------------------------------------------+
if ('categories' == $page['section'])
{
  if (isset($page['category']))
  {
    $result = get_cat_info($page['category']);
    
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
        )
      );
    
    if (!isset($_GET['calendar']))
    {
      $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON id = image_id
  WHERE category_id = '.$page['category'].'
  '.$conf['order_by'].'
;';
      $page['items'] = array_from_query($query, 'image_id');
      
      $page['thumbnails_include'] =
        $result['nb_images'] > 0
        ? 'include/category_default.inc.php'
        : 'include/category_subcats.inc.php';
    } //otherwise the calendar will requery all subitems
  }
  else
  {
    $page['title'] = $lang['no_category'];
    $page['thumbnails_include'] = 'include/category_subcats.inc.php';
  }
}
// special sections
else
{
  if (!empty($user['forbidden_categories']))
  {
    $forbidden =
      ' category_id NOT IN ('.$user['forbidden_categories'].')';
  }
  else
  {
    $forbidden = ' 1 = 1';
  }
// +-----------------------------------------------------------------------+
// |                           search section                              |
// +-----------------------------------------------------------------------+
  if ($page['section'] == 'search')
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
  else if ($page['section'] == 'favorites')
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
  else if ($page['section'] == 'recent_pics')
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
  else if ($page['section'] == 'recent_cats')
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
  else if ($page['section'] == 'most_visited')
  {
    $page['super_order_by'] = true;
    $conf['order_by'] = ' ORDER BY hit DESC, file ASC';
    $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE hit > 0
    AND '.$forbidden.'
    '.$conf['order_by'].'
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
// |                          best rated section                           |
// +-----------------------------------------------------------------------+
  else if ($page['section'] == 'best_rated')
  {
    $page['super_order_by'] = true;
    $conf['order_by'] = ' ORDER BY average_rate DESC, id ASC';
    
    $query ='
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE average_rate IS NOT NULL
    AND '.$forbidden.'
    '.$conf['order_by'].'
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
  else if ($page['section'] == 'list')
  {
    $query ='
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE image_id IN ('.implode(',', $page['list']).')
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

// +-----------------------------------------------------------------------+
// |                             chronology                                |
// +-----------------------------------------------------------------------+

if (isset($_GET['calendar']))
{
  include_once( PHPWG_ROOT_PATH.'include/functions_calendar.inc.php' );
  initialize_calendar();
}

// echo '<pre>'; print_r($page); echo '</pre>';


?>