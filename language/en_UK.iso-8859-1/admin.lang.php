<?php
// +-----------------------------------------------------------------------+
// |                           en_EN/common.lang.php                           |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : 1.4                                                   |
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

// Main words
$lang['links'] = 'Links';
$lang['general'] = 'General';
$lang['config'] = 'Configuration';
$lang['users'] = 'Users';
$lang['instructions'] = 'Instructions';
$lang['history'] = 'History';
$lang['manage'] = 'Manage';
$lang['waiting'] = 'Waiting';
$lang['groups'] = 'Groups';
$lang['permissions'] = 'Permissions';
$lang['update'] = 'Synchronize';
$lang['storage'] = 'Directory';
$lang['edit'] = 'Edit';
$lang['authorized'] = 'Authorized';
$lang['forbidden'] = 'Forbidden';
$lang['free'] = 'Free';
$lang['restricted'] = 'Restricted';
$lang['metadata']='Metadata';

// Specific words
$lang['phpinfos'] = 'PHP Information';
$lang['remote_site'] = 'Remote site';
$lang['remote_sites'] = 'Remote sites';
$lang['gallery_default'] = 'Gallery Default';
$lang['upload'] = 'Upload';

// Remote sites management
$lang['remote_site_create'] = 'Create a new site : (give its URL to generate_file_listing.php)';
$lang['remote_site_uncorrect_url'] = 'Remote site url must start by http or https and must only contain characters among "/", "a-zA-Z0-9", "-" or "_"';
$lang['remote_site_already_exists'] = 'This site already exists';
$lang['remote_site_generate'] = 'generate listing';
$lang['remote_site_generate_hint'] = 'generate file listing.xml on remote site';
$lang['remote_site_update'] = 'update';
$lang['remote_site_update_hint'] = 'read remote listing.xml and updates database';
$lang['remote_site_clean'] = 'clean';
$lang['remote_site_clean_hint'] = 'remove remote listing.xml file';
$lang['remote_site_delete'] = 'delete';
$lang['remote_site_delete_hint'] = 'delete this site and all its attached elements';
$lang['remote_site_file_not_found'] = 'file create_listing_file.php on remote site was not found';
$lang['remote_site_error'] = 'an error happened';
$lang['remote_site_listing_not_found'] = 'remote listing file was not found';
$lang['remote_site_removed'] = 'was removed on remote site';
$lang['remote_site_removed_title'] = 'Removed elements';
$lang['remote_site_created'] = 'created';
$lang['remote_site_deleted'] = 'deleted';

// Categorie words
$lang['cat_up'] = 'Move up';
$lang['cat_down'] = 'Move down';
$lang['cat_add'] = 'Add a virtual category';
$lang['cat_virtual'] = 'Virtual category';
$lang['cat_public'] = 'Public category';
$lang['cat_private'] = 'Private category';
$lang['cat_image_info'] = 'Images info';
$lang['editcat_status'] = 'Status';
$lang['editcat_confirm'] = 'Category informations updated successfully.';
$lang['editcat_perm'] = 'To set permissions for this category, click';
$lang['cat_upload'] = 'Select uploadable categories';
$lang['cat_upload_info'] = 'Only non virtual categories are shown.';
$lang['cat_lock'] = 'Lock';
$lang['cat_lock_info'] = 'This category will temporary been disabled for maintenance.';
$lang['cat_access_info'] = 'Permission management.';

// Titles
$lang['admin_panel'] = 'Administration Panel';
$lang['default_message'] = 'PhpWebGallery Administration Control Panel';
$lang['title_liste_users'] = 'Users list';
$lang['title_history'] = 'History';
$lang['title_update'] = 'Database update';
$lang['title_configuration'] = 'PhpWebGallery configuration';
$lang['title_instructions'] = 'Instructions';
$lang['title_categories'] = 'Categories management';
$lang['title_edit_cat'] = 'Edit a category';
$lang['title_info_images'] = 'Modify category\'s image information';
$lang['title_thumbnails'] = 'Thumbnail creation';
$lang['title_thumbnails_2'] = 'for';
$lang['title_default'] = 'PhpWebGallery administration';
$lang['title_waiting'] = 'Pictures waiting for validation';

//Error messages
$lang['cat_error_name'] = 'The name of a category mustn\'t be empty';

//Configuration
$lang['conf_confirmation'] = 'Information data registered in database';
$lang['conf_default'] = 'Default display';
$lang['conf_cookie'] = 'Session & Cookie';

// Configuration -> general
$lang['conf_general_title'] = 'Main configuration';
$lang['conf_mail_webmaster'] = 'Webmaster mail adress';
$lang['conf_mail_webmaster_info'] = 'Visitors will be able to contact site administrator with this mail';
$lang['conf_mail_webmaster_error'] = 'e-mail address refused, it must be like name@domain.com';
$lang['conf_prefix'] = 'Thumbnail prefix';
$lang['conf_prefix_info'] = 'Thumbnails use this prefix. Do not fill if your not sure.';
$lang['conf_prefix_error'] = 'Thumbnail\'s prefix must only contain characters among : a to z (case insensitive), "-" or "_"';
$lang['conf_access'] = 'Access type';
$lang['conf_access_info'] = '- free : anyone can enter the site, any visitor can create an account in order to customize the appareance of the website<br />- restricted : the webmaster create accounts. Only registered users can enter the site';
$lang['conf_log_info'] = 'Keep an history of visits on your website ? Visits will be shown in the history section of the administration panel';
$lang['conf_notification'] = 'Mail notification';
$lang['conf_notification_info'] = 'Automated mail notification for adminsitrators (and only for them) when a user add a comment or upload a picture.';

// Configuration -> comments
$lang['conf_comments_title'] = 'Users comments';
$lang['conf_show_comments'] = 'Show users comments';
$lang['conf_show_comments_info'] = 'Display the users comments under each picture ?';
$lang['conf_comments_forall'] = 'Comments for all ?';
$lang['conf_comments_forall_info'] = 'Even guest not registered can post comments';
$lang['conf_nb_comment_page'] = 'Number of comments per page';
$lang['conf_nb_comment_page_info'] = 'number of comments to display on each page. This number is unlimited for a picture. Enter a number between 5 and 50.';
$lang['conf_nb_comment_page_error'] = 'The number of comments a page must be between 5 and 50 included.';
$lang['conf_comments_validation'] = 'Validation';
$lang['conf_comments_validation_info'] = 'An administrator validate users posted comments before the becom visible on the site';

// Configuration -> default
$lang['conf_default_title'] = 'Default display';
$lang['conf_default_language_info'] = 'Default language';
$lang['conf_default_theme_info'] = 'Default theme';
$lang['conf_nb_image_line_info'] = 'Number of pictures for each row by default';
$lang['conf_nb_line_page_info'] = 'Number of rows by page by default';
$lang['conf_recent_period_info'] = 'By days. Period within a picture is shown as new. Must be superior to 1 day.';
$lang['conf_default_expand_info'] = 'Expand all categories by default in the menu ?';
$lang['conf_show_nb_comments_info'] = 'show the number of comments for each picture on the thumbnails page';

// Configuration -> upload
$lang['conf_upload_title'] = 'Users upload';
$lang['conf_authorize_upload'] = 'Authorize upload of pictures';
$lang['conf_authorize_upload_info'] = '';
$lang['conf_upload_maxfilesize'] = 'Maximum filesize';
$lang['conf_upload_maxfilesize_info'] = 'Maximum filesize for the uploaded pictures. Must be a number between 10 and 1000 KB.';
$lang['conf_upload_maxfilesize_error'] = 'Maximum filesize for the uploaded pictures must be a number between 10 and 1000 KB.';
$lang['conf_upload_maxwidth'] = 'Maximum width';
$lang['conf_upload_maxwidth_info'] = 'Maximum width authorized for the uploaded images. Must be a number superior to 10 pixels';
$lang['conf_upload_maxwidth_error'] = 'maximum width authorized for the uploaded images must be a number superior to 10 pixels.';
$lang['conf_upload_maxheight'] = 'Maximum height';
$lang['conf_upload_maxheight_info'] = 'Maximum height authorized for the uploaded images. Must be a number superior to 10 pixels';
$lang['conf_upload_maxheight_error'] = 'maximum height authorized for the uploaded images must be a number superior to 10 pixels.';
$lang['conf_upload_tn_maxwidth'] = 'thumbnails maximum width';
$lang['conf_upload_tn_maxwidth_info'] = 'Maximum width authorized for the uploaded thumbnails. Must be a number superior to 10 pixels';
$lang['conf_upload_maxwidth_thumbnail_error'] = 'Maximum width authorized for the uploaded thumbnails must be a number superior to 10 pixels.';
$lang['conf_upload_tn_maxheight'] = 'Thumbnails maximum height';
$lang['conf_upload_tn_maxheight_info'] = 'Maximum height authorized for the uploaded thumbnails. Must be a number superior to 10 pixels';
$lang['conf_upload_maxheight_thumbnail_error'] = 'Maximum height authorized for the uploaded thumbnails must be a number superior to 10 pixels.';

// Configuration -> session
$lang['conf_session_title'] = 'Sessions';
$lang['conf_cookies'] = 'Authorize cookies';
$lang['conf_cookies_info'] = 'Users won\'t have to log on each visit any more. Less secure.';
$lang['conf_session_size'] = 'Identifier size';
$lang['conf_session_size_info'] = '- the longer your identifier is, the more secure your site is<br />- enter a number between 4 and 50';
$lang['conf_session_size_error'] = 'the session identifier size must be an integer value between 4 and 50';
$lang['conf_session_time'] = 'validity period';
$lang['conf_session_time_info'] = '- the shorter the validity period is, the more secure your site is<br />- enter a number between 5 and 60, in minutes';
$lang['conf_session_time_error'] = 'the session time must be an integer value between 5 and 60';

// Configuration -> metadata
$lang['conf_metadata_title'] = 'Metadata';
$lang['conf_use_exif'] = 'Use EXIF';
$lang['conf_use_exif_info'] = 'Use EXIF data during metadata synchronization into PhpWebGallery database';
$lang['conf_use_iptc'] = 'Use IPTC';
$lang['conf_use_iptc_info'] = 'Use IPTC data during metadata synchronization into PhpWebGallery database';
$lang['conf_show_exif'] = 'Show EXIF';
$lang['conf_show_exif_info'] = 'Give the possibility to show EXIF metadata on visualisation page. See include/config.inc.php for available EXIF fields';
$lang['conf_show_iptc'] = 'Show IPTC';
$lang['conf_show_iptc_info'] = 'Give the possibility to show IPTC metadata on visualisation page. See include/config.inc.php for available IPTC fields';

// Configuration -> remote
$lang['conf_remote_site_delete_info'] = 'Deleting a remote server will delete all the image and the categories in relation with this server.';

//FAQ
$lang['help_images_title'] = 'Adding pictures';
$lang['help_images_intro'] = 'How to place pictures in your directories';
$lang['help_images'][0] = 'in the directory "galleries", create directories that will represent your categories';
$lang['help_images'][1] = 'in each directory, you can create as many sub-level directories as you wish.';
$lang['help_images'][2] = 'you can create as many categories and sub-categories for each category as you wish';
$lang['help_images'][3] = 'picture files must have jpeg format (extension jpg or JPG), gif format (extension gif or GIF) or png format (extension png or PNG).';
$lang['help_images'][4] = 'try not to use blank space " " or hyphen "-" in picture files, I advise you to use underscore "_" character which is managed by PhpWebGallery and will provide better results';
$lang['help_thumbnails_title'] = 'Thumbnails';
$lang['help_thumbnails'][0] = 'in each directory containing picture to display on your site, there is a sub-directory nammed "thumbnail", if it doesn\'t exist, create it to place your thumbnails into it.';
$lang['help_thumbnails'][1] = 'thumbnails don\'t need to have the same extension as their associated picture (a picture with .jpg extension can have a thumbnail in .GIF extention for instance).';
$lang['help_thumbnails'][2] = 'the thumbnail associated to a picture must be prefixed with the prefix given on the configuration page(image.jpg -> TN_image.GIF for instance).';
$lang['help_thumbnails'][3] = 'I advise you to use the module for windows downloadable on the presentation site of PhpWebGallery for thumbnails management.';
$lang['help_thumbnails'][4] = 'you can use the thumbnail creation page integrated in PhpWebGallery, but I don\'t advice you so, because thumbnail quality may be poor and it uses a high CPU load which can be a problem if you use free web hosting.';
$lang['help_thumbnails'][5] = 'if you choose to use your hosting provider to create thumbnails, you must give 775 rights on "galleries" folder and all its sub-folders.';
$lang['help_database_title'] = 'Updating database';
$lang['help_database'][0] = 'once pictures files and thumbnails correctly placed in the directories, clic on "database update" in the menu of the administration panel.';
$lang['help_infos_title'] = 'Miscellanous informations';
$lang['help_infos'][1] = 'As soon as you created your gallery, go in the user list and modify permissions for user "visiteur". Indeed, every new registered users will have by default the same permissions as "visiteur" user.';
$lang['help_remote_title'] = 'Remote site';
$lang['help_remote'][0] = 'PhpWebGallery offers the possibility to use several servers to store the images which will compose your gallery. It can be useful if your gallery is installed on one limited space and that you have a big quantity of images to be shown. Please , follow this procedure : ';
$lang['help_remote'][1] = '1. edit file "create_listing_file.php" (you will find it in the directory "admin"), by modifying the line "$prefix_thumbnail = "TN-";" if the prefix for your thumbnails is not "TN-".';
$lang['help_remote'][2] = '2. place file "create_listing_file.php" modified on your distant website, in the root directory of your directories of images  (as the directory "galleries" of this website) by ftp.';
$lang['help_remote'][3] = '3. launch script using the url http://domaineDistant/repGalerie/create_listing_file.php, a file listing.xml has just been created.';
$lang['help_remote'][4] = '4. get back file listing.xml from your distant website to place it in directory "admin" of this website.';
$lang['help_remote'][5] = '5. please , launch an update of the data of images by the interface of administration, once the listing.xml used file, kill it from the directory "admin".';
$lang['help_remote'][6] = 'You can update the contents of a distant website by redoing the described manipulation. You can also kill a distant website by choosing the option in the configuration section of the administration panel.';
$lang['help_upload_title'] = 'Added images by users';
$lang['help_upload'][0] = 'PhpWebGallery offers the possibility for users to upload images. in order to do it :';
$lang['help_upload'][1] = '1. authorize the option in the configuration zone of the administration panel';
$lang['help_upload'][2] = '2. authorize the rights in writing in the images directories';
$lang['help_database'][1] = 'In order to avoid the update of too many pictures in a single update, I advise to start by updating only categories, then on the categories section of the administration panel, update each category thanks to the link "update"';
$lang['help_upload'][3] = 'The category must have upload available itself for upload.';
$lang['help_upload'][4] = 'Uploaded images by the users are not directly visible on the website, they must be validated by an administrator.  For that purpose, an administrator must go on the page "en attente" of the administration panel, to validate or to refuse the images proposed, then launch an update of the images data.';
$lang['help_virtual_title'] = 'Links between pictures and categories and virtual categories';
$lang['help_virtual'][0] = 'PhpWebGallery is able to dissociate categories where pictures are stored and categories where pictures are shown.';
$lang['help_virtual'][1] = 'By default, pictures are shown only in their real categories : the ones corresponding to directories on the web server.';
$lang['help_virtual'][2] = 'To link a picture to a category, you just have to make the association on the page of picture informations or on the informations of all pictures of a category.';
$lang['help_virtual'][3] = 'Using this principle, it is possible to create virtual categories in PhpWebGallery : no real directory coresponds to this category. You just have to create this category on the section "categories" of the admin panel.';
$lang['help_groups_title'] = 'Users Groups';
$lang['help_groups'][0] = 'PhpWebGallery is able to manage groups of users. It can be very useful to have common permission access for private categories.';
$lang['help_groups'][1] = '1. Create the group "family" on the section "Groups" of admin panel.';
$lang['help_groups'][2] = '2. On the section "Users", edit on of them and associate him to the group "family".';
$lang['help_groups'][3] = '3. By modifying the permissions for a category or for a group, you\'ll see that all categories accessible for a group are accessible for its members.';
$lang['help_groups'][4] = 'A user can belong to several groups. The authorization is stronger than prohibition : if a user "jack" belongs to the group "family" and "friends", and that only group "family" can see category "Christmas 2003", "jack" will be able to see "Christmas 2003".';
$lang['help_access_title'] = 'Access authorization';
$lang['help_access'][0] = 'PhpWebGallery is able to forbid access to categories. Categories can be "public" or "private". In order to forbid access to a category :';
$lang['help_access'][1] = '1. Modify category informations (from the "categories" section in tha admin panel) and make it "private".';
$lang['help_access'][2] = '2. On the page of permissions (for a group or a user) the private category will be shown and you\'ll be able to authorize access or not.';
$lang['help_infos'][2] = 'If you have any question, do not hesitate to take a look at the forum or ask a question there. The <a href="http://forum.phpwebgallery.net" style="text-decoration:underline">forum</a> (message board) is available on the presentation site of PhpWebGallery.';

// Image informations
$lang['infoimage_general'] = 'General options for the category';
$lang['infoimage_useforall'] = 'use for all pictures ?';
$lang['infoimage_creation_date'] = 'Creation date';
$lang['infoimage_detailed'] = 'Option for each picture';
$lang['infoimage_title'] = 'Title';
$lang['infoimage_keyword_separation'] = '(separate with coma ",")';
$lang['infoimage_addtoall'] = 'add to all';
$lang['infoimage_removefromall'] = 'remove from all';
$lang['infoimage_associate'] = 'Associate to the category';

// Update
$lang['update_missing_tn'] = 'the thumbnail is missing for';
$lang['update_disappeared_tn'] = 'the thumbnail disapeared';
$lang['update_disappeared'] = 'doesn\'t exist';
$lang['update_part_deletion'] = 'Deletion of images that have no thumbnail or that doesn\'t exist';
$lang['update_part_research'] = 'Search for new images in the directories';
$lang['update_research_added'] = 'added';
$lang['update_research_tn_ext'] = 'thumbnail in';
$lang['update_nb_new_elements'] = 'elements added in the database';
$lang['update_nb_del_elements'] = 'elements deleted in the database';
$lang['update_nb_new_categories'] = 'categories added in the database';
$lang['update_nb_del_categories'] = 'categories deleted in the database';
$lang['update_default_title'] = 'Choose an option';
$lang['update_only_cat'] = 'update categories, not pictures';
$lang['update_all'] = 'update all';
$lang['update_sync_metadata_question'] = 'Do you want to synchronize new elements informations with files metadata ?';

$lang['menu_add_user'] = 'add';
$lang['menu_list_user'] = 'list';
$lang['user_err_modify'] = 'This user can\'t be modified or deleted';
$lang['user_err_unknown'] = 'This user doesn\'t exist in the database';
$lang['adduser_info_message'] = 'Informations registered in the database for user ';
$lang['adduser_info_password_updated'] = '(password updated)';
$lang['adduser_info_back'] = 'back to the users list';
$lang['adduser_fill_form'] = 'Please fill the following form';
$lang['adduser_unmodify'] = 'unmodifiable';
$lang['adduser_status'] = 'status';
$lang['adduser_status_admin'] = 'admin';
$lang['adduser_status_guest'] = 'guest';
$lang['permuser_info_message'] = 'Permissions registered';
$lang['permuser_title'] = 'Restrictions for user';
$lang['permuser_warning'] = 'Warning : a "<span style="font-weight:bold;">forbidden access</span>" to the root of a category prevent from accessing the whole category';

$lang['permuser_parent_forbidden'] = 'parent category forbidden';
$lang['listuser_confirm'] = 'Do you really want to delete this user';
$lang['listuser_info_deletion'] = 'was removed from database';
$lang['listuser_user_group'] = 'Users group';
$lang['listuser_modify'] = 'modify';
$lang['listuser_modify_hint'] = 'modify informations of';
$lang['listuser_permission'] = 'Permissions';
$lang['listuser_permission_hint'] = 'modify permissions of';
$lang['listuser_delete'] = 'delete';
$lang['listuser_delete_hint'] = 'delete user';
$lang['listuser_button_all'] = 'all';
$lang['listuser_button_invert'] = 'invert';
$lang['listuser_button_create_address'] = 'create mail address';

$lang['tn_width'] = 'width';
$lang['tn_height'] = 'height';
$lang['tn_no_support'] = 'Picture unreachable or no support';
$lang['tn_format'] = 'for the file format';
$lang['tn_thisformat'] = 'for this file format';
$lang['tn_err_width'] = 'width must be a number superior to';
$lang['tn_err_height'] = 'height must be a number superior to';
$lang['tn_results_title'] = 'Results of miniaturization';
$lang['tn_picture'] = 'picture';
$lang['tn_results_gen_time'] = 'generated in';
$lang['tn_stats'] = 'General statistics';
$lang['tn_stats_nb'] = 'number of miniaturized pictures';
$lang['tn_stats_total'] = 'total time';
$lang['tn_stats_max'] = 'max time';
$lang['tn_stats_min'] = 'min time';
$lang['tn_stats_mean'] = 'average time';
$lang['tn_err'] = 'You made mistakes';
$lang['tn_params_title'] = 'Miniaturization parameters';
$lang['tn_params_GD'] = 'GD version';
$lang['tn_params_GD_info'] = '- GD is the picture manipulating library for PHP<br />-choose the version installed on your server. If you choose the wrong, you\'ll just have errors messages, come back with your browser and choose the other version. If no version works, it means your server doesn\'t support GD.';
$lang['tn_params_width_info'] = 'maximum width that thumbnails can take';
$lang['tn_params_height_info'] = 'maximum height that thumbnails can take';
$lang['tn_params_create'] = 'create';
$lang['tn_params_create_info'] = 'Do not try to miniaturize too many pictures in the same time.<br />Indeed, miniaturization uses a lot of CPU. If you installed PhpWebGallery on a free provider, a too high CPU load can sometime lead to the deletion of your website.';
$lang['tn_params_format'] = 'file format';
$lang['tn_params_format_info'] = 'only jpeg file format is supported for thumbnail creation';
$lang['tn_alone_title'] = 'pictures without thumbnail (jpeg and png only)';
$lang['tn_dirs_title'] = 'Directories list';
$lang['tn_dirs_alone'] = 'pictures without thumbnail';


$lang['title_add'] = 'Add a user';
$lang['title_modify'] = 'Modify a user';
$lang['title_groups'] = 'Groups management';
$lang['title_user_perm'] = 'Modify permission for user';
$lang['title_cat_perm'] = 'Modify permissions for category';
$lang['title_group_perm'] = 'Modify permissions for group';
$lang['title_picmod'] = 'Modify informations about a picture';
$lang['adduser_associate'] = 'Associate to group';
$lang['group_add'] = 'Add a group';
$lang['group_add_error1'] = 'The name of a group must not contain " or \'';
$lang['group_add_error2'] = 'This name is already used by another group';
$lang['group_confirm'] = 'Are you sure you want to remove this group ?';
$lang['group_list_title'] = 'List of existing groups';
$lang['group_err_unknown'] = 'This group doesn\'t exist in the database';
$lang['stats_pages_seen'] = 'pages seen';
$lang['stats_visitors'] = 'guests';
$lang['stats_empty'] = 'empty history';
$lang['stats_pages_seen_graph_title'] = 'Number of pages seen by day';
$lang['stats_visitors_graph_title'] = 'Number of guests by day';
$lang['comments_last_title'] = 'Last comments';
$lang['comments_non_validated_title'] = 'Comments waiting for validation';

$lang['step1_err_copy'] = 'Copy the text between hyphens and paste it into the file "include/mysql.inc.php"(Warning : mysql.inc.php must only contain what is in blue, no line return or space character)';
$lang['permuser_only_private'] = 'Only private categories are shown';
$lang['waiting_update'] = 'Validated pictures will be displayed only once pictures database updated';
$lang['cat_unknown_id'] = 'This category is unknown in the database';
$lang['install_warning'] = 'The file "install.php" is still present. Please remove it from your server. It is not secure to keep it.';

 ?>