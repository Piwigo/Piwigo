<?php
// +-----------------------------------------------------------------------+
// |                             register.php                              |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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
//-------------------------------------------------- access authorization check
if ( $conf['access'] == "restricted" )
{
  echo $lang['only_members'];
  exit();
}
//----------------------------------------------------------- user registration
$error = array();
if ( isset( $_POST['submit'] ) )
{
  $error = register_user( $_POST['login'], $_POST['password'],
                          $_POST['password_conf'], $_POST['mail_address'] );
  if ( sizeof( $error ) == 0 )
  {
    $session_id = session_create( $_POST['login'] );
    $url = 'category.php?id='.$session_id;
    header( 'Request-URI: '.$url );
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
}

$login = empty($_POST['login'])?$_POST['login']:'';
$email = empty($_POST['login'])?$_POST['login']:'';

//----------------------------------------------------- template initialization
//
// Start output of page
//
$title= $lang['register_page_title'];
include('include/page_header.php');

$template->set_filenames( array('register'=>'register.tpl') );
initialize_template();

$template->assign_vars(array(
  'L_TITLE' => $lang['register_title'],
  'L_GUEST' => $lang['ident_guest_visit'],
  'L_SUBMIT' => $lang['submit'],
  'L_USERNAME' => $lang['login'],
  'L_PASSWORD' => $lang['password'],
  'L_CONFIRM_PASSWORD' => $lang['reg_confirm'],
  'L_EMAIL' => $lang['mail_address'],
  
  'F_ACTION' => add_session_id('register.php'),
  'F_LOGIN' => $login,
  'F_MAIL' => $email
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

$template->pparse('register');
include('include/page_tail.php');
?>
