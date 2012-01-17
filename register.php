<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

if (isset($_POST['submit']))
{
  if (!verify_ephemeral_key(@$_POST['key']))
  {
		set_status_header(403);
    array_push($page['errors'], 'Invalid/expired form key');
  }

  if ($_POST['password'] != $_POST['password_conf'])
  {
    array_push($page['errors'], l10n('please enter your password again'));
  }

  $page['errors'] =
      register_user($_POST['login'],
                    $_POST['password'],
                    $_POST['mail_address'],
                    true,
                    $page['errors']);

  if (count($page['errors']) == 0)
  {
    // email notification
    if (isset($_POST['send_password_by_mail']) and isset($_POST['mail_address']))
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
            
      $keyargs_content = array(
        get_l10n_args('Hello %s,', $_POST['login']),
        get_l10n_args('Thank you for registering at %s!', $conf['gallery_title']),
        get_l10n_args('', ''),
        get_l10n_args('Here are your connection settings', ''),
        get_l10n_args('Username: %s', $_POST['login']),
        get_l10n_args('Password: %s', $_POST['password']),
        get_l10n_args('Email: %s', $_POST['mail_address']),
        get_l10n_args('', ''),
        get_l10n_args('If you think you\'ve received this email in error, please contact us at %s', get_webmaster_mail_address()),
        );
        
      pwg_mail(
        $_POST['mail_address'],
        array(
          'subject' => '['.$conf['gallery_title'].'] '.l10n('Registration'),
          'content' => l10n_args($keyargs_content),
          'content_format' => 'text/plain',
          )
        );
        
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
$template->parse('register');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
