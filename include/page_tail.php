<?php
/***************************************************************************
 *                              page_footer.php                            *
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

$handle = $vtp->Open( './template/'.$user['template'].'/footer.vtp' );
//------------------------------------------------------------- generation time
$time = get_elapsed_time( $t2, get_moment() );
$vtp->setGlobalVar( $handle, 'time', $time );

$vtp->setGlobalVar( $handle, 'generation_time', $lang['generation_time'] );
$vtp->setGlobalVar( $handle, 'version', $conf['version'] );
$vtp->setGlobalVar( $handle, 'site_url', $conf['site_url'] );
$vtp->setVarF( $handle, 'footer',
               './template/'.$user['template'].'/footer.htm' );

//
// Generate the page
//

$output.= $vtp->Display( $handle, 0 );
echo $output;
?>