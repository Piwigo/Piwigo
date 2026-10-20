<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

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