<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * Returns all people for ws functions.
 * helper func for people ws function not used directly in ws.php
 *
 * @param array $params
 *  - string search (optional)
 *  - is_visible (optional)
 *  - per_page
 *  - page
 * @return array [id, name, url_name]
 */
function ws_get_people(array $params)
{
  $where_clauses = array();
  
  // build where clauses
  if (isset($params['is_visible']))
  {
    $where_clauses[] = 'p.is_visible = \'' . boolean_to_string($params['is_visible']) . '\'';
  }

  if (!empty($params['search']))
  {
    $where_clauses[] = 'p.name LIKE \'%'.pwg_db_real_escape_string($params['search']).'%\'';
  }

  $where_sql = count($where_clauses) > 0
    ? 'WHERE ' . implode(' AND ', $where_clauses)
    : '';

  // build pagination
  $per_page = intval($params['per_page']);
  $page = intval($params['page']);
  $offset = $page * $per_page;

  // queries
  $query = '
SELECT 
    p.*,
    COUNT(ip.image_id) AS photos
  FROM '.PERSONS_TABLE.' AS p
  LEFT JOIN '.IMAGE_PERSON_TABLE.' AS ip ON ip.person_id = p.id
    '. $where_sql .'
  GROUP BY p.id
  ORDER BY p.id DESC
  LIMIT ' . $per_page . '
  OFFSET ' . $offset . '
;';

  $people = query2array($query);

  $query = '
SELECT COUNT(*) AS nb
  FROM ' . PERSONS_TABLE .' AS p
  '. $where_sql .'
;';
  $count_result = pwg_db_fetch_assoc(pwg_query($query));
  $total_count = intval($count_result['nb']);

  return array(
    'paging' => array(
      'page' => $page,
      'per_page' => $per_page,
      'total' => $total_count,
      'total_pages' => ceil($total_count / $per_page),
    ),
    'people' => $people,
  );
}

/**
 * API method
 * Returns a list of people
 * @param mixed[] $params
 *    @option int[] user_id (optional)
 *    @option string username (optional)
 *    @option string[] status (optional)
 *    @option int min_level (optional)
 *    @option int max_level (optional)
 *    @option int[] group_id (optional)
 *    @option int per_page
 *    @option int page
 *    @option string order
 *    @option string display
 *    @option string filter
 *    @option int[] exclude (optional)
 *    @option string min_register
 *    @option string max_register
 */
function ws_people_getList($params, &$service)
{
}

/**
 * API method
 * Returns the list of people as you can see them in administration
 * @param mixed[] $params
 *
 * Only admin can run this method and permissions are not taken into
 * account.
 */
function ws_people_getAdminList($params, &$service)
{
  $people = ws_get_people($params);

  $people['total_people'] = get_total_people();

  return $people;
}

/**
 * API method
 * Add a person
 * @param mixed[] $params
 *    @option string name
 *    @option bool is_visible
 */
function ws_people_add($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if (!validate_people_source($params['source']))
  {
    return new PwgError(403, 'source must be: manual, auto or imported.');
  }

  if ('' === trim(strip_tags($params['name'])))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'name must not be empty.');
  }

  $new_person = create_person(
    $params['name'],
    null, // not a created_by user_id but associated with user_id
    $params['is_visible'],
    $params['source']
  );

  return array(
    'id' => $new_person['id'],
  );
}

/**
 * API method
 * Edit a person
 * @param mixed[] $params
 *    @option int id
 *    @option string name
 */
function ws_people_edit($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }
  
  $is_edited = edit_person($params['person_id'], $params);

  if (isset($is_edited['error']))
  {
    return new PwgError($is_edited['code'], $is_edited['error']);
  }

  return array(
    'id' => $params['person_id'],
  );
}

/**
 * API method
 * Delete a person
 * @param mixed[] $params
 *    @option int id
 */
function ws_people_delete($params, &$service)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $people_ids = $params['people_ids'];

  $query = '
SELECT COUNT(*)
  FROM `'. PERSONS_TABLE .'`
  WHERE id in ('.implode(',', $people_ids) .')
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count != count($people_ids))
  {
    return new PwgError(401, 'All people does not exist.');
  }



  if (count($people_ids) > 0)
  {
    delete_people($people_ids);
    return array( 'id' => $people_ids );
  } else {
    return array( 'id' => array() );
  }
}

/**
 * API method
 * Associate a person to an image, or update the box of the association
 * @param mixed[] $params
 *    @option int image_id
 *    @option int person_id
 *    @option int box_x1, box_y1, box_x2, box_y2, image_width, image_height (optional, all or none)
 *    @option string source
 */
function ws_people_associate($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $association = associate_person_to_image($params['image_id'], $params['person_id'], $params);

  if (isset($association['error']))
  {
    return new PwgError($association['code'], $association['error']);
  }

  return array(
    'image_person_id' => $association['id'],
    'created' => $association['created'],
  );
}

/**
 * API method
 * Dissociate a person from an image
 * @param mixed[] $params
 *    @option int image_person_id
 */
function ws_people_dissociate($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $is_dissociated = dissociate_person_from_image($params['image_person_id']);

  if (isset($is_dissociated['error']))
  {
    return new PwgError($is_dissociated['code'], $is_dissociated['error']);
  }

  return array(
    'image_person_id' => $params['image_person_id'],
  );
}

/**
 * API method
 * Returns the people of an image
 * @param mixed[] $params
 *    @option int image_id
 */
function ws_people_getByImage($params, &$service)
{
  return array(
    'people' => get_image_people($params['image_id']),
  );
}

/**
 * API method
 * Returns the images of a person
 * @param mixed[] $params
 *    @option int person_id
 *    @option int per_page
 *    @option int page
 */
function ws_people_getImages($params, &$service)
{
  $images = get_person_images($params['person_id'], $params['per_page'], $params['page']);

  if (isset($images['error']))
  {
    return new PwgError($images['code'], $images['error']);
  }

  return $images;
}

/**
 * API method
 * Replace the people of an image
 * @param mixed[] $params
 *    @option int image_id
 *    @option string people JSON list of [person_id, box_x1, box_y1, box_x2,
 *      box_y2, image_width, image_height, is_cover]
 */
function ws_people_setImagePeople($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $people = json_decode(stripslashes($params['people']), true);
  if (!is_array($people))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'people must be a JSON list.');
  }

  $is_set = set_image_people($params['image_id'], $people);

  if (isset($is_set['error']))
  {
    return new PwgError($is_set['code'], $is_set['error']);
  }

  return array(
    'people' => get_image_people($params['image_id']),
  );
}