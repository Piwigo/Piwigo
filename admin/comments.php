<?php
/***************************************************************************
 *                               comments.php                              *
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
include_once( './include/isadmin.inc.php' );
//------------------------------------------------------------------- functions
function display_pictures( $mysql_result, $maxtime, $validation_box = false )
{
  global $vtp,$sub,$lang,$conf,
    $array_cat_directories,$array_cat_site_id,$array_cat_names;

  while ( $row = mysql_fetch_array( $mysql_result ) )
  {
    $vtp->addSession( $sub, 'picture' );
    // 2. for each picture, getting informations for displaying thumbnail and
    //    link to the full size picture
    $query = 'SELECT name,file,cat_id,tn_ext';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' WHERE id = '.$row['image_id'];
    $query.= ';';
    $subresult = mysql_query( $query );
    $subrow = mysql_fetch_array( $subresult );

    if ( $array_cat_directories[$subrow['cat_id']] == '' )
    {
      $cat_result = get_cat_info( $subrow['cat_id'] );
      $array_cat_directories[$subrow['cat_id']] = $cat_result['dir'];
      $array_cat_site_id[$subrow['cat_id']] = $cat_result['site_id'];
      $array_cat_names[$subrow['cat_id']] =
        get_cat_display_name( $cat_result['name'], ' &gt; ', '' );
    }
    $cat_directory = $array_cat_directories[$row['cat_id']];
    $file = get_filename_wo_extension( $subrow['file'] );
    // name of the picture
    $name = $array_cat_names[$subrow['cat_id']].' &gt; ';
    if ( $subrow['name'] != '' )
    {
      $name.= $subrow['name'];
    }
    else
    {
      $name.= str_replace( '_', ' ', $file );
    }
    $name.= ' [ '.$subrow['file'].' ]';
    $vtp->setVar( $sub, 'picture.title', $name );
    // source of the thumbnail picture
    $src = '';
    if ( $array_cat_site_id[$subrow['cat_id']] == 1 )
    {
      $src.= '.';
    }
    $src.= $array_cat_directories[$subrow['cat_id']];
    $src.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $src.= $file.'.'.$subrow['tn_ext'];
    $vtp->setVar( $sub, 'picture.thumb_src', $src );
    // link to the full size picture
    $url = '../picture.php?cat='.$subrow['cat_id'];
    $url.= '&amp;image_id='.$row['image_id'];
    $vtp->setVar( $sub, 'picture.thumb_url', add_session_id( $url ) );
    // 3. for each picture, retrieving all comments
    $query = 'SELECT id,date,author,content';
    $query.= ' FROM '.PREFIX_TABLE.'comments';
    $query.= ' WHERE image_id = '.$row['image_id'];
    $query.= ' AND date > '.$maxtime;
    $query.= ' ORDER BY date DESC';
    $query.= ';';
    $subresult = mysql_query( $query );
    while ( $subrow = mysql_fetch_array( $subresult ) )
    {
      $vtp->addSession( $sub, 'comment' );
      $vtp->setVar( $sub, 'comment.author', $subrow['author'] );
      $displayed_date = $lang['day'][date( "w", $subrow['date'] )];
      $displayed_date.= date( " j ", $subrow['date'] );
      $displayed_date.= $lang['month'][date( "n", $subrow['date'] )];
      $displayed_date.= date( " Y G:i", $subrow['date'] );
      $vtp->setVar( $sub, 'comment.date', $displayed_date );
      $vtp->setVar( $sub, 'comment.content', nl2br( $subrow['content'] ) );
      $vtp->addSession( $sub, 'delete' );
      $url = './admin.php?page=comments';
      if ( isset( $_GET['last_days'] ) ) $url.= '&amp;last_days='.MAX_DAYS;
      if ( isset( $_GET['show_unvalidated'] ) )
        $url.= '&amp;show_unvalidated=true';
      $url.= '&amp;del='.$subrow['id'];
      $vtp->setVar( $sub, 'delete.link', add_session_id( $url ) );
      $vtp->closeSession( $sub, 'delete' );
      // if the comment has to be validated, we display a checkbox
      if ( $validation_box )
      {
        $vtp->addSession( $sub, 'validation' );
        $vtp->setVar( $sub, 'validation.id', $subrow['id'] );
        $vtp->closeSession( $sub, 'validation' );
      }
      $vtp->closeSession( $sub, 'comment' );
    }
    $vtp->closeSession( $sub, 'picture' );
  }
}
//------------------------------------------------------------ comment deletion
if ( isset( $_GET['del'] ) and is_numeric( $_GET['del'] ) )
{
  $query = 'DELETE FROM '.PREFIX_TABLE.'comments';
  $query.= ' WHERE id = '.$_GET['del'];
  $query.= ';';
  mysql_query( $query );
}
//--------------------------------------------------------- comments validation
if ( isset( $_POST['submit'] ) )
{
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'comments';
  $query.= " WHERE validated = 'false'";
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( $_POST['validate-'.$row['id']] == 'true' )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'comments';
      $query.= " SET validated = 'true'";
      $query.= ' WHERE id = '.$row['id'];
      $query.= ';';
      mysql_query( $query );
    }
  }
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/comments.vtp' );
$tpl = array( 'stats_last_days','delete','close','submit','open' );
templatize_array( $tpl, 'lang', $sub );
//--------------------------------------------------- number of days to display
if ( isset( $_GET['last_days'] ) ) define( MAX_DAYS, $_GET['last_days'] );
else                               define( MAX_DAYS, 0 );
//----------------------------------------- non specific section initialization
$array_cat_directories = array();
$array_cat_names       = array();
$array_cat_site_id     = array();
//------------------------------------------------------- last comments display
$vtp->addSession( $sub, 'section' );
$vtp->setVar( $sub, 'section.title', $lang['comments_last_title'] );
$vtp->addSession( $sub, 'last_days' );
foreach ( $conf['last_days'] as $option ) {
  $vtp->addSession( $sub, 'last_day_option' );
  $vtp->setVar( $sub, 'last_day_option.option', $option );
  $url = './admin.php?page=comments';
  $url.= '&amp;last_days='.($option - 1);
  $vtp->setVar( $sub, 'last_day_option.link', add_session_id( $url ) );
  if ( $option == MAX_DAYS + 1 )
  {
    $vtp->setVar( $sub, 'last_day_option.style', 'font-weight:bold;');
  }
  $vtp->closeSession( $sub, 'last_day_option' );
}
$vtp->closeSession( $sub, 'last_days' );
if ( isset( $_GET['last_days'] ) )
{
  $vtp->addSession( $sub, 'close' );
  $url = './admin.php?page=comments';
  if ( isset( $_GET['show_unvalidated'] ) )
  {
    $url.= '&amp;show_unvalidated='.$_GET['show_unvalidated'];
  }
  $vtp->setVar( $sub, 'close.url', add_session_id( $url ) );
  $vtp->closeSession( $sub, 'close' );
  // 1. retrieving picture ids which have comments recently added
  $date = date( 'Y-m-d', time() - ( MAX_DAYS*24*60*60 ) );
  list($year,$month,$day) = explode( '-', $date);
  $maxtime = mktime( 0,0,0,$month,$day,$year );
  $query = 'SELECT DISTINCT(image_id) as image_id';
  $query.= ' FROM '.PREFIX_TABLE.'comments';
  $query.=     ', '.PREFIX_TABLE.'images as images';
  $query.=     ', '.PREFIX_TABLE.'categories';
  $query.= ' WHERE image_id = images.id';
  $query.= ' AND   cat_id = images.cat_id';
  $query.= ' AND date > '.$maxtime;
  $query.= ' ORDER BY cat_id ASC,date_available DESC';
  $query.= ';';
  $result = mysql_query( $query );
  display_pictures( $result, $maxtime );
}
$vtp->closeSession( $sub, 'section' );
//---------------------------------------------- non validated comments display
$vtp->addSession( $sub, 'section' );
$vtp->setVar( $sub, 'section.title', $lang['comments_non_validated_title'] );
if ( isset( $_GET['show_unvalidated'] ) )
{
  // form starts
  $vtp->addSession( $sub, 'start_form' );
  $action = './admin.php?page=comments';
  if ( isset( $_GET['last_days'] ) )
  {
    $action.= '&amp;last_days='.$_GET['last_days'];
  }
  $action.= '&amp;show_unvalidated=true';
  $vtp->setVar( $sub, 'start_form.action', add_session_id( $action ) );
  $vtp->closeSession( $sub, 'start_form' );
  // close this section ?
  $vtp->addSession( $sub, 'close' );
  $url = './admin.php?page=comments';
  if ( isset( $_GET['last_days'] ) )
  {
    $url.= '&amp;last_days='.$_GET['last_days'];
  }
  $vtp->setVar( $sub, 'close.url', add_session_id( $url ) );
  $vtp->closeSession( $sub, 'close' );
  // retrieving all picture ids which have unvalidated comments
  $query = 'SELECT DISTINCT(image_id) as image_id';
  $query.= ' FROM '.PREFIX_TABLE.'comments as comments';
  $query.=     ', '.PREFIX_TABLE.'images as images';
  $query.=     ', '.PREFIX_TABLE.'categories';
  $query.= ' WHERE image_id = images.id';
  $query.= ' AND   cat_id = images.cat_id';
  $query.= " AND comments.validated = 'false'";
  $query.= ' ORDER BY cat_id ASC,date_available DESC';
  $query.= ';';
  $result = mysql_query( $query );
  display_pictures( $result, 0, true );
  $vtp->addSession( $sub, 'submit' );
  $vtp->closeSession( $sub, 'submit' );
  // form ends
  $vtp->addSession( $sub, 'end_form' );
  $vtp->closeSession( $sub, 'end_form' );
}
else
{
  $vtp->addSession( $sub, 'open' );
  $url = './admin.php?page=comments';
  if ( isset( $_GET['last_days'] ) )
  {
    $url.= '&amp;last_days='.$_GET['last_days'];
  }
  $url.= '&amp;show_unvalidated=true';
  $vtp->setVar( $sub, 'open.url', add_session_id( $url ) );
  $vtp->closeSession( $sub, 'open' );
}
$vtp->closeSession( $sub, 'section' );
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle, 'sub', $sub );
?>