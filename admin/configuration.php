<?
/***************************************************************************
 *                             configuration.php                           *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

include_once( './admin/include/isadmin.inc.php' );
	
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
$conf_infos =
array( 'prefix_thumbnail','webmaster','mail_webmaster','access',
       'session_id_size','session_time','session_keyword','max_user_listbox',
       'show_comments','nb_comment_page','upload_available',
       'upload_maxfilesize', 'upload_maxwidth','upload_maxheight',
       'upload_maxwidth_thumbnail','upload_maxheight_thumbnail','log',
       'comments_validation','comments_forall','authorize_cookies',
       'mail_notification' );
$default_user_infos =
array( 'nb_image_line','nb_line_page','language','maxwidth',
       'maxheight','expand','show_nb_comments','short_period','long_period',
       'template' );
$error = array();
if ( isset( $_POST['submit'] ) )
{
  $int_pattern = '/^\d+$/';
  // empty session table if asked
  if ( $_POST['empty_session_table'] == 1 )
  {
    $query = 'DELETE FROM '.PREFIX_TABLE.'sessions';
    $query.= ' WHERE expiration < '.time().';';
    mysql_query( $query );
  }
  // deletion of site as asked
  $site_deleted = false;
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'sites';
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
  // max_user_listbox must be an integer between 0 and 255 included
  if ( !preg_match( $int_pattern, $_POST['max_user_listbox'] )
       or $_POST['max_user_listbox'] < 0
       or $_POST['max_user_listbox'] > 255 )
  {
    array_push( $error, $lang['conf_err_max_user_listbox'] );
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

  if ( $_POST['maxwidth'] != ''
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
  }
  // updating configuraiton if no error found
  if ( count( $error ) == 0 )
  {
    mysql_query( 'DELETE FROM '.PREFIX_TABLE.'config;' );
    $query = 'INSERT INTO '.PREFIX_TABLE.'config';
    $query.= ' (';
    foreach ( $conf_infos as $i => $conf_info ) {
      if ( $i > 0 ) $query.= ',';
      $query.= $conf_info;
    }
    $query.= ')';
    $query.= ' VALUES';
    $query.= ' (';
    foreach ( $conf_infos as $i => $conf_info ) {
      if ( $i > 0 ) $query.= ',';
      if ( $_POST[$conf_info] == '' ) $query.= 'NULL';
      else                            $query.= "'".$_POST[$conf_info]."'";
    }
    $query.= ')';
    $query.= ';';
    mysql_query( $query );

    $query = 'UPDATE '.PREFIX_TABLE.'users';
    $query.= ' SET';
    foreach ( $default_user_infos as $i => $default_user_info ) {
      if ( $i > 0 ) $query.= ',';
      else          $query.= ' ';
      $query.= $default_user_info;
      $query.= ' = ';
      if ( $_POST[$default_user_info] == '' )
      {
        $query.= 'NULL';
      }
      else
      {
        $query.= "'".$_POST[$default_user_info]."'";
      }
    }
    $query.= " WHERE username = 'guest'";
    $query.= ';';
    mysql_query( $query );
  }
//--------------------------------------------------------- data initialization
  foreach ( $conf_infos as $conf_info ) {
    $$conf_info = $_POST[$conf_info];
  }
  foreach ( $default_user_infos as $default_user_info ) {
    $$default_user_info = $_POST[$default_user_info];
  }
}
else
{
//--------------------------------------------------------- data initialization
  $query  = 'SELECT '.implode( ',', $conf_infos );
  $query .= ' FROM '.PREFIX_TABLE.'config;';
  $row = mysql_fetch_array( mysql_query( $query ) );
  foreach ( $conf_infos as $info ) {
    if ( isset( $row[$info] ) ) $$info = $row[$info];
    else                        $$info = '';
  }

  $query  = 'SELECT '.implode( ',', $default_user_infos );
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= " WHERE username = 'guest'";
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  foreach ( $default_user_infos as $info ) {
    if ( isset( $row[$info] ) ) $$info = $row[$info];
    else                        $$info = '';
  }
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open(
  './template/'.$user['template'].'/admin/configuration.vtp' );

$tpl = array( 'conf_confirmation','remote_site','delete',
              'conf_remote_site_delete_info','submit','errors_title' );
templatize_array( $tpl, 'lang', $sub );
//-------------------------------------------------------------- errors display
if ( sizeof( $error ) != 0 )
{
  $vtp->addSession( $sub, 'errors' );
  for ( $i = 0; $i < sizeof( $error ); $i++ )
  {
    $vtp->addSession( $sub, 'li' );
    $vtp->setVar( $sub, 'li.li', $error[$i] );
    $vtp->closeSession( $sub, 'li' );
  }
  $vtp->closeSession( $sub, 'errors' );
}
//-------------------------------------------------------- confirmation display
if ( count( $error ) == 0 and isset( $_POST['submit'] ) )
{
  $vtp->addSession( $sub, 'confirmation' );
  $vtp->closeSession( $sub, 'confirmation' );
}
//----------------------------------------------------------------- form action
$form_action = add_session_id( './admin.php?page=configuration' );
$vtp->setVar( $sub, 'form_action', $form_action );
//------------------------------------------------------- general configuration
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'title_line' );
$vtp->setVar( $sub, 'title_line.title', $lang['conf_general_title'] );
$vtp->closeSession( $sub, 'title_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
// webmaster name
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_general_webmaster'] );
$vtp->addSession( $sub, 'hidden' );
$vtp->setVar( $sub, 'hidden.text', $webmaster );
$vtp->setVar( $sub, 'hidden.name', 'webmaster' );
$vtp->setVar( $sub, 'hidden.value', $webmaster );
$vtp->closeSession( $sub, 'hidden' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_general_webmaster_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// webmaster mail address
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_general_mail'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'mail_webmaster' );
$vtp->setVar( $sub, 'text.value', $mail_webmaster );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_general_mail_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// prefix for thumbnails
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_general_prefix'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'prefix_thumbnail' );
$vtp->setVar( $sub, 'text.value', $prefix_thumbnail );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_general_prefix_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// access type
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_general_access'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'access' );
$vtp->setVar( $sub, 'radio.value', 'free' );
$vtp->setVar( $sub, 'radio.option', $lang['conf_general_access_1'] );
$checked = '';
if ( $access == 'free' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'access' );
$vtp->setVar( $sub, 'radio.value', 'restricted' );
$vtp->setVar( $sub, 'radio.option', $lang['conf_general_access_2'] );
$checked = '';
if ( $access == 'restricted' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_general_access_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// maximum user number to display in the listbox of identification page
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name',
              $lang['conf_general_max_user_listbox'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'max_user_listbox' );
$vtp->setVar( $sub, 'text.value', $max_user_listbox );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_general_max_user_listbox_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// activate log
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_general_log'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'log' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$checked = '';
if ( $log == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'log' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$checked = '';
if ( $log == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_general_log_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// mail notification for admins
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name',
              $lang['conf_general_mail_notification'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'mail_notification' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$checked = '';
if ( $mail_notification == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'mail_notification' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$checked = '';
if ( $mail_notification == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_general_mail_notification_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
//------------------------------------------------------ comments configuration
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'title_line' );
$vtp->setVar( $sub, 'title_line.title', $lang['conf_comments_title'] );
$vtp->closeSession( $sub, 'title_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
// show comments ?
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_comments_show_comments'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'show_comments' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$checked = '';
if ( $show_comments == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'show_comments' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$checked = '';
if ( $show_comments == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_comments_show_comments_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// coments for all ? true -> guests can post messages
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_comments_forall'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'comments_forall' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$checked = '';
if ( $comments_forall == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'comments_forall' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$checked = '';
if ( $comments_forall == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_comments_forall_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// number of comments per page
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name',
              $lang['conf_comments_comments_number'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'nb_comment_page' );
$vtp->setVar( $sub, 'text.value', $nb_comment_page );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_comments_comments_number_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// coments validation
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_comments_validation'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'comments_validation' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$checked = '';
if ( $comments_validation == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'comments_validation' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$checked = '';
if ( $comments_validation == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_comments_validation_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
//-------------------------------------------------- default user configuration
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'title_line' );
$vtp->setVar( $sub, 'title_line.title', $lang['conf_default_title'] );
$vtp->closeSession( $sub, 'title_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
// default language
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_language'] );
$vtp->addSession( $sub, 'select' );
$vtp->setVar( $sub, 'select.name', 'language' );
$option = get_languages( './language/' );
for ( $i = 0; $i < sizeof( $option ); $i++ )
{
  $vtp->addSession( $sub, 'option' );
  $vtp->setVar( $sub, 'option.option', $option[$i] );
  if ( $option[$i] == $language )
  {
    $vtp->setVar( $sub, 'option.selected', ' selected="selected"' );
  }
  $vtp->closeSession( $sub, 'option' );
}
$vtp->closeSession( $sub, 'select' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_language_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// number of image per row
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_nb_image_per_row'] );
$vtp->addSession( $sub, 'select' );
$vtp->setVar( $sub, 'select.name', 'nb_image_line' );
for ( $i = 0; $i < sizeof( $conf['nb_image_row'] ); $i++ )
{
  $vtp->addSession( $sub, 'option' );
  $vtp->setVar( $sub, 'option.option', $conf['nb_image_row'][$i] );
  if ( $conf['nb_image_row'][$i] == $nb_image_line )
  {
    $vtp->setVar( $sub, 'option.selected', ' selected="selected"' );
  }
  $vtp->closeSession( $sub, 'option' );
}
$vtp->closeSession( $sub, 'select' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_default_nb_image_per_row_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// number of row per page
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_nb_row_per_page'] );
$vtp->addSession( $sub, 'select' );
$vtp->setVar( $sub, 'select.name', 'nb_line_page' );
for ( $i = 0; $i < sizeof( $conf['nb_row_page'] ); $i++ )
{
  $vtp->addSession( $sub, 'option' );
  $vtp->setVar( $sub, 'option.option', $conf['nb_row_page'][$i] );
  if ( $conf['nb_row_page'][$i] == $nb_line_page )
  {
    $vtp->setVar( $sub, 'option.selected', ' selected="selected"' );
  }
  $vtp->closeSession( $sub, 'option' );
}
$vtp->closeSession( $sub, 'select' );
$vtp->setVar( $sub, 'param_line.def',
              $lang['conf_default_nb_row_per_page_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// template
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_theme'] );
$vtp->addSession( $sub, 'select' );
$vtp->setVar( $sub, 'select.name', 'template' );
$option = get_dirs( './template/' );

for ( $i = 0; $i < sizeof( $option ); $i++ )
{
  $vtp->addSession( $sub, 'option' );
  $vtp->setVar( $sub, 'option.option', $option[$i] );
  if ( $option[$i] == $template )
  {
    $vtp->setVar( $sub, 'option.selected', ' selected="selected"' );
  }
  $vtp->closeSession( $sub, 'option' );
}
$vtp->closeSession( $sub, 'select' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_theme_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// short period time
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_short_period'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'short_period' );
$vtp->setVar( $sub, 'text.value', $short_period );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_short_period_info']);
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// long period time
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_long_period'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'long_period' );
$vtp->setVar( $sub, 'text.value', $long_period );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_long_period_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// max displayed width
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['maxwidth'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'maxwidth' );
$vtp->setVar( $sub, 'text.value', $maxwidth );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_maxwidth_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// max displayed height
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['maxheight'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'maxheight' );
$vtp->setVar( $sub, 'text.value', $maxheight );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_maxheight_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// expand all categories ?
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_expand'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'expand' );

$vtp->setVar( $sub, 'radio.value', 'true' );
$checked = '';
if ( $expand == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'expand' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$checked = '';
if ( $expand == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_expand_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// show number of comments on thumbnails page
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['customize_show_nb_comments'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'show_nb_comments' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$checked = '';
if ( $show_nb_comments == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'show_nb_comments' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$checked = '';
if ( $show_nb_comments == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_default_show_nb_comments_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
//-------------------------------------------------------- upload configuration
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'title_line' );
$vtp->setVar( $sub, 'title_line.title', $lang['conf_upload_title'] );
$vtp->closeSession( $sub, 'title_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
// is upload available ?
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_upload_available'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'upload_available' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$checked = '';
if ( $upload_available == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'upload_available' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$checked = '';
if ( $upload_available == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_upload_available_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// max filesize uploadable
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_upload_maxfilesize'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'upload_maxfilesize' );
$vtp->setVar( $sub, 'text.value', $upload_maxfilesize );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_upload_maxfilesize_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// maxwidth uploadable
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_upload_maxwidth'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'upload_maxwidth' );
$vtp->setVar( $sub, 'text.value', $upload_maxwidth );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_upload_maxwidth_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// maxheight uploadable
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_upload_maxheight'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'upload_maxheight' );
$vtp->setVar( $sub, 'text.value', $upload_maxheight );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_upload_maxheight_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// maxwidth for thumbnail
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name',$lang['conf_upload_maxwidth_thumbnail']);
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'upload_maxwidth_thumbnail' );
$vtp->setVar( $sub, 'text.value', $upload_maxwidth_thumbnail );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar($sub,'param_line.def',$lang['conf_upload_maxwidth_thumbnail_info']);
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// maxheight for thumbnail
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub,'param_line.name',$lang['conf_upload_maxheight_thumbnail']);
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'upload_maxheight_thumbnail' );
$vtp->setVar( $sub, 'text.value', $upload_maxheight_thumbnail );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_upload_maxheight_thumbnail_info']);
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
//------------------------------------------------------ sessions configuration
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'title_line' );
$vtp->setVar( $sub, 'title_line.title', $lang['conf_session_title'] );
$vtp->closeSession( $sub, 'title_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
// authorize cookies ?
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_session_cookie'] );
$vtp->addSession( $sub, 'group' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'authorize_cookies' );
$vtp->setVar( $sub, 'radio.value', 'true' );
$checked = '';
if ( $authorize_cookies == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['yes'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->addSession( $sub, 'radio' );
$vtp->setVar( $sub, 'radio.name', 'authorize_cookies' );
$vtp->setVar( $sub, 'radio.value', 'false' );
$checked = '';
if ( $authorize_cookies == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'radio.checked', $checked );
$vtp->setVar( $sub, 'radio.option', $lang['no'] );
$vtp->closeSession( $sub, 'radio' );
$vtp->closeSession( $sub, 'group' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_session_cookie_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// session size
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_session_size'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'session_id_size' );
$vtp->setVar( $sub, 'text.value', $session_id_size );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_session_size_info']);
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// session length
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_session_time'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'session_time' );
$vtp->setVar( $sub, 'text.value', $session_time );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_session_time_info']);
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// session keyword
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_session_key'] );
$vtp->addSession( $sub, 'text' );
$vtp->setVar( $sub, 'text.name', 'session_keyword' );
$vtp->setVar( $sub, 'text.value', $session_keyword );
$vtp->closeSession( $sub, 'text' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_session_key_info']);
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );
// session deletion
$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'param_line' );
$vtp->setVar( $sub, 'param_line.name', $lang['conf_session_delete'] );
$vtp->addSession( $sub, 'check' );
$vtp->addSession( $sub, 'box' );
$vtp->setVar( $sub, 'box.name', 'empty_session_table' );
$vtp->setVar( $sub, 'box.value', '1' );
$vtp->setVar( $sub, 'box.checked', ' checked="checked"' );
$vtp->closeSession( $sub, 'box' );
$vtp->closeSession( $sub, 'check' );
$vtp->setVar( $sub, 'param_line.def', $lang['conf_session_delete_info'] );
$vtp->closeSession( $sub, 'param_line' );
$vtp->closeSession( $sub, 'line' );

$vtp->addSession( $sub, 'line' );
$vtp->addSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'space_line' );
$vtp->closeSession( $sub, 'line' );
//------------------------------------------------ remote sites administration 
$query = 'select id,galleries_url';
$query.= ' from '.PREFIX_TABLE.'sites';
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
$vtp->Parse( $handle , 'sub', $sub );
?>