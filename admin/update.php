<?php
/***************************************************************************
 *                                update.php                               *
 *                            ------------------                           *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
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

include_once( './include/isadmin.inc.php' );
//------------------------------------------------------------------- functions
function insert_local_category( $id_uppercat )
{
  global $conf, $page, $user, $lang;
 
  $uppercats = '';
		
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
    $src = '../template/'.$user['template'].'/admin/images/puce.gif';
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
    $id = intval($row['id']);
    $sub_category_dirs[$id] = $row['dir'];
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
        $value = '';
        $name = str_replace( '_', ' ', $sub_dir );

        $value.= "('".$sub_dir."','".$name."',1";
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

function insert_local_image( $rep, $category_id )
{
  global $lang,$conf,$count_new;

  $output = '';
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
    $lien_image = $rep.'/'.$row['file'];
    $lien_thumbnail = $rep.'/thumbnail/'.$conf['prefix_thumbnail'];
    $lien_thumbnail.= get_filename_wo_extension( $row['file'] );
    $lien_thumbnail.= '.'.$row['tn_ext'];
		
    if ( !is_file ( $lien_image ) or !is_file ( $lien_thumbnail ) )
    {
      if ( !is_file ( $lien_image ) )
      {
        $output.= $row['file'];
        $output.= ' <span style="font-weight:bold;">';
        $output.= $lang['update_disappeared'].'</span><br />';
      }
      if ( !is_file ( $lien_thumbnail ) )
      {
        $output.= $row['file'];
        $output.= ' : <span style="font-weight:bold;">';
        $output.= $lang['update_disappeared_tn'].'</span><br />';
      }
      // suppression de la base :
      delete_image( $row['id'] );
    }
  }
		
  // searching the new images in the directory
  $pictures = array();		
  $tn_ext = '';
  if ( $opendir = opendir( $rep ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( is_file( $rep.'/'.$file ) and is_image( $rep.'/'.$file ) )
      {
        // is the picture waiting for validation by an administrator ?
        $query = 'SELECT id,validated,infos';
        $query.= ' FROM '.PREFIX_TABLE.'waiting';
        $query.= ' WHERE storage_category_id = '.$category_id;
        $query.= " AND file = '".$file."'";
        $query.= ';';
        $result = mysql_query( $query );
        $waiting = mysql_fetch_array( $result );
        if (mysql_num_rows( $result ) == 0 or $waiting['validated'] == 'true')
        {
          if ( $tn_ext = TN_exists( $rep, $file ) )
          {
            // is the picture already in the database ?
            $query = 'SELECT id';
            $query.= ' FROM '.PREFIX_TABLE.'images';
            $query.= ' WHERE storage_category_id = '.$category_id;
            $query.= " AND file = '".$file."'";
            $query.= ';';
            $result = mysql_query( $query );
            if ( mysql_num_rows( $result ) == 0 )
            {
              // the name of the file must not use acentuated characters or
              // blank space..
              if ( preg_match( '/^[a-zA-Z0-9-_.]+$/', $file ) )
              {
                $picture = array();
                $picture['file']     = $file;
                $picture['tn_ext']   = $tn_ext;
                $picture['date'] = date( 'Y-m-d', filemtime($rep.'/'.$file) );
                $picture['filesize'] = floor( filesize($rep.'/'.$file) / 1024);
                $image_size = @getimagesize( $rep.'/'.$file );
                $picture['width']    = $image_size[0];
                $picture['height']   = $image_size[1];
                if ( $waiting['validated'] == 'true' )
                {
                  // retrieving infos from the XML description of
                  // $waiting['infos']
                  $infos = nl2br( $waiting['infos'] );
                  $picture['author']        = getAttribute( $infos, 'author' );
                  $picture['comment']       = getAttribute( $infos, 'comment');
                  $unixtime = getAttribute( $infos, 'date_creation' );
                  $picture['date_creation'] = '';
                  if ( $unixtime != '' )
                    $picture['date_creation'] = date( 'Y-m-d', $unixtime );
                  $picture['name']          = getAttribute( $infos, 'name' );
                  // deleting the waiting element
                  $query = 'DELETE FROM '.PREFIX_TABLE.'waiting';
                  $query.= ' WHERE id = '.$waiting['id'];
                  $query.= ';';
                  mysql_query( $query );
                }
                array_push( $pictures, $picture );
              }
              else
              {
                $output.= '<span style="color:red;">"'.$file.'" : ';
                $output.= $lang['update_wrong_dirname'].'</span><br />';
              }

            }
          }
          else
          {
            $output.= '<span style="color:red;">';
            $output.= $lang['update_missing_tn'].' : '.$file;
            $output.= ' (<span style="font-weight:bold;">';
            $output.= $conf['prefix_thumbnail'];
            $output.= get_filename_wo_extension( $file ).'.XXX</span>';
            $output.= ', XXX = ';
            $output.= implode( ', ', $conf['picture_ext'] );
            $output.= ')</span><br />';
          }
        }
      }
    }
  }
  // inserting the pictures found in the directory
  foreach ( $pictures as $picture ) {
    $query = 'INSERT INTO '.PREFIX_TABLE.'images';
    $query.= ' (file,storage_category_id,date_available,tn_ext';
    $query.= ',filesize,width,height';
    $query.= ',name,author,comment,date_creation)';
    $query.= ' VALUES ';
    $query.= "('".$picture['file']."','".$category_id."'";
    $query.= ",'".$picture['date']."','".$picture['tn_ext']."'";
    $query.= ",'".$picture['filesize']."','".$picture['width']."'";
    $query.= ",'".$picture['height']."','".$picture['name']."'";
    $query.= ",'".$picture['author']."','".$picture['comment']."'";
    if ( $picture['date_creation'] != '' )
    {
      $query.= ",'".$picture['date_creation']."'";
    }
    else
    {
      $query.= ',NULL';
    }
    $query.= ');';
    mysql_query( $query );
    $count_new++;
    // retrieving the id of newly inserted picture
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' WHERE storage_category_id = '.$category_id;
    $query.= " AND file = '".$picture['file']."'";
    $query.= ';';
    list( $image_id ) = mysql_fetch_array( mysql_query( $query ) );
    // adding the link between this picture and its storage category
    $query = 'INSERT INTO '.PREFIX_TABLE.'image_category';
    $query.= ' (image_id,category_id) VALUES ';
    $query.= ' ('.$image_id.','.$category_id.')';
    $query.= ';';
    mysql_query( $query );

    $output.= $picture['file'];
    $output.= ' <span style="font-weight:bold;">';
    $output.= $lang['update_research_added'].'</span>';
    $output.= ' ('.$lang['update_research_tn_ext'].' '.$picture['tn_ext'].')';
    $output.= '<br />';
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
function insert_remote_category( $xml_dir, $site_id, $id_uppercat, $level )
{
  global $conf,$user;

  $output = '';
  $categories = array();
  $list_dirs = getChildren( $xml_dir, 'dir'.$level );
  for ( $i = 0; $i < sizeof( $list_dirs ); $i++ )
  {
    // is the category already existing ?
    $category_id = '';
    $dir = getAttribute( $list_dirs[$i], 'name' );
    $categories[$i] = $dir;

    $src = '../template/'.$user['template'].'/admin/images/puce.gif';
    $output.= '<img src="'.$src.'" alt="&gt;" />';
    $output.= '<span style="font-weight:bold;">'.$dir.'</span>';
    $output.= '<div class="retrait">';

    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE site_id = '.$site_id;
    $query.= " AND dir = '".$dir."'";
    if ( $id_uppercat == 'NULL' )
    {
      $query.= ' AND id_uppercat IS NULL';
    }
    else
    {
      $query.= ' AND id_uppercat = '.$id_uppercat;
    }
    $query.= ';';
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) == 0 )
    {
      $name = str_replace( '_', ' ', $dir );
      // we have to create the category
      $query = 'INSERT INTO '.PREFIX_TABLE.'categories';
      $query.= ' (name,dir,site_id,id_uppercat) VALUES ';
      $query.= "('".$name."','".$dir."',".$site_id;
      if ( !is_numeric( $id_uppercat ) )
      {
        $query.= ',NULL';
      }
      else
      {
        $query.= ','.$id_uppercat;
      }
      $query.= ');';
      mysql_query( $query );
      $category_id = mysql_insert_id();
    }
    else
    {
      // we get the already registered id
      $row = mysql_fetch_array( $result );
      $category_id = $row['id'];
    }
    $output.= insert_remote_image( $list_dirs[$i], $category_id );
    $output.= insert_remote_category( $list_dirs[$i], $site_id,
                                      $category_id, $level+1 );
    $output.= '</div>';
  }
  // we have to remove the categories of the database not present in the xml
  // file (ie deleted from the picture storage server)
  $query = 'SELECT dir,id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = '.$site_id;
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' AND id_uppercat IS NULL';
  }
  else
  {
    $query.= ' AND id_uppercat = '.$id_uppercat;
  }
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // is the category in the xml file ?
    if ( !in_array( $row['dir'], $categories ) )
    {
      delete_category( $row['id'] );
    }
  }

  return $output;
}
	
// insert_remote_image searchs the "root" node of the xml_dir given and
// insert the contained pictures if the are not in the database yet.
function insert_remote_image( $xml_dir, $category_id )
{
  global $count_new,$lang;

  $output = '';
  $root = getChild( $xml_dir, 'root' );
  $pictures = array();
  $xml_pictures = getChildren( $root, 'picture' );
  for ( $j = 0; $j < sizeof( $xml_pictures ); $j++ )
  {
    //<picture file="albatros.jpg" tn_ext="png" date="2002-04-14"
    //  filesize="35" width="640" height="480" />
    $file     = getAttribute( $xml_pictures[$j], 'file' );
    $tn_ext   = getAttribute( $xml_pictures[$j], 'tn_ext' );
    $date     = getAttribute( $xml_pictures[$j], 'date' ); 
    $filesize = getAttribute( $xml_pictures[$j], 'filesize' );
    $width    = getAttribute( $xml_pictures[$j], 'width' );
    $height   = getAttribute( $xml_pictures[$j], 'height' );
			
    $pictures[$j] = $file;
			
    // is the picture already existing in the database ?
    $query = 'SELECT id,tn_ext';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' WHERE storage_category_id = '.$category_id;
    $query.= " AND file = '".$file."'";
    $query.= ';';
    $result = mysql_query( $query );
    $query = '';
    if ( mysql_num_rows( $result ) == 0 )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'images';
      $query.= ' (file,storage_category_id,date_available,tn_ext';
      $query.= ',filesize,width,height)';
      $query.= ' VALUES (';
      $query.= "'".$file."'";
      $query.= ",'".$category_id."'";
      $query.= ",'".$date."'";
      $query.= ",'".$tn_ext."'";
      $query.= ",'".$filesize."'";
      $query.= ",'".$width."'";
      $query.= ",'".$height."'";
      $query.= ')';
      $query.= ';';
      mysql_query( $query );
      // retrieving the id of newly inserted picture
      $query = 'SELECT id';
      $query.= ' FROM '.PREFIX_TABLE.'images';
      $query.= ' WHERE storage_category_id = '.$category_id;
      $query.= " AND file = '".$file."'";
      $query.= ';';
      list( $image_id ) = mysql_fetch_array( mysql_query( $query ) );
      // adding the link between this picture and its storage category
      $query = 'INSERT INTO '.PREFIX_TABLE.'image_category';
      $query.= ' (image_id,category_id) VALUES ';
      $query.= ' ('.$image_id.','.$category_id.')';
      $query.= ';';
      mysql_query( $query );

      $output.= $file;
      $output.= ' <span style="font-weight:bold;">';
      $output.= $lang['update_research_added'].'</span>';
      $output.= ' ('.$lang['update_research_tn_ext'].' '.$tn_ext.')<br />';

      $count_new++;
    }
    else
    {
      // is the tn_ext the same in the xml file and in the database ?
      $row = mysql_fetch_array( $result );
      if ( $row['tn_ext'] != $tn_ext )
      {
        $query = 'UPDATE '.PREFIX_TABLE.'images';
        $query.= ' SET';
        $query.= " tn_ext = '".$tn_ext."'";
        $query.= ' WHERE storage_category_id = '.$category_id;
        $query.= " AND file = '".$file."'";
        $query.= ';';
      }
    }
    // execution of the query
    if ( $query != '' )
    {
      mysql_query( $query );
    }
  }
  // we have to remove the pictures of the database not present in the xml file
  // (ie deleted from the picture storage server)
  $query = 'SELECT id,file';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE storage_category_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // is the file in the xml file ?
    if ( !in_array( $row['file'], $pictures ) )
    {
      delete_image( $row['id'] );
    }
  }
  return $output;
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/update.vtp' );
$tpl = array( 'update_default_title', 'update_only_cat', 'update_all',
              'update_research_conclusion', 'update_deletion_conclusion',
              'remote_site', 'update_part_research' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//-------------------------------------------- introduction : choices of update
// Display choice if "update" var is not specified
check_cat_id( $_GET['update'] );
if ( !isset( $_GET['update'] )
     and !( isset( $page['cat'] )
            or $_GET['update'] == 'cats'
            or $_GET['update'] == 'all' ) )
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
//------------------------------------------------- local update : ../galleries
else
{
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

  remote_images();
  $vtp->setVar( $sub, 'remote_update.count_new', $count_new );
  $vtp->setVar( $sub, 'remote_update.count_deleted', $count_deleted );

  $vtp->closeSession( $sub, 'remote_update' );
}
//---------------------------------------- update informations about categories
if ( isset( $_GET['update'] )
     or isset( $page['cat'] )
     or @is_file( './listing.xml' ) )
{
  update_category( 'all' );
  synchronize_all_users();
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>