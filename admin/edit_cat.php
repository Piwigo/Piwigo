<?php
/***************************************************************************
 *                edit_cat.php is a part of PhpWebGallery                  *
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
	
	if ( $HTTP_GET_VARS['valider'] == 1 )
	{
		$query = "update PREFIX_TABLE"."categories ";
		if ( $HTTP_POST_VARS['name'] == "" )
		{
			$query.= "set name = NULL, ";
		}
		else
		{
			$query.= "set name = '".htmlspecialchars( $HTTP_POST_VARS['name'], ENT_QUOTES)."', ";
		}
		if ( $HTTP_POST_VARS['comment'] == "" )
		{
			$query.= "comment = NULL, ";
		}
		else
		{
			$query.= "comment = '".htmlspecialchars( $HTTP_POST_VARS['comment'], ENT_QUOTES )."', ";
		}
		$query.= "status = '".$HTTP_POST_VARS['status']."' ";
		$query.= "where id = '".$HTTP_GET_VARS['cat']."';";
		mysql_query( $query );
		
		$result = mysql_query( "select id from PREFIX_TABLE"."users where pseudo != '".$conf['webmaster']."';" );
		while ( $row = mysql_fetch_array ( $result ) )
		{
			check_favorites( $row['id'] );
		}
		
		echo"<div style=\"color:red;text-align:center;\">".$lang['editcat_confirm']." [ <a href=\"".add_session_id_to_url( "./admin.php?page=cat" )."\">".$lang['editcat_back']."</a> ]</div>";		
	}
	
	echo "
	<form action=\"".add_session_id_to_url( "./admin.php?page=edit_cat&amp;cat=".$HTTP_GET_VARS['cat']."&amp;valider=1" )."\" method=\"post\">
		<table style=\"width:100%;\">";
	$query = "select a.id,name,dir,status,comment,id_uppercat,site_id,galleries_url";
	$query.= " from PREFIX_TABLE"."categories as a, PREFIX_TABLE"."sites as b";
	$query.= " where a.id = ".$HTTP_GET_VARS['cat'];
	$query.= " and a.site_id = b.id;";
	$row = mysql_fetch_array( mysql_query( $query ) );
	$result = get_cat_info( $row['id'] );
	$array_cat_names = $result['name'];
	echo "
			<tr>
				<th colspan=\"2\">".$lang['editcat_title1']." ".$lang['category']." \"".get_cat_display_name( $array_cat_names, " - ", "font-style:italic;" )."\" [ dir : ".$row['dir']." ]</th>
			</tr>";
	if ( $row['site_id'] != 1 )
	{
		echo "
			<tr>
				<td style=\"width:20%;\">Server</td>
				<td class=\"row2\">".$row['galleries_url']."</td>
			</tr>";
	}
	echo "
			<tr>
				<td style=\"width:20%;\">".$lang['editcat_name']."</td>
				<td class=\"row2\"><input type=\"text\" name=\"name\" value=\"".$row['name']."\" maxlength=\"255\"/></td>
			</tr>
			<tr>
				<td style=\"width:20%;\">".$lang['editcat_comment']."</td>
				<td class=\"row2\"><textarea name=\"comment\" rows=\"5\" cols=\"50\" style=\"overflow:auto\">".$row['comment']."</textarea></td>
			</tr>
			<tr>
				<td style=\"width:20%;\">".$lang['editcat_status']."</td>
				<td class=\"row2\">
					<select name=\"status\">";
	// on récupère toutes les status possibles dans la base 
	// par l'intermédiaire de la fonction get_enums trouvable 
	// dans le fichier config.php
	$option = get_enums( PREFIX_TABLE."categories", "status" );
	for ( $i = 0; $i < sizeof( $option ); $i++ )
	{
		if ( $option[$i] == $row['status'] )
		{
			echo"
						<option selected>$option[$i]</option>";
		}
		else
		{
			echo"
						<option>$option[$i]</option>";
		}
	}
	echo"
					</select>
					".$lang['editcat_status_info']."
				</td>
			</tr>
			<tr>
				<td colspan=\"2\">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".$lang['submit']."\"/></td>
			</tr>
		</table>
	</form>";
?>