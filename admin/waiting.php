<?php
/***************************************************************************
 *                     waiting.php is a part of PhpWebGallery              *
 *                            -------------------                          *
 *   last update          : Monday, October 28, 2002                         *
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
	//-------------------------------------------------------------- mise à jour
	if ( isset( $HTTP_POST_VARS['submit'] ) )
	{
		$query = "select id,cat_id,file,tn_ext";
		$query.= " from PREFIX_TABLE"."waiting";
		$query.= ";";
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			$key = "validate-".$row['id'];
			if ( isset( $HTTP_POST_VARS[$key] ) )
			{
				$query = "delete from PREFIX_TABLE"."waiting";
				$query.= " where id = ".$row['id'];
				$query.= ";";
				mysql_query( $query );
				if ( $HTTP_POST_VARS[$key] == "false" )
				{
					// we have to delete the picture and the thumbnail if it exists
					$cat = get_cat_info( $row['cat_id'] );
					unlink( ".".$cat['dir'].$row['file'] );
					if ( $row['tn_ext'] != "" )
					{
						$file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
						unlink( ".".$cat['dir']."thumbnail/".$conf['prefixe_thumbnail'].$file.".".$row['tn_ext'] );
					}
				}
			}
		}
	}
	//--------------------------------------------------------------- formulaire
	$cat_names = array();
	echo "
		<form action=\"".add_session_id_to_url( "./admin.php?page=waiting" )."\" method=\"post\">
			<table style=\"width:100%;\">
				<tr>
					<th style=\"width:20%;\">".$lang['category']."</th>
					<th style=\"width:20%;\">".$lang['date']."</th>
					<th style=\"width:20%;\">".$lang['file']."</th>
					<th style=\"width:20%;\">".$lang['thumbnail']."</th>
					<th style=\"width:20%;\">".$lang['author']."</th>
					<th style=\"width:1px;\">&nbsp;</th>
				</tr>";
	$query = "select id,cat_id,file,username,mail_address,date,tn_ext";
	$query.= " from PREFIX_TABLE"."waiting";
	$query.= " order by cat_id";
	$query.= ";";
	$result = mysql_query( $query );
	$i = 0;
	while ( $row = mysql_fetch_array( $result ) )
	{
		$style = "";
		if ( $i%2 == 0 )
		{
			$style = "class=\"row2\"";
		}
		if ( !isset( $cat_names[$row['cat_id']] ) )
		{
			$cat = get_cat_info( $row['cat_id'] );
			$cat_names[$row['cat_id']] = array();
			$cat_names[$row['cat_id']]['dir'] = ".".$cat['dir'];
			$cat_names[$row['cat_id']]['display_name'] = get_cat_display_name( $cat['name'], " - ", "font-style:italic;" );
		}
		echo "
				<tr>
					<td $style style=\"white-space:nowrap;\">".$cat_names[$row['cat_id']]['display_name']."</td>
					<td $style style=\"white-space:nowrap;\">".$lang['day'][date( "w", $row['date'] )].date( " j ", $row['date'] ).$lang['month'][date( "n", $row['date'] )].date( " Y G:i", $row['date'] )."</td>
					<td $style style=\"white-space:nowrap;\">
						<a target=\"_blank\" href=\"".$cat_names[$row['cat_id']]['dir'].$row['file']."\">".$row['file']."</td>
					</td>
					<td $style style=\"white-space:nowrap;\">";
		if ( $row['tn_ext'] != "" )
		{
			$file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
			echo "<a target=\"_blank\" href=\"".$cat_names[$row['cat_id']]['dir']."thumbnail/".$conf['prefixe_thumbnail'].$file.".".$row['tn_ext']."\">".$conf['prefixe_thumbnail'].$file.".".$row['tn_ext'];
		}
		else
		{
			echo "&nbsp;";
		}
		echo "
					</td>
					<td $style style=\"white-space:nowrap;\">
						<a href=\"mailto:".$row['mail_address']."\">".$row['username']."</a>
					</td>
					<td $style style=\"white-space:nowrap;\">
						<input type=\"radio\" name=\"validate-".$row['id']."\" value=\"true\" />".$lang['submit']."
						<input type=\"radio\" name=\"validate-".$row['id']."\" value=\"false\" />".$lang['delete']."
					</td>
				</tr>";
		$i++;
	}
	echo "
				<tr>
					<td colspan=\"5\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang['submit']."\" style=\"margin:5px;\" /></td>
				</tr>";
	echo "
			</table>
		</form>";
?>