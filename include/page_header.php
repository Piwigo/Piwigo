<?php
/***************************************************************************
 *                              page_header.php                            *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
//
// Start output of page
//
$vtp = new VTemplate;
$handle = $vtp->Open( './template/'.$user['template'].'/header.vtp' );
$vtp->setGlobalVar( $handle, 'charset', $lang['charset'] );
$vtp->setGlobalVar( $handle, 'style',
                    './template/'.$user['template'].'/default.css');

// refresh
if ( isset( $refresh ) and $refresh > 0 and isset( $url_link ) )
{
  $vtp->addSession( $handle, 'refresh' );
  $vtp->setVar( $handle, 'refresh.time', $refresh );
  $url = $url_link.'&amp;slideshow='.$refresh;
  $vtp->setVar( $handle, 'refresh.url', add_session_id( $url ) );
  $vtp->closeSession( $handle, 'refresh' );
}

$vtp->setGlobalVar( $handle, 'title', $title );
$vtp->setVarF( $handle, 'header',
               './template/'.$user['template'].'/header.htm' );

//
// Generate the page
//

$output = $vtp->Display( $handle, 0 );
?>