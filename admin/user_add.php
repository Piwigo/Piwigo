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
 
include_once( './include/isadmin.inc.php' );

$error = array();
$absent = false;

$query = 'select';
$query.= ' pseudo,status,mail_address';
$query.= ' from '.$prefixeTable.'users';
$query.= ' where id = '.$_GET['user_id'];
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );

$pseudo = $row['pseudo'];
$status = $row['status'];
$mail_address = $row['mail_address'];

if ( $pseudo == 'visiteur' ||
     ( $pseudo == $conf['webmaster']
       && $user['pseudo'] != $conf['webmaster'] ) )
{
  echo "<div class=\"erreur\">".$lang['user_err_modify']."</div>";
  $absent = true;
}
if ( $_GET['mode'] == 'modif' )
{
  if ( $pseudo == '' )
  {
    echo"<div class=\"info\">".$lang['user_err_unknown']."</div>";
    $absent = true;
  }
}
if ( !$absent )
{
  if ( $_GET['valider'] == 1 )
  {			
    if ( $_GET['mode'] != 'modif' )
    {
      $error = register_user( $_POST['pseudo'], $_POST['password'],
                              $_POST['password'], $_POST['mail_address'],
                              $_POST['status'] );
    }
    else
    {
      $use_new_password = false;
      if ( $_POST['use_new_pwd'] == 1)
      {
        $use_new_password = true;
      }
      $error = update_user( $_GET['user_id'], $_POST['mail_address'],
                            $_POST['status'], $use_new_password,
                            $_POST['password'] );
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
  if ( sizeof( $error ) == 0 && $_GET['valider'] == 1 )
  {
    echo"<div class=\"info\">".$lang['adduser_info_message']."\"$pseudo\" ";
    if ( $_POST['use_new_pwd'] == 1 )
    {
      echo $lang['adduser_info_password_updated']." ";
    }
    echo"[ <a href=\"".add_session_id_to_url( "./admin.php?page=liste_users" )."\">".$lang['adduser_info_back']."</a> ]</div>";
  }
  if ( $_GET['valider'] != 1 || $_GET['mode'] != "modif" || sizeof( $error ) > 0 )
  {
    if ( $_GET['mode'] != "modif" && sizeof( $error ) == 0 )
    {
      unset( $pseudo, $password, $status, $mail_address );
    }
    if ( !isset( $_POST['use_new_pwd'] ) || $_POST['use_new_pwd'] != 1 )
    {
      unset( $password );
    }
    $action = "./admin.php?page=ajout&amp;valider=1";
    if ( $_GET['mode'] == "modif" )
    {
      $action.= "&amp;mode=modif&amp;user_id=".$_GET['user_id'];
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
    if ( $_GET['mode'] == "modif" )
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
    if ( $_GET['mode'] == "modif" )
    {
      echo $lang['new']." ".$lang['password']."<input type=\"checkbox\" name=\"use_new_pwd\" value=\"1\"";
      if ( isset( $_POST['use_new_pwd'] ) && $_POST['use_new_pwd'] == 1 )
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
    if ( $_GET['mode'] == "modif" )
    {
      echo "<div style=\"text-align:center;margin-bottom:10px;\">[ <a href=\"".add_session_id_to_url( "./admin.php?page=liste_users" )."\">".$lang['adduser_info_back']."</a> ]</div>";
    }
  }
}
?>