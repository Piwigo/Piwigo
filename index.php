<?php
/***************************************************************************
 *                                  index.php                              *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

define( 'PREFIX_INCLUDE', '' );
include_once( './include/functions.inc.php' );
database_connection();
// retrieving configuration informations
$query = 'SELECT access';
$query.= ' FROM '.PREFIX_TABLE.'config;';
$row = mysql_fetch_array( mysql_query( $query ) );
if ( $row['access'] == 'restricted' )
{
  if ( isset( $_COOKIE['id'] ) ) $url = 'category';
  else                           $url = 'identification';
}
else                             $url = 'category';
// redirection
$url.= '.php';
header( 'Request-URI: '.$url );  
header( 'Content-Location: '.$url );  
header( 'Location: '.$url );
exit();
?>