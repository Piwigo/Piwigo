<?php
/***************************************************************************
 *                  search.php is a part of PhpWebGallery                  *
 *                            -------------------                          *
 *   last update          : Wednesday, July 25, 2002                       *
 *   email                : pierrick@z0rglub.com                           *
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
//-------------------------------------------------- access authorization check
check_login_authorization();
//----------------------------------------------------------------- redirection
$error = array();
if ( isset( $_POST['search'] ) )
{
  $i = 0;
  if ( strlen( $_POST['search'] ) > 2 )
  {
    $url = add_session_id( 'category.php?cat=search&search='.
                           $_POST['search'], true );
    header( 'Request-URI: '.$url );
    header( 'Content-Location: '.$url );  
    header( 'Location: '.$url );
    exit();
  }
  else
  {
    $error[$i++] = $lang['invalid_search'];
  }
}
//----------------------------------------------------- template initialization
$vtp = new VTemplate;
$handle = $vtp->Open( './template/default/search.vtp' );
// language
$vtp->setGlobalVar( $handle, 'search_page_title',$lang['search_title'] );
$vtp->setGlobalVar( $handle, 'search_title',     $lang['search_title'] );
$vtp->setGlobalVar( $handle, 'search_return_main_page',
                    $lang['search_return_main_page'] );
$vtp->setGlobalVar( $handle, 'submit',           $lang['submit'] );
// user
$vtp->setGlobalVar( $handle, 'page_style',       $user['style'] );
// structure
$vtp->setGlobalVar( $handle, 'frame_start',      get_frame_start() );
$vtp->setGlobalVar( $handle, 'frame_begin',      get_frame_begin() );
$vtp->setGlobalVar( $handle, 'frame_end',        get_frame_end() );
//----------------------------------------------------------------- form action
$vtp->setGlobalVar( $handle, 'form_action', add_session_id( './search.php' ) );
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
//---------------------------------------------------------------- search field
$vtp->addSession( $handle, 'line' );
$vtp->setVar( $handle, 'line.name', $lang['search_field_search'] );
$vtp->addSession( $handle, 'text' );
$vtp->setVar( $handle, 'text.size', '40' );
$vtp->setVar( $handle, 'text.name', 'search' );
$vtp->setVar( $handle, 'text.value', $_POST['search'] );
$vtp->closeSession( $handle, 'text' );
$vtp->closeSession( $handle, 'line' );
//---------------------------------------------------- return to main page link
$vtp->setGlobalVar( $handle, 'back_url', add_session_id( './category.php' ) );
//----------------------------------------------------------- html code display
$code = $vtp->Display( $handle, 0 );
echo $code;
//------------------------------------------------------------ log informations
$query = 'insert into '.$prefixeTable.'history';
$query.= '(date,login,IP,page) values';
$query.= "('".time()."', '".$user['pseudo']."','".$_SERVER['REMOTE_ADDR']."'";
$query.= ",'search');";
@mysql_query( $query );
?>