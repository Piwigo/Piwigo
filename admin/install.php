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
function header_install()
{
  $output = "
<html>
	<head>
		<title>PhpWebGallery 1.2</title>
		<style>
			a
			{
				text-decoration : none;
				color : #006699;
			}
			a:hover
			{
				text-decoration : underline;
			}
			body,table,input,form,select,textarea
			{
				font-family : Arial, Verdana, Sans-Serif;
				font-size : 12px;
			}
			.miniature
			{
				border : solid 1px black;
			}
			body
			{
				background-color :  #E5E5E5;
			}
			.titretable1
			{
				color : black;
				background-color : #D3DCE3;
				text-align : center;
				border : 2px solid #006699;
			}
			.grostitre
			{
				text-align : center;
				font-size : 20px;
				margin-bottom : 20px;
			}
			.plan
			{
				margin : 10px 10px 10px 2px;
				white-space : nowrap;
			}
			.table1
			{
				border-collapse : collapse;
				background-color : #FFFFFF;
			}
			.contenucellule
			{
				background-color : #EEEEEE;
				border : 2px solid #006699;
			}
			.style1
			{
				margin-top : 20px;
			}
			th
			{
				font-weight : bold;
				background-color : #D3DCE3;
			}
			td.row1
			{
				background-color : #DDDDDD;
			}
			td.row2
			{
				background-color : #E8E8E8;
			}
			.cat_plan
			{
				font-weight : bold;
			}
			.retrait
			{
				margin : 10px;
				margin-left : 30px;
				margin-top : 2px;
			}
			input,textarea
			{
				border-width : 1;
				border-color : #000000;
				background : #ffffff;
				color: #000000;
			}
			.erreur
			{
				color : red;
				text-align : center;
			}
			.info
			{
				color : darkblue;
				text-align : center;
			}
		</style>
	</head>
	<body>
		<table style=\"width:100%;height:100%\">
			<tr align=\"center\" valign=\"middle\">
				<td>
					<div class=\"grostitre\">PhpWebGallery 1.2</div>
					<table width=\"700\" class=\"table1\" style=\"margin:auto;\">
						<tr>
							<td class=\"contenucellule\">";
  return $output;
}
	
function footer_install()
{
  $output = "
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>
</html>";
  return $output;
}
	
if ( isset( $HTTP_GET_VARS['language'] ) )
{
  $isadmin = true;
  $lang = array();
  include( "../language/".$HTTP_GET_VARS['language'].".php" );
}
	
/*---------------------------------------Step 1------------------------------------*/
if ( $HTTP_GET_VARS['step'] == 1 )
{
  $erreur1 = true;
  $message = "";
  // création du fichier de configuration de connexion à la BD mysql
  if( isset( $HTTP_POST_VARS['cfgBase'] ) && isset( $HTTP_POST_VARS['cfgUser'] ) && isset( $HTTP_POST_VARS['cfgPassword'] ) && isset( $HTTP_POST_VARS['cfgHote'] ) )
  {
    if ( @mysql_connect( $HTTP_POST_VARS['cfgHote'], $HTTP_POST_VARS['cfgUser'], $HTTP_POST_VARS['cfgPassword'] ) )
    {
      if ( @mysql_select_db($HTTP_POST_VARS['cfgBase'] ) )
      {
        $message.= "<div class=\"info\">".$lang['step1_confirmation']."</div>";
        $erreur1 = false;
      }
      else
      {
        $message.= "<div class=\"erreur\">".$lang['step1_err_db']."</div>";
      }
    }
    else
    {
      $message.= "<div class=\"erreur\">".$lang['step1_err_server']."</div>";
    }
			
    if ( !$erreur1 )
    {				
      // écriture du fichier de configuration
      if ( $fp = @fopen("../include/mysql.inc.php","a+") )
      {
        fwrite( $fp, "<?php\n\t\$cfgBase='".$HTTP_POST_VARS['cfgBase']."';\n\t\$cfgUser='".$HTTP_POST_VARS['cfgUser']."';\n\t\$cfgPassword='".$HTTP_POST_VARS['cfgPassword']."';\n\t\$cfgHote='".$HTTP_POST_VARS['cfgHote']."';\n\t\PREFIX_TABLE='".$HTTP_POST_VARS['prefixe']."';\n?>" ); 
        fclose( $fp );
      }
      $cfgHote = "";
      $cfgUser = "";
      $cfgPassword = "";
      $cfgBase = "";
      include ( "../include/mysql.inc.php" );
      $erreur2 = true;
      if ( @mysql_connect( $cfgHote, $cfgUser, $cfgPassword ) )
      {
        if ( @mysql_select_db ( $cfgBase ) )
        {
          $erreur2 = false;
        }
      }
      if ( $erreur2 )
      {
        $message.="<br /><br />".$lang['step1_err_copy']." :<br />
							-----------------------------------------------------<br />
							<div style=\"color:blue;\">&lt;?php<br />
							\$cfgBase = '".$HTTP_POST_VARS['cfgBase']."';<br />
							\$cfgUser = '".$HTTP_POST_VARS['cfgUser']."';<br />
							\$cfgPassword = '".$HTTP_POST_VARS['cfgPassword']."';<br />
							\$cfgHote = '".$HTTP_POST_VARS['cfgHote']."';<br />
							\PREFIX_TABLE = '".$HTTP_POST_VARS['prefixe']."';<br />
							?&gt;</div>
							-----------------------------------------------------<br />";
        $message.= "<div style=\"text-align:center;\">".$lang['step1_err_copy_2']."<br />";
        $message.= "<a href=\"install.php?step=2&amp;language=".$HTTP_GET_VARS['language']."\">".$lang['step1_err_copy_next']."</a></div>";
      }
      else
      {
        $url = "install.php?step=2&language=".$HTTP_GET_VARS['language'];
        header("Request-URI: $url");  
        header("Content-Location: $url");  
        header("Location: $url");
        exit();
      }
    }
  }
		
  echo header_install();
  if ( isset( $message ) && $message != "" )
  {
    echo"
					<table width=\"100%\">
						<tr>
							<th>".$lang['install_message']."</th>
						</tr>
						<tr>
							<td>$message</td>
						</tr>
					</table>";
  }
  if ( $erreur1 )
  {
    echo"
					<form method=\"post\" action=\"install.php?step=1&amp;language=".$HTTP_GET_VARS['language']."\">
						<table width=\"100%\">
							<tr>
								<th colspan=\"3\">".$lang['step1_title']."</th>
							</tr>
							<tr>
								<td colspan=\"3\">&nbsp;</th>
							</tr>
							<tr>
								<td>".$lang['step1_host']."</td>
								<td align=center><input type='text' name='cfgHote' value='";
    if ( !isset( $HTTP_POST_VARS['cfgHote'] ) )
    {
      echo"localhost";
    }
    else
    {
      echo $HTTP_POST_VARS['cfgHote'];
    }
    echo"'></td>
								<td class=\"row2\">".$lang['step1_host_info']."</td>
							</tr>
							<tr>
								<td>".$lang['step1_user']."</td>
								<td align=center><input type='text' name='cfgUser' value='".$HTTP_POST_VARS['cfgUser']."'></td>
								<td class=\"row2\">".$lang['step1_user_info']."</td>
							</tr>
							<tr>
								<td>".$lang['step1_pass']."</td>
								<td align=center><input type='password' name='cfgPassword' value=''></td>
								<td class=\"row2\">".$lang['step1_pass_info']."</td>
							</tr>
							<tr>
								<td>".$lang['step1_database']."</td>
								<td align=center><input type='text' name='cfgBase' value='".$HTTP_POST_VARS['cfgBase']."'></td>
								<td class=\"row2\">".$lang['step1_database_info']."</td>
							</tr>
							<tr>
								<td>".$lang['step1_prefix']."</td>
								<td align=center><input type='text' name='prefixe' value='";
    if ( !isset( $HTTP_POST_VARS['prefixe'] ) )
    {
      echo"phpwebgallery_";
    }
    else
    {
      echo $HTTP_POST_VARS['prefixe'];
    }
    echo"'></td>
								<td class=\"row2\">".$lang['step1_prefix_info']."</td>
							</tr>
							<tr>
								<td colspan=\"3\">&nbsp;</th>
							</tr>
							<tr>
								<td colspan=3 align=center><input type='submit' name='Valider' value=\"".$lang['submit']." *\"></td>
							</tr>
						</table>
					</form>";
  }
  echo footer_install();
}
/*---------------------------------------Step 2------------------------------------*/
else if ( $HTTP_GET_VARS['step'] == 2 )
{
  include( "../include/mysql.inc.php" );
  mysql_connect( $cfgHote, $cfgUser, $cfgPassword ) or die ( "erreur de connexion au serveur" );
  mysql_select_db( $cfgBase ) or die ( "erreur de connexion a la base de donnees" );
		
  if ( !isset( $HTTP_POST_VARS['submit'] ) )
  {
    $query = "CREATE TABLE ".PREFIX_TABLE."categories (
				id tinyint(3) unsigned NOT NULL auto_increment,
			  date_dernier date NOT NULL default '0000-00-00',
			  nb_images smallint(5) unsigned NOT NULL default '0',
			  name varchar(255) default NULL,
			  id_uppercat tinyint(3) unsigned default NULL,
			  comment text,
			  dir varchar(255) NOT NULL default '',
			  rank tinyint(3) unsigned default NULL,
			  status enum('visible','invisible') NOT NULL default 'visible',
			  site_id tinyint(4) unsigned NOT NULL default '1',
			  PRIMARY KEY (id)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."comments (
			  id int(11) unsigned NOT NULL auto_increment,
			  image_id smallint(5) unsigned NOT NULL default '0',
			  date int(11) unsigned NOT NULL default '0',
			  author varchar(255) NOT NULL default '',
			  content longtext,
			  PRIMARY KEY  (id)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."config (
			  periode_courte smallint(5) unsigned NOT NULL default '7',
			  periode_longue smallint(5) unsigned NOT NULL default '14',
			  prefix_thumbnail varchar(10) NOT NULL default 'TN-',
			  webmaster varchar(255) NOT NULL default '',
			  mail_webmaster varchar(255) NOT NULL default '',
			  acces enum('libre','restreint') NOT NULL default 'libre',
			  session_id_size tinyint(3) unsigned NOT NULL default '4',
			  session_keyword varchar(255) NOT NULL default '',
			  session_time tinyint(3) unsigned NOT NULL default '30',
			  max_user_listbox tinyint(3) unsigned NOT NULL default '10',
			  expand enum('true','false') NOT NULL default 'false',
			  show_comments enum('true','false') NOT NULL default 'true',
			  nb_comment_page tinyint(4) NOT NULL default '10',
			  upload_available enum('true','false') NOT NULL default 'false',
			  upload_maxfilesize smallint(5) unsigned NOT NULL default '150',
			  upload_maxwidth smallint(5) unsigned NOT NULL default '800',
			  upload_maxheight smallint(5) unsigned NOT NULL default '600',
			  upload_maxwidth_thumbnail smallint(5) unsigned NOT NULL default '150',
			  upload_maxheight_thumbnail smallint(5) unsigned NOT NULL default '100'
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."favorites (
			  user_id smallint(5) unsigned NOT NULL default '0',
			  image_id smallint(5) unsigned NOT NULL default '0',
			  KEY user_id (user_id,image_id)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."history (
			  date int(11) NOT NULL default '0',
			  login varchar(15) default NULL,
			  IP varchar(50) NOT NULL default '',
			  categorie varchar(150) default NULL,
			  page varchar(50) default NULL,
			  titre varchar(150) default NULL,
			  commentaire varchar(200) default NULL
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."images (
			  id smallint(5) unsigned NOT NULL auto_increment,
			  file varchar(255) NOT NULL default '',
			  cat_id tinyint(3) unsigned NOT NULL default '0',
			  date_available date NOT NULL default '0000-00-00',
			  date_creation date default NULL,
			  tn_ext char(3) NOT NULL default 'jpg',
			  name varchar(255) default NULL,
			  comment varchar(255) default NULL,
			  author varchar(255) default NULL,
			  hit int(10) unsigned NOT NULL default '0',
			  filesize mediumint(9) unsigned default NULL,
			  width smallint(9) unsigned default NULL,
			  height smallint(9) unsigned default NULL,
			  PRIMARY KEY  (id),
			  KEY cat_id (cat_id)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."restrictions (
			  user_id smallint(5) unsigned NOT NULL default '0',
			  cat_id tinyint(3) unsigned NOT NULL default '0',
			  PRIMARY KEY  (user_id,cat_id)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."sessions (
			  id varchar(255) binary NOT NULL default '',
			  user_id smallint(5) unsigned NOT NULL default '0',
			  expiration int(10) unsigned NOT NULL default '0',
			  ip varchar(255) NOT NULL default '',
			  PRIMARY KEY  (id)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."sites (
			  id tinyint(4) NOT NULL auto_increment,
			  galleries_url varchar(255) NOT NULL default '',
			  PRIMARY KEY  (id),
			  UNIQUE KEY galleries_url (galleries_url)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."users (
			  id smallint(5) unsigned NOT NULL auto_increment,
			  pseudo varchar(20) binary NOT NULL default '',
			  password varchar(255) NOT NULL default '',
			  mail_address varchar(255) default NULL,
			  nombre_image_ligne tinyint(1) unsigned NOT NULL default '5',
			  nombre_ligne_page tinyint(3) unsigned NOT NULL default '3',
			  theme varchar(255) NOT NULL default 'melodie/blue',
			  status enum('admin','membre','visiteur') NOT NULL default 'visiteur',
			  language varchar(50) NOT NULL default 'english',
			  maxwidth smallint(6) default NULL,
			  maxheight smallint(6) default NULL,
			  PRIMARY KEY  (id),
			  UNIQUE KEY pseudo (pseudo)
			);";
    mysql_query( $query );
    $query = "CREATE TABLE ".PREFIX_TABLE."waiting (
			  id int(10) unsigned NOT NULL auto_increment,
			  cat_id tinyint(3) unsigned NOT NULL default '0',
			  file varchar(255) NOT NULL default '',
			  username varchar(255) NOT NULL default '',
			  mail_address varchar(255) NOT NULL default '',
			  date int(10) unsigned NOT NULL default '0',
			  tn_ext char(3) default NULL,
			  PRIMARY KEY  (id)
			);";
    mysql_query( $query );
  }
  if ( isset( $HTTP_POST_VARS['submit'] ) )
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
    if ( $HTTP_POST_VARS['webmaster'] == "" )
    {
      $erreur .= "<li>".$lang['step2_err_login1']."</li>";
      $nb_erreur++;
    }
    $webmaster = ereg_replace( "[ ]{2,}", " ", $HTTP_POST_VARS['webmaster'] );
    if ( ereg( "^.* $", $webmaster ) || ereg( "^ .*$", $webmaster) )
    {
      $erreur .= "<li>".$lang['step2_err_login2']."</li>";
      $nb_erreur++;
    }
    if ( ereg( "'",$webmaster ) || ereg( "\"",$webmaster ) )
    {
      $erreur .= "<li>".$lang['step2_err_login3']."</li>";
      $nb_erreur++;
    }
    // on vérifie que le password rentré correspond bien à la confirmation faite par l'utilisateur
    if ( $HTTP_POST_VARS['pwdWebmaster'] != $HTTP_POST_VARS['pwdWebmasterConf'] )
    {
      $erreur .= "<li>".$lang['step2_err_pass']."</li>";
      $nb_erreur++;
    }
    // le mail doit être conforme à qqch du type : nom@serveur.com
    if( !ereg("([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)", $HTTP_POST_VARS['mail_webmaster'] ) )
    {
      $erreur .= "<li>".$lang['step2_err_mail']."</li>";
      $nb_erreur++;
    }
    // on met à jour les paramètres de l'application dans le cas où il n'y aucune erreur
    if ( $nb_erreur == 0 )
    {
      mysql_query( "delete from PREFIX_TABLE"."config" );
      $query = "insert into PREFIX_TABLE"."config (webmaster,mail_webmaster) values ('$webmaster','".$HTTP_POST_VARS['mail_webmaster']."')";
      mysql_query($query);
      $query = "insert into PREFIX_TABLE"."sites values (1, './galleries/');";
      mysql_query($query);
      $query = "insert into PREFIX_TABLE"."users (pseudo,password,status,language) values ('$webmaster','".md5( $pwdWebmaster )."','admin','".$HTTP_GET_VARS['language']."')";
      mysql_query($query);
      mysql_query("insert into PREFIX_TABLE"."users (pseudo,password,status,language) values ('visiteur','".md5( "" )."','visiteur','".$HTTP_GET_VARS['language']."')");
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
					<form method=\"post\" action=\"install.php?step=2&amp;language=".$HTTP_GET_VARS['language']."\">
						<table width=100%>
							<tr>
								<th colspan=\"3\">".$lang['step2_title']."</th>
							</tr>
							<tr>
								<td colspan=\"3\">&nbsp;</td>
							</tr>
							<tr>
								<td>".$lang['conf_general_webmaster']."</td>
								<td align=\"center\"><input type='text' name='webmaster' value=\"".$HTTP_POST_VARS['webmaster']."\"></td>
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
								<td align=center><input type='text' name='mail_webmaster' value=\"".$HTTP_POST_VARS['mail_webmaster']."\"></td>
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
/*----------------------------------Language choice------------------------------------*/
else
{
  include( "../include/functions.php" );
  echo header_install();
  echo"
					<form method=\"get\" action=\"install.php\">
						<input type=\"hidden\" name=\"step\" value=\"1\"/>
						<table width=\"100%\">
							<tr>
								<td align=\"center\">
									<select name=\"language\">";
  $languages = get_languages( "../language/" );
  for ( $i = 0; $i < sizeof ( $languages ); $i++ )
  {
    echo"
										<option>".$languages[$i]."</option>";
  }
  echo"
										</select>
										<input type=\"submit\" value=\"Go\">
								</td>
							</tr>
						</table>
					</form>";
  echo footer_install();
}
?>