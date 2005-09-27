<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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
  global $lang;

  if ( $mail_address == '' )
  {
    return '';
  }
  $regex = '/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)*\.[a-z]+$/';
  if ( !preg_match( $regex, $mail_address ) )
  {
    return $lang['reg_err_mail_address'];
  }
}

function register_user($login, $password, $mail_address)
{
  global $lang, $conf;

  $errors = array();
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
    $insert =
      array(
        $conf['user_fields']['username'] => mysql_escape_string($login),
        $conf['user_fields']['password'] => $conf['pass_convert']($password),
        $conf['user_fields']['email'] => $mail_address
        );

    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    mass_inserts(USERS_TABLE, array_keys($insert), array($insert));
    
    create_user_infos(mysql_insert_id());
  }
  
  return $errors;
}

function check_login_authorization($guest_allowed = true)
{
  global $user,$lang,$conf,$template;

  if ($user['is_the_guest'] and !$guest_allowed)
  {
    echo '<div style="text-align:center;">'.$lang['only_members'].'<br />';
    echo '<a href="./identification.php">'.$lang['ident_title'].'</a></div>';
    exit();
  }

  if ($conf['gallery_locked'])
  {
    echo '<div style="text-align:center;">';
    echo $lang['gallery_locked_message'];
    echo '</div>';
    if ($user['status'] != 'admin')
    {
      exit();
    }
  }
}

function setup_style($style)
{
  return new Template(PHPWG_ROOT_PATH.'template/'.$style);
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

      // update user cache
      $query = '
DELETE FROM '.USER_CACHE_TABLE.'
  WHERE user_id = '.$userdata['id'].'
;';
      pwg_query($query);
  
      $query = '
INSERT INTO '.USER_CACHE_TABLE.'
  (user_id,need_update,forbidden_categories)
  VALUES
  ('.$userdata['id'].',\'false\',\''.$userdata['forbidden_categories'].'\')
;';
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

  // retrieving images allowed : belonging to at least one authorized
  // category
  $query = '
SELECT DISTINCT f.image_id
  FROM '.FAVORITES_TABLE.' AS f INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
    ON f.image_id = ic.image_id
  WHERE f.user_id = '.$user['id'].'
    AND ic.category_id NOT IN ('.$user['forbidden_categories'].')
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

  // if user is not an admin, locked categories can be considered as private$
  if ($user_status != 'admin')
  {
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE visible = \'false\'
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($private_array, $row['id']);
    }

    $private_array = array_unique($private_array);
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

  // at least, the list contains -1 values. This category does not exists so
  // where clauses such as "WHERE category_id NOT IN(-1)" will always be
  // true.
  array_push($forbidden_array, '-1');
  
  return implode(',', $forbidden_array);
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

/**
 * add user informations based on default values
 *
 * @param int user_id
 */
function create_user_infos($user_id)
{
  global $conf;
  
  list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

  $insert =
    array(
      'user_id' => $user_id,
      'status' => $user_id == $conf['webmaster_id'] ? 'admin' : 'guest',
      'template' => $conf['default_template'],
      'nb_image_line' => $conf['nb_image_line'],
      'nb_line_page' => $conf['nb_line_page'],
      'language' => $conf['default_language'],
      'recent_period' => $conf['recent_period'],
      'expand' => boolean_to_string($conf['auto_expand']),
      'show_nb_comments' => boolean_to_string($conf['show_nb_comments']),
      'maxwidth' => $conf['default_maxwidth'],
      'maxheight' => $conf['default_maxheight'],
      'registration_date' => $dbnow
      );

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  mass_inserts(USER_INFOS_TABLE, array_keys($insert), array($insert));
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
 * in descending order of preference: user language, default language,
 * PhpWebGallery default language.
 *
 * @param string filename
 * @return string filepath
 */
function get_language_filepath($filename)
{
  global $user, $conf;
  
  $directories =
    array(
      PHPWG_ROOT_PATH.'language/'.$user['language'],
      PHPWG_ROOT_PATH.'language/'.$conf['default_language'],
      PHPWG_ROOT_PATH.'language/'.PHPWG_DEFAULT_LANGUAGE
      );

  foreach ($directories as $directory)
  {
    $filepath = $directory.'/'.$filename;
    
    if (file_exists($filepath))
    {
      return $filepath;
    }
  }
  
  return false;
}
?>