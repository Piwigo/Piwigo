<?php
/***************************************************************************
 *                            picture_modify.php                           *
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
//--------------------------------------------------------- update informations
$errors = array();
// first, we verify whether there is a mistake on the given creation date
if ( isset( $_POST['creation_date'] ) and $_POST['creation_date'] != '' )
{
  if ( !check_date_format( $_POST['creation_date'] ) )
    array_push( $errors, $lang['err_date'] );
}
if ( isset( $_POST['submit'] ) )
{
  $query = 'UPDATE '.PREFIX_TABLE.'images';
  
  $query.= ' SET name = ';
  if ( $_POST['name'] == '' )
    $query.= 'NULL';
  else
    $query.= "'".htmlentities( $_POST['name'], ENT_QUOTES )."'";
  
  $query.= ', author = ';
  if ( $_POST['author'] == '' )
    $query.= 'NULL';
  else
    $query.= "'".htmlentities($_POST['author'],ENT_QUOTES)."'";

  $query.= ', comment = ';
  if ( $_POST['comment'] == '' )
    $query.= 'NULL';
  else
    $query.= "'".htmlentities($_POST['comment'],ENT_QUOTES)."'";

  $query.= ', date_creation = ';
  if ( check_date_format( $_POST['creation_date'] ) )
    $query.= "'".date_convert( $_POST['creation_date'] )."'";
  else if ( $_POST['creation_date'] == '' )
    $query.= 'NULL';

  $query.= ', keywords = ';
  $keywords_array = get_keywords( $_POST['keywords'] );
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

  $query.= ' WHERE id = '.$_GET['image_id'];
  $query.= ';';
  mysql_query( $query );
  // make the picture representative of a category ?
  $query = 'SELECT DISTINCT(category_id) as category_id';
  $query.= ',representative_picture_id';
  $query.= ' FROM '.PREFIX_TABLE.'image_category AS ic';
  $query.= ', '.PREFIX_TABLE.'categories AS c';
  $query.= ' WHERE c.id = ic.category_id';
  $query.= ' AND image_id = '.$_GET['image_id'];
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // if the user ask the picture to be the representative picture of its
    // category, the category is updated in the database (without wondering
    // if this picture was already the representative one)
    if ( isset($_POST['representative-'.$row['category_id']]) )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'categories';
      $query.= ' SET representative_picture_id = '.$_GET['image_id'];
      $query.= ' WHERE id = '.$row['category_id'];
      $query.= ';';
      mysql_query( $query );
    }
    // if the user ask this picture to be not any more the representative,
    // we have to set the representative_picture_id of this category to NULL
    else if ( isset( $row['representative_picture_id'] )
              and $row['representative_picture_id'] == $_GET['image_id'] )
    {
      $query = 'UPDATE '.PREFIX_TABLE.'categories';
      $query.= ' SET representative_picture_id = NULL';
      $query.= ' WHERE id = '.$row['category_id'];
      $query.= ';';
      mysql_query( $query );
    }
  }
  $associate_or_dissociate = false;
  // associate with a new category ?
  if ( $_POST['associate'] != '-1' and $_POST['associate'] != '' )
  {
    // does the uppercat id exists in the database ?
    if ( !is_numeric( $_POST['associate'] ) )
    {
      array_push( $errors, $lang['cat_unknown_id'] );
    }
    else
    {
      $query = 'SELECT id';
      $query.= ' FROM '.PREFIX_TABLE.'categories';
      $query.= ' WHERE id = '.$_POST['associate'];
      $query.= ';';
      if ( mysql_num_rows( mysql_query( $query ) ) == 0 )
        array_push( $errors, $lang['cat_unknown_id'] );
    }
  }
  if ( $_POST['associate'] != '-1'
       and $_POST['associate'] != ''
       and count( $errors ) == 0 )
  {
    $query = 'INSERT INTO '.PREFIX_TABLE.'image_category';
    $query.= ' (category_id,image_id) VALUES ';
    $query.= '('.$_POST['associate'].','.$_GET['image_id'].')';
    $query.= ';';
    mysql_query( $query);
    $associate_or_dissociate = true;
    update_category( $_POST['associate'] );
  }
  // dissociate any category ?
  // retrieving all the linked categories
  $query = 'SELECT DISTINCT(category_id) as category_id';
  $query.= ' FROM '.PREFIX_TABLE.'image_category';
  $query.= ' WHERE image_id = '.$_GET['image_id'];
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( isset($_POST['dissociate-'.$row['category_id']]) )
    {
      $query = 'DELETE FROM '.PREFIX_TABLE.'image_category';
      $query.= ' WHERE image_id = '.$_GET['image_id'];
      $query.= ' AND category_id = '.$row['category_id'];
      $query.= ';';
      mysql_query( $query );
      $associate_or_dissociate = true;
      update_category( $row['category_id'] );
    }
  }
  if ( $associate_or_dissociate )
  {
    synchronize_all_users();
  }
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open(
  './template/'.$user['template'].'/admin/picture_modify.vtp' );

$tpl = array( 'submit','errors_title','picmod_update','picmod_back',
              'default','file','size','filesize','registration_date',
              'author','creation_date','keywords','comment', 'upload_name',
              'dissociate','categories','infoimage_associate',
              'cat_image_info','category_representative' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//-------------------------------------------------------------- errors display
if ( count( $errors ) != 0 )
{
  $vtp->addSession( $sub, 'errors' );
  foreach ( $errors as $error ) {
    $vtp->addSession( $sub, 'li' );
    $vtp->setVar( $sub, 'li.content', $error );
    $vtp->closeSession( $sub, 'li' );
  }
  $vtp->closeSession( $sub, 'errors' );
}
//-------------------------------------------- displaying informations and form
$action = './admin.php?'.$_SERVER['QUERY_STRING'];
$vtp->setVar( $sub, 'form_action', $action );
// retrieving direct information about picture
$infos = array( 'file','date_available','date_creation','tn_ext','name'
                ,'filesize','width','height','author','comment','keywords'
                ,'storage_category_id' );
$query = 'SELECT '. implode( ',', $infos );
$query.= ' FROM '.PREFIX_TABLE.'images';
$query.= ' WHERE id = '.$_GET['image_id'];
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );

foreach ( $infos as $info ) {
  if ( !isset( $row[$info] ) ) $row[$info] = '';
}

// picture title
if ( $row['name'] == '' )
{
  $title = str_replace( '_',' ',get_filename_wo_extension($row['file']) );
}
else
{
  $title = $row['name'];
}
$vtp->setVar( $sub, 'title', $title );
$vtp->setVar( $sub, 'f_file', $row['file'] );
$vtp->setVar( $sub, 'f_size', $row['width'].' * '.$row['height'] );
$vtp->setVar( $sub, 'f_filesize', $row['filesize'].' KB' );
$vtp->setVar( $sub, 'f_registration_date',format_date($row['date_available']));
$default_name = str_replace( '_',' ',get_filename_wo_extension($row['file']) );
$vtp->setVar( $sub, 'default_name', $default_name );
// if this form is displayed after an unsucceeded submit, we have to display
// the values filled by the user (wright or wrong).
if ( count( $errors ) > 0 )
{
  $name            = $_POST['name'];
  $author          = $_POST['author'];
  $creation_date   = $_POST['creation_date'];
  $keywords        = $_POST['keywords'];
  $comment         = $_POST['comment'];
}
else
{
  $name            = $row['name'];
  $author          = $row['author'];
  $creation_date   = date_convert_back( $row['date_creation'] );
  $keywords        = $row['keywords'];
  $comment         = $row['comment'];
}
$vtp->setVar( $sub, 'f_name',            $name );
$vtp->setVar( $sub, 'f_author',          $author );
$vtp->setVar( $sub, 'f_creation_date',   $creation_date );
$vtp->setVar( $sub, 'f_keywords',        $keywords );
$vtp->setVar( $sub, 'f_comment',         $comment );
// retrieving directory where picture is stored (for displaying the
// thumbnail)
$thumbnail_url = get_complete_dir( $row['storage_category_id'] );
$result = get_cat_info( $row['storage_category_id'] );
$cat_name = get_cat_display_name( $result['name'], ' &gt; ', '' );
$vtp->setVar( $sub, 'dir', $cat_name );
if ( $result['site_id'] == 1 ) $thumbnail_url = '.'.$thumbnail_url;
$file_wo_ext = get_filename_wo_extension( $row['file'] );
$thumbnail_url.= '/thumbnail/';
$thumbnail_url.= $conf['prefix_thumbnail'].$file_wo_ext.'.'.$row['tn_ext'];
$vtp->setVar( $sub, 'thumbnail_url', $thumbnail_url );
// storage category is linked by default
$vtp->addSession( $sub, 'linked_category' );
$vtp->setVar( $sub, 'linked_category.name', $cat_name );
$url = '../picture.php?image_id='.$_GET['image_id'];
$url.= '&amp;cat='.$row['storage_category_id'];
$vtp->setVar( $sub, 'linked_category.url',add_session_id( $url));
$url = './admin.php?page=infos_images&amp;cat_id='.$row['storage_category_id'];
$vtp->setVar( $sub, 'linked_category.infos_images_link',add_session_id( $url));
if ( $result['status'] == 'private' )
{
  $private_string = '<span style="color:red;font-weight:bold;">';
  $private_string.= $lang['private'].'</span>';
  $vtp->setVar( $sub, 'linked_category.private', $private_string );
}
if ( !$result['visible'] )
{
  $invisible_string = '<span style="color:red;">';
  $invisible_string.= $lang['cat_invisible'].'</span>';
  $vtp->setVar( $sub, 'linked_category.invisible', $invisible_string );
}
$vtp->setVar( $sub, 'linked_category.id', $row['storage_category_id'] );
if ( $result['representative_picture_id'] == $_GET['image_id'] )
{
  $vtp->setVar( $sub, 'linked_category.representative_checked',
                ' checked="checked"' );
}
$vtp->closeSession( $sub, 'linked_category' );
// retrieving all the linked categories
$query = 'SELECT DISTINCT(category_id) as category_id,status,visible';
$query.= ',representative_picture_id';
$query.= ' FROM '.PREFIX_TABLE.'image_category';
$query.= ','.PREFIX_TABLE.'categories';
$query.= ' WHERE image_id = '.$_GET['image_id'];
$query.= ' AND category_id != '.$row['storage_category_id'];
$query.= ' AND category_id = id';
$query.= ';';
$result = mysql_query( $query );
while ( $row = mysql_fetch_array( $result ) )
{
  $vtp->addSession( $sub, 'linked_category' );
  $vtp->setVar( $sub, 'linked_category.id', $row['category_id'] );

  $vtp->addSession( $sub, 'checkbox' );
  $vtp->setVar( $sub, 'checkbox.id', $row['category_id'] );
  $vtp->closeSession( $sub, 'checkbox' );

  $cat_infos = get_cat_info( $row['category_id'] );
  $cat_name = get_cat_display_name( $cat_infos['name'], ' &gt; ', '' );
  $vtp->setVar( $sub, 'linked_category.name', $cat_name );

  $url = '../picture.php?image_id='.$_GET['image_id'];
  $url.= '&amp;cat='.$row['category_id'];
  $vtp->setVar( $sub, 'linked_category.url',add_session_id( $url));

  $url = './admin.php?page=infos_images&amp;cat_id='.$row['category_id'];
  $vtp->setVar( $sub, 'linked_category.infos_images_link',
                add_session_id( $url));

  if ( $row['status'] == 'private' )
  {
    $private_string = '<span style="color:red;font-weight:bold;">';
    $private_string.= $lang['private'].'</span>';
    $vtp->setVar( $sub, 'linked_category.private', $private_string );
  }

  if ( !get_boolean( $row['visible'] ) )
  {
    $invisible_string = '<span style="color:red;">';
    $invisible_string.= $lang['cat_invisible'].'</span>';
    $vtp->setVar( $sub, 'linked_category.invisible', $invisible_string );
  }

  if ( isset( $row['representative_picture_id'] )
       and $row['representative_picture_id'] == $_GET['image_id'] )
  {
    $vtp->setVar( $sub, 'linked_category.representative_checked',
                  ' checked="checked"' );
  }
  
  $vtp->closeSession( $sub, 'linked_category' );
}
// if there are linked category other than the storage category, we show
// propose the dissociate text
if ( mysql_num_rows( $result ) > 0 )
{
  $vtp->addSession( $sub, 'dissociate' );
  $vtp->closeSession( $sub, 'dissociate' );
}
// associate to another category ?
//
// We only show a List Of Values if the number of categories is less than
// $conf['max_LOV_categories']
$query = 'SELECT COUNT(id) AS nb_total_categories';
$query.= ' FROM '.PREFIX_TABLE.'categories';
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );
if ( $row['nb_total_categories'] < $conf['max_LOV_categories'] )
{
  $vtp->addSession( $sub, 'associate_LOV' );
  $vtp->addSession( $sub, 'associate_cat' );
  $vtp->setVar( $sub, 'associate_cat.value', '-1' );
  $vtp->setVar( $sub, 'associate_cat.content', '' );
  $vtp->closeSession( $sub, 'associate_cat' );
  $page['plain_structure'] = get_plain_structure( true );
  $structure = create_structure( '', array() );
  display_categories( $structure, '&nbsp;' );
  $vtp->closeSession( $sub, 'associate_LOV' );
}
// else, we only display a small text field, we suppose the administrator
// knows the id of its category
else
{
  $vtp->addSession( $sub, 'associate_text' );
  $vtp->closeSession( $sub, 'associate_text' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>