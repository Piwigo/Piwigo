<?php
// +-----------------------------------------------------------------------+
// |                            config.inc.php                             |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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
$conf['version']            = 'BSF';
$conf['site_url']           = 'http://www.phpwebgallery.net';
$conf['forum_url']          = 'http://forum.phpwebgallery.net';
// $conf['file_ext'] lists all extensions (case insensitive) allowed for
// your PhpWebGallery installation
$conf['file_ext']           = array('jpg','JPG','png','PNG','gif','GIF'
                                    ,'mpg','zip','avi','mp3','ogg');
// $conf['picture_ext'] must bea subset of $conf['file_ext']
$conf['picture_ext']        = array('jpg','JPG','png','PNG','gif','GIF');
$conf['top_number']         = 10;
$conf['anti-flood_time']    = 60; // seconds between 2 comments : 0 to disable
$conf['max_LOV_categories'] = 50;

// $conf['show_iptc_mapping'] is used for showing IPTC metadata on
// picture.php page. For each key of the array, you need to have the same
// key in the $lang array. For example, if my first key is 'iptc_keywords'
// (associated to '2#025') then you need to have $lang['iptc_keywords'] set
// in language/$user['language']/common.lang.php. If you don't have the lang
// var set, the key will be simply displayed
//
// To know how to associated iptc_field with their meaning, use
// tools/metadata.php
$conf['show_iptc_mapping'] = array(
  'iptc_keywords'        => '2#025',
  'iptc_caption_writer'  => '2#122',
  'iptc_byline_title'    => '2#085',
  'iptc_caption'         => '2#120'
  );

// in EXIF fields, you can choose to display fields in sub-arrays, for
// example ['COMPUTED']['ApertureFNumber']. for this, add
// 'COMPUTED;ApertureFNumber' in $conf['show_exif_fields']
//
// The key displayed in picture.php will be $lang['exif_field_Make'] for
// example and if it exists. For compound fields, only take into account the
// last part : for key 'COMPUTED;ApertureFNumber', you need
// $lang['exif_field_ApertureFNumber']
$conf['show_exif_fields'] = array('Make',
                                  'Model',
                                  'DateTime',
                                  'COMPUTED;ApertureFNumber');
// for PHP version newer than 4.1.2 :
// $conf['show_exif_fields'] = array('CameraMake','CameraModel','DateTime');
?>
