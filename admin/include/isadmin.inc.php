<?php
/***************************************************************************
 *                              isadmin.inc.php                            *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
define( PREFIXE_INCLUDE, '.' );

include_once( '../include/config.inc.php' );
include_once( '../include/user.inc.php' );
include( './include/functions.php' );

$conf['lien_puce'] = $conf['repertoire_image'].'puce.gif';

$isadmin = true;
include_once( '../language/'.$user['language'].'.php' );

if ( $user['status'] != 'admin' )
{
  echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
  echo '<a href="../identification.php">'.$lang['ident_title'].'</a></div>';
  exit();
}
?>