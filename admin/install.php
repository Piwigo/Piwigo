<?php
/***************************************************************************
 *                                install.php                              *
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

//-------------------------------------------------------------------- includes
define( PREFIX_INCLUDE, '.' );
include( '../include/vtemplate.class.php' );
include( '../include/functions.inc.php' );
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( '../template/default/admin/install.vtp' );
$vtp->setGlobalVar( $handle, 'release', '1.3' );
//-------------------------------------------------------------------- language
if ( isset( $_GET['language'] ) )
{
  $isadmin = true;
  $lang = array();
  include( '../language/'.$_GET['language'].'.php' );
  $tpl = array( 'step1_err_copy', 'step1_err_copy_2', 'step1_err_copy_next',
                'errors_title', 'step1_title','step1_host','step1_host_info',
                'step1_user','step1_user_info','step1_pass','step1_pass_info',
                'step1_database','step1_database_info','step1_prefix',
                'step1_prefix_info','submit','infos_title','step2_title',
                'conf_general_webmaster','conf_general_webmaster_info',
                'step2_pwd','step2_pwd_info','step2_pwd_conf',
                'step2_pwd_conf_info','conf_general_mail',
                'conf_general_mail_info','install_end_title',
                'install_end_message','install_help');
  templatize_array( $tpl, 'lang', $handle );
  $vtp->setGlobalVar( $handle, 'language', $_GET['language'] );
}
//---------------------- Step 1 : connection informations, write of config file
if ( $_GET['step'] == 1 )
{
  $errors = array();
  $infos  = array();
  // creation of ./include/mysql.inc.php : file containing database
  // connection informations
  if ( isset( $_POST['cfgBase'] )
       and isset( $_POST['cfgUser'] )
       and isset( $_POST['cfgPassword'] )
       and isset( $_POST['cfgHote'] ) )
  {
    if ( @mysql_connect( $_POST['cfgHote'],
                         $_POST['cfgUser'],
                         $_POST['cfgPassword'] ) )
    {
      if ( @mysql_select_db($_POST['cfgBase'] ) )
      {
        array_push( $infos, $lang['step1_confirmation'] );
      }
      else
      {
        array_push( $errors, $lang['step1_err_db'] );
      }
    }
    else
    {
      array_push( $errors, $lang['step1_err_server'] );
    }
			
    if ( count( $errors ) == 0 )
    {
      $file_content = "<?php";
      $file_content.= "\n\$cfgBase = '".     $_POST['cfgBase']."';";
      $file_content.= "\n\$cfgUser = '".     $_POST['cfgUser']."';";
      $file_content.= "\n\$cfgPassword = '". $_POST['cfgPassword']."';";
      $file_content.= "\n\$cfgHote = '".     $_POST['cfgHote']."';";
      $file_content.= "\n\$prefixeTable = '".$_POST['prefixeTable']."';";
      $file_content.= "\n?>";
      // writting the configuration file
      if ( $fp = @fopen( '../include/mysql.inc.php', 'a+' ) )
      {
        fwrite( $fp, $file_content ); 
        fclose( $fp );
      }
      $cfgHote     = '';
      $cfgUser     = '';
      $cfgPassword = '';
      $cfgBase     = '';
      include( '../include/mysql.inc.php' );
      $file_OK = false;
      if ( @mysql_connect( $cfgHote, $cfgUser, $cfgPassword ) )
      {
        if ( @mysql_select_db( $cfgBase ) ) $file_OK = true;
      }
      if ( !$file_OK )
      {
        $vtp->addSession( $handle, 'error_copy' );
        $html_content = htmlentities( $file_content, ENT_QUOTES );
        $html_content = nl2br( $html_content );
        $vtp->setVar( $handle, 'error_copy.file_content', $html_content );
        $vtp->closeSession( $handle, 'error_copy' );
      }
      else
      {
        $url = 'install.php?step=2&language='.$_GET['language'];
        header( 'Request-URI: '.$url );  
        header( 'Content-Location: '.$url);  
        header( 'Location: '.$url );
        exit();
      }
    }
  }
  // errors display
  if ( sizeof( $errors ) != 0 )
  {
    $vtp->addSession( $handle, 'errors' );
    foreach ( $errors as $error ) {
      $vtp->addSession( $handle, 'error' );
      $vtp->setVar( $handle, 'error.content', $error );
      $vtp->closeSession( $handle, 'error' );
    }
    $vtp->closeSession( $handle, 'errors' );
  }
  // infos display
  if ( sizeof( $infos ) != 0 )
  {
    $vtp->addSession( $handle, 'infos' );
    foreach ( $infos as $info ) {
      $vtp->addSession( $handle, 'info' );
      $vtp->setVar( $handle, 'info.content', $info );
      $vtp->closeSession( $handle, 'info' );
    }
    $vtp->closeSession( $handle, 'infos' );
  }
  // form display (if necessary)
  if ( !isset( $_POST['submit'] ) or sizeof( $errors ) > 0 )
  {
    $vtp->addSession( $handle, 'step1' );

    // host
    if ( !isset( $_POST['cfgHote'] ) )
      $vtp->setVar( $handle, 'step1.f_host', 'localhost' );
    else
      $vtp->setVar( $handle, 'step1.f_host', $_POST['cfgHote'] );
    // user
    $vtp->setVar( $handle, 'step1.f_user', $_POST['cfgUser'] );
    // base
    $vtp->setVar( $handle, 'step1.f_base', $_POST['cfgBase'] );
    // prefixeTable
    if ( !isset( $_POST['prefixeTable'] ) )
      $vtp->setVar( $handle, 'step1.f_prefixeTable', 'phpwebgallery_' );
    else
      $vtp->setVar( $handle, 'step1.f_prefixeTable', $_POST['prefixeTable'] );

    $vtp->closeSession( $handle, 'step1' );
  }
}
//------------------------------------- Step 2 : creation of tables in database
else if ( $_GET['step'] == 2 )
{
  $errors = array();
  $infos  = array();

  include( '../include/mysql.inc.php' );
  mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
    or die ( "Can't connect to database host" );
  mysql_select_db( $cfgBase )
    or die ( "Connection to host succeeded, but database selection failed" );
		
  if ( !isset( $_POST['submit'] ) )
  {
    // tables creation, based on phpwebgallery_structure.sql
    $sql_lines = file( './phpwebgallery_structure.sql' );
    $query = '';
    foreach ( $sql_lines as $sql_line ) {
      $sql_line = trim( $sql_line );
      if ( preg_match( '/(^--|^$)/', $sql_line ) ) continue;
      $query.= ' '.$sql_line;
      // if we reached the end of query, we execute it and reinitialize the
      // variable "query"
      if ( preg_match( '/;$/', $sql_line ) )
      {
        $query = trim( $query );
        $query = str_replace( 'phpwebgallery_', $prefixeTable, $query );
        // we don't execute "DROP TABLE" queries
        if ( !preg_match( '/^DROP TABLE/i', $query ) )
          mysql_query( $query );
        $query = '';
      }
    }
  }

  if ( isset( $_POST['submit'] ) )
  {
    // webmaster login must be
    // 1. non empty
    // 2. without characters ' or "
    $webmaster = preg_replace( '/\s{2,}/', ' ', $_POST['webmaster'] );
    $webmaster = trim( $webmaster );
    if ( $webmaster == '' )
      array_push( $errors, $lang['step2_err_login1'] );
    if ( preg_match( '/[\'"]/', $webmaster ) )
      array_push( $errors, $lang['step2_err_login3'] );
    // the webmaster string must be the same as its confirmation
    if ( $_POST['pwdWebmaster'] != $_POST['pwdWebmasterConf'] )
      array_push( $errors, $lang['step2_err_pass'] );
    // mail address must have this format : name@server.com
    $error_mail_address = validate_mail_address( $_POST['mail_webmaster'] );
    if ( $error_mail_address != '' )
      array_push( $errors, $error_mail_address );
    if ( $_POST['mail_webmaster'] == '' )
      array_push( $errors, $lang['reg_err_mail_address'] );

    // if no error found till here : insertion of data in tables
    if ( count( $errors ) == 0 )
    {
      $query = 'DELETE FROM '.$prefixeTable.'config';
      mysql_query( $query );

      $query = 'INSERT INTO '.$prefixeTable.'config';
      $query.= ' (webmaster,mail_webmaster) VALUES ';
      $query.= " ('".$webmaster."','".$_POST['mail_webmaster']."')";
      $query.= ';';
      mysql_query( $query );

      $query = 'INSERT INTO '.$prefixeTable.'sites';
      $query.= " (id,galleries_url) VALUES (1, './galleries/')";
      $query.= ';';
      mysql_query( $query );

      // webmaster admin user
      $query = 'INSERT INTO '.$prefixeTable.'users';
      $query.= ' (id,username,password,status,language) VALUES ';
      $query.= "(1,'".$webmaster."','".md5( $_POST['pwdWebmaster'] )."'";
      $query.= ",'admin','".$_GET['language']."')";
      $query.= ';';
      mysql_query($query);

      // guest user
      $query = 'INSERT INTO '.$prefixeTable.'users';
      $query.= '(id,username,password,status,language) VALUES ';
      $query.= "(2,'guest','','guest','".$_GET['language']."')";
      $query.= ';';
      mysql_query( $query );
    }
  }

  // errors display
  if ( sizeof( $errors ) != 0 )
  {
    $vtp->addSession( $handle, 'errors' );
    foreach ( $errors as $error ) {
      $vtp->addSession( $handle, 'error' );
      $vtp->setVar( $handle, 'error.content', $error );
      $vtp->closeSession( $handle, 'error' );
      }
    $vtp->closeSession( $handle, 'errors' );
  }

  if ( !isset( $_POST['submit'] ) or sizeof( $errors ) > 0 )
  {
    $vtp->addSession( $handle, 'step2' );
    $vtp->setVar( $handle, 'step2.f_webmaster', $_POST['webmaster'] );
    $vtp->setVar( $handle, 'step2.f_mail_webmaster', $_POST['mail_webmaster']);
    $vtp->closeSession( $handle, 'step2' );
  }

  // end of installation message
  if ( isset( $_POST['submit'] ) and count( $errors ) == 0 )
  {
    $vtp->addSession( $handle, 'install_end' );
    $vtp->closeSession( $handle, 'install_end' );
  }
}
//---------------------------------------------------- Step 0 : language choice
else
{
  $vtp->addSession( $handle, 'step0' );
  $languages = get_languages( '../language/' );
  foreach ( $languages as $language ) {
    $vtp->addSession( $handle, 'language' );
    $vtp->setVar( $handle, 'language.name', $language );
    $vtp->closeSession( $handle, 'language' );
  }
  $vtp->closeSession( $handle, 'step0' );
}
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>