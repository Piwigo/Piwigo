<?php
// +-----------------------------------------------------------------------+
// |                           configuration.php                           |
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

if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}

include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
	
$Caracs = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A", 
                "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A", 
                "Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E", 
                "Ê" => "E", "Ë" => "E", "Ì" => "I", "Í" => "I", 
                "Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N", 
                "Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O", 
                "Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U", 
                "Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s", 
                "à" => "a", "á" => "a", "â" => "a", "ã" => "a", 
                "ä" => "a", "å" => "a", "æ" => "a", "ç" => "c", 
                "è" => "e", "é" => "e", "ê" => "e", "ë" => "e", 
                "ì" => "i", "í" => "i", "î" => "i", "ï" => "i", 
                "ð" => "o", "ñ" => "n", "ò" => "o", "ó" => "o", 
                "ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o", 
                "ù" => "u", "ú" => "u", "û" => "u", "ü" => "u", 
                "ý" => "y", "ÿ" => "y");
//------------------------------ verification and registration of modifications
$error = array();
if ( isset( $_POST['submit'] ) )
{
  $int_pattern = '/^\d+$/';
  // deletion of site as asked
  $site_deleted = false;
  $query = 'SELECT id';
  $query.= ' FROM '.SITES_TABLE;
  $query.= " WHERE galleries_url <> './galleries/';";
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $site = 'delete_site_'.$row['id'];
    if ( $_POST[$site] == 1 )
    {
      delete_site( $row['id'] );
      $site_deleted = true;
    }
  }
  // if any picture of this site were linked to another categories, we have
  // to update the informations of those categories. To make it simple, we
  // just update all the categories
  if ( $site_deleted )
  {
    update_category( 'all' );
    synchronize_all_users();
  }
  // thumbnail prefix must not contain accentuated characters
  $old_prefix = $_POST['prefix_thumbnail'];
  $prefix = strtr( $_POST['prefix_thumbnail'], $Caracs );
  if ( $old_prefix != $prefix )
  {
    array_push( $error, $lang['conf_err_prefixe'] );
  }
  // mail must be formatted as follows : name@server.com
  $pattern = '/^[\w-]+(\.[\w-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$/';
  if ( !preg_match( $pattern, $_POST['mail_webmaster'] ) )
  {
    array_push( $error, $lang['conf_err_mail'] );
  }
  // periods must be integer values, they represents number of days
  if ( !preg_match( $int_pattern, $_POST['short_period'] )
       or !preg_match( $int_pattern, $_POST['long_period'] ) )
  {
    array_push( $error, $lang['err_periods'] );
  }
  else
  {
    // long period must be longer than short period
    if ( $_POST['long_period'] <= $_POST['short_period']
         or $_POST['short_period'] <= 0 )
    {
      array_push( $error, $lang['err_periods_2'] );
    }
  }
  // session_id size must be an integer between 4 and 50
  if ( !preg_match( $int_pattern, $_POST['session_id_size'] )
       or $_POST['session_id_size'] < 4
       or $_POST['session_id_size'] > 50 )
  {
    array_push( $error, $lang['conf_err_sid_size'] );
  }
  // session_time must be an integer between 5 and 60, in minutes
  if ( !preg_match( $int_pattern, $_POST['session_time'] )
       or $_POST['session_time'] < 5
       or $_POST['session_time'] > 60 )
  {
    array_push( $error, $lang['conf_err_sid_time'] );
  }
  // the number of comments per page must be an integer between 5 and 50
  // included
  if ( !preg_match( $int_pattern, $_POST['nb_comment_page'] )
       or $_POST['nb_comment_page'] < 5
       or $_POST['nb_comment_page'] > 50 )
  {
    array_push( $error, $lang['conf_err_comment_number'] );
  }
  // the maximum upload filesize must be an integer between 10 and 1000
  if ( !preg_match( $int_pattern, $_POST['upload_maxfilesize'] )
       or $_POST['upload_maxfilesize'] < 10
       or $_POST['upload_maxfilesize'] > 1000 )
  {
    array_push( $error, $lang['conf_err_upload_maxfilesize'] );
  }
  // the maximum width of uploaded pictures must be an integer superior to
  // 10
  if ( !preg_match( $int_pattern, $_POST['upload_maxwidth'] )
       or $_POST['upload_maxwidth'] < 10 )
  {
    array_push( $error, $lang['conf_err_upload_maxwidth'] );
  }
  // the maximum height  of uploaded pictures must be an integer superior to
  // 10
  if ( !preg_match( $int_pattern, $_POST['upload_maxheight'] )
       or $_POST['upload_maxheight'] < 10 )
  {
    array_push( $error, $lang['conf_err_upload_maxheight'] );
  }
  // the maximum width of uploaded thumbnails must be an integer superior to
  // 10
  if ( !preg_match( $int_pattern, $_POST['upload_maxwidth_thumbnail'] )
       or $_POST['upload_maxwidth_thumbnail'] < 10 )
  {
    array_push( $error, $lang['conf_err_upload_maxwidth_thumbnail'] );
  }
  // the maximum width of uploaded thumbnails must be an integer superior to
  // 10
  if ( !preg_match( $int_pattern, $_POST['upload_maxheight_thumbnail'] )
       or $_POST['upload_maxheight_thumbnail'] < 10 )
  {
    array_push( $error, $lang['conf_err_upload_maxheight_thumbnail'] );
  }

/*  if ( $_POST['maxwidth'] != ''
       and ( !preg_match( $int_pattern, $_POST['maxwidth'] )
             or $_POST['maxwidth'] < 50 ) )
  {
    array_push( $error, $lang['err_maxwidth'] );
  }
  if ( $_POST['maxheight']
       and ( !preg_match( $int_pattern, $_POST['maxheight'] )
             or $_POST['maxheight'] < 50 ) )
  {
    array_push( $error, $lang['err_maxheight'] );
  }*/
  // updating configuraiton if no error found
  if ( count( $error ) == 0 )
  {
    $result = mysql_query( "SELECT * FROM ".CONFIG_TABLE );
    while ( $row = mysql_fetch_array( $result ) )
	{
	  $config_name = $row['param'];
	  $conf[$config_name] = ( isset($_POST[$config_name]) ) ? $_POST[$config_name] : $row['value'];
      if ( isset( $_POST[$config_name] ) )
      {
        $query = 'UPDATE '.CONFIG_TABLE;
        $query.= " SET value = '". str_replace("\'", "''", $conf[$config_name]) ;
        $query.= "' WHERE param = '$config_name'";
        mysql_query( $query );
      }
    }
  }
}

$access = ($conf['access']=='free')?'ACCESS_FREE':'ACCESS_RESTRICTED'; 
$log = ($conf['log']=='true')?'HISTORY_YES':'HISTORY_NO'; 
$mail_notif = ($conf['mail_notification']=='true')?'MAIL_NOTIFICATION_YES':'MAIL_NOTIFICATION_NO'; 
$show_comments = ($conf['show_comments']=='true')?'SHOW_COMMENTS_YES':'SHOW_COMMENTS_NO';
$comments_all = ($conf['comments_forall']=='true')?'COMMENTS_ALL_YES':'COMMENTS_ALL_NO';
$comments_validation = ($conf['comments_validation']=='true')?'VALIDATE_COMMENTS_YES':'VALIDATE_COMMENTS_NO';
$expand = ($conf['auto_expand']=='true')?'EXPAND_TREE_YES':'EXPAND_TREE_NO';
$nb_comments = ($conf['show_nb_comments']=='true')?'NB_COMMENTS_YES':'NB_COMMENTS_NO';
$upload = ($conf['upload_available']=='true')?'UPLOAD_YES':'UPLOAD_NO';
$cookie = ($conf['authorize_cookies']=='true')?'COOKIE_YES':'COOKIE_NO';

//----------------------------------------------------- template initialization
$template->set_filenames( array('config'=>'admin/configuration.tpl') );

$template->assign_vars(array(
  'ADMIN_NAME'=>$conf['webmaster'],
  'ADMIN_MAIL'=>$conf['mail_webmaster'],
  'THUMBNAIL_PREFIX'=>$conf['prefix_thumbnail'],
  'NB_COMMENTS_PAGE'=>$conf['nb_comment_page'],
  'LANG_SELECT'=>language_select($conf['default_lang'], 'default_lang'),
  'NB_IMAGE_LINE'=>$conf['nb_image_line'],
  'NB_ROW_PAGE'=>$conf['nb_line_page'],
  'STYLE_SELECT'=>style_select($conf['default_style'], 'default_style'),
  'SHORT_PERIOD'=>$conf['short_period'],
  'LONG_PERIOD'=>$conf['long_period'],
  'UPLOAD_MAXSIZE'=>$conf['upload_maxfilesize'],
  'UPLOAD_MAXWIDTH'=>$conf['upload_maxwidth'],
  'UPLOAD_MAXHEIGHT'=>$conf['upload_maxheight'],
  'TN_UPLOAD_MAXWIDTH'=>$conf['upload_maxwidth_thumbnail'],
  'TN_UPLOAD_MAXHEIGHT'=>$conf['upload_maxheight_thumbnail'],
  'SESSION_LENGTH'=>$conf['session_time'],
  'SESSION_ID_SIZE'=>$conf['session_id_size'],
  
  $access=>'checked="checked"',
  $log=>'checked="checked"',
  $mail_notif=>'checked="checked"',
  $show_comments=>'checked="checked"',
  $comments_all=>'checked="checked"',
  $comments_validation=>'checked="checked"',
  $expand=>'checked="checked"',
  $nb_comments=>'checked="checked"',
  $upload=>'checked="checked"',
  $cookie=>'checked="checked"',
  
  'L_CONFIRM'=>$lang['conf_confirmation'],
  'L_CONF_GENERAL'=>$lang['conf_general_title'],
  'L_ADMIN_NAME'=>$lang['conf_general_webmaster'],
  'L_ADMIN_NAME_INFO'=>$lang['conf_general_webmaster_info'],
  'L_ADMIN_MAIL'=>$lang['conf_general_mail'],
  'L_ADMIN_MAIL_INFO'=>$lang['conf_general_mail_info'],
  'L_THUMBNAIL_PREFIX'=>$lang['conf_general_prefix'],
  'L_THUMBNAIL_PREFIX_INFO'=>$lang['conf_general_prefix_info'],
  'L_ACCESS'=>$lang['conf_general_access'],
  'L_ACCESS_INFO'=>$lang['conf_general_access_info'],
  'L_ACCESS_FREE'=>$lang['conf_general_access_1'],
  'L_ACCESS_RESTRICTED'=>$lang['conf_general_access_2'],
  'L_HISTORY'=>$lang['conf_general_log'],
  'L_HISTORY_INFO'=>$lang['conf_general_log_info'],
  'L_MAIL_NOTIFICATION'=>$lang['conf_general_mail_notification'],
  'L_MAIL_NOTIFICATION_INFO'=>$lang['conf_general_mail_notification_info'],
  'L_CONF_COMMENTS'=>$lang['conf_comments_title'],
  'L_SHOW_COMMENTS'=>$lang['conf_comments_show_comments'],
  'L_SHOW_COMMENTS_INFO'=>$lang['conf_comments_show_comments_info'],
  'L_COMMENTS_ALL'=>$lang['conf_comments_forall'],
  'L_COMMENTS_ALL_INFO'=>$lang['conf_comments_forall_info'],
  'L_NB_COMMENTS_PAGE'=>$lang['conf_comments_comments_number'],
  'L_NB_COMMENTS_PAGE_INFO'=>$lang['conf_comments_comments_number_info'],
  'L_VALIDATE_COMMENTS'=>$lang['conf_comments_validation'],
  'L_VALIDATE_COMMENTS_INFO'=>$lang['conf_comments_validation_info'],
  'L_ABILITIES_SETTINGS'=>$lang['conf_default_title'],
  'L_LANG_SELECT'=>$lang['customize_language'],
  'L_LANG_SELECT_INFO'=>$lang['conf_default_language_info'],
  'L_NB_IMAGE_LINE'=>$lang['customize_nb_image_per_row'],
  'L_NB_IMAGE_LINE_INFO'=>$lang['conf_default_nb_image_per_row_info'],
  'L_NB_ROW_PAGE'=>$lang['customize_nb_row_per_page'],
  'L_NB_ROW_PAGE_INFO'=>$lang['conf_default_nb_row_per_page_info'],
  'L_STYLE_SELECT'=>$lang['customize_theme'],
  'L_STYLE_SELECT_INFO'=>$lang['conf_default_theme_info'],
  'L_SHORT_PERIOD'=>$lang['customize_short_period'],
  'L_SHORT_PERIOD_INFO'=>$lang['conf_default_short_period_info'],
  'L_LONG_PERIOD'=>$lang['customize_long_period'],
  'L_LONG_PERIOD_INFO'=>$lang['conf_default_long_period_info'],
  'L_EXPAND_TREE'=>$lang['customize_expand'],
  'L_EXPAND_TREE_INFO'=>$lang['conf_default_expand_info'],
  'L_NB_COMMENTS'=>$lang['customize_show_nb_comments'],
  'L_NB_COMMENTS_INFO'=>$lang['conf_default_show_nb_comments_info'],
  'L_UPLOAD'=>$lang['conf_upload_available'],
  'L_UPLOAD_INFO'=>$lang['conf_upload_available_info'],
  'L_CONF_UPLOAD'=>$lang['conf_upload_title'],
  'L_UPLOAD_MAXSIZE'=>$lang['conf_upload_maxfilesize'],
  'L_UPLOAD_MAXSIZE_INFO'=>$lang['conf_upload_maxfilesize_info'],
  'L_UPLOAD_MAXWIDTH'=>$lang['conf_upload_maxwidth'],
  'L_UPLOAD_MAXWIDTH_INFO'=>$lang['conf_upload_maxwidth_info'],
  'L_UPLOAD_MAXHEIGHT'=>$lang['conf_upload_maxheight'],
  'L_UPLOAD_MAXHEIGHT_INFO'=>$lang['conf_upload_maxheight_info'],
  'L_TN_UPLOAD_MAXWIDTH'=>$lang['conf_upload_maxwidth_thumbnail'],
  'L_TN_UPLOAD_MAXWIDTH_INFO'=>$lang['conf_upload_maxwidth_thumbnail_info'],
  'L_TN_UPLOAD_MAXHEIGHT'=>$lang['conf_upload_maxheight_thumbnail'],
  'L_TN_UPLOAD_MAXHEIGHT_INFO'=>$lang['conf_upload_maxheight_thumbnail'],
  'L_CONF_SESSION'=>$lang['conf_session_title'],
  'L_COOKIE'=>$lang['conf_session_cookie'],
  'L_COOKIE_INFO'=>$lang['conf_session_cookie_info'],
  'L_SESSION_LENGTH'=>$lang['conf_session_time'],
  'L_SESSION_LENGTH_INFO'=>$lang['conf_session_time_info'],
  'L_SESSION_ID_SIZE'=>$lang['conf_session_size'],
  'L_SESSION_ID_SIZE_INFO'=>$lang['conf_session_size_info'],
  'L_YES'=>$lang['yes'],
  'L_NO'=>$lang['no'],
  'L_SUBMIT'=>$lang['submit'],
  
  'F_ACTION'=>add_session_id(PHPWG_ROOT_PATH.'admin.php?page=configuration')
  ));

$tpl = array( 'conf_confirmation','remote_site','delete',
              'conf_remote_site_delete_info','submit','errors_title' );

//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error[$i]));
  }
}
elseif ( isset( $_POST['submit'] ) )
{
  $template->assign_block_vars('confirmation' ,array());
}
//------------------------------------------------ remote sites administration 
$query = 'select id,galleries_url';
$query.= ' from '.SITES_TABLE;
$query.= " where galleries_url <> './galleries/';";
$result = mysql_query( $query );
if ( mysql_num_rows( $result ) > 0 )
{
  $vtp->addSession( $sub, 'remote_sites' );
  $i = 0;
  while ( $row = mysql_fetch_array( $result ) )
  {
    $vtp->addSession( $sub, 'site' );
    $vtp->setVar( $sub, 'site.url', $row['galleries_url'] );
    $vtp->setVar( $sub, 'site.id', $row['id'] );
    if ( $i == 0 )
    {
      $vtp->addSession( $sub, 'rowspan' );
      $vtp->setVar( $sub, 'rowspan.nb_sites', mysql_num_rows( $result ) );
      $vtp->closeSession( $sub, 'rowspan' );
    }
    $vtp->closeSession( $sub, 'site' );
    $i++;
  }
  $vtp->closeSession( $sub, 'remote_sites' );
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'config');
?>
