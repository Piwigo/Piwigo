<?php
/***************************************************************************
 *                                picture.php                              *
 *                            -------------------                          *
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
// this page shows the image full size
//----------------------------------------------------------- personnal include
include_once( './include/init.inc.php' );       
//-------------------------------------------------- access authorization check
check_cat_id( $_GET['cat'] );
check_login_authorization();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
}
//---------------------------------------- incrementation of the number of hits
$query = 'UPDATE '.PREFIX_TABLE.'images';
$query.= ' SET hit=hit+1';
$query.= ' WHERE id='.$_GET['image_id'];
$query.= ';';
@mysql_query( $query );
//-------------------------------------------------------------- initialization
initialize_category( 'picture' );
//------------------------------------- main picture information initialization
$query = 'SELECT id,date_available,comment,hit,keywords';
$query.= ',author,name,file,date_creation,filesize,width,height';
$query.= ',storage_category_id';
if ( is_numeric( $page['cat'] ) )
{
  $query.= ',category_id';
}
$query.= ' FROM '.PREFIX_TABLE.'images';
$query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
$query.= ' ON id = ic.image_id';
$query.= $page['where'];
$query.= ' AND id = '.$_GET['image_id'];
$query.= $conf['order_by'];
$query.= ';';
$result = mysql_query( $query );
// if this image_id doesn't correspond to this category, an error message is
// displayed, and execution is stopped
if ( mysql_num_rows( $result ) == 0 )
{
  echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
  echo '<a href="'.add_session_id( './category.php' ).'">';
  echo $lang['thumbnails'].'</a></div>';
  exit();
}
$row = mysql_fetch_array( $result );
$page['id']             = $row['id'];
$page['file']           = $row['file'];
$page['name']           = $row['name'];
$page['date_available'] = $row['date_available'];
$page['comment']        = $row['comment'];
$page['hit']            = $row['hit'];
$page['author']         = $row['author'];
$page['date_creation']  = $row['date_creation'];
$page['filesize']       = $row['filesize'];
$page['width']          = $row['width'];
$page['height']         = $row['height'];
if (is_numeric( $page['cat'] ))
	$page['category_id']    = $row['category_id'];
$page['keywords']       = $row['keywords'];
$page['storage_category_id'] = $row['storage_category_id'];
// retrieving the number of the picture in its category (in order)
$query = 'SELECT DISTINCT(id)';
$query.= ' FROM '.PREFIX_TABLE.'images';
$query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
$query.= ' ON id = ic.image_id';
$query.= $page['where'];
$query.= $conf['order_by'];
$query.= ';';
$result = mysql_query( $query );
$page['num'] = 0;
$row = mysql_fetch_array( $result );
while ( $row['id'] != $page['id'] )
{
  $page['num']++;
  $row = mysql_fetch_array( $result );
}
//--------------------------------------------------------- favorite management
if ( isset( $_GET['add_fav'] ) )
{
  if ( $_GET['add_fav'] == 1 )
  {
    // verify if the picture is already in the favorite of the user
    $query = 'SELECT COUNT(*) AS nb_fav';
    $query.= ' FROM '.PREFIX_TABLE.'favorites';
    $query.= ' WHERE image_id = '.$page['id'];
    $query.= ' AND user_id = '.$user['id'];
    $query.= ';';
    $result = mysql_query( $query );
    $row = mysql_fetch_array( $result );
    if ( $row['nb_fav'] == 0 )
    {
      $query = 'INSERT INTO '.PREFIX_TABLE.'favorites';
      $query.= ' (image_id,user_id) VALUES';
      $query.= ' ('.$page['id'].','.$user['id'].')';
      $query.= ';';
      $result = mysql_query( $query );
    }
  }
  if ( $_GET['add_fav'] == 0 )
  {
    $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
    $query.= ' WHERE user_id = '.$user['id'];
    $query.= ' AND image_id = '.$page['id'];
    $query.= ';';
    $result = mysql_query( $query );
    
    $page['cat_nb_images'] = $page['cat_nb_images'] - 1;
    if ( $page['cat_nb_images'] <= 0 )
    {
      // there is no favorite picture anymore
      // we redirect the user to the category page
      $url = add_session_id( 'category.php' );
      header( 'Request-URI: '.$url );
      header( 'Content-Location: '.$url );  
      header( 'Location: '.$url );
      exit();
    }
    // redirection of the user to the picture.php page
    // with the right picture
    $page['num'] = $page['num'] - 1;
    if ( $page['num'] == -1 )
    {
      $page['num'] = 0;
    }
    $query = 'SELECT id';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
    $query.= ' ON id = ic.image_id';
    $query.= $page['where'];
    $query.= $conf['order_by'];
    $query.= ' LIMIT '.$page['num'].',1';
    $query.= ';';
    $result = mysql_query( $query );
    $row = mysql_fetch_array( $result );
    $redirect = './picture.php?image_id='.$row['id'].'&cat='.$page['cat'];
    $redirect.= '&expand='.$_GET['expand'];
    if ( $page['cat'] == 'search' )
    {
      $redirect.= '&search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
    }
    $url = add_session_id( $redirect, true );
    header( 'Request-URI: '.$url );
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
}

//---------------------------------------------- next picture thumbnail display
$next = 0;
if ( $page['num'] < $page['cat_nb_images']-1 )
{
  $next = $page['num'] + 1;
  $query = 'SELECT DISTINCT(id),name,file,tn_ext,storage_category_id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
  $query.= ' ON id=ic.image_id';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$next.',1';
  $query.= ';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );

  if ( !isset($array_cat_directories[$row['storage_category_id']]))
  {
    $array_cat_directories[$row['storage_category_id']] =
      get_complete_dir( $row['storage_category_id'] );
  }
  $cat_directory = $array_cat_directories[$row['storage_category_id']];

  $file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
  $next_lien_thumbnail = $cat_directory.'thumbnail/';
  $next_lien_thumbnail.= $conf['prefix_thumbnail'].$file.".".$row['tn_ext'];
  
  if ( $row['name'] != "" )
  {
    $next_alt_thumbnail = $row['name'];
  }
  else
  {
    $next_alt_thumbnail = $file;
  }
  $next_title = $lang['next_image']." : ".$next_alt_thumbnail;

  $next_url_link = './picture.php?image_id='.$row['id'].'&amp;cat='.$page['cat'];
  $next_url_link.= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $next_url_link.= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
}
//----------------------------------------------------- template initialization
//
// Start output of page
//
//------------------------------------------------------------------ page title
$title = $page['name']; 
if ( $title == '')
{
  $title = str_replace("_"," ",get_filename_wo_extension($page['file']));
}
$refresh = 0;
if ( isset( $_GET['slideshow'] ) && isset($next_url_link)) 
{
	$refresh= $_GET['slideshow'];
	$url_link = $next_url_link;
}
include('include/page_header.php');

$handle = $vtp->Open( './template/'.$user['template'].'/picture.vtp' );
initialize_template();

$tpl = array( 'back','submit','comments_title','comments_del','delete',
              'comments_add','author','slideshow','slideshow_stop',
              'period_seconds' );
templatize_array( $tpl, 'lang', $handle );
//-------------------------------------------------------- slideshow management
if ( isset( $_GET['slideshow'] ) )
{
  if ( !is_numeric( $_GET['slideshow'] ) )
    $_GET['slideshow'] = $conf['slideshow_period'][0];
  $vtp->addSession( $handle, 'stop_slideshow' );
  $url = './picture.php';
  $url.= '?image_id='.$page['id'];
  $url.= '&amp;cat='.$page['cat'];
  $url.= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'];
    $url.= '&amp;mode='.$_GET['mode'];
  }
  $vtp->setVar( $handle, 'stop_slideshow.url', add_session_id( $url ) );
  $vtp->closeSession( $handle, 'stop_slideshow' );
}
else
{
  $vtp->addSession( $handle, 'start_slideshow' );
  foreach ( $conf['slideshow_period'] as $option ) {
    $vtp->addSession( $handle, 'second' );
    $vtp->setVar( $handle, 'second.option', $option );
    $url = './picture.php';
    $url.= '?image_id='.$page['id'];
    $url.= '&amp;cat='.$page['cat'];
	if (isset($_GET['expand']))
	    $url.= '&amp;expand='.$_GET['expand'];
    if ( $page['cat'] == 'search' )
    {
      $url.= '&amp;search='.$_GET['search'];
      $url.= '&amp;mode='.$_GET['mode'];
    }
    $url.= '&amp;slideshow='.$option;
    $vtp->setVar( $handle, 'second.url', add_session_id( $url ) );
    $vtp->closeSession( $handle, 'second' );
  }
  $vtp->closeSession( $handle, 'start_slideshow' );
}

//-------------------------------------------------- previous picture thumbnail
if ( $page['num'] >= 1 )
{
  $prev = $page['num'] - 1;
  $query = 'SELECT DISTINCT(id),name,file,tn_ext,storage_category_id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
  $query.= ' ON id=ic.image_id';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$prev.',1';
  $query.= ';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );

  if ( !isset($array_cat_directories[$row['storage_category_id']]) )
  {
    $array_cat_directories[$row['storage_category_id']] =
      get_complete_dir( $row['storage_category_id'] );
  }
  $cat_directory = $array_cat_directories[$row['storage_category_id']];

  $file = substr( $row['file'], 0, strrpos ( $row['file'], '.' ) );
  $lien_thumbnail = $cat_directory.'thumbnail/';
  $lien_thumbnail.= $conf['prefix_thumbnail'].$file.".".$row['tn_ext'];

  $prev_title = $lang['previous_image'].' : ';
  $alt_thumbnaill = '';
  if ( $row['name'] != '' ) $alt_thumbnail = $row['name'];
  else                      $alt_thumbnail = $file;
  $prev_title.= $alt_thumbnail;
  
  $url_link = './picture.php?image_id='.$row['id'].'&amp;cat='.$page['cat'];
  $url_link.= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $url_link.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
  // sending vars for display
  $vtp->addSession( $handle, 'previous' );
  $vtp->setGlobalVar( $handle, 'previous.url', add_session_id( $url_link ) );
  $vtp->setGlobalVar( $handle, 'previous.title', $prev_title );
  $vtp->setGlobalVar( $handle, 'previous.src', $lien_thumbnail );
  $vtp->setGlobalVar( $handle, 'previous.alt', $alt_thumbnail );
  $vtp->closeSession( $handle, 'previous' );
}
else
{
  $vtp->addSession( $handle, 'previous_empty' );
  $vtp->closeSession( $handle, 'previous_empty' );
}
//-------------------------------------------------------- main picture display
if ( is_numeric( $page['cat'] ) )
{
  $intitule_cat = get_cat_display_name( $page['cat_name'], " - ",
                                        "font-style:italic;" );
}
else
{
  $intitule_cat = $page['title'];
}

if ( !isset($array_cat_directories[$page['storage_category_id']]) )
{
  $array_cat_directories[$page['storage_category_id']] =
    get_complete_dir( $page['storage_category_id'] );
}
$cat_directory = $array_cat_directories[$page['storage_category_id']];

$n = $page['num'] + 1;
$intitule_titre = replace_space( $intitule_cat." - " ).$n.'/';
$intitule_titre.= $page['cat_nb_images']."<br />";
if ( $page['name'] != "" )
{
  $intitule_file = $page['name'];
}
else
{
  $intitule_file = str_replace( "_", " ",
                                substr( $page['file'], 0,
                                        strrpos ( $page['file'], ".") ) );
}
if ( $page['cat'] == 'search' )
{
  $intitule_file = replace_search( $intitule_file, $_GET['search'] );
}
$vtp->setGlobalVar( $handle, 'title', $intitule_titre.$intitule_file );

$lien_image = $cat_directory.$page['file'];

// calculation of width and height
if ( $page['width'] == "" )
{
  $taille_image = @getimagesize( $lien_image );
  $original_width = $taille_image[0];
  $original_height = $taille_image[1];
}
else
{
  $original_width = $page['width'];
  $original_height = $page['height'];
}

$picture_size = get_picture_size( $original_width, $original_height,
				  $user['maxwidth'], $user['maxheight'] );
$final_width  = $picture_size[0];
$final_height = $picture_size[1];
        
$url_link = './category.php?cat='.$page['cat'].'&amp;';
$url_link.= 'num='.$page['num']; 
if (isset($_GET['expand']))
	$url_link.='&amp;expand='.$_GET['expand'];
if ( $page['cat'] == 'search' )
{
  $url_link.= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
}
$vtp->setGlobalVar( $handle, 'picture_link', add_session_id( $url_link ) );
$vtp->setGlobalVar( $handle, 'picture_width', $final_width );
$vtp->setGlobalVar( $handle, 'picture_height', $final_height );
$vtp->setGlobalVar( $handle, 'picture_src', $lien_image );
$vtp->setGlobalVar( $handle, 'picture_alt', $page['file'] );

if ( $page['comment'] != '' )
{
  if ( $page['cat'] == 'search' )
  {
    $picture_comment = replace_search( $page['comment'], $_GET['search'] );
    $vtp->setGlobalVar( $handle, 'picture_comment', $picture_comment );
  }
  else
  {
    $vtp->setGlobalVar( $handle, 'picture_comment', $page['comment'] );
  }
}
//--------------------------------------------------------- picture information
// author
if ( $page['author'] != "" )
{
  $vtp->addSession( $handle, 'info_line' );
  $vtp->setVar( $handle, 'info_line.name', $lang['author'].' : ' );
  $vtp->setVar( $handle, 'info_line.content', $page['author'] );
  $vtp->closeSession( $handle, 'info_line' );
}
// creation date
if ( $page['date_creation'] != "" )
{
  $vtp->addSession( $handle, 'info_line' );
  $vtp->setVar( $handle, 'info_line.name', $lang['creation_date'].' : ' );
  $vtp->setVar( $handle, 'info_line.content',
                format_date( $page['date_creation'] ) );
  $vtp->closeSession( $handle, 'info_line' );
}
// date of availability
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['registration_date'].' : ' );
list( $year,$month,$day ) = explode( '-', $page['date_available'] );
$vtp->setVar( $handle, 'info_line.content',
              format_date( $page['date_available'] ) );
$vtp->closeSession( $handle, 'info_line' );
// size in pixels
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['size'].' : ' );
if ( $original_width != $final_width or $original_height != $final_height )
{
  $content = '[ <a href="'.$lien_image.'" title="'.$lang['true_size'].'">';
  $content.= $original_width.'*'.$original_height.'</a> ]';
  $vtp->setVar( $handle, 'info_line.content', $content );
}
else
{
  $content = $original_width.'*'.$original_height;
  $vtp->setVar( $handle, 'info_line.content', $content );
}
$vtp->closeSession( $handle, 'info_line' );
// file
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['file'].' : ' );
if ( $page['cat'] == 'search' )
{
  $content = replace_search( $page['file'], $_GET['search'] );
  $vtp->setVar( $handle, 'info_line.content', $content );
}
else
{
  $vtp->setVar( $handle, 'info_line.content', $page['file'] );
}
$vtp->closeSession( $handle, 'info_line' );
// filesize
if ( $page['filesize'] == "" )
{
  $poids = floor ( filesize( $lien_image ) / 1024 );
}
else
{
  $poids = $page['filesize'];
}
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['filesize'].' : ' );
$vtp->setVar( $handle, 'info_line.content', $poids.' KB' );
$vtp->closeSession( $handle, 'info_line' );
// keywords
if ( $page['keywords'] != '' )
{
  $vtp->addSession( $handle, 'info_line' );
  $vtp->setVar( $handle, 'info_line.name', $lang['keywords'].' : ' );
  $keywords = explode( ',', $page['keywords'] );
  $content = '';
  $url = './category.php?cat=search&amp;expand='.$_GET['expand'];
  $url.= '&amp;mode=OR&amp;search=';
  foreach ( $keywords as $i => $keyword ) {
    $local_url = add_session_id( $url.$keyword );
    if ( $i > 0 ) $content.= ',';
    $content.= '<a href="'.$local_url.'">'.$keyword.'</a>';
  }
  $vtp->setVar( $handle, 'info_line.content', $content );
  $vtp->closeSession( $handle, 'info_line' );
}
// number of visits
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['visited'].' : ' );
$vtp->setVar( $handle, 'info_line.content', $page['hit'].' '.$lang['times'] );
$vtp->closeSession( $handle, 'info_line' );
//------------------------------------------------------- favorite manipulation
if ( !$user['is_the_guest'] )
{
  // verify if the picture is already in the favorite of the user
  $query = 'SELECT COUNT(*) AS nb_fav FROM '.PREFIX_TABLE.'favorites WHERE image_id = '.$page['id'];
  $query.= ' AND user_id = '.$user['id'].';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  if (!$row['nb_fav'])
{
  $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$page['id'];
  if (isset($_GET['expand']))
	  $url.= '&amp;expand='.$_GET['expand'];
  $url.='&amp;add_fav=1';
  if ( $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
  $vtp->addSession( $handle, 'favorite' );
  $vtp->setVar( $handle, 'favorite.link', add_session_id( $url ) );
  $vtp->setVar( $handle, 'favorite.title', $lang['add_favorites_hint'] );
  $vtp->setVar( $handle, 'favorite.src',
                './template/'.$user['template'].'/theme/favorite.gif' );
  $vtp->setVar( $handle, 'favorite.alt','[ '.$lang['add_favorites_alt'].' ]' );
  $vtp->closeSession( $handle, 'favorite' );
}
else
{
  $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$page['id'];
  $url.= '&amp;expand='.$_GET['expand'].'&amp;add_fav=0';
  $vtp->addSession( $handle, 'favorite' );
  $vtp->setVar( $handle, 'favorite.link', add_session_id( $url ) );
  $vtp->setVar( $handle, 'favorite.title', $lang['del_favorites_hint'] );
  $vtp->setVar( $handle, 'favorite.src',
                './template/'.$user['template'].'/theme/del_favorite.gif' );
  $vtp->setVar( $handle, 'favorite.alt','[ '.$lang['del_favorites_alt'].' ]' );
  $vtp->closeSession( $handle, 'favorite' );
}
}
//------------------------------------ admin link for information modifications
if ( $user['status'] == 'admin' )
{
  $vtp->addSession( $handle, 'modification' );
  $url = './admin/admin.php?page=picture_modify&amp;cat_id='.$page['cat'];
  $url.= '&amp;image_id='.$page['id'];
  $vtp->setVar( $handle, 'modification.link', add_session_id( $url ) );
  $vtp->setVar( $handle, 'modification.name', $lang['link_info_image'] );
}

if ( $next )
{
  // sending vars for display
  $vtp->addSession( $handle,   'next' );
  $vtp->setGlobalVar( $handle, 'next.url', add_session_id( $next_url_link ) );
  $vtp->setGlobalVar( $handle, 'next.title', $next_title );
  $vtp->setGlobalVar( $handle, 'next.src', $next_lien_thumbnail );
  $vtp->setGlobalVar( $handle, 'next.alt', $next_alt_thumbnail );
  $vtp->closeSession( $handle, 'next' );
}
else
{
  $vtp->addSession( $handle, 'next_empty' );
  $vtp->closeSession( $handle, 'next_empty' );
}
//---------------------------------------------------- users's comments display
if ( $conf['show_comments'] )
{
  $vtp->addSession( $handle, 'comments' );
  // comment registeration
  if ( isset( $_POST['content'] ) and $_POST['content'] != '' )
  {
    $register_comment = true;

    if ( !$user['is_the_guest'] ) $author = $user['username'];
    if ( $_POST['author'] != '' ) $author = $_POST['author'];
    // if a guest try to use the name of an already existing user, he must
    // be rejected
    if ( isset( $author ) and $author != $user['username'] )
    {
      $query = 'SELECT COUNT(*) AS user_exists';
      $query.= ' FROM '.PREFIX_TABLE.'users';
      $query.= " WHERE username = '".$author."'";
      $query.= ';';
      $row = mysql_fetch_array( mysql_query( $query ) );
      if ( $row['user_exists'] == 1 )
      {
        $vtp->addSession( $handle, 'information' );
        $message = $lang['comment_user_exists'];
        $vtp->setVar( $handle, 'information.content', $message );
        $vtp->closeSession( $handle, 'information' );
        $register_comment = false;
      }
    }

    if ( $register_comment )
    {
      // anti-flood system
      $reference_date = time() - $conf['anti-flood_time'];
      $query = 'SELECT id';
      $query.= ' FROM '.PREFIX_TABLE.'comments';
      $query.= ' WHERE date > '.$reference_date;
      $query.= " AND author = '".$author."'";
      $query.= ';';
      if ( mysql_num_rows( mysql_query( $query ) ) == 0
           or $conf['anti-flood_time'] == 0 )
      {
        $query = 'INSERT INTO '.PREFIX_TABLE.'comments';
        $query.= ' (author,date,image_id,content,validated) VALUES';
        $query.= ' (';
        if ( !isset( $author ) ) $query.= 'NULL';
        else                     $query.= "'".$author."'";
        $query.= ','.time().','.$page['id'];
        $query.= ",'".htmlspecialchars( $_POST['content'], ENT_QUOTES)."'";
        if ( !$conf['comments_validation'] or $user['status'] == 'admin' )
          $query.= ",'true'";
        else
          $query.= ",'false'";
        $query.= ');';
        mysql_query( $query );
        // information message
        $vtp->addSession( $handle, 'information' );
        $message = $lang['comment_added'];
        if ( $conf['comments_validation'] and $user['status'] != 'admin' )
        {
          $message.= '<br />'.$lang['comment_to_validate'];
        }
        $vtp->setVar( $handle, 'information.content', $message );
        $vtp->closeSession( $handle, 'information' );
        // notification to the administrators
        if ( $conf['mail_notification'] )
        {
          $cat_name = get_cat_display_name( $page['cat_name'], ' > ', '' );
          $cat_name = strip_tags( $cat_name );
          if ( $page['name'] == '' ) $picture = $page['file'];
          else                       $picture = $page['name'];
          notify( 'comment', $cat_name.' > '.$picture );
        }
      }
      else
      {
        // information message
        $vtp->addSession( $handle, 'information' );
        $message = $lang['comment_anti-flood'];
        $vtp->setVar( $handle, 'information.content', $message );
        $vtp->closeSession( $handle, 'information' );
      }
    }
  }
  // comment deletion
  if ( isset( $_GET['del'] )
       and is_numeric( $_GET['del'] )
       and $user['status'] == 'admin' )
  {
    $query = 'DELETE FROM '.PREFIX_TABLE.'comments';
    $query.= ' WHERE id = '.$_GET['del'].';';
    mysql_query( $query );
  }
  // number of comment for this picture
  $query = 'SELECT COUNT(*) AS nb_comments';
  $query.= ' FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE image_id = '.$page['id'];
  $query.= " AND validated = 'true'";
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $page['nb_comments'] = $row['nb_comments'];
  // navigation bar creation
  $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$page['id'];
  if (isset($_GET['expand']))
  	$url.= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
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
  $page['navigation_bar'] = create_navigation_bar( $url, $page['nb_comments'],
                                                   $page['start'],
                                                   $conf['nb_comment_page'],
                                                   '' );
  // sending vars for display
  $vtp->setGlobalVar( $handle, 'navigation_bar', $page['navigation_bar'] );
  $vtp->setGlobalVar( $handle, 'nb_comments', $page['nb_comments'] );

  $query = 'SELECT id,author,date,image_id,content';
  $query.= ' FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE image_id = '.$page['id'];
  $query.= " AND validated = 'true'";
  $query.= ' ORDER BY date ASC';
  $query.= ' LIMIT '.$page['start'].', '.$conf['nb_comment_page'].';';
  $result = mysql_query( $query );
                
  while ( $row = mysql_fetch_array( $result ) )
  {
    $vtp->addSession( $handle, 'comment' );
    $author = $row['author'];
    if ( $row['author'] == '' ) $author = $lang['guest'];
    $vtp->setVar( $handle, 'comment.author', $author );
    $vtp->setVar( $handle, 'comment.date',
                  format_date( $row['date'], 'unix', true ) );
    $content = nl2br( $row['content'] );

    // replace _word_ by an underlined word
    $pattern = '/_([^\s]*)_/';
    $replacement = '<span style="text-decoration:underline;">\1</span>';
    $content = preg_replace( $pattern, $replacement, $content );

    // replace *word* by a bolded word
    $pattern = '/\*([^\s]*)\*/';
    $replacement = '<span style="font-weight:bold;">\1</span>';
    $content = preg_replace( $pattern, $replacement, $content );

    // replace /word/ by an italic word
    $pattern = '/\/([^\s]*)\//';
    $replacement = '<span style="font-style:italic;">\1</span>';
    $content = preg_replace( $pattern, $replacement, $content );
    
    $vtp->setVar( $handle, 'comment.content', $content );
    if ( $user['status'] == 'admin' )
    {
      $vtp->addSession( $handle, 'delete' );
      $vtp->setVar( $handle, 'delete.link',
                    add_session_id( $url.'&amp;del='.$row['id'] ) );
      $vtp->closeSession( $handle, 'delete' );
    }
    $vtp->closeSession( $handle, 'comment' );
  }

  if ( !$user['is_the_guest']
       or ( $user['is_the_guest'] and $conf['comments_forall'] ) )
  {
    $vtp->addSession( $handle, 'add_comment' );
    // form action
    $action = str_replace( '&', '&amp;', $_SERVER['REQUEST_URI'] );
    $vtp->setGlobalVar( $handle, 'form_action', $action );
    // display author field if the user is not logged in
    if ( !$user['is_the_guest'] )
    {
      $vtp->addSession( $handle, 'author_known' );
	  if (isset($user['pseudo']))
	      $vtp->setVar( $handle, 'author_known.value', $user['pseudo'] );
      $vtp->closeSession( $handle, 'author_known' );
    }
    else
    {
      $vtp->addSession( $handle, 'author_field' );
      $vtp->closeSession( $handle, 'author_field' );
    }
    $vtp->closeSession( $handle, 'add_comment' );
  }
  $vtp->closeSession( $handle, 'comments' );
}
//------------------------------------------------------------ log informations
pwg_log( 'picture', $intitule_cat, $page['file'] );
mysql_close();
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;

include('include/page_tail.php');
?>