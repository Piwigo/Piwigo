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

// +-----------------------------------------------------------------------+
// |                                 misc                                  |
// +-----------------------------------------------------------------------+

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

// calendar_datefield : date field of table "images" used for calendar
// catgory
$conf['calendar_datefield'] = 'date_creation';

// rate : enable feature for rating elements
$conf['rate'] = true;

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

// show_thumbnail_caption : on thumbnails page, show thumbnail captions ?
$conf['show_thumbnail_caption'] = true;

// show_picture_name_on_title : on picture presentation page, show picture
// name ?
$conf['show_picture_name_on_title'] = true;

// allow_random_representative : do you wish PhpWebGallery to search among
// categories elements a new representative at each reload ?
//
// If false, an element is randomly or manually chosen to represent its
// category and remains the representative as long as an admin does not
// change it.
//
// Warning : setting this parameter to true is CPU consuming. Each time you
// change the value of this parameter from false to true, an administrator
// must update categories informations in screen [Admin > General >
// Maintenance].
$conf['allow_random_representative'] = false;

// allow_html_descriptions : authorize administrators to use HTML in
// category and element description.
$conf['allow_html_descriptions'] = true;

// gallery_title : Title at top of each page and for RSS feed
$conf['gallery_title'] = 'PhpWebGallery demonstration site';

// gallery_description : Short description displayed with gallery title
$conf['gallery_description'] = 'My photos web site';

// galery_url : URL given in RSS feed
$conf['gallery_url'] = 'http://demo.phpwebgallery.net';

// prefix_thumbnail : string before filename. Thumbnail's prefix must only
// contain characters among : a to z (case insensitive), "-" or "_".
$conf['prefix_thumbnail'] = 'TN-';

// +-----------------------------------------------------------------------+
// |                               metadata                                |
// +-----------------------------------------------------------------------+

// show_iptc: Show IPTC metadata on picture.php if asked by user
$conf['show_iptc'] = false;

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

// use_iptc: Use IPTC data during database synchronization with files
// metadata
$conf['use_iptc'] = false;

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

// show_exif: Show EXIF metadata on picture.php (table or line presentation
// avalaible)
$conf['show_exif'] = true;

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
$conf['show_exif_fields'] = array(
  'Make',
  'Model',
  'DateTimeOriginal',
  'COMPUTED;ApertureFNumber'
  );

// use_exif: Use EXIF data during database synchronization with files
// metadata
$conf['use_exif'] = false;

// use_exif_mapping: same behaviour as use_iptc_mapping
$conf['use_exif_mapping'] = array(
  'date_creation' => 'DateTimeOriginal'
  );

// +-----------------------------------------------------------------------+
// |                               sessions                                |
// +-----------------------------------------------------------------------+

// authorize_remembering : permits user to stay logged for a long time. It
// creates a cookie on client side.
$conf['authorize_remembering'] = true;

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

// +-----------------------------------------------------------------------+
// |                                debug                                  |
// +-----------------------------------------------------------------------+

// show_queries : for debug purpose, show queries and execution times
$conf['show_queries'] = false;

// show_gt : display generation time at the bottom of each page
$conf['show_gt'] = true;

// debug_l10n : display a warning message each time an unset language key is
// accessed
$conf['debug_l10n'] = false;

// +-----------------------------------------------------------------------+
// |                            authentication                             |
// +-----------------------------------------------------------------------+

// apache_authentication : use Apache authentication as reference instead of
// users table ?
$conf['apache_authentication'] = false;

// users_table: which table is the reference for users? Can be a different
// table than PhpWebGallery table
//
// If you decide to use another table than the default one, you need to
// prepare your database by deleting some datas :
//
// delete from phpwebgallery_user_access;
// delete from phpwebgallery_user_cache;
// delete from phpwebgallery_user_feed;
// delete from phpwebgallery_user_group;
// delete from phpwebgallery_user_infos;
// delete from phpwebgallery_sessions;
// delete from phpwebgallery_rate;
// update phpwebgallery_images set average_rate = NULL;
// delete from phpwebgallery_caddie;
// delete from phpwebgallery_favorites;
//
// All informations contained in these tables and column are related to
// phpwebgallery_users table.
$conf['users_table'] = $prefixeTable.'users';

// user_fields : mapping between generic field names and table specific
// field names. For example, in PWG, the mail address is names
// "mail_address" and in punbb, it's called "email".
$conf['user_fields'] = array(
  'id' => 'id',
  'username' => 'username',
  'password' => 'password',
  'email' => 'mail_address'
  );

// pass_convert : function to crypt or hash the clear user password to store
// it in the database
$conf['pass_convert'] = create_function('$s', 'return md5($s);');

// guest_id : id of the anonymous user
$conf['guest_id'] = 2;

// webmaster_id : webmaster'id.
$conf['webmaster_id'] = 1;

// +-----------------------------------------------------------------------+
// |                                upload                                 |
// +-----------------------------------------------------------------------+

// upload_maxfilesize: maximum filesize for the uploaded pictures. In
// kilobytes.
$conf['upload_maxfilesize'] = 200;

// upload_maxheight: maximum height authorized for the uploaded images. In
// pixels.
$conf['upload_maxheight'] = 800;

// upload_maxwidth: maximum width authorized for the uploaded images. In
// kilobytes.
$conf['upload_maxwidth'] = 800;

// upload_maxheight_thumbnail: maximum height authorized for the uploaded
// thumbnails
$conf['upload_maxheight_thumbnail'] = 100;

// upload_maxwidth_thumbnail: maximum width authorized for the uploaded
// thumbnails
$conf['upload_maxwidth_thumbnail'] = 150;
?>
