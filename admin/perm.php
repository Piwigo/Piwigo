<?php
/***************************************************************************
 *                     perm.php is a part of PhpWebGallery                 *
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
	//---------------------------------------------------données de l'utilisateur
	if ( isset( $HTTP_GET_VARS['user_id'] ) )
	{
		$query = "select id,pseudo,status from PREFIX_TABLE"."users where id = '".$HTTP_GET_VARS['user_id']."';";
		$result = mysql_query( $query );
		$row = mysql_fetch_array( $result );
		$page['pseudo'] = $row['pseudo'];
		$page['status'] = $row['status'];
		if ( mysql_num_rows( $result ) == 0 )
		{
			echo"<div class=\"erreur\">".$lang['user_err_unknown']."</div>";
			$erreur = true;
		}
		if ( $row['pseudo'] == $conf['webmaster'] )
		{
			echo"<div class=\"erreur\">".$lang['user_err_modify']."</div>";
			$erreur = true;
		}
	}
	//---------------------------------------------------données de la catégorie
	if ( isset( $HTTP_GET_VARS['cat_id'] ) )
	{
		$HTTP_GET_VARS['cat'] = $HTTP_GET_VARS['cat_id'];
		check_cat_id( $HTTP_GET_VARS['cat_id'] );
		if ( isset( $page['cat'] ) )
		{
			$result = get_cat_info( $page['cat'] );
			$page['cat_name'] = $result['name'];
			$page['id_uppercat'] = $result['id_uppercat'];
		}
	}
	//--------------------------------------------------------------- mise à jour
	if ( isset( $HTTP_POST_VARS['submit'] ) )
	{
		if ( isset( $HTTP_GET_VARS['user_id'] ) )
		{
			mysql_query ( "delete from PREFIX_TABLE"."restrictions where user_id = ".$HTTP_GET_VARS['user_id'].";" );
			$result = mysql_query ( "select id from PREFIX_TABLE"."categories;" );
			while ( $row = mysql_fetch_array ( $result ) )
			{
				$nom_select = "acces-".$row['id'];
				if ( $HTTP_POST_VARS[$nom_select] == 1 )
				{
					mysql_query ( "insert into PREFIX_TABLE"."restrictions (user_id,cat_id) values ('".$HTTP_GET_VARS['user_id']."','".$row['id']."');" );
				}
			}
			check_favorites( $HTTP_GET_VARS['user_id'] );
			echo "<div class=\"info\">".$lang['permuser_info_message']." [ <a href=\"".add_session_id_to_url( "./admin.php?page=liste_users" )."\">".$lang['adduser_info_back']."</a> ]</div>";
		}
		else if ( isset( $HTTP_GET_VARS['cat_id'] ) )
		{
			mysql_query ( "delete from PREFIX_TABLE"."restrictions where cat_id = '".$page['cat']."';" );
			$result = mysql_query( "select id from PREFIX_TABLE"."users where pseudo != '".$conf['webmaster']."';" );
			while ( $row = mysql_fetch_array ( $result ) )
			{
				$nom_select = "acces-".$row['id'];
				if ( $HTTP_POST_VARS[$nom_select] == 1 )
				{
					mysql_query ( "insert into PREFIX_TABLE"."restrictions (user_id,cat_id) values ('".$row['id']."','".$page['cat']."');" );
				}
				check_favorites( $row['id'] );
			}
			echo "<div class=\"info\">".$lang['permuser_info_message']." [ <a href=\"".add_session_id_to_url( "./admin.php?page=cat" )."\">".$lang['editcat_back']."</a> ]</div>";
		}
	}
	//--------------------------------------------------------------- formulaire
	function display_cat_manager( $id_uppercat, $indent, $uppercat_authorized, $level, $restriction )
	{
		global PREFIX_TABLE,$lang,$conf;
		
		$output = "";
		
		// will we use <th> or <td> lines ?
		if ( $level == 0 )
		{
			$start_line = "<th ";
			$start_line2 = "<th ";
			$end_line = "</th>";
		}
		else
		{
			$start_line = "<td ";
			$end_line = "</td>";
			if ( $level == 1 )
			{
				$start_line2 = "<td class=\"row1\" ";
			}
			else if ( $level == 2 )
			{
				$start_line2 = "<td class=\"row2\" ";
			}
			else if ( $level == 3 )
			{
				$start_line2 = "<td class=\"row3\" ";
			}
			else if ( $level == 4 )
			{
				$start_line2 = "<td class=\"row4\" ";
			}
			else
			{
				$start_line2 = "<td ";
			}
		}
		
		$query = "select id,name,dir,rank";
		$query.= " from PREFIX_TABLE"."categories";
		if ( !is_numeric( $id_uppercat ) )
		{
			$query.= " where id_uppercat is NULL";
		}
		else
		{
			$query.= " where id_uppercat = $id_uppercat";
		}
		$query.= " order by rank asc;";
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			$subcat_authorized = true;
			
			$output.= "\n<tr>";
			$output.= "\n\t".$start_line."style=\"width:85%;text-align:left;\">$indent<img src=\"".$conf['lien_puce']."\" alt=\"&gt;\" />&nbsp;";
			if ( in_array( $row['id'], $restriction ) || !$uppercat_authorized )
			{
				$subcat_authorized = false;
				$color = "red";
			}
			else
			{
				$color = "green";
			}
			$output.= "<span style=\"color:$color;\">";
			if ( $row['name'] == "" )
			{
				$output.= str_replace( "_", " ", $row['dir'] );
			}
			else
			{
				$output.= $row['name'];
			}
			$output.= " [ dir : ".$row['dir']." ]";
			$output.= "</span>".$end_line;
			$output.= "\n\t".$start_line2." style=\"width:15%;white-space:nowrap;text-align:right;font-weight:normal;\">";
			$output.= "\n\t\t<input type=\"radio\" name=\"acces-".$row['id']."\" value=\"0\" checked=\"checked\"/>".$lang['permuser_authorized'];
			$output.= "\n\t\t<input type=\"radio\" name=\"acces-".$row['id']."\" value=\"1\"";
			if ( in_array( $row['id'], $restriction ) )
			{
				$output.= " checked=\"checked\"";
			}
			$output.= "/>".$lang['permuser_forbidden'];
			$output.= $end_line;
			$output.= "\n<tr>";
			$output.= display_cat_manager( $row['id'], $indent."&nbsp;&nbsp;&nbsp;&nbsp;", $subcat_authorized, $level + 1, $restriction );
		}
		return $output;
	}
	
	if ( !$erreur )
	{
		//----------------------------------------------
		// cas 1 : permissions pour un utilisateur donné
		if ( isset( $HTTP_GET_VARS['user_id'] ) )
		{
			echo"
			<table style=\"width:100%;\">
				<tr>
					<th>".$lang['permuser_title']." \"".$page['pseudo']."\"</th>
				</tr>
			</table>";
			$restriction = get_restrictions( $HTTP_GET_VARS['user_id'], $page['status'], false );
			echo"
			<form action=\"".add_session_id_to_url( "./admin.php?page=perm&amp;user_id=".$HTTP_GET_VARS['user_id'] )."\" method=\"post\">
				<div style=\"color:darkblue;margin:10px;text-align:center;\">".$lang['permuser_warning']."</div>
				<table style=\"width:100%;\">";

			echo display_cat_manager( "NULL", "&nbsp;&nbsp;&nbsp;&nbsp;", true, 0, $restriction );

			echo"
					<tr>
						<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang['submit']."\"/></td>
					</tr>
				<table>
			</form>";
		}
		//----------------------------------------------
		// cas 2 : permissions pour une catégorie donnée
		else if ( isset( $HTTP_GET_VARS['cat_id'] ) )
		{
			echo"
			<table style=\"width:100%;\">
				<tr>
					<th>".$lang['permuser_cat_title']."\"".get_cat_display_name( $page['cat_name'], " - ", "font-style:italic;" )."\"</th>
				</tr>
			</table>";
			echo"
			<form action=\"".add_session_id_to_url( "./admin.php?page=perm&amp;cat_id=".$page['cat'] )."\" method=\"post\">
				<table style=\"width:100%;\">";
			
			$result = mysql_query( "select id,pseudo,status from PREFIX_TABLE"."users where pseudo != '".$conf['webmaster']."';" );
			$i = 0;
			while ( $row = mysql_fetch_array( $result ) )
			{
				$restrictions = get_restrictions( $row['id'], $row['status'], false );
				$is_user_allowed = is_user_allowed( $page['cat'], $restrictions );
				$class = "";
				if ( $i%2 == 1 )
				{
					$class = "class=\"row2\"";
				}
				echo"
					<tr>
						<td $class><a href=\"".add_session_id_to_url( "./admin.php?page=perm&amp;user_id=".$row['id'] )."\">";
				echo "<span style=\"color:";
				if ( $is_user_allowed > 0 )
				{
					echo "red";
				}
				else
				{
					echo "green";
				}
				echo "\">".$row['pseudo']."</span></a></td>
						<td $class style=\"text-align:right;\">";
				if ( $is_user_allowed == 2 )
				{
					echo "<a href=\"".add_session_id_to_url( "./admin.php?page=perm&amp;cat_id=".$page['id_uppercat'] )."\"><span style=\"color:red;\">".$lang['permuser_parent_forbidden']."</span></a>";
				}
				else
				{
					echo"
							<input type=\"radio\" name=\"acces-".$row['id']."\" value=\"0\" checked=\"checked\"/>".$lang['permuser_authorized']."
							<input type=\"radio\" name=\"acces-".$row['id']."\" value=\"1\"";
					if ( $is_user_allowed == 1 )
					{
						echo" checked=\"checked\"";
					}
					echo"/>".$lang['permuser_forbidden'];
				}
				echo"
						</td>
					</tr>";
				$i++;
			}
			echo"
					<tr>
						<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang['submit']."\"/></td>
					</tr>
				</table>
			</form>";
		}
	}
?>