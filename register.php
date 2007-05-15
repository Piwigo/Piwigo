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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_NONE);

//----------------------------------------------------------- user registration

if (!$conf['allow_user_registration'])
{
  page_forbidden('User registration closed');
}

$errors = array();
if (isset($_POST['submit']))
{
  if ($_POST['password'] != $_POST['password_conf'])
  {
    array_push($errors, l10n('reg_err_pass'));
  }

  $errors =
      register_user($_POST['login'],
                    $_POST['password'],
                    $_POST['mail_address'],
                    $errors);

  if (count($errors) == 0)
  {
    $user_id = get_userid($_POST['login']);
    log_user( $user_id, false);

    if ($conf['email_admin_on_new_user'])
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
      $username = $_POST['login'];
      $admin_url = get_absolute_root_url()
                   .'admin.php?page=user_list&username='.$username;

      $keyargs_content = array
      (
        get_l10n_args('User: %s', $username),
        get_l10n_args('Email: %s', $_POST['mail_address']),
        get_l10n_args('', ''),
        get_l10n_args('Admin: %s', $admin_url)
      );

      pwg_mail_notification_admins
      (
        get_l10n_args('Registration of %s', $username),
        $keyargs_content
      );
    }
    redirect(make_index_url());
  }
}

$login = !empty($_POST['login'])?$_POST['login']:'';
$email = !empty($_POST['mail_address'])?$_POST['mail_address']:'';

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= l10n('register_page_title');
$page['body_id'] = 'theRegisterPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('register'=>'register.tpl') );
$template->assign_vars(array(
  'U_HOME' => make_index_url(),

  'F_ACTION' => 'register.php',
  'F_LOGIN' => $login,
  'F_EMAIL' => $email
  ));

//-------------------------------------------------------------- errors display
if ( sizeof( $errors ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $errors ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}

$template->parse('register');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
