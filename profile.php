<?php
// +-----------------------------------------------------------------------+
// |                              profile.php                              |
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

// customize appearance of the site for a user
//----------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
//-------------------------------------------------- access authorization check
check_login_authorization();
if ( $user['is_the_guest'] )
{
  echo '<div style="text-align:center;">'.$lang['only_members'].'<br />';
  echo '<a href="./identification.php">'.$lang['ident_title'].'</a></div>';
  exit();
}
//------------------------------------------------------ update & customization
$infos = array( 'nb_image_line', 'nb_line_page', 'language',
                'maxwidth', 'maxheight', 'expand', 'show_nb_comments',
                'short_period', 'long_period', 'template', 'mail_address' );
// mise à jour dans la base de données des valeurs
// des paramètres pour l'utilisateur courant
//    - on teste si chacune des variables est passée en argument à la page
//    - ce qui signifie que l'on doit venir de la page de personnalisation
$errors = array();
if ( isset( $_POST['submit'] ) )
{
  $int_pattern = '/^\d+$/';
  if ( $_POST['maxwidth'] != ''
       and ( !preg_match( $int_pattern, $_POST['maxwidth'] )
             or $_POST['maxwidth'] < 50 ) )
  {
    array_push( $errors, $lang['err_maxwidth'] );
  }
  if ( $_POST['maxheight']
       and ( !preg_match( $int_pattern, $_POST['maxheight'] )
             or $_POST['maxheight'] < 50 ) )
  {
    array_push( $errors, $lang['err_maxheight'] );
  }
  // periods must be integer values, they represents number of days
  if ( !preg_match( $int_pattern, $_POST['short_period'] )
       or !preg_match( $int_pattern, $_POST['long_period'] ) )
  {
    array_push( $errors, $lang['err_periods'] );
  }
  else
  {
    // long period must be longer than short period
    if ( $_POST['long_period'] <= $_POST['short_period']
         or $_POST['short_period'] <= 0 )
    {
      array_push( $errors, $lang['err_periods_2'] );
    }
  }
  $mail_error = validate_mail_address( $_POST['mail_address'] );
  if ( $mail_error != '' ) array_push( $errors, $mail_error );
  // password must be the same as its confirmation
  if ( isset( $_POST['use_new_pwd'] )
       and $_POST['password'] != $_POST['passwordConf'] )
    array_push( $errors, $lang['reg_err_pass'] );
  
  if ( count( $errors ) == 0 )
  {
    $query = 'UPDATE '.USERS_TABLE;
    $query.= ' SET ';
    foreach ( $infos as $i => $info ) {
      if ( $i > 0 ) $query.= ',';
      $query.= $info;
      $query.= ' = ';
      if ( $_POST[$info] == '' ) $query.= 'NULL';
      else                       $query.= "'".$_POST[$info]."'";
    }
    $query.= ' WHERE id = '.$user['id'];
    $query.= ';';
    mysql_query( $query );

    if ( isset( $_POST['use_new_pwd'] ) )
    {
      $query = 'UPDATE '.USERS_TABLE;
      $query.= " SET password = '".md5( $_POST['password'] )."'";
      $query.= ' WHERE id = '.$user['id'];
      $query.= ';';
      mysql_query( $query );
    }
    if ( isset( $_POST['create_cookie'] ) )
    {
      setcookie( 'id',$page['session_id'],$_POST['cookie_expiration'],
                 cookie_path() );
      // update the expiration date of the session
      $query = 'UPDATE '.SESSIONS_TABLE;
      $query.= ' SET expiration = '.$_POST['cookie_expiration'];
      $query.= " WHERE id = '".$page['session_id']."'";
      $query.= ';';
      mysql_query( $query );
    }
    // redirection
    $url = 'category.php';
    if ( !isset($_POST['create_cookie']) ) $url = add_session_id( $url,true );
    header( 'Request-URI: '.$url );  
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
}
//----------------------------------------------------- template initialization
//
// Start output of page
//
$title = $lang['customize_page_title'];
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('profile'=>'profile.tpl'));
initialize_template();

$template->assign_vars(array(
  'L_TITLE' => $lang['customize_title'],
  'L_PASSWORD' => $lang['password'],
  'L_NEW' =>  $lang['new'], 
  'L_CONFIRM' =>  $lang['reg_confirm'], 
  'L_SUBMIT' =>  $lang['submit'], 
  'L_COOKIE' =>  $lang['create_cookie'],
	
  'F_ACTION' => add_session_id( './profile.php' ),

  'U_RETURN' => add_session_id('./category.php?'.$_SERVER['QUERY_STRING'])
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

$template->assign_block_vars('select',array(
  'F_LABEL'=>$lang['customize_nb_image_per_row'],
  'F_NAME'=>'nb_image_line',
  'F_OPTIONS'=>make_jumpbox($conf['nb_image_row'], $user['nb_image_line'])
  ));

$template->assign_block_vars('select',array(
  'F_LABEL'=>$lang['customize_nb_row_per_page'],
  'F_NAME'=>'nb_line_page',
  'F_OPTIONS'=>make_jumpbox($conf['nb_row_page'], $user['nb_line_page'])
  ));

$template->assign_block_vars('select',array(
  'F_LABEL'=>$lang['customize_template'],
  'F_NAME'=>'template',
  'F_OPTIONS'=>make_jumpbox(get_dirs( './template' ), $user['template'])
  ));

$template->assign_block_vars('select',array(
  'F_LABEL'=>$lang['customize_language'],
  'F_NAME'=>'language',
  'F_OPTIONS'=>make_jumpbox($lang['lang'], $user['language'], true)
  ));

$template->assign_block_vars('text',array(
  'F_LABEL'=>$lang['customize_short_period'],
  'F_NAME'=>'short_period',
  'F_VALUE'=>$user['short_period']
  ));

$template->assign_block_vars('text',array(
  'F_LABEL'=>$lang['customize_long_period'],
  'F_NAME'=>'long_period',
  'F_VALUE'=>$user['long_period']
  ));

$template->assign_block_vars('text',array(
  'F_LABEL'=>$lang['maxwidth'],
  'F_NAME'=>'maxwidth',
  'F_VALUE'=>$user['maxwidth']
  ));

$template->assign_block_vars('text',array(
  'F_LABEL'=>$lang['maxheight'],
  'F_NAME'=>'maxheight',
  'F_VALUE'=>$user['maxheight']
  ));

$template->assign_block_vars('text',array(
  'F_LABEL'=>$lang['mail_address'],
  'F_NAME'=>'mail_address',
  'F_VALUE'=>$user['mail_address']
  ));

$template->assign_block_vars('radio',array(
  'F_LABEL'=>$lang['customize_expand'],
  'F_OPTIONS'=>make_radio('expand', array(true=>$lang['yes'], false=>$lang['no']), $user['expand'], true)
  ));

$template->assign_block_vars('radio',array(
  'F_LABEL'=>$lang['customize_show_nb_comments'],
  'F_OPTIONS'=>make_radio('show_nb_comments', array(true=>$lang['yes'], false=>$lang['no']), $user['show_nb_comments'], true)
  ));

//----------------------------------------------------------- html code display
$template->pparse('profile');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
