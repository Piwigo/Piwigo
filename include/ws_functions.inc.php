<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

/**** IMPLEMENTATION OF WEB SERVICE METHODS ***********************************/

/**
 * Event handler for method invocation security check. Should return a PwgError
 * if the preconditions are not satifsied for method invocation.
 */
function ws_isInvokeAllowed($res, $methodName, $params)
{
  global $conf;

  if ( strpos($methodName,'reflection.')===0 )
  { // OK for reflection
    return $res;
  }

  if ( !is_autorize_status(ACCESS_GUEST) and
      strpos($methodName,'pwg.session.')!==0 )
  {
    return new PwgError(401, 'Access denied');
  }

  return $res;
}

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

/**
 * returns an array of image attributes that are to be encoded as xml attributes
 * instead of xml elements
 */
function ws_std_get_image_xml_attributes()
{
  return array(
    'id','tn_url','element_url','high_url', 'file','width','height','hit'
    );
}

/**
 * returns PWG version (web service method)
 */
function ws_getVersion($params, &$service)
{
  global $conf;
  if ($conf['show_version'])
    return PHPWG_VERSION;
  else
    return new PwgError(403, 'Forbidden');
}

function ws_caddie_add($params, &$service)
{
  if (!is_admin())
  {
    return new PwgError(401, 'Access denied');
  }
  $params['image_id'] = array_map( 'intval',$params['image_id'] );
  if ( empty($params['image_id']) )
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }
  global $user;
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.' LEFT JOIN '.CADDIE_TABLE.' ON id=element_id AND user_id='.$user['id'].'
  WHERE id IN ('.implode(',',$params['image_id']).')
    AND element_id IS NULL';
  $datas = array();
  foreach ( array_from_query($query, 'id') as $id )
  {
    array_push($datas, array('element_id'=>$id, 'user_id'=>$user['id']) );
  }
  if (count($datas))
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    mass_inserts(CADDIE_TABLE, array('element_id','user_id'), $datas);
  }
  return count($datas);
}

/**
 * returns images per category (web service method)
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
  $where_clauses[] = get_sql_condition_FandF(
        array('forbidden_categories' => 'id'),
        NULL, true
      );

  $query = '
SELECT id, name, permalink, image_order
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
    $where_clauses[] = get_sql_condition_FandF( array(
          'visible_images' => 'i.id'
        ), null, true
      );

    $order_by = ws_std_image_sql_order($params, 'i.');
    if ( empty($order_by)
          and count($params['cat_id'])==1
          and isset($cats[ $params['cat_id'][0] ]['image_order'])
        )
    {
      $order_by = $cats[ $params['cat_id'][0] ]['image_order'];
    }
    $order_by = empty($order_by) ? $conf['order_by'] : 'ORDER BY '.$order_by;

    $query = '
SELECT i.*, GROUP_CONCAT(category_id) cat_ids
  FROM '.IMAGES_TABLE.' i
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON i.id=image_id
  WHERE '. implode('
    AND ', $where_clauses).'
GROUP BY i.id
'.$order_by.'
LIMIT '.(int)($params['per_page']*$params['page']).','.(int)$params['per_page'];

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
      foreach ( array('file', 'name', 'comment') as $k )
      {
        $image[$k] = $row[$k];
      }
      $image = array_merge( $image, ws_std_get_urls($row) );

      $image_cats = array();
      foreach ( explode(',', $row['cat_ids']) as $cat_id )
      {
        $url = make_index_url(
                array(
                  'category' => $cats[$cat_id],
                  )
                );
        $page_url = make_picture_url(
                array(
                  'category' => $cats[$cat_id],
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
          ws_std_get_image_xml_attributes() )
      )
    );
}


/**
 * returns a list of categories (web service method)
 */
function ws_categories_getList($params, &$service)
{
  global $user,$conf;

  $where = array();

  if (!$params['recursive'])
  {
    if ($params['cat_id']>0)
      $where[] = '(id_uppercat='.(int)($params['cat_id']).'
    OR id='.(int)($params['cat_id']).')';
    else
      $where[] = 'id_uppercat IS NULL';
  }
  else if ($params['cat_id']>0)
  {
    $where[] = 'uppercats REGEXP \'(^|,)'.
      (int)($params['cat_id'])
      .'(,|$)\'';
  }

  if ($params['public'])
  {
    $where[] = 'status = "public"';
    $where[] = 'visible = "true"';
    $where[]= 'user_id='.$conf['guest_id'];
  }
  else
  {
    $where[]= 'user_id='.$user['id'];
  }

  $query = '
SELECT id, name, permalink, uppercats, global_rank,
    nb_images, count_images AS total_nb_images,
    date_last, max_date_last, count_categories AS nb_categories
  FROM '.CATEGORIES_TABLE.'
   INNER JOIN '.USER_CACHE_CATEGORIES_TABLE.' ON id=cat_id
  WHERE '. implode('
    AND ', $where);

  $result = pwg_query($query);

  $cats = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $row['url'] = make_index_url(
        array(
          'category' => $row
          )
      );
    foreach( array('id','nb_images','total_nb_images','nb_categories') as $key)
    {
      $row[$key] = (int)$row[$key];
    }

    array_push($cats, $row);
  }
  usort($cats, 'global_rank_compare');
  return array(
    'categories' => new PwgNamedArray(
      $cats,
      'category',
      array(
        'id',
        'url',
        'nb_images',
        'total_nb_images',
        'nb_categories',
        'date_last',
        'max_date_last',
        )
      )
    );
}

/**
 * returns the list of categories as you can see them in administration (web
 * service method).
 *
 * Only admin can run this method and permissions are not taken into
 * account.
 */
function ws_categories_getAdminList($params, &$service)
{
  if (!is_admin())
  {
    return new PwgError(401, 'Access denied');
  }

  $query = '
SELECT
    category_id,
    COUNT(*) AS counter
  FROM '.IMAGE_CATEGORY_TABLE.'
  GROUP BY category_id
;';
  $nb_images_of = simple_hash_from_query($query, 'category_id', 'counter');

  $query = '
SELECT
    id,
    name,
    uppercats,
    global_rank
  FROM '.CATEGORIES_TABLE.'
;';
  $result = pwg_query($query);
  $cats = array();

  while ($row = mysql_fetch_assoc($result))
  {
    $id = $row['id'];
    $row['nb_images'] = isset($nb_images_of[$id]) ? $nb_images_of[$id] : 0;
    array_push($cats, $row);
  }

  usort($cats, 'global_rank_compare');
  return array(
    'categories' => new PwgNamedArray(
      $cats,
      'category',
      array(
        'id',
        'nb_images',
        'name',
        'uppercats',
        'global_rank',
        )
      )
    );
}

/**
 * returns detailed information for an element (web service method)
 */
function ws_images_addComment($params, &$service)
{
  if (!$service->isPost())
  {
    return new PwgError(405, "This method requires HTTP POST");
  }
  $params['image_id'] = (int)$params['image_id'];
  $query = '
SELECT DISTINCT image_id
  FROM '.IMAGE_CATEGORY_TABLE.' INNER JOIN '.CATEGORIES_TABLE.' ON category_id=id
  WHERE commentable="true"
    AND image_id='.$params['image_id'].
    get_sql_condition_FandF(
      array(
        'forbidden_categories' => 'id',
        'visible_categories' => 'id',
        'visible_images' => 'image_id'
      ),
      ' AND'
    );
  if ( !mysql_num_rows( pwg_query( $query ) ) )
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }

  $comm = array(
    'author' => trim( stripslashes($params['author']) ),
    'content' => trim( stripslashes($params['content']) ),
    'image_id' => $params['image_id'],
   );

  include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

  $comment_action = insert_user_comment(
      $comm, $params['key'], $infos
    );

  switch ($comment_action)
  {
    case 'reject':
      array_push($infos, l10n('comment_not_added') );
      return new PwgError(403, implode("\n", $infos) );
    case 'validate':
    case 'moderate':
      $ret = array(
          'id' => $comm['id'],
          'validation' => $comment_action=='validate',
        );
      return new PwgNamedStruct(
          'comment',
          $ret,
          null, array()
        );
    default:
      return new PwgError(500, "Unknown comment action ".$comment_action );
  }
}

/**
 * returns detailed information for an element (web service method)
 */
function ws_images_getInfo($params, &$service)
{
  @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
  global $user, $conf;
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
    return new PwgError(404, "image_id not found");
  }
  $image_row = array_merge( $image_row, ws_std_get_urls($image_row) );

  //-------------------------------------------------------- related categories
  $query = '
SELECT id, name, permalink, uppercats, global_rank, commentable
  FROM '.IMAGE_CATEGORY_TABLE.'
    INNER JOIN '.CATEGORIES_TABLE.' ON category_id = id
  WHERE image_id = '.$image_row['id'].
  get_sql_condition_FandF(
      array( 'forbidden_categories' => 'category_id' ),
      ' AND'
    ).'
;';
  $result = pwg_query($query);
  $is_commentable = false;
  $related_categories = array();
  while ($row = mysql_fetch_assoc($result))
  {
    if ($row['commentable']=='true')
    {
      $is_commentable = true;
    }
    unset($row['commentable']);
    $row['url'] = make_index_url(
        array(
          'category' => $row
          )
      );

    $row['page_url'] = make_picture_url(
        array(
          'image_id' => $image_row['id'],
          'image_file' => $image_row['file'],
          'category' => $row
          )
      );
    $row['id']=(int)$row['id'];
    array_push($related_categories, $row);
  }
  usort($related_categories, 'global_rank_compare');
  if ( empty($related_categories) )
  {
    return new PwgError(401, 'Access denied');
  }

  //-------------------------------------------------------------- related tags
  $related_tags = get_common_tags( array($image_row['id']), -1 );
  foreach( $related_tags as $i=>$tag)
  {
    $tag['url'] = make_index_url(
        array(
          'tags' => array($tag)
          )
      );
    $tag['page_url'] = make_picture_url(
        array(
          'image_id' => $image_row['id'],
          'image_file' => $image_row['file'],
          'tags' => array($tag),
          )
      );
    unset($tag['counter']);
    $tag['id']=(int)$tag['id'];
    $related_tags[$i]=$tag;
  }
  //------------------------------------------------------------- related rates
  $query = '
SELECT COUNT(rate) AS count
     , ROUND(AVG(rate),2) AS average
     , ROUND(STD(rate),2) AS stdev
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_row['id'].'
;';
  $rating = mysql_fetch_assoc(pwg_query($query));
  $rating['count'] = (int)$rating['count'];

  //---------------------------------------------------------- related comments
  $related_comments = array();

  $where_comments = 'image_id = '.$image_row['id'];
  if ( !is_admin() )
  {
    $where_comments .= '
    AND validated="true"';
  }

  $query = '
SELECT COUNT(id) nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE '.$where_comments;
  list($nb_comments) = array_from_query($query, 'nb_comments');
  $nb_comments = (int)$nb_comments;

  if ( $nb_comments>0 and $params['comments_per_page']>0 )
  {
    $query = '
SELECT id, date, author, content
  FROM '.COMMENTS_TABLE.'
  WHERE '.$where_comments.'
  ORDER BY date
  LIMIT '.(int)($params['comments_per_page']*$params['comments_page']).
    ','.(int)$params['comments_per_page'];

    $result = pwg_query($query);
    while ($row = mysql_fetch_assoc($result))
    {
      $row['id']=(int)$row['id'];
      array_push($related_comments, $row);
    }
  }

  $comment_post_data = null;
  if ($is_commentable and
      (!is_a_guest()
        or (is_a_guest() and $conf['comments_forall'] )
      )
      )
  {
    $comment_post_data['author'] = $user['username'];
    $comment_post_data['key'] = get_comment_post_key($params['image_id']);
  }

  $ret = $image_row;
  foreach ( array('id','width','height','hit','filesize') as $k )
  {
    if (isset($ret[$k]))
    {
      $ret[$k] = (int)$ret[$k];
    }
  }
  foreach ( array('path', 'storage_category_id') as $k )
  {
    unset($ret[$k]);
  }

  $ret['rates'] = array( WS_XML_ATTRIBUTES => $rating );
  $ret['categories'] = new PwgNamedArray($related_categories, 'category', array('id','url', 'page_url') );
  $ret['tags'] = new PwgNamedArray($related_tags, 'tag', array('id','url_name','url','name','page_url') );
  if ( isset($comment_post_data) )
  {
    $ret['comment_post'] = array( WS_XML_ATTRIBUTES => $comment_post_data );
  }
  $ret['comments'] = array(
     WS_XML_ATTRIBUTES =>
        array(
          'page' => $params['comments_page'],
          'per_page' => $params['comments_per_page'],
          'count' => count($related_comments),
          'nb_comments' => $nb_comments,
        ),
     WS_XML_CONTENT => new PwgNamedArray($related_comments, 'comment', array('id','date') )
      );

  return new PwgNamedStruct('image',$ret, null, array('name','comment') );
}


/**
 * rates the image_id in the parameter
 */
function ws_images_Rate($params, &$service)
{
  $image_id = (int)$params['image_id'];
  $query = '
SELECT DISTINCT id FROM '.IMAGES_TABLE.'
  INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id
  WHERE id='.$image_id
  .get_sql_condition_FandF(
    array(
        'forbidden_categories' => 'category_id',
        'forbidden_images' => 'id',
      ),
    '    AND'
    ).'
    LIMIT 1';
  if ( mysql_num_rows( pwg_query($query) )==0 )
  {
    return new PwgError(404, "Invalid image_id or access denied" );
  }
  $rate = (int)$params['rate'];
  include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
  $res = rate_picture( $image_id, $rate );
  if ($res==false)
  {
    global $conf;
    return new PwgError( 403, "Forbidden or rate not in ". implode(',',$conf['rate_items']));
  }
  return $res;
}


/**
 * returns a list of elements corresponding to a query search
 */
function ws_images_search($params, &$service)
{
  global $page;
  $images = array();
  include_once( PHPWG_ROOT_PATH .'include/functions_search.inc.php' );
  include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');

  $where_clauses = ws_std_image_sql_filter( $params, 'i.' );
  $order_by = ws_std_image_sql_order($params, 'i.');

  $super_order_by = false;
  if ( !empty($order_by) )
  {
    global $conf;
    $conf['order_by'] = 'ORDER BY '.$order_by;
    $super_order_by=true; // quick_search_result might be faster
  }

  $search_result = get_quick_search_results($params['query'],
      $super_order_by,
      implode(',', $where_clauses)
    );

  $image_ids = array_slice(
      $search_result['items'],
      $params['page']*$params['per_page'],
      $params['per_page']
    );

  if ( count($image_ids) )
  {
    $query = '
SELECT * FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $image_ids).')';

    $image_ids = array_flip($image_ids);
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
      foreach ( array('file', 'name', 'comment') as $k )
      {
        $image[$k] = $row[$k];
      }
      $image = array_merge( $image, ws_std_get_urls($row) );
      $images[$image_ids[$image['id']]] = $image;
    }
    ksort($images, SORT_NUMERIC);
    $images = array_values($images);
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
          ws_std_get_image_xml_attributes() )
      )
    );
}

function ws_images_setPrivacyLevel($params, &$service)
{
  if (!is_admin() || is_adviser() )
  {
    return new PwgError(401, 'Access denied');
  }
  $params['image_id'] = array_map( 'intval',$params['image_id'] );
  if ( empty($params['image_id']) )
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }
  global $conf;
  if ( !in_array( (int)$params['level'], $conf['available_permission_levels']) )
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid level");
  }
  $query = '
UPDATE '.IMAGES_TABLE.'
  SET level='.(int)$params['level'].'
  WHERE id IN ('.implode(',',$params['image_id']).')';
  $result = pwg_query($query);
  $affected_rows = mysql_affected_rows();
  if ($affected_rows)
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    invalidate_user_cache();
  }
  return $affected_rows;
}

function ws_images_add_chunk($params, &$service)
{
  // data
  // original_sum
  // type {thumb, file, high}
  // position
  
  if (!is_admin() || is_adviser() )
  {
    return new PwgError(401, 'Access denied');
  }

  if (!$service->isPost())
  {
    return new PwgError(405, "This method requires HTTP POST");
  }

  $upload_dir = PHPWG_ROOT_PATH.'upload/buffer';

  // create the upload directory tree if not exists
  if (!is_dir($upload_dir)) {
    umask(0000);
    $recursive = true;
    if (!@mkdir($upload_dir, 0777, $recursive))
    {
      return new PwgError(500, 'error during buffer directory creation');
    }
  }

  if (!is_writable($upload_dir))
  {
    // last chance to make the directory writable
    @chmod($upload_dir, 0777);

    if (!is_writable($upload_dir))
    {
      return new PwgError(500, 'buffer directory has no write access');
    }
  }

  secure_directory($upload_dir);

  $filename = sprintf(
    '%s-%s-%05u.block',
    $params['original_sum'],
    $params['type'],
    $params['position']
    );

  ws_logfile('[ws_images_add_chunk] data length : '.strlen($params['data']));

  $bytes_written = file_put_contents(
    $upload_dir.'/'.$filename,
    base64_decode($params['data'])
    );

  if (false === $bytes_written) {
    return new PwgError(
      500,
      'an error has occured while writting chunk '.$params['position'].' for '.$params['type']
      );
  }
}

function merge_chunks($output_filepath, $original_sum, $type)
{
  ws_logfile('[merge_chunks] input parameter $output_filepath : '.$output_filepath);

  if (is_file($output_filepath))
  {
    unlink($output_filepath);
    
    if (is_file($output_filepath))
    {
      new PwgError(500, '[merge_chunks] error while trying to remove existing '.$output_filepath);
      exit();
    }
  }
  
  $upload_dir = PHPWG_ROOT_PATH.'upload/buffer';
  $pattern = '/'.$original_sum.'-'.$type.'/';
  $chunks = array();
  
  if ($handle = opendir($upload_dir))
  {
    while (false !== ($file = readdir($handle)))
    {
      if (preg_match($pattern, $file))
      {
        ws_logfile($file);
        array_push($chunks, $upload_dir.'/'.$file);
      }
    }
    closedir($handle);
  }

  sort($chunks);

  if (function_exists('memory_get_usage')) {
    ws_logfile('[merge_chunks] memory_get_usage before loading chunks: '.memory_get_usage());
  }

  $i = 0;
  
  foreach ($chunks as $chunk)
  {
    $string = file_get_contents($chunk);
    
    if (function_exists('memory_get_usage')) {
      ws_logfile('[merge_chunks] memory_get_usage on chunk '.++$i.': '.memory_get_usage());
    }
    
    if (!file_put_contents($output_filepath, $string, FILE_APPEND))
    {
      new PwgError(500, '[merge_chunks] error while writting chunks for '.$output_filepath);
      exit();
    }
    
    unlink($chunk);
  }

  if (function_exists('memory_get_usage')) {
    ws_logfile('[merge_chunks] memory_get_usage after loading chunks: '.memory_get_usage());
  }
}

/*
 * The $file_path must be the path of the basic "web sized" photo
 * The $type value will automatically modify the $file_path to the corresponding file
 */
function add_file($file_path, $type, $original_sum, $file_sum)
{
  $file_path = file_path_for_type($file_path, $type);

  $upload_dir = dirname($file_path);
  
  if (!is_dir($upload_dir)) {
    umask(0000);
    $recursive = true;
    if (!@mkdir($upload_dir, 0777, $recursive))
    {
      new PwgError(500, '[add_file] error during '.$type.' directory creation');
      exit();
    }
  }

  if (!is_writable($upload_dir))
  {
    // last chance to make the directory writable
    @chmod($upload_dir, 0777);

    if (!is_writable($upload_dir))
    {
      new PwgError(500, '[add_file] '.$type.' directory has no write access');
      exit();
    }
  }

  secure_directory($upload_dir);

  // merge the thumbnail
  merge_chunks($file_path, $original_sum, $type);
  chmod($file_path, 0644);

  // check dumped thumbnail md5
  $dumped_md5 = md5_file($file_path);
  if ($dumped_md5 != $file_sum) {
    new PwgError(500, '[add_file] '.$type.' transfer failed');
    exit();
  }

  list($width, $height) = getimagesize($file_path);
  $filesize = floor(filesize($file_path)/1024);

  return array(
    'width' => $width,
    'height' => $height,
    'filesize' => $filesize,
    );
}

function ws_images_addFile($params, &$service)
{
  // image_id
  // type {thumb, file, high}
  // sum

  global $conf;
  if (!is_admin() || is_adviser() )
  {
    return new PwgError(401, 'Access denied');
  }

  $params['image_id'] = (int)$params['image_id'];
  if ($params['image_id'] <= 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }

  //
  // what is the path?
  //
  $query = '
SELECT
    path,
    md5sum
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$params['image_id'].'
;';
  list($file_path, $original_sum) = mysql_fetch_row(pwg_query($query));

  // TODO only files added with web API can be updated with web API

  //
  // makes sure directories are there and call the merge_chunks
  //
  $infos = add_file($file_path, $params['type'], $original_sum, $params['sum']);

  //
  // update basic metadata from file
  //
  $update = array();
  
  if ('high' == $params['type'])
  {
    $update['high_filesize'] = $infos['filesize'];
    $update['has_high'] = 'true';
  }

  if ('file' == $params['type'])
  {
    $update['filesize'] = $infos['filesize'];
    $update['width'] = $infos['width'];
    $update['height'] = $infos['height'];
  }

  // we may have nothing to update at database level, for example with a
  // thumbnail update
  if (count($update) > 0)
  {
    $update['id'] = $params['image_id'];
    
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    mass_updates(
      IMAGES_TABLE,
      array(
        'primary' => array('id'),
        'update'  => array_diff(array_keys($update), array('id'))
        ),
      array($update)
      );
  }
}

function ws_images_add($params, &$service)
{
  global $conf;
  if (!is_admin() || is_adviser() )
  {
    return new PwgError(401, 'Access denied');
  }

  foreach ($params as $param_key => $param_value) {
    ws_logfile(
      sprintf(
        '[pwg.images.add] input param "%s" : "%s"',
        $param_key,
        is_null($param_value) ? 'NULL' : $param_value
        )
      );
  }

  // does the image already exists ?
  $query = '
SELECT
    COUNT(*) AS counter
  FROM '.IMAGES_TABLE.'
  WHERE md5sum = \''.$params['original_sum'].'\'
;';
  list($counter) = mysql_fetch_row(pwg_query($query));
  if ($counter != 0) {
    return new PwgError(500, 'file already exists');
  }

  // current date
  list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));
  list($year, $month, $day) = preg_split('/[^\d]/', $dbnow, 4);

  // upload directory hierarchy
  $upload_dir = sprintf(
    PHPWG_ROOT_PATH.'upload/%s/%s/%s',
    $year,
    $month,
    $day
    );

  // compute file path
  $date_string = preg_replace('/[^\d]/', '', $dbnow);
  $random_string = substr($params['file_sum'], 0, 8);
  $filename_wo_ext = $date_string.'-'.$random_string;
  $file_path = $upload_dir.'/'.$filename_wo_ext.'.jpg';

  // add files
  $file_infos  = add_file($file_path, 'file',  $params['original_sum'], $params['file_sum']);
  $thumb_infos = add_file($file_path, 'thumb', $params['original_sum'], $params['thumbnail_sum']);

  if (isset($params['high_sum']))
  {
    $high_infos = add_file($file_path, 'high', $params['original_sum'], $params['high_sum']);
  }

  // database registration
  $insert = array(
    'file' => $filename_wo_ext.'.jpg',
    'date_available' => $dbnow,
    'tn_ext' => 'jpg',
    'name' => $params['name'],
    'path' => $file_path,
    'filesize' => $file_infos['filesize'],
    'width' => $file_infos['width'],
    'height' => $file_infos['height'],
    'md5sum' => $params['original_sum'],
    );

  $info_columns = array(
    'name',
    'author',
    'comment',
    'level',
    'date_creation',
    );

  foreach ($info_columns as $key)
  {
    if (isset($params[$key]))
    {
      $insert[$key] = $params[$key];
    }
  }

  if (isset($params['high_sum']))
  {
    $insert['has_high'] = 'true';
    $insert['high_filesize'] = $high_infos['filesize'];
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  mass_inserts(
    IMAGES_TABLE,
    array_keys($insert),
    array($insert)
    );

  $image_id = mysql_insert_id();

  // let's add links between the image and the categories
  if (isset($params['categories']))
  {
    ws_add_image_category_relations($image_id, $params['categories']);
  }

  // and now, let's create tag associations
  if (isset($params['tag_ids']) and !empty($params['tag_ids']))
  {
    set_tags(
      explode(',', $params['tag_ids']),
      $image_id
      );
  }

  invalidate_user_cache();
}

/**
 * perform a login (web service method)
 */
function ws_session_login($params, &$service)
{
  global $conf;

  if (!$service->isPost())
  {
    return new PwgError(405, "This method requires HTTP POST");
  }
  if (try_log_user($params['username'], $params['password'],false))
  {
    return true;
  }
  return new PwgError(999, 'Invalid username/password');
}


/**
 * performs a logout (web service method)
 */
function ws_session_logout($params, &$service)
{
  if (!is_a_guest())
  {
    logout_user();
  }
  return true;
}

function ws_session_getStatus($params, &$service)
{
  global $user;
  $res = array();
  $res['username'] = is_a_guest() ? 'guest' : $user['username'];
  foreach ( array('status', 'template', 'theme', 'language') as $k )
  {
    $res[$k] = $user[$k];
  }
  $res['charset'] = get_pwg_charset();
  return $res;
}


/**
 * returns a list of tags (web service method)
 */
function ws_tags_getList($params, &$service)
{
  $tags = get_available_tags();
  if ($params['sort_by_counter'])
  {
    usort($tags, create_function('$a,$b', 'return -$a["counter"]+$b["counter"];') );
  }
  else
  {
    usort($tags, 'tag_alpha_compare');
  }
  for ($i=0; $i<count($tags); $i++)
  {
    $tags[$i]['id'] = (int)$tags[$i]['id'];
    $tags[$i]['counter'] = (int)$tags[$i]['counter'];
    $tags[$i]['url'] = make_index_url(
        array(
          'section'=>'tags',
          'tags'=>array($tags[$i])
        )
      );
  }
  return array('tags' => new PwgNamedArray($tags, 'tag', array('id','url_name','url', 'name', 'counter' )) );
}

/**
 * returns the list of tags as you can see them in administration (web
 * service method).
 *
 * Only admin can run this method and permissions are not taken into
 * account.
 */
function ws_tags_getAdminList($params, &$service)
{
  if (!is_admin())
  {
    return new PwgError(401, 'Access denied');
  }

  $tags = get_all_tags();
  return array(
    'tags' => new PwgNamedArray(
      $tags,
      'tag',
      array(
        'name',
        'id',
        'url_name',
        )
      )
    );
}

/**
 * returns a list of images for tags (web service method)
 */
function ws_tags_getImages($params, &$service)
{
  @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
  global $conf;

  // first build all the tag_ids we are interested in
  $params['tag_id'] = array_map( 'intval',$params['tag_id'] );
  $tags = find_tags($params['tag_id'], $params['tag_url_name'], $params['tag_name']);
  $tags_by_id = array();
  foreach( $tags as $tag )
  {
    $tags['id'] = (int)$tag['id'];
    $tags_by_id[ $tag['id'] ] = $tag;
  }
  unset($tags);
  $tag_ids = array_keys($tags_by_id);


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
LIMIT '.(int)($params['per_page']*$params['page']).','.(int)$params['per_page'];

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
      foreach ( array('file', 'name', 'comment') as $k )
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
          ws_std_get_image_xml_attributes() )
      )
    );
}

function ws_categories_add($params, &$service)
{
  if (!is_admin() or is_adviser())
  {
    return new PwgError(401, 'Access denied');
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  $creation_output = create_virtual_category(
    $params['name'],
    $params['parent']
    );

  if (isset($creation_output['error']))
  {
    return new PwgError(500, $creation_output['error']);
  }

  invalidate_user_cache();

  return $creation_output;
}

function ws_tags_add($params, &$service)
{
  if (!is_admin() or is_adviser())
  {
    return new PwgError(401, 'Access denied');
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  $creation_output = create_tag($params['name']);

  if (isset($creation_output['error']))
  {
    return new PwgError(500, $creation_output['error']);
  }

  return $creation_output;
}

function ws_images_exist($params, &$service)
{
  if (!is_admin() or is_adviser())
  {
    return new PwgError(401, 'Access denied');
  }

  // search among photos the list of photos already added, based on md5sum
  // list
  $md5sums = preg_split(
    '/[\s,;\|]/',
    $params['md5sum_list'],
    -1,
    PREG_SPLIT_NO_EMPTY
    );

  $query = '
SELECT
    id,
    md5sum
  FROM '.IMAGES_TABLE.'
  WHERE md5sum IN (\''.implode("','", $md5sums).'\')
;';
  $id_of_md5 = simple_hash_from_query($query, 'md5sum', 'id');

  $result = array();

  foreach ($md5sums as $md5sum)
  {
    $result[$md5sum] = null;
    if (isset($id_of_md5[$md5sum]))
    {
      $result[$md5sum] = $id_of_md5[$md5sum];
    }
  }

  return $result;
}

function ws_images_checkFiles($params, &$service)
{
  if (!is_admin() or is_adviser())
  {
    return new PwgError(401, 'Access denied');
  }

  // input parameters
  //
  // image_id
  // thumbnail_sum
  // file_sum
  // high_sum

  $params['image_id'] = (int)$params['image_id'];
  if ($params['image_id'] <= 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }

  $query = '
SELECT
    path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$params['image_id'].'
;';
  $result = pwg_query($query);
  if (mysql_num_rows($result) == 0) {
    return new PwgError(404, "image_id not found");
  }
  list($path) = mysql_fetch_row($result);

  $ret = array();

  foreach (array('thumb', 'file', 'high') as $type) {
    $param_name = $type;
    if ('thumb' == $type) {
      $param_name = 'thumbnail';
    }

    if (isset($params[$param_name.'_sum'])) {
      $type_path = file_path_for_type($path, $type);
      if (!is_file($type_path)) {
        $ret[$param_name] = 'missing';
      }
      else {
        if (md5_file($type_path) != $params[$param_name.'_sum']) {
          $ret[$param_name] = 'differs';
        }
        else {
          $ret[$param_name] = 'equals';
        }
      }
    }
  }

  return $ret;
}

function file_path_for_type($file_path, $type='thumb')
{
  // resolve the $file_path depending on the $type
  if ('thumb' == $type) {
    $file_path = get_thumbnail_location(
      array(
        'path' => $file_path,
        'tn_ext' => 'jpg',
        )
      );
  }

  if ('high' == $type) {
    @include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
    $file_path = get_high_location(
      array(
        'path' => $file_path,
        'has_high' => 'true'
        )
      );
  }

  return $file_path;
}

function ws_images_setInfo($params, &$service)
{
  global $conf;
  if (!is_admin() || is_adviser() )
  {
    return new PwgError(401, 'Access denied');
  }

  if (!$service->isPost())
  {
    return new PwgError(405, "This method requires HTTP POST");
  }

  $params['image_id'] = (int)$params['image_id'];
  if ($params['image_id'] <= 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid image_id");
  }

  $query='
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$params['image_id'].'
;';

  $image_row = mysql_fetch_assoc(pwg_query($query));
  if ($image_row == null)
  {
    return new PwgError(404, "image_id not found");
  }

  // database registration
  $update = array();

  $info_columns = array(
    'name',
    'author',
    'comment',
    'level',
    'date_creation',
    );

  foreach ($info_columns as $key)
  {
    if (isset($params[$key]))
    {
      if ('fill_if_empty' == $params['single_value_mode'])
      {
        if (empty($image_row[$key]))
        {
          $update[$key] = $params[$key];
        }
      }
      elseif ('replace' == $params['single_value_mode'])
      {
        $update[$key] = $params[$key];
      }
      else
      {
        new PwgError(
          500,
          '[ws_images_setInfo]'
          .' invalid parameter single_value_mode "'.$params['single_value_mode'].'"'
          .', possible values are {fill_if_empty, replace}.'
          );
        exit();
      }
    }
  }

  if (count(array_keys($update)) > 0)
  {
    $update['id'] = $params['image_id'];

    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    mass_updates(
      IMAGES_TABLE,
      array(
        'primary' => array('id'),
        'update'  => array_diff(array_keys($update), array('id'))
        ),
      array($update)
      );
  }

  if (isset($params['categories']))
  {
    ws_add_image_category_relations(
      $params['image_id'],
      $params['categories'],
      ('replace' == $params['multiple_value_mode'] ? true : false)
      );
  }

  // and now, let's create tag associations
  if (isset($params['tag_ids']))
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

    $tag_ids = explode(',', $params['tag_ids']);

    if ('replace' == $params['multiple_value_mode'])
    {
      set_tags(
        $tag_ids,
        $params['image_id']
        );
    }
    elseif ('append' == $params['multiple_value_mode'])
    {
      add_tags(
        $tag_ids,
        array($params['image_id'])
        );
    }
    else
    {
      new PwgError(
        500,
        '[ws_images_setInfo]'
        .' invalid parameter multiple_value_mode "'.$params['multiple_value_mode'].'"'
        .', possible values are {replace, append}.'
        );
      exit();
    }
  }

  invalidate_user_cache();
}

function ws_add_image_category_relations($image_id, $categories_string, $replace_mode=false)
{
  // let's add links between the image and the categories
  //
  // $params['categories'] should look like 123,12;456,auto;789 which means:
  //
  // 1. associate with category 123 on rank 12
  // 2. associate with category 456 on automatic rank
  // 3. associate with category 789 on automatic rank
  $cat_ids = array();
  $rank_on_category = array();
  $search_current_ranks = false;

  $tokens = explode(';', $categories_string);
  foreach ($tokens as $token)
  {
    @list($cat_id, $rank) = explode(',', $token);

    if (!preg_match('/^\d+$/', $cat_id))
    {
      continue;
    }

    array_push($cat_ids, $cat_id);

    if (!isset($rank))
    {
      $rank = 'auto';
    }
    $rank_on_category[$cat_id] = $rank;

    if ($rank == 'auto')
    {
      $search_current_ranks = true;
    }
  }

  $cat_ids = array_unique($cat_ids);

  if (count($cat_ids) == 0)
  {
    new PwgError(
      500,
      '[ws_add_image_category_relations] there is no category defined in "'.$categories_string.'"'
      );
    exit();
  }
  
  $query = '
SELECT
    id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $cat_ids).')
;';
  $db_cat_ids = array_from_query($query, 'id');

  $unknown_cat_ids = array_diff($cat_ids, $db_cat_ids);
  if (count($unknown_cat_ids) != 0)
  {
    new PwgError(
      500,
      '[ws_add_image_category_relations] the following categories are unknown: '.implode(', ', $unknown_cat_ids)
      );
    exit();
  }
  
  $to_update_cat_ids = array();
    
  // in case of replace mode, we first check the existing associations
  $query = '
SELECT
    category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$image_id.'
;';
  $existing_cat_ids = array_from_query($query, 'category_id');

  if ($replace_mode)
  {
    $to_remove_cat_ids = array_diff($existing_cat_ids, $cat_ids);
    if (count($to_remove_cat_ids) > 0)
    {
      $query = '
DELETE
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id = '.$image_id.'
    AND category_id IN ('.implode(', ', $to_remove_cat_ids).')
;';
      pwg_query($query);
      update_category($to_remove_cat_ids);
    }
  }
  
  $new_cat_ids = array_diff($cat_ids, $existing_cat_ids);
  if (count($new_cat_ids) == 0)
  {
    return true;
  }
    
  if ($search_current_ranks)
  {
    $query = '
SELECT
    category_id,
    MAX(rank) AS max_rank
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE rank IS NOT NULL
    AND category_id IN ('.implode(',', $new_cat_ids).')
  GROUP BY category_id
;';
    $current_rank_of = simple_hash_from_query(
      $query,
      'category_id',
      'max_rank'
      );

    foreach ($new_cat_ids as $cat_id)
    {
      if (!isset($current_rank_of[$cat_id]))
      {
        $current_rank_of[$cat_id] = 0;
      }
      
      if ('auto' == $rank_on_category[$cat_id])
      {
        $rank_on_category[$cat_id] = $current_rank_of[$cat_id] + 1;
      }
    }
  }
  
  $inserts = array();
  
  foreach ($new_cat_ids as $cat_id)
  {
    array_push(
      $inserts,
      array(
        'image_id' => $image_id,
        'category_id' => $cat_id,
        'rank' => $rank_on_category[$cat_id],
        )
      );
  }
  
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  mass_inserts(
    IMAGE_CATEGORY_TABLE,
    array_keys($inserts[0]),
    $inserts
    );
  
  update_category($new_cat_ids);
}

function ws_categories_setInfo($params, &$service)
{
  global $conf;
  if (!is_admin() || is_adviser() )
  {
    return new PwgError(401, 'Access denied');
  }

  if (!$service->isPost())
  {
    return new PwgError(405, "This method requires HTTP POST");
  }

  // category_id
  // name
  // comment

  $params['category_id'] = (int)$params['category_id'];
  if ($params['category_id'] <= 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid category_id");
  }

  // database registration
  $update = array(
    'id' => $params['category_id'],
    );

  $info_columns = array(
    'name',
    'comment',
    );

  $perform_update = false;
  foreach ($info_columns as $key)
  {
    if (isset($params[$key]))
    {
      $perform_update = true;
      $update[$key] = $params[$key];
    }
  }

  if ($perform_update)
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    mass_updates(
      CATEGORIES_TABLE,
      array(
        'primary' => array('id'),
        'update'  => array_diff(array_keys($update), array('id'))
        ),
      array($update)
      );
  }
  
}

function ws_logfile($string)
{
  global $conf;

  if (!$conf['ws_enable_log']) {
    return true;
  }
  
  file_put_contents(
    $conf['ws_log_filepath'],
    '['.date('c').'] '.$string."\n",
    FILE_APPEND
    );
}
?>
