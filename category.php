<?php
/***************************************************************************
 *                               category.php                              *
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
// determine the initial instant to indicate the generation time of this page
$t1 = explode( ' ', microtime() );
$t2 = explode( '.', $t1[0] );
$t2 = $t1[1].'.'.$t2[1];
//----------------------------------------------------------- personnal include
include_once( './include/init.inc.php' );
//-------------------------------------------------- access authorization check
check_cat_id( $_GET['cat'] );
check_login_authorization();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
}
//-------------------------------------------------------------- initialization
// creation of the array containing the cat ids to expand in the menu
// $page['tab_expand'] contains an array with the category ids
// $page['expand'] contains the string to display in URL with comma
$page['tab_expand'] = array();
if ( isset ( $_GET['expand'] ) and $_GET['expand'] != 'all' )
{
  $j = 0;
  $tab_expand = explode( ",", $_GET['expand'] );
  $size = sizeof( $tab_expand );
  for ( $i = 0; $i < $size; $i++ )
  {
    if ( is_numeric( $tab_expand[$i] ) )
    {
      $page['tab_expand'][$j++] = $tab_expand[$i];
    }
  }
  $page['expand'] = implode( ',', $page['tab_expand'] );
}
// in case of expanding all authorized cats
// The $page['expand'] equals 'all' and
// $page['tab_expand'] contains all the authorized cat ids
if ( $user['expand'] == 'true' or $_GET['expand'] == 'all' )
{
  $page['tab_expand'] = array();
  $query = 'select id';
  $query.= ' from '.$prefixeTable.'categories';
  $query.= ' where id_uppercat is null;';
  $result = mysql_query( $query );
  $i = 0;
  while ( $row = mysql_fetch_array( $result ) )
  {
    $page['tab_expand'][$i++] = $row['id'];
  }
  $page['expand'] = 'all';
}
// detection of the start picture to display
if ( !isset( $_GET['start'] )
     or !is_numeric( $_GET['start'] )
     or ( is_numeric( $_GET['start'] ) and $_GET['start'] < 0 ) )
{
  $page['start'] = 0;
}
else
{
  $page['start'] = $_GET['start'];
}
// Sometimes, a "num" is provided in the URL. It is the number
// of the picture to show. This picture must be in the thumbnails page.
// We have to find the right $page['start'] that show the num picture
// in this category
if ( is_numeric( $_GET['num'] ) and $_GET['num'] >= 0 )
{
  $page['start'] = floor( $_GET['num'] / $user['nb_image_page'] );
  $page['start']*= $user['nb_image_page'];
}
initialize_category();
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/category.vtp' );
initialize_template();

$tpl = array( 'categories','hint_category','sub-cat','images_available',
              'total','title_menu','nb_image_category','send_mail',
              'title_send_mail','generation_time','upload_name',
              'connected_user','recent_image','days','generation_time',
              'favorite_cat_hint','favorite_cat','stats',
              'most_visited_cat_hint','most_visited_cat','recent_cat',
              'recent_cat_hint' );
templatize_array( $tpl, 'lang' );

$tpl = array( 'mail_webmaster','webmaster','top_number','version','site_url' );
templatize_array( $tpl, 'conf' );

$tpl = array( 'short_period','long_period','style','lien_collapsed','pseudo' );
templatize_array( $tpl, 'user' );

$tpl = array( 'title','navigation_bar','cat_comment','cat_nb_images' );
templatize_array( $tpl, 'page' );

// special global template vars
$vtp->setGlobalVar( $handle, 'icon_short', get_icon( time() ) );
$icon_long = get_icon( time() - ( $user['short_period'] * 24 * 60 * 60 + 1 ) );
$vtp->setGlobalVar( $handle, 'icon_long', $icon_long );
$nb_total_pictures = get_total_image( "", $user['restrictions'] );
$vtp->setGlobalVar( $handle, 'nb_total_pictures',$nb_total_pictures );
//------------------------------------------------------------- categories menu
// normal categories
display_cat( '', '&nbsp;', $user['restrictions'], $page['tab_expand'] );
// favorites cat
if ( !$user['is_the_guest'] )
{
  $vtp->addSession( $handle, 'favorites' );
  $url = add_session_id('./category.php?cat=fav&amp;expand='.$page['expand'] );
  $vtp->setVar( $handle, 'favorites.url', $url );
  // searching the number of favorite picture
  $query = 'select count(*) as count';
  $query.= ' from '.$prefixeTable.'favorites';
  $query.= ' where user_id = '.$user['id'].';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  $vtp->setVar( $handle, 'favorites.nb_favorites', $row['count'] );
  $vtp->closeSession( $handle, 'favorites' );
}
// most visited pictures category
$url = add_session_id( './category.php?cat=most_visited'.
                       '&amp;expand='.$page['expand'] );
$vtp->setGlobalVar( $handle, 'most_visited_url', $url );
// recent pictures
$url = add_session_id( './category.php?cat=recent'.
                       '&amp;expand='.$page['expand'] );
$vtp->setGlobalVar( $handle, 'recent_url', $url );
//--------------------------------------------------------------------- summary
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.url', './identification.php' );
if ( !$user['is_the_guest'] )
{
  $vtp->setVar( $handle, 'summary.title', '' );
  $vtp->setVar( $handle, 'summary.name',
                replace_space( $lang['change_login'] ) );
}
else
{
  $vtp->setVar( $handle, 'summary.title', $lang['hint_login'] );
  $vtp->setVar( $handle, 'summary.name',
                replace_space( $lang['login'] ) );
}
$vtp->closeSession( $handle, 'summary' );
// links for registered users
if ( !$user['is_the_guest'] )
{
  // logout link
  $vtp->addSession( $handle, 'summary' );
  $vtp->setVar( $handle, 'summary.url', './category.php?cat='.$page['cat'] );
  $vtp->setVar( $handle, 'summary.title', '' );
  $vtp->setVar( $handle, 'summary.name', replace_space( $lang['logout'] ) );
  $vtp->closeSession( $handle, 'summary' );
  // customization link
  $vtp->addSession( $handle, 'summary' );
  $url = './profile.php?cat='.$page['cat'];
  $url.= '&amp;expand='.$page['expand'];
  if ( $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'];
  }
  $vtp->setVar( $handle, 'summary.url', add_session_id( $url ) );
  $vtp->setVar( $handle, 'summary.title', $lang['hint_customize'] );
  $vtp->setVar( $handle, 'summary.name', replace_space( $lang['customize'] ) );
  $vtp->closeSession( $handle, 'summary' );
}
// search link
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.url', add_session_id( './search.php' ) );
$vtp->setVar( $handle, 'summary.title', $lang['hint_search'] );
$vtp->setVar( $handle, 'summary.name', replace_space( $lang['search'] ) );
$vtp->closeSession( $handle, 'summary' );
// about link
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.url',
              add_session_id( './about.php?expand='.$page['expand'] ) );
$vtp->setVar( $handle, 'summary.title', $lang['hint_about'] );
$vtp->setVar( $handle, 'summary.name', replace_space( $lang['about'] ) );
$vtp->closeSession( $handle, 'summary' );
// administration link
if ( $user['status'] == 'admin' )
{
  $vtp->addSession( $handle, 'summary' );
  $vtp->setVar( $handle, 'summary.url',
                add_session_id( './admin/admin.php' ) );
  $vtp->setVar( $handle, 'summary.title', $lang['hint_admin'] );
  $vtp->setVar( $handle, 'summary.name', replace_space( $lang['admin'] ) );
  $vtp->closeSession( $handle, 'summary' );
}
//-------------------------------------------------------------- category title
if ( isset ( $page['cat'] ) )
{
  if ( is_numeric( $page['cat'] ) )
  {
    $cat_title = get_cat_display_name( $page['cat_name'], '<br />',
                                    'font-style:italic;' );
    $vtp->setGlobalVar( $handle, "cat_title", $cat_title );
  }
  else
  {
    if ( $page['cat'] == 'search' )
    {
      $page['title'].= ' : <span style="font-style:italic;">';
      $page['title'].= $_GET['search']."</span>";
    }
    $page['title'] = replace_space( $page['title'] );
    $vtp->setGlobalVar( $handle, "cat_title", $page['title'] );
  }
}
else
{
  $vtp->setGlobalVar( $handle, "cat_title",
                      replace_space( $lang['no_category'] ) );
}
//------------------------------------------------------------------ thumbnails
if ( isset( $page['cat'] ) and $page['cat_nb_images'] != 0 )
{
  if ( is_numeric( $page['cat'] ) )
  {
    $cat_directory = $page['cat_dir'];
  }
  else if ( $page['cat'] == 'search' or $page['cat'] == 'fav' )
  {
    $array_cat_directories = array();
  }
  
  $query = 'select id,file,date_available,comment,';
  $query.= ' author,tn_ext,name,filesize,width,height,cat_id';
  $query.= ' from '.$prefixeTable.'images';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' limit '.$page['start'].','.$page['nb_image_page'];
  $query.= ';';
  $result = mysql_query( $query );

  $vtp->addSession( $handle, 'thumbnails' );
  $vtp->addSession( $handle, 'line' );
  // compteur d'itération pour aller à la ligne
  // toutes les "$nb_image_ligne" images
  $i = 1;
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( !is_numeric( $page['cat'] ) )
    {
      if ( $array_cat_directories[$row['cat_id']] == '' )
      {
        $cat_result = get_cat_info( $row['cat_id'] );
        $array_cat_directories[$row['cat_id']] = $cat_result['dir'];
      }
      $cat_directory = $array_cat_directories[$row['cat_id']];
    }
    // filename without extension
    $file = substr ( $row['file'], 0, strrpos ( $row['file'], '.' ) );
    // name of the picture
    if ( $row['name'] != '' )
    {
      $name = $row['name'];
    }
    else
    {
      $name = str_replace( '_', ' ', $file );
    }
    if ( $page['cat'] == 'search' )
    {
      $name = replace_search( $name, $_GET['search'] );
    }
    // thumbnail url
    $thumbnail_url = $cat_directory;
    $thumbnail_url.= 'thumbnail/'.$conf['prefixe_thumbnail'];
    $thumbnail_url.= $file.'.'.$row['tn_ext'];
    // message in title for the thumbnail
    $tab_date = explode( '-', $row['date_available'] );
    $thumbnail_title = $lang['registration_date'];
    $thumbnail_title.= ' '.$tab_date[2].'/'.$tab_date[1].'/'.$tab_date[0];
    if ( $row['comment'] != '' )
    {             
      $thumbnail_title .= "\n".$lang['comment'].' : '.$row['comment'];
    }             
    if ( $row['author'] != '' )
    {             
      $thumbnail_title .= "\n".$lang['author'].' : '.$row['author'];
    }
    if ( $row['width'] == '' )
    {
      $taille_image = @getimagesize( $lien_image );
      $width = $taille_image[0];
      $height = $taille_image[1];
    }
    else
    {
      $width = $row['width'];
      $height = $row['height'];
    }
    $thumbnail_title .= "\n".$lang['size'].' : '.$width.'*'.$height;
    $thumbnail_title .= "\n".$lang['file'].' : '.$row['file'];
    if ( $row['filesize'] == '' )
    {
      $poids = floor( filesize( $lien_image ) / 1024 );
    }
    else
    {
      $poids = $row['filesize'];
    }
    $thumbnail_title .= "\n".$lang['filesize'].' : '.$poids.' KB';
    // url link on picture.php page
    $url_link = './picture.php?cat='.$page['cat'];
    $url_link.= '&amp;image_id='.$row['id'].'&amp;expand='.$page['expand'];
    if ( $page['cat'] == 'search' )
    {
      $url_link.= '&amp;search='.$_GET['search'];
    }
    // date of availability for creation icon
    $date = explode( '-', $row['date_available'] );
    $date = mktime( 0, 0, 0, $date[1], $date[2], $date[0] );
    // sending vars to display
    $vtp->addSession( $handle, 'thumbnail' );
    $vtp->setVar( $handle, 'thumbnail.url', add_session_id( $url_link ) );
    $vtp->setVar( $handle, 'thumbnail.src', $thumbnail_url );
    $vtp->setVar( $handle, 'thumbnail.alt', $row['file'] );
    $vtp->setVar( $handle, 'thumbnail.title', $thumbnail_title );
    $vtp->setVar( $handle, 'thumbnail.name', $name );
    $vtp->setVar( $handle, 'thumbnail.icon', get_icon( $date ) );

    if ( $conf['show_comments'] and $user['show_nb_comments'] )
    {
      $vtp->addSession( $handle, 'nb_comments' );
      $query = 'select count(*) as nb_comments';
      $query.= ' from '.$prefixeTable.'comments';
      $query.= ' where image_id = '.$row['id'];
      $query.= ';';
      $row = mysql_fetch_array( mysql_query( $query ) );
      $vtp->setVar( $handle, 'nb_comments.nb', $row['nb_comments'] );
      $vtp->closeSession( $handle, 'nb_comments' );
    }
    
    $vtp->closeSession( $handle, 'thumbnail' );
    
    if ( $i == $user['nb_image_line'] )
    {
      $vtp->closeSession( $handle, 'line' );
      $vtp->addSession( $handle, 'line' );
      $i = 1;
    }
    else
    {
      $i++;
    }
  }
  if ( $i < $user['nb_image_line'] )
  {
    $vtp->closeSession( $handle, 'line' );
  }
  $vtp->closeSession( $handle, 'thumbnails' );
}
elseif ( isset( $page['cat'] )
         and is_numeric( $page['cat'] )
         and $page['cat_nb_images'] == 0 )
{
  $vtp->addSession( $handle, 'thumbnails' );
  
  $query = 'select id,name,dir,date_dernier';
  $query.= ' from '.$prefixeTable.'categories';
  $query.= ' where id_uppercat = '.$page['cat'];
  $query.= ' order by rank;';
  $cat_result = mysql_query( $query );
  $i = 1;
  $vtp->addSession( $handle, 'line' );
  while ( $cat_row = mysql_fetch_array( $cat_result ) )
  {
    $result = get_cat_info( $cat_row['id'] );
    $cat_directory = $result['dir'];

    $name = '[ <span style="font-weight:bold;">';
    if ( $cat_row['name'] != '' )
    {
      $name.= $cat_row['name'];
    }
    else
    {
      $name.= $cat_row['dir'];
    }
    $name.= '</span> ]';
    $name = replace_space( $name );
    
    $query = 'select file,tn_ext';
    $query.= ' from '.$prefixeTable.'images';
    $query.= ' where cat_id = '.$cat_row['id'];
    $query.= ' order by rand()';
    $query.= ' limit 0,1';
    $query.= ';';
    $image_result = mysql_query( $query );
    $image_row = mysql_fetch_array( $image_result );

    $file = substr ( $image_row['file'], 0,
                     strrpos ( $image_row['file'], '.' ) );

    // creating links for thumbnail and associated category
    $lien_image = $cat_directory;
    $lien_thumbnail = $lien_image;
    $lien_thumbnail.= 'thumbnail/'.$conf['prefixe_thumbnail'];
    $lien_thumbnail.= $file.'.'.$image_row['tn_ext'];
    $lien_image.= $image_row['file'];

    $thumbnail_title = '';

    $url_link = './category.php?cat='.$cat_row['id'];
    if ( !in_array( $page['cat'], $page['tab_expand'] ) )
    {
      $page['tab_expand'][sizeof( $page['tab_expand'] )] = $page['cat'];
      $page['expand'] = implode( ',', $page['tab_expand'] );
    }
    $url_link.= '&amp;expand='.$page['expand'];

    // sending vars to display
    $vtp->addSession( $handle, 'thumbnail' );
    $vtp->setVar( $handle, 'thumbnail.url', add_session_id( $url_link ) );
    $vtp->setVar( $handle, 'thumbnail.src', $lien_thumbnail );
    $vtp->setVar( $handle, 'thumbnail.alt', $image_row['file'] );
    $vtp->setVar( $handle, 'thumbnail.title', $thumbnail_title );
    $vtp->setVar( $handle, 'thumbnail.name', $name );

    $date = explode( '-', $cat_row['date_dernier'] );
    $date = mktime( 0, 0, 0, $date[1], $date[2], $date[0] );
    $vtp->setVar( $handle, 'thumbnail.icon', get_icon( $date ) );

    $vtp->closeSession( $handle, 'thumbnail' );

    if ( $i == $user['nb_image_line'] )
    {
      $vtp->closeSession( $handle, 'line' );
      $vtp->addSession( $handle, 'line' );
      $i = 1;
    }
    else
    {
      $i++;
    }
  }
  $vtp->closeSession( $handle, 'thumbnails' );
}
//------------------------------------------------------- category informations
if ( isset ( $page['cat'] ) )
{
  $vtp->addSession( $handle, 'cat_infos' );
  // navigation bar
  if ( $page['navigation_bar'] != '' )
  {
    $vtp->addSession( $handle, 'navigation' );
    $vtp->closeSession( $handle, 'navigation' );
  }
  // category comment
  if ( isset( $page['comment'] ) and $page['comment'] != '' )
  {
    $vtp->addSession( $handle, 'cat_comment' );
    $vtp->closeSession( $handle, 'cat_navigation' );
  }
  // total number of pictures in the category
  if ( is_numeric( $page['cat'] ) )
  {
    $vtp->setVar( $handle, 'cat_infos.cat_name',
                  get_cat_display_name( $page['cat_name'], ' - ',
                                        'font-style:italic;' ) );
  }
  else
  {
    $vtp->setVar( $handle, 'cat_infos.cat_name', $page['title'] );
  }
  // upload a picture in the category
  if ( $page['cat_site_id'] == 1 and $conf['upload_available'] )
  {
    $vtp->addSession( $handle, 'upload' );
    $url = './upload.php?cat='.$page['cat'].'&amp;expand='.$page['expand'];
    $vtp->setVar( $handle, 'upload.url', add_session_id( $url ) );
    $vtp->closeSession( $handle, 'upload' );
  }
  $vtp->closeSession( $handle, 'cat_infos' );
}
//------------------------------------------------------------ log informations
pwg_log( 'category', $page['title'] );
mysql_close();
//------------------------------------------------------------- generation time
$time = get_elapsed_time( $t2, get_moment() );
$vtp->setGlobalVar( $handle, 'time', $time );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>