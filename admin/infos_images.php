<?php
/***************************************************************************
 *                             infos_images.php                            *
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
include_once( '../template/'.$user['template'].'/htmlfunctions.inc.php' );
//------------------------------------------------------------------- functions
function check_date_format ( $date )
{
  // date arrives at this format : DD/MM/YYYY
  // checkdate ( int month, int day, int year)
  $tab_date = explode( '/', $date );
  return checkdate ( $tab_date[1], $tab_date[0], $tab_date[2]);
}

function date_convert( $date )
{
  // date arrives at this format : DD/MM/YYYY
  // It must be transformed in YYYY-MM-DD
  $tab_date = explode( '/', $date );
  return $tab_date[2].'-'.$tab_date[1].'-'.$tab_date[0];
}

function date_convert_back( $date )
{
  // date arrives at this format : YYYY-MM-DD
  // It must be transformed in DD/MM/YYYY
  if ( $date != '' )
  {
    $tab_date = explode( '-', $date );
    return $tab_date[2].'/'.$tab_date[1].'/'.$tab_date[0];
  }
  else
  {
    return '';
  }
}
//-------------------------------------------------------------- initialization
check_cat_id( $_GET['cat_id'] );

if ( isset( $page['cat'] ) )
{
//--------------------------------------------------- update individual options
  $query = 'SELECT id,file';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE cat_id = '.$page['cat'];
  $query.= ';';
  $result = mysql_query( $query );
  $i = 1;
  while ( $row = mysql_fetch_array( $result ) )
  {
    $name          = 'name-'.$row['id'];
    $author        = 'author-'.$row['id'];
    $comment       = 'comment-'.$row['id'];
    $date_creation = 'date_creation-'.$row['id'];
    if ( isset( $_POST[$name] ) )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'images';
      if ( $_POST[$name] == '' )
      {
        $query.= ' SET name = NULL';
      }
      else
      {
        $query.= " SET name = '".htmlentities( $_POST[$name], ENT_QUOTES )."'";
      }
      if ( $_POST[$author] == '' )
      {
        $query.= ', author = NULL';
      }
      else
      {
        $query.= ", author = '".htmlentities($_POST[$author],ENT_QUOTES)."'";
      }
      if ( $_POST[$comment] == '' )
      {
        $query.= ', comment = NULL';
      }
      else
      {
        $query.= ", comment = '".htmlentities($_POST[$comment],ENT_QUOTES)."'";
      }
      if ( check_date_format( $_POST[$date_creation] ) )
      {
        $date = date_convert( $_POST[$date_creation] );
        $query.= ", date_creation = '".$date."'";
      }
      else if ( $_POST[$date_creation] == '' )
      {
        $query.= ', date_creation = NULL';
      }
      $query.= ' WHERE id = '.$row['id'];
      $query.= ';';
      mysql_query( $query );
    }
  }
//------------------------------------------------------ update general options
  if ( $_POST['use_common_author'] == 1 )
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
    $query.= ' WHERE cat_id = '.$page['cat'];
    $query.= ';';
    mysql_query( $query );
  }
  if ( $_POST['use_common_date_creation'] == 1 )
  {
    if ( check_date_format( $_POST['date_creation_cat'] ) )
    {
      $date = date_convert( $_POST['date_creation_cat'] );
      $query = 'UPDATE '.PREFIX_TABLE.'images';
      if ( $_POST['date_creation_cat'] == '' )
      {
        $query.= ' SET date_creation = NULL';
      }
      else
      {
        $query.= " SET date_creation = '".$date."'";
      }
      $query.= ' WHERE cat_id = '.$page['cat'];
      $query.= ';';
      mysql_query( $query );
    }
    else
    {
      echo $lang['infoimage_err_date'];
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
  $result = get_cat_info( $page['cat'] );
  $cat['local_dir'] = $result['local_dir'];
  $cat['dir'] = $result['dir'];
  $cat['name'] = $result['name'];
  $cat['site_id'] = $result['site_id'];
  $cat['nb_images'] = $result['nb_images'];
//----------------------------------------------------- template initialization
  $sub = $vtp->Open('../template/'.$user['template'].'/admin/infos_image.vtp');
  $tpl = array( 'infoimage_general','author','infoimage_useforall','submit',
                'infoimage_creation_date','infoimage_detailed','thumbnail',
                'infoimage_title','infoimage_comment',
                'infoimage_creation_date' );
  templatize_array( $tpl, 'lang', $sub );
//------------------------------------------------------------------------ form
  $url = './admin.php?page=infos_images&amp;cat_id='.$page['cat'];
  $url.= '&amp;start='.$page['start'];
  $vtp->setVar( $sub, 'form_action', add_session_id( $url ) ); 
  $page['navigation_bar'] = create_navigation_bar(
    $url, $cat['nb_images'],$page['start'], $page['nb_image_page'], '' );
  $vtp->setVar( $sub, 'navigation_bar', $page['navigation_bar'] );
  $cat_name = get_cat_display_name( $cat['name'], ' - ', 'font-style:italic;');
  $vtp->setVar( $sub, 'cat_name', $cat_name );

  $query = 'SELECT id,file,comment,author,tn_ext,name,date_creation';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' WHERE cat_id = '.$page['cat'];
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
    $vtp->setVar( $sub, 'picture.date_creation',
                  date_convert_back( $row['date_creation'] ) );
    $file = get_filename_wo_extension( $row['file'] );
    $vtp->setVar( $sub, 'picture.default_name', $file );
    // creating url to thumbnail
    if ( $cat['site_id'] == 1 )
    { 
      $thumbnail_url = '../galleries/'.$cat['local_dir'].'/';
    }
    else
    {
      $thumbnail_url = $cat['dir'];
    }
    $thumbnail_url.= 'thumbnail/';
    $thumbnail_url.= $conf['prefixe_thumbnail'].$file.".".$row['tn_ext'];
    $vtp->setVar( $sub, 'picture.thumbnail_url', $thumbnail_url );
    $vtp->closeSession( $sub, 'picture' );
  }
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>