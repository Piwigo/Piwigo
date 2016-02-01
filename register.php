<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_FREE);

//----------------------------------------------------------- user registration

if (!$conf['allow_user_registration'])
{
  page_forbidden('User registration closed');
}

trigger_notify('loc_begin_register');

if (isset($_POST['submit']))
{
  if (!verify_ephemeral_key(@$_POST['key']))
  {
		set_status_header(403);
    $page['errors'][] = l10n('Invalid/expired form key');
  }

  if(empty($_POST['password']))
  {
    $page['errors'][] = l10n('Password is missing. Please enter the password.');
  }
  else if(empty($_POST['password_conf']))
  {
    $page['errors'][] = l10n('Password confirmation is missing. Please confirm the chosen password.');
  }
  else if ($_POST['password'] != $_POST['password_conf'])
  {
    $page['errors'][] = l10n('The passwords do not match');
  }

  register_user(
    $_POST['login'],
    $_POST['password'],
    $_POST['mail_address'],
    true,
    $page['errors'],
    isset($_POST['send_password_by_mail'])
    );

  if (count($page['errors']) == 0)
  {
    // email notification
    if (isset($_POST['send_password_by_mail']) and email_check_format($_POST['mail_address']))
    {
      $_SESSION['page_infos'][] = l10n('Successfully registered, you will soon receive an email with your connection settings. Welcome!');
    }
    
    // log user and redirect
    $user_id = get_userid($_POST['login']);
    log_user($user_id, false);
    redirect(make_index_url());
  }
	$registration_post_key = get_ephemeral_key(2);
}
else
{
	$registration_post_key = get_ephemeral_key(6);
}

$login = !empty($_POST['login'])?htmlspecialchars(stripslashes($_POST['login'])):'';
$email = !empty($_POST['mail_address'])?htmlspecialchars(stripslashes($_POST['mail_address'])):'';

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= l10n('Registration');
$page['body_id'] = 'theRegisterPage';

$template->set_filenames( array('register'=>'register.tpl') );
$template->assign(array(
  'U_HOME' => make_index_url(),
	'F_KEY' => $registration_post_key,
  'F_ACTION' => 'register.php',
  'F_LOGIN' => $login,
  'F_EMAIL' => $email,
  'obligatory_user_mail_address' => $conf['obligatory_user_mail_address'],
  ));

// include menubar
$themeconf = $template->get_template_vars('themeconf');
if (!isset($themeconf['hide_menu_on']) OR !in_array('theRegisterPage', $themeconf['hide_menu_on']))
{
  include( PHPWG_ROOT_PATH.'include/menubar.inc.php');
}

include(PHPWG_ROOT_PATH.'include/page_header.php');
trigger_notify('loc_end_register');
flush_page_messages();
$template->parse('register');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
