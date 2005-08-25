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

//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//----------------------------------------------------------- user registration
$errors = array();
if (isset($_POST['submit']))
{
  if ($_POST['password'] != $_POST['password_conf'])
  {
    array_push($errors, $lang['reg_err_pass']);
  }
  
  $errors =
    array_merge(
      $errors,
      register_user($_POST['login'],
                    $_POST['password'],
                    $_POST['mail_address'])
      );
  
  if (count($errors) == 0)
  {
    $query = '
SELECT id
  FROM '.USERS_TABLE.'
  WHERE username = \''.$_POST['login'].'\'
;';
    list($user_id) = mysql_fetch_array(pwg_query($query));
    $session_id = session_create($user_id, $conf['session_length']);
    $url = 'category.php?id='.$session_id;
    redirect($url);
  }
}

$login = !empty($_POST['login'])?$_POST['login']:'';
$email = !empty($_POST['mail_address'])?$_POST['mail_address']:'';

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['register_page_title'];
$page['body_id'] = 'theRegisterPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('register'=>'register.tpl') );
$template->assign_vars(array(
  'L_TITLE' => $lang['register_title'],
  'L_GUEST' => $lang['ident_guest_visit'],
  'L_SUBMIT' => $lang['submit'],
  'L_USERNAME' => $lang['login'],
  'L_PASSWORD' => $lang['password'],
  'L_CONFIRM_PASSWORD' => $lang['reg_confirm'],
  'L_EMAIL' => $lang['mail_address'],

  'U_HOME' => add_session_id(PHPWG_ROOT_PATH.'category.php'),
  
  'F_ACTION' => add_session_id('register.php'),
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
