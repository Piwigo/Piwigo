<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

// "index.php?/category/12-foo/start-24&action=fill_caddie" or
// "index.php/category/12-foo/start-24&action=fill_caddie"
// must return :
//
// array(
//   'section'  => 'categories',
//   'category' => 12,
//   'start'    => 24
//   'action'   => 'fill_caddie'
//   );

$page['section'] = 'categories';

if ( isset($_SERVER["PATH_INFO"]) and !empty($_SERVER["PATH_INFO"]) )
{
  $rewritten = $_SERVER["PATH_INFO"];
  $rewritten = str_replace('//', '/', $rewritten);
  $path_count = count( explode('/', $rewritten) );
  $page['root_path'] = PHPWG_ROOT_PATH.str_repeat('../', $path_count-1);
}
else
{
  $rewritten = '';
  foreach (array_keys($_GET) as $keynum => $key)
  {
    $rewritten = $key;
    break;
  }
  $page['root_path'] = PHPWG_ROOT_PATH;
}

// deleting first "/" if displayed
$tokens = explode(
  '/',
  preg_replace('#^/#', '', $rewritten)
  );
// $tokens = array(
//   0 => category,
//   1 => 12-foo,
//   2 => start-24
//   );

$next_token = 0;
if (basename($_SERVER['SCRIPT_FILENAME']) == 'picture.php')
{ // the first token must be the identifier for the picture
  if ( isset($_GET['image_id'])
       and isset($_GET['cat']) and is_numeric($_GET['cat']) )
  {// url compatibility with versions below 1.6
    $url = make_picture_url( array(
        'section' => 'categories',
        'category' => $_GET['cat'],
        'image_id' => $_GET['image_id']
      ) );
    redirect($url);
  }
  $token = $tokens[$next_token];
  $next_token++;
  if ( is_numeric($token) )
  {
    $page['image_id'] = $token;
  }
  else
  {
    preg_match('/^(\d+-)?(.*)?$/', $token, $matches);
    if (isset($matches[1]) and is_numeric($matches[1]=rtrim($matches[1],'-')) )
    {
      $page['image_id'] = $matches[1];
      if ( !empty($matches[2]) )
      {
        $page['image_file'] = $matches[2];
      }

    }
    else
    {
      if ( !empty($matches[2]) )
      {
        $page['image_file'] = $matches[2];
      }
      else
      {
        die('Fatal: picture identifier is missing');
      }
    }
  }
}

if (0 === strpos($tokens[$next_token], 'categor'))
{
  $page['section'] = 'categories';
  $next_token++;

  if (isset($tokens[$next_token])
      and preg_match('/^(\d+)/', $tokens[$next_token], $matches))
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
  $i = $next_token;

  $requested_tag_ids = array();
  $requested_tag_url_names = array();

  while (isset($tokens[$i]))
  {
    if ( preg_match('/^(created-|posted-|start-(\d)+)/', $tokens[$i]) )
      break;

    if ( preg_match('/^(\d+)(?:-(.*))?/', $tokens[$i], $matches) )
    {
      array_push($requested_tag_ids, $matches[1]);
    }
    else
    {
      array_push($requested_tag_url_names, "'".$tokens[$i]."'");
    }
    $i++;
  }
  $next_token = $i;

  if ( empty($requested_tag_ids) && empty($requested_tag_url_names) )
  {
    die('Fatal: at least one tag required');
  }
  // tag infos
  $query = '
SELECT name, url_name, id
  FROM '.TAGS_TABLE.'
  WHERE ';
  if ( !empty($requested_tag_ids) )
  {
    $query.= 'id IN ('.implode(',', $requested_tag_ids ).')';
  }
  if ( !empty($requested_tag_url_names) )
  {
    if ( !empty($requested_tag_ids) )
    {
      $query.= ' OR ';
    }
    $query.= 'url_name IN ('.implode(',', $requested_tag_url_names ).')';
  }
  $result = pwg_query($query);
  $tag_infos = array();
  while ($row = mysql_fetch_array($result))
  {
    $tag_infos[ $row['id'] ] = $row;
    array_push($page['tags'], $row );//we loose given tag order; is it important?
  }
  if ( empty($page['tags']) )
  {
    page_not_found('Requested tag does not exist', get_root_url().'tags.php' );
  }
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

$i = $next_token;

while (isset($tokens[$i]))
{
  if (preg_match('/^start-(\d+)/', $tokens[$i], $matches))
  {
    $page['start'] = $matches[1];
  }

  if (preg_match('/^(posted|created)/', $tokens[$i] ))
  {
    $chronology_tokens = explode('-', $tokens[$i] );

    $page['chronology_field'] = $chronology_tokens[0];

    array_shift($chronology_tokens);
    $page['chronology_style'] = $chronology_tokens[0];

    array_shift($chronology_tokens);
    if ( count($chronology_tokens)>0 )
    {
      if ('list'==$chronology_tokens[0] or
          'calendar'==$chronology_tokens[0])
      {
        $page['chronology_view'] = $chronology_tokens[0];
        array_shift($chronology_tokens);
      }
      $page['chronology_date'] = $chronology_tokens;
    }
  }

  $i++;
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
    if (empty($result))
    {
      page_not_found('Requested category does not exist' );
    }

    $page = array_merge(
      $page,
      array(
        'comment'          => $result['comment'],
        'cat_dir'          => $result['dir'],
        'cat_name'         => $result['name'],
        'cat_site_id'      => $result['site_id'],
        'cat_uploadable'   => $result['uploadable'],
        'cat_commentable'  => $result['commentable'],
        'cat_id_uppercat'  => $result['id_uppercat'],
        'uppercats'        => $result['uppercats'],

        'title' => get_cat_display_name($result['name'], '', false),
        )
      );

    if (!isset($page['chronology_field']))
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
// |                            tags section                               |
// +-----------------------------------------------------------------------+
  if ($page['section'] == 'tags')
  {
    $page['tag_ids'] = array();
    foreach ($page['tags'] as $tag)
    {
      array_push($page['tag_ids'], $tag['id']);
    }

    $items = get_image_ids_for_tags($page['tag_ids']);

    // permissions depends on category, so to only keep images that are
    // reachable to the connected user, we need to check category
    // associations
    if (!empty($items) )
    {
      $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.' INNER JOIN '.IMAGES_TABLE.' ON image_id=id
  WHERE image_id IN ('.implode(',', $items).')
    AND '.$forbidden.
    $conf['order_by'].'
;';
      $items = array_unique(
        array_from_query($query, 'image_id')
        );
    }

    $title = count($page['tags']) > 1 ? l10n('Tags') : l10n('Tag');
    $title.= ' ';

    $tag_num = 1;
    foreach ($page['tag_ids'] as $tag_id)
    {
      $title.=
        ($tag_num++ > 1 ? ' + ' : '')
        .'<a href="'
        .make_index_url(
          array(
            'tags' => array(
              array(
                'id' => $tag_id,
                'url_name' => $tag_infos[$tag_id]['url_name'],
                ),
              )
            )
          )
        .'">'
        .$tag_infos[$tag_id]['name']
        .'</a>';
    }

    $page = array_merge(
      $page,
      array(
        'title' => $title,
        'items' => array_values($items),
        'thumbnails_include' => 'include/category_default.inc.php',
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                           search section                              |
// +-----------------------------------------------------------------------+
  if ($page['section'] == 'search')
  {
    include_once( PHPWG_ROOT_PATH .'include/functions_search.inc.php' );

    $search_items = get_search_items($page['search']);
    if ( !empty($search_items) )
    {
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE id IN ('.implode(',', $search_items).')
    AND '.$forbidden.'
  '.$conf['order_by'].'
;';
      $page['items'] = array_from_query($query, 'id');
    }
    else
    {
      $page['items'] = array();
    }

    $page = array_merge(
      $page,
      array(
        'title' => $lang['search_result'],
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
}

// +-----------------------------------------------------------------------+
// |                             chronology                                |
// +-----------------------------------------------------------------------+

if (isset($page['chronology_field']))
{
  include_once( PHPWG_ROOT_PATH.'include/functions_calendar.inc.php' );
  initialize_calendar();
}

$page['cat_nb_images'] = isset($page['items']) ? count($page['items']) : 0;

if (basename($_SERVER['SCRIPT_FILENAME']) == 'picture.php'
    and !isset($page['image_id']) )
{
  if ( !empty($page['items']) )
  {
    $query = '
SELECT id,file
  FROM '.IMAGES_TABLE .'
  WHERE id IN ('.implode(',',$page['items']).')
  AND file LIKE "' . $page['image_file'] . '.%" ESCAPE "|"'
;
    $result = pwg_query($query);
    if (mysql_num_rows($result)>0)
    {
      list($page['image_id'], $page['image_file']) = mysql_fetch_row($result);
    }
  }
  if ( !isset($page['image_id']) )
  {
    $page['image_id'] = -1; // will fail in picture.php
  }
}
?>