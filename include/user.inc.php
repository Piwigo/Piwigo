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

// retrieving connected user informations

if (isset($_COOKIE['id']))
{
  $session_id = $_COOKIE['id'];
  $user['has_cookie'] = true;
}
else if (isset($_GET['id']))
{
  $session_id = $_GET['id'];
  $user['has_cookie'] = false;
}
else
{
  $user['has_cookie'] = false;
}

if (isset($session_id)
    and ereg("^[0-9a-zA-Z]{".$conf['session_id_size']."}$", $session_id))
{
  $page['session_id'] = $session_id;
  $query = '
SELECT user_id,expiration,NOW() AS now
  FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$page['session_id'].'\'
;';
  $result = pwg_query($query);
  if (mysql_num_rows($result) > 0)
  {
    $row = mysql_fetch_array($result);
    if (strnatcmp($row['expiration'], $row['now']) < 0)
    {
      // deletion of the session from the database, because it is
      // out-of-date
      $delete_query = '
DELETE FROM '.SESSIONS_TABLE.'
  WHERE id = \''.$page['session_id'].'\'
;';
      pwg_query($delete_query);
    }
    else
    {
      $user['id'] = $row['user_id'];
      $user['is_the_guest'] = false;
    }
  }
}
if (!isset($user['id']))
{
  $user['id'] = 2;
  $user['is_the_guest'] = true;
}

$query = '
SELECT u.*, uf.*
  FROM '.USERS_TABLE.' AS u LEFT JOIN '.USER_FORBIDDEN_TABLE.' AS uf
    ON id = user_id
  WHERE u.id = '.$user['id'].'
;';
$row = mysql_fetch_array(pwg_query($query));

// affectation of each value retrieved in the users table into a variable of
// the array $user.
foreach ($row as $key => $value)
{
  if (!is_numeric($key))
  {
    // If the field is true or false, the variable is transformed into a
    // boolean value.
    if ($value == 'true' or $value == 'false')
    {
      $user[$key] = get_boolean($value);
    }
    else
    {
      $user[$key] = $value;
    }
  }
}

// properties of user guest are found in the configuration
if ($user['is_the_guest'])
{
  $user['template'] = $conf['default_template'];
  $user['nb_image_line'] = $conf['nb_image_line'];
  $user['nb_line_page'] = $conf['nb_line_page'];
  $user['language'] = $conf['default_language'];
  $user['maxwidth'] = $conf['default_maxwidth'];
  $user['maxheight'] = $conf['default_maxheight'];
  $user['recent_period'] = $conf['recent_period'];
  $user['expand'] = $conf['auto_expand'];
  $user['show_nb_comments'] = $conf['show_nb_comments'];
}

// if no information were found about user in user_forbidden table OR the
// forbidden categories must be updated : only if current user is in public
// part
if (!defined('IN_ADMIN') or !IN_ADMIN)
{
  if (!isset($user['need_update'])
      or !is_bool($user['need_update'])
      or $user['need_update'] == true)
  {
    $user['forbidden_categories'] = calculate_permissions($user['id'],
                                                          $user['status']);
  }
}

// forbidden_categories is a must be empty, at least
if (!isset($user['forbidden_categories']))
{
  $user['forbidden_categories'] = '';
}

// special for $user['restrictions'] array
$user['restrictions'] = explode(',', $user['forbidden_categories']);
if ($user['restrictions'][0] == '')
{
  $user['restrictions'] = array();
}

// calculation of the number of picture to display per page
$user['nb_image_page'] = $user['nb_image_line'] * $user['nb_line_page'];

if (empty($user['language'])
    or !file_exists(PHPWG_ROOT_PATH.'language/'.
                    $user['language'].'/common.lang.php'))
{
  $user['language'] = $conf['default_language'];
}
include_once(PHPWG_ROOT_PATH.'language/'.$user['language'].'/common.lang.php');

// displaying the username in the language of the connected user, instead of
// "guest" as you can find in the database
if ($user['is_the_guest'])
{
  $user['username'] = $lang['guest'];
}

// only if we are in the administration section
if (defined('IN_ADMIN') and IN_ADMIN)
{
  $langdir = PHPWG_ROOT_PATH.'language/'.$user['language'];
  if (!file_exists($langdir.'/admin.lang.php'))
  {
    $langdir = PHPWG_ROOT_PATH.'language/'.$conf['default_language'];
  }
  include_once($langdir.'/admin.lang.php');
  include_once($langdir.'/faq.lang.php');
}

if (empty($user['template']))
{
  $user['template'] = $conf['default_template'];
}
$template = setup_style($user['template']);
?>
