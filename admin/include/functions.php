<?php
/***************************************************************************
 *                               functions.php                             *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
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
//    - all the links between images and this category
//    - all the restrictions linked to the category
// The function works recursively.
function delete_category( $id )
{
  // destruction of all the related images
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    delete_image( $row['id'] );
  }

  // destruction of the links between images and this category
  $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
  $query.= ' WHERE category_id = '.$id;
  $query.= ';';
  mysql_query( $query );

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
//    - all the links between categories and this image
//    - all the favorites associated to the image
function delete_image( $id )
{
  global $count_deleted;
		
  // destruction of the comments on the image
  $query = 'DELETE FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE image_id = '.$id;
  $query.= ';';
  mysql_query( $query );

  // destruction of the links between images and this category
  $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
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
  $query = 'SELECT image_id';
  $query.= ' FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  $result = mysql_query ( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // for each picture, we have to check all the categories it belongs
    // to. Indeed if a picture belongs to category_1 and category_2 and that
    // category_2 is not restricted to the user, he can have the picture as
    // favorite.
    $query = 'SELECT DISTINCT(category_id) as category_id';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE image_id = '.$row['image_id'];
    $query.= ';';
    $picture_result = mysql_query( $query );
    $picture_cat = array();
    while ( $picture_row = mysql_fetch_array( $picture_result ) )
    {
      array_push( $picture_cat, $picture_row['category_id'] );
    }
    if ( count( array_diff( $picture_cat, $restricted_cat ) ) > 0 )
    {
      $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
      $query.= ' WHERE image_id = '.$row['image_id'];
      $query.= ' AND user_id = '.$user_id;
      $query.= ';';
      mysql_query( $query );
    }
  }
}

// update_category updates calculated informations about a category :
// date_last and nb_images. It also verifies that the representative picture
// is really linked to the category.
function update_category( $id = 'all' )
{
  if ( $id == 'all' )
  {
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      // recursive call
      update_category( $row['id'] );
    }
  }
  else if ( is_numeric( $id ) )
  {
    // updating the number of pictures
    $query = 'SELECT COUNT(*) as nb_images';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE category_id = '.$id;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    $query = 'UPDATE '.PREFIX_TABLE.'categories';
    $query.= ' SET nb_images = '.$row['nb_images'];
    $query.= ' WHERE id = '.$id;
    $query.= ';';
    mysql_query( $query );
    // updating the date_last
    $query = 'SELECT date_available';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' LEFT JOIN '.PREFIX_TABLE.'image_category ON id = image_id';
    $query.= ' WHERE category_id = '.$id;
    $query.= ' ORDER BY date_available DESC';
    $query.= ' LIMIT 0,1';
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    $query = 'UPDATE '.PREFIX_TABLE.'categories';
    $query.= " SET date_last = '".$row['date_available']."'";
    $query.= ' WHERE id = '.$id;
    $query.= ';';
    mysql_query( $query );
    // updating the representative_picture_id : if the representative
    // picture of the category is not any more linked to the category, we
    // have to set representative_picture_id to NULL
    $query = 'SELECT representative_picture_id';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$id;
    $row = mysql_fetch_array( mysql_query( $query ) );
    // if the category has no representative picture (ie
    // representative_picture_id == NULL) we don't update anything
    if ( $row['representative_picture_id'] != '' )
    {
      $query = 'SELECT image_id';
      $query.= ' FROM '.PREFIX_TABLE.'image_category';
      $query.= ' WHERE category_id = '.$id;
      $query.= ' AND image_id = '.$row['representative_picture_id'];
      $query.= ';';
      $result = mysql_query( $query );
      if ( mysql_num_rows( $result ) == 0 )
      {
        $query = 'UPDATE '.PREFIX_TABLE.'categories';
        $query.= ' SET representative_picture_id = NULL';
        $query.= ' WHERE id = '.$id;
        $query.= ';';
        mysql_query( $query );
      }
    }
  }
}

function check_date_format( $date )
{
  // date arrives at this format : DD/MM/YYYY
  list($day,$month,$year) = explode( '/', $date );
  return checkdate ( $month, $day, $year );
}

function date_convert( $date )
{
  // date arrives at this format : DD/MM/YYYY
  // It must be transformed in YYYY-MM-DD
  list($day,$month,$year) = explode( '/', $date );
  return $year.'-'.$month.'-'.$day;
}

function date_convert_back( $date )
{
  // date arrives at this format : YYYY-MM-DD
  // It must be transformed in DD/MM/YYYY
  if ( $date != '' )
  {
    list($year,$month,$day) = explode( '-', $date );
    return $day.'/'.$month.'/'.$year;
  }
  else
  {
    return '';
  }
}

// get_keywords returns an array with relevant keywords found in the string
// given in argument. Keywords must be separated by comma in this string.
// keywords must :
//   - be longer or equal to 3 characters
//   - not contain ', " or blank characters
//   - unique in the string ("test,test" -> "test")
function get_keywords( $keywords_string )
{
  $keywords = array();

  $candidates = explode( ',', $keywords_string );
  foreach ( $candidates as $candidate ) {
    if ( strlen($candidate) >= 3 and !preg_match( '/(\'|"|\s)/', $candidate ) )
      array_push( $keywords, $candidate );
  }

  return array_unique( $keywords );
}

function display_categories( $categories, $indent,
                             $selected = -1, $forbidden = -1 )
{
  global $vtp,$sub;

  foreach ( $categories as $category ) {
    if ( $category['id'] != $forbidden )
    {
      $vtp->addSession( $sub, 'associate_cat' );
      $vtp->setVar( $sub, 'associate_cat.value',   $category['id'] );
      $content = $indent.'- '.$category['name'];
      $vtp->setVar( $sub, 'associate_cat.content', $content );
      if ( $category['id'] == $selected )
        $vtp->setVar( $sub, 'associate_cat.selected', ' selected="selected"' );
      $vtp->closeSession( $sub, 'associate_cat' );
      display_categories( $category['subcats'], $indent.str_repeat('&nbsp;',3),
                          $selected, $forbidden );
    }
  }
}
?>