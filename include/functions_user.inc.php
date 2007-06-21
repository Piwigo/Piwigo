<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

// validate_mail_address verifies whether the given mail address has the
// right format. ie someone@domain.com "someone" can contain ".", "-" or
// even "_". Exactly as "domain". The extension doesn't have to be
// "com". The mail address can also be empty.
// If the mail address doesn't correspond, an error message is returned.
function validate_mail_address( $mail_address )
{
  global $lang, $conf;

  if (empty($mail_address) and
      !($conf['obligatory_user_mail_address'] and in_array(script_basename(), array('register', 'profile'))))
  {
    return '';
  }
  $regex = '/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.[a-z]+$/';
  if ( !preg_match( $regex, $mail_address ) )
  {
    return $lang['reg_err_mail_address'];
  }
}

function register_user($login, $password, $mail_address, $errors = array())
{
  global $lang, $conf;

  if ($login == '')
  {
    array_push($errors, $lang['reg_err_login1']);
  }
  if (ereg("^.* $", $login))
  {
    array_push($errors, $lang['reg_err_login2']);
  }
  if (ereg("^ .*$", $login))
  {
    array_push($errors, $lang['reg_err_login3']);
  }
  if (get_userid($login))
  {
    array_push($errors, $lang['reg_err_login5']);
  }
  $mail_error = validate_mail_address($mail_address);
  if ('' != $mail_error)
  {
    array_push($errors, $mail_error);
  }

  // if no error until here, registration of the user
  if (count($errors) == 0)
  {
    // what will be the inserted id ?
    $query = '
SELECT MAX('.$conf['user_fields']['id'].') + 1
  FROM '.USERS_TABLE.'
;';
    list($next_id) = mysql_fetch_array(pwg_query($query));

    $insert =
      array(
        $conf['user_fields']['id'] => $next_id,
        $conf['user_fields']['username'] => mysql_escape_string($login),
        $conf['user_fields']['password'] => $conf['pass_convert']($password),
        $conf['user_fields']['email'] => $mail_address
        );

    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
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
    while ($row = mysql_fetch_array($result))
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

    if (count($inserts) != 0)
    {
      include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
      mass_inserts(USER_GROUP_TABLE, array('user_id', 'group_id'), $inserts);
    }
  }

    create_user_infos($next_id);

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

function setup_style($style)
{
  return new Template(PHPWG_ROOT_PATH.'template/'.$style);
}

function build_user( $user_id, $use_cache )
{
  global $conf;

  $user['id'] = $user_id;
  $user = array_merge( $user, getuserdata($user_id, $use_cache) );

  // calculation of the number of picture to display per page
  $user['nb_image_page'] = $user['nb_image_line'] * $user['nb_line_page'];

  if (is_admin($user['status']))
  {
    list($user['admin_template'], $user['admin_theme']) =
      explode
      (
        '/',
        isset($conf['default_admin_layout']) ? $conf['default_admin_layout']
                                             : $user['template']
      );
  }

  list($user['template'], $user['theme']) = explode('/', $user['template']);

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
  WHERE '.$conf['user_fields']['id'].' = \''.$user_id.'\'
;';

  $row = mysql_fetch_array(pwg_query($query));

  while (true)
  {
    $query = '
SELECT ui.*, uc.*
  FROM '.USER_INFOS_TABLE.' AS ui LEFT JOIN '.USER_CACHE_TABLE.' AS uc
    ON ui.user_id = uc.user_id
  WHERE ui.user_id = \''.$user_id.'\'
;';
    $result = pwg_query($query);
    if (mysql_num_rows($result) > 0)
    {
      break;
    }
    else
    {
      create_user_infos($user_id);
    }
  }

  $row = array_merge($row, mysql_fetch_array($result));

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
      $userdata['forbidden_categories'] =
        calculate_permissions($userdata['id'], $userdata['status']);

      update_user_cache_categories($userdata);

      // Set need update are done
      $userdata['need_update'] = false;

      // Indicate update done
      $userdata['need_update_done'] = true;

      $query = '
SELECT COUNT(DISTINCT(image_id)) as total
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id NOT IN ('.$userdata['forbidden_categories'].')
;';
      list($userdata['nb_total_images']) = mysql_fetch_array(pwg_query($query));

      // update user cache
      $query = '
DELETE FROM '.USER_CACHE_TABLE.'
  WHERE user_id = '.$userdata['id'].'
;';
      pwg_query($query);

      $query = '
INSERT INTO '.USER_CACHE_TABLE.'
  (user_id, need_update, forbidden_categories, nb_total_images)
  VALUES
  ('.$userdata['id'].',\''.boolean_to_string($userdata['need_update']).'\',\''
  .$userdata['forbidden_categories'].'\','.$userdata['nb_total_images'].')
;';
      pwg_query($query);
    }
    else
    {
      // Indicate update not done
      $userdata['need_update_done'] = false;
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
  while ($row = mysql_fetch_array($result))
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
  while ($row = mysql_fetch_array($result))
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
  while ($row = mysql_fetch_array($result))
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
    while ($row = mysql_fetch_array($result))
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
  $group_by = '';

  $query = 'SELECT c.id cat_id, global_rank';
  if ( !isset($filter_days) )
  {
    $query .= ',
    date_last cat_date_last,
    nb_images cat_nb_images
  FROM '.CATEGORIES_TABLE.' as c';
  }
  else
  {
    // Count by date_available to avoid count null
    $query .= ',
    MAX(date_available) cat_date_last,
    COUNT(date_available) cat_nb_images
  FROM '.CATEGORIES_TABLE.' as c
    LEFT JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.category_id = c.id
    LEFT JOIN '.IMAGES_TABLE.' AS i
      ON ic.image_id = i.id AND
          i.date_available > SUBDATE(CURRENT_DATE,INTERVAL '.$filter_days.' DAY)';
    $group_by = 'c.id';
  }

  if ( !empty($userdata['forbidden_categories']) )
  {
    $query.= '
  WHERE c.id NOT IN ('.$userdata['forbidden_categories'].')';
  }

  if ( !empty($group_by) )
  {
    $query.= '
  GROUP BY '.$group_by;
  }

  $result = pwg_query($query);

  $cats = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $row['user_id'] = $userdata['id'];
    $row['count_categories'] = 0;
    $row['count_images'] = $row['cat_nb_images'];
    $row['max_date_last'] = $row['cat_date_last'];

    $cats += array($row['cat_id'] => $row);
  }
  usort($cats, 'global_rank_compare');

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
        $category['count_images'] = $category['cat_nb_images'];
        // next line for update_cats_with_filtered_data
        $category['nb_images'] = $category['cat_nb_images'];
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
 * update data of user_cache_categories
 *
 * @param array userdata
 * @return null
 */
function update_user_cache_categories($userdata)
{
  // delete user cache
  $query = '
DELETE FROM '.USER_CACHE_CATEGORIES_TABLE.'
  WHERE user_id = '.$userdata['id'].'
;';
  pwg_query($query);

  $cats = get_computed_categories($userdata, null);

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  mass_inserts
  (
    USER_CACHE_CATEGORIES_TABLE,
    array
    (
      'user_id', 'cat_id',
      'max_date_last', 'count_images', 'count_categories'
    ),
    $cats
  );
}

/**
 * returns the username corresponding to the given user identifier if exists
 *
 * @param int user_id
 * @return mixed
 */
function get_username($user_id)
{
  global $conf;

  $query = '
SELECT '.$conf['user_fields']['username'].'
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.intval($user_id).'
;';
  $result = pwg_query($query);
  if (mysql_num_rows($result) > 0)
  {
    list($username) = mysql_fetch_row($result);
  }
  else
  {
    return false;
  }

  return $username;
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

  $username = mysql_escape_string($username);

  $query = '
SELECT '.$conf['user_fields']['id'].'
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.$username.'\'
;';
  $result = pwg_query($query);

  if (mysql_num_rows($result) == 0)
  {
    return false;
  }
  else
  {
    list($user_id) = mysql_fetch_row($result);
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
    list($count) = mysql_fetch_row(pwg_query($query));
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
  global $page, $conf;
  
  if (!isset($page['cache_default_user']))
  {
    $query = 'select * from '.USER_INFOS_TABLE.
            ' where user_id = '.$conf['default_user_id'].';';

    $result = pwg_query($query);
    $page['cache_default_user'] = mysql_fetch_assoc($result);
    
    if ($page['cache_default_user'] !== false)
    {
      unset($page['cache_default_user']['user_id']);
      unset($page['cache_default_user']['status']);
      unset($page['cache_default_user']['registration_date']);
    }
  }

  if (is_array($page['cache_default_user']) and $convert_str)
  {
    $default_user = array();
    foreach ($page['cache_default_user'] as $name => $value)
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
    return $page['cache_default_user'];
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
  if ($default_user === false or !isset($default_user[$value_name]))
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
function get_default_template()
{
  return get_default_user_value('template', PHPWG_DEFAULT_TEMPLATE);
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
    if (is_integer($arg_id))
    {
      $user_ids[] = $arg_id;
    }
  }

  if (!empty($user_ids))
  {
    $inserts = array();
    list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

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
      if ($user_id == $conf['webmaster_id'])
      {
        $status = 'webmaster';
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
          'registration_date' => $dbnow
          ));

      array_push($inserts, $insert);
      }

    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    mass_inserts(USER_INFOS_TABLE, array_keys($inserts[0]), $inserts);

  }
}

/**
 * returns the groupname corresponding to the given group identifier if
 * exists
 *
 * @param int group_id
 * @return mixed
 */
function get_groupname($group_id)
{
  $query = '
SELECT name
  FROM '.GROUPS_TABLE.'
  WHERE id = '.intval($group_id).'
;';
  $result = pwg_query($query);
  if (mysql_num_rows($result) > 0)
  {
    list($groupname) = mysql_fetch_row($result);
  }
  else
  {
    return false;
  }

  return $groupname;
}

/**
 * return the file path of the given language filename, depending on the
 * availability of the file
 *
 * in descending order of preference: 
 *   param language, user language, default language
 * PhpWebGallery default language.
 *
 * @param string filename
 * @param string dirname
 * @param string language
 * @return string filepath
 */
function get_language_filepath($filename, $dirname = '', $language = '')
{
  global $user, $conf;

  if (empty($dirname))
  {
    $dirname = PHPWG_ROOT_PATH;
  }
  $dirname .= 'language'.'/';

  $dir_methods = array();

  if (!empty($language))
  {
    $dir_methods[] = 1;
  }

  $dir_methods[] = 2;
  $dir_methods[] = 3;
  $dir_methods[] = 4;

  foreach ($dir_methods as $dir_method)
  {
    switch ($dir_method)
    {
      case '1':
      {
        $directory = $dirname.$language;
        break;
      }
      case '2':
      {
        $directory = $dirname.$user['language'];
        break;
      }
      case '3':
      {
        $directory = $dirname.get_default_language();
        break;
      }
      case '4':
      default:
      {
        $directory = $dirname.PHPWG_DEFAULT_LANGUAGE;
        break;
      }
      {
        $directory = '.';
      }
    }

    $filepath = $directory.'/'.$filename;

    if (file_exists($filepath))
    {
      return $filepath;
    }
  }

  return false;
}

/**
 * returns the auto login key or false on error
 * @param int user_id
 * @param string [out] username
*/
function calculate_auto_login_key($user_id, &$username)
{
  global $conf;
  $query = '
SELECT '.$conf['user_fields']['username'].' AS username
  , '.$conf['user_fields']['password'].' AS password
FROM '.USERS_TABLE.'
WHERE '.$conf['user_fields']['id'].' = '.$user_id;
  $result = pwg_query($query);
  if (mysql_num_rows($result) > 0)
  {
    $row = mysql_fetch_assoc($result);
    $username = $row['username'];
    $data = $row['username'].$row['password'];
    $key = base64_encode(
      pack('H*', sha1($data))
      .hash_hmac('md5', $data, $conf['secret_key'],true)
      );
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
    $key = calculate_auto_login_key($user_id, $username);
    if ($key!==false)
    {
      $cookie = array('id' => (int)$user_id, 'key' => $key);
      setcookie($conf['remember_me_name'],
            serialize($cookie),
            time()+$conf['remember_me_length'],
            cookie_path()
          );
    }
  }
  else
  { // make sure we clean any remember me ...
    setcookie($conf['remember_me_name'], '', 0, cookie_path());
  }
  if ( session_id()!="" )
  { // we regenerate the session for security reasons
    // see http://www.acros.si/papers/session_fixation.pdf
    session_regenerate_id();
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
    $cookie = unserialize(stripslashes($_COOKIE[$conf['remember_me_name']]));
    if ($cookie!==false and is_numeric(@$cookie['id']) )
    {
      $key = calculate_auto_login_key( $cookie['id'], $username );
      if ($key!==false and $key===$cookie['key'])
      {
        log_user($cookie['id'], true);
        trigger_action('login_success', $username);
        return true;
      }
    }
    setcookie($conf['remember_me_name'], '', 0, cookie_path());
  }
  return false;
}

/**
 * Tries to login a user given username and password (must be MySql escaped)
 * return true on success
 */
function try_log_user($username, $password, $remember_me)
{
  global $conf;
  // retrieving the encrypted password of the login submitted
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['password'].' AS password
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.$username.'\'
;';
  $row = mysql_fetch_assoc(pwg_query($query));
  if ($row['password'] == $conf['pass_convert']($password))
  {
    log_user($row['id'], $remember_me);
    trigger_action('login_success', $username);
    return true;
  }
  trigger_action('login_failure', $username);
  return false;
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
 * Return access_type definition of uuser
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
        ($conf['guest_access'] ? ACCESS_GUEST : ACCESS_NONE);
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
    case 'default':
    {
      $access_type_status = ACCESS_NONE;
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
 * Return if current user is an adviser
 * @return bool
*/
function is_adviser()
{
  global $user;

  return ($user['adviser'] == 'true');
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
    if (is_adviser())
    {
      return 'adviser.mode@'.$_SERVER['SERVER_NAME'];
    }
    else
    {
      return $email_address;
    }
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
      {
        if (!empty($filter['visible_images']))
        {
          $sql_list[] =
            $field_name.' IN ('.$filter['visible_images'].')';
        }
        break;
      }
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
    if ($force_one_condition)
    {
      $sql = '1 = 1';
    }
    else
    {
      $sql = '';
    }
  }

  if (isset($prefix_condition) and !empty($sql))
  {
    $sql = $prefix_condition.' '.$sql;
  }

  return $sql;
}

?>