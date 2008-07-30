<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
  global $conf, $calling_partner_id;

  if ( strpos($methodName,'reflection.')===0 )
  { // OK for reflection
    return $res;
  }

  if ( !is_autorize_status(ACCESS_GUEST) and
      strpos($methodName,'pwg.session.')!==0 )
  {
    return new PwgError(401, 'Access denied');
  }

  if ( !$conf['ws_access_control'] )
  {
    return $res; // No controls are requested
  }
  $query = '
SELECT * FROM '.WEB_SERVICES_ACCESS_TABLE."
 WHERE `name` = '$calling_partner_id'
   AND NOW() <= end; ";
  $result = pwg_query($query);
  $row = mysql_fetch_assoc($result);
  if ( empty($row) )
  {
    return new PwgError(403, 'Partner id does not exist or is expired');
  }
  if ( !empty($row['request'])
      and strpos($methodName, $row['request'])==false
      and strpos($methodName, 'session')==false
      and strpos($methodName, 'getVersion')==false )
  { // session and getVersion are allowed to diagnose any failure reason
    return new PwgError(403, 'Method not allowed');
  }

  return $res;
}

/**
 * ws_addControls
 * returns additionnal controls if requested
 * usable for 99% of Web Service methods
 *
 * - Args
 * $methodName: is the requested method
 * $partner: is the key
 * $tbl_name: is the alias_name in the query (sometimes called correlation name)
 *            null if !getting picture informations
 * - Logic
 * Access_control is not active: Return
 * Key is incorrect: Return 0 = 1 (False condition for MySQL)
 * One of Params doesn't match with type of request: return 0 = 1 again
 * Access list(id/cat/tag) is converted in expended image-id list
 * image-id list: converted to an in-where-clause
 *
 * The additionnal in-where-clause is return
 */
function ws_addControls( $methodName, &$params, $tbl_name )
{
  global $conf, $calling_partner_id;
  if ( !$conf['ws_access_control'] or !isset($calling_partner_id) )
  {
    return '1=1'; // No controls are requested
  }

// Is it an active Partner?
  $query = '
SELECT * FROM '.WEB_SERVICES_ACCESS_TABLE."
 WHERE `name` = '$calling_partner_id'
   AND NOW() <= end; ";
$result = pwg_query($query);
  if ( mysql_num_rows( $result ) == 0 )
  {
    return '0=1'; // Unknown partner or Obsolate agreement
  }

  $row = mysql_fetch_array($result);

// Overide general object limit
  $params['per_page'] = $row['limit'];

// Target restrict
// 3 cases: list, cat or tag
// Behind / we could found img-ids, cat-ids or tag-ids
  $target = $row['access'];
  if ( $target == '')
  {
    return '1=1'; // No controls are requested
  }
  list($type, $str_ids) = explode('/',$target); // Find type list

// (array) 1,2,21,3,22,4,5,9-12,6,11,12,13,2,4,6,
  $arr_ids = expand_id_list( explode( ',',$str_ids ) );
  $addings = implode(',', $arr_ids);
// (string) 1,2,3,4,5,6,9,10,11,12,13,21,22,
  if ( $type == 'list')
  {
    return $tbl_name . 'id IN ( ' . $addings . ' ) ';
  }

  if ( $type == 'cat' )
  {
    $addings = implode(',', get_image_ids_for_cats($arr_ids));
    return $tbl_name . 'id IN ( ' . $addings . ' ) ';
  }

  if ( $type == 'tag' )
  {
    $addings = implode(',', get_image_ids_for_tags($arr_ids, 'OR'));
    return $tbl_name . 'id IN ( ' . $addings . ' ) ';
  }
  // Unmanaged new type?
  return ' 0 = 1 '; // ???
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
    $where_clauses[] = ws_addControls( 'categories.getImages', $params, 'i.' );

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
      'categories' =>
          new PwgNamedArray($cats,'category',
            array('id','url','nb_images','total_nb_images','nb_categories','date_last','max_date_last')
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

  include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

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
    ).' AND '.
    ws_addControls( 'images.getInfo', $params, '' ).'
LIMIT 1;';

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
  LIMIT '.$params['comments_per_page']*(int)$params['comments_page'].
    ','.$params['comments_per_page'];

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
    include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');
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
  $ret['tags'] = new PwgNamedArray($related_tags, 'tag', array('id','url_name','url','page_url') );
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

function ws_images_add($params, &$service)
{
  global $conf;
  
  // name
  // category_id
  // file_content
  // file_sum
  // thumbnail_content
  // thumbnail_sum
  
  // $fh_log = fopen('/tmp/php.log', 'w');
  // fwrite($fh_log, time()."\n");
  // fwrite($fh_log, 'input:  '.$params['file_sum']."\n");
  // fwrite($fh_log, 'input:  '.$params['thumbnail_sum']."\n");

  // current date
  list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));
  list($year, $month, $day) = preg_split('/[^\d]/', $dbnow, 4);
  
  $upload_dir = sprintf(
    PHPWG_ROOT_PATH.'upload/%s/%s/%s',
    $year,
    $month,
    $day
    );

  fwrite($fh_log, $upload_dir."\n");
  
  if (!is_dir($upload_dir)) {
    umask(0000);
    $recursive = true;
    mkdir($upload_dir, 0777, $recursive);
  }

  $date_string = preg_replace('/[^\d]/', '', $dbnow);
  $random_string = substr($params['file_sum'], 0, 8);

  $filename_wo_ext = $date_string.'-'.$random_string;
  
  $file_path = $upload_dir.'/'.$filename_wo_ext.'.jpg';
  $fh_file = fopen($file_path, 'w');
  fwrite($fh_file, base64_decode($params['file_content']));
  fclose($fh_file);

  // check dumped file md5sum with expected md5sum

  $thumbnail_dir = $upload_dir.'/thumbnail';
  if (!is_dir($thumbnail_dir)) {
    umask(0000);
    mkdir($thumbnail_dir, 0777);
  }
  
  $thumbnail_path = sprintf(
    '%s/%s%s.%s',
    $thumbnail_dir,
    $conf['prefix_thumbnail'],
    $filename_wo_ext,
    'jpg'
    );
  $fh_thumbnail = fopen($thumbnail_path, 'w');
  fwrite($fh_thumbnail, base64_decode($params['thumbnail_content']));
  fclose($fh_thumbnail);

  // check dumped thumbnail md5

  // fwrite($fh_log, 'output: '.md5_file($file_path)."\n");
  // fwrite($fh_log, 'output: '.md5_file($thumbnail_path)."\n");

  // database registration
  $insert = array(
    'file' => $filename_wo_ext.'.jpg',
    'date_available' => $dbnow,
    'tn_ext' => 'jpg',
    'name' => $params['name'],
    'path' => $file_path,
    );

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  mass_inserts(
    IMAGES_TABLE,
    array_keys($insert),
    array($insert)
    );

  $image_id = mysql_insert_id();

  $insert = array(
    'category_id' => $params['category_id'],
    'image_id'=> $image_id,
    );
  mass_inserts(
    IMAGE_CATEGORY_TABLE,
    array_keys($insert),
    array($insert)
    );
  
  // fclose($fh_log);
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
  global $user, $conf;
  if (!is_a_guest())
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
  return array('tags' => new PwgNamedArray($tags, 'tag', array('id','url_name','url', 'counter' )) );
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
    $where_clauses[] = ws_addControls( 'tags.getImages', $params, 'i.' );

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


/**
 * expand_id_list($ids) convert a human list expression to a full ordered list
 * example : expand_id_list( array(5,2-3,2) ) returns array( 2, 3, 5)
 * */
function expand_id_list($ids)
{
  $tid = array();
  foreach ( $ids as $id )
  {
    if ( is_numeric($id) )
    {
      $tid[] = (int) $id;
    }
    else
    {
      $range = explode( '-', $id );
      if ( is_numeric($range[0]) and is_numeric($range[1]) )
      {
        $from = min($range[0],$range[1]);
        $to = max($range[0],$range[1]);
        for ($i = $from; $i <= $to; $i++)
        {
          $tid[] = (int) $i;
        }
      }
    }
  }
  $result = array_unique ($tid); // remove duplicates...
  sort ($result);
  return $result;
}


/**
 * converts a cat-ids array in image-ids array
 * FIXME Function which should already exist somewhere else
 * */
function get_image_ids_for_cats($cat_ids)
{
  $cat_list = implode(',', $cat_ids);
  $ret_ids = array();
  $query = '
  SELECT DISTINCT image_id
    FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id in ('.$cat_list.')
  ;';
  return array_from_query($query, 'image_id');
}

?>
