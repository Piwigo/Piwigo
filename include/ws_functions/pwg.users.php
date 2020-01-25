<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Returns a list of users
 * @param mixed[] $params
 *    @option int[] user_id (optional)
 *    @option string username (optional)
 *    @option string[] status (optional)
 *    @option int min_level (optional)
 *    @option int[] group_id (optional)
 *    @option int per_page
 *    @option int page
 *    @option string order
 *    @option string display
 */
function ws_users_getList($params, &$service)
{
  global $conf;

  $where_clauses = array('1=1');

  if (!empty($params['user_id']))
  {
    $where_clauses[] = 'u.'.$conf['user_fields']['id'].' IN('. implode(',', $params['user_id']) .')';
  }

  if (!empty($params['username']))
  {
    $where_clauses[] = 'u.'.$conf['user_fields']['username'].' LIKE \''.pwg_db_real_escape_string($params['username']).'\'';
  }

  if (!empty($params['status']))
  {
    $params['status'] = array_intersect($params['status'], get_enums(USER_INFOS_TABLE, 'status'));
    if (count($params['status']) > 0)
    {
      $where_clauses[] = 'ui.status IN("'. implode('","', $params['status']) .'")';
    }
  }

  if (!empty($params['min_level']))
  {
    if ( !in_array($params['min_level'], $conf['available_permission_levels']) )
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid level');
    }
    $where_clauses[] = 'ui.level >= '.$params['min_level'];
  }

  if (!empty($params['group_id']))
  {
    $where_clauses[] = 'ug.group_id IN('. implode(',', $params['group_id']) .')';
  }

  $display = array('u.'.$conf['user_fields']['id'] => 'id');

  if ($params['display'] != 'none')
  {
    $params['display'] = array_map('trim', explode(',', $params['display']));

    if (in_array('all', $params['display']))
    {
      $params['display'] = array(
        'username','email','status','level','groups','language','theme',
        'nb_image_page','recent_period','expand','show_nb_comments','show_nb_hits',
        'enabled_high','registration_date','registration_date_string',
        'registration_date_since', 'last_visit', 'last_visit_string',
        'last_visit_since'
        );
    }
    else if (in_array('basics', $params['display']))
    {
      $params['display'] = array_merge($params['display'], array(
        'username','email','status','level','groups',
        ));
    }
    $params['display'] = array_flip($params['display']);

    // if registration_date_string or registration_date_since is requested,
    // then registration_date is automatically added
    if (isset($params['display']['registration_date_string']) or isset($params['display']['registration_date_since']))
    {
      $params['display']['registration_date'] = true;
    }

    // if last_visit_string or last_visit_since is requested, then
    // last_visit is automatically added
    if (isset($params['display']['last_visit_string']) or isset($params['display']['last_visit_since']))
    {
      $params['display']['last_visit'] = true;
    }

    if (isset($params['display']['username']))
    {
      $display['u.'.$conf['user_fields']['username']] = 'username';
    }
    if (isset($params['display']['email']))
    {
      $display['u.'.$conf['user_fields']['email']] = 'email';
    }

    $ui_fields = array(
      'status','level','language','theme','nb_image_page','recent_period','expand',
      'show_nb_comments','show_nb_hits','enabled_high','registration_date',
      'last_visit'
      );
    foreach ($ui_fields as $field)
    {
      if (isset($params['display'][$field]))
      {
        $display['ui.'.$field] = $field;
      }
    }
  }
  else
  {
    $params['display'] = array();
  }

  $query = '
SELECT DISTINCT ';

  $first = true;
  foreach ($display as $field => $name)
  {
    if (!$first) $query.= ', ';
    else $first = false;
    $query.= $field .' AS '. $name;
  }

  if (isset($display['ui.last_visit']))
  {
    if (!$first) $query.= ', ';
    $query.= 'ui.last_visit_from_history AS last_visit_from_history';
  }

  $query.= '
  FROM '. USERS_TABLE .' AS u
    INNER JOIN '. USER_INFOS_TABLE .' AS ui
      ON u.'. $conf['user_fields']['id'] .' = ui.user_id
    LEFT JOIN '. USER_GROUP_TABLE .' AS ug
      ON u.'. $conf['user_fields']['id'] .' = ug.user_id
  WHERE
    '. implode(' AND ', $where_clauses) .'
  ORDER BY '. $params['order'] .'
  LIMIT '. $params['per_page'] .'
  OFFSET '. ($params['per_page']*$params['page']) .'
;';

  $users = array();
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['id'] = intval($row['id']);
    if (isset($params['display']['groups']))
    {
      $row['groups'] = array(); // will be filled later
    }
    $users[ $row['id'] ] = $row;
  }

  if (count($users) > 0)
  {
    if (isset($params['display']['groups']))
    {
      $query = '
SELECT user_id, group_id
  FROM '. USER_GROUP_TABLE .'
  WHERE user_id IN ('. implode(',', array_keys($users)) .')
;';
      $result = pwg_query($query);
      
      while ($row = pwg_db_fetch_assoc($result))
      {
        $users[ $row['user_id'] ]['groups'][] = intval($row['group_id']);
      }
    }
    
    if (isset($params['display']['registration_date_string']))
    {
      foreach ($users as $cur_user)
      {
        $users[$cur_user['id']]['registration_date_string'] = format_date($cur_user['registration_date'], array('day', 'month', 'year'));
      }
    }

    if (isset($params['display']['registration_date_since']))
    {
      foreach ($users as $cur_user)
      {
        $users[ $cur_user['id'] ]['registration_date_since'] = time_since($cur_user['registration_date'], 'month');
      }
    }

    if (isset($params['display']['last_visit']))
    {
      foreach ($users as $cur_user)
      {
        $last_visit = $cur_user['last_visit'];
        $users[ $cur_user['id'] ]['last_visit'] = $last_visit;

        if (!get_boolean($cur_user['last_visit_from_history']) and empty($last_visit))
        {
          $last_visit = get_user_last_visit_from_history($cur_user['id'], true);
          $users[ $cur_user['id'] ]['last_visit'] = $last_visit;
        }

        if (isset($params['display']['last_visit_string']))
        {
          $users[ $cur_user['id'] ]['last_visit_string'] = format_date($last_visit, array('day', 'month', 'year'));
        }
        
        if (isset($params['display']['last_visit_since']))
        {
          $users[ $cur_user['id'] ]['last_visit_since'] = time_since($last_visit, 'day');
        }
      }
    }
  }

  $users = trigger_change('ws_users_getList', $users);
  
  return array(
    'paging' => new PwgNamedStruct(
      array(
        'page' => $params['page'],
        'per_page' => $params['per_page'],
        'count' => count($users)
        )
      ),
    'users' => new PwgNamedArray(array_values($users), 'user')
    );
}

/**
 * API method
 * Adds a user
 * @param mixed[] $params
 *    @option string username
 *    @option string password (optional)
 *    @option string email (optional)
 */
function ws_users_add($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }
  
  global $conf;

  if ($conf['double_password_type_in_admin'])
  {
    if ($params['password'] != $params['password_confirm'])
    {
      return new PwgError(WS_ERR_INVALID_PARAM, l10n('The passwords do not match'));
    }
  }

  $user_id = register_user(
    $params['username'],
    $params['password'],
    $params['email'],
    false, // notify admin
    $errors,
    $params['send_password_by_mail']
    );

  if (!$user_id)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, $errors[0]);
  }

  return $service->invoke('pwg.users.getList', array('user_id'=>$user_id));
}

/**
 * API method
 * Get a new authentication key for a user.
 * @param mixed[] $params
 *    @option int[] user_id
 *    @option string pwg_token
 */
function ws_users_getAuthKey($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $authkey = create_user_auth_key($params['user_id']);

  if ($authkey === false)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'invalid user_id');
  }

  return $authkey;
}

/**
 * API method
 * Deletes users
 * @param mixed[] $params
 *    @option int[] user_id
 *    @option string pwg_token
 */
function ws_users_delete($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  global $conf, $user;

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  $protected_users = array(
    $user['id'],
    $conf['guest_id'],
    $conf['default_user_id'],
    $conf['webmaster_id'],
    );

  // an admin can't delete other admin/webmaster
  if ('admin' == $user['status'])
  {
    $query = '
SELECT
    user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE status IN (\'webmaster\', \'admin\')
;';
    $protected_users = array_merge($protected_users, query2array($query, null, 'user_id'));
  }
  
  // protect some users
  $params['user_id'] = array_diff($params['user_id'], $protected_users);

  $counter = 0;
  
  foreach ($params['user_id'] as $user_id)
  {
    delete_user($user_id);
    $counter++;
  }

  return l10n_dec(
    '%d user deleted', '%d users deleted',
    $counter
    );
}

/**
 * API method
 * Updates users
 * @param mixed[] $params
 *    @option int[] user_id
 *    @option string username (optional)
 *    @option string password (optional)
 *    @option string email (optional)
 *    @option string status (optional)
 *    @option int level (optional)
 *    @option string language (optional)
 *    @option string theme (optional)
 *    @option int nb_image_page (optional)
 *    @option int recent_period (optional)
 *    @option bool expand (optional)
 *    @option bool show_nb_comments (optional)
 *    @option bool show_nb_hits (optional)
 *    @option bool enabled_high (optional)
 */
function ws_users_setInfo($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  global $conf, $user;

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  $updates = $updates_infos = array();
  $update_status = null;

  if (count($params['user_id']) == 1)
  {
    if (get_username($params['user_id'][0]) === false)
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'This user does not exist.');
    }

    if (!empty($params['username']))
    {
      $user_id = get_userid($params['username']);
      if ($user_id and $user_id != $params['user_id'][0])
      {
        return new PwgError(WS_ERR_INVALID_PARAM, l10n('this login is already used'));
      }
      if ($params['username'] != strip_tags($params['username']))
      {
        return new PwgError(WS_ERR_INVALID_PARAM, l10n('html tags are not allowed in login'));
      }
      $updates[ $conf['user_fields']['username'] ] = $params['username'];
    }

    if (!empty($params['email']))
    {
      if ( ($error = validate_mail_address($params['user_id'][0], $params['email'])) != '')
      {
        return new PwgError(WS_ERR_INVALID_PARAM, $error);
      }
      $updates[ $conf['user_fields']['email'] ] = $params['email'];
    }

    if (!empty($params['password']))
    {
      if (!is_webmaster())
      {
        $password_protected_users = array($conf['guest_id']);

        $query = '
SELECT
    user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE status IN (\'webmaster\', \'admin\')
;';
        $admin_ids = query2array($query, null, 'user_id');

        // we add all admin+webmaster users BUT the user herself
        $password_protected_users = array_merge($password_protected_users, array_diff($admin_ids, array($user['id'])));

        if (in_array($params['user_id'][0], $password_protected_users))
        {
          return new PwgError(403, 'Only webmasters can change password of other "webmaster/admin" users');
        }
      }

      $updates[ $conf['user_fields']['password'] ] = $conf['password_hash']($params['password']);
    }
  }

  if (!empty($params['status']))
  {
    if (in_array($params['status'], array('webmaster', 'admin')) and !is_webmaster() )
    {
      return new PwgError(403, 'Only webmasters can grant "webmaster/admin" status');
    }
    
    if ( !in_array($params['status'], array('guest','generic','normal','admin','webmaster')) )
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid status');
    }

    $protected_users = array(
      $user['id'],
      $conf['guest_id'],
      $conf['webmaster_id'],
      );

    // an admin can't change status of other admin/webmaster
    if ('admin' == $user['status'])
    {
      $query = '
SELECT
    user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE status IN (\'webmaster\', \'admin\')
;';
      $protected_users = array_merge($protected_users, query2array($query, null, 'user_id'));
    }

    // status update query is separated from the rest as not applying to the same
    // set of users (current, guest and webmaster can't be changed)
    $params['user_id_for_status'] = array_diff($params['user_id'], $protected_users);

    $update_status = $params['status'];
  }

  if (!empty($params['level']) or @$params['level']===0)
  {
    if ( !in_array($params['level'], $conf['available_permission_levels']) )
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid level');
    }
    $updates_infos['level'] = $params['level'];
  }

  if (!empty($params['language']))
  {
    if ( !in_array($params['language'], array_keys(get_languages())) )
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid language');
    }
    $updates_infos['language'] = $params['language'];
  }

  if (!empty($params['theme']))
  {
    if ( !in_array($params['theme'], array_keys(get_pwg_themes())) )
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid theme');
    }
    $updates_infos['theme'] = $params['theme'];
  }

  if (!empty($params['nb_image_page']))
  {
    $updates_infos['nb_image_page'] = $params['nb_image_page'];
  }

  if (!empty($params['recent_period']) or @$params['recent_period']===0)
  {
    $updates_infos['recent_period'] = $params['recent_period'];
  }

  if (!empty($params['expand']) or @$params['expand']===false)
  {
    $updates_infos['expand'] = boolean_to_string($params['expand']);
  }

  if (!empty($params['show_nb_comments']) or @$params['show_nb_comments']===false)
  {
    $updates_infos['show_nb_comments'] = boolean_to_string($params['show_nb_comments']);
  }

  if (!empty($params['show_nb_hits']) or @$params['show_nb_hits']===false)
  {
    $updates_infos['show_nb_hits'] = boolean_to_string($params['show_nb_hits']);
  }

  if (!empty($params['enabled_high']) or @$params['enabled_high']===false)
  {
    $updates_infos['enabled_high'] = boolean_to_string($params['enabled_high']);
  }

  // perform updates
  single_update(
    USERS_TABLE,
    $updates,
    array($conf['user_fields']['id'] => $params['user_id'][0])
    );

  if (isset($updates[ $conf['user_fields']['password'] ]))
  {
    deactivate_user_auth_keys($params['user_id'][0]);
  }

  if (isset($update_status) and count($params['user_id_for_status']) > 0)
  {
    $query = '
UPDATE '. USER_INFOS_TABLE .' SET
    status = "'. $update_status .'"
  WHERE user_id IN('. implode(',', $params['user_id_for_status']) .')
;';
    pwg_query($query);
  }

  if (count($updates_infos) > 0)
  {
    $query = '
UPDATE '. USER_INFOS_TABLE .' SET ';

    $first = true;
    foreach ($updates_infos as $field => $value)
    {
      if (!$first) $query.= ', ';
      else $first = false;
      $query.= $field .' = "'. $value .'"';
    }

    $query.= '
  WHERE user_id IN('. implode(',', $params['user_id']) .')
;';
    pwg_query($query);
  }

  // manage association to groups
  if (!empty($params['group_id']))
  {
    $query = '
DELETE
  FROM '.USER_GROUP_TABLE.'
  WHERE user_id IN ('.implode(',', $params['user_id']).')
;';
    pwg_query($query);

    // we remove all provided groups that do not really exist
    $query = '
SELECT
    id
  FROM `'.GROUPS_TABLE.'`
  WHERE id IN ('.implode(',', $params['group_id']).')
;';
    $group_ids = array_from_query($query, 'id');

    // if only -1 (a group id that can't exist) is in the list, then no
    // group is associated
    
    if (count($group_ids) > 0)
    {
      $inserts = array();
      
      foreach ($group_ids as $group_id)
      {
        foreach ($params['user_id'] as $user_id)
        {
          $inserts[] = array('user_id' => $user_id, 'group_id' => $group_id);
        }
      }

      mass_inserts(USER_GROUP_TABLE, array_keys($inserts[0]), $inserts);
    }
  }

  invalidate_user_cache();

  pwg_activity('user', $params['user_id'], 'edit');

  return $service->invoke('pwg.users.getList', array(
    'user_id' => $params['user_id'],
    'display' => 'basics,'.implode(',', array_keys($updates_infos)),
    ));
}

/**
 * API method
 * Adds a favorite image for the current user
 * @param mixed[] $params
 *    @option int image_id
 */
function ws_users_favorites_add($params, &$service)
{
  global $user;

  if (is_a_guest())
  {
    return new PwgError(403, 'User must be logged in.');
  }

  // does the image really exist?
  $query = '
SELECT COUNT(*)
  FROM '. IMAGES_TABLE .'
  WHERE id = '. $params['image_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(404, 'image_id not found');
  }

  single_insert(
    FAVORITES_TABLE,
    array(
      'image_id' => $params['image_id'],
      'user_id' => $user['id'],
    ),
    array('ignore' => true)
  );

  return true;
}

/**
 * API method
 * Removes a favorite image for the current user
 * @param mixed[] $params
 *    @option int image_id
 */
function ws_users_favorites_remove($params, &$service)
{
  global $user;

  if (is_a_guest())
  {
    return new PwgError(403, 'User must be logged in.');
  }

  // does the image really exist?
  $query = '
SELECT COUNT(*)
  FROM '. IMAGES_TABLE .'
  WHERE id = '. $params['image_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(404, 'image_id not found');
  }

  $query = '
DELETE
  FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND image_id = '.$params['image_id'].'
;';

  pwg_query($query);

  return true;
}

/**
 * API method
 * Returns the favorite images of the current user
 * @param mixed[] $params
 *    @option int per_page
 *    @option int page
 *    @option string order
 */
function ws_users_favorites_getList($params, &$service)
{
  global $conf, $user;

  if (is_a_guest())
  {
    return false;
  }

  check_user_favorites();

  $order_by = ws_std_image_sql_order($params, 'i.');
  $order_by = empty($order_by) ? $conf['order_by'] : 'ORDER BY '.$order_by;

  $query = '
SELECT
    i.*
  FROM '.FAVORITES_TABLE.'
    INNER JOIN '.IMAGES_TABLE.' i ON image_id = i.id
  WHERE user_id = '.$user['id'].'
'.get_sql_condition_FandF(
      array(
        'visible_images' => 'id'
        ),
      'AND'
      ).'
    '.$order_by.'
;';
  $images = array();
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $image = array();

    foreach (array('id', 'width', 'height', 'hit') as $k)
    {
      if (isset($row[$k]))
      {
        $image[$k] = (int)$row[$k];
      }
    }

    foreach (array('file', 'name', 'comment', 'date_creation', 'date_available') as $k)
    {
      $image[$k] = $row[$k];
    }

    $images[] = array_merge($image, ws_std_get_urls($row));
  }

  $count = count($images);
  $images = array_slice($images, $params['per_page']*$params['page'], $params['per_page']);

  return array(
    'paging' => new PwgNamedStruct(
      array(
        'page' => $params['page'],
        'per_page' => $params['per_page'],
        'count' => $count
      )
    ),
    'images' => new PwgNamedArray(
      $images, 'image',
      ws_std_get_image_xml_attributes()
     )
   );
}

?>
