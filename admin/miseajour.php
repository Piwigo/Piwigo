<?
/***************************************************************************
 *                  miseajour.php is a part of PhpWebGallery               *
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
	
	function insert_local_category( $cat_id )
	{
		global $prefixeTable,$conf,$page,$HTTP_GET_VARS;
		
		$site_id = 1;
		
		// 0. retrieving informations on the category to display
		$cat_directory = "../galleries";
		
		if ( is_numeric( $cat_id ) )
		{
			$result = get_cat_info( $cat_id );
			$cat_directory.= "/".$result['local_dir'];
			// 1. display the category name to update
			echo "
				<img src=\"".$conf['lien_puce']."\" alt=\"&gt;\" /><span style=\"font-weight:bold;\">".$result['name'][0]."</span> [ dir : ".$result['last_dir']." ]
				<div class=\"retrait\">";
			
			// 2. we search pictures of the category only if the update is for all or a cat_id is specified
			if ( isset( $page['cat'] ) || $HTTP_GET_VARS['update'] == 'all' )
			{
				insert_local_image( $cat_directory, $cat_id );
				update_cat_info( $cat_id );
			}
		}
		
		// 3. we have to remove the categories of the database not present anymore
		$query = "select id from $prefixeTable"."categories";
		$query.= " where site_id = $site_id";
		if ( !is_numeric( $cat_id ) )
		{
			$query.= " and id_uppercat is NULL;";
		}
		else
		{
			$query.= " and id_uppercat = $cat_id;";
		}
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			// retrieving the directory
			$rep = "../galleries";
			$resultat = get_cat_info( $row['id'] );
			$rep.= "/".$resultat['local_dir'];
			
			// is the directory present ?
			if ( !is_dir( $rep ) )
			{
				delete_category( $row['id'] );
			}
		}
		
		// 4. retrieving the sub-directories
		$sub_rep = array();
		$i = 0;
		$dirs = "";
		if ( $opendir = opendir ( $cat_directory ) )
		{
			while ( $file = readdir ( $opendir ) )
			{
				if ( $file != "." && $file != ".." && is_dir ( $cat_directory."/".$file ) && $file != "thumbnail" )
				{
					$sub_rep[$i++] = $file;
				}
			}
		}
		
		for ( $i = 0; $i < sizeof( $sub_rep ); $i++ )
		{
			// 5. is the category already existing ? we create a subcat if not existing
			$category_id = "";
			$query = "select id from $prefixeTable"."categories";
			$query.= " where site_id = $site_id";
			$query.= " and dir = '".$sub_rep[$i]."'";
			if ( !is_numeric( $cat_id ) )
			{
				$query.= " and id_uppercat is NULL;";
			}
			else
			{
				$query.= " and id_uppercat = $cat_id;";
			}
			$result = mysql_query( $query );
			if ( mysql_num_rows( $result ) == 0 )
			{
				// we have to create the category
				$query = "insert into $prefixeTable"."categories (dir,site_id,id_uppercat) values ('".$sub_rep[$i]."','$site_id'";
				if ( !is_numeric( $cat_id ) )
				{
					$query.= ",NULL";
				}
				else
				{
					$query.= ",'$cat_id'";
				}
				$query.= ");";
				mysql_query( $query );
				$category_id = mysql_insert_id();
			}
			else
			{
				// we get the already registered id
				$row = mysql_fetch_array( $result );
				$category_id = $row['id'];
			}
			// 6. recursive call
			insert_local_category( $category_id );
		}
		
		if ( is_numeric( $cat_id ) )
		{
			echo "
				</div>";
		}
	}
	
	function insert_local_image( $rep, $category_id )
	{
		global $prefixeTable,$lang,$conf,$count_new;
		
		// we have to delete all the images from the database that :
		//     - are not in the directory anymore
		//     - don't have the associated thumbnail available anymore
		$query = "select id,file,tn_ext from $prefixeTable"."images";
		$query.= " where cat_id = $category_id;";
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			$lien_image = $rep."/".$row['file'];
			$lien_thumbnail = $rep."/"."thumbnail/".$conf['prefixe_thumbnail'].substr( $row['file'], 0, strrpos( $row['file'], "." ) ).".".$row['tn_ext'];
		
			if ( !is_file ( $lien_image ) || !is_file ( $lien_thumbnail ) )
			{
				if ( !is_file ( $lien_image ) )
				{
					echo $row['file']." <span style=\"font-weight:bold;\">".$lang['update_disappeared']."</span><br />";
				}
				if ( !is_file ( $lien_thumbnail ) )
				{
					echo $row['file']." : <span style=\"font-weight:bold;\">".$lang['update_disappeared_tn']."</span><br />";
				}
				// suppression de la base :
				delete_image( $row['id'] );
			}
		}
		
		// searching the new images in the directory
		$pictures = array();		
		$i = 0;
		$tn_ext = "";
		$root = "";
		if ( $opendir = opendir ( $rep ) )
		{
			while ( $file = readdir ( $opendir ) )
			{
				if ( is_file( $rep."/".$file ) && is_image( $rep."/".$file ) )
				{
					// is the picture waiting for validation by an administrator ?
					$query = "select id from $prefixeTable"."waiting";
					$query.= " where cat_id = $category_id";
					$query.= " and file = '$file';";
					$result = mysql_query( $query );
					if ( mysql_num_rows( $result ) == 0 )
					{
						if ( $tn_ext = TN_exist( $rep, $file ) )
						{
							// is the picture already in the database ?
							$query = "select id from $prefixeTable"."images";
							$query.= " where cat_id = $category_id";
							$query.= " and file = '$file';";
							$result = mysql_query( $query );
							if ( mysql_num_rows( $result ) == 0 )
							{
								$pictures[$i] = array();
								$pictures[$i]['file'] = $file;
								$pictures[$i]['tn_ext'] = $tn_ext;
								$pictures[$i]['date'] = date( "Y-m-d", filemtime ( $rep."/".$file ) );
								$pictures[$i]['filesize'] = floor ( filesize( $rep."/".$file ) / 1024 );
								$image_size = @getimagesize( $rep."/".$file );
								$pictures[$i]['width'] = $image_size[0];
								$pictures[$i]['height'] = $image_size[1];
								$i++;
							}
						}
						else
						{
							echo "<span style=\"color:red;\">".$lang['update_missing_tn']." : $file (<span style=\"font-weight:bold;\">".$conf['prefixe_thumbnail'].substr( $file, 0, strrpos( $file, "." ) ).".XXX</span>, XXX = gif, png or jpg)</span><br />";
						}
					}
				}
			}
		}
		// inserting the pictures found in the directory
		$root.= "\n".$indent."<root>";
		if ( sizeof( $pictures ) > 0 )
		{
			for( $i = 0; $i < sizeof( $pictures ); $i++ )
			{
				$query = "insert into $prefixeTable"."images (file,cat_id,date_available,tn_ext,filesize,width,height) values ('".$pictures[$i]['file']."','".$category_id."','".$pictures[$i]['date']."','".$pictures[$i]['tn_ext']."','".$pictures[$i]['filesize']."','".$pictures[$i]['width']."','".$pictures[$i]['height']."');";
				echo"
							".$pictures[$i]['file']." <span style=\"font-weight:bold;\">".$lang['update_research_added']."</span> (".$lang['update_research_tn_ext']." ".$pictures[$i]['tn_ext'].")<br />";
				$count_new++;
				mysql_query( $query );
			}
		}
	}
	
	// The function "update_cat_info" updates the information about the last online image
	// and the number of images in the category
	function update_cat_info( $category_id )
	{
		global $prefixeTable;
		
		$query = "select date_available from $prefixeTable"."images";
		$query.= " where cat_id = $category_id";
		$query.= " order by date_available desc limit 0,1;";
		$result = mysql_query( $query );
		$row = mysql_fetch_array( $result );
		$date_last = $row['date_available'];
		
		$query = "select count(*) as nb_images from $prefixeTable"."images";
		$query.= " where cat_id = $category_id";
		$result = mysql_query( $query );
		$row = mysql_fetch_array( $result );
		$nb_images = $row['nb_images'];
		
		$query = "update $prefixeTable"."categories";
		$query.= " set date_dernier = '$date_last'";
		$query.= ", nb_images = $nb_images";
		$query.= " where id = $category_id;";
		mysql_query( $query );
	}
	
	function getContent( $element, $node )
	{		
		$content = str_replace( "<".$node.">", "", $element );
		$content = str_replace( "</".$node.">", "", $content );
		return $content;
	}
	
	function getChild( $document, $node )
	{
		preg_match("/\<".$node.">.*\<\/".$node."\>/U", $document, $retour);
		return $retour[0];
	}
	
	function getChildren( $document, $node )
	{
		preg_match_all("/\<".$node.">.*\<\/".$node."\>/U", $document, $retour);
		return $retour[0];
	}
	
	function remote_images()
	{
		global $conf, $prefixeTable, $lang;
		
		// 1.is there a file listing.xml ?
		$filename = "listing.xml";
		$xml_content = "";
		if ( $fp = @fopen ( $filename, "r" ) )
		{
			while ( !feof ( $fp ) )
			{
				$xml_content .= fgets ( $fp, 1024 );
			}
			@fclose( $file );
			$xml_content = str_replace("\n","",$xml_content);
			$xml_content = str_replace("\t","",$xml_content);
		}
		else
		{
			return false;
		}
		$url = getContent( getChild( $xml_content, "url" ), "url" );
		echo "<span style=\"font-weight:bold;color:navy;\">$url</span><br /><br />";
		
		// 2. is the site already existing ?
		$site_id = "";
		$result = mysql_query( "select id from $prefixeTable"."sites where galleries_url = '$url';" );
		if ( mysql_num_rows($result ) == 0 )
		{
			// we have to register this site in the database
			mysql_query( "insert into $prefixeTable"."sites (galleries_url) values ('$url');" );
			$site_id = mysql_insert_id();
		}
		else
		{
			// we get the already registered id
			$row = mysql_fetch_array( $result );
			$site_id = $row['id'];
		}
		
		// 3. available dirs in the file
		insert_remote_category( $xml_content, $site_id, "NULL", 0 );
	}
	
	// insert_remote_category search the "dir" node of the xml_dir given
	// and insert the contained categories if the are not in the database yet.
	// The function also delete the categories that are in the database
	// and not in the xml_file
	function insert_remote_category( $xml_dir, $site_id, $id_uppercat, $level )
	{
		global $prefixeTable,$conf;;
		
		$categories = array();
		$list_dirs = getChildren( $xml_dir, "dir".$level );
		for ( $i = 0; $i < sizeof( $list_dirs ); $i++ )
		{
			// is the category already existing ?
			$category_id = "";
			$name = getContent( getChild( $list_dirs[$i], "name" ), "name" );
			$categories[$i] = $name;
			echo "
				<img src=\"".$conf['lien_puce']."\"><span style=\"font-weight:bold;\">$name</span>
				<div class=\"retrait\">";
			$query = "select id from $prefixeTable"."categories";
			$query.= " where site_id = '$site_id'";
			$query.= " and dir = '$name'";
			if ( $id_uppercat == "NULL" )
			{
				$query.= " and id_uppercat is NULL;";
			}
			else
			{
				$query.= " and id_uppercat = '$id_uppercat';";
			}
			//echo "<br />".$query;
			$result = mysql_query( $query );
			if ( mysql_num_rows( $result ) == 0 )
			{
				// we have to create the category
				$query = "insert into $prefixeTable"."categories (dir,site_id,id_uppercat) values ('$name','$site_id'";
				if ( $id_uppercat == "NULL" )
				{
					$query.= ",NULL";
				}
				else
				{
					$query.= ",'$id_uppercat'";
				}
				$query.= ");";
				//echo "<br />".$query;
				mysql_query( $query );
				$category_id = mysql_insert_id();
			}
			else
			{
				// we get the already registered id
				$row = mysql_fetch_array( $result );
				$category_id = $row['id'];
			}
			insert_remote_image( $list_dirs[$i], $category_id );
			update_cat_info( $category_id );
			insert_remote_category( $list_dirs[$i], $site_id, $category_id, $level + 1 );
			echo "
				</div>";
		}
		// we have to remove the categories of the database not present in the xml file
		// (ie deleted from the picture storage server)
		$query = "select dir,id from $prefixeTable"."categories";
		$query.= " where site_id = '$site_id'";
		if ( $id_uppercat == "NULL" )
		{
			$query.= " and id_uppercat is NULL;";
		}
		else
		{
			$query.= " and id_uppercat = '$id_uppercat';";
		}
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			// is the category in the xml file ?
			if ( !in_array( $row['dir'], $categories ) )
			{
				delete_category( $row['id'] );
			}
		}
	}
	
	// insert_remote_image search the "root" node of the xml_dir given
	// and insert the contained pictures if the are not in the database yet
	function insert_remote_image( $xml_dir, $category_id )
	{
		global $prefixeTable,$count_new,$lang;
		
		$root = getChild( $xml_dir, "root" );
		$pictures = array();
		$xml_pictures = getChildren( $root, "picture" );
		for ( $j = 0; $j < sizeof( $xml_pictures ); $j++ )
		{
			//<picture>
			//	<file>albatros.jpg</file>
			//	<tn_ext>png</tn_ext>
			//	<date>2002-04-14</date>
			//	<filesize>35</filesize>
			//	<width>640</width>
			//	<height>480</height>
			//</picture>
			$file = getContent( getChild( $xml_pictures[$j], "file" ), "file" );
			$tn_ext = getContent( getChild( $xml_pictures[$j], "tn_ext" ), "tn_ext" );
			$date = getContent( getChild( $xml_pictures[$j], "date" ), "date" );
			$filesize = getContent( getChild( $xml_pictures[$j], "filesize" ), "filesize" );
			$width = getContent( getChild( $xml_pictures[$j], "width" ), "width" );
			$height = getContent( getChild( $xml_pictures[$j], "height" ), "height" );
			
			$pictures[$j] = $file;
			
			// is the picture already existing in the database ?
			$query = "select id,tn_ext from $prefixeTable"."images where cat_id = '$category_id' and file = '$file';";
			$result = mysql_query( $query );
			$query = "";
			if ( mysql_num_rows( $result ) == 0 )
			{
				$query = "insert into $prefixeTable"."images (file,cat_id,date_available,tn_ext,filesize,width,height) values ('$file','$category_id','$date','$tn_ext','$filesize','$width','$height');";
				echo"
							$file <span style=\"font-weight:bold;\">".$lang['update_research_added']."</span> (".$lang['update_research_tn_ext']." $tn_ext)<br />";
				$count_new++;
			}
			else
			{
				// is the tn_ext the same in the xml file and in the database ?
				$row = mysql_fetch_array( $result );
				if ( $row['tn_ext'] != $tn_ext )
				{
					$query = "update $prefixeTable"."images set tn_ext = '$tn_ext' where cat_id = '$category_id' and file = '$file';";
				}
			}
			// execution of the query
			if ( $query != "" )
			{
				mysql_query( $query );
			}
		}
		// we have to remove the pictures of the database not present in the xml file
		// (ie deleted from the picture storage server)
		$query = "select id,file from $prefixeTable"."images where cat_id = '$category_id';";
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			// is the file in the xml file ?
			if ( !in_array( $row['file'], $pictures ) )
			{
				delete_image( $row['id'] );
			}
		}
	}
	//------------------------------------------------------------------------------
	echo "<table style=\"width:100%;\">";	
	//------------------------------------------------------------------------------
	// Display choice if "update" var is not specified
	check_cat_id( $HTTP_GET_VARS['update'] );
	if ( !isset( $HTTP_GET_VARS['update'] ) && !( isset( $page['cat'] ) || $HTTP_GET_VARS['update'] == 'cats' || $HTTP_GET_VARS['update'] == 'all' ) )
	{
		echo"
	<tr><th>".$lang['update_default_title']."</th></tr>
	<tr>
		<td>
			<div class=\"retrait\">
				<img src=\"".$conf['lien_puce']."\" alt=\"&gt;\" /><a href=\"".add_session_id_to_url( "./admin.php?page=miseajour&amp;update=cats" )."\">".$lang['update_only_cat']."</a>
				<br /><img src=\"".$conf['lien_puce']."\" alt=\"&gt;\" /><a href=\"".add_session_id_to_url( "./admin.php?page=miseajour&amp;update=all" )."\">".$lang['update_all']."</a>
			</div>
		</td>
	</tr>";
	}
	//------------------------------------------------------------------------------
	// Recherche des nouvelles images dans les repertoires
	else
	{
		$count_new = 0;
		$count_deleted = 0;
		echo"
	<tr><th>".$lang['update_part_research']."</th></tr>
	<tr>
		<td>
			<div class=\"retrait\">";
		if ( isset( $page['cat'] ) )
		{
			insert_local_category( $page['cat'] );
		}
		else
		{
			insert_local_category( "NULL" );
		}
		echo "<br /><span style=\"color:blue;\">$count_new ".$lang['update_research_conclusion']."</span>";
		echo "<br /><span style=\"color:red;\">$count_deleted ".$lang['update_deletion_conclusion']."</span>";
		echo "
			</div>
		</td>
	</tr>";
	}
	//------------------------------------------------------------------------------
	// Searching new pictures in the file listing.xml from a remote storage server
	if ( @is_file( "./listing.xml" ) )
	{
		$count_new = 0;
		$count_deleted = 0;
		echo"
		<tr><th>Site distant</th></tr>
		<tr>
			<td>
				<div class=\"retrait\">";
		remote_images();
		echo "<br /><span style=\"color:blue;\">$count_new ".$lang['update_research_conclusion']."</span>";
		echo "<br /><span style=\"color:red;\">$count_deleted ".$lang['update_deletion_conclusion']."</span>";
		echo "
					</div>
			</td>
		</tr>";
	}
	//------------------------------------------------------------------------------
	echo "</table>";
?>