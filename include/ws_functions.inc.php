<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Rev$
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

/**** IMPLEMENTATION OF WEB SERVICE METHODS ***********************************/

/**
 * returns a "standard" (for our web service) array of sql where clauses that
 * filters the images (images table only)
 */
function ws_std_image_sql_filter( $params, $tbl_name='' )
{
  $clauses = array();
  if ( is_numeric($params['f_min_rate']) )
  {
    $clauses[] = $tbl_name.'average_rate>'.$params['f_min_rate'];
  }
  if ( is_numeric($params['f_max_rate']) )
  {
    $clauses[] = $tbl_name.'average_rate<='.$params['f_max_rate'];
  }
  if ( is_numeric($params['f_min_hit']) )
  {
    $clauses[] = $tbl_name.'hit>'.$params['f_min_hit'];
  }
  if ( is_numeric($params['f_max_hit']) )
  {
    $clauses[] = $tbl_name.'hit<='.$params['f_max_hit'];
  }
  if ( isset($params['f_min_date_posted']) )
  {
    $clauses[] = $tbl_name."date_available>='".$params['f_min_date_posted']."'";
  }
  if ( isset($params['f_max_date_posted']) )
  {
    $clauses[] = $tbl_name."date_available<'".$params['f_max_date_posted']."'";
  }
  if ( isset($params['f_min_date_created']) )
  {
    $clauses[] = $tbl_name."date_creation>='".$params['f_min_date_created']."'";
  }
  if ( isset($params['f_max_date_created']) )
  {
    $clauses[] = $tbl_name."date_creation<'".$params['f_max_date_created']."'";
  }
  if ( is_numeric($params['f_min_ratio']) )
  {
    $clauses[] = $tbl_name.'width/'.$tbl_name.'height>'.$params['f_min_ratio'];
  }
  if ( is_numeric($params['f_max_ratio']) )
  {
    $clauses[] = $tbl_name.'width/'.$tbl_name.'height<='.$params['f_max_ratio'];
  }
  if ( $params['f_with_thumbnail'] )
  {
    $clauses[] = $tbl_name.'tn_ext IS NOT NULL';
  }
  return $clauses;
}

/**
 * returns a "standard" (for our web service) ORDER BY sql clause for images
 */
function ws_std_image_sql_order( $params, $tbl_name='' )
{
  $ret = '';
  if ( empty($params['order']) )
  {
    return $ret;
  }
  $matches = array();
  preg_match_all('/([a-z_]+) *(?:(asc|desc)(?:ending)?)? *(?:, *|$)/i',
    $params['order'], $matches);
  for ($i=0; $i<count($matches[1]); $i++)
  {
    switch ($matches[1][$i])
    {
      case 'date_created':
        $matches[1][$i] = 'date_creation'; break;
      case 'date_posted':
        $matches[1][$i] = 'date_available'; break;
      case 'rand': case 'random':
        $matches[1][$i] = 'RAND()'; break;
    }
    $sortable_fields = array('id', 'file', 'name', 'hit', 'average_rate',
      'date_creation', 'date_available', 'RAND()' );
    if ( in_array($matches[1][$i], $sortable_fields) )
    {
      if (!empty($ret))
        $ret .= ', ';
      if ($matches[1][$i] != 'RAND()' )
      {
        $ret .= $tbl_name;
      }
      $ret .= $matches[1][$i];
      $ret .= ' '.$matches[2][$i];
    }
  }
  return $ret;
}

/**
 * returns an array map of urls (thumb/element) for image_row - to be returned
 * in a standard way by different web service methods
 */
function ws_std_get_urls($image_row)
{
  $ret = array(
    'tn_url' => get_thumbnail_url($image_row),
    'element_url' => get_element_url($image_row)
  );
  global $user;
  if ($user['enabled_high'] and $image_row['has_high'] )
  {
    $ret['high_url'] = get_high_url($image_row);
  }
  return $ret;
}


function ws_getVersion($params, &$service)
{
  return PHPWG_VERSION;
}

/**
 * returns images per category (wb service method)
 */
function ws_categories_getImages($params, &$service)
{
  @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
  global $user, $conf;

  $images = array();

  //------------------------------------------------- get the related categories
  $where_clauses = array();
  foreach($params['cat_id'] as $cat_id)
  {
    $cat_id = (int)$cat_id;
    if ($cat_id<=0)
      continue;
    if ($params['recursive'])
    {
      $where_clauses[] = 'uppercats REGEXP \'(^|,)'.$cat_id.'(,|$)\'';
    }
    else
    {
      $where_clauses[] = 'id='.$cat_id;
    }
  }
  if (!empty($where_clauses))
  {
    $where_clauses = array( '('.
    implode('
    OR ', $where_clauses) . ')'
      );
  }
  $where_clauses[] = 'id NOT IN ('.$user['forbidden_categories'].')';

  $query = '
SELECT id, name, image_order
  FROM '.CATEGORIES_TABLE.'
  WHERE '. implode('
    AND ', $where_clauses);
  $result = pwg_query($query);
  $cats = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $row['id'] = (int)$row['id'];
    $cats[ $row['id'] ] = $row;
  }

  //-------------------------------------------------------- get the images
  if ( !empty($cats) )
  {
    $where_clauses = ws_std_image_sql_filter( $params, 'i.' );
    $where_clauses[] = 'category_id IN ('
      .implode(',', array_keys($cats) )
      .')';
    $order_by = ws_std_image_sql_order($params, 'i.');
    if (empty($order_by))
    {// TODO check for category order by (image_order)
      $order_by = $conf['order_by'];
    }
    else
    {
      $order_by = 'ORDER BY '.$order_by;
    }
    $query = '
SELECT i.*, GROUP_CONCAT(category_id) cat_ids
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON i.id=image_id
  WHERE '. implode('
    AND ', $where_clauses).'
GROUP BY i.id
'.$order_by.'
LIMIT '.$params['per_page']*$params['page'].','.$params['per_page'];

    $result = pwg_query($query);
    while ($row = mysql_fetch_assoc($result))
    {
      $image = array();
      foreach ( array('id', 'width', 'height', 'hit') as $k )
      {
        if (isset($row[$k]))
        {
          $image[$k] = (int)$row[$k];
        }
      }
      foreach ( array('name', 'file') as $k )
      {
        $image[$k] = $row[$k];
      }
      $image = array_merge( $image, ws_std_get_urls($row) );

      $image_cats = array();
      foreach ( explode(',', $row['cat_ids']) as $cat_id )
      {
        $url = make_index_url(
                array(
                  'category' => $cat_id,
                  'cat_name' => $cats[$cat_id]['name'],
                  )
                );
        $page_url = make_picture_url(
                array(
                  'category' => $cat_id,
                  'cat_name' => $cats[$cat_id]['name'],
                  'image_id' => $row['id'],
                  'image_file' => $row['file'],
                  )
                );
        array_push( $image_cats,  array(
              WS_XML_ATTRIBUTES => array (
                  'id' => (int)$cat_id,
                  'url' => $url,
                  'page_url' => $page_url,
                )
            )
          );
      }

      $image['categories'] = new PwgNamedArray(
            $image_cats,'category', array('id','url','page_url')
          );
      array_push($images, $image);
    }
  }

  return array( 'images' =>
    array (
      WS_XML_ATTRIBUTES =>
        array(
            'page' => $params['page'],
            'per_page' => $params['per_page'],
            'count' => count($images)
          ),
       WS_XML_CONTENT => new PwgNamedArray($images, 'image',
          array('id', 'tn_url', 'element_url', 'file','width','height','hit') )
      )
    );
}

/**
 * returns a list of categories
 */
function ws_categories_getList($params, &$service)
{
  global $user;

  $where = array();
  $where[]= 'user_id='.$user['id'];
  if ($params['cat_id']>0)
  {
    $where[] = 'uppercats REGEXP \'(^|,)'.
      (int)($params['cat_id'])
      .'(,|$)\'';
  }

  if (!$params['recursive'])
  {
    if ($params['cat_id']>0)
      $where[] = 'id_uppercat='.(int)($params['cat_id']);
    else
      $where[] = 'id_uppercat IS NULL';
  }

  if ($params['public'])
  {
    $where[] = 'status = "public"';
    $where[] = 'visible = "true"';
  }
  else
  {
    $where[] = 'id NOT IN ('.$user['forbidden_categories'].')';
  }

  $query = '
SELECT id, name, uppercats, global_rank,
    max_date_last, count_images AS nb_images, count_categories AS nb_categories
  FROM '.CATEGORIES_TABLE.'
   INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id=cat_id
  WHERE '. implode('
    AND ', $where);
  $query .= '
ORDER BY global_rank';

  $result = pwg_query($query);

  $cats = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $row['url'] = make_index_url(
        array(
          'category' => $row['id'],
          'cat_name' => $row['name'],
          )
      );
    foreach( array('id','nb_images','nb_categories') as $key)
    {
      $row[$key] = (int)$row[$key];
    }
    array_push($cats, $row);
  }
  usort($cats, 'global_rank_compare');
  return array(
      'categories' =>
          new PwgNamedArray($cats,'category',
            array('id','url','nb_images','nb_categories','max_date_last')
          )
    );
}

function ws_images_getInfo($params, &$service)
{
  @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
  global $user;
  $params['image_id'] = (int)$params['image_id'];
  if ( $params['image_id']<=0 )
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }
  $query='
SELECT * FROM '.IMAGES_TABLE.'
  WHERE id='.$params['image_id'].
    get_sql_condition_FandF(
      array('visible_images' => 'id'),
      ' AND'
    ).'
LIMIT 1';

  $image_row = mysql_fetch_assoc(pwg_query($query));
  if ($image_row==null)
  {
    return new PwgError(999, "image_id not found");
  }
  array_merge( $image_row, ws_std_get_urls($image_row) );

  //-------------------------------------------------------- related categories
  $query = '
SELECT c.id,c.name,c.uppercats,c.global_rank
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.CATEGORIES_TABLE.' c ON category_id = id
  WHERE image_id = '.$image_row['id'].'
    AND category_id NOT IN ('.$user['forbidden_categories'].')
;';
  $result = pwg_query($query);
  $related_categories = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $row['url'] = make_index_url(
        array(
          'category' => $row['id'],
          'cat_name' => $row['name'],
          )
      );

    $row['page_url'] = make_picture_url(
        array(
          'image_id' => $image_row['id'],
          'image_file' => $image_row['file'],
          'category' => $row['id'],
          'cat_name' => $row['name'],
          )
      );
    array_push($related_categories, $row);
  }
  usort($related_categories, 'global_rank_compare');
  if ( empty($related_categories) )
  {
    return new PwgError(401, 'Access denied');
  }

  //-------------------------------------------------------------- related tags
  $query = '
SELECT id, name, url_name
  FROM '.IMAGE_TAG_TABLE.'
    INNER JOIN '.TAGS_TABLE.' ON tag_id = id
  WHERE image_id = '.$image_row['id'].'
;';
  $result = pwg_query($query);
  $related_tags = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $row['url'] = make_index_url(
        array(
          'tags' => array($row)
          )
      );
    $row['page_url'] = make_picture_url(
        array(
          'image_id' => $image_row['id'],
          'image_file' => $image_row['file'],
          'tags' => array($row),
          )
      );
    array_push($related_tags, $row);
  }
  //---------------------------------------------------------- related comments
  $query = '
SELECT COUNT(id) nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$image_row['id'];
  list($nb_comments) = array_from_query($query, 'nb_comments');

  $query = '
SELECT id, date, author, content
  FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$image_row['id'].'
    AND validated="true"';
  $query .= '
  ORDER BY date DESC
  LIMIT 0, 5';

  $result = pwg_query($query);
  $related_comments = array();
  while ($row = mysql_fetch_assoc($result))
  {
    array_push($related_comments, $row);
  }

  //------------------------------------------------------------- related rates
  $query = '
SELECT COUNT(rate) AS count
     , ROUND(AVG(rate),2) AS average
     , ROUND(STD(rate),2) AS stdev
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_row['id'].'
;';
  $row = mysql_fetch_assoc(pwg_query($query));

  $ret = $image_row;
  $ret['rates'] = array( WS_XML_ATTRIBUTES => $row );
  $ret['categories'] = new PwgNamedArray($related_categories, 'category', array('id','url', 'page_url') );
  $ret['tags'] = new PwgNamedArray($related_tags, 'tag', array('id','url_name','url','page_url') );
  $ret['comments'] = array(
     WS_XML_ATTRIBUTES => array('nb_comments' => $nb_comments),
     WS_XML_CONTENT => new PwgNamedArray($related_comments, 'comment', array('id') )
      );
  unset($ret['path']);
  unset($ret['storage_category_id']);
  return new PwgNamedStruct('image',$ret, null, array('name','comment') );
}


function ws_session_login($params, &$service)
{
  global $conf;

  if (!$service->isPost())
  {
    return new PwgError(400, "This method requires POST");
  }

  $username = $params['username'];
  // retrieving the encrypted password of the login submitted
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['password'].' AS password
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.$username.'\'
;';
  $row = mysql_fetch_assoc(pwg_query($query));

  if ($row['password'] == $conf['pass_convert']($params['password']))
  {
    log_user($row['id'], false);
    return true;
  }
  return new PwgError(999, 'Invalid username/password');
}

function ws_session_logout($params, &$service)
{
  global $user, $conf;
  if (!$user['is_the_guest'])
  {
    $_SESSION = array();
    session_unset();
    session_destroy();
    setcookie(session_name(),'',0,
        ini_get('session.cookie_path'),
        ini_get('session.cookie_domain')
      );
    setcookie($conf['remember_me_name'], '', 0, cookie_path());
  }
  return true;
}

function ws_session_getStatus($params, &$service)
{
  global $user;
  $res = array();
  $res['username'] = $user['is_the_guest'] ? 'guest' : $user['username'];
  $res['status'] = $user['status'];
  return $res;
}


function ws_tags_getList($params, &$service)
{
  global $user;
  $tags = get_available_tags();
  if ($params['sort_by_counter'])
  {
    usort($tags, create_function('$a,$b', 'return -$a["counter"]+$b["counter"];') );
  }
  else
  {
    usort($tags, 'name_compare');
  }
  for ($i=0; $i<count($tags); $i++)
  {
    $tags[$i]['id'] = (int)$tags[$i]['tag_id'];
    $tags[$i]['counter'] = (int)$tags[$i]['counter'];
    unset($tags[$i]['tag_id']);
    $tags[$i]['url'] = make_index_url(
        array(
          'section'=>'tags',
          'tags'=>array($tags[$i])
        )
      );
  }
  return array('tags' => new PwgNamedArray($tags, 'tag', array('id','url_name','url', 'counter' )) );
}

function ws_tags_getImages($params, &$service)
{
  @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
  global $user, $conf;

  // first build all the tag_ids we are interested in
  $tag_ids = array();
  $tags = get_available_tags();
  $tags_by_id = array();
  for( $i=0; $i<count($tags); $i++ )
  {
    $tags[$i]['tag_id']=(int)$tags[$i]['tag_id'];
    $tags[$i]['id']=(int)$tags[$i]['tag_id']; //required by make_xxx_url
  }
  foreach( $tags as $tag )
  {
    $tags_by_id[ $tag['tag_id'] ] = $tag;
    if (
        in_array($tag['name'], $params['tag_name'])
      or
        in_array($tag['url_name'], $params['tag_url_name'])
       )
    {
      $tag_ids[] = $tag['tag_id'];
    }
  }
  unset($tags);

  foreach( $params['tag_id'] as $tag_id )
  {
    if ( (int)$tag_id > 0 )
    {
      $tag_ids[] = $tag_id;
    }
  }

  $tag_ids = array_unique( $tag_ids );

  $image_ids = array();
  $image_tag_map = array();

  if ( !empty($tag_ids) )
  { // build list of image ids with associated tags per image
    if ($params['tag_mode_and'])
    {
      $image_ids = get_image_ids_for_tags( $tag_ids );
    }
    else
    {
      $query = '
SELECT image_id, GROUP_CONCAT(tag_id) tag_ids
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',',$tag_ids).')
  GROUP BY image_id';
      $result = pwg_query($query);
      while ( $row=mysql_fetch_assoc($result) )
      {
        $row['image_id'] = (int)$row['image_id'];
        array_push( $image_ids, $row['image_id'] );
        $image_tag_map[ $row['image_id'] ] = explode(',', $row['tag_ids']);
      }
    }
  }

  $images = array();
  if ( !empty($image_ids))
  {
    $where_clauses = ws_std_image_sql_filter($params);
    $where_clauses[] = get_sql_condition_FandF(
        array
          (
            'forbidden_categories' => 'category_id',
            'visible_categories' => 'category_id',
            'visible_images' => 'i.id'
          ),
        '', true
      );
    $where_clauses[] = 'id IN ('.implode(',',$image_ids).')';
    $order_by = ws_std_image_sql_order($params);
    if (empty($order_by))
    {
      $order_by = $conf['order_by'];
    }
    else
    {
      $order_by = 'ORDER BY '.$order_by;
    }

    $query = '
SELECT DISTINCT i.* FROM '.IMAGES_TABLE.' i
  INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON i.id=image_id
  WHERE '. implode('
    AND ', $where_clauses).'
'.$order_by.'
LIMIT '.$params['per_page']*$params['page'].','.$params['per_page'];

    $result = pwg_query($query);
    while ($row = mysql_fetch_assoc($result))
    {
      foreach ( array('id', 'width', 'height', 'hit') as $k )
      {
        if (isset($row[$k]))
        {
          $image[$k] = (int)$row[$k];
        }
      }
      foreach ( array('name', 'file') as $k )
      {
        $image[$k] = $row[$k];
      }
      $image = array_merge( $image, ws_std_get_urls($row) );

      $image_tag_ids = ($params['tag_mode_and']) ? $tag_ids : $image_tag_map[$image['id']];
      $image_tags = array();
      foreach ($image_tag_ids as $tag_id)
      {
        $url = make_index_url(
                 array(
                  'section'=>'tags',
                  'tags'=> array($tags_by_id[$tag_id])
                )
              );
        $page_url = make_picture_url(
                 array(
                  'section'=>'tags',
                  'tags'=> array($tags_by_id[$tag_id]),
                  'image_id' => $row['id'],
                  'image_file' => $row['file'],
                )
              );
        array_push($image_tags, array(
                'id' => (int)$tag_id,
                'url' => $url,
                'page_url' => $page_url,
              )
            );
      }
      $image['tags'] = new PwgNamedArray($image_tags, 'tag',
              array('id','url_name','url','page_url')
            );
      array_push($images, $image);
    }
  }

  return array( 'images' =>
    array (
      WS_XML_ATTRIBUTES =>
        array(
            'page' => $params['page'],
            'per_page' => $params['per_page'],
            'count' => count($images)
          ),
       WS_XML_CONTENT => new PwgNamedArray($images, 'image',
          array('id', 'tn_url', 'element_url', 'file','width','height','hit') )
      )
    );
}

?>
