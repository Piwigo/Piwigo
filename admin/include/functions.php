<?php
/***************************************************************************
 *                               functions.php                             *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

$tab_ext_create_TN = array ( 'jpg', 'png', 'JPG', 'PNG' );

// is_image returns true if the given $filename (including the path) is a
// picture according to its format and its extension.
// As GD library can only generate pictures from jpeg and png files, if you
// ask if the filename is an image for thumbnail creation (second parameter
// set to true), the only authorized formats are jpeg and png.
function is_image( $filename, $create_thumbnail = false )
{
  global $conf, $tab_ext_create_TN;

  if ( is_file( $filename ) )
  {
    $size = getimagesize( $filename );
    // $size[2] == 1 means GIF
    // $size[2] == 2 means JPG
    // $size[2] == 3 means PNG
    if ( !$create_thumbnail )
    {
      if ( in_array( get_extension( $filename ), $conf['picture_ext'] )
           and ( $size[2] == 1 or $size[2] == 2 or $size[2] == 3 ) )
      {
        return true;
      }
    }
    else
    {
      if ( in_array( get_extension( $filename ), $tab_ext_create_TN )
           and ( $size[2] == 2 or $size[2] == 3 ) )
      {
        return true;
      }
    }
  }
  return false;
}
	
function TN_exists( $dir, $file )
{
  global $conf;

  $filename = get_filename_wo_extension( $file );
  foreach ( $conf['picture_ext'] as $ext ) {
    $test = $dir.'/thumbnail/'.$conf['prefix_thumbnail'].$filename.'.'.$ext;
    if ( is_file ( $test ) )
    {
      return $ext;
    }
  }
  return false;
}	
	
// The function delete_site deletes a site
// and call the function delete_category for each primary category of the site
function delete_site( $id )
{
  // destruction of the categories of the site
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    delete_category( $row['id'] );
  }
		
  // destruction of the site
  $query = 'DELETE FROM '.PREFIX_TABLE.'sites';
  $query.= ' WHERE id = '.$id;
  $query.= ';';
  mysql_query( $query );
}
	
// The function delete_category deletes the category identified by the $id
// It also deletes (in the database) :
//    - all the images of the images (thanks to delete_image, see further)
//    - all the restrictions linked to the category
// The function works recursively.
function delete_category( $id )
{
  // destruction of all the related images
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE cat_id = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    delete_image( $row['id'] );
  }

  // destruction of the access linked to the category
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE cat_id = '.$id;
  $query.= ';';
  mysql_query( $query );
  $query = 'DELETE FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE cat_id = '.$id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the sub-categories
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id_uppercat = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while( $row = mysql_fetch_array( $result ) )
  {
    delete_category( $row['id'] );
  }

  // destruction of the category
  $query = 'DELETE FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id = '.$id;
  $query.= ';';
  mysql_query( $query );
}
	
// The function delete_image deletes the image identified by the $id
// It also deletes (in the database) :
//    - all the comments related to the image
//    - all the favorites associated to the image
function delete_image( $id )
{
  global $count_deleted;
		
  // destruction of the comments on the image
  $query = 'DELETE FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE image_id = '.$id;
  $query.= ';';
  mysql_query( $query );
		
  // destruction of the favorites associated with the picture
  $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE image_id = '.$id;
  $query.= ';';
  mysql_query( $query );
		
  // destruction of the image
  $query = 'DELETE FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE id = '.$id;
  $query.= ';';
  mysql_query( $query );
  $count_deleted++;
}
	
// The delete_user function delete a user identified by the $user_id
// It also deletes :
//     - all the access linked to this user
//     - all the links to any group
//     - all the favorites linked to this user
//     - all sessions linked to this user
function delete_user( $user_id )
{
  // destruction of the access linked to the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the group links for this user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the favorites associated with the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the sessions linked with the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'sessions';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  mysql_query( $query );
		
  // destruction of the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'users';
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  mysql_query( $query );
}

// delete_group deletes a group identified by its $group_id.
// It also deletes :
//     - all the access linked to this group
//     - all the links between this group and any user
function delete_group( $group_id )
{
  // destruction of the access linked to the group
  $query = 'DELETE FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the group links for this group
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the group
  $query = 'DELETE FROM '.PREFIX_TABLE.'groups';
  $query.= ' WHERE id = '.$group_id;
  $query.= ';';
  mysql_query( $query );
}

// The check_favorites function deletes all the favorites of a user if he is
// not allowed to see them (the category or an upper category is restricted
// or invisible)
function check_favorites( $user_id )
{
  $query = 'SELECT status';
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $status = $row['status'];
  // retrieving all the restricted categories for this user
  $restricted_cat = get_all_restrictions( $user_id, $status );
  // retrieving all the favorites for this user and comparing their
  // categories to the restricted categories
  $query = 'SELECT image_id, cat_id';
  $query.= ' FROM '.PREFIX_TABLE.'favorites, '.PREFIX_TABLE.'images';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ' AND id = image_id';
  $query.= ';';
  $result = mysql_query ( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( in_array( $row['cat_id'], $restricted_cat ) )
    {
      $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
      $query.= ' WHERE image_id = '.$row['image_id'];
      $query.= ' AND user_id = '.$user_id;
      $query.= ';';
      mysql_query( $query );
    }
  }
}
?>