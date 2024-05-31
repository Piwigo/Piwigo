<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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

trigger_notify('loc_begin_password');

check_input_parameter('action', $_GET, false, '/^(lost|reset|none)$/');

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
    $page['errors'][] = l10n('Invalid username or email');
    return false;
  }
  
  $user_id = get_userid_by_email($_POST['username_or_email']);
    
  if (!is_numeric($user_id))
  {
    $user_id = get_userid($_POST['username_or_email']);
  }

  if (!is_numeric($user_id))
  {
    $page['errors'][] = l10n('Invalid username or email');
    return false;
  }

  $userdata = getuserdata($user_id, false);

  // password request is not possible for guest/generic users
  $status = $userdata['status'];
  if (is_a_guest($status) or is_generic($status))
  {
    $page['errors'][] = l10n('Password reset is not allowed for this user');
    return false;
  }

  if (empty($userdata['email']))
  {
    $page['errors'][] = l10n(
      'User "%s" has no email address, password reset is not possible',
      $userdata['username']
      );
    return false;
  }

  $generate_link = generate_reset_password_link($user_id);
  
  $userdata['activation_key'] = $generate_link['activation_key'];

  $email_params = pwg_generate_reset_password_mail($userdata['username'], $generate_link['reset_password_link'], $conf['gallery_title']);

  if (pwg_mail($userdata['email'], $email_params))
  {
    $page['infos'][] = l10n('Check your email for the confirmation link');
    return true;
  }
  else
  {
    $page['errors'][] = l10n('Error sending email');
    return false;
  }
}

/**
 *  checks the activation key: does it match the expected pattern? is it
 *  linked to a user? is this user allowed to reset his password?
 *
 * @return mixed (user_id if OK, false otherwise)
 */
function check_password_reset_key($reset_key)
{
  global $page, $conf;

  $key = $reset_key;
  if (!preg_match('/^[a-z0-9]{20}$/i', $key))
  {
    $page['errors'][] = l10n('Invalid key');
    return false;
  }

  $query = '
SELECT
    user_id,
    status,
    activation_key
  FROM '.USER_INFOS_TABLE.'
  WHERE activation_key IS NOT NULL
    AND activation_key_expire > NOW()
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (pwg_password_verify($key, $row['activation_key']))
    {
      if (is_a_guest($row['status']) or is_generic($row['status']))
      {
        $page['errors'][] = l10n('Password reset is not allowed for this user');
        return false;
      }

      $user_id = $row['user_id'];
      break;
    }
  }

  if (empty($user_id))
  {
    $page['errors'][] = l10n('Invalid key');
    return false;
  }
  
  return $user_id;
}

/**
 * checks the passwords, checks that user is allowed to reset his password,
 * update password, fills $page['errors'] and $page['infos'].
 *
 * @return bool (true if password was reset, false otherwise)
 */
function reset_password()
{
  global $page, $conf;

  if ($_POST['use_new_pwd'] != $_POST['passwordConf'])
  {
    $page['errors'][] = l10n('The passwords do not match');
    return false;
  }

  if (!isset($_GET['key']))
  {
    $page['errors'][] = l10n('Invalid key');
  }
  
  $user_id = check_password_reset_key($_GET['key']);
  
  if (!is_numeric($user_id))
  {
    return false;
  }
    
  single_update(
    USERS_TABLE,
    array($conf['user_fields']['password'] => $conf['password_hash']($_POST['use_new_pwd'])),
    array($conf['user_fields']['id'] => $user_id)
    );

  deactivate_password_reset_key($user_id);
  deactivate_user_auth_keys($user_id);

  $page['infos'][] = l10n('Your password has been reset');
  $page['infos'][] = '<a href="'.get_root_url().'identification.php">'.l10n('Login').'</a>';

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

if (isset($_GET['key']) and !isset($_POST['submit']))
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
trigger_notify('loc_end_password');
flush_page_messages();
$template->pparse('password');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>
