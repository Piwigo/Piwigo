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
                'step1_prefix_info','submit','infos_title' );
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
      $file_content.= "\n\$prefix_table = '".$_POST['prefix_table']."';";
      $file_content.= "\n?>";
      // writting the configuraiton file
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
    {
      $vtp->setVar( $handle, 'step1.f_host', 'localhost' );
    }
    else
    {
      $vtp->setVar( $handle, 'step1.f_host', $_POST['cfgHote'] );
    }
    // user
    $vtp->setVar( $handle, 'step1.f_user', $_POST['cfgUser'] );
    // base
    $vtp->setVar( $handle, 'step1.f_base', $_POST['cfgBase'] );
    // prefix_table
    if ( !isset( $_POST['prefix_table'] ) )
    {
      $vtp->setVar( $handle, 'step1.f_prefix_table', 'phpwebgallery_' );
    }
    else
    {
      $vtp->setVar( $handle, 'step1.f_prefix_table', $_POST['prefix_table'] );
    }
    
    $vtp->closeSession( $handle, 'step1' );
  }
}
//------------------------------------- Step 2 : creation of tables in database
else if ( $_GET['step'] == 2 )
{
  include( '../include/mysql.inc.php' );
  mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
    or die ( "Can't connect to database host" );
  mysql_select_db( $cfgBase )
    or die ( "Connection to host succeeded, but database selection failed" );
		
  if ( !isset( $_POST['submit'] ) )
  {
    // tables creation, based on phpwebgallery_structure.sql
  }

  if ( isset( $_POST['submit'] ) )
  {
    $configuration = false;
    $erreur = "";
    $nb_erreur = 0;
    // le pseudo du webmaster ne doit pas
    // 1. être vide
    // 2. commencer ou se terminer par un espace
    // 3. comporter les caractères ' ou "
    // Notes sur le pseudo du webmaster :
    // - lorsque l'on trouve plusieurs occurences
    // consécutives du caractère espace, on réduit à une seule occurence
    if ( $_POST['webmaster'] == "" )
    {
      $erreur .= "<li>".$lang['step2_err_login1']."</li>";
      $nb_erreur++;
    }
    $webmaster = ereg_replace( "[ ]{2,}", " ", $_POST['webmaster'] );
    if ( ereg( "^.* $", $webmaster ) or ereg( "^ .*$", $webmaster) )
    {
      $erreur .= "<li>".$lang['step2_err_login2']."</li>";
      $nb_erreur++;
    }
    if ( ereg( "'",$webmaster ) or ereg( "\"",$webmaster ) )
    {
      $erreur .= "<li>".$lang['step2_err_login3']."</li>";
      $nb_erreur++;
    }
    // on vérifie que le password rentré correspond bien à la confirmation faite par l'utilisateur
    if ( $_POST['pwdWebmaster'] != $_POST['pwdWebmasterConf'] )
    {
      $erreur .= "<li>".$lang['step2_err_pass']."</li>";
      $nb_erreur++;
    }
    // le mail doit être conforme à qqch du type : nom@serveur.com
    if( !ereg("([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)", $_POST['mail_webmaster'] ) )
    {
      $erreur .= "<li>".$lang['step2_err_mail']."</li>";
      $nb_erreur++;
    }
    // on met à jour les paramètres de l'application dans le cas où il n'y aucune erreur
    if ( $nb_erreur == 0 )
    {
      mysql_query( "delete from PREFIX_TABLE"."config" );
      $query = "insert into PREFIX_TABLE"."config (webmaster,mail_webmaster) values ('$webmaster','".$_POST['mail_webmaster']."')";
      mysql_query($query);
      $query = "insert into PREFIX_TABLE"."sites values (1, './galleries/');";
      mysql_query($query);
      $query = "insert into PREFIX_TABLE"."users (pseudo,password,status,language) values ('$webmaster','".md5( $pwdWebmaster )."','admin','".$_GET['language']."')";
      mysql_query($query);
      mysql_query("insert into PREFIX_TABLE"."users (pseudo,password,status,language) values ('visiteur','".md5( "" )."','visiteur','".$_GET['language']."')");
      $configuration = true;
    }
  }
		
  echo header_install();
  if ( $configuration )
  {
    echo"
						<table width=\"100%\">
							<tr>
								<th>".$lang['install_end_title']."</th>
							</tr>
							<tr>
								<td>&nbsp;</th>
							</tr>
							<tr>
								<td>".$lang['install_end_message']."</td>
							</tr>
						</table>";
  }
  else
  {
    if ( $nb_erreur > 0 )
    {
      echo"
						<table width=100%>
							<tr>
								<th>".$lang['install_message']."</th>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>$erreur</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>";
    }
    echo"
					<form method=\"post\" action=\"install.php?step=2&amp;language=".$_GET['language']."\">
						<table width=100%>
							<tr>
								<th colspan=\"3\">".$lang['step2_title']."</th>
							</tr>
							<tr>
								<td colspan=\"3\">&nbsp;</td>
							</tr>
							<tr>
								<td>".$lang['conf_general_webmaster']."</td>
								<td align=\"center\"><input type='text' name='webmaster' value=\"".$_POST['webmaster']."\"></td>
								<td class=\"row2\">".$lang['conf_general_webmaster_info']."</td>
							</tr>
							<tr>
								<td>".$lang['step2_pwd']."</td>
								<td align=center><input type='password' name='pwdWebmaster' value=''></td>
								<td class=\"row2\">".$lang['step2_pwd_info']."</td>
							</tr>
							<tr>
								<td>".$lang['step2_pwd_conf']."</td>
								<td align=center><input type='password' name='pwdWebmasterConf' value=''></td>
								<td class=\"row2\">".$lang['step2_pwd_conf_info']."</td>
							</tr>
							<tr>
								<td>".$lang['conf_general_mail']."</td>
								<td align=center><input type='text' name='mail_webmaster' value=\"".$_POST['mail_webmaster']."\"></td>
								<td class=\"row2\">".$lang['conf_general_mail_info']."</td>
							</tr>
							<tr>
								<td colspan=\"3\">&nbsp;</th>
							</tr>
							<tr>
								<td colspan=3 align=center>
									<input type='submit' name='submit' value='".$lang['submit']."'>
								</td>
							</tr>
						</table>
					</form>";
  }
  echo footer_install();
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