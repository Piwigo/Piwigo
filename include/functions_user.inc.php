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

function register_user($login, $password, $password_conf,
                       $mail_address, $status = 'guest')
{
  global $lang, $conf;

  $errors = array();
  // login must not
  //      1. be empty
  //      2. start ou end with space character
  //      3. include ' or " characters
  //      4. be already used
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

  if (ereg("'", $login) or ereg("\"", $login))
  {
    array_push($errors, $lang['reg_err_login4']);
  }
  else
  {
    $query = '
SELECT id
  FROM '.USERS_TABLE.'
  WHERE username = \''.$login.'\'
;';
    $result = pwg_query($query);
    if (mysql_num_rows($result) > 0)
    {
      array_push($errors, $lang['reg_err_login5']);
    }
  }
  // given password must be the same as the confirmation
  if ($password != $password_conf)
  {
    array_push($errors, $lang['reg_err_pass']);
  }

  $error_mail_address = validate_mail_address($mail_address);
  if ($error_mail_address != '')
  {
    array_push($errors, $error_mail_address);
  }

  // if no error until here, registration of the user
  if (count($errors) == 0)
  {
    $insert = array();
    $insert['username'] = $login;
    $insert['password'] = md5($password);
    $insert['status'] = $status;
    $insert['template'] = $conf['default_template'];
    $insert['nb_image_line'] = $conf['nb_image_line'];
    $insert['nb_line_page'] = $conf['nb_line_page'];
    $insert['language'] = $conf['default_language'];
    $insert['recent_period'] = $conf['recent_period'];
    $insert['expand'] = boolean_to_string($conf['auto_expand']);
    $insert['show_nb_comments'] = boolean_to_string($conf['show_nb_comments']);
    if ( $mail_address != '' )
    {
      $insert['mail_address'] = $mail_address;
    }
    if ($conf['default_maxwidth'] != '')
    {
      $insert['maxwidth'] = $conf['default_maxwidth'];
    }
    if ($conf['default_maxheight'] != '')
    {
      $insert['maxheight'] = $conf['default_maxheight'];
    }

    $query = '
INSERT INTO '.USERS_TABLE.'
  ('.implode(',', array_keys($insert)).')
  VALUES
  (';
    $is_first = true;
    foreach (array_keys($insert) as $field)
    {
      if (!$is_first)
      {
        $query.= ',';
      }
      $query.= "'".$insert[$field]."'";
      $is_first = false;
    }
    $query.= ')
;';
    pwg_query($query);
  }
  return $errors;
}

function update_user( $user_id, $mail_address, $status,
                      $use_new_password = false, $password = '' )
{
  $error = array();
  $i = 0;
  
  $error_mail_address = validate_mail_address( $mail_address );
  if ( $error_mail_address != '' )
  {
    $error[$i++] = $error_mail_address;
  }

  if ( sizeof( $error ) == 0 )
  {
    $query = 'UPDATE '.USERS_TABLE;
    $query.= " SET status = '".$status."'";
    if ( $use_new_password )
    {
      $query.= ", password = '".md5( $password )."'";
    }
    $query.= ', mail_address = ';
    if ( $mail_address != '' )
    {
      $query.= "'".$mail_address."'";
    }
    else
    {
      $query.= 'NULL';
    }
    $query.= ' WHERE id = '.$user_id;
    $query.= ';';
    pwg_query( $query );
  }
  return $error;
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

function getuserdata($user)
{
  $sql = "SELECT * FROM " . USERS_TABLE;
  $sql.= " WHERE ";
  $sql .= ( ( is_integer($user) ) ? "id = $user" : "username = '" .  str_replace("\'", "''", $user) . "'" ) . " AND id <> " . ANONYMOUS;
  $result = pwg_query($sql);
  return ( $row = mysql_fetch_array($result) ) ? $row : false;
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
  
  $query = '
SELECT f.image_id
  FROM '.FAVORITES_TABLE.' AS f INNER JOIN '.IMAGE_CATEGORY_TABLE.' AS ic
    ON f.image_id = ic.image_id
  WHERE f.user_id = '.$user['id'].'
    AND ic.category_id IN ('.$user['forbidden_categories'].')
;';
  $result = pwg_query($query);
  $elements = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($elements, $row['image_id']);
  }

  if (count($elements) > 0)
  {
    $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE image_id IN ('.implode(',', $elements).')
    AND user_id = '.$user['id'].'
;';
    pwg_query($query);
  }
}

/**
 * update table user_forbidden for the given user
 *
 * table user_forbidden contains calculated data. Calculation is based on
 * private categories minus categories authorized to the groups the user
 * belongs to minus the categories directly authorized to the user
 *
 * @param int user_id
 * @return string forbidden_categories
 */
function calculate_permissions($user_id)
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
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($authorized_array, $row['cat_id']);
  }

  // retrieve category ids authorized to the groups the user belongs to
  $query = '
SELECT cat_id
  FROM '.USER_GROUP_TABLE.' AS ug INNER JOIN '.GROUP_ACCESS_TABLE.' AS ga
    ON ug.group_id = ga.group_id
  WHERE ug.user_id = '.$user_id.'
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    array_push($authorized_array, $row['cat_id']);
  }

  // uniquify ids : some private categories might be authorized for the
  // groups and for the user
  $authorized_array = array_unique($authorized_array);

  // only unauthorized private categories are forbidden
  $forbidden_array = array_diff($private_array, $authorized_array);

  $query = '
DELETE FROM '.USER_FORBIDDEN_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  $forbidden_categories = implode(',', $forbidden_array);
  
  $query = '
INSERT INTO '.USER_FORBIDDEN_TABLE.'
  (user_id,need_update,forbidden_categories)
  VALUES
  ('.$user_id.',\'false\',\''.$forbidden_categories.'\')
;';
  pwg_query($query);
  
  return $forbidden_categories;
}
?>
