<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

check_status(ACCESS_GUEST);

$username = !empty($_POST['username'])?$_POST['username']:$user['username'];
$mail_address = !empty($_POST['mail_address'])?$_POST['mail_address']:@$user['mail_address'];
$name = !empty($_POST['name'])?$_POST['name']:'';
$author = !empty($_POST['author'])?$_POST['author']:'';
$date_creation = !empty($_POST['date_creation'])?$_POST['date_creation']:'';
$comment = !empty($_POST['comment'])?$_POST['comment']:'';

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
  global $conf, $lang, $page, $mail_address;

  $result = array();
  $result['error'] = array();
  //echo $_FILES['picture']['name']."<br />".$temp_name;
  $extension = get_extension( $_FILES['picture']['name'] );
  if (!in_array($extension, $conf['picture_ext']))
  {
    array_push( $result['error'], l10n('upload_advise_filetype') );
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
                l10n('upload_advise_filesize').$my_max_file_size.' KB' );
  }
  else
  {
    // check if we are allowed to upload this file_type
    // upload de la photo sous un nom temporaire
    if ( !move_uploaded_file( $_FILES['picture']['tmp_name'], $temp_name ) )
    {
      array_push( $result['error'], l10n('upload_cannot_upload') );
    }
    else
    {
      $size = getimagesize( $temp_name );
      if ( isset( $image_max_width )
           and $image_max_width != ""
           and $size[0] > $image_max_width )
      {
        array_push( $result['error'],
                    l10n('upload_advise_width').$image_max_width.' px' );
      }
      if ( isset( $image_max_height )
           and $image_max_height != ""
           and $size[1] > $image_max_height )
      {
        array_push( $result['error'],
                    l10n('upload_advise_height').$image_max_height.' px' );
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
        array_push( $result['error'], l10n('upload_advise_filetype') );
      }
    }
  }
  if ( sizeof( $result['error'] ) > 0 )
  {
    // destruction de l'image avec le nom temporaire
    @unlink( $temp_name );
  }
  else
  {
    @chmod( $temp_name, 0644);
  }

  //------------------------------------------------------------ log informations
  pwg_log();

  return $result;
}

//-------------------------------------------------- access authorization check
if (isset($_GET['cat']) and is_numeric($_GET['cat']))
{
  $page['category'] = $_GET['cat'];
}

if (isset($page['category']))
{
  check_restrictions( $page['category'] );
  $category = get_cat_info( $page['category'] );
  $category['cat_dir'] = get_complete_dir( $page['category'] );

  if (url_is_remote($category['cat_dir']) or !$category['uploadable'])
  {
    page_forbidden('upload not allowed');
  }
}
else { // $page['category'] may be set by a futur plugin but without it
  bad_request('invalid parameters');
}

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
  $path = $category['cat_dir'].$_FILES['picture']['name'];
  if ( @is_file( $path ) )
  {
    array_push( $error, l10n('upload_file_exists') );
  }
  // test de la présence des champs obligatoires
  if ( empty($_FILES['picture']['name']))
  {
    array_push( $error, l10n('upload_filenotfound') );
  }
  if ( !ereg( "([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+)",
             $_POST['mail_address'] ) )
  {
    array_push( $error, l10n('reg_err_mail_address') );
  }
  if ( empty($_POST['username']) )
  {
    array_push( $error, l10n('upload_err_username') );
  }

  $date_creation = '';
  if ( !empty($_POST['date_creation']) )
  {
    list( $day,$month,$year ) = explode( '/', $_POST['date_creation'] );
    // int checkdate ( int month, int day, int year)
    if (checkdate($month, $day, $year))
    {
      $date_creation = $year.'-'.$month.'-'.$day;
    }
    else
    {
      array_push( $error, l10n('err_date') );
    }
  }
  // creation of the "infos" field :
  // <infos author="Pierrick LE GALL" comment="my comment"
  //        date_creation="2004-08-14" name="" />
  $xml_infos = '<infos';
  $xml_infos.= encodeAttribute('author', $_POST['author']);
  $xml_infos.= encodeAttribute('comment', $_POST['comment']);
  $xml_infos.= encodeAttribute('date_creation', $date_creation);
  $xml_infos.= encodeAttribute('name', $_POST['name']);
  $xml_infos.= ' />';

  if ( !preg_match( '/^[a-zA-Z0-9-_.]+$/', $_FILES['picture']['name'] ) )
  {
    array_push( $error, l10n('update_wrong_dirname') );
  }

  if ( sizeof( $error ) == 0 )
  {
    $result = validate_upload( $path, $conf['upload_maxfilesize'],
                               $conf['upload_maxwidth'],
                               $conf['upload_maxheight']  );
    for ( $j = 0; $j < sizeof( $result['error'] ); $j++ )
    {
      array_push( $error, $result['error'][$j] );
    }
  }

  if ( sizeof( $error ) == 0 )
  {
    $query = 'insert into '.WAITING_TABLE;
    $query.= ' (storage_category_id,file,username,mail_address,date,infos)';
    $query.= ' values ';
    $query.= '('.$page['category'].",'".$_FILES['picture']['name']."'";
    $query.= ",'".htmlspecialchars( $_POST['username'], ENT_QUOTES)."'";
    $query.= ",'".$_POST['mail_address']."',".time().",'".$xml_infos."')";
    $query.= ';';
    pwg_query( $query );
    $page['waiting_id'] = mysql_insert_id();

    if ($conf['email_admin_on_picture_uploaded'])
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $waiting_url = get_absolute_root_url().'admin.php?page=upload';

      $keyargs_content = array
      (
        get_l10n_args('Category: %s', get_cat_display_name($category['upper_names'], null, false)),
        get_l10n_args('Picture name: %s', $_FILES['picture']['name']),
        get_l10n_args('User: %s', $_POST['username']),
        get_l10n_args('Email: %s', $_POST['mail_address']),
        get_l10n_args('Picture name: %s', $_POST['name']),
        get_l10n_args('Author: %s', $_POST['author']),
        get_l10n_args('Creation date: %s', $_POST['date_creation']),
        get_l10n_args('Comment: %s', $_POST['comment']),
        get_l10n_args('', ''),
        get_l10n_args('Waiting page: %s', $waiting_url)
      );

      pwg_mail_notification_admins
      (
        get_l10n_args('Picture uploaded by %s', $_POST['username']),
        $keyargs_content
      );
    }
  }
}

//------------------------------------------------------------ thumbnail upload
if ( isset( $_POST['submit'] ) and isset( $_GET['waiting_id'] ) )
{
  // upload of the thumbnail
  $query = 'select file';
  $query.= ' from '.WAITING_TABLE;
  $query.= ' where id = '.$_GET['waiting_id'];
  $query.= ';';
  $result= pwg_query( $query );
  $row = mysql_fetch_array( $result );
  $file = substr ( $row['file'], 0, strrpos ( $row['file'], ".") );
  $extension = get_extension( $_FILES['picture']['name'] );

  if (($path = mkget_thumbnail_dir($category['cat_dir'], $error)) != false)
  {
    $path.= '/'.$conf['prefix_thumbnail'].$file.'.'.$extension;
    $result = validate_upload( $path, $conf['upload_maxfilesize'],
                               $conf['upload_maxwidth_thumbnail'],
                               $conf['upload_maxheight_thumbnail']  );
    for ( $j = 0; $j < sizeof( $result['error'] ); $j++ )
    {
      array_push( $error, $result['error'][$j] );
    }
  }

  if ( sizeof( $error ) == 0 )
  {
    $query = 'update '.WAITING_TABLE;
    $query.= " set tn_ext = '".$extension."'";
    $query.= ' where id = '.$_GET['waiting_id'];
    $query.= ';';
    pwg_query( $query );
    $page['upload_successful'] = true;
  }
}

//
// Start output of page
//
$title= l10n('upload_title');
$page['body_id'] = 'theUploadPage';
include(PHPWG_ROOT_PATH.'include/page_header.php');
$template->set_filenames(array('upload'=>'upload.tpl'));

$u_form = PHPWG_ROOT_PATH.'upload.php?cat='.$page['category'];
if ( isset( $page['waiting_id'] ) )
{
$u_form.= '&amp;waiting_id='.$page['waiting_id'];
}

if ( isset( $page['waiting_id'] ) )
{
  $advise_title=l10n('upload_advise_thumbnail').$_FILES['picture']['name'];
}
else
{
  $advise_title = l10n('upload_advise');
  $advise_title.= get_cat_display_name($category['upper_names']);
}

$template->assign(
  array(
    'ADVISE_TITLE' => $advise_title,
    'NAME' => $username,
    'EMAIL' => $mail_address,
    'NAME_IMG' => $name,
    'AUTHOR_IMG' => $author,
    'DATE_IMG' => $date_creation,
    'COMMENT_IMG' => $comment,

    'F_ACTION' => $u_form,

    'U_RETURN' => make_index_url(array('category' => $category)),
    )
  );

$template->assign('errors', $error);
$template->assign('UPLOAD_SUCCESSFUL', $page['upload_successful'] );

if ( !$page['upload_successful'] )
{
//--------------------------------------------------------------------- advises
  if ( !empty($conf['upload_maxfilesize']) )
  {
    $content = l10n('upload_advise_filesize');
    $content.= $conf['upload_maxfilesize'].' KB';
    $template->append('advises', $content);
  }

  if ( isset( $page['waiting_id'] ) )
  {
    if ( $conf['upload_maxwidth_thumbnail'] != '' )
    {
      $content = l10n('upload_advise_width');
      $content.= $conf['upload_maxwidth_thumbnail'].' px';
      $template->append('advises', $content);
    }
    if ( $conf['upload_maxheight_thumbnail'] != '' )
    {
      $content = l10n('upload_advise_height');
      $content.= $conf['upload_maxheight_thumbnail'].' px';
      $template->append('advises', $content);
    }
  }
  else
  {
    if ( $conf['upload_maxwidth'] != '' )
    {
      $content = l10n('upload_advise_width');
      $content.= $conf['upload_maxwidth'].' px';
      $template->append('advises', $content);
    }
    if ( $conf['upload_maxheight'] != '' )
    {
      $content = l10n('upload_advise_height');
      $content.= $conf['upload_maxheight'].' px';
      $template->append('advises', $content);
    }
  }
  $template->append('advises', l10n('upload_advise_filetype'));

//----------------------------------------- optionnal username and mail address
  if ( !isset( $page['waiting_id'] ) )
  {
    $template->assign('SHOW_FORM_FIELDS', true);
  }
}

//----------------------------------------------------------- html code display
$template->parse('upload');
include(PHPWG_ROOT_PATH.'include/page_tail.php');
?>
