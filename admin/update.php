<?php
/***************************************************************************
 *                                update.php                               *
 *                            ------------------                           *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
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
function insert_local_category( $cat_id )
{
  global $conf, $page;
		
  $site_id = 1;
		
  // 0. retrieving informations on the category to display
  $cat_directory = '../galleries';
		
  if ( is_numeric( $cat_id ) )
  {
    $result = get_cat_info( $cat_id );
    $cat_directory.= '/'.$result['local_dir'];
    // 1. display the category name to update
    $output = '<img src="./images/puce.gif" alt="&gt;" />';
    $output.= '<span style="font-weight:bold;">'.$result['name'][0].'</span>';
    $output.= ' [ '.$result['last_dir'].' ]';
    $output.= '<div class="retrait">';
			
    // 2. we search pictures of the category only if the update is for all
    //    or a cat_id is specified
    if ( isset( $page['cat'] ) or $_GET['update'] == 'all' )
    {
      $output.= insert_local_image( $cat_directory, $cat_id );
      update_cat_info( $cat_id );
    }
  }
		
  // 3. we have to remove the categories of the database not present anymore
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE site_id = '.$site_id;
  if ( !is_numeric( $cat_id ) )
  {
    $query.= ' AND id_uppercat IS NULL';
  }
  else
  {
    $query.= ' AND id_uppercat = '.$cat_id;
  }
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // retrieving the directory
    $rep = '../galleries';
    $resultat = get_cat_info( $row['id'] );
    $rep.= '/'.$resultat['local_dir'];
			
    // is the directory present ?
    if ( !is_dir( $rep ) )
    {
      delete_category( $row['id'] );
    }
  }
		
  // 4. retrieving the sub-directories
  $sub_rep = array();
  $i = 0;
  $dirs = '';
  if ( $opendir = opendir ( $cat_directory ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( $file != '.'
           and $file != '..'
           and is_dir ( $cat_directory.'/'.$file )
           and $file != 'thumbnail' )
      {
        $sub_rep[$i++] = $file;
      }
    }
  }
		
  for ( $i = 0; $i < sizeof( $sub_rep ); $i++ )
  {
    // 5. Is the category already existing ? we create a subcat if not
    //    existing
    $category_id = '';
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE site_id = '.$site_id;
    $query.= " AND dir = '".$sub_rep[$i]."'";
    if ( !is_numeric( $cat_id ) )
    {
      $query.= ' AND id_uppercat IS NULL';
    }
    else
    {
      $query.= ' AND id_uppercat = '.$cat_id;
    }
    $query.= ';';
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) == 0 )
    {
      // we have to create the category
      $query = 'INSERT INTO '.PREFIX_TABLE.'categories';
      $query.= ' (dir,site_id,id_uppercat) VALUES';
      $query.= " ('".$sub_rep[$i]."','".$site_id."'";
      if ( !is_numeric( $cat_id ) )
      {
        $query.= ',NULL';
      }
      else
      {
        $query.= ",'".$cat_id."'";
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
    // 6. recursive call
    $output.= insert_local_category( $category_id );
  }
		
  if ( is_numeric( $cat_id ) )
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
  $query.= ' WHERE cat_id = '.$category_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $lien_image = $rep.'/'.$row['file'];
    $lien_thumbnail = $rep.'/thumbnail/'.$conf['prefixe_thumbnail'];
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
  if ( $opendir = opendir ( $rep ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( is_file( $rep.'/'.$file ) and is_image( $rep.'/'.$file ) )
      {
        // is the picture waiting for validation by an administrator ?
        $query = 'SELECT id';
        $query.= ' FROM '.PREFIX_TABLE.'waiting';
        $query.= ' WHERE cat_id = '.$category_id;
        $query.= " AND file = '".$file."'";
        $query.= ';';
        $result = mysql_query( $query );
        if ( mysql_num_rows( $result ) == 0 )
        {
          if ( $tn_ext = TN_exists( $rep, $file ) )
          {
            // is the picture already in the database ?
            $query = 'SELECT id';
            $query.= ' FROM '.PREFIX_TABLE.'images';
            $query.= ' WHERE cat_id = '.$category_id;
            $query.= " AND file = '".$file."'";
            $query.= ';';
            $result = mysql_query( $query );
            if ( mysql_num_rows( $result ) == 0 )
            {
              $picture = array();
              $picture['file'] = $file;
              $picture['tn_ext'] = $tn_ext;
              $picture['date'] = date( 'Y-m-d', filemtime ( $rep.'/'.$file ) );
              $picture['filesize'] = floor( filesize( $rep.'/'.$file ) / 1024);
              $image_size = @getimagesize( $rep.'/'.$file );
              $picture['width'] = $image_size[0];
              $picture['height'] = $image_size[1];
              array_push( $pictures, $picture );
            }
          }
          else
          {
            $output.= '<span style="color:red;">';
            $output.= $lang['update_missing_tn'].' : '.$file;
            $output.= ' (<span style="font-weight:bold;">';
            $output.= $conf['prefixe_thumbnail'];
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
    $query.= ' (file,cat_id,date_available,tn_ext,filesize,width,height)';
    $query.= ' VALUES ';
    $query.= "('".$picture['file']."','".$category_id."'";
    $query.= ",'".$picture['date']."','".$picture['tn_ext']."'";
    $query.= ",'".$picture['filesize']."','".$picture['width']."'";
    $query.= ",'".$picture['height']."')";
    $query.= ';';
    mysql_query( $query );
    $count_new++;
    
    $output.= $picture['file'];
    $output.= ' <span style="font-weight:bold;">';
    $output.= $lang['update_research_added'].'</span>';
    $output.= ' ('.$lang['update_research_tn_ext'].' '.$picture['tn_ext'].')';
    $output.= '<br />';
  }
  return $output;
}
	
// The function "update_cat_info" updates the information about the last
// online image and the number of images in the category
function update_cat_info( $category_id )
{
  $query = 'SELECT date_available';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE cat_id = '.$category_id;
  $query.= ' ORDER BY date_available DESC';
  $query.= ' LIMIT 0,1';
  $query.= ';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  $date_last = $row['date_available'];
		
  $query = 'SELECT COUNT(*) as nb_images';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE cat_id = '.$category_id;
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  $nb_images = $row['nb_images'];
		
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= " SET date_dernier = '".$date_last."'";
  $query.= ', nb_images = '.$nb_images;
  $query.= ' where id = '.$category_id;
  $query.= ';';
  mysql_query( $query );
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
  $query = 'select id';
  $query.= ' from '.PREFIX_TABLE.'sites';
  $query.= " where galleries_url = '".$url."'";
  $query.= ';';
  $result = mysql_query( $query );
  if ( mysql_num_rows($result ) == 0 )
  {
    // we have to register this site in the database
    $query = 'insert into '.PREFIX_TABLE.'sites';
    $query.= " (galleries_url) values ('".$url."')";
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
  global $conf;

  $output = '';
  $categories = array();
  $list_dirs = getChildren( $xml_dir, 'dir'.$level );
  for ( $i = 0; $i < sizeof( $list_dirs ); $i++ )
  {
    // is the category already existing ?
    $category_id = '';
    $name = getAttribute( $list_dirs[$i], 'name' );
    $categories[$i] = $name;

    $output.= '<img src="./images/puce.gif">';
    $output.= '<span style="font-weight:bold;">'.$name.'</span>';
    $output.= '<div class="retrait">';

    $query = 'select id';
    $query.= ' from '.PREFIX_TABLE.'categories';
    $query.= ' where site_id = '.$site_id;
    $query.= " and dir = '".$name."'";
    if ( $id_uppercat == 'NULL' )
    {
      $query.= ' and id_uppercat is NULL';
    }
    else
    {
      $query.= ' and id_uppercat = '.$id_uppercat;
    }
    $query.= ';';
    $result = mysql_query( $query );
    if ( mysql_num_rows( $result ) == 0 )
    {
      // we have to create the category
      $query = 'insert into '.PREFIX_TABLE.'categories';
      $query.= " (dir,site_id,id_uppercat) values ('".$name."',".$site_id;
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
    update_cat_info( $category_id );
    $output.= insert_remote_category( $list_dirs[$i], $site_id,
                                      $category_id, $level+1 );
    $output.= '</div>';
  }
  // we have to remove the categories of the database not present in the xml
  // file (ie deleted from the picture storage server)
  $query = 'select dir,id';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where site_id = '.$site_id;
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' and id_uppercat is NULL';
  }
  else
  {
    $query.= ' and id_uppercat = '.$id_uppercat;
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
    $query = 'select id,tn_ext';
    $query.= ' from '.PREFIX_TABLE.'images';
    $query.= ' where cat_id = '.$category_id;
    $query.= " and file = '".$file."'";
    $query.= ';';
    $result = mysql_query( $query );
    $query = '';
    if ( mysql_num_rows( $result ) == 0 )
    {
      $query = 'insert into '.PREFIX_TABLE.'images';
      $query.= ' (file,cat_id,date_available,tn_ext,filesize,width,height)';
      $query.= ' values (';
      $query.= "'".$file."'";
      $query.= ",'".$category_id."'";
      $query.= ",'".$date."'";
      $query.= ",'".$tn_ext."'";
      $query.= ",'".$filesize."'";
      $query.= ",'".$width."'";
      $query.= ",'".$height."'";
      $query.= ')';
      $query.= ';';

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
        $query = 'update '.PREFIX_TABLE.'images';
        $query.= ' set';
        $query.= " tn_ext = '".$tn_ext."'";
        $query.= ' where cat_id = '.$category_id;
        $query.= " and file = '".$file."'";
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
  $query = 'select id,file';
  $query.= ' from '.PREFIX_TABLE.'images';
  $query.= ' where cat_id = '.$category_id;
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
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>