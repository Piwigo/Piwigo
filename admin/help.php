<?php
/***************************************************************************
 *                                  help.php                               *
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
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/help.vtp' );
$tpl = array( );
templatize_array( $tpl, 'lang', $sub );
//----------------------------------------------------- help categories display
$categories = array( 'images','thumbnails','database','remote','upload',
                     'infos' );
foreach ( $categories as $category ) {
  $vtp->addSession( $sub, 'cat' );
  if ( $category == 'images' )
  {
    $vtp->addSession( $sub, 'illustration' );
    $vtp->setVar( $sub, 'illustration.pic_src', './images/admin.png' );
    $vtp->setVar( $sub, 'illustration.pic_alt', '' );
    $vtp->setVar( $sub, 'illustration.caption', $lang['help_images_intro'] );
    $vtp->closeSession( $sub, 'illustration' );
  }
  $vtp->setVar( $sub, 'cat.name', $lang['help_'.$category.'_title'] );
  foreach ( $lang['help_'.$category] as $item ) {
    $vtp->addSession( $sub, 'item' );
    $vtp->setVar( $sub, 'item.content', $item );
    $vtp->closeSession( $sub, 'item' );
  }

  $vtp->closeSession( $sub, 'cat' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>