<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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
$lang['public'] = 'public';
$lang['private'] = 'private';
$lang['metadata']='Metadata';
$lang['visitors'] = 'Visitors';
$lang['locked'] = 'Locked';
$lang['unlocked'] = 'Unlocked';
$lang['lock'] = 'Lock';
$lang['unlock'] = 'Unlock';
$lang['up'] = 'Move up';
$lang['down'] = 'Move down';
$lang['path'] = 'path';

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
$lang['remote_site_listing_not_found'] = 'listing.xml file was not found';
$lang['remote_site_removed'] = 'was removed on remote site';
$lang['remote_site_removed_title'] = 'Removed elements';
$lang['remote_site_created'] = 'created';
$lang['remote_site_deleted'] = 'deleted';
$lang['remote_site_local_found'] = 'A local listing.xml file has been found for ';
$lang['remote_site_local_new'] = '(new site)';
$lang['remote_site_local_update'] = 'read local listing.xml and update';

// Categories
$lang['cat_security'] = 'Public / Private';
$lang['cat_options'] = 'Category options';
$lang['cat_add'] = 'Add a virtual category';
$lang['cat_virtual'] = 'Virtual category';
$lang['cat_public'] = 'Public category';
$lang['cat_private'] = 'Private category';
$lang['cat_image_info'] = 'Images info';
$lang['editcat_status'] = 'Status';
$lang['editcat_confirm'] = 'Category informations updated successfully.';
$lang['editcat_perm'] = 'To set permissions for this category, click';
$lang['editcat_lock_info'] = 'The category and its sub-categories will temporary been disabled for maintenance.';
$lang['editcat_uploadable'] = 'Authorize upload';
$lang['editcat_uploadable_info'] = 'Authorize users to upload files';
$lang['editcat_commentable_info'] = 'Authorize users to comment elements of this category';
$lang['cat_access_info'] = 'Permission management. If you make a category private, all its child categories becomes private. If you make a category public, all its parent categories becomes public';
$lang['cat_virtual_added'] = 'Virtual category added';
$lang['cat_virtual_deleted'] = 'Virtual category deleted';
$lang['cat_upload_title'] = 'Select uploadable categories';
$lang['cat_upload_info'] = 'Only non virtual and non remote categories are shown.';
$lang['cat_lock_title'] = 'Lock categories';
$lang['cat_lock_info'] = 'Selected categories will temporary been disabled for maintenance.
<br />If you lock a category, all its child categories become locked.
<br />If you unlock a category, all its parent categories become unlocked.';
$lang['cat_comments_title'] = 'Authorize users to add comments on selected categories';
$lang['cat_comments_info'] = 'By inheritance, an element is commentable if it belongs at least to one commentable category.';
$lang['cat_status_title'] = 'Manage authorizations for selected categories';
$lang['cat_status_info'] = 'Selected categories are private : you will need to authorize users and/or groups to access to them.
<br />If you make a category private, all its child categories becomes private.
<br />If you make a category public, all its parent categories becomes public';
$lang['cat_representant'] = 'Random representant';

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
$lang['title_cat_options'] = 'Categories options';
$lang['title_groups'] = 'Groups management';

//Error messages
$lang['cat_error_name'] = 'The name of a category should not be empty';

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
$lang['conf_log_info'] = 'Keep an history of visits on your website ? Visits will be shown in the history section of the administration panel';
$lang['conf_notification'] = 'Mail notification';
$lang['conf_notification_info'] = 'Automated mail notification for adminsitrators (and only for them) when a user add a comment or upload a picture.';
$lang['conf_gallery_locked'] = 'Lock gallery';
$lang['conf_gallery_locked_info'] = 'Lock the entire gallery for maintenance. Only administrator users will be able to reach the gallery';

// Configuration -> comments
$lang['conf_comments_title'] = 'Users comments';
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
$lang['conf_authorize_remembering'] = 'Authorize remembering';
$lang['conf_authorize_remembering_info'] = 'Permits user to log for a long time. It creates a cookie on client side, with duration set in include/config.inc.php (1 year per default)';

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
$lang['infoimage_associated'] = 'virtually associated to';
$lang['infoimage_dissociated'] = 'dissociated from';
$lang['storage_category'] = 'storage category';
$lang['represents'] = 'represents';
$lang['doesnt_represent'] = 'doesn\'t represent';
$lang['waiting_update'] = 'Validated pictures will be displayed only once pictures database updated';
$lang['cat_unknown_id'] = 'This category is unknown in the database';

// Thumbnails
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

// Update
$lang['update_missing_tn'] = 'the thumbnail is missing for';
$lang['update_missing_tn_short'] = 'missing thumbnail';
$lang['update_missing_tn_info'] = 'a picture filetype requires a thumbnail. The thumbnail must be present in the sub-directory "thumbnail" of the category directory. The thumbnail filename must start with "'.$conf['prefix_thumbnail'].'" and the extension must be among the following list :';
$lang['update_disappeared_tn'] = 'the thumbnail disapeared';
$lang['update_disappeared'] = 'doesn\'t exist';
$lang['update_part_deletion'] = 'Deletion of images that have no thumbnail or that doesn\'t exist';
$lang['update_part_research'] = 'Search for new images in the directories';
$lang['update_research_added'] = 'added';
$lang['update_research_deleted'] = 'deleted';
$lang['update_research_tn_ext'] = 'thumbnail in';
$lang['update_nb_new_elements'] = 'elements added in the database';
$lang['update_nb_del_elements'] = 'elements deleted in the database';
$lang['update_nb_new_categories'] = 'categories added in the database';
$lang['update_nb_del_categories'] = 'categories deleted in the database';
$lang['update_default_title'] = 'Choose an option';
$lang['update_sync_files'] = 'synchronize files structure with database';
$lang['update_sync_dirs'] = 'only directories';
$lang['update_sync_all'] = 'directories + files';
$lang['update_sync_metadata'] = 'synchronize files metadata with database elements informations';
$lang['update_sync_metadata_new'] = 'only never synchronized elements';
$lang['update_sync_metadata_all'] = 'even already synchronized elements';
$lang['update_cats_subset'] = 'reduce to single existing categories';
$lang['update_nb_errors'] = 'errors during synchronization';
$lang['update_error_list_title'] = 'Error list';
$lang['update_errors_caption'] = 'Errors caption';
$lang['update_display_info'] = 'display maximum informations (added categories and elements, deleted categories and elements)';
$lang['update_simulate'] = 'only perform a simulation (no change in database will be made)';
$lang['update_infos_title'] = 'Detailed informations';
$lang['update_simulation_title'] = '[Simulation]';

// History
$lang['stats_title'] = 'Last year statistics';
$lang['stats_month_title'] = 'Monthly statistics';
$lang['stats_pages_seen'] = 'Pages seen';
$lang['stats_global_graph_title'] = 'Pages seen by month';
$lang['stats_visitors_graph_title'] = 'Nombre de visiteurs par jour';

// Users
$lang['title_user_modify'] = 'Modify a user';
$lang['title_user_perm'] = 'Modify permission for user';
$lang['user_err_modify'] = 'This user can\'t be modified or deleted';
$lang['user_err_unknown'] = 'This user doesn\'t exist in the database';
$lang['user_management'] = 'Special field for administrators';
$lang['user_status'] = 'User status';
$lang['user_status_admin'] = 'Administrator';
$lang['user_status_guest'] = 'User';
$lang['user_delete'] = 'Delete user';
$lang['user_delete_hint'] = 'Click here to delete this user. Warning! This operation cannot be undone!';
$lang['permuser_only_private'] = 'Only private categories are shown';
$lang['permuser_info'] = 'Only private categories are listed. Private/Public category status can be set in screen "Categories &gt; Public / Private"';

// Groups
$lang['group_confirm_delete']= 'Confirm group deletion';
$lang['group_add'] = 'Add a group';
$lang['group_add_error1'] = 'The name of a group must not contain " or \' or be empty.';
$lang['group_add_error2'] = 'This name is already used by another group.';
$lang['group_list_title'] = 'List of existing groups';
$lang['group_edit'] = 'Manage users of the group';
$lang['group_deny_user'] = 'Deny selected';
$lang['group_add_user']= 'Add user';


$lang['title_cat_perm'] = 'Modify permissions for category';
$lang['title_group_perm'] = 'Modify permissions for group';
$lang['title_picmod'] = 'Modify informations about a picture';
?>