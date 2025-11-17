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

check_input_parameter('action', $_GET, false, '/^(lost|reset|lost_code|reset_end|none)$/');

// +-----------------------------------------------------------------------+
// | Functions                                                             |
// +-----------------------------------------------------------------------+

/**
 * checks the validity of input parameters, fills $page['errors'] and
 * $page['infos'] and send an email with the verification code
 *
 * @return bool
 */
function process_verification_code()
{
  global $page, $conf, $logger;
  
  if (isset($_SESSION['reset_password_code']))
  {
    return true;
  }
  
  // empty param
  $username_or_email = trim($_POST['username_or_email'] ?? '');
  if (empty($username_or_email))
  {
    $page['errors']['password_form_error'] = l10n('Invalid username or email');
    return false;
  }

  // retrievies user by email is not try by username
  $user_id = get_userid_by_email($username_or_email);

  if (!is_numeric($user_id))
  {
    $user_id = get_userid($username_or_email);
  }

  // when no user is found, we assign guest_id instead of stopping.
  // this lets the function behave identically for unknown users,
  // preventing username/email enumeration through timing or responses.
  $is_user_found = is_numeric($user_id);
  if (!$is_user_found)
  {
    $user_id = $conf['guest_id'];
  }

  $userdata = getuserdata($user_id, false);

  $status = $userdata['status'];

  if ($is_user_found)
  {
    // block early for generic or guest user because
    // we don't consider theses users has sensible for username/email enumeration
    if (is_a_guest($status) or is_generic($status))
    {
      $page['errors']['password_form_error'] = l10n('Password reset is not allowed for this user');
      return false;
    }

    // check lockout
    if (
      isset($userdata['preferences']['reset_password_forbidden_until'])
      and $userdata['preferences']['reset_password_forbidden_until'] > time()
    )
    {
      $page['errors']['password_form_error'] = l10n('Too many attempts, please try later..');
      return false;
    }
  }

  // check if we want to skip email sending
  // if user is guest, generic or doesn't have email
  $skip_mail = !$is_user_found or empty($userdata['email']);

  // send mail with verification code to user
  switch_lang_to($userdata['language']);
  $user_code = generate_user_code();
  $template_mail = pwg_generate_code_verification_mail($user_code['code']);
  if (!$skip_mail)
  {
    $mail_send = pwg_mail($userdata['email'], $template_mail);
  }
  switch_lang_back();

  $_SESSION['reset_password_code'] = [
      'secret' => $user_code['secret'],
      'attempts' => 0,
      'user_id' => $is_user_found ? $user_id : null,
      'created_at' => time(),
      'ttl' => min($conf['password_reset_code_duration'], 900) // max 15 min
    ];

  return true;
}

/**
 * checks the validity of input parameters, fills $page['errors'] and
 * $page['infos']
 *
 * @return bool (true if valid, false otherwise)
 */
function process_password_request()
{
  global $page, $user;

  $state = $_SESSION['reset_password_code'] ?? null;
  if (!$state)
  {
    return true;
  }

  // check expired
  if (time() > $state['created_at'] + $state['ttl'])
  {
    unset($_SESSION['reset_password_code']);
    $page['errors']['password_form_error'] = l10n('Code expired');
    return false;
  }

  $_SESSION['reset_password_code']['attempts']++;
    
  $is_valid = true;
  $user_code = trim($_POST['user_code'] ?? '');

  if (
    empty($user_code) // empty user code
    || !preg_match('/^\d{6}$/', $user_code) // check digit 6
    || !verify_user_code($state['secret'], $user_code)) // verify user code
  {
    $is_valid = false;
  }

  if (!$is_valid)
  {
    if ($_SESSION['reset_password_code']['attempts'] >= 3)
    {
      unset($_SESSION['reset_password_code']);
      // lockout account for 1hour
      if (!empty($state['user_id']))
      {
        $save_user = $user;
        $user = build_user($state['user_id'], false);
        userprefs_update_param('reset_password_forbidden_until', time() + 60 * 60);
        $user = $save_user;

        pwg_activity('user', $state['user_id'], 'reset_password_failure_too_many');
      }
      $page['errors']['login_page_error'] = l10n('Too many attempts, please try later..');
      return false;
    }

    if (!empty($state['user_id']))
    {
      pwg_activity('user', $state['user_id'], 'reset_password_failure_code');
    }
    $page['errors']['password_form_error'] = l10n('Invalid verification code');
    return false;
  }

  // verify code success
  $user_id = $state['user_id'];
  unset($_SESSION['reset_password_code']);

  if (empty($user_id))
  {
    $page['errors']['password_form_error'] = l10n('Invalid verification code');
    return false;
  }

  $save_user = $user;
  $user = build_user($user_id, false);
  userprefs_delete_param('reset_password_forbidden_until');

  $_SESSION['valid_reset_password_code'] = array( 
    'user_id' => $user_id,
    'username' => $user['username'],
    'email' => $user['email'],
    'language' => $user['language'],
  );
  $status = $user['status'] ?? null;
  $has_no_email = empty($user['email']);
  $page['username'] = $user['username'];
  $user = $save_user;

  // fallback check: don't send mail when user is guest, generic or doesn't have email
  if (is_a_guest($status) || is_generic($status) || $has_no_email)
  {
    $page['errors']['password_form_error'] = l10n('Password reset is not allowed for this user');
    return false;
  }

  return true;
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
    $page['errors']['password_page_error'] = l10n('Invalid key');
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
        $page['errors']['password_page_error'] = l10n('Password reset is not allowed for this user');
        return false;
      }

      $user_id = $row['user_id'];
      break;
    }
  }

  if (empty($user_id))
  {
    $page['errors']['password_page_error'] = l10n('Invalid key');
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
    $page['errors']['password_form_error'] = l10n('The passwords do not match');
    return false;
  }

  $user_id = reset_password_key() ?: reset_password_code();
  
  if (!is_numeric($user_id))
  {
    $page['errors']['password_form_error'] = l10n('Invalid key or code');
    return false;
  }
    
  single_update(
    USERS_TABLE,
    array($conf['user_fields']['password'] => $conf['password_hash']($_POST['use_new_pwd'])),
    array($conf['user_fields']['id'] => $user_id)
    );

  if (isset($_SESSION['valid_reset_password_code']) and !empty($_SESSION['valid_reset_password_code']['email']))
  {
    $reset_user = $_SESSION['valid_reset_password_code'];
    switch_lang_to($reset_user['language']);

    $api_keys = get_available_api_key($reset_user['user_id']);
    $nb_of_apikeys = $api_keys ? count($api_keys) : 0;
    $template_mail = pwg_generate_success_reset_password_mail($reset_user['username'], $nb_of_apikeys);
    pwg_mail($reset_user['email'], $template_mail);

    switch_lang_back();
  }
  unset($_SESSION['valid_reset_password_code']);

  pwg_activity('user', $user_id, 'reset_password_success');

  $page['infos'][] = l10n('Your password has been reset');
  $page['infos'][] = '<a href="'.get_root_url().'identification.php">'.l10n('Login').'</a>';

  return true;
}

function reset_password_key()
{
  if (!isset($_GET['key']))
  {
    return false;
  }
  
  $user_id = check_password_reset_key($_GET['key']);
  
  if (!is_numeric($user_id))
  {
    return false;
  }

  deactivate_password_reset_key($user_id);
  deactivate_user_auth_keys($user_id);
  return $user_id;
}

function reset_password_code()
{
  if (!isset($_SESSION['valid_reset_password_code']))
  {
    return false;
  }

  return $_SESSION['valid_reset_password_code']['user_id'] ?? false;
}

// +-----------------------------------------------------------------------+
// | Process form                                                          |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']))
{
  check_pwg_token();
  
  if ('lost' == $_GET['action'])
  {
    if (process_verification_code())
    {
      $page['infos'][] = l10n('An email has been sent with a verification code');
      $page['action'] = 'lost_code';
    }
  }

  if ('lost_code' == $_GET['action'])
  {
    if (process_password_request())
    {
      $page['infos'][] = l10n('Verification successful! You can now choose a new password.');
      $page['action'] = 'reset';
    }
  }

  if ('reset' == $_GET['action'])
  {
    if (reset_password())
    {
      $page['action'] = 'reset_end';
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
  $first_login = false;
  $user_id = check_password_reset_key($_GET['key']);
  if (is_numeric($user_id))
  {
    $userdata = getuserdata($user_id, false);
    $page['username'] = $userdata['username'];
    $template->assign('key', $_GET['key']);
    $first_login = has_already_logged_in($user_id);

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
  elseif (in_array($_GET['action'], array('lost', 'lost_code', 'reset', 'none')))
  {
    $page['action'] = $_GET['action'];
  }
}

if ('reset' == $page['action'])
{
  if ( (!isset($_GET['key']) and (is_a_guest() or is_generic())) and !isset($_SESSION['valid_reset_password_code']) )
  {
    redirect(get_gallery_home_url());
  }
}

if ('lost' == $page['action'] and !is_a_guest())
{
  redirect(get_gallery_home_url());
}

if ('lost_code' == $page['action'] and !isset($_SESSION['reset_password_code']))
{
  redirect(get_gallery_home_url(). 'identification.php');
}

if ('lost' == $page['action'] and isset($_SESSION['reset_password_code']))
{
  $page['action'] = 'lost_code';
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
else if ('reset' == $page['action'] and isset($first_login) and $first_login) 
{
  $title = l10n('Welcome');
  $template->assign('is_first_login', true);
}

$page['body_id'] = 'thePasswordPage';

$template->set_filenames( array('password'=>'password.tpl') );
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

//Load language if cookie is set from login/register/password pages
if (isset($_COOKIE['lang']) and $user['language'] != $_COOKIE['lang'])
{
  if (!array_key_exists($_COOKIE['lang'], get_languages()))
  {
    fatal_error('[Hacking attempt] the input parameter "'.$_COOKIE['lang'].'" is not valid');
  }
  
  $user['language'] = $_COOKIE['lang'];
  load_language('common.lang', '', array('language'=>$user['language']));
}

//Get list of languages
foreach (get_languages() as $language_code => $language_name)
{
  $language_options[$language_code] = $language_name;
}

$template->assign(array(
  'language_options' => $language_options,
  'current_language' => $user['language']
));

//Get link to doc
if ('fr' == substr($user['language'], 0, 2))
{
  $help_link = "https://doc-fr.piwigo.org/les-utilisateurs/se-connecter-a-piwigo";
}
else
{
  $help_link = "https://doc.piwigo.org/managing-users/log-in-to-piwigo";
}

$template->assign('HELP_LINK', $help_link);


// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_notify('loc_end_password');
flush_page_messages();
$template->pparse('password');
include(PHPWG_ROOT_PATH.'include/page_tail.php');

?>
