<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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
 * local/config/config.inc.php. See tools/config.inc.php as an example.
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

// order_by_inside_category : inside a category, images can also be ordered
// by rank. A manually defined rank on each image for the category.
//
// In addition to fields of #images table, you can use the
// #image_category.rank column
//
// $conf['order_by_inside_category'] = ' ORDER BY rank';
// will sort images by the manually defined rank of images in this album.
$conf['order_by_inside_category'] = $conf['order_by'];

// file_ext : file extensions (case sensitive) authorized
$conf['file_ext'] = array('jpg','JPG','jpeg','JPEG',
                          'png','PNG','gif','GIF','mpg','zip',
                          'avi','mp3','ogg');

// picture_ext : file extensions for picture file, must be a subset of
// file_ext
$conf['picture_ext'] = array('jpg','JPG','jpeg','JPEG',
                             'png','PNG','gif','GIF');

// top_number : number of element to display for "best rated" and "most
// visited" categories
$conf['top_number'] = 15;

// anti-flood_time : number of seconds between 2 comments : 0 to disable
$conf['anti-flood_time'] = 60;

// qualified spam comments are not registered (false will register them
// but they will require admin validation)
$conf['comment_spam_reject'] = true;

// maximum number of links in a comment before it is qualified spam
$conf['comment_spam_max_links'] = 3;

// calendar_datefield : date field of table "images" used for calendar
// catgory
$conf['calendar_datefield'] = 'date_creation';

// calendar_show_any : the calendar shows an aditional 'any' button in the
// year/month/week/day navigation bars
$conf['calendar_show_any'] = true;

// calendar_show_empty : the calendar shows month/weeks/days even if there are
//no elements for these
$conf['calendar_show_empty'] = true;

// calendar_month_cell_width, calendar_month_cell_height : define the
// width and the height of a cell in the monthly calendar when viewing a
// given month. a value of 0 means that the pretty view is not shown.
// a good suggestion would be to have the width and the height equal
// and smaller than tn_width and tn_height.
$conf['calendar_month_cell_width'] =80;
$conf['calendar_month_cell_height']=80;

// newcat_default_commentable : at creation, must a category be commentable
// or not ?
$conf['newcat_default_commentable'] = true;

// newcat_default_uploadable : at creation, must a category be uploadable or
// not ?
$conf['newcat_default_uploadable'] = false;

// newcat_default_visible : at creation, must a category be visible or not ?
// Warning : if the parent category is invisible, the category is
// automatically create invisible. (invisible = locked)
$conf['newcat_default_visible'] = true;

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
$conf['tn_height'] = 128;

// tn_compression_level: compression level for thumbnail creation. 0 is low
// quality, 100 is high quality.
$conf['tn_compression_level'] = 75;

// show_version : shall the version of Piwigo be displayed at the
// bottom of each page ?
$conf['show_version'] = true;

// meta_ref to reference multiple sets of incorporated pages or elements
// Set it false to avoid referencing in google, and other search engines.
$conf['meta_ref'] = true;

// links : list of external links to add in the menu. An example is the best
// than a long explanation :
//
// Simple use:
//  for each link is associated a label
//  $conf['links'] = array(
//    'http://piwigo.org' => 'PWG website',
//    'http://piwigo.org/forum' => 'PWG forum',
//    );
//
// Advenced use:
//  You can also used special options. Instead to pass a string like parameter value
//  you can pass a array with different optional parameter values
//  $conf['links'] = array(
//    'http://piwigo.org' => array('label' => 'PWG website', 'new_window' => false, 'eval_visible' => 'return true;'),
//    'http://piwigo.org/forum' => array('label' => 'For ADMIN', 'new_window' => true, 'eval_visible' => 'return is_admin();'),
//    'http://piwigo.org/ext' => array('label' => 'For Guest', 'new_window' => true, 'eval_visible' => 'return is_a_guest();'),
//    'http://piwigo.org/downloads' =>
//      array('label' => 'PopUp', 'new_window' => true,
//      'nw_name' => 'PopUp', 'nw_features' => 'width=800,height=450,location=no,status=no,toolbar=no,scrollbars=no,menubar=no'),
//    );
// Parameters:
//  'label':
//    Label to display for the link, must be defined
//  'new_window':
//    If true open link on tab/window
//    [Default value is true if it's not defined]
//  'nw_name':
//    Name use when new_window is true
//    [Default value is '' if it's not defined]
//  'nw_features':
//    features use when new_window is true
//    [Default value is '' if it's not defined]
//  'eval_visible':
//    It's php code witch must return if the link is visible or not
//    [Default value is true if it's not defined]
//
// Equivalence:
//  $conf['links'] = array(
//    'http://piwigo.org' => 'PWG website',
//    );
//  $conf['links'] = array(
//    'http://piwigo.org' => array('label' => 'PWG website', 'new_window' => false, 'visible' => 'return true;'),
//    );
//
// If the array is empty, the "Links" box won't be displayed on the main
// page.
$conf['links'] = array();

// random_index_redirect: list of 'internal' links to use when no section is defined on index.php.
// An example is the best than a long explanation :
//
//  for each link is associated a php condition
//  '' condition is equivalent to 'return true;'
//  $conf['random_index_redirect'] = array(
//    PHPWG_ROOT_PATH.'index.php?/best_rated' => 'return true;',
//    PHPWG_ROOT_PATH.'index.php?/recent_pics' => 'return is_a_guest();',
//    PHPWG_ROOT_PATH.'random.php' => '',
//    PHPWG_ROOT_PATH.'index.php?/categories' => '',
//    );
$conf['random_index_redirect'] = array();

// reverse_home_title: if Piwigo is your home page for a better robot index
// we recommend to set it true (Only index page will reverse its title)
$conf['reverse_home_title'] = false;

// List of notes to display on all header page
// example $conf['header_notes']  = array('Test', 'Hello');
$conf['header_notes']  = array();

// show_thumbnail_caption : on thumbnails page, show thumbnail captions ?
$conf['show_thumbnail_caption'] = true;

// show_picture_name_on_title : on picture presentation page, show picture
// name ?
$conf['show_picture_name_on_title'] = true;

// display_fromto: display the date creation bounds of a
// category.
$conf['display_fromto'] = false;

// allow_random_representative : do you wish Piwigo to search among
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

// prefix_thumbnail : string before filename. Thumbnail's prefix must only
// contain characters among : a to z (case insensitive), "-" or "_".
$conf['prefix_thumbnail'] = 'TN-';

// dir_thumbnail : directory where thumbnail reside.
$conf['dir_thumbnail'] = 'thumbnail';

// users_page: how many users to display in screen
// Administration>Identification>Users?
$conf['users_page'] = 20;

// image level permissions available in the admin interface
$conf['available_permission_levels'] = array(0,1,2,4,8);

// mail_options: only set it true if you have a send mail warning with
// "options" parameter missing on mail() function execution.
$conf['mail_options'] = false;

// send_bcc_mail_webmaster: send bcc mail to webmaster. Set true for debug
// or test.
$conf['send_bcc_mail_webmaster'] = false;

// default_email_format:
//  Define the default email format use to send email
//  Value could be text/plain  or text/html
$conf['default_email_format'] = 'text/html';

// alternative_email_format:
//  Define the alternative email format use to send email
//  Value could be text/plain  or text/html
$conf['alternative_email_format'] = 'text/plain';

// define the name of sender mail:
// If value is empty, gallery title is used
$conf['mail_sender_name'] = '';

// smtp configuration
// (work if fsockopen function is allowed for smtp port)
// smtp_host: smtp server host
//  if null, regular mail function is used
//   format: hoststring[:port]
//   exemple: smtp.pwg.net:21
// smtp_user/smtp_password: user & password for smtp identication
$conf['smtp_host'] = '';
$conf['smtp_user'] = '';
$conf['smtp_password'] = '';


// check_upgrade_feed: check if there are database upgrade required. Set to
// true, a message will strongly encourage you to upgrade your database if
// needed.
//
// This configuration parameter is set to true in BSF branch and to false
// elsewhere.
$conf['check_upgrade_feed'] = true;

// rate_items: available rates for a picture
$conf['rate_items'] = array(0,1,2,3,4,5);

// Define default method to use ('http' or 'html' in order to do redirect)
$conf['default_redirect_method'] = 'http';

// Define using double password type in admin's users management panel
$conf['double_password_type_in_admin'] = false;

// Define if logins must be case sentitive or not at users registration. ie :
// If set true, the login "user" will equal "User" or "USER" or "user",
// etc. ... And it will be impossible to use such login variation to create a
// new user account.
$conf['insensitive_case_logon'] = false;

// how should we check for unicity when adding a photo. Can be 'md5sum' or
// 'filename'
$conf['uniqueness_mode'] = 'md5sum';

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

// use_iptc_mapping : in which IPTC fields will Piwigo find image
// information ? This setting is used during metadata synchronisation. It
// associates a piwigo_images column name to a IPTC key
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
$conf['use_exif'] = true;

// use_exif_mapping: same behaviour as use_iptc_mapping
$conf['use_exif_mapping'] = array(
  'date_creation' => 'DateTimeOriginal'
  );

// +-----------------------------------------------------------------------+
// |                               sessions                                |
// +-----------------------------------------------------------------------+

// session_use_cookies: specifies to use cookie to store
// the session id on client side
$conf['session_use_cookies'] = true;

// session_use_only_cookies: specifies to only use cookie to store
// the session id on client side
$conf['session_use_only_cookies'] = true;

// session_use_trans_sid: do not use transparent session id support
$conf['session_use_trans_sid'] = false;

// session_name: specifies the name of the session which is used as cookie name
$conf['session_name'] = 'pwg_id';

// session_save_handler: comment the line below
// to use file handler for sessions.
$conf['session_save_handler'] = 'db';

// authorize_remembering : permits user to stay logged for a long time. It
// creates a cookie on client side.
$conf['authorize_remembering'] = true;

// remember_me_name: specifies the name of the cookie used to stay logged
$conf['remember_me_name'] = 'pwg_remember';

// remember_me_length : time of validity for "remember me" cookies, in
// seconds.
$conf['remember_me_length'] = 5184000;

// session_length : time of validity for normal session, in seconds.
$conf['session_length'] = 3600;

// +-----------------------------------------------------------------------+
// |                            debug/performance                          |
// +-----------------------------------------------------------------------+

// show_queries : for debug purpose, show queries and execution times
$conf['show_queries'] = false;

// show_gt : display generation time at the bottom of each page
$conf['show_gt'] = true;

// debug_l10n : display a warning message each time an unset language key is
// accessed
$conf['debug_l10n'] = false;

// activate template debugging - a new window will appear
$conf['debug_template'] = false;

// save copies of sent mails into local data dir
$conf['debug_mail'] = false;

// die_on_sql_error: if an SQL query fails, should everything stop?
$conf['die_on_sql_error'] = true;

// if true, some language strings are replaced during template compilation
// (insted of template output). this results in better performance. however
// any change in the language file will not be propagated until you purge
// the compiled templates from the admin / maintenance menu
$conf['compiled_template_cache_language'] = false;

// This tells Smarty whether to check for recompiling or not. Recompiling
// does not need to happen unless a template is changed. false results in
// better performance.
$conf['template_compile_check'] = true;

// This forces Smarty to (re)compile templates on every invocation. This is
// handy for development and debugging. It should never be used in a
// production environment.
$conf['template_force_compile'] = false;

// activate merging of javascript / css files
$conf['template_combine_files'] = true;

// this permit to show the php errors reporting (see INI 'error_reporting'
// for possible values)
// gives an empty value '' to deactivate
$conf['show_php_errors'] = E_ALL;

// sends your hosting PHP and MySQL versions to piwigo.org as anonymously as
// possible, for statistics purpose. No personnal data are transmitted
$conf['send_hosting_technical_details'] = true;

// +-----------------------------------------------------------------------+
// |                            authentication                             |
// +-----------------------------------------------------------------------+

// apache_authentication : use Apache authentication as reference instead of
// users table ?
$conf['apache_authentication'] = false;

// users_table: which table is the reference for users? Can be a different
// table than Piwigo table
//
// If you decide to use another table than the default one, you need to
// prepare your database by deleting some datas :
//
// delete from piwigo_user_access;
// delete from piwigo_user_cache;
// delete from piwigo_user_feed;
// delete from piwigo_user_group;
// delete from piwigo_user_infos;
// delete from piwigo_sessions;
// delete from piwigo_rate;
// update piwigo_images set average_rate = null;
// delete from piwigo_caddie;
// delete from piwigo_favorites;
//
// All informations contained in these tables and column are related to
// piwigo_users table.
$conf['users_table'] = $prefixeTable.'users';

// If you decide to use external authentication
// change conf below by $conf['external_authentification'] = true;
$conf['external_authentification'] = false;

// Other tables can be changed, if you define associated constants
// Example:
//   define('USER_INFOS_TABLE', 'pwg_main'.'user_infos');

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
// default_user_id : id of user used for default value
$conf['default_user_id'] = $conf['guest_id'];

// Registering process and guest/generic members get language from the browser
// if language isn't available PHPWG_DEFAULT_LANGUAGE is used as previously
$conf['browser_language'] = true;

// webmaster_id : webmaster'id.
$conf['webmaster_id'] = 1;

// allow to use adviser mode
$conf['allow_adviser'] = false;

// does the guest have access ?
// (not a security feature, set your categories "private" too)
// If false it'll be redirected from index.php to identification.php
$conf['guest_access'] = true;

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
// pixels.
$conf['upload_maxwidth'] = 800;

// upload_maxheight_thumbnail: maximum height authorized for the uploaded
// thumbnails
$conf['upload_maxheight_thumbnail'] = 128;

// upload_maxwidth_thumbnail: maximum width authorized for the uploaded
// thumbnails
$conf['upload_maxwidth_thumbnail'] = 128;

// +-----------------------------------------------------------------------+
// |                               history                                 |
// +-----------------------------------------------------------------------+

// nb_logs_page :  how many logs to display on a page
$conf['nb_logs_page'] = 300;

// +-----------------------------------------------------------------------+
// |                                 urls                                  |
// +-----------------------------------------------------------------------+

// question_mark_in_urls : the generated urls contain a ? sign. This can be
// changed to false only if the server translates PATH_INFO variable
// (depends on the server AcceptPathInfo directive configuration)
$conf['question_mark_in_urls'] = true;

// php_extension_in_urls : if true, the urls generated for picture and
// category will not contain the .php extension. This will work only if
// .htaccess defines Options +MultiViews parameter or url rewriting rules
// are active.
$conf['php_extension_in_urls'] = true;

// category_url_style : one of 'id' (default) or 'id-name'. 'id-name'
// means that an simplified ascii represntation of the category name will
// appear in the url
$conf['category_url_style'] = 'id';

// picture_url_style : one of 'id' (default), 'id-file' or 'file'. 'id-file'
// or 'file' mean that the file name (without extension will appear in the
// url). Note that one aditionnal sql query will occur if 'file' is choosen.
// Note that you might experience navigation issues if you choose 'file'
// and your file names are not unique
$conf['picture_url_style'] = 'id';

// tag_url_style : one of 'id-tag' (default), 'id' or 'tag'.
// Note that if you choose 'tag' and the url (ascii) representation of your
// tags is not unique, all tags with the same url representation will be shown
$conf['tag_url_style'] = 'id-tag';

// +-----------------------------------------------------------------------+
// |                                 tags                                  |
// +-----------------------------------------------------------------------+

// full_tag_cloud_items_number: number of tags to show in the full tag
// cloud. Only the most represented tags will be shown
$conf['full_tag_cloud_items_number'] = 200;

// menubar_tag_cloud_items_number: number of tags to show in the tag
// cloud in the menubar. Only the most represented tags will be shown
$conf['menubar_tag_cloud_items_number'] = 100;

// content_tag_cloud_items_number: number of related tags to show in the tag
// cloud on the content page, when the current section is not a set of
// tags. Only the most represented tags will be shown
$conf['content_tag_cloud_items_number'] = 12;

// tags_levels: number of levels to use for display. Each level is bind to a
// CSS class tagLevelX.
$conf['tags_levels'] = 5;

// tags_default_display_mode: group tags by letter or display a tag cloud by
// default? 'letters' or 'cloud'.
$conf['tags_default_display_mode'] = 'cloud';

// tag_letters_column_number: how many columns to display tags by letter
$conf['tag_letters_column_number'] = 4;

// +-----------------------------------------------------------------------+
// | Notification by mail                                                  |
// +-----------------------------------------------------------------------+

// Default Value for nbm user
$conf['nbm_default_value_user_enabled'] = false;

// Search list user to send quickly (List all without to check news)
// More quickly but less fun to use
$conf['nbm_list_all_enabled_users_to_send'] = false;

// Max time used on one pass in order to send mails.
// Timeout delay ratio.
$conf['nbm_max_treatment_timeout_percent'] = 0.8;

// If timeout cannot be compite with nbm_max_treatment_timeout_percent,
// nbm_treatment_timeout_default is used by default
$conf['nbm_treatment_timeout_default'] = 20;

// Parameters used in get_recent_post_dates for the 2 kind of notification
$conf['recent_post_dates'] = array(
  'RSS' => array('max_dates' => 5, 'max_elements' => 6, 'max_cats' => 6),
  'NBM' => array('max_dates' => 7, 'max_elements' => 3, 'max_cats' => 9)
  );

// the author shown in the RSS feed <author> element
$conf['rss_feed_author'] = 'Piwigo notifier';

// +-----------------------------------------------------------------------+
// | Set admin layout                                                      |
// +-----------------------------------------------------------------------+

$conf['admin_theme'] = 'roma';

// should we load the active plugins ? true=Yes, false=No
$conf['enable_plugins']=true;

// Web services are allowed (true) or completely forbidden (false)
$conf['allow_web_services'] = true;

// enable log for web services
$conf['ws_enable_log'] = false;

// web services log file path
$conf['ws_log_filepath'] = '/tmp/piwigo_ws.log';

// Maximum number of images to be returned foreach call to the web service
$conf['ws_max_images_per_page'] = 500;

// Display a link to subscribe to Piwigo Announcements Newsletter
$conf['show_newsletter_subscription'] = true;

// +-----------------------------------------------------------------------+
// | Filter                                                                |
// +-----------------------------------------------------------------------+
// $conf['filter_pages'] contains configuration for each pages
//   o If values are not defined for a specific page, default value are used
//   o Array is composed by the basename of each page without extention
//   o List of value names:
//     - used: filter function are used
//       (if false nothing is done [start, cancel, stop, ...]
//     - cancel: cancel current started filter
//     - add_notes: add notes about current started filter on the header
//   o Empty configuration in order to disable completely filter functions
//     No filter, No icon,...
//     $conf['filter_pages'] = array();
$conf['filter_pages'] = array
  (
    // Default page
    'default' => array(
      'used' => true, 'cancel' => false, 'add_notes' => false),
    // Real pages
    'index' => array('add_notes' => true),
    'tags' => array('add_notes' => true),
    'search' => array('add_notes' => true),
    'comments' => array('add_notes' => true),
    'admin' => array('used' => false),
    'feed' => array('used' => false),
    'notification' => array('used' => false),
    'nbm' => array('used' => false),
    'popuphelp' => array('used' => false),
    'profile' => array('used' => false),
    'ws' => array('used' => false),
    'identification' => array('cancel' => true),
    'install' => array('cancel' => true),
    'password' => array('cancel' => true),
    'register' => array('cancel' => true),
  );

// +-----------------------------------------------------------------------+
// | Slideshow                                                             |
// +-----------------------------------------------------------------------+
// slideshow_period : waiting time in seconds before loading a new page
// during automated slideshow
// slideshow_period_min, slideshow_period_max are bounds of slideshow_period
// slideshow_period_step is the step of navigation between min and max
$conf['slideshow_period_min'] = 1;
$conf['slideshow_period_max'] = 10;
$conf['slideshow_period_step'] = 1;
$conf['slideshow_period'] = 4;

// slideshow_repeat : slideshow loops on pictures
$conf['slideshow_repeat'] = true;

// $conf['light_slideshow'] indicates to use slideshow.tpl in state of
// picture.tpl for slideshow
// Take care to have slideshow.tpl in all available templates
// Or set it false.
// Check if Picture's plugins are compliant with it
// Every plugin from 1.7 would be design to manage light_slideshow case.
$conf['light_slideshow'] = true;

// the local data directory is used to store data such as compiled templates
// or other plugin variables etc
$conf['local_data_dir'] = dirname(dirname(__FILE__)).'/_data';

// where should the API/UploadForm add photos? This path must be relative to
// the Piwigo installation directory (but can be outside, as long as it's
// reachable from your webserver).
$conf['upload_dir'] = './upload';

// where should the user be guided when there is no photo in his gallery yet?
$conf['no_photo_yet_url'] = 'admin.php?page=photos_add';

// directory with themes inside
$conf['themes_dir'] = PHPWG_ROOT_PATH.'themes';

// pLoader direct download url for windows
$conf['ploader_download_windows'] = 'http://piwigo.org/ext/download.php?eid=270';

// pLoader direct download url for mac
$conf['ploader_download_mac'] = 'http://piwigo.org/ext/download.php?eid=353';

// pLoader direct download url for linux
$conf['ploader_download_linux'] = 'http://piwigo.org/ext/download.php?eid=269';

// enable the synchronization method for adding photos
$conf['enable_synchronization'] = true;

// PEM url
$conf['alternative_pem_url'] = '';


?>
