<?php
/***************************************************************************
 *                    functions.php is a part of PhpWebGallery             *
 *                            -------------------                          *
 *   last update          : Tuesday, September 26, 2002                    *
 *   email                : pierrick@z0rglub.com                           *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
	
	$tab_ext = array ( 'jpg', 'gif', 'JPG','GIF','png','PNG' );
	$tab_ext_create_TN = array ( 'jpg', 'JPG','png','PNG' );
	
	function get_extension( $filename )
	{
		return substr ( strrchr($filename,"."), 1, strlen ( $filename ) );
	}
	
	function is_image( $filename, $create_thumbnail = false )
	{
		global $tab_ext, $tab_ext_create_TN;
		$is_image = false;
		if ( is_file ( $filename ) )
		{
			$size = getimagesize( $filename );
			// $size[2] == 1 means GIF
			// $size[2] == 2 means JPG
			// $size[2] == 3 means PNG
			if ( !$create_thumbnail )
			{
				if ( in_array ( get_extension( $filename ), $tab_ext ) && ( $size[2] == 1 || $size[2] == 2 || $size[2] == 3 ) )
				{
					$is_image = true;
				}
			}
			else
			{
				if ( in_array ( get_extension( $filename ), $tab_ext_create_TN ) && ( $size[2] == 2 || $size[2] == 3 ) )
				{
					$is_image = true;
				}
			}
		}
		return $is_image;
	}
	
	function TN_exist ( $dir, $file )
	{
		global $tab_ext, $conf;
		$titre = substr ( $file, 0, strrpos ( $file, ".") );
		for ( $i = 0; $i < sizeof ( $tab_ext ); $i++ )
		{
			$test = $dir."/thumbnail/".$conf['prefixe_thumbnail'].$titre.".".$tab_ext[$i];
			if ( is_file ( $test ) )
			{
				return $tab_ext[$i];
			}
		}
		return false;
	}	
	
	// The function delete_site deletes a site
	// and call the function delete_category for each primary category of the site
	function delete_site( $id )
	{
		global $prefixeTable;
		
		// destruction of the categories of the site
		$query = "select id from $prefixeTable"."categories where site_id = $id;";
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			delete_category( $row['id'] );
		}
		
		// destruction of the site
		$query = "delete from $prefixeTable"."sites where id = $id;";
		mysql_query( $query );
	}
	
	// The function delete_category deletes the category identified by the $id
	// It also deletes (in the database) :
	//    - all the images of the images (thanks to delete_image, see further)
	//    - all the restrictions linked to the category
	// The function works recursively.
	function delete_category( $id )
	{
		global $prefixeTable;
		
		// destruction of all the related images
		$query = "select id from $prefixeTable"."images where cat_id = '".$id."';";
		$result = mysql_query( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			delete_image( $row['id'] );
		}
		
		// destruction of the restrictions linked to the category
		$query = "delete from $prefixeTable"."restrictions where cat_id = '".$id."';";
		mysql_query( $query );
		
		// destruction of the sub-categories
		$query = "select id from $prefixeTable"."categories where id_uppercat = '$id';";
		$result = mysql_query( $query );
		while( $row = mysql_fetch_array( $result ) )
		{
			delete_category( $row['id'] );
		}
		
		// destruction of the category
		$query = "delete from $prefixeTable"."categories where id = '$id';";
		mysql_query( $query );
	}
	
	// The function delete_image deletes the image identified by the $id
	// It also deletes (in the database) :
	//    - all the comments related to the image
	//    - all the favorites associated to the image
	function delete_image( $id )
	{
		global $prefixeTable,$count_deleted;
		
		// destruction of the comments on the image
		$query = "delete from $prefixeTable"."comments where image_id = $id;";
		mysql_query( $query );
		
		// destruction of the favorites associated with the picture
		$query = "delete from $prefixeTable"."favorites where image_id = $id;";
		mysql_query( $query );
		
		// destruction of the image
		$query = "delete from $prefixeTable"."images where id = $id;";
		mysql_query( $query );
		$count_deleted++;
	}
	
	// The delete_user function delete a user identified by the $user_id
	// It also deletes :
	//     - all the restrictions linked to this user
	//     - all the favorites linked to this user
	function delete_user( $user_id )
	{
		global $prefixeTable;
		
		// destruction of the restrictions linked to the user
		$query = "delete from $prefixeTable"."restrictions where user_id = $user_id;";
		mysql_query( $query );
		
		// destruction of the favorites associated with the user
		$query = "delete from $prefixeTable"."favorites where user_id = $user_id;";
		mysql_query( $query );
		
		// destruction of the user
		$query = "delete from $prefixeTable"."users where id = $user_id;";
		mysql_query( $query );
	}
	
	// The check_favorites function deletes all the favorites of a user if he is not allowed to see them
	// (the category or an upper category is restricted or invisible)
	function check_favorites( $user_id )
	{
		global $prefixeTable;
		
		$row = mysql_fetch_array( mysql_query( "select status from $prefixeTable"."users where id = $user_id;" ) );
		$status = $row['status'];
		// retrieving all the restricted categories for this user
		$restricted_cat = get_all_restrictions( $user_id, $status );
		// retrieving all the favorites for this user and comparing their categories to the restricted categories
		$query = "select image_id, cat_id";
		$query.= " from $prefixeTable"."favorites, $prefixeTable"."images";
		$query.= " where user_id = $user_id";
		$query.= " and id = image_id";
		$query.= ";";
		$result = mysql_query ( $query );
		while ( $row = mysql_fetch_array( $result ) )
		{
			if ( in_array( $row['cat_id'], $restricted_cat ) )
			{
				$query = "delete from $prefixeTable"."favorites";
				$query.= " where image_id = ".$row['image_id'];
				$query.= " and user_id = $user_id";
				$query.= ";";
				mysql_query( $query );
			}
		}
	}
?>