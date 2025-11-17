<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\user
 */


/**
 * Checks if an email is well formed and not already in use.
 *
 * @param int $user_id
 * @param string $mail_address
 * @return string|void error message or nothing
 */
function validate_mail_address($user_id, $mail_address)
{
  global $conf;

  if (empty($mail_address) and
      !($conf['obligatory_user_mail_address'] and
      in_array(script_basename(), array('register', 'profile'))))
  {
    return '';
  }

  if ( !email_check_format($mail_address) )
  {
    return l10n('mail address must be like xxx@yyy.eee (example : jack@altern.org)');
  }

  if (defined("PHPWG_INSTALLED") and !empty($mail_address))
  {
    $query = '
SELECT count(*)
FROM '.USERS_TABLE.'
WHERE upper('.$conf['user_fields']['email'].') = upper(\''.$mail_address.'\')
'.(is_numeric($user_id) ? 'AND '.$conf['user_fields']['id'].' != \''.$user_id.'\'' : '').'
;';
    list($count) = pwg_db_fetch_row(pwg_query($query));
    if ($count != 0)
    {
      return l10n('this email address is already in use');
    }
  }
}

/**
 * Checks if a login is not already in use.
 * Comparision is case insensitive.
 *
 * @param string $login
 * @return string|void error message or nothing
 */
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
 * Searches for user with the same username in different case.
 *
 * @param string $username typically typed in by user for identification
 * @return string $username found in database
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

/**
 * Creates a new user.
 *
 * @param string $login
 * @param string $password
 * @param string $mail_adress
 * @param bool $notify_admin
 * @param array &$errors populated with error messages
 * @param bool $notify_user
 * @return int|false user id or false
 */
function register_user($login, $password, $mail_address, $notify_admin=true, &$errors = array(), $notify_user=false)
{
  global $conf;

  if ($login == '')
  {
    $errors[] = l10n('Please, enter a login');
  }
  if (preg_match('/^.* $/', $login))
  {
    $errors[] = l10n('login mustn\'t end with a space character');
  }
  if (preg_match('/^ .*$/', $login))
  {
    $errors[] = l10n('login mustn\'t start with a space character');
  }
  if (get_userid($login))
  {
    $errors[] = l10n('this login is already used');
  }
  if ($login != strip_tags($login))
  {
    $errors[] = l10n('html tags are not allowed in login');
  }
  $mail_error = validate_mail_address(null, $mail_address);
  if ('' != $mail_error)
  {
    $errors[] = $mail_error;
  }

  if ($conf['insensitive_case_logon'] == true)
  {
    $login_error = validate_login_case($login);
    if ($login_error != '')
    {
      $errors[] = $login_error;
    }
  }

  $errors = trigger_change(
    'register_user_check',
    $errors,
    array(
      'username'=>$login,
      'password'=>$password,
      'email'=>$mail_address,
      )
    );

  // if no error until here, registration of the user
  if (empty($errors))
  {
    $insert = array(
      $conf['user_fields']['username'] => $login,
      $conf['user_fields']['password'] => $conf['password_hash']($password),
      $conf['user_fields']['email'] => $mail_address
      );

    single_insert(USERS_TABLE, $insert);
    $user_id = pwg_db_insert_id();

    // Assign by default groups
    $query = '
SELECT id
  FROM `'.GROUPS_TABLE.'`
  WHERE is_default = \''.boolean_to_string(true).'\'
  ORDER BY id ASC
;';
    $result = pwg_query($query);

    $inserts = array();
    while ($row = pwg_db_fetch_assoc($result))
    {
      $inserts[] = array(
        'user_id' => $user_id,
        'group_id' => $row['id']
        );
    }

    if (count($inserts) != 0)
    {
      mass_inserts(USER_GROUP_TABLE, array('user_id', 'group_id'), $inserts);
    }

    $override = array();
    if ($conf['browser_language'] and $language = get_browser_language())
    {
      $override['language'] = $language;
    }
    
    create_user_infos($user_id, $override);

    if ($notify_admin and 'none' != $conf['email_admin_on_new_user'])
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
      $admin_url = get_absolute_root_url().'admin.php?page=user_list&user_id='.$user_id;

      $keyargs_content = array(
        get_l10n_args('User: %s', stripslashes($login) ),
        get_l10n_args('Email: %s', $mail_address),
        get_l10n_args(''),
        get_l10n_args('Admin: %s', $admin_url),
        );

      $group_id = null;
      if (preg_match('/^group:(\d+)$/', $conf['email_admin_on_new_user'], $matches))
      {
        $group_id = $matches[1];
      }

      pwg_mail_notification_admins(
        get_l10n_args('Registration of %s', stripslashes($login) ),
        $keyargs_content,
        true, // $send_technical_details
        $group_id
        );
    }

    if ($notify_user and email_check_format($mail_address))
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $length = rand(10, 15);
      $keyargs_content = array(
        get_l10n_args('Hello %s,', stripslashes($login)),
        get_l10n_args('Thank you for registering at %s!', $conf['gallery_title']),
        get_l10n_args('', ''),
        get_l10n_args('Here are your connection settings', ''),
        get_l10n_args('', ''),
        get_l10n_args('Link: %s', get_absolute_root_url()),
        get_l10n_args('Username: %s', stripslashes($login)),
        get_l10n_args('Password: %s', str_repeat("*", $length)),
        get_l10n_args('Email: %s', $mail_address),
        get_l10n_args('', ''),
        get_l10n_args('If you think you\'ve received this email in error, please contact us at %s', get_webmaster_mail_address()),
        );

      pwg_mail(
        $mail_address,
        array(
          'subject' => '['.$conf['gallery_title'].'] '.l10n('Registration'),
          'content' => l10n_args($keyargs_content),
          'content_format' => 'text/plain',
          )
        );
    }

    trigger_notify(
      'register_user',
      array(
        'id'=>$user_id,
        'username'=>$login,
        'email'=>$mail_address,
        )
      );

    pwg_activity('user', $user_id, 'add');

    return $user_id;
  }
  else
  {
    return false;
  }
}

/**
 * Fetches user data from database.
 * Same that getuserdata() but with additional tests for guest.
 *
 * @param int $user_id
 * @param boolean $user_cache
 * @return array
 */
function build_user($user_id, $use_cache=true)
{
  global $conf;

  $user['id'] = $user_id;
  $user = array_merge( $user, getuserdata($user_id, $use_cache) );

  if ($user['id'] == $conf['guest_id'] and $user['status'] <> 'guest')
  {
    $user['status'] = 'guest';
    $user['internal_status']['guest_must_be_guest'] = true;
  }

  // Check user theme. 2 possible problems:
  // 1. the user_infos.theme was not found in the themes table, thus themes.name is null
  // 2. the theme is not really installed on the filesystem
  if (!isset($user['theme_name']) or !check_theme_installed($user['theme']))
  {
    $user['theme'] = get_default_theme();
    $user['theme_name'] = $user['theme'];
  }

  return $user;
}

/**
 * Finds informations related to the user identifier.
 *
 * @param int $user_id
 * @param boolean $use_cache
 * @return array
 */
function getuserdata($user_id, $use_cache=false)
{
  global $conf, $logger;

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
  $userdata = array_merge($row, $user_infos_row);

  foreach ($userdata as &$value)
  {
      // If the field is true or false, the variable is transformed into a boolean value.
      if ($value == 'true')
      {
        $value = true;
      }
      elseif ($value == 'false')
      {
        $value = false;
      }
  }
  unset($value);

  $userdata['preferences'] = empty($userdata['preferences']) ? array() : unserialize($userdata['preferences']);

  if ($use_cache)
  {
    $generate_user_cache = false;
    $cache_generation_token_name = 'generate_user_cache-u'.$userdata['id'];
    $exec_code = substr(sha1(random_bytes(1000)), 0, 4);
    $logger_msg_prefix = '['.__FUNCTION__.'][exec_code='.$exec_code.'][user_id='.$userdata['id'].'] ';

    if (!isset($userdata['need_update'])
        or !is_bool($userdata['need_update'])
        or $userdata['need_update'] == true)
    {
      $logger->info($logger_msg_prefix.'needs user_cache to be rebuilt');

      $exec_id = pwg_unique_exec_begins($cache_generation_token_name);
      if (false === $exec_id)
      {
        $logger->info($logger_msg_prefix.'starts to wait for another request to build user_cache');
        $user_cache_waiting_start_time = get_moment();
        for ($k = 0; $k < 20; $k++)
        {
          sleep(1);

          $query = '
SELECT
   COUNT(*)
  FROM '.USER_CACHE_TABLE.'
  WHERE user_id='.$userdata['id'].'
;';
          list($nb_cache_lines) = pwg_db_fetch_row(pwg_query($query));

          $logger_msg = $logger_msg_prefix.'user_cache generation waiting k='.$k.' ';
          $waiting_time = get_elapsed_time($user_cache_waiting_start_time, get_moment());

          if ($nb_cache_lines > 0)
          {
            $logger->info($logger_msg.'user_cache rebuilt, after waiting '.$waiting_time);
            return getuserdata($user_id, false);
          }
          elseif (!pwg_unique_exec_is_running($cache_generation_token_name))
          {
            $logger->info($logger_msg.'user_cache rebuilt but has been reset since, give it another try, after waiting '.$waiting_time);
            return getuserdata($user_id, true);
          }
          else
          {
            $logger->info($logger_msg.'user_cache not ready yet, after waiting '.$waiting_time);
          }
        }

        $logger->info($logger_msg_prefix.'user_cache generation waiting has timed out after '.get_elapsed_time($user_cache_waiting_start_time, get_moment()));
        set_status_header(503, 'Service Unavailable');
        @header('Retry-After: 900');
        header('Content-Type: text/html; charset='.get_pwg_charset());
        echo l10n('Rebuilding user cache takes long. Please, come back later.');
        echo str_repeat( ' ', 512); //IE6 doesn't error output if below a size
        exit();
      }
      else
      {
        $generate_user_cache = true;
      }
    }

    if ($generate_user_cache)
    {
      $user_cache_generation_start_time = get_moment();
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
      $forbidden_ids = query2array($query,null, 'id');

      if ( empty($forbidden_ids) )
      {
        $forbidden_ids[] = 0;
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
            $forbidden_ids[] = $cat['cat_id'];
            remove_computed_category($user_cache_cats, $cat);
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
      mass_inserts(
        USER_CACHE_CATEGORIES_TABLE,
        array(
          'user_id', 'cat_id',
          'date_last', 'max_date_last', 'nb_images', 'count_images', 'nb_categories', 'count_categories'
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
    last_photo_date,
    image_access_type, image_access_list)
  VALUES
  ('.$userdata['id'].',\''.boolean_to_string($userdata['need_update']).'\','
  .$userdata['cache_update_time'].',\''
  .$userdata['forbidden_categories'].'\','.$userdata['nb_total_images'].','.
  (empty($userdata['last_photo_date']) ? 'NULL': '\''.$userdata['last_photo_date'].'\'').
  ',\''.$userdata['image_access_type'].'\',\''.$userdata['image_access_list'].'\')';
      pwg_query($query);

      pwg_unique_exec_ends($cache_generation_token_name);
      $logger->info($logger_msg_prefix.'user_cache generated, executed in '.get_elapsed_time($user_cache_generation_start_time, get_moment()));
    }
  }

  return $userdata;
}

/**
 * Deletes favorites of the current user if he's not allowed to see them.
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
  '.get_sql_condition_FandF(
      array(
        'forbidden_categories' => 'ic.category_id',
        ),
      'AND'
    ).'
;';
  $authorizeds = query2array($query,null, 'image_id');

  $query = '
SELECT image_id
  FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  $favorites = query2array($query,null, 'image_id');

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
 * Calculates the list of forbidden categories for a given user.
 *
 * Calculation is based on private categories minus categories authorized to
 * the groups the user belongs to minus the categories directly authorized
 * to the user. The list contains at least 0 to be compliant with queries
 * such as "WHERE category_id NOT IN ($forbidden_categories)"
 *
 * @param int $user_id
 * @param string $user_status
 * @return string comma separated ids
 */
function calculate_permissions($user_id, $user_status)
{
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE status = \'private\'
;';
  $private_array = query2array($query,null, 'id');

  // retrieve category ids directly authorized to the user
  $query = '
SELECT cat_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  $authorized_array = query2array($query,null, 'cat_id');

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
      query2array($query,null, 'cat_id')
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
    $forbidden_array = array_merge($forbidden_array, query2array($query, null, 'id') );
    $forbidden_array = array_unique($forbidden_array);
  }

  if ( empty($forbidden_array) )
  {// at least, the list contains 0 value. This category does not exists so
   // where clauses such as "WHERE category_id NOT IN(0)" will always be
   // true.
    $forbidden_array[] = 0;
  }

  return implode(',', $forbidden_array);
}

/**
 * Returns user identifier thanks to his name.
 *
 * @param string $username
 * @param int|false
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

/**
 * Returns user identifier thanks to his email.
 *
 * @param string $email
 * @param int|false
 */
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
 * Returns a array with default user valuees.
 *
 * @param convert_str ceonferts 'true' and 'false' into booleans
 * @return array
 */
function get_default_user_info($convert_str=true)
{
  global $cache, $conf;

  if (!isset($cache['default_user']))
  {
    $query = '
SELECT *
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$conf['default_user_id'].'
;';

    $result = pwg_query($query);

    if (pwg_db_num_rows($result) > 0)
    {
      $cache['default_user'] = pwg_db_fetch_assoc($result);

      unset($cache['default_user']['user_id']);
      unset($cache['default_user']['status']);
      unset($cache['default_user']['registration_date']);
      unset($cache['default_user']['last_visit']);
      unset($cache['default_user']['last_visit_from_history']);
    }
    else
    {
      $cache['default_user'] = false;
    }
  }

  if (is_array($cache['default_user']) and $convert_str)
  {
    $default_user = $cache['default_user'];
    foreach ($default_user as &$value)
    {
      // If the field is true or false, the variable is transformed into a boolean value.
      if ($value == 'true')
      {
        $value = true;
      }
      elseif ($value == 'false')
      {
        $value = false;
      }
    }
    return $default_user;
  }
  else
  {
    return $cache['default_user'];
  }
}

/**
 * Returns a default user value.
 *
 * @param string $value_name
 * @param mixed $default
 * @return mixed
 */
function get_default_user_value($value_name, $default)
{
  $default_user = get_default_user_info(true);
  if ($default_user === false or empty($default_user[$value_name]))
  {
    return $default;
  }
  else
  {
   return $default_user[$value_name];
  }
}

/**
 * Returns the default theme.
 * If the default theme is not available it returns the first available one.
 *
 * @return string
 */
function get_default_theme()
{
  $theme = get_default_user_value('theme', PHPWG_DEFAULT_TEMPLATE);
  if (check_theme_installed($theme))
  {
    return $theme;
  }

  // let's find the first available theme
  $active_themes = array_keys(get_pwg_themes());
  return isset($active_themes[0]) ? $active_themes[0] : 'default';
}

/**
 * Returns the default language.
 *
 * @return string
 */
function get_default_language()
{
  return get_default_user_value('language', PHPWG_DEFAULT_LANGUAGE);
}

/**
 * Tries to find the browser language among available languages.
 *
 * @return string
 */
function get_browser_language()
{
  $language_header = @$_SERVER['HTTP_ACCEPT_LANGUAGE'];
  if ($language_header == '')
  {
    return false;
  }

  // case insensitive match
  // 'en-US;q=0.9, fr-CH, kok-IN;q=0.7' => 'en_us;q=0.9, fr_ch, kok_in;q=0.7'
  $language_header = strtolower(str_replace("-", "_", $language_header));
  $match_pattern = '/(([a-z]{1,8})(?:_[a-z0-9]{1,8})*)\s*(?:;\s*q\s*=\s*([01](?:\.[0-9]{0,3})?))?/';
  $matches = null;
  preg_match_all($match_pattern, $language_header, $matches);
  $accept_languages_full = $matches[1];  // ['en-us', 'fr-ch', 'kok-in']
  $accept_languages_short = $matches[2];  // ['en', 'fr', 'kok']
  if (!count($accept_languages_full))
  {
    return false;
  }

  // if the quality value is absent for an language, use 1 as the default
  $q_values = $matches[3];  // ['0.9', '', '0.7']
  foreach ($q_values as $i => $q_value)
  {
    $q_values[$i] = ($q_values[$i] === '') ? 1 : floatval($q_values[$i]);
  }

  // since quick sort is not stable,
  // sort by $indices explicitly after sorting by $q_values
  $indices = range(1, count($q_values));
  array_multisort(
    $q_values, SORT_DESC, SORT_NUMERIC,
    $indices, SORT_ASC, SORT_NUMERIC,
    $accept_languages_full,
    $accept_languages_short
  );

  // list all enabled language codes in the Piwigo installation
  // in both full and short forms, and case insensitive
  $languages_available = array();
  foreach (get_languages() as $language_code => $language_name)
  {
    $lowercase_full = strtolower($language_code);
    $lowercase_parts = explode('_', $lowercase_full, 2);
    $lowercase_prefix = $lowercase_parts[0];
    $languages_available[$lowercase_full] = $language_code;
    $languages_available[$lowercase_prefix] = $language_code;
  }

  foreach ($q_values as $i => $q_value)
  {
    // if the exact language variant is present, make sure it's chosen
    // en-US;q=0.9 => en_us => en_US
    if (array_key_exists($accept_languages_full[$i], $languages_available))
    {
      return $languages_available[$accept_languages_full[$i]];
    }
    // only in case that an exact match was not available,
    // should we fallback to other variants in the same language family
    // fr_CH => fr => fr_FR
    else if (array_key_exists($accept_languages_short[$i], $languages_available))
    {
      return $languages_available[$accept_languages_short[$i]];
    }
  }

  return false;
}

/**
 * Creates user informations based on default values.
 *
 * @param int|int[] $user_ids
 * @param array $override_values values used to override default user values
 */
function create_user_infos($user_ids, $override_values=null)
{
  global $conf;

  if (!is_array($user_ids))
  {
    $user_ids = array($user_ids);
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
      elseif (($user_id == $conf['guest_id']) or
               ($user_id == $conf['default_user_id']))
      {
        $status = 'guest';
      }
      else
      {
        $status = 'normal';
      }
      
      $insert = array_merge(
        array_map('pwg_db_real_escape_string', $default_user),
        array(
          'user_id' => $user_id,
          'status' => $status,
          'registration_date' => $dbnow,
          'level' => $level
          ));

      $inserts[] = $insert;
    }

    mass_inserts(USER_INFOS_TABLE, array_keys($inserts[0]), $inserts);
  }
}

/**
 * Returns the auto login key for an user or false if the user is not found.
 *
 * @param int $user_id
 * @param int $time
 * @param string &$username fille with corresponding username
 * @return string|false
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

/**
 * Performs all required actions for user login.
 *
 * @param int $user_id
 * @param bool $remember_me
 */
function log_user($user_id, $remember_me)
{
  global $conf, $user;

  //New default login and register pages, if users changes languages and succesfully logs in
  //we want to update the userpref language stored in a cookie

  //TODO check value of cookie

  if (isset($_COOKIE['lang']) and $user['language'] != $_COOKIE['lang'])
  {
    if (!array_key_exists($_COOKIE['lang'], get_languages()))
    {
      fatal_error('[Hacking attempt] the input parameter "'.$_COOKIE['lang'].'" is not valid');
    }

    single_update(
      USER_INFOS_TABLE,
      array('language' => $_COOKIE['lang']),
      array('user_id' => $user_id)
    );

    // We unset the lang cookie, if user has changed their language using interface we don't want to keep setting it back 
    // to what was chosen using standard pages lang switch
    setcookie("lang", "", time() - 3600);
  }

  if ($remember_me and $conf['authorize_remembering'])
  {
    $now = time();
    $key = calculate_auto_login_key($user_id, $now, $username);
    if ($key!==false)
    {
      $cookie = $user_id.'-'.$now.'-'.$key;
      setcookie($conf['remember_me_name'],
        $cookie,
        time()+$conf['remember_me_length'],
        cookie_path(),ini_get('session.cookie_domain'),ini_get('session.cookie_secure'),
        ini_get('session.cookie_httponly')
        );
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
  trigger_notify('user_login', $user['id']);
  pwg_activity('user', $user['id'], 'login');
}

/**
 * Performs auto-connection when cookie remember_me exists.
 *
 * @return bool
 */
function auto_login()
{
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
        // Since Piwigo 16, 'connected_with' in the session defines the authentication context (UI, API, etc).
        // Auto-login via remember-me may miss this, so we set it to 'pwg_ui' for UI logins (not API).
        if (script_basename() != 'ws')
        {
          $_SESSION['connected_with'] = 'pwg_ui';
        }
        log_user($cookie[0], true);
        trigger_notify('login_success', stripslashes($username));
        return true;
      }
    }
    setcookie($conf['remember_me_name'], '', 0, cookie_path(),ini_get('session.cookie_domain'));
  }
  return false;
}

/**
 * Hashes a password with the PasswordHash class from phpass security library.
 * @since 2.5
 *
 * @param string $password plain text
 * @return string
 */
function pwg_password_hash($password)
{
  global $pwg_hasher;

  if (empty($pwg_hasher))
  {
    require_once(PHPWG_ROOT_PATH.'include/passwordhash.class.php');

    // We use the portable hash feature from phpass because we can't be sure
    // Piwigo runs on PHP 5.3+ (and won't run on an older version in the
    // future)
    $pwg_hasher = new PasswordHash(13, true);
  }

  return $pwg_hasher->HashPassword($password);
}

/**
 * Verifies a password, with the PasswordHash class from phpass security library.
 * If the hash is 'old' (assumed MD5) the hash is updated in database, used for
 * migration from Piwigo 2.4.
 * @since 2.5
 *
 * @param string $password plain text
 * @param string $hash may be md5 or phpass hashed password
 * @param integer $user_id only useful to update password hash from md5 to phpass
 * @return bool
 */
function pwg_password_verify($password, $hash, $user_id=null)
{
  global $conf, $pwg_hasher;

  // If the password has not been hashed with the current algorithm.
  if (strpos($hash, '$P') !== 0)
  {
    if (!empty($conf['pass_convert']))
    {
      $check = ($hash == $conf['pass_convert']($password));
    }
    else
    {
      $check = ($hash == md5($password));
    }

    if ($check)
    {
      if (!isset($user_id) or $conf['external_authentification'])
      {
        return true;
      }

      // Rehash using new hash.
      $hash = pwg_password_hash($password);

      single_update(
        USERS_TABLE,
        array('password' => $hash),
        array('id' => $user_id)
        );
    }
  }

  // If the stored hash is longer than an MD5, presume the
  // new style phpass portable hash.
  if (empty($pwg_hasher))
  {
    require_once(PHPWG_ROOT_PATH.'include/passwordhash.class.php');

    // We use the portable hash feature
    $pwg_hasher = new PasswordHash(13, true);
  }

  return $pwg_hasher->CheckPassword($password, $hash);
}

/**
 * Tries to login a user given username and password (must be MySql escaped).
 *
 * @param string $username
 * @param string $password
 * @param bool $remember_me
 * @return bool
 */
function try_log_user($username, $password, $remember_me)
{
  return trigger_change('try_log_user', false, $username, $password, $remember_me);
}

add_event_handler('try_log_user', 'pwg_login');

/**
 * Default method for user login, can be overwritten with 'try_log_user' trigger.
 * @see try_log_user()
 *
 * @param string $username
 * @param string $password
 * @param bool $remember_me
 * @return bool
 */
function pwg_login($success, $username, $password, $remember_me)
{
  if ($success===true)
  {
    return true;
  }

  // we force the session table to be clean
  pwg_session_gc();

  global $conf;

  // Find user by username or email (if it exists)
  $user_found = find_user_by_username_or_email($username);

  // SECURITY: Constant-time authentication to prevent timing attacks
  // 
  // We always perform password verification, even when the user doesn't exist,
  // to prevent attackers from distinguishing between:
  //  - "user exists, wrong password" (slow: runs password_verify)
  //  - "user doesn't exist" (fast: would skip verification)
  // 
  // This timing difference could allow user enumeration. By using a fake user
  // with a pre-generated hash, we ensure consistent execution time regardless
  // of whether the account exists or not.
  $fake_user = generate_fake_user();

  // Verify password with fallback to fake user
  $password_verify = $conf['password_verify'](
    $password,
    $user_found['password'] ?? $fake_user['password'],
    $user_found['id'] ?? $fake_user['id']
  );

  // If the user was not found, is a guest, or the password is incorrect
  if (empty($user_found) || 'guest' === $user_found['status'] || !$password_verify)
  {
    if (!empty($user_found) && !$password_verify)
    {
      pwg_activity('user', $user_found['id'], 'login_failure_wrong_password');
    }
    trigger_notify('login_failure', stripslashes($username));
    return false;
  }

  // PLUGIN HOOK: Allow plugins to intercept authentication before log_user()
  // 
  // Expected $state array structure:
  //  - 'can_login' (bool): Set to false to block login
  //  - 'reason' (string|null): Custom activity log reason if login blocked
  //  - 'authenticated' (bool): Set to true if plugin handles log_user() itself
  // 
  // Example plugin implementation:
  //   add_event_handler('finalize_login', 'my_2fa_check');
  //   function my_2fa_check($state, $user, $remember_me) {
  //     if (!verify_2fa_code()) {
  //       $state['can_login'] = false;
  //       $state['reason'] = '2fa_failed';
  //     }
  //     return $state;
  //   }
  $state = array(
    'can_login' => true,
    'reason' => null,
    'authenticated' => false,
  );
  $state = trigger_change('finalize_login', $state, $user_found, $remember_me);

  if (!$state['can_login'])
  {
    pwg_activity('user', $user_found['id'], $state['reason'] ?? 'login_failure_before_log_user');
    trigger_notify('login_failure_before_log_user', stripslashes($username));
    return false;
  }

  // If plugin handled authentication, skip log_user()
  if (!$state['authenticated'])
  {
    log_user($user_found['id'], $remember_me);
  }

  clear_fake_user_cache();
  trigger_notify('login_success', stripslashes($username));
  return true;
}


/**
 * Find user by username or email
 * search by username first then email
 *
 * @since 16
 * @param string $username_or_email
 * @return array|null
 */
function find_user_by_username_or_email($username_or_email)
{
  global $conf;

  $username_or_email = pwg_db_real_escape_string($username_or_email);

  $query = '
SELECT 
  '.$conf['user_fields']['id'].' AS id,
  '.$conf['user_fields']['username'].' AS username,
  '.$conf['user_fields']['email'].' AS email,
  '.$conf['user_fields']['password'].' AS password,
  status
FROM '.USERS_TABLE.' AS u
  LEFT JOIN '.USER_INFOS_TABLE.' AS i
    ON u.'.$conf['user_fields']['id'].' = i.user_id
  WHERE ';

  $where_username = $conf['user_fields']['username'].' = \'' . $username_or_email . '\'';
  $where_email = $conf['user_fields']['email'].' = \'' . $username_or_email . '\'';

  $user = pwg_db_fetch_assoc(pwg_query($query.$where_username))
    ?: pwg_db_fetch_assoc(pwg_query($query.$where_email));
  
  if (!empty($user))
  {
    // The user may not exist in the user_infos table, so we consider it's a "normal" user by default
    $user['status'] = $user['status'] ?? 'normal';
    return $user;
  }

  return null;
}

/**
 * Generate a fake user with hashed password (with the current algo)
 * 
 * SECURITY: This function is used for timing attack mitigation in pwg_login().
 * The fake user hash is cached per session to avoid repeated hashing overhead
 * while maintaining constant-time authentication behavior.
 * 
 * @since 16
 * @return array id and password
 */
function generate_fake_user()
{
  global $conf;

  // Check if password_hash or password_verify has been changed
  $is_verify_hash_changed = 'pwg_password_hash' !== $conf['password_hash']
    || 'pwg_password_verify' !== $conf['password_verify'];

  // Generate once per session to avoid repeated hashing overhead.
  // Uses current password_hash algorithm to match real user verification costs.
  if (!isset($_SESSION['fake_user_cache']) || $is_verify_hash_changed)
  {
    $fake_password = bin2hex(random_bytes(10));
    $_SESSION['fake_user_cache'] = array(
      'id' => null,
      'password' => $conf['password_hash']($fake_password)
    );
  }

  return $_SESSION['fake_user_cache'];
}

/**
 * Clear current session fake user cache
 * 
 * @since 16
 * @return void
 */
function clear_fake_user_cache()
{
  unset($_SESSION['fake_user_cache']);
}

/**
 * Performs all the cleanup on user logout.
 */
function logout_user()
{
  global $conf;

  trigger_notify('user_logout', @$_SESSION['pwg_uid']);
  pwg_activity('user', @$_SESSION['pwg_uid'], 'logout');

  $_SESSION = array();
  session_unset();
  session_destroy();
  setcookie(session_name(),'',0,
      ini_get('session.cookie_path'),
      ini_get('session.cookie_domain')
    );
  setcookie($conf['remember_me_name'], '', 0, cookie_path(),ini_get('session.cookie_domain'));
}

/**
 * Return user status.
 *
 * @param string $user_status used if $user not initialized
 * @return string
 */
function get_user_status($user_status='')
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

/**
 * Return ACCESS_* value for a given $status.
 *
 * @param string $user_status used if $user not initialized
 * @return int one of ACCESS_* constants
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

/**
 * Returns if user has access to a particular ACCESS_*
 *
 * @return int $access_type one of ACCESS_* constants
 * @param string $user_status used if $user not initialized
 * @return bool
 */
function is_autorize_status($access_type, $user_status='')
{
  return (get_access_type_status($user_status) >= $access_type);
}

/**
 * Abord script if user has no access to a particular ACCESS_*
 *
 * @return int $access_type one of ACCESS_* constants
 * @param string $user_status used if $user not initialized
 */
function check_status($access_type, $user_status='')
{
  if (!is_autorize_status($access_type, $user_status))
  {
    access_denied();
  }
}

/**
 * Returns if user is generic.
 *
 * @param string $user_status used if $user not initialized
 * @return bool
 */
function is_generic($user_status='')
{
  return get_user_status($user_status) == 'generic';
}

/**
 * Returns if user is a guest.
 *
 * @param string $user_status used if $user not initialized
 * @return bool
 */
function is_a_guest($user_status='')
{
  return get_user_status($user_status) == 'guest';
}

/**
 * Returns if user is, at least, a classic user.
 *
 * @param string $user_status used if $user not initialized
 * @return bool
 */
function is_classic_user($user_status='')
{
  return is_autorize_status(ACCESS_CLASSIC, $user_status);
}

/**
 * Returns if user is, at least, an administrator.
 *
 * @param string $user_status used if $user not initialized
 * @return bool
 */
function is_admin($user_status='')
{
  return is_autorize_status(ACCESS_ADMINISTRATOR, $user_status);
}

/**
 * Returns if user is a webmaster.
 *
 * @param string $user_status used if $user not initialized
 * @return bool
 */
function is_webmaster($user_status='')
{
  return is_autorize_status(ACCESS_WEBMASTER, $user_status);
}

/**
 * Returns if current user can edit/delete/validate a comment.
 *
 * @param string $action edit/delete/validate
 * @param int $comment_author_id
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

/**
 * Compute sql WHERE condition with restrict and filter data.
 * "FandF" means Forbidden and Filters.
 *
 * @param array $condition_fields one witch fields apply each filter
 *    - forbidden_categories
 *    - visible_categories
 *    - forbidden_images
 *    - visible_images
 * @param string $prefix_condition prefixes query if condition is not empty
 * @param boolean $force_one_condition use at least "1 = 1"
 * @return string
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
          elseif ( !empty($user['image_access_list']) and !empty($user['image_access_type']) )
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
 * Returns sql WHERE condition for recent photos/albums for current user.
 *
 * @param string $db_field
 * @return string
 */
function get_recent_photos_sql($db_field)
{
  global $user;
  if (!isset($user['last_photo_date']))
  {
    return '0=1';
  }
  return $db_field.'>=LEAST('
    .pwg_db_get_recent_period_expression($user['recent_period'])
    .','.pwg_db_get_recent_period_expression(1,$user['last_photo_date']).')';
}

/**
 * Performs auto-connection if authentication key is valid.
 *
 * @since 2.8
 *
 * @return bool
 */
function auth_key_login($auth_key, $connection_by_header=false)
{
  global $conf, $user, $page;

  $valid_key = false;
  $secret_key = null;
  if (preg_match('/^[a-z0-9]{30}$/i', $auth_key))
  {
    $valid_key = 'auth_key';
  }
  else if (preg_match('/^pkid-\d{8}-[a-z0-9]{20}:[a-z0-9]{40}$/i', $auth_key))
  {
    $valid_key = 'api_key';
    $tmp_key = explode(':', $auth_key);
    $auth_key = $tmp_key[0];
    $secret_key = $tmp_key[1];
  }

  if (!$valid_key) return false;

  $query = '
SELECT
    *,
    '.$conf['user_fields']['username'].' AS username,
    '.$conf['user_fields']['email'].' AS email,
    NOW() AS dbnow,
    DATEDIFF(uak.expired_on, NOW()) AS days_left,
    SUBDATE(NOW(), INTERVAL 48 HOUR) AS 48h_ago
  FROM '.USER_AUTH_KEYS_TABLE.' AS uak
    JOIN '.USER_INFOS_TABLE.' AS ui ON uak.user_id = ui.user_id
    JOIN '.USERS_TABLE.' AS u ON u.'.$conf['user_fields']['id'].' = ui.user_id
  WHERE auth_key = \''.$auth_key.'\'
;';
  $keys = query2array($query);

  if (count($keys) == 0)
  {
    return false;
  }
  
  $key = $keys[0];

  // is the key still valid?
  if (strtotime($key['expired_on']) < strtotime($key['dbnow']))
  {
    $page['auth_key_invalid'] = true;
    return false;
  }

  // admin/webmaster/guest can't get connected with authentication keys
  if ('auth_key' === $valid_key and !in_array($key['status'], array('normal','generic')))
  {
    return false;
  }

  // the key is an api_key
  if ('api_key' === $valid_key)
  {
    // check secret
    if (!pwg_password_verify($secret_key, $key['apikey_secret']))
    {
      return false;
    }

    // is the key is revoked?
    if (null != $key['revoked_on'])
    {
      return false;
    }

    // check if we need to notificate the user
    $days_left = intval($key['days_left']);
    if (
      $days_left <= 7 // the key expire in max 7 days
      and !empty($key['email']) // the user have an email
      and (
        null === $key['last_notified_on'] // we never send an email for this key
        or strtotime($key['last_notified_on']) < strtotime($key['48h_ago']) // OR when the last email was sent more than 48 hours ago
      )
    )
    {
      $page['notify_api_key_expiration'] = array(
        'days_left' => $days_left,
        'dbnow' => $key['dbnow'],
        'auth_key' => $key['auth_key']
      );
    }
  }

  $user['id'] = $key['user_id'];

  // update last used key 
  single_update(
    USER_AUTH_KEYS_TABLE,
    array('last_used_on' => $key['dbnow']),
    array(
      'user_id' => $user['id'],
      'auth_key' => $key['auth_key']
    ),   
  );

  // set the type of connection
  $_SESSION['connected_with'] = $valid_key;

  // if the connection is made via an API key in the header,
  // access is authenticated without creating a persistent user session
  // this enables stateless authentication for API calls
  if ($connection_by_header)
  {
    return true;
  }

  log_user($user['id'], false);
  trigger_notify('login_success', $key['username']);

  // to be registered in history table by pwg_log function
  $page['auth_key_id'] = $key['auth_key_id'];

  return true;
}

/**
 * Creates an authentication key.
 *
 * @since 2.8
 * @param int $user_id
 * @return array
 */
function create_user_auth_key($user_id, $user_status=null)
{
  global $conf;

  if (0 == $conf['auth_key_duration'])
  {
    return false;
  }

  if (!isset($user_status))
  {
    // we have to find the user status
    $query = '
SELECT
    status
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
    $user_infos = query2array($query);

    if (count($user_infos) == 0)
    {
      return false;
    }

    $user_status = $user_infos[0]['status'];
  }

  if (!in_array($user_status, array('normal','generic')))
  {
    return false;
  }
  
  $candidate = generate_key(30);
  
  $query = '
SELECT
    COUNT(*),
    NOW(),
    ADDDATE(NOW(), INTERVAL '.$conf['auth_key_duration'].' SECOND)
  FROM '.USER_AUTH_KEYS_TABLE.'
  WHERE auth_key = \''.$candidate.'\'
;';
  list($counter, $now, $expiration) = pwg_db_fetch_row(pwg_query($query));
  if (0 == $counter)
  {
    $key = array(
      'auth_key' => $candidate,
      'user_id' => $user_id,
      'created_on' => $now,
      'duration' => $conf['auth_key_duration'],
      'expired_on' => $expiration,
      'key_type' => 'auth_key',
      );
    
    single_insert(USER_AUTH_KEYS_TABLE, $key);

    $key['auth_key_id'] = pwg_db_insert_id();
    
    return $key;
  }
  else
  {
    return create_user_auth_key($user_id, $user_status);
  }
}

/**
 * Deactivates authentication keys
 *
 * @since 2.8
 * @param int $user_id
 * @return null
 */
function deactivate_user_auth_keys($user_id)
{
  $query = '
UPDATE '.USER_AUTH_KEYS_TABLE.'
  SET expired_on = NOW()
  WHERE user_id = '.$user_id.'
    AND expired_on > NOW()
    AND key_type = \'auth_key\'
;';
  pwg_query($query);
}

/**
 * Deactivates password reset key
 *
 * @since 11
 * @param int $user_id
 * @return null
 */
function deactivate_password_reset_key($user_id)
{
  single_update(
    USER_INFOS_TABLE,
    array(
      'activation_key' => null,
      'activation_key_expire' => null,
      ),
    array('user_id' => $user_id)
    );
}

/**
 * Generate reset password link
 *
 * @since 15
 * @param int $user_id
 * @param boolean $first_login
 * @return array time_validation and password link 
 */
function generate_password_link($user_id, $first_login=false)
{
  global $conf;

  $activation_key = generate_key(20);

  $duration = $first_login
  ? $conf['password_activation_duration'] 
  : $conf['password_reset_duration'];
  list($expire) = pwg_db_fetch_row(pwg_query('SELECT ADDDATE(NOW(), INTERVAL '. $duration .' SECOND)'));

  single_update(
    USER_INFOS_TABLE,
    array(
      'activation_key' => pwg_password_hash($activation_key),
      'activation_key_expire' => $expire,
      ),
    array('user_id' => $user_id)
    );

    set_make_full_url();

    $password_link = get_root_url().'password.php?key='.$activation_key;

    unset_make_full_url();

    $time_validation = time_since(
      strtotime('now -'.$duration.' second'),
      'second',
      null,
      false
    );

    return array(
      'time_validation' => $time_validation,
      'password_link' => $password_link,
    );
}

/**
 * Gets the last visit (datetime) of a user, based on history table
 *
 * @since 2.9
 * @param int $user_id
 * @param boolean $save_in_user_infos to store result in user_infos.last_visit
 * @return string date & time of last visit
 */
function get_user_last_visit_from_history($user_id, $save_in_user_infos=false)
{
  $last_visit = null;

  $query = '
SELECT
    date,
    time
FROM '.HISTORY_TABLE.'
  WHERE user_id = '.$user_id.'
  ORDER BY id DESC
  LIMIT 1
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $last_visit = $row['date'].' '.$row['time'];
  }

  if ($save_in_user_infos)
  {
    $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET last_visit = '.(is_null($last_visit) ? 'NULL' : "'".$last_visit."'").',
      last_visit_from_history = \'true\',
      lastmodified = lastmodified
  WHERE user_id = '.$user_id.'
';
    pwg_query($query);
  }

  return $last_visit;
}

/**
 * Save user preferences in database
 * @since 13
 */
function userprefs_save()
{
  global $user;

  $dbValue = pwg_db_real_escape_string(serialize($user['preferences']));

  $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET preferences = \''.$dbValue.'\'
  WHERE user_id = '.$user['id'].'
;';
  pwg_query($query);
}

/**
 * Add or update a user preferences parameter
 * @since 13
 *
 * @param string $param
 * @param string $value
 * @param boolean $updateGlobal update global *$conf* variable
 */
function userprefs_update_param($param, $value)
{
  global $user;

  // If the field is true or false, the variable is transformed into a boolean value.
  if ('true' == $value)
  {
    $value = true;
  }
  elseif ('false' == $value)
  {
    $value = false;
  }

  $user['preferences'][$param] = $value;

  userprefs_save();
}

/**
 * Delete one or more user preferences parameters
 * @since 13
 *
 * @param string|string[] $params
 */
function userprefs_delete_param($params)
{
  global $user;

  if (!is_array($params))
  {
    $params = array($params);
  }
  if (empty($params))
  {
    return;
  }

  foreach ($params as $param)
  {
    if (isset($user['preferences'][$param]))
    {
      unset($user['preferences'][$param]);
    }
  }

  userprefs_save();
}

/**
 * Return a default value for a user preferences parameter.
 * @since 13
 *
 * @param string $param the configuration value to be extracted (if it exists)
 * @param mixed $default_value the default value if it does not exist yet.
 *
 * @return mixed The configuration value if the variable exists, otherwise the default.
 */
function userprefs_get_param($param, $default_value=null)
{
  global $user;

  if (isset($user['preferences'][$param]))
  {
    return $user['preferences'][$param];
  }

  return $default_value;
}

/**
 * See if this is the first time the user has logged on
 *
 * @since 15
 * @param int $user_id
 * @return bool true if first connexion else false 
 */
function has_already_logged_in($user_id)
{
  $query = '
SELECT COUNT(*)
  FROM '.ACTIVITY_TABLE.'
  WHERE action = \'login\' and performed_by = '.$user_id.'';

  list($logged_in) = pwg_db_fetch_row(pwg_query($query));
  if ($logged_in > 0)
  {
    return false;
  }
  return true;
}

/**
 * Check all user infos and save parameters
 *
 * @since 16
 * @param mixed[] $params
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
function check_and_save_user_infos($params)
{
  if (isset($params['username']) and strlen(str_replace( " ", "",  $params['username'])) == 0)
  {
    // return new PwgError(WS_ERR_INVALID_PARAM, 'Name field must not be empty');
    return array(
      'error' => array(
        'code' => WS_ERR_INVALID_PARAM,
        'message' => 'Name field must not be empty'
      )
    );
  }

  global $conf, $user, $service;

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  $updates = $updates_infos = array();
  $update_status = null;

  if (count($params['user_id']) == 1)
  {
    if (get_username($params['user_id'][0]) === false)
    {
      // return new PwgError(WS_ERR_INVALID_PARAM, 'This user does not exist.');
      return array(
        'error' => array(
          'code' => WS_ERR_INVALID_PARAM,
          'message' => 'This user does not exist.'
        )
      );
    }

    if (!empty($params['username']))
    {
      $user_id = get_userid($params['username']);
      if ($user_id and $user_id != $params['user_id'][0])
      {
        // return new PwgError(WS_ERR_INVALID_PARAM, l10n('this login is already used'));
        return array(
          'error' => array(
            'code' => WS_ERR_INVALID_PARAM,
            'message' => l10n('this login is already used')
          )
        );
      }
      if ($params['username'] != strip_tags($params['username']))
      {
        // return new PwgError(WS_ERR_INVALID_PARAM, l10n('html tags are not allowed in login'));
        return array(
          'error' => array(
            'code' => WS_ERR_INVALID_PARAM,
            'message' => l10n('html tags are not allowed in login')
          )
        );
      }
      $updates[ $conf['user_fields']['username'] ] = $params['username'];
    }

    if (!empty($params['email']))
    {
      if ( ($error = validate_mail_address($params['user_id'][0], $params['email'])) != '')
      {
        // return new PwgError(WS_ERR_INVALID_PARAM, $error);
        return array(
          'error' => array(
            'code' => WS_ERR_INVALID_PARAM,
            'message' => $error
          )
        );
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
          // return new PwgError(403, 'Only webmasters can change password of other "webmaster/admin" users');
          return array(
            'error' => array(
              'code' => 403,
              'message' => 'Only webmasters can change password of other "webmaster/admin" users'
            )
          );
        }
      }

      $updates[ $conf['user_fields']['password'] ] = $conf['password_hash']($params['password']);
    }
  }

  if (!empty($params['status']))
  {
    if (in_array($params['status'], array('webmaster', 'admin')) and !is_webmaster() )
    {
      // return new PwgError(403, 'Only webmasters can grant "webmaster/admin" status');
      return array(
        'error' => array(
          'code '=> 403,
          'message' => 'Only webmasters can grant "webmaster/admin" status'
        )
      );
    }
    
    if ( !in_array($params['status'], array('guest','generic','normal','admin','webmaster')) )
    {
      // return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid status');
      return array(
        'error' => array(
          'code' => WS_ERR_INVALID_PARAM,
          'message' => 'Invalid status'
        )
      );
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
      // return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid level');
      return array(
        'error' => array(
          'code' => WS_ERR_INVALID_PARAM,
          'message' => 'Invalid level'
        )
      );
    }
    $updates_infos['level'] = $params['level'];
  }

  if (!empty($params['language']))
  {
    if ( !in_array($params['language'], array_keys(get_languages())) )
    {
      // return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid language');
      return array(
        'error' => array(
          'code' => WS_ERR_INVALID_PARAM,
          'message' => 'Invalid language'
        )
      );
    }
    $updates_infos['language'] = $params['language'];
  }

  if (!empty($params['theme']))
  {
    if ( !in_array($params['theme'], array_keys(get_pwg_themes())) )
    {
      // return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid theme');
      return array(
        'error' => array(
          'code' => WS_ERR_INVALID_PARAM,
          'message' => 'Invalid theme'
        )
      );
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

  if (isset($updates[ $conf['user_fields']['email'] ]))
  {
    deactivate_password_reset_key($params['user_id'][0]);
  }

  if (isset($update_status) and count($params['user_id_for_status']) > 0)
  {
    $query = '
UPDATE '. USER_INFOS_TABLE .' SET
    status = "'. $update_status .'"
  WHERE user_id IN('. implode(',', $params['user_id_for_status']) .')
;';
    pwg_query($query);

    // we delete sessions, ie disconnect, for users if status becomes "guest".
    // It's like deactivating the user.
    if ('guest' == $update_status)
    {
      foreach ($params['user_id_for_status'] as $user_id_for_status)
      {
        delete_user_sessions($user_id_for_status);
      }
    }
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

  return array(
    'user_id' => $params['user_id'],
    'infos' => $updates_infos,
    'account' => $updates
  );
}

/**
 * Create a new api_key
 *
 * @since 16
 * @param int $user_id
 * @param int|null $duration
 * @param string $key_name
 * @return array auth_key / apikey_secret / apikey_name / 
 * user_id / created_on / duration / expired_on / key_type
 */
function create_api_key($user_id, $duration, $key_name)
{
  $key_id = 'pkid-'.date('Ymd').'-'.generate_key(20);
  $key_secret = generate_key(40);

  list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));

  $key = array(
    'auth_key' => $key_id,
    'apikey_secret' => pwg_password_hash($key_secret),
    'apikey_name' => $key_name,
    'user_id' => $user_id,
    'created_on' => $dbnow,
    'key_type' => 'api_key'
  );

  if (!empty($duration))
  {
    $query = '
SELECT
  ADDDATE(NOW(), INTERVAL '.($duration * 60 * 60 * 24).' SECOND)
;';
    list($expiration) = pwg_db_fetch_row(pwg_query($query));
    $key['duration'] = $duration;
  }
  $key['expired_on'] = $expiration;
  
  single_insert(USER_AUTH_KEYS_TABLE, $key);

  $key['apikey_secret'] = $key_secret;
  return $key;
}

/**
 * Revoke a api_key
 *
 * @since 16
 * @param int $user_id
 * @param string $pkid
 * @return string|bool
 */
function revoke_api_key($user_id, $pkid)
{
  $query = '
SELECT 
  COUNT(*),
  NOW()
  FROM `'.USER_AUTH_KEYS_TABLE.'`
  WHERE auth_key = "'.$pkid.'"
  AND user_id = '.$user_id.'
;';

  list($key, $now) = pwg_db_fetch_row(pwg_query($query));
  if ($key == 0)
  {
    return l10n('API Key not found');
  }

  single_update(
    USER_AUTH_KEYS_TABLE,
    array('revoked_on' => $now),
    array(
      'auth_key' => $pkid,
      'user_id' => $user_id
    )
  );

  return true;
}

/**
 * Edit a api_key
 *
 * @since 16
 * @param int $user_id
 * @param string $pkid
 * @return string|bool
 */
function edit_api_key($user_id, $pkid, $api_name)
{
  $query = '
SELECT 
  COUNT(*)
  FROM `'.USER_AUTH_KEYS_TABLE.'`
  WHERE auth_key = "'.$pkid.'"
  AND user_id = '.$user_id.'
;';

  list($key) = pwg_db_fetch_row(pwg_query($query));
  if ($key == 0)
  {
    return l10n('API Key not found');
  }

  single_update(
    USER_AUTH_KEYS_TABLE,
    array('apikey_name' => $api_name),
    array(
      'auth_key' => $pkid,
      'user_id' => $user_id
    )
  );

  return true;
}

/**
 * Get all api_key
 *
 * @since 16
 * @param string $user_id
 * @return array|false
 */
function get_api_key($user_id)
{
  $query = '
SELECT *
  FROM `'.USER_AUTH_KEYS_TABLE.'`
  WHERE user_id = '.$user_id.'
  AND key_type = "api_key"
;';

  $api_keys = query2array($query);
  if (!$api_keys) return false;

  $query = '
SELECT
  NOW()
;';
  list($now) = pwg_db_fetch_row(pwg_query($query));

  foreach ($api_keys as $i => $api_key)
  {
    $api_key['apikey_secret'] = str_repeat("*", 40);
    unset($api_key['auth_key_id'], $api_key['user_id'], $api_key['key_type']);

    $api_key['apikey_name'] = stripslashes($api_key['apikey_name']);

    $api_key['created_on_format'] = format_date($api_key['created_on'], array('day', 'month', 'year'));
    $api_key['expired_on_format'] = format_date($api_key['expired_on'], array('day', 'month', 'year'));
    $api_key['last_used_on_since'] = 
      $api_key['last_used_on']
      ? time_since($api_key['last_used_on'], 'day') 
      : l10n('Never');

    $expired_on = str2DateTime($api_key['expired_on']);
    $now = str2DateTime($now);
    
    $api_key['is_expired'] = $expired_on < $now;
    if ($api_key['is_expired'])
    {
      $api_key['expiration'] = l10n('Expired');
    }
    else
    {
      $diff = dateDiff($now, $expired_on);
      if ($diff->days > 0)
      {
        $api_key['expiration'] = l10n('%d days', $diff->days);
      }
      elseif ($diff->h > 0)
      {
        $api_key['expiration'] = l10n('%d hours', $diff->h);
      }
      else
      {
        $api_key['expiration'] = l10n('%d minutes', $diff->i);
      }
    }
      
    $api_key['expired_on_since'] = time_since($api_key['expired_on'], 'day');

    $api_key['revoked_on_since'] = 
      $api_key['revoked_on']
      ? time_since($api_key['revoked_on'], 'day') 
      : null;

    $api_key['revoked_on_message'] =
      $api_key['revoked_on']
      ? l10n('This API key was manually revoked on %s', format_date($api_key['revoked_on'], array('day', 'month', 'year')))
      : null;

    $api_keys[$i] = $api_key;
  }

  return $api_keys;
}

/**
 * Get all available api_key
 *
 * @since 16
 * @param string $user_id
 * @return array|false
 */
function get_available_api_key($user_id)
{
  $api_keys = get_api_key($user_id);

  if (!$api_keys) return false;

  $available = array();
  foreach($api_keys as $api_key)
  {
    if (!$api_key['is_expired'] && empty($api_key['revoked_on']))
    {
      $available[] = $api_key;
    }
  }

  return count($available) > 0 ? $available : false;
}

/**
 * Is connected with pwg_ui (identification.php)
 *
 * @since 16
 * @return bool
 */
function connected_with_pwg_ui()
{
  // You can manage your api key only if you are connected via identification.php
  if (isset($_SESSION['connected_with']) and 'pwg_ui' === $_SESSION['connected_with'])
  {
    return true;
  }
  return false;
}

/**
 * Notify an user when his api key is about to expire
 *
 * @since 16
 * @return bool
 */
function notification_api_key_expiration($username, $email, $days_left)
{
  global $conf;

  include_once(PHPWG_ROOT_PATH . 'include/functions_mail.inc.php');
  $days_left_str = $days_left <= 1 ? 
    l10n('Your API key will expire in %d day.', $days_left)
    : l10n('Your API key will expire in %d days.', $days_left);

  $message = '<p style="margin: 20px 0">' . l10n('Hello %s,', $username) . '</p>';
  $message .= '<p style="margin: 20px 0">' . $days_left_str . '</p>';
  $message .= '<p style="margin: 20px 0">' . l10n('To continue using the API, please renew your key before it expires.') . '</p>';
  $message .= '<p style="margin: 20px 0">' . l10n('You can manage your API keys in your <a href="%s">account settings.</a>', get_absolute_root_url().'profile.php') . '</p>';

  $result = @pwg_mail(
    $email,
    array(
      'subject' => '[' . $conf['gallery_title'] . '] ' . l10n('Your API key will expire soon'),
      'content' => $message,
      'content_format' => 'text/html',
    )
  );

  return $result;
}

/**
 * Generate an user code for verification
 *
 * @since 16
 * @return array [$secret, $code]
 */
function generate_user_code()
{
  global $conf;
  
  require_once(PHPWG_ROOT_PATH . 'include/totp.class.php');
  $secret = PwgTOTP::generateSecret();
  $code = PwgTOTP::generateCode($secret, min($conf['password_reset_code_duration'], 900)); // max 15 minutes

  return array(
    'secret' => $secret,
    'code' => $code
  );
}

/**
 * Verify user code
 *
 * @since 16
 * @param string $secret
 * @param string $code
 * @return bool
 */
function verify_user_code($secret, $code)
{
  global $conf;

  require_once(PHPWG_ROOT_PATH . 'include/totp.class.php');
  return PwgTOTP::verifyCode($code, $secret, min($conf['password_reset_code_duration'], 900), 1);
}

/**
 * Register in the user session, the "context" of the last 10 viewed images.
 *
 * @since 16
 */
function save_edit_context()
{
  global $page;

  if (!is_admin() or !isset($page['section_url']) or !isset($page['image_id']))
  {
    return;
  }

  $_SESSION['edit_context'] ??= [];

  // the $page['section_url'] is set in the include/section_init script. It
  // contains the URL describing the "context" of the photo. Examples:
  //
  // * /198/list/2,69,198
  // * /198/category/18801-yes_man
  // * /198/tags/27-city_nantes/28-city_rennes
  // * /198/search/psk-20251103-lqCHHAFSZY/posted-monthly-list-2025-3
  //
  // same photo #198 in different context. We need it to propose the best
  // return page on the photo edit page in the administration.

  // let's add the item on top of previous registered values and keep only the last 10 values
  $_SESSION['edit_context'] = array_slice(array($page['image_id'] => $page['section_url']) + $_SESSION['edit_context'], 0, 10, true);
}

/**
 * Returns the "context" of the requested image.
 *
 * @since 16
 * @param int $image_id
 * @return string|bool
 */
function get_edit_context($image_id)
{
  if (!isset($_SESSION['edit_context'][$image_id]))
  {
    return false;
  }

  return preg_replace('/^\/'.$image_id.'\//', '', $_SESSION['edit_context'][$image_id]);
}
?>
