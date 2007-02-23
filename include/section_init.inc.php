<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
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

$page['items'] = array();

// some ISPs set PATH_INFO to empty string or to SCRIPT_FILENAME while in the
// default apache implementation it is not set
if ( $conf['question_mark_in_urls']==false and
     isset($_SERVER["PATH_INFO"]) and !empty($_SERVER["PATH_INFO"]) )
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
if (script_basename() == 'picture') // basename without file extention
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
        bad_request('picture identifier is missing');
      }
    }
  }
}

if (0 === strpos(@$tokens[$next_token], 'categor'))
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
else if (0 === strpos(@$tokens[$next_token], 'tag'))
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
      array_push($requested_tag_url_names, $tokens[$i]);
    }
    $i++;
  }
  $next_token = $i;

  if ( empty($requested_tag_ids) && empty($requested_tag_url_names) )
  {
    bad_request('at least one tag required');
  }

  $page['tags'] = find_tags($requested_tag_ids, $requested_tag_url_names);
  if ( empty($page['tags']) )
  {
    page_not_found('Requested tag does not exist', get_root_url().'tags.php' );
  }
}
else if (0 === strpos(@$tokens[$next_token], 'fav'))
{
  $page['section'] = 'favorites';
  $next_token++;
}
else if ('most_visited' == @$tokens[$next_token])
{
  $page['section'] = 'most_visited';
  $next_token++;
}
else if ('best_rated' == @$tokens[$next_token])
{
  $page['section'] = 'best_rated';
  $next_token++;
}
else if ('recent_pics' == @$tokens[$next_token])
{
  $page['section'] = 'recent_pics';
  $next_token++;
}
else if ('recent_cats' == @$tokens[$next_token])
{
  $page['section'] = 'recent_cats';
  $next_token++;
}
else if ('search' == @$tokens[$next_token])
{
  $page['section'] = 'search';
  $next_token++;

  preg_match('/(\d+)/', @$tokens[$next_token], $matches);
  if (!isset($matches[1]))
  {
    bad_request('search identifier is missing');
  }
  $page['search'] = $matches[1];
  $next_token++;
}
else if ('list' == @$tokens[$next_token])
{
  $page['section'] = 'list';
  $next_token++;

  $page['list'] = array();

  // No pictures
  if (empty($tokens[$next_token]))
  {
    // Add dummy element list
    array_push($page['list'], -1);
  }
  // With pictures list
  else
  {
    if (!preg_match('/^\d+(,\d+)*$/', $tokens[$next_token]))
    {
      bad_request('wrong format on list GET parameter');
    }
    foreach (explode(',', $tokens[$next_token]) as $image_id)
    {
      array_push($page['list'], $image_id);
    }
  }
  $next_token++;
}
else
{
  $page['section'] = 'categories';

  switch (script_basename())
  {
    case 'picture':
    {
      //access a picture only by id, file or id-file without given section
      $page['flat'] = true;
      break;
    }
    case 'index':
    {
      // No section defined, go to selected url
      if (!empty($conf['random_index_redirect']) and empty($tokens[$next_token]) )
      {
        $random_index_redirect = array();
        foreach ($conf['random_index_redirect'] as $random_url => $random_url_condition)
        {
          if (empty($random_url_condition) or eval($random_url_condition))
          {
            $random_index_redirect[] = $random_url;
          }
        }
        if (!empty($random_index_redirect))
        {
          redirect($random_index_redirect[mt_rand(0, count($random_index_redirect)-1)]);
        }
      }
      break;
    }
  }
}

$i = $next_token;

while (isset($tokens[$i]))
{
  if (preg_match('/^start-(\d+)/', $tokens[$i], $matches))
  {
    $page['start'] = $matches[1];
  }

  if ('categories' == $page['section'] and
      'flat' == $tokens[$i])
  {
    // indicate a special list of images
    $page['flat'] = true;
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

if (pwg_get_session_var('image_order',0) > 0)
{
  $orders = get_category_preferred_image_orders();

  $conf['order_by'] = str_replace(
    'ORDER BY ',
    'ORDER BY '.$orders[ pwg_get_session_var('image_order',0) ][1].',',
    $conf['order_by']
    );
  $page['super_order_by'] = true;
}

$forbidden = get_sql_condition_FandF(
      array
        (
          'forbidden_categories' => 'category_id',
          'visible_categories' => 'category_id',
          'visible_images' => 'id'
        ),
      'AND'
  );

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
        'comment'            => $result['comment'],
        'cat_dir'            => $result['dir'],
        'cat_name'           => $result['name'],
        'cat_site_id'        => $result['site_id'],
        'cat_uploadable'     => $result['uploadable'],
        'cat_commentable'    => $result['commentable'],
        'cat_id_uppercat'    => $result['id_uppercat'],
        'uppercats'          => $result['uppercats'],
        'title'             =>
          get_cat_display_name($result['name'], '', false),
        )
      );
  }
  else
  {
    $page['title'] = $lang['no_category'];
  }

  if
    (
      (!isset($page['chronology_field'])) and
      (
        (isset($page['category'])) or
        (isset($page['flat']))
      )
    )
  {
    if ( !empty($result['image_order']) and !isset($page['super_order_by']) )
    {
      $conf[ 'order_by' ] = ' ORDER BY '.$result['image_order'];
    }

    if (isset($page['flat']))
    {// flat categories mode
      if ( isset($page['category']) )
      {
        $subcat_ids = get_subcat_ids( array($page['category']) );
        $where_sql = 'category_id IN ('.implode(',',$subcat_ids).')';
      }
      else
      {
        $where_sql = '1=1';
      }
    }
    else
    {// Normal mode
      $where_sql = 'category_id = '.$page['category'];
    }

    // Main query
    $query = '
SELECT DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' ON id = image_id
  WHERE
    '.$where_sql.'
'.$forbidden.'
  '.$conf['order_by'].'
;';

    $page['items'] = array_from_query($query, 'image_id');
  } //otherwise the calendar will requery all subitems
}
// special sections
else
{
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
    '.$forbidden.
    $conf['order_by'].'
;';
      $items = array_unique(
        array_from_query($query, 'image_id')
        );
    }

    $title = get_tags_content_title();

    $page = array_merge(
      $page,
      array(
        'title' => $title,
        'items' => array_values($items),
        )
      );
  }
// +-----------------------------------------------------------------------+
// |                           search section                              |
// +-----------------------------------------------------------------------+
  if ($page['section'] == 'search')
  {
    include_once( PHPWG_ROOT_PATH .'include/functions_search.inc.php' );

    $search_result = get_search_results($page['search']);
    if ( !empty($search_result['items']) and !isset($search_result['as_is']) )
    {
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON id = ic.image_id
  WHERE id IN ('.implode(',', $search_result['items']).')
    '.$forbidden.'
  '.$conf['order_by'].'
;';
      $page['items'] = array_from_query($query, 'id');
    }
    else
    {
      $page['items'] = $search_result['items'];
    }

    $page = array_merge(
      $page,
      array(
        'title' => $lang['search_result'],
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
'.get_sql_condition_FandF
  (
    array
      (
        'visible_images' => 'image_id'
      ),
    'AND'
  ).'
  '.$conf['order_by'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => $lang['favorites'],
        'items' => array_from_query($query, 'image_id'),
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
    '.$forbidden.'
  '.$conf['order_by'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url().'">'
                  .$lang['recent_pics_cat'].'</a>',
        'items' => array_from_query($query, 'id'),
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
    '.$forbidden.'
    '.$conf['order_by'].'
  LIMIT 0, '.$conf['top_number'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url().'">'
                  .$conf['top_number'].' '.$lang['most_visited_cat'].'</a>',
        'items' => array_from_query($query, 'id'),
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
    '.$forbidden.'
    '.$conf['order_by'].'
  LIMIT 0, '.$conf['top_number'].'
;';
    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url().'">'
                  .$conf['top_number'].' '.$lang['best_rated_cat'].'</a>',
        'items' => array_from_query($query, 'id'),
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
    '.$forbidden.'
  '.$conf['order_by'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url().'">'
                    .$lang['random_cat'].'</a>',
        'items' => array_from_query($query, 'id'),
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

if (script_basename() == 'picture'
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

// add meta robots noindex, nofollow to avoid unnecesary robot crawls
$page['meta_robots']=array();
if ( isset($page['chronology_field']) or isset($page['flat'])
      or 'list'==$page['section'] or 'recent_pics'==$page['section'] )
{
  $page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
}
elseif ('tags' == $page['section'])
{
  if ( count($page['tag_ids'])>1 )
  {
    $page['meta_robots']=array('noindex'=>1, 'nofollow'=>1);
  }
}
elseif ('recent_cats'==$page['section'])
{
  $page['meta_robots']['nofollow']=1;
}
if ( $filter['enabled'] )
{
  $page['meta_robots']['noindex']=1;
}

trigger_action('loc_end_section_init');
?>