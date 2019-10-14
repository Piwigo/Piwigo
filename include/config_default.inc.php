<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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

// order_by_custom and order_by_inside_category_custom : for non common pattern
// you can define special ORDER configuration
//
// $conf['order_by_custom'] = ' ORDER BY date_available DESC, file ASC, id ASC';

// order_by_inside_category : inside a category, images can also be ordered
// by rank. A manually defined rank on each image for the category.
//
// $conf['order_by_inside_category_custom'] = $conf['order_by_custom'];

// picture_ext : file extensions for picture file, must be a subset of
// file_ext
$conf['picture_ext'] = array('jpg','jpeg','png','gif');

// file_ext : file extensions (case sensitive) authorized
$conf['file_ext'] = array_merge(
  $conf['picture_ext'],
  array('tiff', 'tif', 'mpg','zip','avi','mp3','ogg','pdf')
  );

// enable_formats: should Piwigo search for multiple formats?
$conf['enable_formats'] = false;

// format_ext : file extensions for formats, ie additional versions of a
// photo (or nay other file). Formats are in sub-directory pwg_format.
$conf['format_ext'] = array('cr2', 'tif', 'tiff', 'nef', 'dng', 'ai', 'psd');

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

// calendar_show_any : the calendar shows an additional 'any' button in the
// year/month/week/day navigation bars
$conf['calendar_show_any'] = true;

// calendar_show_empty : the calendar shows month/weeks/days even if there are
//no elements for these
$conf['calendar_show_empty'] = true;

// newcat_default_commentable : at creation, must a category be commentable
// or not ?
$conf['newcat_default_commentable'] = true;

// newcat_default_visible : at creation, must a category be visible or not ?
// Warning : if the parent category is invisible, the category is
// automatically create invisible. (invisible = locked)
$conf['newcat_default_visible'] = true;

// newcat_default_status : at creation, must a category be public or private
// ? Warning : if the parent category is private, the category is
// automatically create private.
$conf['newcat_default_status'] = 'public';

// newcat_default_position : at creation, should the album appear at the first or last position ?
$conf['newcat_default_position'] = 'first';

// level_separator : character string used for separating a category level
// to the sub level. Suggestions : ' / ', ' &raquo; ', ' &rarr; ', ' - ',
// ' &gt;'
$conf['level_separator'] = ' / ';

// paginate_pages_around : on paginate navigation bar, how many pages
// display before and after the current page ?
$conf['paginate_pages_around'] = 2;

// show_version : shall the version of Piwigo be displayed at the
// bottom of each page ?
$conf['show_version'] = false;

// meta_ref to reference multiple sets of incorporated pages or elements
// Set it false to avoid referencing in Google, and other search engines.
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
// Advanced use:
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

// List of notes to display on all header page
// example $conf['header_notes']  = array('Test', 'Hello');
$conf['header_notes']  = array();

// show_thumbnail_caption : on thumbnails page, show thumbnail captions ?
$conf['show_thumbnail_caption'] = true;

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

// representative_cache_on_level: if a thumbnail is chosen as representative
// but has higher privacy level than current user, Piwigo randomly selects
// another thumbnail. Should be store this thumbnail in cache to avoid
// another consuming SQL query on next page refresh?
$conf['representative_cache_on_level'] = true;

// representative_cache_on_subcats: if a category (= album) only contains
// sub-categories, Piwigo randomly selects a thumbnail among sub-categories
// representative. Should we store this thumbnail in cache to avoid another
// "slightly" consuming SQL query on next page refresh?
$conf['representative_cache_on_subcats'] = true;

// allow_html_descriptions : authorize administrators to use HTML in
// category and element description.
$conf['allow_html_descriptions'] = true;

// image level permissions available in the admin interface
$conf['available_permission_levels'] = array(0,1,2,4,8);

// check_upgrade_feed: check if there are database upgrade required. Set to
// true, a message will strongly encourage you to upgrade your database if
// needed.
//
// This configuration parameter is set to true in BSF branch and to false
// elsewhere.
$conf['check_upgrade_feed'] = false;

// rate_items: available rates for a picture
$conf['rate_items'] = array(0,1,2,3,4,5);

// Define default method to use ('http' or 'html' in order to do redirect)
$conf['default_redirect_method'] = 'http';

// Define using double password type in admin's users management panel
$conf['double_password_type_in_admin'] = false;

// Define if logins must be case sensitive or not of user's registration. ie :
// If set true, the login "user" will equal "User" or "USER" or "user",
// etc. ... And it will be impossible to use such login variation to create a
// new user account.
$conf['insensitive_case_logon'] = false;

// how should we check for unicity when adding a photo. Can be 'md5sum' or
// 'filename'
$conf['uniqueness_mode'] = 'md5sum';

// Library used for image resizing. Value could be 'auto', 'imagick',
// 'ext_imagick' or 'gd'. If value is 'auto', library will be chosen in this
// order. If chosen library is not available, another one will be picked up.
$conf['graphics_library'] = 'auto';

// If library used is external installation of ImageMagick ('ext_imagick'),
// you can define imagemagick directory.
$conf['ext_imagick_dir'] = '';

// how many user comments to display by default on comments.php. Use 'all'
// to display all user comments without pagination. Default available values
// are array(5,10,20,50,'all') but you can set any other numeric value.
$conf['comments_page_nb_comments'] = 10;

// how often should we check for new versions of Piwigo on piwigo.org? In
// seconds. The check is made only if there are visits on Piwigo.
// 0 to disable.
$conf['update_notify_check_period'] = 24*60*60;

// how often should be remind of new versions available? For example a first
// notification was sent on May 5th 2017 for 2.9.1, after how many seconds
// we send it again? 0 to disable.
$conf['update_notify_reminder_period'] = 7*24*60*60;

// should the album description be displayed on all pages (value=true) or
// only the first page (value=false)
$conf['album_description_on_all_pages'] = false;

// +-----------------------------------------------------------------------+
// |                                 email                                 |
// +-----------------------------------------------------------------------+

// send_bcc_mail_webmaster: send bcc mail to webmaster. Set true for debug
// or test.
$conf['send_bcc_mail_webmaster'] = false;

// define the name of sender mail: if value is empty, gallery title is used
$conf['mail_sender_name'] = '';

// define the email of sender mail: if value is empty, webmaster email is used
$conf['mail_sender_email'] = '';

// set true to allow text/html emails
$conf['mail_allow_html'] = true;

// smtp configuration (work if fsockopen function is allowed for smtp port)
// smtp_host: smtp server host
//  if null, regular mail function is used
//   format: hoststring[:port]
//   exemple: smtp.pwg.net:21
// smtp_user/smtp_password: user & password for smtp authentication
$conf['smtp_host'] = '';
$conf['smtp_user'] = '';
$conf['smtp_password'] = '';

// 'ssl' or 'tls'
$conf['smtp_secure'] = null;

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
// available)
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

// allow_html_in_metadata: in case the origin of the photo is unsecure (user
// upload), we remove HTML tags to avoid XSS (malicious execution of
// javascript)
$conf['allow_html_in_metadata'] = false;

// decide which characters can be used as keyword separators (works in EXIF
// and IPTC). Coma "," cannot be removed from this list.
$conf['metadata_keyword_separator_regex'] = '/[.,;]/';

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

// session_use_ip_address: avoid session hijacking by using a part of the IP
// address
$conf['session_use_ip_address'] = true;

// Probability, on each page generated, to launch session garbage
// collector. Integer value between 1 and 100, in %. 0 to disable and let
// the system default behavior (on Debian-like, it's "never delete
// session").
$conf['session_gc_probability'] = 1;

// +-----------------------------------------------------------------------+
// |                            debug/performance                          |
// +-----------------------------------------------------------------------+

// show_queries : for debug purpose, show queries and execution times
$conf['show_queries'] = false;

// show_gt : display generation time at the bottom of each page
$conf['show_gt'] = false;

// debug_l10n : display a warning message each time an unset language key is
// accessed
$conf['debug_l10n'] = false;

// activate template debugging - a new window will appear
$conf['debug_template'] = false;

// save copies of sent mails into local data dir
$conf['debug_mail'] = false;

// die_on_sql_error: if an SQL query fails, should everything stop?
$conf['die_on_sql_error'] = false;

// if true, some language strings are replaced during template compilation
// (instead of template output). this results in better performance. however
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
// update piwigo_images set rating_score = null, added_by = <webmaster_id>;
// delete from piwigo_caddie;
// delete from piwigo_favorites;
//
// All informations contained in these tables and column are related to
// piwigo_users table.
$conf['users_table'] = null;

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

// password_hash: function hash the clear user password to store it in the
// database. The function takes only one parameter: the clear password.
$conf['password_hash'] = 'pwg_password_hash';

// password_verify: function that checks the password against its hash. The
// function takes 2 mandatory parameter : clear password, hashed password +
// an optional parameter user_id. The user_id is used to update the password
// with the new hash introduced in Piwigo 2.5. See function
// pwg_password_verify in include/functions_user.inc.php
$conf['password_verify'] = 'pwg_password_verify';

// guest_id : id of the anonymous user
$conf['guest_id'] = 2;

// default_user_id : id of user used for default value
$conf['default_user_id'] = $conf['guest_id'];

// Registering process and guest/generic members get language from the browser
// if language isn't available PHPWG_DEFAULT_LANGUAGE is used as previously
$conf['browser_language'] = true;

// webmaster_id : webmaster'id.
$conf['webmaster_id'] = 1;

// does the guest have access ?
// (not a security feature, set your categories "private" too)
// If false it'll be redirected from index.php to identification.php
$conf['guest_access'] = true;

// +-----------------------------------------------------------------------+
// |                               history                                 |
// +-----------------------------------------------------------------------+

// nb_logs_page :  how many logs to display on a page
$conf['nb_logs_page'] = 300;

// Every X new line in history, perform an automatic purge. The more often,
// the fewer lines to delete. 0 to disable.
$conf['history_autopurge_every'] = 1021;

// How many lines to keep in history on autopurge? 0 to disable.
$conf['history_autopurge_keep_lines'] = 1000000;

// On history autopurge, how many lines should to deleted at once, maximum?
$conf['history_autopurge_blocksize'] = 50000;

// +-----------------------------------------------------------------------+
// |                                 urls                                  |
// +-----------------------------------------------------------------------+

// gallery_url : you can set a specific URL for the home page of your
// gallery. This is for very specific use and you don't need to change this
// setting when move your gallery to a new directory or a new domain name.
$conf['gallery_url'] = null;

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
// means that an simplified ascii representation of the category name will
// appear in the url
$conf['category_url_style'] = 'id';

// picture_url_style : one of 'id' (default), 'id-file' or 'file'. 'id-file'
// or 'file' mean that the file name (without extension will appear in the
// url). Note that one additional sql query will occur if 'file' is chosen.
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
$conf['menubar_tag_cloud_items_number'] = 20;

// menubar_tag_cloud_content: 'always_all', 'current_only' or 'all_or_current'
// For the tag cloud in the menubar.
// 'always_all': tag cloud always displays all tags available to the user
// 'current_only': tag cloud always displays the tags from the current pictures
// 'all_or_current': when pictures are displayed, tag cloud shows their tags, but 
// when none are displayed, all the tags available to the user are shown.
$conf['menubar_tag_cloud_content'] = 'all_or_current';

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

// If timeout cannot be combined with nbm_max_treatment_timeout_percent,
// nbm_treatment_timeout_default is used by default
$conf['nbm_treatment_timeout_default'] = 20;

// Parameters used in get_recent_post_dates for the 2 kind of notification
$conf['recent_post_dates'] = array(
  'RSS' => array('max_dates' => 5, 'max_elements' => 6, 'max_cats' => 6),
  'NBM' => array('max_dates' => 7, 'max_elements' => 3, 'max_cats' => 9)
  );

// the author shown in the RSS feed <author> element
$conf['rss_feed_author'] = 'Piwigo notifier';

// how long does the authentication key stays valid, in seconds. 3 days by
// default. 0 to disable.
$conf['auth_key_duration'] = 3*24*60*60;

// +-----------------------------------------------------------------------+
// | Set admin layout                                                      |
// +-----------------------------------------------------------------------+

$conf['admin_theme'] = 'clear';

// should we load the active plugins ? true=Yes, false=No
$conf['enable_plugins']=true;

// Web services are allowed (true) or completely forbidden (false)
$conf['allow_web_services'] = true;

// Maximum number of images to be returned foreach call to the web service
$conf['ws_max_images_per_page'] = 500;

// Maximum number of users to be returned foreach call to the web service
$conf['ws_max_users_per_page'] = 1000;

// Display a link to subscribe to Piwigo Announcements Newsletter
$conf['show_newsletter_subscription'] = true;

// +-----------------------------------------------------------------------+
// | Filter                                                                |
// +-----------------------------------------------------------------------+
// $conf['filter_pages'] contains configuration for each pages
//   o If values are not defined for a specific page, default value are used
//   o Array is composed by the basename of each page without extension
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

// the local data directory is used to store data such as compiled templates,
// plugin variables, combined css/javascript or resized images. Beware of
// mandatory trailing slash.
$conf['data_location'] = '_data/';

// where should the API/UploadForm add photos? This path must be relative to
// the Piwigo installation directory (but can be outside, as long as it's
// reachable from your webserver).
$conf['upload_dir'] = './upload';

// where should the user be guided when there is no photo in his gallery yet?
$conf['no_photo_yet_url'] = 'admin.php?page=photos_add';

// directory with themes inside
$conf['themes_dir'] = PHPWG_ROOT_PATH.'themes';

// enable the synchronization method for adding photos
$conf['enable_synchronization'] = true;

// permitted characters for files/directories during synchronization
$conf['sync_chars_regex'] = '/^[a-zA-Z0-9-_.]+$/';

// folders name excluded during synchronization
$conf['sync_exclude_folders'] = array();

// PEM url (default is http://piwigo.org/ext)
$conf['alternative_pem_url'] = '';

// categories ID on PEM
$conf['pem_plugins_category'] = 12;
$conf['pem_themes_category'] = 10;
$conf['pem_languages_category'] = 8;

// based on the EXIF "orientation" tag, should we rotate photos added in the
// upload form or through pwg.images.addSimple web API method?
$conf['upload_form_automatic_rotation'] = true;

// 0-'auto', 1-'derivative' 2-'script'
$conf['derivative_url_style']=0;

$conf['chmod_value']= substr_compare(PHP_SAPI, 'apa', 0, 3)==0 ? 0777 : 0755;

// 'small', 'medium' or 'large'
$conf['derivative_default_size'] = 'medium';

// below which size (in pixels, ie width*height) do we remove metadata
// EXIF/IPTC... from derivative?
$conf['derivatives_strip_metadata_threshold'] = 256000;

//Maximum Ajax requests at once, for thumbnails on-the-fly generation
$conf['max_requests']=3;

// one of '', 'images', 'all'
//TODO: Put this in admin and also manage .htaccess in #sites and upload folders
$conf['original_url_protection'] = '';


// Default behaviour when a new album is created: should the new album inherit the group/user
// permissions from its parent? Note that config is only used for Ftp synchro,
// and if that option is not explicitly transmit when the album is created.
$conf['inheritance_by_default'] = false;

// 'png' or 'jpg': your uploaded TIF photos will have a representative in
// JPEG or PNG file format
$conf['tiff_representative_ext'] = 'png';

// in the upload form, let users upload only picture_exts or all file_exts?
// for some file types, Piwigo will try to generate a pwg_representative
// (TIFF, videos, PDF)
$conf['upload_form_all_types'] = false;

// Size of chunks, in kilobytes. Fast connections will have better
// performances with high values, such as 5000.
$conf['upload_form_chunk_size'] = 500;

// If we try to generate a pwg_representative for a video we use ffmpeg. If
// "ffmpeg" is not visible by the web user, you can define the full path of
// the directory where "ffmpeg" executable is.
$conf['ffmpeg_dir'] = '';

// batch manager: how many images should Piwigo display by default on the
// global mode. Must be among values {20,50,100}
$conf['batch_manager_images_per_page_global'] = 20;

// batch manager: how many images should Piwigo display by default on the
// unit mode. Must be among values {5, 10, 50}
$conf['batch_manager_images_per_page_unit'] = 5;

// how many missing md5sum should Piwigo compute at once.
$conf['checksum_compute_blocksize'] = 50;

// +-----------------------------------------------------------------------+
// |                                 log                                   |
// +-----------------------------------------------------------------------+
// Logs directory, relative to $conf['data_location']
$conf['log_dir'] = '/logs';

// Log level (OFF, CRITICAL, ERROR, WARNING, NOTICE, INFO, DEBUG)
// development = DEBUG, production = ERROR
$conf['log_level'] = 'DEBUG';

// Keep logs file during X days
$conf['log_archive_days'] = 30;

// +-----------------------------------------------------------------------+
// | Proxy Settings                                                        |
// +-----------------------------------------------------------------------+

// If piwigo needs a http-proxy to connect to the internet, set this to true
$conf['use_proxy'] = false;

// Connection string of the proxy
$conf['proxy_server'] = 'proxy.domain.org:port';

// If the http-proxy requires authentication, set username and password here
// e.g. username:password
$conf['proxy_auth'] = '';
?>
