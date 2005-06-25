<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
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

/**
 *                           configuration page
 *
 * Set configuration parameters that are not in the table config. In the
 * application, configuration parameters are considered in the same way
 * coming from config table or config_default.inc.php.
 *
 * It is recommended to let config_default.inc.php as provided and to
 * overwrite configuration in your local configuration file
 * config_local.inc.php
 *
 * Why having some parameters in config table and others in
 * config_*.inc.php? Modifying config_*.inc.php is a "hard" task for low
 * skilled users, they need a GUI for this : admin/configuration. But only
 * parameters that might be modified by low skilled users are in config
 * table, other parameters are in config_*.inc.php
 */

// order_by : how to change the order of display for images in a category ?
//
// There are several fields that can order the display :
//
//  - date_available : the date of the adding to the gallery
//  - file : the name of the file
//  - id : element identifier
//  - date_creation : date of element creation
//
// Once you've chosen which field(s) to use for ordering, you must chose the
// ascending or descending order for each field.  examples :
//
// 1. $conf['order_by'] = " order by date_available desc, file asc";
//    will order pictures by date_available descending & by filename ascending
//
// 2. $conf['order_by'] = " order by file asc";
//    will only order pictures by file ascending without taking into account
//    the date_available
$conf['order_by'] = ' ORDER BY date_available DESC, file ASC, id ASC';

// slideshow_period : waiting time in seconds before loading a new page
// during automated slideshow
$conf['slideshow_period'] = 4;

// last_days : options for X last days to displays for comments
$conf['last_days'] = array(1,2,3,10,30,365);

// file_ext : file extensions (case sensitive) authorized
$conf['file_ext'] = array('jpg','JPG','png','PNG','gif','GIF','mpg','zip',
                          'avi','mp3','ogg');

// picture_ext : file extensions for picture file, must be a subset of
// file_ext
$conf['picture_ext'] = array('jpg','JPG','png','PNG','gif','GIF');

// top_number : number of element to display for "best rated" and "most
// visited" categories
$conf['top_number'] = 15;

// anti-flood_time : number of seconds between 2 comments : 0 to disable
$conf['anti-flood_time'] = 60;

// show_iptc_mapping : is used for showing IPTC metadata on picture.php
// page. For each key of the array, you need to have the same key in the
// $lang array. For example, if my first key is 'iptc_keywords' (associated
// to '2#025') then you need to have $lang['iptc_keywords'] set in
// language/$user['language']/common.lang.php. If you don't have the lang
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

// use_iptc_mapping : in which IPTC fields will PhpWebGallery find image
// information ? This setting is used during metadata synchronisation. It
// associates a phpwebgallery_images column name to a IPTC key
$conf['use_iptc_mapping'] = array(
  'keywords'        => '2#025',
  'date_creation'   => '2#055',
  'author'          => '2#122',
  'name'            => '2#005',
  'comment'         => '2#120'
  );

// show_exif_fields : in EXIF fields, you can choose to display fields in
// sub-arrays, for example ['COMPUTED']['ApertureFNumber']. for this, add
// 'COMPUTED;ApertureFNumber' in $conf['show_exif_fields']
//
// The key displayed in picture.php will be $lang['exif_field_Make'] for
// example and if it exists. For compound fields, only take into account the
// last part : for key 'COMPUTED;ApertureFNumber', you need
// $lang['exif_field_ApertureFNumber']
//
// for PHP version newer than 4.1.2 :
// $conf['show_exif_fields'] = array('CameraMake','CameraModel','DateTime');
// 
$conf['show_exif_fields'] = array('Make',
                                  'Model',
                                  'DateTime',
                                  'COMPUTED;ApertureFNumber');

// calendar_datefield : date field of table "images" used for calendar
// catgory
$conf['calendar_datefield'] = 'date_creation';

// rate : enable feature for rating elements
$conf['rate'] = true;

// remember_me_length : time of validity for "remember me" cookies, in
// seconds.
$conf['remember_me_length'] = 31536000;

// session_length : time of validity for normal session, in seconds.
$conf['session_length'] = 3600;

// session_id_size : a session identifier is compound of alphanumeric
// characters and is case sensitive. Each character is among 62
// possibilities. The number of possible sessions is
// 62^$conf['session_id_size'].
//
// 62^5  =             916,132,832
// 62^10 = 839,299,365,868,340,224
//
$conf['session_id_size'] = 10;

// info_nb_elements_page : number of elements to display per page on
// admin/infos_images
$conf['info_nb_elements_page'] = 5;

// show_queries : for debug purpose, show queries and execution times
$conf['show_queries'] = false;

// show_gt : display generation time at the bottom of each page
$conf['show_gt'] = true;

// newcat_default_commentable : at creation, must a category be commentable
// or not ?
$conf['newcat_default_commentable'] = 'true';

// newcat_default_uploadable : at creation, must a category be uploadable or
// not ?
$conf['newcat_default_uploadable'] = 'false';

// newcat_default_visible : at creation, must a category be visible or not ?
// Warning : if the parent category is invisible, the category is
// automatically create invisible. (invisible = locked)
$conf['newcat_default_visible'] = 'true';

// newcat_default_status : at creation, must a category be public or private
// ? Warning : if the parent category is private, the category is
// automatically create private.
$conf['newcat_default_status'] = 'public';

// level_separator : character string used for separating a category level
// to the sub level. Suggestions : ' / ', ' &raquo; ', ' &rarr; ', ' - ',
// ' &gt;'
$conf['level_separator'] = ' / ';

// paginate_pages_around : on paginate navigation bar, how many pages
// display before and after the current page ?
$conf['paginate_pages_around'] = 2;

// tn_width : default width for thumbnails creation
$conf['tn_width'] = 128;

// tn_height : default height for thumbnails creation
$conf['tn_height'] = 96;

// show_version : shall the version of PhpWebGallery be displayed at the
// bottom of each page ?
$conf['show_version'] = true;

// links : list of external links to add in the menu. An example is the best
// than a long explanation :
//
// $conf['links'] = array(
//   'http://phpwebgallery.net' => 'PWG website',
//   'http://forum.phpwebgallery.net' => 'PWG forum',
//   'http://phpwebgallery.net/doc' => 'PWG wiki'
//   );
//
// If the array is empty, the "Links" box won't be displayed on the main
// page.
$conf['links'] = array();
?>
