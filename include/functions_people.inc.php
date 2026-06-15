<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\tag
 */


/**
 * Returns all people for ws functions.
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
  if (!empty($params['is_visible']))
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

function get_person_by_id(int $id)
{
  $query = '
SELECT *
  FROM '.PERSONS_TABLE.'
  WHERE id = '.$id.'
;';

  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    return null;
  }

  return pwg_db_fetch_assoc($result);
}

function get_person_by_user_id(int $user_id)
{
  $query = '
SELECT *
  FROM '.PERSONS_TABLE.'
  WHERE user_id = '.$user_id.'
;';

  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    return null;
  }

  return pwg_db_fetch_assoc($result);
}

function get_person_in_image_by_id(int $id)
{
  $query = '
SELECT *
  FROM '.IMAGE_PERSON_TABLE.'
  WHERE id = '.$id.'
;';

  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    return null;
  }

  return pwg_db_fetch_assoc($result);
}

function get_total_people()
{
  $query = '
SELECT COUNT(*) as nb
  FROM '.PERSONS_TABLE.'
;';
  $count_result = pwg_db_fetch_assoc(pwg_query($query));

  return intval($count_result['nb']);
}

/**
 * Create a person.
 */
function create_person(string $name, ?int $user_id = null, bool $is_visible = true, string $source = 'manual')
{
  global $user;

  $user_id_sql = $user_id === null ? 'NULL' : $user_id;
  $query = '
INSERT INTO '.PERSONS_TABLE.'
  (name, user_id, is_visible, source)
  VALUES (
    \''.pwg_db_real_escape_string(strip_tags($name)).'\',
    '.$user_id_sql.',
    \''.boolean_to_string($is_visible).'\',
    \''.pwg_db_real_escape_string($source).'\'
  )
;';

  pwg_query($query);

  $new_person_id = pwg_db_insert_id();
  pwg_activity('people', $user['id'], 'add', ['person_id' => $new_person_id]);
  return array(
    'id' => $new_person_id,
  );
}

function edit_person(int $id, array $data)
{
  global $user;

  $validated_data = [];
  $person = get_person_by_id($id);

  if (!$person)
  {
    return array('code' => 404, 'error' => 'Person not found.');
  }

  // name
  if (!empty($data['name']))
  {
    $validated_data['name'] = pwg_db_real_escape_string(strip_tags($data['name']));
  }

  // is_visible
  if (isset($data['is_visible']))
  {

    if (!is_bool($data['is_visible']))
    {
      return array('code' => 403, 'error' => 'is_visible must be: true or false.');
    }

    $validated_data['is_visible'] = boolean_to_string($data['is_visible']);
  }

  // source
  if (!empty($data['source']))
  {
    if (!validate_people_source($data['source']))
    {
      return array('code' => 403, 'error' => 'source must be: manual, auto or imported.');
    }
    $validated_data['source'] = $data['source'];
  }

  // user_id
  // TODO: deplace this in method like associate / deassociate person <=> user
  if (!empty($data['user_id']))
  {
    if (!preg_match('/^\d+$/', $data['user_id']))
    {
      return array('code' => 403, 'error' => 'user_id must be a digit.');
    }

    if ($person['user_id'] != $data['user_id'])
    {
      $is_user_already_associated = get_person_by_user_id($data['user_id']);

      if ($is_user_already_associated)
      {
        return array('code' => 401, 'error' => 'This user is already associated with a person.');
      }

      $validated_data['user_id'] = $data['user_id'];  
    }
  }

  // cover_image_person_id
  if (!empty($data['cover_image_person_id']))
  {
    if (!preg_match('/^\d+$/', $data['cover_image_person_id']))
    {
      return array('code' => 403, 'error' => 'cover_image_person_id must be a digit.');
    }

    $cover_exist = get_person_in_image_by_id($data['cover_image_person_id']);
    if (!$cover_exist)
    {
      return array('code' => 404, 'error' => 'Person in image not found.');
    }

    $validated_data['cover_image_person_id'] = $data['cover_image_person_id'];
  }

  // save data
  if (count($validated_data) === 0)
  {
    return true;
  }

  single_update(
    PERSONS_TABLE,
    $validated_data,
    array('id' => $id)
  );

  pwg_activity('people', $user['id'], 'edit', ['person_id' => $person['id']]);
  return true;
}

/**
 * @param int[]|int $people_ids
 * @return bool
 */
function delete_people($people_ids)
{
  if (is_numeric($people_ids))
  {
    $people_ids = array($people_ids);
  }

  if (!is_array($people_ids))
  {
    return false;
  }

  // we need the list of impacted images, to update their lastmodified like tags
  $query = '
SELECT
    image_id
  FROM '.IMAGE_PERSON_TABLE.'
  WHERE person_id IN ('.implode(',', $people_ids).')
;';
  $image_ids = query2array($query, null, 'image_id');

  $query = '
DELETE
  FROM '.IMAGE_PERSON_TABLE.'
  WHERE person_id IN ('.implode(',', $people_ids).')
;';
  pwg_query($query);

  $query = '
DELETE
  FROM '.PERSONS_TABLE.'
  WHERE id IN ('.implode(',', $people_ids).')
;';
  pwg_query($query);

  pwg_activity('tag', $people_ids, 'delete');
  update_images_lastmodified($image_ids);

  return true;
}

function merge_people() {}

// utils
function validate_people_source(string $source)
{
  if (!in_array($source, ['manual', 'imported', 'auto']))
  {
    return false;
  }

  return true;
}