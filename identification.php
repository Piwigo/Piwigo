<?php
// +-----------------------------------------------------------------------+
// |                          identification.php                           |
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

//-------------------------------------------------------------- identification
$errors = array();
if ( isset( $_POST['login'] ) )
{
  // retrieving the encrypted password of the login submitted
  $query = 'SELECT password';
  $query.= ' FROM '.USERS_TABLE;
  $query.= " WHERE username = '".$_POST['username']."';";
  $row = mysql_fetch_array( mysql_query( $query ) );
  if( $row['password'] == md5( $_POST['pass'] ) )
  {
    $session_id = session_create( $_POST['username'] );
    $url = 'category.php?id='.$session_id;
    header( 'Request-URI: '.$url );
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
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
$title = $lang['ident_page_title'];
include('include/page_header.php');

$template->set_filenames( array('identification'=>'identification.tpl') );
initialize_template();

$template->assign_vars(array(
  'MAIL_ADMIN' => $conf['mail_webmaster'],

  'L_TITLE' => $lang['ident_title'],
  'L_USERNAME' => $lang['login'],
  'L_PASSWORD' => $lang['password'],
  'L_LOGIN' => $lang['submit'],
  'L_GUEST' => $lang['ident_guest_visit'],
  'L_REGISTER' => $lang['ident_register'],
  'L_FORGET' => $lang['ident_forgotten_password'], 
  
  'T_STYLE' => $user['template'],
  
  'F_LOGIN_ACTION' => add_session_id('identification.php')
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

//-------------------------------------------------------------- visit as guest
if ( $conf['access'] == 'free' )
{
  $template->assign_block_vars('free_access',array());
}
//----------------------------------------------------------- html code display
$template->pparse('identification');
include('include/page_tail.php');
?>
