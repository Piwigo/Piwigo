<?php
/***************************************************************************
 *                               category.php                              *
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
//----------------------------------------------------------- personnal include
include_once( './include/init.inc.php' );
//---------------------------------------------------------------------- logout
if ( isset( $_GET['act'] )
     and $_GET['act'] == 'logout'
     and isset( $_COOKIE['id'] ) )
{
  // cookie deletion if exists
  setcookie( 'id', '', 0, cookie_path() );
  $url = 'category.php';
  header( 'Request-URI: '.$url );  
  header( 'Content-Location: '.$url );  
  header( 'Location: '.$url );
  exit();
}
//-------------------------------------------------- access authorization check
if ( isset( $_GET['cat'] ) ) check_cat_id( $_GET['cat'] );
check_login_authorization();
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
}
//-------------------------------------------------------------- initialization
// detection of the start picture to display
if ( !isset( $_GET['start'] )
     or !is_numeric( $_GET['start'] )
     or ( is_numeric( $_GET['start'] ) and $_GET['start'] < 0 ) )
  $page['start'] = 0;
else
  $page['start'] = $_GET['start'];

initialize_category();

// creation of the array containing the cat ids to expand in the menu
// $page['tab_expand'] contains an array with the category ids
// $page['expand'] contains the string to display in URL with comma
$page['tab_expand'] = array();
if ( isset ( $_GET['expand'] ) and $_GET['expand'] != 'all' )
{
  $tab_expand = explode( ',', $_GET['expand'] );
  foreach ( $tab_expand as $id ) {
    if ( is_numeric( $id ) ) array_push( $page['tab_expand'], $id );
  }
}
if ( isset($page['cat']) && is_numeric( $page['cat'] ) )
{
  // the category displayed (in the URL cat=23) must be seen in the menu ->
  // parent categories must be expanded
  $uppercats = explode( ',', $page['uppercats'] );
  foreach ( $uppercats as $uppercat ) {
    array_push( $page['tab_expand'], $uppercat );
  }
}
$page['tab_expand'] = array_unique( $page['tab_expand'] );
$page['expand'] = implode( ',', $page['tab_expand'] );
// in case of expanding all authorized cats
// The $page['expand'] equals 'all' and
// $page['tab_expand'] contains all the authorized cat ids
if ( $user['expand']
     or ( isset( $_GET['expand'] ) and $_GET['expand'] == 'all' ) )
{
  $page['tab_expand'] = array();
  $page['expand'] = 'all';
}
// Sometimes, a "num" is provided in the URL. It is the number
// of the picture to show. This picture must be in the thumbnails page.
// We have to find the right $page['start'] that show the num picture
// in this category
if ( isset( $_GET['num'] )
     and is_numeric( $_GET['num'] )
     and $_GET['num'] >= 0 )
{
  $page['start'] = floor( $_GET['num'] / $user['nb_image_page'] );
  $page['start']*= $user['nb_image_page'];
}
// creating the structure of the categories (useful for displaying the menu)
// creating the plain structure : array of all the available categories and
// their relative informations, see the definition of the function
// get_user_plain_structure for further details.
$page['plain_structure'] = get_user_plain_structure();
$page['structure'] = create_user_structure( '' );
$page['structure'] = update_structure( $page['structure'] );

//----------------------------------------------------- template initialization

//
// Start output of page
//
$title = $page['title'];
include('include/page_header.php');

$handle = $vtp->Open( './template/'.$user['template'].'/category.vtp' );
initialize_template();
$tpl = array(
  'categories','hint_category','sub-cat','images_available','total',
  'title_menu','nb_image_category','send_mail','title_send_mail',
  'connected_user','recent_image','days',
  'favorite_cat_hint','favorite_cat','stats','most_visited_cat_hint',
  'most_visited_cat','recent_cat','recent_cat_hint','upload_picture',
  'comments' );
templatize_array( $tpl, 'lang', $handle );

$tpl = array( 'mail_webmaster','webmaster','top_number');
templatize_array( $tpl, 'conf', $handle );

$tpl = array( 'short_period','long_period','lien_collapsed', 'username' );
templatize_array( $tpl, 'user', $handle );

$tpl = array( 'navigation_bar','cat_comment','cat_nb_images' );
templatize_array( $tpl, 'page', $handle );

// special global template vars
$vtp->setGlobalVar( $handle, 'icon_short', get_icon( time() ) );
$icon_long = get_icon( time() - ( $user['short_period'] * 24 * 60 * 60 + 1 ) );
$vtp->setGlobalVar( $handle, 'icon_long', $icon_long );
$nb_total_pictures = count_user_total_images();
$vtp->setGlobalVar( $handle, 'nb_total_pictures',$nb_total_pictures );

//------------------------------------------------------------- categories menu
$vtp->setVar( $handle, 'home_url', add_session_id( 'category.php' ) );
// normal categories
foreach ( $page['structure'] as $category ) {
  // display category is a function relative to the template
  display_category( $category, '&nbsp;', $handle );
}
// favorites cat
if ( !$user['is_the_guest'] )
{
  $vtp->addSession( $handle, 'favorites' );
  $url = './category.php?cat=fav&amp;expand='.$page['expand'];
  $vtp->setVar( $handle, 'favorites.url', add_session_id( $url ) );
  // searching the number of favorite picture
  $query = 'SELECT COUNT(*) AS count';
  $query.= ' FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user['id'].';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  $vtp->setVar( $handle, 'favorites.nb_favorites', $row['count'] );
  $vtp->closeSession( $handle, 'favorites' );
}
// most visited pictures category
$url = './category.php?cat=most_visited&amp;expand='.$page['expand'];
$vtp->setGlobalVar( $handle, 'most_visited_url', add_session_id( $url ) );
// recent pictures
$url = './category.php?cat=recent&amp;expand='.$page['expand'];
$vtp->setGlobalVar( $handle, 'recent_url', add_session_id( $url ) );
//--------------------------------------------------------------------- summary
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.url', './identification.php' );
if ( !$user['is_the_guest'] )
{
  $vtp->setVar( $handle, 'summary.title', '' );
  $vtp->setVar( $handle, 'summary.name',replace_space($lang['change_login']));
}
else
{
  $vtp->setVar( $handle, 'summary.title', $lang['hint_login'] );
  $vtp->setVar( $handle, 'summary.name',  replace_space( $lang['menu_login']));
}
$vtp->closeSession( $handle, 'summary' );
// links for registered users
if ( !$user['is_the_guest'] )
{
  // logout link
  $vtp->addSession( $handle, 'summary' );
  $vtp->setVar( $handle, 'summary.url', './category.php?act=logout' );
  $vtp->setVar( $handle, 'summary.title', '' );
  $vtp->setVar( $handle, 'summary.name', replace_space( $lang['logout'] ) );
  $vtp->closeSession( $handle, 'summary' );
  // customization link
  $vtp->addSession( $handle, 'summary' );
  $url = './profile.php';
  if (isset($page['cat']) && isset($page['expand']))
  	$url.='?cat='.$page['cat'].'&amp;expand='.$page['expand'];
  if ( isset($page['cat']) && $page['cat'] == 'search' )
  {
    $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
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
// comments link
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.url', add_session_id( './comments.php' ) );
$vtp->setVar( $handle, 'summary.title', $lang['hint_comments'] );
$vtp->setVar( $handle, 'summary.name', replace_space( $lang['comments'] ) );
$vtp->closeSession( $handle, 'summary' );
// about link
$vtp->addSession( $handle, 'summary' );
$vtp->setVar( $handle, 'summary.url', './about.php?'.
              str_replace( '&', '&amp;', $_SERVER['QUERY_STRING'] ) );
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
  $array_cat_directories = array();
  
  $query = 'SELECT distinct(id),file,date_available,tn_ext,name,filesize';
  $query.= ',storage_category_id';
  $query.= ' FROM '.PREFIX_TABLE.'images AS i';
  $query.=' INNER JOIN '.PREFIX_TABLE.'image_category AS ic ON id=ic.image_id';
  $query.= $page['where'];
  $query.= $conf['order_by'];
  $query.= ' LIMIT '.$page['start'].','.$page['nb_image_page'];
  $query.= ';';
  $result = mysql_query( $query );

  $vtp->addSession( $handle, 'thumbnails' );
  $vtp->addSession( $handle, 'line' );
  // iteration counter to use a new <tr> every "$nb_image_line" pictures
  $cell_number = 1;
  // iteration counter to be sure not to create too much lines in the table
  $line_number = 1;
  while ( $row = mysql_fetch_array( $result ) )
  {
    // retrieving the storage dir of the picture
    if ( !isset($array_cat_directories[$row['storage_category_id']]))
    {
      $array_cat_directories[$row['storage_category_id']] =
        get_complete_dir( $row['storage_category_id'] );
    }
    $cat_directory = $array_cat_directories[$row['storage_category_id']];

    $file = get_filename_wo_extension( $row['file'] );
    // name of the picture
    if ( isset( $row['name'] ) and $row['name'] != '' ) $name = $row['name'];
    else $name = str_replace( '_', ' ', $file );

    if ( $page['cat'] == 'search' )
    {
      $name = replace_search( $name, $_GET['search'] );
    }
    // thumbnail url
    $thumbnail_url = $cat_directory;
    $thumbnail_url.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $thumbnail_url.= $file.'.'.$row['tn_ext'];
    // message in title for the thumbnail
    $thumbnail_title = $row['file'];
    if ( $row['filesize'] == '' )
      $poids = floor( filesize( $cat_directory.$row['file'] ) / 1024 );
    else
      $poids = $row['filesize'];
    $thumbnail_title .= ' : '.$poids.' KB';
    // url link on picture.php page
    $url_link = './picture.php?cat='.$page['cat'];
    $url_link.= '&amp;image_id='.$row['id'].'&amp;expand='.$page['expand'];
    if ( $page['cat'] == 'search' )
    {
      $url_link.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
    }
    // date of availability for creation icon
    list( $year,$month,$day ) = explode( '-', $row['date_available'] );
    $date = mktime( 0, 0, 0, $month, $day, $year );
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
      $query = 'SELECT COUNT(*) AS nb_comments';
      $query.= ' FROM '.PREFIX_TABLE.'comments';
      $query.= ' WHERE image_id = '.$row['id'];
      $query.= " AND validated = 'true'";
      $query.= ';';
      $row = mysql_fetch_array( mysql_query( $query ) );
      $vtp->setVar( $handle, 'nb_comments.nb', $row['nb_comments'] );
      $vtp->closeSession( $handle, 'nb_comments' );
    }
    
    $vtp->closeSession( $handle, 'thumbnail' );
    
    if ( $cell_number++ == $user['nb_image_line'] )
    {
      // creating a new line
      $vtp->closeSession( $handle, 'line' );
      // the number of the next cell is 1
      $cell_number = 1;
      // we only create a new line if it does not exceed the maximum line
      // per page for the logged user
      if ( $line_number++ < $user['nb_line_page'] )
      {
        $vtp->addSession( $handle, 'line' );
      }
    }
  }
  $vtp->closeSession( $handle, 'thumbnails' );
}
//-------------------------------------------------------------- empty category
elseif ( ( isset( $page['cat'] )
           and is_numeric( $page['cat'] )
           and $page['cat_nb_images'] == 0
           and $page['plain_structure'][$page['cat']]['nb_sub_categories'] > 0)
         or (!isset($_GET['cat'])))
{
  $vtp->addSession( $handle, 'thumbnails' );
  $vtp->addSession( $handle, 'line' );

  $subcats=array();
  if (isset($page['cat'])) $subcats = get_non_empty_subcat_ids( $page['cat'] );
  else                     $subcats = get_non_empty_subcat_ids( '' );
  $cell_number = 1;
  $i = 0;
  foreach ( $subcats as $subcat_id => $non_empty_id ) {
    $name = '<img src="'.$user['lien_collapsed'].'" style="border:none;"';
    $name.= ' alt="&gt;"/> ';
    $name.= '[ <span style="font-weight:bold;">';
    $name.= $page['plain_structure'][$subcat_id]['name'];
    $name.= '</span> ]';

    // searching the representative picture of the category
    $query = 'SELECT representative_picture_id';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$non_empty_id;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    
    $query = 'SELECT file,tn_ext,storage_category_id';
    $query.= ' FROM '.PREFIX_TABLE.'images, '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE category_id = '.$non_empty_id;
    $query.= ' AND id = image_id';
    // if the category has a representative picture, this is its thumbnail
    // that will be displayed !
    if ( isset( $row['representative_picture_id'] ) )
      $query.= ' AND id = '.$row['representative_picture_id'];
    else
      $query.= ' ORDER BY RAND()';
    $query.= ' LIMIT 0,1';
    $query.= ';';
    $image_result = mysql_query( $query );
    $image_row    = mysql_fetch_array( $image_result );

    $file = get_filename_wo_extension( $image_row['file'] );

    // creating links for thumbnail and associated category
    $thumbnail_link = get_complete_dir( $image_row['storage_category_id'] );
    $thumbnail_link.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $thumbnail_link.= $file.'.'.$image_row['tn_ext'];

    $thumbnail_title = $lang['hint_category'];

    $url_link = './category.php?cat='.$subcat_id;
    if ( isset($page['cat'])&& !in_array( $page['cat'], $page['tab_expand'] ) )
    {
      array_push( $page['tab_expand'], $page['cat'] );
      $page['expand'] = implode( ',', $page['tab_expand'] );
    }
    $url_link.= '&amp;expand='.$page['expand'];
    // we add the category to explore in the expand list
    if ( $page['expand'] != '' ) $url_link.= ',';
    $url_link.= $subcat_id;

    $date = $page['plain_structure'][$subcat_id]['date_last'];

    // sending vars to display
    $vtp->addSession( $handle, 'thumbnail' );
    $vtp->setVar( $handle, 'thumbnail.url', add_session_id( $url_link ) );
    $vtp->setVar( $handle, 'thumbnail.src', $thumbnail_link );
    $vtp->setVar( $handle, 'thumbnail.alt', $image_row['file'] );
    $vtp->setVar( $handle, 'thumbnail.title', $thumbnail_title );
    $vtp->setVar( $handle, 'thumbnail.name', $name );
    $vtp->setVar( $handle, 'thumbnail.icon', get_icon( $date ) );
    $vtp->closeSession( $handle, 'thumbnail' );

    if ( $cell_number++ == $user['nb_image_line'] )
    {
      $vtp->closeSession( $handle, 'line' );
      $cell_number = 1;
      // we open a new line if the subcat was not the last one
      if ( $i++ < count( $subcats ) - 1 )
      {
        $vtp->addSession( $handle, 'line' );
      }
    }
  }
  if ( $i < count( $subcats ) - 1 )
  {
    $vtp->closeSession( $handle, 'line' );
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
    $vtp->addSession( $handle, 'comment' );
    $vtp->setVar( $handle, 'comment.cat_comment', $page['comment'] );
    $vtp->closeSession( $handle, 'comment' );
  }
  // total number of pictures in the category
  if ( is_numeric( $page['cat'] ) )
  {
    $vtp->setVar( $handle, 'cat_infos.cat_name',
                  get_cat_display_name( $page['cat_name'], ' - ',
                                        'font-style:italic;' ) );
    // upload a picture in the category
    if ( $page['cat_site_id'] == 1
         and $conf['upload_available']
         and $page['cat_uploadable'] )
    {
      $vtp->addSession( $handle, 'upload' );
      $url = './upload.php?cat='.$page['cat'].'&amp;expand='.$page['expand'];
      $vtp->setVar( $handle, 'upload.url', add_session_id( $url ) );
      $vtp->closeSession( $handle, 'upload' );
    }
  }
  else
  {
    $vtp->setVar( $handle, 'cat_infos.cat_name', $page['title'] );
  }
  
  $vtp->closeSession( $handle, 'cat_infos' );
}
//------------------------------------------------------------ log informations
pwg_log( 'category', $page['title'] );
mysql_close();
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;

include('include/page_tail.php');
?>