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
                'recent_period', 'template', 'mail_address' );
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
    array_push( $errors, $lang['maxwidth_error'] );
  }
  if ( $_POST['maxheight']
       and ( !preg_match( $int_pattern, $_POST['maxheight'] )
             or $_POST['maxheight'] < 50 ) )
  {
    array_push( $errors, $lang['maxheight_error'] );
  }
  // periods must be integer values, they represents number of days
  if (!preg_match($int_pattern, $_POST['recent_period'])
      or $_POST['recent_period'] <= 0)
  {
    array_push( $errors, $lang['periods_error'] );
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
    redirect( $url );
  }
}
//----------------------------------------------------- template initialization
$expand = ($user['expand']=='true')?'EXPAND_TREE_YES':'EXPAND_TREE_NO';
$nb_comments = ($user['show_nb_comments']=='true')?'NB_COMMENTS_YES':'NB_COMMENTS_NO';

$title = $lang['customize_page_title'];
include(PHPWG_ROOT_PATH.'include/page_header.php');

$template->set_filenames(array('profile'=>'profile.tpl'));

$template->assign_vars(array(
  'LANG_SELECT'=>language_select($user['language'], 'language'),
  'NB_IMAGE_LINE'=>$user['nb_image_line'],
  'NB_ROW_PAGE'=>$user['nb_line_page'],
  'STYLE_SELECT'=>style_select($user['template'], 'template'),
  'RECENT_PERIOD'=>$user['recent_period'],
  
  $expand=>'checked="checked"',
  $nb_comments=>'checked="checked"',
  
  'L_TITLE' => $lang['customize_title'],
  'L_PASSWORD' => $lang['password'],
  'L_NEW' =>  $lang['new'],
  'L_CONFIRM' =>  $lang['reg_confirm'],
  'L_COOKIE' =>  $lang['create_cookie'],
  'L_LANG_SELECT'=>$lang['language'],
  'L_NB_IMAGE_LINE'=>$lang['nb_image_per_row'],
  'L_NB_ROW_PAGE'=>$lang['nb_row_per_page'],
  'L_STYLE_SELECT'=>$lang['theme'],
  'L_RECENT_PERIOD'=>$lang['recent_period'],
  'L_EXPAND_TREE'=>$lang['auto_expand'],
  'L_NB_COMMENTS'=>$lang['show_nb_comments'],
  'L_YES'=>$lang['yes'],
  'L_NO'=>$lang['no'],
  'L_SUBMIT'=>$lang['submit'],
  
  'F_ACTION'=>add_session_id(PHPWG_ROOT_PATH.'profile.php'),
  
  'U_RETURN' => add_session_id(PHPWG_ROOT_PATH.'category.php?'.$_SERVER['QUERY_STRING'])
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

//----------------------------------------------------------- html code display
$template->pparse('profile');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
