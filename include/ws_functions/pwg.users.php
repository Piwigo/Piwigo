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
function ws_users_getList($params, &$service)
{
  global $conf;

  if (!preg_match(PATTERN_ORDER, $params['order']))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid input parameter order');
  }

  // Insensitive case sort order
  if (isset($params['order']))
  {
    if (strpos($params['order'], "username") !== false)
    {
      $params['order'] = str_ireplace("username", "LOWER(username)", $params['order']);
    }
  }

  $where_clauses = array('1=1');

  if (!empty($params['user_id']))
  {
    $where_clauses[] = 'u.'.$conf['user_fields']['id'].' IN('. implode(',', $params['user_id']) .')';
  }

  if (!empty($params['username']))
  {
    $where_clauses[] = 'u.'.$conf['user_fields']['username'].' LIKE \''.pwg_db_real_escape_string($params['username']).'\'';
  }

  $filtered_groups = array();
  if (!empty($params['filter']))
  {
    $filter_query = 'SELECT id FROM `'. GROUPS_TABLE .'` WHERE name LIKE \'%'. $params['filter'] . '%\';';
    $filtered_groups_res = pwg_query($filter_query);
    while ($row = pwg_db_fetch_assoc($filtered_groups_res))
    {
      $filtered_groups[] = $row['id'];
    }
    $filter_where_clause = '('.'u.'.$conf['user_fields']['username'].' LIKE \'%'.
    pwg_db_real_escape_string($params['filter']).'%\' OR '
    .'u.'.$conf['user_fields']['email'].' LIKE \'%'.
    pwg_db_real_escape_string($params['filter']).'%\'';

    if (!empty($filtered_groups)) {
      $filter_where_clause .= 'OR ug.group_id IN ('. implode(',', $filtered_groups).')';
    }
    $where_clauses[] =  $filter_where_clause.')';
  }


  if (!empty($params['min_register'])) {
    if (!preg_match('/^\d\d\d\d(-\d{1,2}){0,2}$/', $params['min_register']))
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid input parameter min_register');
    }

    $date_tokens = explode('-', $params['min_register']);
    $min_register_year = $date_tokens[0];
    $min_register_month = $date_tokens[1] ?? 1;
    $min_register_day =  $date_tokens[2] ?? 1;
    $min_date = sprintf('%u-%02u-%02u', $min_register_year, $min_register_month, $min_register_day);
    $where_clauses[] = 'ui.registration_date >= \''.$min_date.' 00:00:00\'';
  }


  if (!empty($params['max_register'])) {
    if (!preg_match('/^\d\d\d\d(-\d{1,2}){0,2}$/', $params['max_register']))
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid input parameter max_register');
    }

    $max_date_tokens = explode('-', $params['max_register']);
    $max_register_year = $max_date_tokens[0];
    $max_register_month = $max_date_tokens[1] ?? 12;
    $max_register_day = $max_date_tokens[2] ?? date('t', strtotime($max_register_year.'-'.$max_register_month.'-1'));
    $max_date = sprintf('%u-%02u-%02u', $max_register_year, $max_register_month, $max_register_day);
    $where_clauses[] = 'ui.registration_date <= \''.$max_date.' 23:59:59\'';
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

  if (!empty($params['max_level']))
  {
    if ( !in_array($params['max_level'], $conf['available_permission_levels']) )
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid level');
    }
    $where_clauses[] = 'ui.level <= '.$params['max_level'];
  }

  if (!empty($params['group_id']))
  {
    $where_clauses[] = 'ug.group_id IN('. implode(',', $params['group_id']) .')';
  }

  if (!empty($params['exclude']))
  {
    $where_clauses[] = 'u.'.$conf['user_fields']['id'].' NOT IN('. implode(',', $params['exclude']) .')';
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
        'last_visit_since', 'total_count'
        );
    }
    else if (in_array('basics', $params['display']))
    {
      $params['display'] = array_merge($params['display'], array(
        'username','email','status','level','groups',
        ));
    }
    else if (in_array('only_id', $params["display"]))
    {
      $params['display'] = array();
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

  // ADD SQL_CALC_FOUND_ROWS if display total_count is requested
  if (isset($params['display']['total_count'])) {
    $query .= 'SQL_CALC_FOUND_ROWS ';
  }
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
  ORDER BY '. $params['order'];
  if ($params["per_page"] != 0 || !empty($params["display"])) {
    $query .= '
    LIMIT '. $params['per_page'].'
    OFFSET '. ($params['per_page']*$params['page']) .';
    ;';
  }
  $users = array();
  $result = pwg_query($query);
  $total_count = 0;

  /* GET THE RESULT OF SQL_CALC_FOUND_ROWS if display total_count is requested*/
  if (isset($params['display']['total_count'])) {
    $total_count_query_result = pwg_query('SELECT FOUND_ROWS();');
    list($total_count) = pwg_db_fetch_row($total_count_query_result);
    $total_count = (int)$total_count;
  }
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['id'] = intval($row['id']);
    if (isset($params['display']['groups']))
    {
      $row['groups'] = array(); // will be filled later
    }
    $users[ $row['id'] ] = $row;
  }
  
  $users_id_arr = array();
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
    foreach ($users as $cur_user)
    {
      $users_id_arr[] = $cur_user['id'];
      if (isset($params['display']['registration_date_string'])) {
        $users[$cur_user['id']]['registration_date_string'] = format_date($cur_user['registration_date'], array('day', 'month', 'year'));
      }
      if (isset($params['display']['registration_date_since'])) {
        $users[ $cur_user['id'] ]['registration_date_since'] = time_since($cur_user['registration_date'], 'month');
      }
      if (isset($params['display']['last_visit'])) {
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

    /* Removed for optimization above, dont go through the $users array for evert display
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
      }*/ 
  }
  $users = trigger_change('ws_users_getList', $users);
  if ($params["per_page"] == 0 && empty($params["display"])) {
    $method_result = $users_id_arr;
  } else {
    $method_result = array(
      'paging' => new PwgNamedStruct(
        array(
          'page' => $params['page'],
          'per_page' => $params['per_page'],
          'count' => count($users),
          'total_count' => $total_count,
          )
        ),
      'users' => new PwgNamedArray(array_values($users), 'user')
    );
  }
  // deprecated: kept for retrocompatibility
  if (isset($params['display']['total_count'])) {
    $method_result['total_count'] = $total_count;
  }
  return $method_result;
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
  
  if (strlen(str_replace( " ", "",  $params['username'])) == 0) {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Name field must not be empty');
  }

  global $conf;

  if ($conf['double_password_type_in_admin'])
  {
    if ($params['password'] != $params['password_confirm'])
    {
      return new PwgError(WS_ERR_INVALID_PARAM, l10n('The passwords do not match'));
    }
  }

  if ($params['auto_password'])
  {
    $params['password'] = generate_key(rand(15, 20));
  }

  $user_id = register_user(
    $params['username'],
    $params['password'],
    $params['email'],
    false, // notify admin
    $errors,
    false // $params['send_password_by_mail']
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

  $updated_users = check_and_save_user_infos($params);

  if (isset($updated_users['error']))
  {
    return new PwgError($updated_users[ 'error' ][ 'code' ], $updated_users[ 'error' ][ 'message' ]);
  }

  return $service->invoke('pwg.users.getList', array(
    'user_id' => $updated_users['user_id'],
    'display' => 'basics,'.implode(',', array_keys($updated_users['infos'])),
  ));
}

/**
 * API method
 * Update user
 * @since 16
 * @param mixed[] $params
 *    @option string email (optional)
 *    @option int nb_image_page (optional)
 *    @option string theme (optional)
 *    @option string language (optional)
 *    @option int recent_period (optional)
 *    @option bool expand (optional)
 *    @option bool show_nb_comments (optional)
 *    @option bool show_nb_hits (optional)
 *    @option string password (optional)
 *    @option string new_password (optional)
 *    @option string conf_new_password (optional)
 */
function ws_users_setMyInfo($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if (is_a_guest())
  {
    return new PwgError(401, 'Access Denied');
  }

  global $user, $conf;

  // ACTIVATE_COMMENTS
  if (!$conf['activate_comments'])
  {
    unset($params['show_nb_comments']);
  }

  // ALLOW_USER_CUSTOMIZATION
  if (!$conf['allow_user_customization'])
  {
    unset(
      $params['nb_image_page'],
      $params['theme'],
      $params['language'],
      $params['recent_period'],
      $params['expand'],
      $params['show_nb_comments'],
      $params['show_nb_hits']
    );
  }

  // SPECIAL_USER
  $special_user = in_array($user['id'], array($conf['guest_id'], $conf['default_user_id']));
  if ($special_user)
  {
    unset(
      $params['password'],
      $params['theme'],
      $params['language']
    );
  }

  if (!empty($params['password']))
  {
    if ($params['new_password'] != $params['conf_new_password'])
    {
      return new PwgError(403, l10n('The passwords do not match'));
    }

    $query = '
SELECT '.$conf['user_fields']['password'].' AS password
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = \''.$user['id'].'\'
;';
    list($current_password) = pwg_db_fetch_row(pwg_query($query));

    if (!$conf['password_verify']($params['password'], $current_password))
    {
      return new PwgError(403, l10n('Current password is wrong'));
    }

    $params['password'] = $params['new_password'];
  }


  // Unset admin field also new and conf password
  unset(
    $params['new_password'],
    $params['conf_new_password'],
    $params['username'],
    $params['status'],
    $params['level'],
    $params['group_id'],
    $params['enabled_high']
  );
  
  $params['user_id'] = [$user['id']];
  $updated_users = check_and_save_user_infos($params);

  if (isset($updated_users['error']))
  {
    return new PwgError($updated_users[ 'error' ][ 'code' ], $updated_users[ 'error' ][ 'message' ]);
  }
  
  return l10n('Your changes have been applied.');
}

/**
 * API method
 * Set a preferences parameter to current user
 * @since 13
 * @param mixed[] $params
 *    @option string param
 *    @option string|mixed value
 */
function ws_users_preferences_set($params, &$service)
{
  global $user;

  if (!preg_match('/^[a-zA-Z0-9_-]+$/', $params['param']))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid param name #'.$params['param'].'#');
  }

  $value = stripslashes($params['value']);
  if ($params['is_json'])
  {
    $value = json_decode($value, true);
  }

  userprefs_update_param($params['param'], $value, true);

  return $user['preferences'];
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

/**
 * API method
 * Returns the reset password link of the current user
 * @since 15
 * @param mixed[] $params
 *    @option int user_id
 *    @option string pwg_token
 *    @option boolean send_by_mail
 */
function ws_users_generate_password_link($params, &$service)
{
  global $user, $conf;
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  // check if user exist
  if (get_username($params['user_id']) === false)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This user does not exist.');
  }

  $user_lost = getuserdata($params['user_id']);

  // Cannot perform this action for a guest or generic user
  if (is_a_guest($user_lost['status']) or is_generic($user_lost['status']))
  {
    return new PwgError(403, 'Password reset is not allowed for this user');
  }

  // Only webmaster can perform this action for another webmaster  
  if ('admin' === $user['status'] && 'webmaster' === $user_lost['status'])
  {
    return new PwgError(403, 'You cannot perform this action');
  }

  $first_login = has_already_logged_in($params['user_id']);
  $send_by_mail_response = null;
  $lang_to_use = $first_login ? get_default_language() : $user_lost['language'];

  switch_lang_to($lang_to_use);
  $generate_link = generate_password_link($params['user_id'], $first_login);

  if ($params['send_by_mail'] and !empty($user_lost['email']))
  {
    if ($first_login)
    {
      $email_params = pwg_generate_set_password_mail($user_lost['username'], $generate_link['password_link'], $conf['gallery_title'], $generate_link['time_validation']);
    }
    else
    {
      $email_params = pwg_generate_reset_password_mail($user_lost['username'], $generate_link['password_link'], $conf['gallery_title'], $generate_link['time_validation']);
    }
    // Here we remove the display of errors because they prevent the response from being parsed
    if (@pwg_mail($user_lost['email'], $email_params))
    {
      $send_by_mail_response = 'Mail sent at : ' . $user_lost['email'];
    } 
    else
    {
      $send_by_mail_response = false;
    }
  }
  switch_lang_back();
  
  return array(
    'generated_link' => $generate_link['password_link'],
    'send_by_mail' => $send_by_mail_response,
    'time_validation' => $generate_link['time_validation'],
  );
}

/**
 * API method
 * Set a user as the main user
 * @since 15
 * @param mixed[] $params
 *    @option int user_id
 *    @option string pwg_token
 */
function ws_set_main_user($params, &$service)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  // check if not webmaster
  if (!is_webmaster())
  {
    return new PwgError(403, 'You cannot perform this action');
  }

  //check pwg_token
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  // checl if user exist
  if (get_username($params['user_id']) === false)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'This user does not exist.');
  }

  $new_main_user = getuserdata($params['user_id']);

  // check if the user to set as main user is not webmaster
  if ('webmaster' !== $new_main_user['status'])
  {
    return new PwgError(403, 'This user cannot become a main user because he is not a webmaster.');
  }

  conf_update_param('webmaster_id', $params['user_id']);
  return 'The main user has been changed.';
}

/**
 * API method
 * Create a new api key for the current user
 * @since 15
 * @param mixed[] $params
 */
function ws_create_api_key($params, &$service)
{
  global $user, $logger;

  if (is_a_guest() OR !connected_with_pwg_ui()) return new PwgError(401, 'Acces Denied');

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if ($params['duration'] < 1 OR $params['duration'] > 999999)
  {
    return new PwgError(400, 'Invalid duration max days is 999999');
  }

  if (strlen($params['key_name']) > 100)
  {
    return new PwgError(400, 'Key name is too long');
  }

  $key_name = pwg_db_real_escape_string($params['key_name']);
  $duration = 0 == $params['duration'] ? 1 : $params['duration'];

  $secret = create_api_key($user['id'], $duration, $key_name);

  $logger->info('[api_key][user_id='.$user['id'].'][action=create][key_name='.$params['key_name'].']');

  return $secret;
}

/**
 * API method
 * Revoke a api key for the current user
 * @since 15
 * @param mixed[] $params
 */
function ws_revoke_api_key($params, &$service)
{
  global $user, $logger;

  if (is_a_guest() OR !connected_with_pwg_ui()) return new PwgError(401, 'Acces Denied');

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, l10n('Invalid security token'));
  }

  if (!preg_match('/^pkid-\d{8}-[a-z0-9]{20}$/i', $params['pkid']))
  {
    return new PwgError(403, l10n('Invalid pkid format'));
  }

  $revoked_key = revoke_api_key($user['id'], $params['pkid']);

  if (true !== $revoked_key)
  {
    return new PwgError(403, $revoked_key);
  }

  $logger->info('[api_key][user_id='.$user['id'].'][action=revoke][pkid='.$params['pkid'].']');

  return l10n('API Key has been successfully revoked.');
}

/**
 * API method
 * Edit a api key for the current user
 * @since 15
 * @param mixed[] $params
 */
function ws_edit_api_key($params, &$service)
{
  global $user, $logger;

  if (is_a_guest())
  {
    return new PwgError(401, 'Acces Denied');
  }

  if (!connected_with_pwg_ui())
  {
    return new PwgError(401, 'Acces Denied');
  }

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, l10n('Invalid security token'));
  }

  if (!preg_match('/^pkid-\d{8}-[a-z0-9]{20}$/i', $params['pkid']))
  {
    return new PwgError(403, l10n('Invalid pkid format'));
  }

  $key_name = pwg_db_real_escape_string($params['key_name']);
  $edited_key = edit_api_key($user['id'], $params['pkid'], $key_name);

  if (true !== $edited_key)
  {
    return new PwgError(403, $edited_key);
  }

  $logger->info('[api_key][user_id='.$user['id'].'][action=edit][pkid='.$params['pkid'].'][new_name='.$key_name.']');

  return l10n('API Key has been successfully edited.');
}

/**
 * API method
 * Get all api key for the current user
 * @since 15
 * @param mixed[] $params
 */
function ws_get_api_key($params, &$service)
{
  global $user;

  if (is_a_guest())
  {
    return new PwgError(401, 'Acces Denied');
  }

  if (!connected_with_pwg_ui())
  {
    return new PwgError(401, 'Acces Denied');
  }

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $api_keys = get_api_key($user['id']);

  return $api_keys ?? l10n('No API key found');
}
?>
