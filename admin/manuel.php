<?
/***************************************************************************
 *               manuel.php is a part of PhpWebGallery                     *
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
	
	echo"
		<table style=\"width:100%;\">
			<tr>
				<th>".$lang['help_images_title']."</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<div style=\"text-align:center;margin:auto;margin-bottom:10px;\"><img src=\"".$conf['repertoire_image']."admin.png\" style=\"border:1px solid black;\" alt=\"\"/></div>
					".$lang['help_images_intro']." :
					<ul style=\"margin-right:10px;\">";
	for ( $i = 0; $i < sizeof( $lang['help_images'] ); $i++ )
	{
		echo"
						<li>".$lang['help_images'][$i]."</li>";
	}
	echo"
					</ul>";
	echo"
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>".$lang['help_thumbnails_title']."</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<ul style=\"margin-right:10px;\">";
	for ( $i = 0; $i < sizeof( $lang['help_thumbnails'] ); $i++ )
	{
		echo"
						<li>".$lang['help_thumbnails'][$i]."</li>";
	}
	echo"
					</ul>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>".$lang['help_database_title']."</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<ul style=\"margin-right:10px;\">";
	for ( $i = 0; $i < sizeof( $lang['help_database'] ); $i++ )
	{
		echo"
						<li>".$lang['help_database'][$i]."</li>";
	}
	echo"
					</ul>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>".$lang['help_remote_title']."</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<ul style=\"margin-right:10px;\">";
	for ( $i = 0; $i < sizeof( $lang['help_remote'] ); $i++ )
	{
		echo"
						<li>".$lang['help_remote'][$i]."</li>";
	}
	echo"
					</ul>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>".$lang['help_upload_title']."</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<ul style=\"margin-right:10px;\">";
	for ( $i = 0; $i < sizeof( $lang['help_upload'] ); $i++ )
	{
		echo"
						<li>".$lang['help_upload'][$i]."</li>";
	}
	echo"
					</ul>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<th>".$lang['help_infos_title']."</th>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<ul style=\"margin-right:10px;\">";
	for ( $i = 0; $i < sizeof( $lang['help_infos'] ); $i++ )
	{
		echo"
						<li>".$lang['help_infos'][$i]."</li>";
	}
	echo"
					</ul>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>";
?>