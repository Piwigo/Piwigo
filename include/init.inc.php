<?php
/***************************************************************************
 *                               init.inc.php                              *
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
define( PREFIX_INCLUDE, '' );

include_once( './include/config.inc.php' );
include_once( './include/user.inc.php' );

// calculation of the number of picture to display per page
$user['nb_image_page'] = $user['nb_image_line'] * $user['nb_line_page'];
// retrieving the restrictions for this user
$user['restrictions'] = get_restrictions( $user['id'], $user['status'], true );
        
$isadmin = false;
include_once( './language/'.$user['language'].'.php' );
// displaying the username in the language of the connected user, instead of
// "guest" as you can find in the database
if ( $user['is_the_guest'] ) $user['username'] = $lang['guest'];
include_once( './template/'.$user['template'].'/htmlfunctions.inc.php' );
?>