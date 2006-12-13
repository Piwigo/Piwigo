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

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

//-------------------------------------------------------------- identification
$errors = array();

$redirect_to = '';
if ( !empty($_GET['redirect']) )
{
  $redirect_to = urldecode($_GET['redirect']);
  if ( $user['is_the_guest'] )
  {
    array_push($errors, $lang['access_forbiden']);
  }
}

if (isset($_POST['login']))
{
  $redirect_to = isset($_POST['redirect']) ? $_POST['redirect'] : '';
  $username = mysql_escape_string($_POST['username']);
  // retrieving the encrypted password of the login submitted
  $query = '
SELECT '.$conf['user_fields']['id'].' AS id,
       '.$conf['user_fields']['password'].' AS password
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username'].' = \''.$username.'\'
;';
  $row = mysql_fetch_array(pwg_query($query));
  if ($row['password'] == $conf['pass_convert']($_POST['password']))
  {
    $remember_me = false;
    if (isset($_POST['remember_me'])
        and $_POST['remember_me'] == 1)
    {
      $remember_me = true;
    }
    log_user($row['id'], $remember_me);
    pwg_log_login( $username );
    redirect(empty($redirect_to) ? make_index_url() : $redirect_to);
  }
  else
  {
    array_push( $errors, $lang['invalid_pwd'] );
  }
}

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title = $lang['identification'];
$page['body_id'] = 'theIdentificationPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames( array('identification'=>'identification.tpl') );

$template->assign_vars(
  array(
    'U_REGISTER' => PHPWG_ROOT_PATH.'register.php',
    'U_LOST_PASSWORD' => PHPWG_ROOT_PATH.'password.php',
    'U_HOME' => make_index_url(),
    'U_REDIRECT' => $redirect_to,

    'F_LOGIN_ACTION' => PHPWG_ROOT_PATH.'identification.php'
    ));

if ($conf['authorize_remembering'])
{
  $template->assign_block_vars('remember_me',array());
}
if ($conf['allow_user_registration'])
{
  $template->assign_block_vars('register',array());
}

//-------------------------------------------------------------- errors display
if ( sizeof( $errors ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $errors ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}
//-------------------------------------------------------------- visit as guest
$template->assign_block_vars('free_access',array());
//----------------------------------------------------------- html code display
$template->parse('identification');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
