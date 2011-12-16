<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

// validate_mail_address:
//   o verifies whether the given mail address has the
//     right format. ie someone@domain.com "someone" can contain ".", "-" or
//     even "_". Exactly as "domain". The extension doesn't have to be
//     "com". The mail address can also be empty.
//   o check if address could be empty
//   o check if address is not used by a other user
// If the mail address doesn't correspond, an error message is returned.
//
function validate_mail_address($user_id, $mail_address)
{
  global $conf;

  if (empty($mail_address) and
      !($conf['obligatory_user_mail_address'] and
      in_array(script_basename(), array('register', 'profile'))))
  {
    return '';
  }

  $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // before  arobase
  $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // domain name
  $regex = '/^' . $atom . '+' . '(\.' . $atom . '+)*' . '@' . '(' . $domain . '{1,63}\.)+' . $domain . '{2,63}$/i';

  if ( !preg_match( $regex, $mail_address ) )
  {
    return l10n('mail address must be like xxx@yyy.eee (example : jack@altern.org)');
  }

  if (defined("PHPWG_INSTALLED") and !empty($mail_address))
  {
    $query = '
select count(*)
from '.USERS_TABLE.'
where upper('.$conf['user_fields']['email'].') = upper(\''.$mail_address.'\')
'.(is_numeric($user_id) ? 'and '.$conf['user_fields']['id'].' != \''.$user_id.'\'' : '').'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      return l10n('this email address is already in use');
    }
  }
}

// validate_login_case:
//   o check if login is not used by a other user
// If the login doesn't correspond, an error message is returned.
//
function validate_login_case($login)
{
  global $conf;
  
  if (defined("PHPWG_INSTALLED"))
  {
    $query = "
SELECT ".$conf['user_fields']['username']."
FROM ".USERS_TABLE."
WHERE LOWER(".stripslashes($conf['user_fields']['username']).") = '".strtolower($login)."'
;";

    $count = pwg_db_num_rows(pwg_query($query));

    if ($count > 0)
    {
      return l10n('this login is already used');
    }
  }
}
/**
 * For test on username case sensitivity
 *
 * @param : $username typed in by user for identification
 *
 * @return : $username found in database
 *
 */
function search_case_username($username)
{
  global $conf;

  $username_lo = strtolower($username);

  $SCU_users = array();
  
  $q = pwg_query("
    SELECT ".$conf['user_fields']['username']." AS username
    FROM `".USERS_TABLE."`;
  ");
  while ($r = pwg_db_fetch_assoc($q))
   $SCU_users[$r['username']] = strtolower($r['username']);
   // $SCU_users is now an associative table where the key is the account as
   // registered in the DB, and the value is this same account, in lower case
   
  $users_found = array_keys($SCU_users, $username_lo);
  // $users_found is now a table of which the values are all the accounts
  // which can be written in lowercase the same way as $username
  if (count($users_found) != 1) // If ambiguous, don't allow lowercase writing
   return $username; // but normal writing will work
  else
   return $users_found[0];
}
function register_user($login, $password, $mail_address,
  $with_notification = true, $errors = array())
{
  global $conf;

  if ($login == '')
  {
    array_push($errors, l10n('Please, enter a login'));
  }
  if (preg_match('/^.* $/', $login))
  {
    array_push($errors, l10n('login mustn\'t end with a space character'));
  }
  if (preg_match('/^ .*$/', $login))
  {
    array_push($errors, l10n('login mustn\'t start with a space character'));
  }
  if (get_userid($login))
  {
    array_push($errors, l10n('this login is already used'));
  }
  if ($login != strip_tags($login))
  {
    array_push($errors, l10n('html tags are not allowed in login'));
  }
  $mail_error = validate_mail_address(null, $mail_address);
  if ('' != $mail_error)
  {
    array_push($errors, $mail_error);
  }

  if ($conf['insensitive_case_logon'] == true)
  {
    $login_error = validate_login_case($login);
    if ($login_error != '')
    {
      array_push($errors, $login_error);
    }
  }

  $errors = trigger_event('register_user_check',
              $errors,
              array(
                'username'=>$login,
                'password'=>$password,
                'email'=>$mail_address,
              )
            );

  // if no error until here, registration of the user
  if (count($errors) == 0)
  {
    // what will be the inserted id ?
    $query = '
SELECT MAX('.$conf['user_fields']['id'].') + 1
  FROM '.USERS_TABLE.'
;';
    list($next_id) = pwg_db_fetch_row(pwg_query($query));

    $insert =
      array(
        $conf['user_fields']['id'] => $next_id,
        $conf['user_fields']['username'] => pwg_db_real_escape_string($login),
        $conf['user_fields']['password'] => $conf['pass_convert']($password),
        $conf['user_fields']['email'] => $mail_address
        );

    mass_inserts(USERS_TABLE, array_keys($insert), array($insert));

    // Assign by default groups
    {
      $query = '
SELECT id
  FROM '.GROUPS_TABLE.'
  WHERE is_default = \''.boolean_to_string(true).'\'
  ORDER BY id ASC
;';
      $result = pwg_query($query);

      $inserts = array();
      while ($row = pwg_db_fetch_assoc($result))
      {
        array_push
        (
          $inserts,
          array
          (
            'user_id' => $next_id,
            'group_id' => $row['id']
          )
        );
      }
    }

    if (count($inserts) != 0)
    {
      mass_inserts(USER_GROUP_TABLE, array('user_id', 'group_id'), $inserts);
    }

    $override = null;
    if ($with_notification and $conf['browser_language'])
    {
      if ( !get_browser_language($override['language']) )
        $override=null;
    }
    create_user_infos($next_id, $override);

    if ($with_notification and $conf['email_admin_on_new_user'])
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
      $admin_url = get_absolute_root_url()
                   .'admin.php?page=user_list&username='.$login;

      $keyargs_content = array
      (
        get_l10n_args('User: %s', stripslashes($login)),
        get_l10n_args('Email: %s', $_POST['mail_address']),
        get_l10n_args('', ''),
        get_l10n_args('Admin: %s', $admin_url)
      );

      pwg_mail_notification_admins
      (
        get_l10n_args('Registration of %s', stripslashes($login)),
        $keyargs_content
      );
    }

    trigger_action('register_user',
      array(
        'id'=>$next_id,
        'username'=>$login,
        'email'=>$mail_address,
       )
      );
  }

  return $errors;
}

function build_user( $user_id, $use_cache )
{
  global $conf;

  $user['id'] = $user_id;
  $user = array_merge( $user, getuserdata($user_id, $use_cache) );

  if ($user['id'] == $conf['guest_id'] and $user['status'] <> 'guest')
  {
    $user['status'] = 'guest';
    $user['internal_status']['guest_must_be_guest'] = true;
  }

  // Check user theme
  if (!isset($user['theme_name']))
  {
    $user['theme'] = get_default_theme();
  }

  return $user;
}

/**
 * find informations related to the user identifier
 *
 * @param int user identifier
 * @param boolean use_cache
 * @param array
 */
function getuserdata($user_id, $use_cache)
{
  global $conf;

  $userdata = array();

  // retrieve basic user data
  $query = '
SELECT ';
  $is_first = true;
  foreach ($conf['user_fields'] as $pwgfield => $dbfield)
  {
    if ($is_first)
    {
      $is_first = false;
    }
    else
    {
      $query.= '
     , ';
    }
    $query.= $dbfield.' AS '.$pwgfield;
  }
  $query.= '
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = \''.$user_id.'\'';

  $row = pwg_db_fetch_assoc(pwg_query($query));

  // retrieve additional user data ?
  if ($conf['external_authentification'])
  {
    $query = '
SELECT
    COUNT(1) AS counter
  FROM '.USER_INFOS_TABLE.' AS ui
    LEFT JOIN '.USER_CACHE_TABLE.' AS uc ON ui.user_id = uc.user_id
    LEFT JOIN '.THEMES_TABLE.' AS t ON t.id = ui.theme
  WHERE ui.user_id = '.$user_id.'
  GROUP BY ui.user_id
;';
    list($counter) = pwg_db_fetch_row(pwg_query($query));
    if ($counter != 1)
    {
      create_user_infos($user_id);
    }
  }

  // retrieve user info
  $query = '
SELECT
    ui.*,
    uc.*,
    t.name AS theme_name
  FROM '.USER_INFOS_TABLE.' AS ui
    LEFT JOIN '.USER_CACHE_TABLE.' AS uc ON ui.user_id = uc.user_id
    LEFT JOIN '.THEMES_TABLE.' AS t ON t.id = ui.theme
  WHERE ui.user_id = '.$user_id.'
;';

  $result = pwg_query($query);
  $user_infos_row = pwg_db_fetch_assoc($result);

  // then merge basic + additional user data
  $row = array_merge($row, $user_infos_row);

  foreach ($row as $key => $value)
  {
    if (!is_numeric($key))
    {
      // If the field is true or false, the variable is transformed into a
      // boolean value.
      if ($value == 'true' or $value == 'false')
      {
        $userdata[$key] = get_boolean($value);
      }
      else
      {
        $userdata[$key] = $value;
      }
    }
  }

  if ($use_cache)
  {
    if (!isset($userdata['need_update'])
        or !is_bool($userdata['need_update'])
        or $userdata['need_update'] == true)
    {
      $userdata['cache_update_time'] = time();

      // Set need update are done
      $userdata['need_update'] = false;

      $userdata['forbidden_categories'] =
        calculate_permissions($userdata['id'], $userdata['status']);

      /* now we build the list of forbidden images (this list does not contain
      images that are not in at least an authorized category)*/
      $query = '
SELECT DISTINCT(id)
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id=image_id
  WHERE category_id NOT IN ('.$userdata['forbidden_categories'].')
    AND level>'.$userdata['level'];
      $forbidden_ids = array_from_query($query, 'id');

      if ( empty($forbidden_ids) )
      {
        array_push( $forbidden_ids, 0 );
      }
      $userdata['image_access_type'] = 'NOT IN'; //TODO maybe later
      $userdata['image_access_list'] = implode(',',$forbidden_ids);


      $query = '
SELECT COUNT(DISTINCT(image_id)) as total
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id NOT IN ('.$userdata['forbidden_categories'].')
    AND image_id '.$userdata['image_access_type'].' ('.$userdata['image_access_list'].')';
      list($userdata['nb_total_images']) = pwg_db_fetch_row(pwg_query($query));


      // now we update user cache categories
      $user_cache_cats = get_computed_categories($userdata, null);
      if ( !is_admin($userdata['status']) )
      { // for non admins we forbid categories with no image (feature 1053)
        $forbidden_ids = array();
        foreach ($user_cache_cats as $cat)
        {
          if ($cat['count_images']==0)
          {
            array_push($forbidden_ids, $cat['cat_id']);
            unset( $user_cache_cats[$cat['cat_id']] );
          }
        }
        if ( !empty($forbidden_ids) )
        {
          if ( empty($userdata['forbidden_categories']) )
          {
            $userdata['forbidden_categories'] = implode(',', $forbidden_ids);
          }
          else
          {
            $userdata['forbidden_categories'] .= ','.implode(',', $forbidden_ids);
          }
        }
      }

      // delete user cache
      $query = '
DELETE FROM '.USER_CACHE_CATEGORIES_TABLE.'
  WHERE user_id = '.$userdata['id'];
      pwg_query($query);

      // Due to concurrency issues, we ask MySQL to ignore errors on
      // insert. This may happen when cache needs refresh and that Piwigo is
      // called "very simultaneously".
      mass_inserts
      (
        USER_CACHE_CATEGORIES_TABLE,
        array
        (
          'user_id', 'cat_id',
          'date_last', 'max_date_last', 'nb_images', 'count_images', 'count_categories'
        ),
        $user_cache_cats,
        array('ignore' => true)
      );


      // update user cache
      $query = '
DELETE FROM '.USER_CACHE_TABLE.'
  WHERE user_id = '.$userdata['id'];
      pwg_query($query);

      // for the same reason as user_cache_categories, we ignore error on
      // this insert
      $query = '
INSERT IGNORE INTO '.USER_CACHE_TABLE.'
  (user_id, need_update, cache_update_time, forbidden_categories, nb_total_images,
    image_access_type, image_access_list)
  VALUES
  ('.$userdata['id'].',\''.boolean_to_string($userdata['need_update']).'\','
  .$userdata['cache_update_time'].',\''
  .$userdata['forbidden_categories'].'\','.$userdata['nb_total_images'].',\''
  .$userdata['image_access_type'].'\',\''.$userdata['image_access_list'].'\')';
      pwg_query($query);
    }
  }

  return $userdata;
}

/*
 * deletes favorites of the current user if he's not allowed to see them
 *
 * @return void
 */
function check_user_favorites()
{
  global $user;

  if ($user['forbidden_categories'] == '')
  {
    return;
  }

  // $filter['visible_categories'] and $filter['visible_images']
  // must be not used because filter <> restriction
  // retrieving images allowed : belonging to at least one authorized
  // category
  $query = '
SELECT DISTINCT f.image_id
  FROM '.FAVORITES_TABLE.' AS f INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
    ON f.image_id = ic.image_id
  WHERE f.user_id = '.$user['id'].'
'.get_sql_condition_FandF
  (
    array
      (
        'forbidden_categories' => 'ic.category_id',
      ),
    'AND'
  ).'
;';
  $result = pwg_query($query);
  $authorizeds = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($authorizeds, $row['image_id']);
  }

  $query = '
SELECT image_id
  FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  $result = pwg_query($query);
  $favorites = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($favorites, $row['image_id']);
  }

  $to_deletes = array_diff($favorites, $authorizeds);

  if (count($to_deletes) > 0)
  {
    $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE image_id IN ('.implode(',', $to_deletes).')
    AND user_id = '.$user['id'].'
;';
    pwg_query($query);
  }
}

/**
 * calculates the list of forbidden categories for a given user
 *
 * Calculation is based on private categories minus categories authorized to
 * the groups the user belongs to minus the categories directly authorized
 * to the user. The list contains at least -1 to be compliant with queries
 * such as "WHERE category_id NOT IN ($forbidden_categories)"
 *
 * @param int user_id
 * @param string user_status
 * @return string forbidden_categories
 */
function calculate_permissions($user_id, $user_status)
{
  $private_array = array();
  $authorized_array = array();

  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($private_array, $row['id']);
  }

  // retrieve category ids directly authorized to the user
  $query = '
SELECT cat_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  $authorized_array = array_from_query($query, 'cat_id');

  // retrieve category ids authorized to the groups the user belongs to
  $query = '
SELECT cat_id
  FROM '.USER_GROUP_TABLE.' AS ug INNER JOIN '.GROUP_ACCESS_TABLE.' AS ga
    ON ug.group_id = ga.group_id
  WHERE ug.user_id = '.$user_id.'
;';
  $authorized_array =
    array_merge(
      $authorized_array,
      array_from_query($query, 'cat_id')
      );

  // uniquify ids : some private categories might be authorized for the
  // groups and for the user
  $authorized_array = array_unique($authorized_array);

  // only unauthorized private categories are forbidden
  $forbidden_array = array_diff($private_array, $authorized_array);

  // if user is not an admin, locked categories are forbidden
  if (!is_admin($user_status))
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'false\'
;';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      array_push($forbidden_array, $row['id']);
    }
    $forbidden_array = array_unique($forbidden_array);
  }

  if ( empty($forbidden_array) )
  {// at least, the list contains 0 value. This category does not exists so
   // where clauses such as "WHERE category_id NOT IN(0)" will always be
   // true.
    array_push($forbidden_array, 0);
  }

  return implode(',', $forbidden_array);
}

/**
 * compute data of categories branches (one branch only)
 */
function compute_branch_cat_data(&$cats, &$list_cat_id, &$level, &$ref_level)
{
  $date = '';
  $count_images = 0;
  $count_categories = 0;
  do
  {
    $cat_id = array_pop($list_cat_id);
    if (!is_null($cat_id))
    {
      // Count images and categories
      $cats[$cat_id]['count_images'] += $count_images;
      $cats[$cat_id]['count_categories'] += $count_categories;
      $count_images = $cats[$cat_id]['count_images'];
      $count_categories = $cats[$cat_id]['count_categories'] + 1;

      if ((empty($cats[$cat_id]['max_date_last'])) or ($cats[$cat_id]['max_date_last'] < $date))
      {
        $cats[$cat_id]['max_date_last'] = $date;
      }
      else
      {
        $date = $cats[$cat_id]['max_date_last'];
      }
      $ref_level = substr_count($cats[$cat_id]['global_rank'], '.') + 1;
    }
    else
    {
      $ref_level = 0;
    }
  } while ($level <= $ref_level);

  // Last cat updating must be added to list for next branch
  if ($ref_level <> 0)
  {
    array_push($list_cat_id, $cat_id);
  }
}

/**
 * compute data of categories branches
 */
function compute_categories_data(&$cats)
{
  $ref_level = 0;
  $level = 0;
  $list_cat_id = array();

  foreach ($cats as $id => $category)
  {
    // Compute
    $level = substr_count($category['global_rank'], '.') + 1;
    if ($level > $ref_level)
    {
      array_push($list_cat_id, $id);
    }
    else
    {
      compute_branch_cat_data($cats, $list_cat_id, $level, $ref_level);
      array_push($list_cat_id, $id);
    }
    $ref_level = $level;
  }

  $level = 1;
  compute_branch_cat_data($cats, $list_cat_id, $level, $ref_level);
}

/**
 * get computed array of categories
 *
 * @param array userdata
 * @param int filter_days number of recent days to filter on or null
 * @return array
 */
function get_computed_categories($userdata, $filter_days=null)
{
  $query = 'SELECT c.id AS cat_id, global_rank';
  // Count by date_available to avoid count null
  $query .= ',
  MAX(date_available) AS date_last, COUNT(date_available) AS nb_images
FROM '.CATEGORIES_TABLE.' as c
  LEFT JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.category_id = c.id
  LEFT JOIN '.IMAGES_TABLE.' AS i
    ON ic.image_id = i.id
      AND i.level<='.$userdata['level'];

  if ( isset($filter_days) )
  {
    $query .= ' AND i.date_available > '.pwg_db_get_recent_period_expression($filter_days);
  }

  if ( !empty($userdata['forbidden_categories']) )
  {
    $query.= '
  WHERE c.id NOT IN ('.$userdata['forbidden_categories'].')';
  }

  $query.= '
  GROUP BY c.id, c.global_rank';

  $result = pwg_query($query);

  $cats = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['user_id'] = $userdata['id'];
    $row['count_categories'] = 0;
    $row['count_images'] = (int)$row['nb_images'];
    $row['max_date_last'] = $row['date_last'];

    $cats += array($row['cat_id'] => $row);
  }
  uasort($cats, 'global_rank_compare');

  compute_categories_data($cats);

  if ( isset($filter_days) )
  {
    $cat_tmp = $cats;
    $cats = array();

    foreach ($cat_tmp as $category)
    {
      if (!empty($category['max_date_last']))
      {
        // Re-init counters
        $category['count_categories'] = 0;
        $category['count_images'] = (int)$category['nb_images'];
        // Keep category
        $cats[$category['cat_id']] = $category;
      }
    }
    // Compute a second time
    compute_categories_data($cats);
  }
  return $cats;
}

/**
 * returns user identifier thanks to his name, false if not found
 *
 * @param string username
 * @param int user identifier
 */
function get_userid($username)
{
  global $conf;

  $username = pwg_db_real_escape_string($username);

  $query = '
SELECT '.$conf['user_fields']['id'].'
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.$username.'\'
;';
  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    return false;
  }
  else
  {
    list($user_id) = pwg_db_fetch_row($result);
    return $user_id;
  }
}

function get_userid_by_email($email)
{
  global $conf;

  $email = pwg_db_real_escape_string($email);
  
  $query = '
SELECT
    '.$conf['user_fields']['id'].'
  FROM '.USERS_TABLE.'
  WHERE UPPER('.$conf['user_fields']['email'].') = UPPER(\''.$email.'\')
;';
  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    return false;
  }
  else
  {
    list($user_id) = pwg_db_fetch_row($result);
    return $user_id;
  }
}

/**
 * search an available feed_id
 *
 * @return string feed identifier
 */
function find_available_feed_id()
{
  while (true)
  {
    $key = generate_key(50);
    $query = '
SELECT COUNT(*)
  FROM '.USER_FEED_TABLE.'
  WHERE id = \''.$key.'\'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if (0 == $count)
    {
      return $key;
    }
  }
}

/*
 * Returns a array with default user value
 *
 * @param convert_str allows to convert string value if necessary
 */
function get_default_user_info($convert_str = true)
{
  global $cache, $conf;

  if (!isset($cache['default_user']))
  {
    $query = 'SELECT * FROM '.USER_INFOS_TABLE.
            ' WHERE user_id = '.$conf['default_user_id'].';';

    $result = pwg_query($query);
    $cache['default_user'] = pwg_db_fetch_assoc($result);

    if ($cache['default_user'] !== false)
    {
      unset($cache['default_user']['user_id']);
      unset($cache['default_user']['status']);
      unset($cache['default_user']['registration_date']);
    }
  }

  if (is_array($cache['default_user']) and $convert_str)
  {
    $default_user = array();
    foreach ($cache['default_user'] as $name => $value)
    {
      // If the field is true or false, the variable is transformed into a
      // boolean value.
      if ($value == 'true' or $value == 'false')
      {
        $default_user[$name] = get_boolean($value);
      }
      else
      {
        $default_user[$name] = $value;
      }
    }
    return $default_user;
  }
  else
  {
    return $cache['default_user'];
  }
}

/*
 * Returns a default user value
 *
 * @param value_name: name of value
 * @param sos_value: value used if don't exist value
 */
function get_default_user_value($value_name, $sos_value)
{
  $default_user = get_default_user_info(true);
  if ($default_user === false or empty($default_user[$value_name]))
  {
    return $sos_value;
  }
  else
  {
   return $default_user[$value_name];
  }
}

/*
 * Returns the default template value
 *
 */
function get_default_theme()
{
  $theme = get_default_user_value('theme', PHPWG_DEFAULT_TEMPLATE);
  if (check_theme_installed($theme))
  {
    return $theme;
  }
  
  // let's find the first available theme
  $active_themes = get_pwg_themes();
  foreach (array_keys(get_pwg_themes()) as $theme_id)
  {
    if (check_theme_installed($theme_id))
    {
      return $theme_id;
    }
  }
}

/*
 * Returns the default language value
 *
 */
function get_default_language()
{
  return get_default_user_value('language', PHPWG_DEFAULT_LANGUAGE);
}

/**
  * Returns true if the browser language value is set into param $lang
  *
  */
function get_browser_language(&$lang)
{
  $browser_language = substr(@$_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
  foreach (get_languages() as $language_code => $language_name)
  {
    if (substr($language_code, 0, 2) == $browser_language)
    {
      $lang = $language_code;
      return true;
    }
  }
  return false;
}

/**
 * add user informations based on default values
 *
 * @param int user_id / array of user_if
 * @param array of values used to override default user values
 */
function create_user_infos($arg_id, $override_values = null)
{
  global $conf;

  if (is_array($arg_id))
  {
    $user_ids = $arg_id;
  }
  else
  {
    $user_ids = array();
    if (is_numeric($arg_id))
    {
      $user_ids[] = $arg_id;
    }
  }

  if (!empty($user_ids))
  {
    $inserts = array();
    list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

    $default_user = get_default_user_info(false);
    if ($default_user === false)
    {
      // Default on structure are used
      $default_user = array();
    }

    if (!is_null($override_values))
    {
      $default_user = array_merge($default_user, $override_values);
    }

    foreach ($user_ids as $user_id)
    {
      $level= isset($default_user['level']) ? $default_user['level'] : 0;
      if ($user_id == $conf['webmaster_id'])
      {
        $status = 'webmaster';
        $level = max( $conf['available_permission_levels'] );
      }
      else if (($user_id == $conf['guest_id']) or
               ($user_id == $conf['default_user_id']))
      {
        $status = 'guest';
      }
      else
      {
        $status = 'normal';
      }

      $insert = array_merge(
        $default_user,
        array(
          'user_id' => $user_id,
          'status' => $status,
          'registration_date' => $dbnow,
          'level' => $level
          ));

      array_push($inserts, $insert);
    }

    mass_inserts(USER_INFOS_TABLE, array_keys($inserts[0]), $inserts);
  }
}

/**
 * returns the auto login key or false on error
 * @param int user_id
 * @param time_t time
 * @param string [out] username
*/
function calculate_auto_login_key($user_id, $time, &$username)
{
  global $conf;
  $query = '
SELECT '.$conf['user_fields']['username'].' AS username
  , '.$conf['user_fields']['password'].' AS password
FROM '.USERS_TABLE.'
WHERE '.$conf['user_fields']['id'].' = '.$user_id;
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) > 0)
  {
    $row = pwg_db_fetch_assoc($result);
    $username = stripslashes($row['username']);
    $data = $time.$user_id.$username;
    $key = base64_encode( hash_hmac('sha1', $data, $conf['secret_key'].$row['password'],true) );
    return $key;
  }
  return false;
}

/*
 * Performs all required actions for user login
 * @param int user_id
 * @param bool remember_me
 * @return void
*/
function log_user($user_id, $remember_me)
{
  global $conf, $user;

  if ($remember_me and $conf['authorize_remembering'])
  {
    $now = time();
    $key = calculate_auto_login_key($user_id, $now, $username);
    if ($key!==false)
    {
      $cookie = $user_id.'-'.$now.'-'.$key;
      if (version_compare(PHP_VERSION, '5.2', '>=') )
      {
        setcookie($conf['remember_me_name'],
            $cookie,
            time()+$conf['remember_me_length'],
            cookie_path(),ini_get('session.cookie_domain'),ini_get('session.cookie_secure'),
            ini_get('session.cookie_httponly')
          );
      }
      else
      {
        setcookie($conf['remember_me_name'],
            $cookie,
            time()+$conf['remember_me_length'],
            cookie_path(),ini_get('session.cookie_domain'),ini_get('session.cookie_secure')
          );
      }
    }
  }
  else
  { // make sure we clean any remember me ...
    setcookie($conf['remember_me_name'], '', 0, cookie_path(),ini_get('session.cookie_domain'));
  }
  if ( session_id()!="" )
  { // we regenerate the session for security reasons
    // see http://www.acros.si/papers/session_fixation.pdf
    session_regenerate_id(true);
  }
  else
  {
    session_start();
  }
  $_SESSION['pwg_uid'] = (int)$user_id;

  $user['id'] = $_SESSION['pwg_uid'];
}

/*
 * Performs auto-connexion when cookie remember_me exists
 * @return true/false
*/
function auto_login() {
  global $conf;

  if ( isset( $_COOKIE[$conf['remember_me_name']] ) )
  {
    $cookie = explode('-', stripslashes($_COOKIE[$conf['remember_me_name']]));
    if ( count($cookie)===3
        and is_numeric(@$cookie[0]) /*user id*/
        and is_numeric(@$cookie[1]) /*time*/
        and time()-$conf['remember_me_length']<=@$cookie[1]
        and time()>=@$cookie[1] /*cookie generated in the past*/ )
    {
      $key = calculate_auto_login_key( $cookie[0], $cookie[1], $username );
      if ($key!==false and $key===$cookie[2])
      {
        log_user($cookie[0], true);
        trigger_action('login_success', stripslashes($username));
        return true;
      }
    }
    setcookie($conf['remember_me_name'], '', 0, cookie_path(),ini_get('session.cookie_domain'));
  }
  return false;
}

/**
 * Tries to login a user given username and password (must be MySql escaped)
 * return true on success
 */
function try_log_user($username, $password, $remember_me)
{
  // we force the session table to be clean
  pwg_session_gc();
  
  global $conf;
  // retrieving the encrypted password of the login submitted
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['password'].' AS password
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.pwg_db_real_escape_string($username).'\'
;';
  $row = pwg_db_fetch_assoc(pwg_query($query));
  if ($row['password'] == $conf['pass_convert']($password))
  {
    log_user($row['id'], $remember_me);
    trigger_action('login_success', stripslashes($username));
    return true;
  }
  trigger_action('login_failure', stripslashes($username));
  return false;
}

/** Performs all the cleanup on user logout */
function logout_user()
{
  global $conf;
  $_SESSION = array();
  session_unset();
  session_destroy();
  setcookie(session_name(),'',0,
      ini_get('session.cookie_path'),
      ini_get('session.cookie_domain')
    );
  setcookie($conf['remember_me_name'], '', 0, cookie_path(),ini_get('session.cookie_domain'));
}

/*
 * Return user status used in this library
 * @return string
*/
function get_user_status($user_status)
{
  global $user;

  if (empty($user_status))
  {
    if (isset($user['status']))
    {
      $user_status = $user['status'];
    }
    else
    {
      // swicth to default value
      $user_status = '';
    }
  }
  return $user_status;
}

/*
 * Return access_type definition of user
 * Test does with user status
 * @return bool
*/
function get_access_type_status($user_status='')
{
  global $conf;

  switch (get_user_status($user_status))
  {
    case 'guest':
    {
      $access_type_status =
        ($conf['guest_access'] ? ACCESS_GUEST : ACCESS_FREE);
      break;
    }
    case 'generic':
    {
      $access_type_status = ACCESS_GUEST;
      break;
    }
    case 'normal':
    {
      $access_type_status = ACCESS_CLASSIC;
      break;
    }
    case 'admin':
    {
      $access_type_status = ACCESS_ADMINISTRATOR;
      break;
    }
    case 'webmaster':
    {
      $access_type_status = ACCESS_WEBMASTER;
      break;
    }
    default:
    {
      $access_type_status = ACCESS_FREE;
      break;
    }
  }

  return $access_type_status;
}

/*
 * Return if user have access to access_type definition
 * Test does with user status
 * @return bool
*/
function is_autorize_status($access_type, $user_status = '')
{
  return (get_access_type_status($user_status) >= $access_type);
}

/*
 * Check if user have access to access_type definition
 * Stop action if there are not access
 * Test does with user status
 * @return none
*/
function check_status($access_type, $user_status = '')
{
  if (!is_autorize_status($access_type, $user_status))
  {
    access_denied();
  }
}

/*
 * Return if user is generic
 * @return bool
*/
 function is_generic($user_status = '')
{
  return get_user_status($user_status) == 'generic';
}

/*
 * Return if user is only a guest
 * @return bool
*/
 function is_a_guest($user_status = '')
{
  return get_user_status($user_status) == 'guest';
}

/*
 * Return if user is, at least, a classic user
 * @return bool
*/
 function is_classic_user($user_status = '')
{
  return is_autorize_status(ACCESS_CLASSIC, $user_status);
}

/*
 * Return if user is, at least, an administrator
 * @return bool
*/
 function is_admin($user_status = '')
{
  return is_autorize_status(ACCESS_ADMINISTRATOR, $user_status);
}

/*
 * Return if user is, at least, a webmaster
 * @return bool
*/
 function is_webmaster($user_status = '')
{
  return is_autorize_status(ACCESS_WEBMASTER, $user_status);
}

/*
 * Adviser status is depreciated from piwigo 2.2
 * @return false
*/
function is_adviser()
{
  // TODO for Piwigo 2.4 : trigger a warning. We don't do it on Piwigo 2.3
  // to avoid changes for plugin contributors
  // trigger_error('call to obsolete function is_adviser', E_USER_WARNING);
  return false;
}

/*
 * Return if current user can edit/delete/validate a comment
 * @param action edit/delete/validate
 * @return bool
 */
function can_manage_comment($action, $comment_author_id)
{
  global $user, $conf;
  
  if (is_a_guest())
  {
    return false;
  }
  
  if (!in_array($action, array('delete','edit', 'validate')))
  {
    return false;
  }

  if (is_admin())
  {
    return true;
  }

  if ('edit' == $action and $conf['user_can_edit_comment'])
  {
    if ($comment_author_id == $user['id']) {
      return true;
    }
  }

  if ('delete' == $action and $conf['user_can_delete_comment'])
  {
    if ($comment_author_id == $user['id']) {
      return true;
    }
  }

  return false;
}

/*
 * Return mail address as display text
 * @return string
*/
function get_email_address_as_display_text($email_address)
{
  global $conf;

  if (!isset($email_address) or (trim($email_address) == ''))
  {
    return '';
  }
  else
  {
    return $email_address;
  }
}

/*
 * Compute sql where condition with restrict and filter data. "FandF" means
 * Forbidden and Filters.
 *
 * @param array condition_fields: read function body
 * @param string prefix_condition: prefixes sql if condition is not empty
 * @param boolean force_one_condition: use at least "1 = 1"
 *
 * @return string sql where/conditions
 */
function get_sql_condition_FandF(
  $condition_fields,
  $prefix_condition = null,
  $force_one_condition = false
  )
{
  global $user, $filter;

  $sql_list = array();

  foreach ($condition_fields as $condition => $field_name)
  {
    switch($condition)
    {
      case 'forbidden_categories':
      {
        if (!empty($user['forbidden_categories']))
        {
          $sql_list[] =
            $field_name.' NOT IN ('.$user['forbidden_categories'].')';
        }
        break;
      }
      case 'visible_categories':
      {
        if (!empty($filter['visible_categories']))
        {
          $sql_list[] =
            $field_name.' IN ('.$filter['visible_categories'].')';
        }
        break;
      }
      case 'visible_images':
        if (!empty($filter['visible_images']))
        {
          $sql_list[] =
            $field_name.' IN ('.$filter['visible_images'].')';
        }
        // note there is no break - visible include forbidden
      case 'forbidden_images':
        if (
            !empty($user['image_access_list'])
            or $user['image_access_type']!='NOT IN'
            )
        {
          $table_prefix=null;
          if ($field_name=='id')
          {
            $table_prefix = '';
          }
          elseif ($field_name=='i.id')
          {
            $table_prefix = 'i.';
          }
          if ( isset($table_prefix) )
          {
            $sql_list[]=$table_prefix.'level<='.$user['level'];
          }
          else
          {
            $sql_list[]=$field_name.' '.$user['image_access_type']
                .' ('.$user['image_access_list'].')';
          }
        }
        break;
      default:
      {
        die('Unknow condition');
        break;
      }
    }
  }

  if (count($sql_list) > 0)
  {
    $sql = '('.implode(' AND ', $sql_list).')';
  }
  else
  {
    $sql = $force_one_condition ? '1 = 1' : '';
  }

  if (isset($prefix_condition) and !empty($sql))
  {
    $sql = $prefix_condition.' '.$sql;
  }

  return $sql;
}

/**
 * search an available activation_key
 *
 * @return string
 */
function get_user_activation_key()
{
  while (true)
  {
    $key = generate_key(20);
    $query = '
SELECT COUNT(*)
  FROM '.USER_INFOS_TABLE.'
  WHERE activation_key = \''.$key.'\'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if (0 == $count)
    {
      return $key;
    }
  }
}

?>
