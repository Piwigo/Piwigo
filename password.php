<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_FREE);

trigger_action('loc_begin_password');

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+

/**
 * checks the validity of input parameters, fills $page['errors'] and
 * $page['infos'] and send an email with confirmation link
 *
 * @return bool (true if email was sent, false otherwise)
 */
function process_password_request()
{
  global $page, $conf;
  
  if (empty($_POST['username_or_email']))
  {
    array_push($page['errors'], l10n('Invalid username or email'));
    return false;
  }
  
  $user_id = get_userid_by_email($_POST['username_or_email']);
    
  if (!is_numeric($user_id))
  {
    $user_id = get_userid($_POST['username_or_email']);
  }

  if (!is_numeric($user_id))
  {
    array_push($page['errors'], l10n('Invalid username or email'));
    return false;
  }

  $userdata = getuserdata($user_id, false);

  // password request is not possible for guest/generic users
  $status = $userdata['status'];
  if (is_a_guest($status) or is_generic($status))
  {
    array_push($page['errors'], l10n('Password reset is not allowed for this user'));
    return false;
  }

  if (empty($userdata['email']))
  {
    array_push(
      $page['errors'],
      sprintf(
        l10n('User "%s" has no email address, password reset is not possible'),
        $userdata['username']
        )
      );
    return false;
  }

  if (empty($userdata['activation_key']))
  {
    $activation_key = get_user_activation_key();

    single_update(
      USER_INFOS_TABLE,
      array('activation_key' => $activation_key),
      array('user_id' => $user_id)
      );

    $userdata['activation_key'] = $activation_key;
  }

  set_make_full_url();
  
  $message = l10n('Someone requested that the password be reset for the following user account:') . "\r\n\r\n";
  $message.= sprintf(
    l10n('Username "%s" on gallery %s'),
    $userdata['username'],
    get_gallery_home_url()
    );
  $message.= "\r\n\r\n";
  $message.= l10n('To reset your password, visit the following address:') . "\r\n";
  $message.= get_gallery_home_url().'/password.php?key='.$userdata['activation_key']."\r\n\r\n";
  $message.= l10n('If this was a mistake, just ignore this email and nothing will happen.')."\r\n";

  unset_make_full_url();

  $message = trigger_event('render_lost_password_mail_content', $message);

  $email_params = array(
    'subject' => '['.$conf['gallery_title'].'] '.l10n('Password Reset'),
    'content' => $message,
    'email_format' => 'text/plain',
    );

  if (pwg_mail($userdata['email'], $email_params))
  {
    array_push($page['infos'], l10n('Check your email for the confirmation link'));
    return true;
  }
  else
  {
    array_push($page['errors'], l10n('Error sending email'));
    return false;
  }
}

/**
 *  checks the activation key: does it match the expected pattern? is it
 *  linked to a user? is this user allowed to reset his password?
 *
 * @return mixed (user_id if OK, false otherwise)
 */
function check_password_reset_key($key)
{
  global $page;
  
  if (!preg_match('/^[a-z0-9]{20}$/i', $key))
  {
    array_push($page['errors'], l10n('Invalid key'));
    return false;
  }

  $query = '
SELECT
    user_id,
    status
  FROM '.USER_INFOS_TABLE.'
  WHERE activation_key = \''.$key.'\'
;';
  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    array_push($page['errors'], l10n('Invalid key'));
    return false;
  }
  
  $userdata = pwg_db_fetch_assoc($result);

  if (is_a_guest($userdata['status']) or is_generic($userdata['status']))
  {
    array_push($page['errors'], l10n('Password reset is not allowed for this user'));
    return false;
  }

  return $userdata['user_id'];
}

/**
 * checks the passwords, checks that user is allowed to reset his password,
 * update password, fills $page['errors'] and $page['infos'].
 *
 * @return bool (true if password was reset, false otherwise)
 */
function reset_password()
{
  global $page, $user, $conf;

  if ($_POST['use_new_pwd'] != $_POST['passwordConf'])
  {
    array_push($page['errors'], l10n('The passwords do not match'));
    return false;
  }

  if (isset($_GET['key']))
  {
    $user_id = check_password_reset_key($_GET['key']);
    if (!is_numeric($user_id))
    {
      array_push($page['errors'], l10n('Invalid key'));
      return false;
    }
  }
  else
  {
    // we check the currently logged in user
    if (is_a_guest() or is_generic())
    {
      array_push($page['errors'], l10n('Password reset is not allowed for this user'));
      return false;
    }

    $user_id = $user['id'];
  }
    
  single_update(
    USERS_TABLE,
    array($conf['user_fields']['password'] => $conf['password_hash']($_POST['use_new_pwd'])),
    array($conf['user_fields']['id'] => $user_id)
    );

  array_push($page['infos'], l10n('Your password has been reset'));

  if (isset($_GET['key']))
  {
    array_push($page['infos'], '<a href="'.get_root_url().'identification.php">'.l10n('Login').'</a>');
  }
  else
  {
    array_push($page['infos'], '<a href="'.get_gallery_home_url().'">'.l10n('Return to home page').'</a>');
  }

  return true;
}

// +-----------------------------------------------------------------------+
// | Process form                                                          |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']))
{
  check_pwg_token();
  
  if ('lost' == $_GET['action'])
  {
    if (process_password_request())
    {
      $page['action'] = 'none';
    }
  }

  if ('reset' == $_GET['action'])
  {
    if (reset_password())
    {
      $page['action'] = 'none';
    }
  }
}

// +-----------------------------------------------------------------------+
// | key and action                                                        |
// +-----------------------------------------------------------------------+

// a connected user can't reset the password from a mail
if (isset($_GET['key']) and !is_a_guest())
{
  unset($_GET['key']);
}

if (isset($_GET['key']))
{
  $user_id = check_password_reset_key($_GET['key']);
  if (is_numeric($user_id))
  {
    $userdata = getuserdata($user_id, false);
    $page['username'] = $userdata['username'];
    $template->assign('key', $_GET['key']);

    if (!isset($page['action']))
    {
      $page['action'] = 'reset';
    }
  }
  else
  {
    $page['action'] = 'none';
  }
}

if (!isset($page['action']))
{
  if (!isset($_GET['action']))
  {
    $page['action'] = 'lost';
  }
  elseif (in_array($_GET['action'], array('lost', 'reset', 'none')))
  {
    $page['action'] = $_GET['action'];
  }
}

if ('reset' == $page['action'] and !isset($_GET['key']) and (is_a_guest() or is_generic()))
{
  redirect(get_gallery_home_url());
}

if ('lost' == $page['action'] and !is_a_guest())
{
  redirect(get_gallery_home_url());
}

// +-----------------------------------------------------------------------+
// | template initialization                                               |
// +-----------------------------------------------------------------------+

$title = l10n('Password Reset');
if ('lost' == $page['action'])
{
  $title = l10n('Forgot your password?');

  if (isset($_POST['username_or_email']))
  {
    $template->assign('username_or_email', htmlspecialchars(stripslashes($_POST['username_or_email'])));
  }
}

$page['body_id'] = 'thePasswordPage';

$template->set_filenames(array('password'=>'password.tpl'));
$template->assign(
  array(
    'title' => $title,
    'form_action'=> get_root_url().'password.php',
    'action' => $page['action'],
    'username' => isset($page['username']) ? $page['username'] : $user['username'],
    'PWG_TOKEN' => get_pwg_token(),
    )
  );


// include menubar
$themeconf = $template->get_template_vars('themeconf');
if (!isset($themeconf['hide_menu_on']) OR !in_array('thePasswordPage', $themeconf['hide_menu_on']))
{
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
}

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_action('loc_end_password');
flush_page_messages();
$template->pparse('password');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>
