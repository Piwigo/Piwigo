<?php
/***************************************************************************
 *                                update.php                               *
 *                            ------------------                           *
 *   application   : PhpWebGallery 1.4 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

include_once( './admin/include/isadmin.inc.php' );
//------------------------------------------------------------------- functions
function insert_local_category( $id_uppercat )
{
  global $conf, $page, $user, $lang;
 
  $uppercats = '';
  $output = '';

  // 0. retrieving informations on the category to display
  $cat_directory = './galleries';
  if ( is_numeric( $id_uppercat ) )
  {
    $query = 'SELECT name,uppercats,dir';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$id_uppercat;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    $uppercats = $row['uppercats'];
    $name      = $row['name'];
    $dir       = $row['dir'];

    $upper_array = explode( ',', $uppercats );

    $local_dir = '';

    $database_dirs = array();
    $query = 'SELECT id,dir';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id IN ('.$uppercats.')';
    $query.= ';';
    $result = mysql_query( $query );
    while( $row = mysql_fetch_array( $result ) )
    {
      $database_dirs[$row['id']] = $row['dir'];
    }
    foreach ( $upper_array as $id ) {
      $local_dir.= $database_dirs[$id].'/';
    }

    $cat_directory.= '/'.$local_dir;

    // 1. display the category name to update
    $src = './template/'.$user['template'].'/admin/images/puce.gif';
    $output = '<img src="'.$src.'" alt="&gt;" />';
    $output.= '<span style="font-weight:bold;">'.$name.'</span>';
    $output.= ' [ '.$dir.' ]';
    $output.= '<div class="retrait">';

    // 2. we search pictures of the category only if the update is for all
    //    or a cat_id is specified
    if ( isset( $page['cat'] ) or $_GET['update'] == 'all' )
    {
      $output.= insert_local_image( $cat_directory, $id_uppercat );
    }
  }

  $sub_dirs = get_category_directories( $cat_directory );

  $sub_category_dirs = array();
  $query = 'SELECT id,dir';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = 1';
  if (!is_numeric($id_uppercat)) $query.= ' AND id_uppercat IS NULL';
  else                           $query.= ' AND id_uppercat = '.$id_uppercat;
  $query.= ' AND dir IS NOT NULL'; // virtual categories not taken
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $sub_category_dirs[$row['id']] = $row['dir'];
  }
  
  // 3. we have to remove the categories of the database not present anymore
  foreach ( $sub_category_dirs as $id => $dir ) {
    if ( !in_array( $dir, $sub_dirs ) ) delete_category( $id );
  }

  // array of new categories to insert
  $inserts = array();
  
  foreach ( $sub_dirs as $sub_dir ) {
    // 5. Is the category already existing ? we create a subcat if not
    //    existing
    $category_id = array_search( $sub_dir, $sub_category_dirs );
    if ( !is_numeric( $category_id ) )
    {
      if ( preg_match( '/^[a-zA-Z0-9-_.]+$/', $sub_dir ) )
      {
        $name = str_replace( '_', ' ', $sub_dir );

        $value = "('".$sub_dir."','".$name."',1";
        if ( !is_numeric( $id_uppercat ) ) $value.= ',NULL';
        else                               $value.= ','.$id_uppercat;
        $value.= ",'undef'";
        $value.= ')';
        array_push( $inserts, $value );
      }
      else
      {
        $output.= '<span style="color:red;">"'.$sub_dir.'" : ';
        $output.= $lang['update_wrong_dirname'].'</span><br />';
      }
    }
  }

  // we have to create the category
  if ( count( $inserts ) > 0 )
  {
    $query = 'INSERT INTO '.PREFIX_TABLE.'categories';
    $query.= ' (dir,name,site_id,id_uppercat,uppercats) VALUES ';
    $query.= implode( ',', $inserts );
    $query.= ';';
    mysql_query( $query );
    // updating uppercats field
    $query = 'UPDATE '.PREFIX_TABLE.'categories';
    $query.= ' SET uppercats = ';
    if ( $uppercats != '' ) $query.= "CONCAT('".$uppercats."',',',id)";
    else                    $query.= 'id';
    $query.= ' WHERE id_uppercat ';
    if (!is_numeric($id_uppercat)) $query.= 'IS NULL';
    else                           $query.= '= '.$id_uppercat;
    $query.= ';';
    mysql_query( $query );
  }

  // Recursive call on the sub-categories (not virtual ones)
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = 1';
  if (!is_numeric($id_uppercat)) $query.= ' AND id_uppercat IS NULL';
  else                           $query.= ' AND id_uppercat = '.$id_uppercat;
  $query.= ' AND dir IS NOT NULL'; // virtual categories not taken
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $output.= insert_local_category( $row['id'] );
  }

  if ( is_numeric( $id_uppercat ) )
  {
    $output.= '</div>';
  }
  return $output;
}

function insert_local_image( $dir, $category_id )
{
  global $lang,$conf,$count_new;

  $output = '';

  // fs means filesystem : $fs_pictures contains pictures in the filesystem
  // found in $dir, $fs_thumbnails contains thumbnails...
  $fs_pictures   = get_picture_files( $dir );
  $fs_thumbnails = get_thumb_files( $dir.'thumbnail' );

  // we have to delete all the images from the database that :
  //     - are not in the directory anymore
  //     - don't have the associated thumbnail available anymore
  $query = 'SELECT id,file,tn_ext';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $pic_to_delete = false;
    if ( !in_array( $row['file'], $fs_pictures ) )
    {
      $output.= $row['file'];
      $output.= ' <span style="font-weight:bold;">';
      $output.= $lang['update_disappeared'].'</span><br />';
      $pic_to_delete = true;
    }

    $thumbnail = $conf['prefix_thumbnail'];
    $thumbnail.= get_filename_wo_extension( $row['file'] );
    $thumbnail.= '.'.$row['tn_ext'];
    if ( !in_array( $thumbnail, $fs_thumbnails ) )
    {
      $output.= $row['file'];
      $output.= ' : <span style="font-weight:bold;">';
      $output.= $lang['update_disappeared_tn'].'</span><br />';
      $pic_to_delete = true;
    }

    if ( $pic_to_delete ) delete_image( $row['id'] );
  }

  $registered_pictures = array();
  $query = 'SELECT file';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $registered_pictures, $row['file'] );
  }

  // validated pictures are picture uploaded by users, validated by an admin
  // and not registered (visible) yet
  $validated_pictures    = array();
  $unvalidated_pictures  = array();
  
  $query = 'SELECT file,infos,validated';
  $query.= ' FROM '.PREFIX_TABLE.'waiting';
  $query.= ' WHERE storage_category_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( $row['validated'] == 'true' )
      $validated_pictures[$row['file']] = $row['infos'];
    else
      array_push( $unvalidated_pictures, $row['file'] );
  }

  // we only search among the picture present in the filesystem and not
  // present in the database yet. If we know that this picture is known as
  // an uploaded one but not validated, it's not tested neither
  $unregistered_pictures = array_diff( $fs_pictures
                                       ,$registered_pictures
                                       ,$unvalidated_pictures );

  $inserts = array();
  
  foreach ( $unregistered_pictures as $unregistered_picture ) {
    if ( preg_match( '/^[a-zA-Z0-9-_.]+$/', $unregistered_picture ) )
    {
      $file_wo_ext = get_filename_wo_extension( $unregistered_picture );
      $tn_ext = '';
      foreach ( $conf['picture_ext'] as $ext ) {
        $test = $conf['prefix_thumbnail'].$file_wo_ext.'.'.$ext;
        if ( !in_array( $test, $fs_thumbnails ) ) continue;
        else { $tn_ext = $ext; break; }
      }
      // if we found a thumnbnail corresponding to our picture...
      if ( $tn_ext != '' )
      {
        $image_size = @getimagesize( $dir.$unregistered_picture );
        // (file, storage_category_id, date_available, tn_ext, filesize,
        // width, height, name, author, comment, date_creation)'
        $value = '(';
        $value.= "'".$unregistered_picture."'";
        $value.= ','.$category_id;
        $value.= ",'".date( 'Y-m-d' )."'";
        $value.= ",'".$tn_ext."'";
        $value.= ','.floor( filesize( $dir.$unregistered_picture) / 1024 );
        $value.= ','.$image_size[0];
        $value.= ','.$image_size[1];
        if ( isset( $validated_pictures[$unregistered_picture] ) )
        {
          // retrieving infos from the XML description from waiting table
          $infos = nl2br( $validated_pictures[$unregistered_picture] );

          $unixtime = getAttribute( $infos, 'date_creation' );
          if ($unixtime != '') $date_creation ="'".date('Y-m-d',$unixtime)."'";
          else                 $date_creation = 'NULL';
          
          $value.= ",'".getAttribute( $infos, 'name' )."'";
          $value.= ",'".getAttribute( $infos, 'author' )."'";
          $value.= ",'".getAttribute( $infos, 'comment')."'";
          $value.= ','.$date_creation;

          // deleting the waiting element
          $query = 'DELETE FROM '.PREFIX_TABLE.'waiting';
          $query.= " WHERE file = '".$unregistered_picture."'";
          $query.= ' AND storage_category_id = '.$category_id;
          $query.= ';';
          mysql_query( $query );
        }
        else
        {
          $value.= ",'','','',NULL";
        }
        $value.= ')';
        
        $count_new++;
        $output.= $unregistered_picture;
        $output.= ' <span style="font-weight:bold;">';
        $output.= $lang['update_research_added'].'</span>';
        $output.= ' ('.$lang['update_research_tn_ext'].' '.$tn_ext.')';
        $output.= '<br />';
        array_push( $inserts, $value );
      }
      else
      {
        $output.= '<span style="color:red;">';
        $output.= $lang['update_missing_tn'].' : '.$unregistered_picture;
        $output.= ' (<span style="font-weight:bold;">';
        $output.= $conf['prefix_thumbnail'];
        $output.= get_filename_wo_extension( $unregistered_picture );
        $output.= '.XXX</span>';
        $output.= ', XXX = ';
        $output.= implode( ', ', $conf['picture_ext'] );
        $output.= ')</span><br />';
      }
    }
    else
    {
      $output.= '<span style="color:red;">"'.$unregistered_picture.'" : ';
      $output.= $lang['update_wrong_dirname'].'</span><br />';
    }
  }

  if ( count( $inserts ) > 0 )
  {
    // inserts all found pictures
    $query = 'INSERT INTO '.PREFIX_TABLE.'images';
    $query.= ' (file,storage_category_id,date_available,tn_ext';
    $query.= ',filesize,width,height';
    $query.= ',name,author,comment,date_creation)';
    $query.= ' VALUES ';
    $query.= implode( ',', $inserts );
    $query.= ';';
    mysql_query( $query );

    // what are the ids of the pictures in the $category_id ?
    $ids = array();

    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' WHERE storage_category_id = '.$category_id;
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      array_push( $ids, $row['id'] );
    }

    // recreation of the links between this storage category pictures and
    // its storage category
    $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE category_id = '.$category_id;
    $query.= ' AND image_id IN ('.implode( ',', $ids ).')';
    $query.= ';';
    mysql_query( $query );

    $query = 'INSERT INTO '.PREFIX_TABLE.'image_category';
    $query.= '(category_id,image_id) VALUES ';
    foreach ( $ids as $num => $image_id ) {
      if ( $num > 0 ) $query.= ',';
      $query.= '('.$category_id.','.$image_id.')';
    }
    $query.= ';';
    mysql_query( $query );
  }
  return $output;
}

// remote_images verifies if a file named "listing.xml" is present is the
// admin directory. If it is the case, creation of a remote picture storage
// site if it doesn't already exists. Then, the function calls
// insert_remote_category for this remote site on the root category.
function remote_images()
{
  global $conf, $lang, $vtp, $sub;

  // 1. is there a file listing.xml ?
  if ( !( $xml_content = getXmlCode( 'listing.xml' ) ) )
  {
    return false;
  }
  $url = getContent( getChild( $xml_content, 'url' ) );
  $vtp->setVar( $sub, 'remote_update.url', $url );

  // 2. is the site already existing ?
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'sites';
  $query.= " WHERE galleries_url = '".$url."'";
  $query.= ';';
  $result = mysql_query( $query );
  if ( mysql_num_rows($result ) == 0 )
  {
    // we have to register this site in the database
    $query = 'INSERT INTO '.PREFIX_TABLE.'sites';
    $query.= " (galleries_url) VALUES ('".$url."')";
    $query.= ';';
    mysql_query( $query );
    $site_id = mysql_insert_id();
  }
  else
  {
    // we get the already registered id
    $row = mysql_fetch_array( $result );
    $site_id = $row['id'];
  }

  // 3. available dirs in the file
  $categories = insert_remote_category( $xml_content, $site_id, 'NULL', 0 );
  $vtp->setVar( $sub, 'remote_update.categories', $categories );
}

// insert_remote_category searchs the "dir" node of the xml_dir given and
// insert the contained categories if the are not in the database yet. The
// function also deletes the categories that are in the database and not in
// the xml_file.
function insert_remote_category( $xml_content, $site_id, $id_uppercat, $level )
{
  global $conf, $page, $user, $lang;
 
  $uppercats = '';
  $output = '';
  // 0. retrieving informations on the category to display
  $cat_directory = '../galleries';
		
  if ( is_numeric( $id_uppercat ) )
  {
    $query = 'SELECT name,uppercats,dir';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$id_uppercat;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    $uppercats = $row['uppercats'];
    $name      = $row['name'];

    // 1. display the category name to update
    $src = './template/'.$user['template'].'/admin/images/puce.gif';
    $output = '<img src="'.$src.'" alt="&gt;" />';
    $output.= '<span style="font-weight:bold;">'.$name.'</span>';
    $output.= ' [ '.$row['dir'].' ]';
    $output.= '<div class="retrait">';

    // 2. we search pictures of the category only if the update is for all
    //    or a cat_id is specified
    $output.= insert_remote_image( $xml_content, $id_uppercat );
  }

  // $xml_dirs contains dir names contained in the xml file for this
  // id_uppercat
  $xml_dirs = array();
  $temp_dirs = getChildren( $xml_content, 'dir'.$level );
  foreach ( $temp_dirs as $temp_dir ) {
    array_push( $xml_dirs, getAttribute( $temp_dir, 'name' ) );
  }

  // $database_dirs contains dir names contained in the database for this
  // id_uppercat and site_id
  $database_dirs = array();
  $query = 'SELECT id,dir';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = '.$site_id;
  if (!is_numeric($id_uppercat)) $query.= ' AND id_uppercat IS NULL';
  else                           $query.= ' AND id_uppercat = '.$id_uppercat;
  $query.= ' AND dir IS NOT NULL'; // virtual categories not taken
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $database_dirs[$row['id']] = $row['dir'];
  }
  
  // 3. we have to remove the categories of the database not present anymore
  foreach ( $database_dirs as $id => $dir ) {
    if ( !in_array( $dir, $xml_dirs ) ) delete_category( $id );
  }

  // array of new categories to insert
  $inserts = array();
  
  foreach ( $xml_dirs as $xml_dir ) {
    // 5. Is the category already existing ? we create a subcat if not
    //    existing
    $category_id = array_search( $xml_dir, $database_dirs );
    if ( !is_numeric( $category_id ) )
    {
      $name = str_replace( '_', ' ', $xml_dir );

      $value = "('".$xml_dir."','".$name."',".$site_id;
      if ( !is_numeric( $id_uppercat ) ) $value.= ',NULL';
      else                               $value.= ','.$id_uppercat;
      $value.= ",'undef'";
      $value.= ')';
      array_push( $inserts, $value );
    }
  }

  // we have to create the category
  if ( count( $inserts ) > 0 )
  {
    $query = 'INSERT INTO '.PREFIX_TABLE.'categories';
    $query.= ' (dir,name,site_id,id_uppercat,uppercats) VALUES ';
    $query.= implode( ',', $inserts );
    $query.= ';';
    mysql_query( $query );
    // updating uppercats field
    $query = 'UPDATE '.PREFIX_TABLE.'categories';
    $query.= ' SET uppercats = ';
    if ( $uppercats != '' ) $query.= "CONCAT('".$uppercats."',',',id)";
    else                    $query.= 'id';
    $query.= ' WHERE id_uppercat ';
    if (!is_numeric($id_uppercat)) $query.= 'IS NULL';
    else                           $query.= '= '.$id_uppercat;
    $query.= ';';
    mysql_query( $query );
  }

  // Recursive call on the sub-categories (not virtual ones)
  $query = 'SELECT id,dir';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = '.$site_id;
  if (!is_numeric($id_uppercat)) $query.= ' AND id_uppercat IS NULL';
  else                           $query.= ' AND id_uppercat = '.$id_uppercat;
  $query.= ' AND dir IS NOT NULL'; // virtual categories not taken
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $database_dirs[$row['dir']] = $row['id'];
  }
  foreach ( $temp_dirs as $temp_dir ) {
    $dir = getAttribute( $temp_dir, 'name' );
    $id_uppercat = $database_dirs[$dir];
    $output.= insert_remote_category( $temp_dir, $site_id,
                                      $id_uppercat,$level+1 );
  }

  if ( is_numeric( $id_uppercat ) ) $output.= '</div>';

  return $output;
}

// insert_remote_image searchs the "root" node of the xml_dir given and
// insert the contained pictures if the are not in the database yet.
function insert_remote_image( $xml_dir, $category_id )
{
  global $count_new,$lang;

  $output = '';
  $root = getChild( $xml_dir, 'root' );

  $fs_pictures = array();
  $xml_pictures = getChildren( $root, 'picture' );
  foreach ( $xml_pictures as $xml_picture ) {
    array_push( $fs_pictures, getAttribute( $xml_picture, 'file' ) );
  }
  
  // we have to delete all the images from the database that are not in the
  // directory anymore (not in the XML anymore)
  $query = 'SELECT id,file';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( !in_array( $row['file'], $fs_pictures ) )
    {
      $output.= $row['file'];
      $output.= ' <span style="font-weight:bold;">';
      $output.= $lang['update_disappeared'].'</span><br />';
      delete_image( $row['id'] );
    }
  }

  $database_pictures = array();
  $query = 'SELECT file';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $database_pictures, $row['file'] );
  }

  $inserts = array();
  $xml_pictures = getChildren( $root, 'picture' );
  foreach ( $xml_pictures as $xml_picture ) {
    // <picture file="albatros.jpg" tn_ext="png" filesize="35" width="640"
    // height="480" />
    $file = getAttribute( $xml_picture, 'file' );

    // is the picture already existing in the database ?
    if ( !in_array( $file, $database_pictures ) )
    {
      $tn_ext = getAttribute( $xml_picture, 'tn_ext' );
      // (file, storage_category_id, date_available, tn_ext, filesize,
      // width, height)
      $value = '(';
      $value.= "'".$file."'";
      $value.= ','.$category_id;
      $value.= ",'".date( 'Y-m-d' )."'";
      $value.= ",'".$tn_ext."'";
      $value.= ','.getAttribute( $xml_picture, 'filesize' );
      $value.= ','.getAttribute( $xml_picture, 'width' );
      $value.= ','.getAttribute( $xml_picture, 'height' );
      $value.= ')';

      $count_new++;
      $output.= $file;
      $output.= ' <span style="font-weight:bold;">';
      $output.= $lang['update_research_added'].'</span>';
      $output.= ' ('.$lang['update_research_tn_ext'].' '.$tn_ext.')';
      $output.= '<br />';
      array_push( $inserts, $value );
    }
  }

  if ( count( $inserts ) > 0 )
  {
    // inserts all found pictures
    $query = 'INSERT INTO '.PREFIX_TABLE.'images';
    $query.= ' (file,storage_category_id,date_available,tn_ext';
    $query.= ',filesize,width,height)';
    $query.= ' VALUES ';
    $query.= implode( ',', $inserts );
    $query.= ';';
    mysql_query( $query );

    // what are the ids of the pictures in the $category_id ?
    $ids = array();

    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' WHERE storage_category_id = '.$category_id;
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      array_push( $ids, $row['id'] );
    }

    // recreation of the links between this storage category pictures and
    // its storage category
    $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE category_id = '.$category_id;
    $query.= ' AND image_id IN ('.implode( ',', $ids ).')';
    $query.= ';';
    mysql_query( $query );

    $query = 'INSERT INTO '.PREFIX_TABLE.'image_category';
    $query.= '(category_id,image_id) VALUES ';
    foreach ( $ids as $num => $image_id ) {
      if ( $num > 0 ) $query.= ',';
      $query.= '('.$category_id.','.$image_id.')';
    }
    $query.= ';';
    mysql_query( $query );
  }

  return $output;
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open( './template/'.$user['template'].'/admin/update.vtp' );
$tpl = array( 'update_default_title', 'update_only_cat', 'update_all',
              'update_research_conclusion', 'update_deletion_conclusion',
              'remote_site', 'update_part_research' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//-------------------------------------------- introduction : choices of update
// Display choice if "update" var is not specified
if (!isset( $_GET['update'] ))
{
  $vtp->addSession( $sub, 'introduction' );
  // only update the categories, not the pictures.
  $url = add_session_id( './admin.php?page=update&amp;update=cats' );
  $vtp->setVar( $sub, 'introduction.only_cat:url', $url );
  // update the entire tree folder
  $url = add_session_id( './admin.php?page=update&amp;update=all' );
  $vtp->setVar( $sub, 'introduction.all:url', $url );
  $vtp->closeSession( $sub, 'introduction' );
}
//-------------------------------------------------- local update : ./galleries
else
{
  check_cat_id( $_GET['update'] );
  $start = get_moment();
  $count_new = 0;
  $count_deleted = 0;
  $vtp->addSession( $sub, 'local_update' );
  if ( isset( $page['cat'] ) )
  {
    $categories = insert_local_category( $page['cat'] );
  }
  else
  {
    $categories = insert_local_category( 'NULL' );
  }
  $end = get_moment();
  echo get_elapsed_time( $start, $end ).' for update <br />';
  $vtp->setVar( $sub, 'local_update.categories', $categories );
  $vtp->setVar( $sub, 'local_update.count_new', $count_new );
  $vtp->setVar( $sub, 'local_update.count_deleted', $count_deleted );
  $vtp->closeSession( $sub, 'local_update' );
}
//------------------------------------------------- remote update : listing.xml
if ( @is_file( './listing.xml' ) )
{
  $count_new = 0;
  $count_deleted = 0;
  $vtp->addSession( $sub, 'remote_update' );
  
  $start = get_moment();
  remote_images();
  $end = get_moment();
  echo get_elapsed_time( $start, $end ).' for remote_images<br />';
  
  $vtp->setVar( $sub, 'remote_update.count_new', $count_new );
  $vtp->setVar( $sub, 'remote_update.count_deleted', $count_deleted );

  $vtp->closeSession( $sub, 'remote_update' );
}
//---------------------------------------- update informations about categories
if ( isset( $_GET['update'] )
     or isset( $page['cat'] )
     or @is_file( './listing.xml' ) )
{
  $start = get_moment();
  update_category( 'all' );
  $end = get_moment();
  echo get_elapsed_time( $start, $end ).' for update_category( all )<br />';

  $start = get_moment();
  synchronize_all_users();
  $end = get_moment();
  echo get_elapsed_time( $start, $end ).' for synchronize_all_users<br />';
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>