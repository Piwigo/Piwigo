<?php
/***************************************************************************
 *             infos_images.php is a part of PhpWebGallery                 *
 *                            -------------------                          *
 *   last update          : Wednesday, July 25, 2002                       *
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
	function check_date_format ( $date )
	{
		// la date arrive à ce format : JJ/MM/AAAA
		// checkdate ( int month, int day, int year)
		$tab_date = explode( "/", $date );
		return checkdate ( $tab_date[1], $tab_date[0], $tab_date[2]);
	}
	
	function date_convert( $date )
	{
		// la date arrive à ce format : JJ/MM/AAAA
		// il faut la transformer en AAAA-MM-JJ
		$tab_date = explode( "/", $date );
		return $tab_date[2]."-".$tab_date[1]."-".$tab_date[0];
	}
	
	function date_convert_back( $date )
	{
		// la date arrive à ce format : AAAA-MM-JJ
		// il faut la transformer en JJ/MM/AAAA
		if ( $date != "" )
		{
			$tab_date = explode( "-", $date );
			return $tab_date[2]."/".$tab_date[1]."/".$tab_date[0];
		}
		else
		{
			return "";
		}
	}
	
	include_once( "./include/isadmin.inc.php" );
	$page['nb_image_page'] = 5;
	check_cat_id( $HTTP_GET_VARS['cat_id'] );
	if ( isset( $page['cat'] ) )
	{
		//------------------------------------------------------------mises à jour
		// 1. options individuelles
		$query = "select id,file ";
		$query.= "from PREFIX_TABLE"."images ";
		$query.= "where cat_id = ".$page['cat']." ";
		$result = mysql_query( $query );
		$i = 1;
		while ( $row = mysql_fetch_array( $result ) )
		{
			$name = "name-".$row['id'];
			$author = "author-".$row['id'];
			$comment = "comment-".$row['id'];
			$date_creation = "date_creation-".$row['id'];
			if ( isset( $HTTP_POST_VARS[$name] ) )
			{
				$query = "update PREFIX_TABLE"."images ";
				if ( $HTTP_POST_VARS[$name] == "" )
				{
					$query.= "set name = NULL ";
				}
				else
				{
					$query.= "set name = '".htmlspecialchars( $HTTP_POST_VARS[$name], ENT_QUOTES )."' ";
				}
				if ( $HTTP_POST_VARS[$author] == "" )
				{
					$query.= ", author = NULL ";
				}
				else
				{
					$query.= ", author = '".htmlspecialchars( $HTTP_POST_VARS[$author], ENT_QUOTES )."' ";
				}
				if ( $HTTP_POST_VARS[$comment] == "" )
				{
					$query.= ", comment = NULL ";
				}
				else
				{
					$query.= ", comment = '".htmlspecialchars( $HTTP_POST_VARS[$comment], ENT_QUOTES )."' ";
				}
				if ( check_date_format( $HTTP_POST_VARS[$date_creation] ) )
				{
					$date = date_convert( $HTTP_POST_VARS[$date_creation] );
					$query.= ", date_creation = '$date' ";
				}
				else if ( $HTTP_POST_VARS[$date_creation] == "" )
				{
					$query.= ", date_creation = NULL ";
				}
				$query.= "where id = '".$row['id']."';";
				mysql_query( $query );
			}
		}
		// 2. options générales
		if ( $HTTP_POST_VARS['use_common_author'] == 1 )
		{
			$query = "update PREFIX_TABLE"."images ";
			if ( $HTTP_POST_VARS['author_cat'] == "" )
			{
				$query.= "set author = NULL ";
			}
			else
			{
				$query.= "set author = '".$HTTP_POST_VARS['author_cat']."' ";
			}
			$query.= "where cat_id = ".$page['cat'].";";
			mysql_query( $query );
		}
		if ( $HTTP_POST_VARS['use_common_date_creation'] == 1 )
		{
			// la date arrive à ce format : JJ/MM/AAAA
			// il faut la transformer en AAAA-MM-JJ
			if ( check_date_format( $HTTP_POST_VARS['date_creation_cat'] ) )
			{
				$date = date_convert( $HTTP_POST_VARS['date_creation_cat'] );
				$query = "update PREFIX_TABLE"."images ";
				if ( $HTTP_POST_VARS['date_creation_cat'] == "" )
				{
					$query.= "set date_creation = NULL ";
				}
				else
				{
					$query.= "set date_creation = '$date' ";
				}
				$query.= "where cat_id = ".$page['cat'].";";
				mysql_query( $query );
			}
			else
			{
				echo $lang['infoimage_err_date'];
			}
		}
		//----------------------------------------------------affichage de la page
		// détection de la page en cours
		if( !isset( $HTTP_GET_VARS['start'] ) || !is_numeric( $HTTP_GET_VARS['start'] ) || ( is_numeric( $HTTP_GET_VARS['start'] ) && $HTTP_GET_VARS['start'] < 0 ) )
		{
			$page['start'] = 0;
		}
		else
		{
			$page['start'] = $HTTP_GET_VARS['start'];
		}
		
		if ( is_numeric( $HTTP_GET_VARS['num'] ) && $HTTP_GET_VARS['num'] >= 0 )
		{
			$page['start'] = floor( $HTTP_GET_VARS['num'] / $page['nb_image_page'] ) * $page['nb_image_page'];
		}
		// retrieving category information
		$result = get_cat_info( $page['cat'] );
		$cat['local_dir'] = $result['local_dir'];
		$cat['dir'] = $result['dir'];
		$cat['name'] = $result['name'];
		$cat['site_id'] = $result['site_id'];
		$cat['nb_images'] = $result['nb_images'];
		
		$url = "./admin.php?page=infos_images&amp;cat_id=".$page['cat'];
		$page['navigation_bar'] = create_navigation_bar( $url, $cat['nb_images'], $page['start'], $page['nb_image_page'], "" );
		echo"
		<form method=\"post\" action=\"".add_session_id_to_url( "./admin.php?page=infos_images&amp;cat_id=".$page['cat']."&amp;start=".$page['start'] )."\">
			<table width=\"100%\">
				<tr>
					<th colspan=\"3\">".$lang['infoimage_general']." \"".get_cat_display_name( $cat['name'], " - ", "font-style:italic;" )."\"</th>
				</tr>
				<tr>
					<td><div style=\"margin-left:50px;\">".$lang['author']."</div></td>
					<td style=\"text-align:center;\"><input type=\"text\" name=\"author_cat\" value=\"\" maxlength=\"255\"/></td>
					<td style=\"text-align:left;\"><input type=\"checkbox\" name=\"use_common_author\" value=\"1\"/>".$lang['infoimage_useforall']."</td>
				</tr>
				<tr>
					<td><div style=\"margin-left:50px;\">".$lang['infoimage_creation_date']." [DD/MM/YYYY]</div></td>
					<td style=\"text-align:center;\"><input type=\"text\" name=\"date_creation_cat\" value=\"\" size=\"12\" maxlength=\"10\"/></td>
					<td style=\"text-align:left;\"><input type=\"checkbox\" name=\"use_common_date_creation\" value=\"1\"/>".$lang['infoimage_useforall']."</td>
				</tr>
			</table>";
		echo"
			<table width=\"100%\">
				<tr>
					<th colspan=\"5\">".$lang['infoimage_detailed']."</th>
				</tr>
				<tr>
					<td colspan=\"5\" align=\"center\">".$page['navigation_bar']."</td>
				</tr>
				<tr>
					<td class=\"row2\" style=\"text-align:center;\">".$lang['thumbnail']."</td>
					<td class=\"row2\" style=\"text-align:center;\">".$lang['infoimage_title']."</td>
					<td class=\"row2\" style=\"text-align:center;\">".$lang['author']."</td>
					<td class=\"row2\" style=\"text-align:center;\">".$lang['infoimage_comment']."</td>
					<td class=\"row2\" style=\"text-align:center;\">".$lang['infoimage_creation_date']."</td>
				</tr>";
		$query = "select id,file,comment,author,tn_ext,name,date_creation";
		$query.= " from PREFIX_TABLE"."images";
		$query.= " where cat_id = ".$page['cat'];
		$query.= $conf['order_by'];
		$query.= " limit ".$page['start'].",".$page['nb_image_page'];
		$query.= ";";
		$result = mysql_query( $query );
		$i = 1;
		while ( $row = mysql_fetch_array( $result ) )
		{
			echo"
				<tr>";
			// création des liens vers la miniature
			$file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
			if ( $cat['site_id'] == 1 )
			{ 
				$lien_thumbnail = "../galleries/".$cat['local_dir']."/";
			}
			else
			{
				$lien_thumbnail = $cat['dir'];
			}
			$lien_thumbnail.= "thumbnail/".$conf['prefixe_thumbnail'].$file.".".$row['tn_ext'];
			// création du "style" de la ligne
			$style = "style=\"text-align:center;\"";
			if ( $i%2 == 0 )
			{
				$style.= " class=\"row2\"";
			}
			echo"
					<td $style><a name=\"".$row['id']."\"><img src=\"$lien_thumbnail\" alt=\"\" class=\"miniature\" title=\"".$row['file']."\"/></td>
					<td $style>$file<br /><input type=\"text\" name=\"name-".$row['id']."\" value=\"".$row['name']."\" maxlength=\"255\"/></td>
					<td $style><input type=\"text\" name=\"author-".$row['id']."\" value=\"".$row['author']."\" maxlength=\"255\"/></td>
					<td $style><textarea name=\"comment-".$row['id']."\" rows=\"3\" cols=\"40\" style=\"overflow:auto\">".$row['comment']."</textarea></td>
					<td $style><input type=\"text\" name=\"date_creation-".$row['id']."\" value=\"".date_convert_back( $row['date_creation'] )."\" maxlength=\"10\" size=\"12\"/></td>";
			echo"
				</tr>";
			$i++;
		}
		echo"
				<tr>
					<td colspan=\"5\" style=\"text-align:center;\"><input type=\"submit\" value=\"".$lang['submit']."\"/></td>
				</tr>
			</table>
		</form>";
	}
?>