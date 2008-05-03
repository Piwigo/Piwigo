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

// "index.php?/category/12-foo/start-24" or
// "index.php/category/12-foo/start-24"
// must return :
//
// array(
//   'section'  => 'categories',
//   'category' => array('id'=>12, ...),
//   'start'    => 24
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
        'category' => get_cat_info($_GET['cat']),
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

$page = array_merge( $page, parse_section_url( $tokens, $next_token) );
if ( !isset($page['section']) )
{
  $page['section'] = 'categories';

  switch (script_basename())
  {
    case 'picture':
      break;
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
    default:
      trigger_error('script_basename "'.script_basename().'" unknown',
        E_USER_WARNING);
  }
}


$page = array_merge( $page, parse_well_known_params_url( $tokens, $next_token) );


if ( script_basename()=='picture' and 'categories'==$page['section'] and
      !isset($page['category']) and !isset($page['chronology_field']) )
{ //access a picture only by id, file or id-file without given section
  $page['flat']=true;
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
    $page = array_merge(
      $page,
      array(
        'comment'           =>
            trigger_event(
              'render_category_description',
              $page['category']['comment'],
              'main_page_category_description'
            ),
        'title'             =>
          get_cat_display_name($page['category']['upper_names'], '', false),
        )
      );
  }
  else
  {
    $page['title'] = l10n('no_category');
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
    if ( !empty($page['category']['image_order']) and !isset($page['super_order_by']) )
    {
      $conf[ 'order_by' ] = ' ORDER BY '.$page['category']['image_order'];
    }

    if (isset($page['flat']))
    {// flat categories mode
      if ( isset($page['category']) )
      { // get all allowed sub-categories
        $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE
    uppercats LIKE "'.$page['category']['uppercats'].',%" '
    .get_sql_condition_FandF(
      array
        (
          'forbidden_categories' => 'id',
          'visible_categories' => 'id',
        ),
      "\n  AND"
          );
        $subcat_ids = array_from_query($query, 'id');
        $subcat_ids[] = $page['category']['id'];
        $where_sql = 'category_id IN ('.implode(',',$subcat_ids).')';
        // remove categories from forbidden because just checked above
        $forbidden = get_sql_condition_FandF(
              array( 'visible_images' => 'id' ),
              'AND'
          );
      }
      else
      {
        $where_sql = '1=1';
      }
    }
    else
    {// Normal mode
      $where_sql = 'category_id = '.$page['category']['id'];
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
SELECT DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.' INNER JOIN '.IMAGES_TABLE.' ON image_id=id
  WHERE image_id IN ('.implode(',', $items).')
    '.$forbidden.
    $conf['order_by'].'
;';
      $items = array_from_query($query, 'image_id');
    }

    $title = get_tags_content_title();

    $page = array_merge(
      $page,
      array(
        'title' => $title,
        'items' => $items,
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
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                  .l10n('search_result').'</a>',
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
        'title' => l10n('favorites'),
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
  WHERE
    date_available >= SUBDATE(
      CURRENT_DATE,INTERVAL '.$user['recent_period'].' DAY)
    '.$forbidden.'
  '.$conf['order_by'].'
;';

    $page = array_merge(
      $page,
      array(
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                  .l10n('recent_pics_cat').'</a>',
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
        'title' => l10n('recent_cats_cat'),
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
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                  .$conf['top_number'].' '.l10n('most_visited_cat').'</a>',
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
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                  .$conf['top_number'].' '.l10n('best_rated_cat').'</a>',
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
        'title' => '<a href="'.duplicate_index_url(array('start'=>0)).'">'
                    .l10n('random_cat').'</a>',
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
  WHERE file LIKE "' . $page['image_file'] . '.%" ESCAPE "|"';
    if ( count($page['items']) < 500)
    {// for very large item sets do not add IN - because slow
      $query .= '
  AND id IN ('.implode(',',$page['items']).')
  LIMIT 0,1';
    }
    $result = pwg_query($query);
    switch (mysql_num_rows($result))
    {
      case 0: break;
      case 1:
        list($page['image_id'], $page['image_file']) = mysql_fetch_row($result);
        break;
      default: // more than 1 file name match
        while ($row = mysql_fetch_row($result) )
        {
          if ( in_array($row[0], $page['items']) )
          {
            list($page['image_id'], $page['image_file']) = $row;
            break;
          }
        }
    }
  }
  if ( !isset($page['image_id']) )
  {
    $page['image_id'] = -1; // will fail in picture.php
  }
}

// add meta robots noindex, nofollow to avoid unnecesary robot crawls
$page['meta_robots']=array();
if ( isset($page['chronology_field'])
      or ( isset($page['flat']) and isset($page['category']) )
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
  $page['meta_robots']['noindex']=1;
}
elseif ('search'==$page['section'])
{
  $page['meta_robots']['nofollow']=1;
}

if ( $filter['enabled'] )
{
  $page['meta_robots']['noindex']=1;
}

// see if we need a redirect because of a permalink
if ( 'categories'==$page['section'] and isset($page['category']) )
{
  $need_redirect=false;
  if ( empty($page['category']['permalink']) )
  {
    if ( $conf['category_url_style'] == 'id-name' and
        @$page['hit_by']['cat_url_name'] !== str2url($page['category']['name']) )
    {
      $need_redirect=true;
    }
  }
  else
  {
    if ( $page['category']['permalink'] !== @$page['hit_by']['cat_permalink'] )
    {
      $need_redirect=true;
    }
  }

  if ($need_redirect)
  {
    $redirect_url = ( script_basename()=='picture'
        ? duplicate_picture_url()
          : duplicate_index_url()
      );
    if (!headers_sent())
    { // this is a permanent redirection
      set_status_header(301);
      redirect_http( $redirect_url );
    }
    redirect( $redirect_url );
  }
  unset( $need_redirect, $page['hit_by'] );
}

trigger_action('loc_end_section_init');
?>