<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Returns the list of groups
 * @param mixed[] $params
 *    @option int[] group_id (optional)
 *    @option string name (optional)
 */
function ws_groups_getList($params, &$service)
{
  $where_clauses = array('1=1');

  if (!empty($params['name']))
  {
    $where_clauses[] = 'LOWER(name) LIKE \''. pwg_db_real_escape_string($params['name']) .'\'';
  }

  if (!empty($params['group_id']))
  {
    $where_clauses[] = 'id IN('. implode(',', $params['group_id']) .')';
  }

  $query = '
SELECT
    g.*, COUNT(user_id) AS nb_users
  FROM `'. GROUPS_TABLE .'` AS g
    LEFT JOIN '. USER_GROUP_TABLE .' AS ug
    ON ug.group_id = g.id
  WHERE '. implode(' AND ', $where_clauses) .'
  GROUP BY id
  ORDER BY '. $params['order'] .'
  LIMIT '. $params['per_page'] .'
  OFFSET '. ($params['per_page']*$params['page']) .'
;';

  $groups = array_from_query($query);

  return array(
    'paging' => new PwgNamedStruct(array(
      'page' => $params['page'],
      'per_page' => $params['per_page'],
      'count' => count($groups)
      )),
    'groups' => new PwgNamedArray($groups, 'group')
    );
}

/**
 * API method
 * Adds a group
 * @param mixed[] $params
 *    @option string name
 *    @option bool is_default
 */
function ws_groups_add($params, &$service)
{
  $params['name'] = pwg_db_real_escape_string(strip_tags(stripslashes($params['name'])));

  // is the name not already used ?
  $query = '
SELECT COUNT(*)
  FROM `'.GROUPS_TABLE.'`
  WHERE name = \''.$params['name'].'\'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count != 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This name is already used by another group.');
  }

  if (strlen(str_replace( " ", "", $params['name'])) == 0) {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Name field must not be empty');
  }

  // creating the group
  single_insert(
    GROUPS_TABLE,
    array(
      'name' => $params['name'],
      'is_default' => boolean_to_string($params['is_default']),
      )
    );
  $inserted_id = pwg_db_insert_id();

  pwg_activity('group', $inserted_id, 'add');

  return $service->invoke('pwg.groups.getList', array('group_id' => $inserted_id));
}

/**
 * API method
 * Deletes a group
 * @param mixed[] $params
 *    @option int[] group_id
 *    @option string pwg_token
 */
function ws_groups_delete($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  $groupnames = array_values(delete_groups($params['group_id']));
  
  invalidate_user_cache();

  return new PwgNamedArray($groupnames, 'group_deleted');
}

/**
 * API method
 * Updates a group
 * @param mixed[] $params
 *    @option int group_id
 *    @option string name (optional)
 *    @option bool is_default (optional)
 */
function ws_groups_setInfo($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if (isset($params['name']) && strlen(str_replace( " ", "",  $params['name'])) == 0) {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Name field must not be empty');
  }

  $updates = array();

  // does the group exist ?
  $query = '
SELECT COUNT(*)
  FROM `'. GROUPS_TABLE .'`
  WHERE id = '. $params['group_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This group does not exist.');
  }

  if (!empty($params['name']))
  {
    $params['name'] = pwg_db_real_escape_string(strip_tags(stripslashes($params['name'])));

    // is the name not already used ?
    $query = '
SELECT COUNT(*)
  FROM `'. GROUPS_TABLE .'`
  WHERE name = \''. $params['name'] .'\'
  AND id != '.$params['group_id'].'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'This name is already used by another group.');
    }

    $updates['name'] = $params['name'];
  }

  if (!empty($params['is_default']) or @$params['is_default']===false)
  {
    $updates['is_default'] = boolean_to_string($params['is_default']);
  }

  single_update(
    GROUPS_TABLE,
    $updates,
    array('id' => $params['group_id'])
    );

  pwg_activity('group', $params['group_id'], 'edit');

  return $service->invoke('pwg.groups.getList', array('group_id' => $params['group_id']));
}

/**
 * API method
 * Adds user(s) to a group
 * @param mixed[] $params
 *    @option int group_id
 *    @option int[] user_id
 */
function ws_groups_addUser($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  // does the group exist ?
  $query = '
SELECT COUNT(*)
  FROM `'. GROUPS_TABLE .'`
  WHERE id = '. $params['group_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This group does not exist.');
  }

  $inserts = array();
  foreach ($params['user_id'] as $user_id)
  {
    $inserts[] = array(
      'group_id' => $params['group_id'],
      'user_id' => $user_id,
      );
  }

  mass_inserts(
    USER_GROUP_TABLE,
    array('group_id', 'user_id'),
    $inserts
    );

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  invalidate_user_cache();

  pwg_activity('group', $params['group_id'], 'edit');
  pwg_activity('user', $params['user_id'], 'edit');

  return $service->invoke('pwg.groups.getList', array('group_id' => $params['group_id']));
}

/**
 * API method
 * Merge groups in one other group
 * @param mixed[] $params
 *    @option int destination_group_id
 *    @option int[] merge_group_id
 */
function ws_groups_merge($params, &$service) {

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $all_groups = $params['merge_group_id'];
  array_push($all_groups, $params['destination_group_id']);

  $all_groups = array_unique($all_groups);
  $merge_group = array_diff($params['merge_group_id'], array($params['destination_group_id']));
  $merge_group_object = $service->invoke('pwg.groups.getList', array('group_id' => $params['merge_group_id']));

  $query = '
SELECT COUNT(*)
  FROM `'. GROUPS_TABLE .'`
  WHERE id in ('.implode(',', $all_groups) .')
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count != count($all_groups))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'All groups does not exist.');
  }

  $user_in_merge_groups = array();
  $user_in_dest = array();
  $user_to_add = array();

  $query = '
SELECT DISTINCT(user_id) 
  FROM `'. USER_GROUP_TABLE .'` 
  WHERE 
    group_id IN ('.implode(',', $merge_group) .')
;';
  $user_in_merge_groups = query2array($query, null, 'user_id');

  $query = '
SELECT user_id 
  FROM `'. USER_GROUP_TABLE .'` 
  WHERE group_id = '.$params['destination_group_id'].'
;';

  $user_in_dest = query2array($query, null, 'user_id');;

  
  $user_to_add = array_diff($user_in_merge_groups, $user_in_dest);

  $inserts = array();
  foreach ($user_to_add as $user)
  {
    $inserts[] = array(
      'group_id' => $params['destination_group_id'],
      'user_id' => $user,
      );
  }

  mass_inserts(
    USER_GROUP_TABLE,
    array('group_id', 'user_id'),
    $inserts,
    array('ignore'=>true)
    );

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  invalidate_user_cache();

  pwg_activity('group', $params['destination_group_id'], 'edit');
  foreach ($user_to_add as $user_id) 
  {
    pwg_activity('user', $user_id, 'edit', array("associated" => $params['destination_group_id']));
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  delete_groups($merge_group);

  return array(
    "destination_group" => $service->invoke('pwg.groups.getList', array('group_id' => $params['destination_group_id'])),
    "deleted_group" => $merge_group_object
  );
}

/**
 * API method
 * Create a copy of a group
 * @param mixed[] $params
 *    @option int group_id
 *    @option string copy_name
 */
function ws_groups_duplicate($params, &$service) {

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $query = '
SELECT COUNT(*)
  FROM `'.GROUPS_TABLE.'`
  WHERE name = \''.pwg_db_real_escape_string($params['copy_name']).'\'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count != 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This name is already used by another group.');
  }

  $query = '
SELECT COUNT(*)
  FROM `'. GROUPS_TABLE .'`
  WHERE id = '.$params["group_id"].'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This group does not exist.');
  }

  $query = '
SELECT is_default 
  FROM `'. GROUPS_TABLE .'` 
  WHERE id = '.$params['group_id'].'
;';

  list($is_default) = pwg_db_fetch_row(pwg_query($query));

  // creating the group
  single_insert(
    GROUPS_TABLE,
    array(
      'name' => $params['copy_name'],
      'is_default' => boolean_to_string($is_default),
      )
    );
  $inserted_id = pwg_db_insert_id();

  pwg_activity('group', $inserted_id, 'add');

  $query = '
  SELECT user_id 
    FROM `'. USER_GROUP_TABLE .'` 
    WHERE group_id = '.$params['group_id'].'
  ;';
  
  $users = query2array($query, null, 'user_id');

  $inserts = array();
  foreach ($users as $user)
  {
    $inserts[] = array(
      'group_id' => $inserted_id,
      'user_id' => $user,
      );
  }

  mass_inserts(
    USER_GROUP_TABLE,
    array('group_id', 'user_id'),
    $inserts,
    array('ignore'=>true)
  );

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  invalidate_user_cache();

  foreach ($users as $user_id) 
  {
    pwg_activity('user', $user_id, 'edit', array("associated" => $params['group_id']));
  }

  return $service->invoke('pwg.groups.getList', array('group_id' => $inserted_id));
}

/**
 * API method
 * Removes user(s) from a group
 * @param mixed[] $params
 *    @option int group_id
 *    @option int[] user_id
 */
function ws_groups_deleteUser($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  // does the group exist ?
  $query = '
SELECT COUNT(*)
  FROM `'. GROUPS_TABLE .'`
  WHERE id = '. $params['group_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This group does not exist.');
  }

  $query = '
DELETE FROM '. USER_GROUP_TABLE .'
  WHERE
    group_id = '. $params['group_id'] .'
    AND user_id IN('. implode(',', $params['user_id']) .')
;';
  pwg_query($query);

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  invalidate_user_cache();

  pwg_activity('group', $params['group_id'], 'edit');
  pwg_activity('user', $params['user_id'], 'edit');

  return $service->invoke('pwg.groups.getList', array('group_id' => $params['group_id']));
}

?>