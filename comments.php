<?php
/***************************************************************************
 *                               comments.php                              *
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

include_once( './include/init.inc.php' );
//------------------------------------------------------------------- functions
function display_pictures( $mysql_result, $maxtime, $forbidden_cat_ids )
{
  global $vtp,$handle,$lang,$conf,
    $array_cat_directories,$array_cat_site_id,$array_cat_names;

  while ( $row = mysql_fetch_array( $mysql_result ) )
  {
    $vtp->addSession( $handle, 'picture' );
    // 1. find a category wich is authorized for the user to display a
    //    category name.
    $query = 'SELECT category_id';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE image_id = '.$row['image_id'];
    if ( count( $forbidden_cat_ids ) > 0 )
    {
      $query.= ' AND category_id NOT IN (';
      foreach ( $forbidden_cat_ids as $i => $restricted_cat ) {
        if ( $i > 0 ) $query.= ',';
        $query.= $restricted_cat;
      }
      $query.= ')';
    }
    $query.= ' ORDER BY RAND()';
    $query.= ';';
    $subrow = mysql_fetch_array( mysql_query( $query ) );
    $category_id = $subrow['category_id'];

    if ( $array_cat_directories[$category_id] == '' )
    {
      $array_cat_directories[$category_id] =
        get_complete_dir( $category_id );
      $cat_result = get_cat_info( $category_id );
      $array_cat_site_id[$category_id] = $cat_result['site_id'];
      $array_cat_names[$category_id] =
        get_cat_display_name( $cat_result['name'], ' &gt; ', '' );
    }
    
    // 2. for each picture, getting informations for displaying thumbnail and
    //    link to the full size picture
    $query = 'SELECT name,file,storage_category_id as cat_id,tn_ext';
    $query.= ' FROM '.PREFIX_TABLE.'images';
    $query.= ' WHERE id = '.$row['image_id'];
    $query.= ';';
    $subresult = mysql_query( $query );
    $subrow = mysql_fetch_array( $subresult );

    if ( $array_cat_directories[$subrow['cat_id']] == '' )
    {
      $array_cat_directories[$subrow['cat_id']] =
        get_complete_dir( $subrow['cat_id'] );
      $cat_result = get_cat_info( $subrow['cat_id'] );
      $array_cat_site_id[$subrow['cat_id']] = $cat_result['site_id'];
      $array_cat_names[$subrow['cat_id']] =
        get_cat_display_name( $cat_result['name'], ' &gt; ', '' );
    }

    $file = get_filename_wo_extension( $subrow['file'] );
    // name of the picture
    $name = $array_cat_names[$category_id].' &gt; ';
    if ( $subrow['name'] != '' ) $name.= $subrow['name'];
    else                         $name.= str_replace( '_', ' ', $file );
    $name.= ' [ '.$subrow['file'].' ]';
    $vtp->setVar( $handle, 'picture.title', $name );
    // source of the thumbnail picture
    $src = $array_cat_directories[$subrow['cat_id']];
    $src.= 'thumbnail/'.$conf['prefix_thumbnail'];
    $src.= $file.'.'.$subrow['tn_ext'];
    $vtp->setVar( $handle, 'picture.thumb_src', $src );
    // link to the full size picture
    $url = './picture.php?cat='.$category_id;
    $url.= '&amp;image_id='.$row['image_id'];
    $vtp->setVar( $handle, 'picture.thumb_url', add_session_id( $url ) );
    // 3. for each picture, retrieving all comments
    $query = 'SELECT id,date,author,content';
    $query.= ' FROM '.PREFIX_TABLE.'comments';
    $query.= ' WHERE image_id = '.$row['image_id'];
    $query.= ' AND date > '.$maxtime;
    $query.= " AND validated = 'true'";
    $query.= ' ORDER BY date DESC';
    $query.= ';';
    $handleresult = mysql_query( $query );
    while ( $subrow = mysql_fetch_array( $handleresult ) )
    {
      $vtp->addSession( $handle, 'comment' );
      $author = $subrow['author'];
      if ( $subrow['author'] == '' ) $author = $lang['guest'];
      $vtp->setVar( $handle, 'comment.author', $author );
      $displayed_date = format_date( $subrow['date'], 'unix', true );
      $vtp->setVar( $handle, 'comment.date', $displayed_date );

      $content = nl2br( $subrow['content'] );
      
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
      $vtp->closeSession( $handle, 'comment' );
    }
    $vtp->closeSession( $handle, 'picture' );
  }
}
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/comments.vtp' );
initialize_template();
$tpl = array( 'title_comments','stats_last_days','search_return_main_page' );
templatize_array( $tpl, 'lang', $handle );
$vtp->setGlobalVar( $handle, 'text_color', $user['couleur_text'] );
//--------------------------------------------------- number of days to display
if ( isset( $_GET['last_days'] ) ) define( 'MAX_DAYS', $_GET['last_days'] );
else                               define( 'MAX_DAYS', 0 );
//----------------------------------------- non specific section initialization
$array_cat_directories = array();
$array_cat_names       = array();
$array_cat_site_id     = array();
//------------------------------------------------------- last comments display
foreach ( $conf['last_days'] as $option ) {
  $vtp->addSession( $handle, 'last_day_option' );
  $vtp->setVar( $handle, 'last_day_option.option', $option );
  $url = './comments.php';
  $url.= '?last_days='.($option - 1);
  $vtp->setVar( $handle, 'last_day_option.link', add_session_id( $url ) );
  $style = '';
  if ( $option == MAX_DAYS + 1 ) $style = 'text-decoration:underline;';
  $vtp->setVar( $handle, 'last_day_option.style', $style );
  $vtp->closeSession( $handle, 'last_day_option' );
}
$vtp->setVar( $handle, 'back_url', add_session_id( './category.php' ) );
// 1. retrieving picture ids which have comments recently added
$date = date( 'Y-m-d', time() - ( MAX_DAYS*24*60*60 ) );
list($year,$month,$day) = explode( '-', $date);
$maxtime = mktime( 0,0,0,$month,$day,$year );
$query = 'SELECT DISTINCT(ic.image_id) as image_id';
$query.= ' FROM '.PREFIX_TABLE.'comments AS c';
$query.=     ', '.PREFIX_TABLE.'image_category AS ic';
$query.= ' WHERE c.image_id = ic.image_id';
$query.= ' AND date > '.$maxtime;
// we must not show pictures of a forbidden category
if ( $user['forbidden_categories'] != '' )
{
  $query.= ' AND category_id NOT IN ';
  $query.= '('.$user['forbidden_categories'].')';
}
$query.= ' ORDER BY ic.image_id DESC';
$query.= ';';
$result = mysql_query( $query );
display_pictures( $result, $maxtime, $user['restrictions'] );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>