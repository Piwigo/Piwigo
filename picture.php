<?php
/***************************************************************************
 *                                picture.php                              *
 *                            -------------------                          *
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
$cat_directory = $page['cat_dir']; // by default
//------------------------------------- main picture information initialization
$query = 'SELECT id,date_available,comment,hit';
$query.= ',author,name,file,date_creation,filesize,width,height,cat_id';
$query.= ' FROM '.PREFIX_TABLE.'images';
$query.= $page['where'];
$query.= ' AND id = '.$_GET['image_id'];
$query.= $conf['order_by'];
$query.= ';';
$result = mysql_query( $query );
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
$page['cat_id']         = $row['cat_id'];
// retrieving the number of the picture in its category (in order)
$query = 'SELECT id';
$query.= ' FROM '.PREFIX_TABLE.'images';
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
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/picture.vtp' );
initialize_template();

$tpl = array( 'back','submit','comments_title','comments_del','delete',
              'comments_add','author','slideshow','slideshow_stop',
              'period_seconds' );
templatize_array( $tpl, 'lang', $handle );
$vtp->setGlobalVar( $handle, 'user_template', $user['template'] );
$vtp->setGlobalVar( $handle, 'text_color', $user['couleur_text'] );
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
//------------------------------------------------------------------ page title
if ( $page['name'] != '' )
{
  $vtp->setGlobalVar( $handle, 'page_title', $page['name'] );
}
else
{
  $vtp->setGlobalVar( $handle, 'page_title', $page['file'] );
}
//-------------------------------------------------- previous picture thumbnail
if ( $page['num'] >= 1 )
{
  $prev = $page['num'] - 1;
  $query = 'SELECT id,name,file,tn_ext,cat_id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$prev.',1';
  $query.= ';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );

  if ( !is_numeric( $page['cat'] ) )
  {
    if ( $array_cat_directories[$row['cat_id']] == '' )
    {
      $cat_result = get_cat_info( $row['cat_id'] );
      $array_cat_directories[$row['cat_id']] = $cat_result['dir'];
    }
    $cat_directory = $array_cat_directories[$row['cat_id']];
  }
                
  $file = substr ( $row['file'], 0, strrpos ( $row['file'], '.' ) );
  $lien_thumbnail = $cat_directory.'/thumbnail/';
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
  $cat_result = get_cat_info( $page['cat_id'] );
  if ( $array_cat_directories[$page['cat_id']] == "" )
  {
    $array_cat_directories[$page['cat_id']] = $cat_result['dir'];
  }
  $cat_directory = $array_cat_directories[$page['cat_id']];
  $intitule_cat = $page['title'];
}
$n = $page['num'] + 1;
$intitule_titre = replace_space( $intitule_cat." - " ).$n.'/'.
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
$url_link.= 'num='.$page['num'].'&amp;expand='.$_GET['expand'];
if ( $page['cat'] == 'search' )
{
  $url_link.= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
}
$vtp->setGlobalVar( $handle, 'picture_link', add_session_id( $url_link ) );
$vtp->setGlobalVar( $handle, 'picture_width', $final_width );
$vtp->setGlobalVar( $handle, 'picture_height', $final_height );
$vtp->setGlobalVar( $handle, 'picture_border_color', $user['couleur_text'] );
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
  list( $year,$month,$day ) = explode( '-', $page['date_creation'] );
  $vtp->setVar( $handle, 'info_line.content',
                $day.'/'.$month.'/'.$year );
  $vtp->closeSession( $handle, 'info_line' );
}
// date of availability
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['registration_date'].' : ' );
list( $year,$month,$day ) = explode( '-', $page['date_available'] );
$vtp->setVar( $handle, 'info_line.content',
              $day.'/'.$month.'/'.$year );
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
// number of visits
$vtp->addSession( $handle, 'info_line' );
$vtp->setVar( $handle, 'info_line.name', $lang['visited'].' : ' );
$vtp->setVar( $handle, 'info_line.content', $page['hit'].' '.$lang['times'] );
$vtp->closeSession( $handle, 'info_line' );
//------------------------------------------------------- favorite manipulation
if ( $page['cat'] != 'fav' and !$user['is_the_guest'] )
{
  $url = './picture.php?cat='.$page['cat'].'&amp;image_id='.$page['id'];
  $url.= '&amp;expand='.$_GET['expand'].'&amp;add_fav=1';
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
if ( $page['cat'] == 'fav' )
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
//------------------------------------ admin link for information modifications
if ( $user['status'] == "admin" and is_numeric( $page['cat'] ) )
{
  $vtp->addSession( $handle, 'modification' );
  $url = './admin/admin.php?page=infos_images&amp;cat_id='.$page['cat'];
  $url.= '&amp;num='.$page['num'];
  $vtp->setVar( $handle, 'modification.link',
                add_session_id( $url )."#".$page['id'] );
  $vtp->setVar( $handle, 'modification.name', $lang['link_info_image'] );
}
//---------------------------------------------- next picture thumbnail display
if ( $page['num'] < $page['cat_nb_images']-1 )
{
  $next = $page['num'] + 1;
  $query = 'SELECT id,name,file,tn_ext,cat_id';
  $query.= ' FROM '.PREFIX_TABLE.'images';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$next.',1';
  $query.= ';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
                
  if ( !is_numeric( $page['cat'] ) )
  {
    if ( $array_cat_directories[$row['cat_id']] == "" )
    {
      $cat_result = get_cat_info( $row['cat_id'] );
      $array_cat_directories[$row['cat_id']] = $cat_result['dir'];
    }
    $cat_directory = $array_cat_directories[$row['cat_id']];
  }

  $file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
  $lien_thumbnail = $cat_directory.'thumbnail/';
  $lien_thumbnail.= $conf['prefix_thumbnail'].$file.".".$row['tn_ext'];
  
  if ( $row['name'] != "" )
  {
    $alt_thumbnail = $row['name'];
  }
  else
  {
    $alt_thumbnail = $file;
  }
  $next_title = $lang['next_image']." : ".$alt_thumbnail;

  $url_link = './picture.php?image_id='.$row['id'].'&amp;cat='.$page['cat'];
  $url_link.= '&amp;expand='.$_GET['expand'];
  if ( $page['cat'] == 'search' )
  {
    $url_link.= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
  }
  // sending vars for display
  $vtp->addSession( $handle,   'next' );
  $vtp->setGlobalVar( $handle, 'next.url', add_session_id( $url_link ) );
  $vtp->setGlobalVar( $handle, 'next.title', $next_title );
  $vtp->setGlobalVar( $handle, 'next.src', $lien_thumbnail );
  $vtp->setGlobalVar( $handle, 'next.alt', $alt_thumbnail );
  $vtp->closeSession( $handle, 'next' );
  // slideshow
  if ( isset( $_GET['slideshow'] ) )
  {
    $vtp->addSession( $handle, 'refresh' );
    $vtp->setVar( $handle, 'refresh.time', 2 );
    $url = $url_link.'&amp;slideshow='.$_GET['slideshow'];
    $vtp->setVar( $handle, 'refresh.url', add_session_id( $url ) );
    $vtp->closeSession( $handle, 'refresh' );
  }
}
else
{
  $vtp->addSession( $handle, 'previous_empty' );
  $vtp->closeSession( $handle, 'previous_empty' );
}
//---------------------------------------------------- users's comments display
if ( $conf['show_comments'] )
{
  $vtp->addSession( $handle, 'comments' );
  // comment registeration
  if ( isset( $_POST['content'] ) and $_POST['content'] != '' )
  {
    $author = $user['username'];
    if ( $_POST['author'] != '' ) $author = $_POST['author'];

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
      $query.= " ('".$author."',".time().",".$page['id'];
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
    $vtp->setVar( $handle, 'comment.author', $row['author'] );
    $displayed_date = $lang['day'][date( "w", $row['date'] )];
    $displayed_date.= date( " j ", $row['date'] );
    $displayed_date.= $lang['month'][date( "n", $row['date'] )];
    $displayed_date.= date( ' Y G:i', $row['date'] );
    $vtp->setVar( $handle, 'comment.date', $displayed_date );
    $vtp->setVar( $handle, 'comment.content', nl2br( $row['content'] ) );
    if ( $user['status'] == 'admin' )
    {
      $vtp->addSession( $handle, 'delete' );
      $vtp->setVar( $handle, 'delete.link',
                    add_session_id( $url.'&amp;del='.$row['id'] ) );
      $vtp->closeSession( $handle, 'delete' );
    }
    $vtp->closeSession( $handle, 'comment' );
  }
  // form action
  $action = str_replace( '&', '&amp;', $_SERVER['REQUEST_URI'] );
  $vtp->setGlobalVar( $handle, 'form_action', $action );
  // display author field if the user is not logged in
  if ( !$user['is_the_guest'] )
  {
    $vtp->addSession( $handle, 'author_known' );
    $vtp->setVar( $handle, 'author_known.value', $user['pseudo'] );
    $vtp->closeSession( $handle, 'author_known' );
  }
  else
  {
    $vtp->addSession( $handle, 'author_field' );
    $vtp->closeSession( $handle, 'author_field' );
  }
  $vtp->closeSession( $handle, 'comments' );
}
//------------------------------------------------------------ log informations
pwg_log( 'picture', $intitule_cat, $page['file'] );
mysql_close();
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>