<?php
/***************************************************************************
 *                              config.inc.php                             *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.4 <http://phpwebgallery.net>          *
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
// How to change the order of display for images in a category ?
//
// You have to modify $conf['order_by'].
// There are several fields that can order the display :
//  - date_available : the date of the adding to the gallery
//  - file : the name of the file
// Once you've chosen which field(s) to use for ordering,
// you must chose the ascending or descending order for each field.
// examples :
// 1. $conf['order_by'] = " order by date_available desc, file asc";
//    will order pictures by date_available descending & by filename ascending
// 2. $conf['order_by'] = " order by file asc";
//    will only order pictures by file ascending
//    without taking into account the date_available
$conf['order_by'] = ' ORDER BY date_available DESC, file ASC';

$conf['nb_image_row']       = array(4,5,6,7,8);
$conf['nb_row_page']        = array(2,3,4,5,6,7,10,20,1000);
$conf['slideshow_period']   = array(2,5,10);
$conf['last_days']          = array(1,2,3,10,30,365);
$conf['version']            = '1.4 - devel';
$conf['site_url']           = 'http://www.phpwebgallery.net';
$conf['forum_url']          = 'http://forum.phpwebgallery.net';
$conf['picture_ext']        = array('jpg','JPG','gif','GIF','png','PNG');
$conf['document_ext']       = array('doc','pdf','zip');
$conf['top_number']         = 10;
$conf['anti-flood_time']    = 60; // seconds between 2 comments : 0 to disable
$conf['max_LOV_categories'] = 50;

$conf['default_lang'] = 'francais';
$conf['default_style'] = 'default';

?>