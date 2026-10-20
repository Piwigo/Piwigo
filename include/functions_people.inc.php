<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

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
  $user_id_sql = $user_id === null ? 'NULL' : $user_id;
  $name = strip_tags($name);
  $query = '
INSERT INTO '.PERSONS_TABLE.'
  (name, url_name, user_id, is_visible, source)
  VALUES (
    \''.pwg_db_real_escape_string($name).'\',
    \''.pwg_db_real_escape_string(trigger_change('render_person_url', $name)).'\',
    '.$user_id_sql.',
    \''.boolean_to_string($is_visible).'\',
    \''.pwg_db_real_escape_string($source).'\'
  )
;';

  pwg_query($query);

  $new_person_id = pwg_db_insert_id();
  pwg_activity('people', $new_person_id, 'add');
  trigger_notify('create_person', $new_person_id);
  return array(
    'id' => $new_person_id,
  );
}

function edit_person(int $id, array $data)
{
  $validated_data = [];
  $person = get_person_by_id($id);

  if (!$person)
  {
    return array('code' => 404, 'error' => 'Person not found.');
  }

  // name
  if (!empty($data['name']))
  {
    $name = strip_tags($data['name']);
    $validated_data['name'] = pwg_db_real_escape_string($name);
    $validated_data['url_name'] = pwg_db_real_escape_string(trigger_change('render_person_url', $name));
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

  // user_id, 0 dissociates the user
  if (isset($data['user_id']))
  {
    if (!preg_match('/^\d+$/', $data['user_id']))
    {
      return array('code' => 403, 'error' => 'user_id must be a digit.');
    }

    if (0 == $data['user_id'])
    {
      $validated_data['user_id'] = null;
    }
    else if ($person['user_id'] != $data['user_id'])
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

    $cover = get_person_in_image_by_id($data['cover_image_person_id']);
    if (!$cover)
    {
      return array('code' => 404, 'error' => 'Person in image not found.');
    }

    if ($cover['person_id'] != $id)
    {
      return array('code' => 403, 'error' => 'The cover must be an image of this person.');
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

  pwg_activity('people', $person['id'], 'edit');
  trigger_notify('edit_person', get_person_by_id($id), $person);
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

  trigger_notify('delete_people', $people_ids);
  pwg_activity('people', $people_ids, 'delete');
  update_images_lastmodified($image_ids);

  return true;
}

function merge_people() {}

/**
 * Associate a person to an image, or update the box of the existing
 * association. The source is set on creation only.
 *
 * @param int $image_id
 * @param int $person_id
 * @param array $data (all optional)
 *  - int box_x1, box_y1, box_x2, box_y2, image_width, image_height (all or none)
 *  - string source manual|auto|imported, default manual
 * @return array ['id' => image_person_id, 'created' => bool] or ['code', 'error']
 */
function associate_person_to_image(int $image_id, int $person_id, array $data = array())
{
  global $user;

  if (!get_person_by_id($person_id))
  {
    return array('code' => 404, 'error' => 'Person not found.');
  }

  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
  if (pwg_db_num_rows(pwg_query($query)) == 0)
  {
    return array('code' => 404, 'error' => 'Image not found.');
  }

  // source
  $source = $data['source'] ?? 'manual';
  if (!validate_people_source($source))
  {
    return array('code' => 403, 'error' => 'source must be: manual, auto or imported.');
  }

  $validated_data = validate_image_person_box($data);
  if (isset($validated_data['error']))
  {
    return $validated_data;
  }

  $query = '
SELECT *
  FROM '.IMAGE_PERSON_TABLE.'
  WHERE image_id = '.$image_id.'
    AND person_id = '.$person_id.'
;';
  $existing = pwg_db_fetch_assoc(pwg_query($query));

  if ($existing)
  {
    // already associated, nothing to update
    if (count($validated_data) == 0)
    {
      return array(
        'id' => $existing['id'],
        'created' => false,
      );
    }

    single_update(
      IMAGE_PERSON_TABLE,
      $validated_data,
      array('id' => $existing['id'])
    );
    $image_person_id = $existing['id'];
  }
  else
  {
    single_insert(
      IMAGE_PERSON_TABLE,
      array_merge(
        $validated_data,
        array(
          'image_id' => $image_id,
          'person_id' => $person_id,
          'source' => $source,
          'tagger_user_id' => $user['id'],
        )
      )
    );
    $image_person_id = pwg_db_insert_id();
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  update_images_lastmodified(array($image_id));

  $created = !$existing;
  trigger_notify('associate_person_to_image', get_person_in_image_by_id($image_person_id), $created);

  return array(
    'id' => $image_person_id,
    'created' => $created,
  );
}

/**
 * Delete an association. If it was the cover of the person, the cover is unset.
 *
 * @param int $image_person_id
 * @return true|array ['code', 'error']
 */
function dissociate_person_from_image(int $image_person_id)
{
  $image_person = get_person_in_image_by_id($image_person_id);
  if (!$image_person)
  {
    return array('code' => 404, 'error' => 'Person in image not found.');
  }

  $query = '
DELETE
  FROM '.IMAGE_PERSON_TABLE.'
  WHERE id = '.$image_person_id.'
;';
  pwg_query($query);

  unset_people_cover(array($image_person_id));

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  update_images_lastmodified(array($image_person['image_id']));

  trigger_notify('dissociate_person_from_image', $image_person);
  return true;
}

/**
 * Replace the people of an image, like set_tags. Everything is checked
 * before writing, so an invalid list changes nothing.
 *
 * @param int $image_id
 * @param array $people list of [person_id, box_x1, box_y1, box_x2, box_y2,
 *   image_width, image_height, is_cover], box fields null for no box
 * @return true|array ['code', 'error']
 */
function set_image_people(int $image_id, array $people)
{
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
  if (pwg_db_num_rows(pwg_query($query)) == 0)
  {
    return array('code' => 404, 'error' => 'Image not found.');
  }

  // check everything before writing
  $person_ids = array();
  $cover_person_id = null;
  foreach ($people as $person)
  {
    if (!is_array($person) or !isset($person['person_id']) or !preg_match('/^\d+$/', $person['person_id']))
    {
      return array('code' => 403, 'error' => 'person_id must be a digit.');
    }

    if (in_array($person['person_id'], $person_ids))
    {
      return array('code' => 403, 'error' => 'A person is given twice.');
    }

    $box = validate_image_person_box($person);
    if (isset($box['error']))
    {
      return $box;
    }

    if (isset($person['is_cover']) and in_array($person['is_cover'], array(true, 'true'), true))
    {
      if (null !== $cover_person_id)
      {
        return array('code' => 403, 'error' => 'Only one cover by image.');
      }
      $cover_person_id = $person['person_id'];
    }

    $person_ids[] = $person['person_id'];
  }

  if (count($person_ids) > 0)
  {
    $query = '
SELECT COUNT(*)
  FROM '.PERSONS_TABLE.'
  WHERE id IN ('.implode(',', $person_ids).')
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != count($person_ids))
    {
      return array('code' => 404, 'error' => 'Person not found.');
    }
  }

  // dissociate the people no longer on the image
  $query = '
SELECT
    person_id,
    id
  FROM '.IMAGE_PERSON_TABLE.'
  WHERE image_id = '.$image_id.'
;';
  $existing = query2array($query, 'person_id', 'id');

  foreach ($existing as $person_id => $image_person_id)
  {
    if (!in_array($person_id, $person_ids))
    {
      dissociate_person_from_image($image_person_id);
    }
  }

  // associate the new people, update the box of the others
  $image_person_ids = array();
  foreach ($people as $person)
  {
    $association = associate_person_to_image($image_id, $person['person_id'], $person);
    if (isset($association['error']))
    {
      return $association;
    }
    $image_person_ids[$person['person_id']] = $association['id'];
  }

  // the people whose cover was on this image lose it, except the new cover
  $covers_to_unset = $image_person_ids;
  if (null !== $cover_person_id)
  {
    unset($covers_to_unset[$cover_person_id]);
  }
  unset_people_cover(array_values($covers_to_unset));

  if (null !== $cover_person_id)
  {
    $person = get_person_by_id($cover_person_id);
    if ($person['cover_image_person_id'] != $image_person_ids[$cover_person_id])
    {
      edit_person($cover_person_id, array('cover_image_person_id' => $image_person_ids[$cover_person_id]));
    }
  }

  return true;
}

/**
 * Returns the people of an image, with the association fields.
 *
 * @param int $image_id
 * @return array
 */
function get_image_people(int $image_id)
{
  $query = '
SELECT
    ip.id AS image_person_id,
    ip.person_id,
    p.name,
    p.url_name,
    p.is_visible,
    ip.box_x1,
    ip.box_y1,
    ip.box_x2,
    ip.box_y2,
    ip.image_width,
    ip.image_height,
    ip.source,
    ip.tagged_on,
    ip.tagger_user_id,
    IF(p.cover_image_person_id = ip.id, \'true\', \'false\') AS is_cover
  FROM '.IMAGE_PERSON_TABLE.' AS ip
    INNER JOIN '.PERSONS_TABLE.' AS p ON p.id = ip.person_id
  WHERE ip.image_id = '.$image_id.'
  ORDER BY p.name
;';

  return query2array($query);
}

/**
 * Returns the images of a person, with the association fields and the
 * thumbnail url. Admin view: photo permissions are not taken into account.
 *
 * @param int $person_id
 * @param int $per_page
 * @param int $page starts at 0
 * @return array ['paging', 'images'] or ['code', 'error']
 */
function get_person_images(int $person_id, int $per_page = 100, int $page = 0)
{
  if (!get_person_by_id($person_id))
  {
    return array('code' => 404, 'error' => 'Person not found.');
  }

  $per_page = max(1, $per_page);
  $page = max(0, $page);

  $where_sql = '
  WHERE ip.person_id = '.$person_id;

  $query = '
SELECT
    i.*,
    ip.id AS image_person_id,
    ip.box_x1,
    ip.box_y1,
    ip.box_x2,
    ip.box_y2,
    ip.image_width,
    ip.image_height,
    ip.source,
    ip.tagged_on
  FROM '.IMAGE_PERSON_TABLE.' AS ip
    INNER JOIN '.IMAGES_TABLE.' AS i ON i.id = ip.image_id
  '.$where_sql.'
  ORDER BY ip.tagged_on DESC, ip.id DESC
  LIMIT '.$per_page.'
  OFFSET '.($per_page * $page).'
;';
  $result = pwg_query($query);

  $images = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $images[] = array(
      'id' => $row['id'],
      'name' => $row['name'],
      'file' => $row['file'],
      'width' => $row['width'],
      'height' => $row['height'],
      'tn_url' => DerivativeImage::thumb_url($row),
      'image_person_id' => $row['image_person_id'],
      'box_x1' => $row['box_x1'],
      'box_y1' => $row['box_y1'],
      'box_x2' => $row['box_x2'],
      'box_y2' => $row['box_y2'],
      'image_width' => $row['image_width'],
      'image_height' => $row['image_height'],
      'source' => $row['source'],
      'tagged_on' => $row['tagged_on'],
    );
  }

  $query = '
SELECT COUNT(*)
  FROM '.IMAGE_PERSON_TABLE.' AS ip
    INNER JOIN '.IMAGES_TABLE.' AS i ON i.id = ip.image_id
  '.$where_sql.'
;';
  list($total) = pwg_db_fetch_row(pwg_query($query));

  return array(
    'paging' => array(
      'page' => $page,
      'per_page' => $per_page,
      'total' => $total,
      'total_pages' => ceil($total / $per_page),
    ),
    'images' => $images,
  );
}

/**
 * Unset the cover of the people whose cover is one of these associations.
 *
 * @param int[] $image_person_ids
 */
function unset_people_cover(array $image_person_ids)
{
  if (count($image_person_ids) == 0)
  {
    return;
  }

  $query = '
UPDATE '.PERSONS_TABLE.'
  SET cover_image_person_id = NULL
  WHERE cover_image_person_id IN ('.implode(',', $image_person_ids).')
;';
  pwg_query($query);
}

// utils

/**
 * Validate a box, in pixels of the image at image_width x image_height.
 * box_x1, box_y1, box_x2, box_y2, image_width and image_height: all or none.
 *
 * @param array $data
 * @return array the box, empty if there is none, or ['code', 'error']
 */
function validate_image_person_box(array $data)
{
  $box_keys = array('box_x1', 'box_y1', 'box_x2', 'box_y2', 'image_width', 'image_height');
  $box = array();
  foreach ($box_keys as $key)
  {
    if (isset($data[$key]))
    {
      if (!preg_match('/^\d+$/', $data[$key]) or $data[$key] > 65535)
      {
        return array('code' => 403, 'error' => $key.' must be an integer between 0 and 65535.');
      }
      $box[$key] = $data[$key];
    }
  }

  if (count($box) == 0)
  {
    return $box;
  }

  if (count($box) != count($box_keys))
  {
    return array('code' => 403, 'error' => 'box needs box_x1, box_y1, box_x2, box_y2, image_width and image_height.');
  }

  if ($box['box_x1'] >= $box['box_x2']
    or $box['box_y1'] >= $box['box_y2']
    or $box['box_x2'] > $box['image_width']
    or $box['box_y2'] > $box['image_height'])
  {
    return array('code' => 403, 'error' => 'box must be inside the image, with x1 < x2 and y1 < y2.');
  }

  return $box;
}

function validate_people_source(string $source)
{
  if (!in_array($source, ['manual', 'imported', 'auto']))
  {
    return false;
  }

  return true;
}