<?php
/***************************************************************************
 *                                 upload.php                              *
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

//------------------------------------------------------------------- functions
// The validate_upload function checks if the image of the given path is valid.
// A picture is valid when :
//     - width, height and filesize are not higher than the maximum
//       filesize authorized by the administrator
//     - the type of the picture is among jpg, gif and png
// The function returns an array containing :
//     - $result['type'] contains the type of the image ('jpg', 'gif' or 'png')
//     - $result['error'] contains an array with the different errors
//       found with the picture
function validate_upload( $temp_name, $my_max_file_size,
                          $image_max_width, $image_max_height )
{
  global $lang;
		
  $result = array();
  $result['error'] = array();
  //echo $_FILES['picture']['name']."<br />".$temp_name;
  $extension = get_extension( $_FILES['picture']['name'] );
  if ( $extension != 'gif' and $extension != 'jpg' and $extension != 'png' )
  {
    array_push( $result['error'], $lang['upload_advise_filetype'] );
    return $result;
  }
  if ( !isset( $_FILES['picture'] ) )
  {
    // do we even have a file?
    array_push( $result['error'], "You did not upload anything!" );
  }
  else if ( $_FILES['picture']['size'] > $my_max_file_size * 1024 )
  {
    array_push( $result['error'],
                $lang['upload_advise_width'].$my_max_file_size.' KB' );
  }
  else
  {
    // check if we are allowed to upload this file_type
    // upload de la photo sous un nom temporaire
    if ( !move_uploaded_file( $_FILES['picture']['tmp_name'], $temp_name ) )
    {
      array_push( $result['error'], $lang['upload_cannot_upload'] );
    }
    else
    {
      $size = getimagesize( $temp_name );
      if ( isset( $image_max_width )
           and $image_max_width != ""
           and $size[0] > $image_max_width )
      {
        array_push( $result['error'],
                    $lang['upload_advise_width'].$image_max_width.' px' );
      }
      if ( isset( $image_max_height )
           and $image_max_height != ""
           and $size[1] > $image_max_height )
      {
        array_push( $result['error'],
                    $lang['upload_advise_height'].$image_max_height.' px' );
      }
      // $size[2] == 1 means GIF
      // $size[2] == 2 means JPG
      // $size[2] == 3 means PNG
      switch ( $size[2] )
      {
      case 1 : $result['type'] = 'gif'; break;
      case 2 : $result['type'] = 'jpg'; break;
      case 3 : $result['type'] = 'png'; break;
      default :
        array_push( $result['error'], $lang['upload_advise_filetype'] );  
      }
    }
  }
  if ( sizeof( $result['error'] ) > 0 )
  {
    // destruction de l'image avec le nom temporaire
    @unlink( $temp_name );
  }
  return $result;
}	
//----------------------------------------------------------- personnal include
include_once( './include/init.inc.php' );
//-------------------------------------------------- access authorization check
check_login_authorization();
check_cat_id( $_GET['cat'] );
if ( isset( $page['cat'] ) and is_numeric( $page['cat'] ) )
{
  check_restrictions( $page['cat'] );
  $result = get_cat_info( $page['cat'] );
  $page['cat_dir']        = get_complete_dir( $page['cat'] );
  $page['cat_site_id']    = $result['site_id'];
  $page['cat_name']       = $result['name'];
  $page['cat_uploadable'] = $result['uploadable'];
}
else
{
  $access_forbidden = true;
}
if ( $access_forbidden == true
     or $page['cat_site_id'] != 1
     or !$conf['upload_available']
     or !$page['cat_uploadable'] )
{
  echo '<div style="text-align:center;">'.$lang['upload_forbidden'].'<br />';
  echo '<a href="'.add_session_id( './category.php' ).'">';
  echo $lang['thumbnails'].'</a></div>';
  exit();
}
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/upload.vtp' );
initialize_template();

$tpl = array( 'upload_title', 'upload_username', 'mail_address', 'submit',
              'upload_successful', 'search_return_main_page','upload_author',
              'upload_name','upload_creation_date','upload_comment',
              'mandatory' );
templatize_array( $tpl, 'lang', $handle );

$error = array();
$page['upload_successful'] = false;
if ( isset( $_GET['waiting_id'] ) )
{
  $page['waiting_id'] = $_GET['waiting_id'];
}
//-------------------------------------------------------------- picture upload
// verfying fields
if ( isset( $_POST['submit'] ) and !isset( $_GET['waiting_id'] ) )
{
  $path = $page['cat_dir'].$_FILES['picture']['name'];
  if ( @is_file( $path ) )
  {
    array_push( $error, $lang['upload_file_exists'] );
  }
  // test de la présence des champs obligatoires
  if ( $_FILES['picture']['name'] == '' )
  {
    array_push( $error, $lang['upload_filenotfound'] );
  }
  if ( !ereg( "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
             $_POST['mail_address'] ) )
  {
    array_push( $error, $lang['reg_err_mail_address'] );
  }
  if ( $_POST['username'] == '' )
  {
    array_push( $error, $lang['upload_err_username'] );
  }

  if ( $_POST['date_creation'] != '' )
  {
    list( $day,$month,$year ) = explode( '/', $_POST['date_creation'] );
    // int checkdate ( int month, int day, int year)
    if ( checkdate( $month, $day, $year ) )
    {
      // int mktime ( int hour, int minute, int second,
      //              int month, int day, int year [, int is_dst])
      $date_creation = mktime( 0, 0, 0, $month, $day, $year );
    }
    else
    {
      array_push( $error, $lang['err_date'] );
    }
  }
  // creation of the "infos" field :
  // <infos author="Pierrick LE GALL" comment="my comment"
  //        date_creation="1056891767" name="" />
  $xml_infos = '<infos';
  $xml_infos.= ' author="'.htmlspecialchars($_POST['author'],ENT_QUOTES).'"';
  $xml_infos.= ' comment="'.htmlspecialchars($_POST['comment'],ENT_QUOTES).'"';
  $xml_infos.= ' date_creation="'.$date_creation.'"';
  $xml_infos.= ' name="'.htmlspecialchars( $_POST['name'], ENT_QUOTES).'"';
  $xml_infos.= ' />';
  
  if ( sizeof( $error ) == 0 )
  {
    $result = validate_upload( $path, $conf['upload_maxfilesize'],
                               $conf['upload_maxwidth'],
                               $conf['upload_maxheight']  );
    $upload_type = $result['type'];
    for ( $j = 0; $j < sizeof( $result['error'] ); $j++ )
    {
      array_push( $error, $result['error'][$j] );
    }
  }

  if ( sizeof( $error ) == 0 )
  {
    $query = 'insert into '.PREFIX_TABLE.'waiting';
    $query.= ' (storage_category_id,file,username,mail_address,date,infos)';
    $query.= ' values ';
    $query.= '('.$page['cat'].",'".$_FILES['picture']['name']."'";
    $query.= ",'".htmlspecialchars( $_POST['username'], ENT_QUOTES)."'";
    $query.= ",'".$_POST['mail_address']."',".time().",'".$xml_infos."')";
    $query.= ';';
    mysql_query( $query );
    $page['waiting_id'] = mysql_insert_id();
  }
}
//------------------------------------------------------------ thumbnail upload
if ( isset( $_POST['submit'] ) and isset( $_GET['waiting_id'] ) )
{
  // upload of the thumbnail
  $query = 'select file';
  $query.= ' from '.PREFIX_TABLE.'waiting';
  $query.= ' where id = '.$_GET['waiting_id'];
  $query.= ';';
  $result= mysql_query( $query );
  $row = mysql_fetch_array( $result );
  $file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
  $extension = get_extension( $_FILES['picture']['name'] );
  $path = $page['cat_dir'].'thumbnail/';
  $path.= $conf['prefix_thumbnail'].$file.'.'.$extension;
  $result = validate_upload( $path, $conf['upload_maxfilesize'],
                             $conf['upload_maxwidth_thumbnail'],
                             $conf['upload_maxheight_thumbnail']  );
  $upload_type = $result['type'];
  for ( $j = 0; $j < sizeof( $result['error'] ); $j++ )
  {
    array_push( $error, $result['error'][$j] );
  }
  if ( sizeof( $error ) == 0 )
  {
    $query = 'update '.PREFIX_TABLE.'waiting';
    $query.= " set tn_ext = '".$extension."'";
    $query.= ' where id = '.$_GET['waiting_id'];
    $query.= ';';
    mysql_query( $query );
    $page['upload_successful'] = true;
  }
}

if ( !$page['upload_successful'] )
{
  $vtp->addSession( $handle, 'upload_not_successful' );
//-------------------------------------------------------------- errors display
  if ( sizeof( $error ) != 0 )
  {
    $vtp->addSession( $handle, 'errors' );
    for ( $i = 0; $i < sizeof( $error ); $i++ )
    {
      $vtp->addSession( $handle, 'li' );
      $vtp->setVar( $handle, 'li.li', $error[$i] );
      $vtp->closeSession( $handle, 'li' );
    }
    $vtp->closeSession( $handle, 'errors' );
  }
//----------------------------------------------------------------- form action
  $url = './upload.php?cat='.$page['cat'].'&amp;expand='.$_GET['expand'];
  if ( isset( $page['waiting_id'] ) )
  {
    $url.= '&amp;waiting_id='.$page['waiting_id'];
  }
  $vtp->setGlobalVar( $handle, 'form_action', add_session_id( $url ) );
//--------------------------------------------------------------------- advises
  if ( $conf['upload_maxfilesize'] != '' )
  {
    $vtp->addSession( $handle, 'advise' );
    $content = $lang['upload_advise_filesize'];
    $content.= $conf['upload_maxfilesize'].' KB';
    $vtp->setVar( $handle, 'advise.content', $content );
    $vtp->closeSession( $handle, 'advise' );
  }
  if ( isset( $page['waiting_id'] ) )
  {
    $advise_title=$lang['upload_advise_thumbnail'].$_FILES['picture']['name'];
    $vtp->setGlobalVar( $handle, 'advise_title', $advise_title );

    if ( $conf['upload_maxwidth_thumbnail'] != '' )
    {
      $vtp->addSession( $handle, 'advise' );
      $content = $lang['upload_advise_width'];
      $content.= $conf['upload_maxwidth_thumbnail'].' px';
      $vtp->setVar( $handle, 'advise.content', $content );
      $vtp->closeSession( $handle, 'advise' );
    }
    if ( $conf['upload_maxheight_thumbnail'] != '' )
    {
      $vtp->addSession( $handle, 'advise' );
      $content = $lang['upload_advise_height'];
      $content.= $conf['upload_maxheight_thumbnail'].' px';
      $vtp->setVar( $handle, 'advise.content', $content );
      $vtp->closeSession( $handle, 'advise' );
    }
  }
  else
  {
    $advise_title = $lang['upload_advise'];
    $advise_title.= get_cat_display_name( $page['cat_name'], ' - ',
                                          'font-style:italic;' );
    $vtp->setGlobalVar( $handle, 'advise_title', $advise_title );

    if ( $conf['upload_maxwidth'] != '' )
    {
      $vtp->addSession( $handle, 'advise' );
      $content = $lang['upload_advise_width'];
      $content.= $conf['upload_maxwidth'].' px';
      $vtp->setVar( $handle, 'advise.content', $content );
      $vtp->closeSession( $handle, 'advise' );
    }
    if ( $conf['upload_maxheight'] != '' )
    {
      $vtp->addSession( $handle, 'advise' );
      $content = $lang['upload_advise_height'];
      $content.= $conf['upload_maxheight'].' px';
      $vtp->setVar( $handle, 'advise.content', $content );
      $vtp->closeSession( $handle, 'advise' );
    }
  }
  $vtp->addSession( $handle, 'advise' );
  $content = $lang['upload_advise_filetype'];
  $vtp->setVar( $handle, 'advise.content', $content );
  $vtp->closeSession( $handle, 'advise' );
//----------------------------------------- optionnal username and mail address
  if ( !isset( $page['waiting_id'] ) )
  {
    $vtp->addSession( $handle, 'fields' );
    // username
    if ( isset( $_POST['username'] ) ) $username = $_POST['username'];
    else                               $username = $user['username'];
    $vtp->setVar( $handle, 'fields.username',  $username );
    // mail address
    if ( isset( $_POST['mail_address'] ) )$mail_address=$_POST['mail_address'];
    else                                  $mail_address=$user['mail_address'];
    $vtp->setGlobalVar( $handle, 'user_mail_address',$user['mail_address'] );
    // name of the picture
    $vtp->setVar( $handle, 'fields.name', $_POST['name'] );
    // author
    $vtp->setVar( $handle, 'fields.author', $_POST['author'] );
    // date of creation
    $vtp->setVar( $handle, 'fields.date_creation', $_POST['date_creation'] );
    // comment
    $vtp->setVar( $handle, 'fields.comment', $_POST['comment'] );

    $vtp->closeSession( $handle, 'fields' );

    $vtp->addSession( $handle, 'note' );
    $vtp->closeSession( $handle, 'note' );
  }
  $vtp->closeSession( $handle, 'upload_not_successful' );
}
else
{
  $vtp->addSession( $handle, 'upload_successful' );
  $vtp->closeSession( $handle, 'upload_successful' );
}
//----------------------------------------------------- return to main page url
$url = './category.php?cat='.$page['cat'].'&amp;expand='.$_GET['expand'];
$vtp->setGlobalVar( $handle, 'return_url', add_session_id( $url ) );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
?>