<?php
/***************************************************************************
 *                             infos_images.php                            *
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

include_once( './admin/include/isadmin.inc.php' );
include_once( './template/'.$user['template'].'/htmlfunctions.inc.php' );
//-------------------------------------------------------------- initialization
check_cat_id( $_GET['cat_id'] );
if ( isset( $page['cat'] ) )
{
//--------------------------------------------------- update individual options
  $query = 'SELECT id,file';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' LEFT JOIN '.PREFIX_TABLE.'image_category ON id = image_id';
  $query.= ' WHERE category_id = '.$page['cat'];
  $query.= ';';
  $result = mysql_query( $query );
  $i = 1;
  while ( $row = mysql_fetch_array( $result ) )
  {
    $name          = 'name-'.$row['id'];
    $author        = 'author-'.$row['id'];
    $comment       = 'comment-'.$row['id'];
    $date_creation = 'date_creation-'.$row['id'];
    $keywords      = 'keywords-'.$row['id'];
    if ( isset( $_POST[$name] ) )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'images';

      $query.= ' SET name = ';
      if ( $_POST[$name] == '' )
        $query.= 'NULL';
      else
        $query.= "'".htmlentities( $_POST[$name], ENT_QUOTES )."'";

      $query.= ', author = ';
      if ( $_POST[$author] == '' )
        $query.= 'NULL';
      else
        $query.= "'".htmlentities($_POST[$author],ENT_QUOTES)."'";

      $query.= ', comment = ';
      if ( $_POST[$comment] == '' )
        $query.= 'NULL';
      else
        $query.= "'".htmlentities($_POST[$comment],ENT_QUOTES)."'";

      $query.= ', date_creation = ';
      if ( check_date_format( $_POST[$date_creation] ) )
        $query.= "'".date_convert( $_POST[$date_creation] )."'";
      else if ( $_POST[$date_creation] == '' )
        $query.= 'NULL';

      $query.= ', keywords = ';
      $keywords_array = get_keywords( $_POST[$keywords] );
      if ( count( $keywords_array ) == 0 )
        $query.= 'NULL';
      else
      {
        $query.= "'";
        foreach ( $keywords_array as $i => $keyword ) {
          if ( $i > 0 ) $query.= ',';
          $query.= $keyword;
        }
        $query.= "'";
      }

      $query.= ' WHERE id = '.$row['id'];
      $query.= ';';
      mysql_query( $query );
    }
    // add link to another category
    if ( $_POST['check-'.$row['id']] == 1 )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'image_category';
      $query.= ' (image_id,category_id) VALUES';
      $query.= ' ('.$row['id'].','.$_POST['associate'].')';
      $query.= ';';
      mysql_query( $query );
    }
  }
  update_category( $_POST['associate'] );
//------------------------------------------------------ update general options
  if ( $_POST['use_common_author'] == 1 )
  {
    $query = 'SELECT image_id';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE category_id = '.$page['cat'];
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'images';
      if ( $_POST['author_cat'] == '' )
      {
        $query.= ' SET author = NULL';
      }
      else
      {
        $query.= ' SET author = ';
        $query.= "'".htmlentities( $_POST['author_cat'], ENT_QUOTES )."'";
      }
      $query.= ' WHERE id = '.$row['image_id'];
      $query.= ';';
      mysql_query( $query );
    }
  }
  if ( $_POST['use_common_date_creation'] == 1 )
  {
    if ( check_date_format( $_POST['date_creation_cat'] ) )
    {
      $date = date_convert( $_POST['date_creation_cat'] );
      $query = 'SELECT image_id';
      $query.= ' FROM '.PREFIX_TABLE.'image_category';
      $query.= ' WHERE category_id = '.$page['cat'];
      $result = mysql_query( $query );
      while ( $row = mysql_fetch_array( $result ) )
      {
        $query = 'UPDATE '.PREFIX_TABLE.'images';
        if ( $_POST['date_creation_cat'] == '' )
        {
          $query.= ' SET date_creation = NULL';
        }
        else
        {
          $query.= " SET date_creation = '".$date."'";
        }
        $query.= ' WHERE id = '.$row['image_id'];
        $query.= ';';
        mysql_query( $query );
      }
    }
    else
    {
      echo $lang['err_date'];
    }
  }
  if ( isset( $_POST['common_keywords'] ) and $_POST['keywords_cat'] != '' )
  {
    $query = 'SELECT id,keywords';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' LEFT JOIN '.PREFIX_TABLE.'image_category ON id = image_id';
    $query.= ' WHERE category_id = '.$page['cat'];
    $query.= ';';
    $result = mysql_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      $specific_keywords = explode( ',', $row['keywords'] );
      $common_keywords   = get_keywords( $_POST['keywords_cat'] );
      // first possiblity : adding the given keywords to all the pictures
      if ( $_POST['common_keywords'] == 'add' )
      {
        $keywords = array_merge( $specific_keywords, $common_keywords );
        $keywords = array_unique( $keywords );
      }
      // second possiblity : removing the given keywords from all pictures
      // (without deleting the other specific keywords
      if ( $_POST['common_keywords'] == 'remove' )
      {
        $keywords = array_diff( $specific_keywords, $common_keywords );
      }
      // cleaning the keywords array, sometimes, an empty value still remain
      $keywords = array_remove( $keywords, '' );
      // updating the picture with new keywords array
      $query = 'UPDATE '.PREFIX_TABLE.'images';
      $query.= ' SET keywords = ';
      if ( count( $keywords ) == 0 )
      {
        $query.= 'NULL';
      }
      else
      {
        $query.= '"';
        $i = 0;
        foreach ( $keywords as $keyword ) {
          if ( $i++ > 0 ) $query.= ',';
          $query.= $keyword;
        }
        $query.= '"';
      }
      $query.= ' WHERE id = '.$row['id'];
      $query.= ';';
      mysql_query( $query );
    }
  }
//--------------------------------------------------------- form initialization
  $page['nb_image_page'] = 5;

  if( !isset( $_GET['start'] )
      or !is_numeric( $_GET['start'] )
      or ( is_numeric( $_GET['start'] ) and $_GET['start'] < 0 ) )
  {
    $page['start'] = 0;
  }
  else
  {
    $page['start'] = $_GET['start'];
  }

  if ( is_numeric( $_GET['num'] ) and $_GET['num'] >= 0 )
  {
    $page['start'] =
      floor( $_GET['num'] / $page['nb_image_page'] ) * $page['nb_image_page'];
  }
  // retrieving category information
  $page['plain_structure'] = get_plain_structure();
  $result = get_cat_info( $page['cat'] );
  $cat['name'] = $result['name'];
  $cat['nb_images'] = $result['nb_images'];
//----------------------------------------------------- template initialization
  $sub = $vtp->Open('./template/'.$user['template'].'/admin/infos_image.vtp');
  $tpl = array( 'infoimage_general','author','infoimage_useforall','submit',
                'infoimage_creation_date','infoimage_detailed','thumbnail',
                'infoimage_title','infoimage_comment',
                'infoimage_creation_date','keywords',
                'infoimage_addtoall','infoimage_removefromall',
                'infoimage_keyword_separation','infoimage_associate' );
  templatize_array( $tpl, 'lang', $sub );
  $vtp->setGlobalVar( $sub, 'user_template',   $user['template'] );
//------------------------------------------------------------------------ form
  $url = './admin.php?page=infos_images&amp;cat_id='.$page['cat'];
  $url.= '&amp;start='.$page['start'];
  $vtp->setVar( $sub, 'form_action', add_session_id( $url ) ); 
  $page['navigation_bar'] = create_navigation_bar(
    $url, $cat['nb_images'],$page['start'], $page['nb_image_page'], '' );
  $vtp->setVar( $sub, 'navigation_bar', $page['navigation_bar'] );
  $cat_name = get_cat_display_name( $cat['name'], ' - ', 'font-style:italic;');
  $vtp->setVar( $sub, 'cat_name', $cat_name );

  $array_cat_directories = array();

  $query = 'SELECT id,file,comment,author,tn_ext,name,date_creation,keywords';
  $query.= ',storage_category_id,category_id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' LEFT JOIN '.PREFIX_TABLE.'image_category ON id = image_id';
  $query.= ' WHERE category_id = '.$page['cat'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$page['start'].','.$page['nb_image_page'];
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $vtp->addSession( $sub, 'picture' );
    $vtp->setVar( $sub, 'picture.id', $row['id'] );
    $vtp->setVar( $sub, 'picture.filename', $row['file'] );
    $vtp->setVar( $sub, 'picture.name', $row['name'] );
    $vtp->setVar( $sub, 'picture.author', $row['author'] );
    $vtp->setVar( $sub, 'picture.comment', $row['comment'] );
    $vtp->setVar( $sub, 'picture.keywords', $row['keywords'] );
    $vtp->setVar( $sub, 'picture.date_creation',
                  date_convert_back( $row['date_creation'] ) );
    $file = get_filename_wo_extension( $row['file'] );
    $vtp->setVar( $sub, 'picture.default_name', $file );
    // creating url to thumbnail
    if ( $array_cat_directories[$row['storage_category_id']] == '' )
    {
      $array_cat_directories[$row['storage_category_id']] =
        get_complete_dir( $row['storage_category_id'] );
    }
    $thumbnail_url = $array_cat_directories[$row['storage_category_id']];
    if ( preg_match( '/^\.\/galleries/', $thumbnail_url ) )
    {
      $thumbnail_url = '.'.$thumbnail_url;
    }
    $thumbnail_url.= 'thumbnail/';
    $thumbnail_url.= $conf['prefix_thumbnail'].$file.".".$row['tn_ext'];
    $vtp->setVar( $sub, 'picture.thumbnail_url', $thumbnail_url );
    $url = './admin.php?page=picture_modify&amp;image_id='.$row['id'];
    $vtp->setVar( $sub, 'picture.url', add_session_id( $url ) );
    $vtp->closeSession( $sub, 'picture' );
  }
  $structure = create_structure( '', array() );
  display_categories( $structure, '&nbsp;' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>