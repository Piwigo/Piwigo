<?php
/***************************************************************************
 *                   ajout.php is a part of PhpWebGallery                  *
 *                            -------------------                          *
 *   last update          : Tuesday, July 16, 2002                         *
 *   email                : pierrick@z0rglub.com                           *
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
 
	include_once( "./include/isadmin.inc.php" );
	$error = array();
	$absent = false;
	
	$row = mysql_fetch_array( mysql_query( "select pseudo,status,mail_address from $prefixeTable"."users where id = '".$HTTP_GET_VARS['user_id']."';" ) );
	$pseudo = $row['pseudo'];
	$status = $row['status'];
	$mail_address = $row['mail_address'];
	if ( $pseudo == "visiteur" || ( $pseudo == $conf['webmaster'] && $user['pseudo'] != $conf['webmaster'] ) )
	{
		echo "<div class=\"erreur\">".$lang['user_err_modify']."</div>";
		$absent = true;
	}
	if ( $HTTP_GET_VARS['mode'] == "modif" )
	{
		if ( $pseudo == "" )
		{
			echo"<div class=\"info\">".$lang['user_err_unknown']."</div>";
			$absent = true;
		}
	}
	if ( !$absent )
	{
		if ( $HTTP_GET_VARS['valider'] == 1 )
		{			
			$i = 0;
			// le pseudo ne doit pas
			// 1. être vide
			// 2. commencer ou se terminer par un espace
			// 3. comporter les caractères ' ou "
			// 4. être déjà utilisé
			// Notes sur le pseudo du webmaster :
			// - lorsque l'on trouve plusieurs occurences consécutives du caractère espace, on réduit à une seule occurence
			if ( $HTTP_GET_VARS['mode'] != "modif" )
			{
				if ( $HTTP_POST_VARS['pseudo'] == "" )
				{
					$error[$i++] = $lang['reg_err_login1'];
				}
				$pseudo = ereg_replace( "[ ]{2,}", " ", $HTTP_POST_VARS['pseudo'] );
				if ( ereg( "^.* $", $pseudo) )
				{
					$error[$i++] = $lang['reg_err_login2'];
				}
				if ( ereg( "^ .*$", $pseudo) )
				{
					$error[$i++] = $lang['reg_err_login3'];
				}
				if ( ereg( "'",$pseudo ) || ereg( "\"",$pseudo ) )
				{
					$error[$i++] = $lang['reg_err_login4'];
				}
				else
				{
					$query = "select id from $prefixeTable"."users where pseudo = '$pseudo';";
					$result = mysql_query( $query );
					if ( mysql_num_rows( $result ) > 0 )
					{
						$error[$i++] = "<li>".$lang['reg_err_login5']."</li>";
					}
				}
			}
			// le mail doit être conforme à qqch du type : nom@serveur.com
			if( $HTTP_POST_VARS['mail_address'] != "" && !ereg( "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)", $HTTP_POST_VARS['mail_address'] ) )
			{
				$error[$i++] = $lang['reg_err_mail_address'];
			}
			// mis à jour des variables pour ne pas afficher celles issue de la BD
			$pseudo = $HTTP_POST_VARS['pseudo'];
			$password = $HTTP_POST_VARS['password'];
			$status = $HTTP_POST_VARS['status'];
			$mail_address = $HTTP_POST_VARS['mail_address'];
			// on met à jour les paramètres de l'applicaiton dans le cas où il n'y aucune erreur
			if ( sizeof( $error ) == 0 && $HTTP_GET_VARS['mode'] != "modif" )
			{
				// 1.récupération des valeurs par défaut de l'application pour nombre_image_ligne,nombre_ligne_page,couleur,language
				$row = mysql_fetch_array( mysql_query( "select nombre_image_ligne,nombre_ligne_page,theme,language from $prefixeTable"."users where pseudo = 'visiteur';" ) );
				// 2.ajout du nouvel utilisateur
				$query = "insert into $prefixeTable"."users (pseudo,password,mail_address,nombre_image_ligne,nombre_ligne_page,theme,language,status) values ('$pseudo','".md5( $HTTP_POST_VARS['password'] )."',";
				if ( $HTTP_POST_VARS['mail_address'] != "" )
				{
					$query.= "'".$HTTP_POST_VARS['mail_address']."'";
				}
				else
				{
					$query.= "NULL";
				}
				$query.= ",'".$row['nombre_image_ligne']."','".$row['nombre_ligne_page']."','".$row['theme']."','".$row['language']."','".$HTTP_POST_VARS['status']."');";
				mysql_query( $query );
				// 3. récupérer l'identifiant de l'utilisateur nouvellement créé
				$row = mysql_fetch_array( mysql_query( "select id from $prefixeTable"."users where pseudo = '$pseudo';" ) );
				$user_id = $row['id'];
				// 4.ajouter les restrictions au nouvel utilisateur, les mêmes que celles de l'utilisateur par défaut
				$query = "select cat_id ";
				$query.= "from $prefixeTable"."restrictions as r,$prefixeTable"."users as u ";
				$query.= "where u.id = r.user_id ";
				$query.= "and u.pseudo = 'visiteur';";
				$result = mysql_query( $query );
				while( $row = mysql_fetch_array( $result ) )
				{
					mysql_query ( "insert into $prefixeTable"."restrictions (user_id,cat_id) values ('$user_id','".$row['cat_id']."');" );
				}
			}
			if ( sizeof( $error ) == 0 && $HTTP_GET_VARS['mode'] == "modif" )
			{
				$query = "update $prefixeTable"."users";
				$query.= " set status = '".$HTTP_POST_VARS['status']."'";
				if ( $HTTP_POST_VARS['use_new_pwd'] == 1 )
				{
					$query.= ", password = '".md5( $HTTP_POST_VARS['password'] )."'";
				}
				$query.= ", mail_address = ";
				if ( $HTTP_POST_VARS['mail_address'] != "" )
				{
					$query.= "'".$HTTP_POST_VARS['mail_address']."'";
				}
				else
				{
					$query.= "NULL";
				}
				$query.= " where id = '".$HTTP_GET_VARS['user_id']."';";
				mysql_query( $query );
			}
		}
		if ( sizeof( $error ) > 0 )
		{
			echo "<div class=\"erreur\">".$lang['adduser_err_message'].sizeof( $error )." :";
			echo "<ul>";
			for ( $i = 0; $i < sizeof( $error ); $i++ )
			{
				echo "<li>".$error[$i]."</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
		if ( sizeof( $error ) == 0 && $HTTP_GET_VARS['valider'] == 1 )
		{
			echo"<div class=\"info\">".$lang['adduser_info_message']."\"$pseudo\" ";
			if ( $HTTP_POST_VARS['use_new_pwd'] == 1 )
			{
				echo $lang['adduser_info_password_updated']." ";
			}
			echo"[ <a href=\"".add_session_id_to_url( "./admin.php?page=liste_users" )."\">".$lang['adduser_info_back']."</a> ]</div>";
		}
		if ( $HTTP_GET_VARS['valider'] != 1 || $HTTP_GET_VARS['mode'] != "modif" || sizeof( $error ) > 0 )
		{
			if ( $HTTP_GET_VARS['mode'] != "modif" && sizeof( $error ) == 0 )
			{
				unset( $pseudo, $password, $status, $mail_address );
			}
			if ( !isset( $HTTP_POST_VARS['use_new_pwd'] ) || $HTTP_POST_VARS['use_new_pwd'] != 1 )
			{
				unset( $password );
			}
			$action = "./admin.php?page=ajout&amp;valider=1";
			if ( $HTTP_GET_VARS['mode'] == "modif" )
			{
				$action.= "&amp;mode=modif&amp;user_id=".$HTTP_GET_VARS['user_id'];
			}
			echo"<form method=\"post\" action=\"".add_session_id_to_url( $action )."\">
				<table style=\"width:100%;\">
				<tr align=\"center\" valign=\"middle\">
					<td>
				<table style=\"margin-left:auto;margin-right:auto;\">
					<tr>
						<th colspan=\"2\">".$lang['adduser_fill_form']."</th>
					</tr>
					<tr>
						<td colspan=\"2\"><div style=\"margin-bottom:0px;\">&nbsp;</div></td>
					</tr>
					<tr>
						<td>".$lang['adduser_login']."</td>
						<td>";
			if ( $HTTP_GET_VARS['mode'] == "modif" )
			{
				echo"<span style=\"color:red;\">$pseudo [".$lang['adduser_unmodify']."]</span>";
				echo"<input type=\"hidden\" name=\"pseudo\" value=\"$pseudo\"/>";
			}
			else
			{
				echo"<input type=\"text\" name=\"pseudo\" value=\"$pseudo\"/>";
			}
			echo"
						</td>
					</tr>";
			echo"
					<tr>
						<td>";
			if ( $HTTP_GET_VARS['mode'] == "modif" )
			{
				echo $lang['new']." ".$lang['password']."<input type=\"checkbox\" name=\"use_new_pwd\" value=\"1\"";
				if ( isset( $HTTP_POST_VARS['use_new_pwd'] ) && $HTTP_POST_VARS['use_new_pwd'] == 1 )
				{
					echo " checked=\"checked\"";
				}
				echo " />";
			}
			else
			{
				echo $lang['password'];
			}
			echo"</td>
						<td>";
			echo"<input type=\"text\" name=\"password\" value=\"$password\"/></td>
					</tr>";
			echo"
					<tr>
						<td>".$lang['reg_mail_address']."</td>";
			echo "
						<td><input type=\"text\" name=\"mail_address\" value=\"$mail_address\"/></td>
					</tr>";			
			echo"
					<tr>
						<td>".$lang['adduser_status']."</td>
						<td>";
			if ( $pseudo == $conf['webmaster'] )
			{
				echo "<span style=\"color:red;\">$status [".$lang['adduser_unmodify']."]</span>
							<input type=\"hidden\" name=\"status\" value=\"$status\"/>";
			}
			else
			{
				echo"
							<select name=\"status\">";
				// on récupère toutes les status possibles dans la base 
				// par l'intermédiaire de la fonction get_enums
				$option = get_enums( $prefixeTable."users", "status" );
				for ( $i = 0; $i < sizeof( $option ); $i++ )
				{
					if ( isset( $status ) )
					{
						echo"
									<option value=\"$option[$i]\"";
						if ( $option[$i] == $status )
						{
							echo" selected=\"selected\"";
						}
						echo">";
						switch ( $option[$i] )
						{
							case "admin" :
							{
								echo $lang['adduser_status_admin'];
								break;
							}
							case "membre" :
							{
								echo $lang['adduser_status_member'];
								break;
							}
							case "visiteur" :
							{
								echo $lang['adduser_status_guest'];
								break;
							}
						}
						echo"</option>";
					}
					else
					{
						echo"
										<option value=\"$option[$i]\"";
						if ( $option[$i] == "visiteur" )
						{
							echo" selected=\"selected\"";
						}
						echo">";
						switch ( $option[$i] )
						{
							case "admin" :
							{
								echo $lang['adduser_status_admin'];
								break;
							}
							case "membre" :
							{
								echo $lang['adduser_status_member'];
								break;
							}
							case "visiteur" :
							{
								echo $lang['adduser_status_guest'];
								break;
							}
						}
						echo"</option>";
					}
				}
				echo"
							</select>";
			}
			echo"
						</td>
					</tr>
					<tr>
						<td colspan=\"2\" align=\"center\"><input type=\"submit\"  value=\"".$lang['submit']."\"/></td>
					</tr>
				</table>
				</td>
				</tr>
				</table>
			</form>";
			if ( $HTTP_GET_VARS['mode'] == "modif" )
			{
				echo "<div style=\"text-align:center;margin-bottom:10px;\">[ <a href=\"".add_session_id_to_url( "./admin.php?page=liste_users" )."\">".$lang['adduser_info_back']."</a> ]</div>";
			}
		}
	}
?>