<?php
$lang['only_members'] = 'Only members can access this page';
$lang['invalid_pwd'] = 'Invalid password!';
$lang['access_forbiden'] = 'You are not authorized to access this page';
$lang['submit'] = 'Submit';
$lang['login'] = 'login';
$lang['password'] = 'password';
$lang['new'] = 'new';
$lang['delete'] = 'delete';
$lang['category'] = 'category';
$lang['thumbnail'] = 'thumbnail';
$lang['date'] = 'date';

// diapo page
$lang['diapo_default_page_title'] = 'No category selected';
$lang['thumbnails'] = 'Thumbnails';
$lang['categories'] = 'Categories';
$lang['hint_category'] = 'shows images at the root of this categry';
$lang['total_images'] = 'total';
$lang['title_menu'] = 'Menu';
$lang['change_login'] = 'change login';
$lang['login'] = 'login';
$lang['hint_login'] = 'identification enables site\'s appareance customization';
$lang['logout'] = 'logout';
$lang['customize'] = 'customize';
$lang['hint_customize'] = 'customize the appareance of the gallery';
$lang['hint_search'] = 'search';
$lang['search'] = 'search';
$lang['favorite_cat'] = 'favorites';
$lang['favorite_cat_hint'] = 'display your favorites';
$lang['about'] = 'about';
$lang['hint_about'] = 'more informations on PhpWebGallery...';
$lang['admin'] = 'admin';
$lang['hint_admin'] = 'available for administrators only';
$lang['no_category'] = 'No category selected<br />please select it in the menu';
$lang['page_number'] = 'page number';
$lang['previous_page'] = 'Previous';
$lang['next_page'] = 'Next';
$lang['nb_image_category'] = 'number of images in this category';
$lang['connected_user_female'] = 'connected user';
$lang['connected_user_male'] = 'connected user';
$lang['recent_image'] = 'image within the';
$lang['days'] = 'days';
$lang['send_mail'] = 'Any comment? Send me an e-mail';
$lang['title_send_mail'] = 'A comment on your site';
$lang['sub-cat'] = 'subcategories';
$lang['images_available'] = 'images in this category';
$lang['total'] = 'images';
$lang['upload_picture'] = 'Upload a picture';

// both diapo and photo pages
$lang['registration_date'] = 'registered on';
$lang['creation_date'] = 'created on';
$lang['comment'] = 'comment';
$lang['author'] = 'author';
$lang['size'] = 'size';
$lang['filesize'] = 'filesize';
$lang['file'] = 'file';
$lang['generation_time'] = 'Page generated in';
$lang['favorites'] = 'Favorites';
$lang['search_result'] = 'Search results';

// about page
$lang['about_page_title'] = 'About PhpWebGallery';
$lang['about_title'] = 'About...';
$lang['about_message'] = '<div style="text-align:center;font-weigh:bold;">Information about PhpWebGallery</div>
<ul>
  <li>This website uses <a href="'.$conf['site_url'].'" style="text-decoration:underline">PhpWebGallery</a> release '.$conf['version'].'. PhpWebGallery is a web application giving you the possibility to create an online images gallery easily.</li>
  <li>Technicaly, PhpWebGallery is fully developped with PHP (the elePHPant) with a MySQL database (the SQuirreL).</li>
  <li>If you have any suggestions or comments, please visit <a href="'.$conf['site_url'].'" style="text-decoration:underline">PhpWebGallery</a> official site, and its dedicated <a href="'.$conf['forum_url'].'" style="text-decoration:underline">forum</a>.</li>
</ul>';
$lang['about_return'] = 'Back';

// identification page
$lang['ident_page_title'] = 'Identification';
$lang['ident_title'] = 'Identification';
$lang['actual_user'] = 'User currently registered as: ';
$lang['ident_register'] = 'Register';
$lang['ident_forgotten_password'] = 'Forget your password ?';
$lang['ident_guest_visit'] = 'Go through the gallery as a visitor';

// page personnalisation
$lang['customize_page_title'] = 'Customization';
$lang['customize_title'] = 'Customization';
$lang['customize_nb_image_per_row'] = 'number of images per row';
$lang['customize_nb_row_per_page'] = 'number of rows per page';
$lang['customize_color'] = 'site color';
$lang['customize_language'] = 'language';
$lang['sex'] = 'sex';
$lang['male'] = 'male';
$lang['female'] = 'female';
$lang['maxwidth'] = 'maximum width of the pictures';
$lang['maxheight'] = 'maximum height of the pictures';
$lang['err_maxwidth'] = 'maximum width must be a number superior to 50';
$lang['err_maxheight'] = 'maximum height must be a number superior to 50';

// photo page
$lang['previous_image'] = 'Previous';
$lang['next_image'] = 'Next';
$lang['back'] = 'Click on the image to go back to the thumbnails page';
$lang['info_image_title'] = 'Image information';
$lang['link_info_image'] = 'Modify information';
$lang['true_size'] = 'Real size';
$lang['comments_title'] = 'Comments from the users of the site';
$lang['comments_del'] = 'delete this comment';
$lang['comments_add'] = 'Add a comment';
$lang['month'][1] = 'January';
$lang['month'][2] = 'February';
$lang['month'][3] = 'March';
$lang['month'][4] = 'April';
$lang['month'][5] = 'May';
$lang['month'][6] = 'June';
$lang['month'][7] = 'July';
$lang['month'][8] = 'August';
$lang['month'][9] = 'September';
$lang['month'][10] = 'October';
$lang['month'][11] = 'November';
$lang['month'][12] = 'December';
$lang['day'][0] = 'Sunday';
$lang['day'][1] = 'Monday';
$lang['day'][2] = 'Tuesday';
$lang['day'][3] = 'Wednesday';
$lang['day'][4] = 'Thursday';
$lang['day'][5] = 'Friday';
$lang['day'][6] = 'Saturday';
$lang['add_favorites_alt'] = 'Add to favorites';
$lang['add_favorites_hint'] = 'Add this picture to your favorites';
$lang['del_favorites_alt'] = 'Delete from favorites';
$lang['del_favorites_hint'] = 'Delete this picture from your favorites';
	
// page register
$lang['register_page_title'] = 'Registration';
$lang['register_title'] = 'Registration';
$lang['reg_err_login1'] = 'Please, enter a login';
$lang['reg_err_login2'] = 'login mustn\'t end with a space character';
$lang['reg_err_login3'] = 'login mustn\'t start with a space character';
$lang['reg_err_login4'] = 'login mustn\'t contain characters " and \'';
$lang['reg_err_login5'] = 'this login is already used';
$lang['reg_err_pass'] = 'please enter your password again';
$lang['reg_confirm'] = 'confirm';
$lang['reg_mail_address'] = 'mail address';
$lang['reg_err_mail_address'] = 'mail address must be like xxx@yyy.eee (example : jack@altern.org)';
	
// page search
$lang['search_title'] = 'Search';
$lang['invalid_search'] = 'search must be done on 3 caracters or more';
$lang['search_field_search'] = 'Search';
$lang['search_return_main_page'] = 'Return to thumbnails page';
	
// page upload
$lang['upload_forbidden'] = 'You can\'t upload pictures in this category';
$lang['upload_file_exists'] = 'A picture\'s name already used';
$lang['upload_filenotfound'] = 'You must choose a picture fileformat for the image';
$lang['upload_cannot_upload'] = 'can\'t upload the picture on the server';
$lang['upload_title'] = 'Upload a picture';
$lang['upload_advise'] = 'Choose an image to place in the category : ';
$lang['upload_advise_thumbnail'] = 'Optional, but recommended : choose a thumbnail to associate to ';
$lang['upload_advise_filesize'] = 'the filesize of the picture must not exceed : ';
$lang['upload_advise_width'] = 'the width of the picture must not exceed : ';
$lang['upload_advise_height'] = 'the height of the picture must not exceed : ';
$lang['upload_advise_filetype'] = 'the picture must be to the fileformat jpg, gif or png';
$lang['upload_err_username'] = 'the username must be given';
$lang['upload_username'] = 'Username';
$lang['upload_successful'] = 'Picture uploaded with success, an administrator will validate it as soon as possible';

//-------------------------------------------------------------- administration
if ( $isadmin )
{
  // page admin
  $lang['title_add'] = 'Add/Modify a user';
  $lang['title_liste_users'] = 'Users list';
  $lang['title_history'] = 'History';
  $lang['title_update'] = 'Database update';
  $lang['title_configuration'] = 'PhpWebGallery configuration';
  $lang['title_instructions'] = 'Instructions';
  $lang['title_permissions'] = 'Modify an user permission';
  $lang['title_categories'] = 'Categories management';
  $lang['title_edit_cat'] = 'Edit a category';
  $lang['title_info_images'] = 'Modify category\'s image information';
  $lang['title_thumbnails'] = 'Thumbnail creation';
  $lang['title_thumbnails_2'] = 'for';
  $lang['title_default'] = 'PhpWebGallery administration';

  $lang['menu_title'] = 'Administration';
  $lang['menu_config'] = 'Configuration';
  $lang['menu_users'] = 'Users';
  $lang['menu_add_user'] = 'add';
  $lang['menu_list_user'] = 'list';
  $lang['menu_categories'] = 'Categories';
  $lang['menu_update'] = 'Database update';
  $lang['menu_thumbnails'] = 'Thumbnails';
  $lang['menu_history'] = 'History';
  $lang['menu_instructions'] = 'Instructions';
  $lang['menu_back'] = 'Back to galleries';
		
  $lang['title_waiting'] = 'Pictures waiting for validation';
  $lang['menu_waiting'] = 'Waiting';

  $lang['default_message'] = 'PhpWebGallery administration panel';

  // page de configuration  
  $lang['conf_err_prefixe'] = 'thumbnail\'s prefix mustn\'t contain any accentued character';
  $lang['conf_err_mail'] = 'e-mail address refused, it must be like name@server.com';
  $lang['conf_err_periods'] = 'periods must be integer values';
  $lang['conf_err_periods_2'] = 'periods must be superior to 0, the long period must be superior to the short one';
  $lang['conf_err_sid_size'] = 'the session identifier size must be an integer value between 4 and 50';
  $lang['conf_err_sid_time'] = 'the session time must be an integer value between 5 and 60';
  $lang['conf_err_max_user_listbox'] = 'the max user listbox number must be an integer value between 0 and 255';
  $lang['conf_err_message'] = 'The number of mistakes you have done is ';
  $lang['conf_confirmation'] = 'Information data registered in database';
		
  $lang['no'] = 'no';
  $lang['yes'] = 'yes';
		
  $lang['conf_general_title'] = 'Main configuration';
  $lang['conf_general_webmaster'] = 'webmaster login';
  $lang['conf_general_webmaster_info'] = 'It will be shown to the visitors. It is necessary for website administration';
  $lang['conf_general_mail'] = 'webmaster mail adress';
  $lang['conf_general_mail_info'] = 'Visitors will be able to contact by this mail';
  $lang['conf_general_prefix'] = 'thumbnail prefix';
  $lang['conf_general_prefix_info'] = 'Thumbnails use this prefix. Do not fill if your not sure.';
  $lang['conf_general_short_period'] = 'short period';
  $lang['conf_general_short_period_info'] = 'By days. Period within a picture is shown with a red mark. The short period must be superior to 1 day.';
  $lang['conf_general_long_period'] = 'long period';
  $lang['conf_general_long_period_info'] = 'By days. Period within a picture is shown with a green mark. The long period must be superior to the short period.';
  $lang['conf_general_access'] = 'access type';
  $lang['conf_general_access_1'] = 'free';
  $lang['conf_general_access_2'] = 'restricted';
  $lang['conf_general_access_info'] = '- free : anyone can enter the site, any visitor can create an account in order to customize the appareance of the website<br />- restricted : the webmaster create accounts. Only registered users can enter the site';
  $lang['conf_general_max_user_listbox'] = 'max listbox users number';
  $lang['conf_general_max_user_listbox_info'] = '- this is the number maximum of users for which PhpWebGallery display a listbox instead of a simple text box on the identification page<br />- enter a number between 0 and 255, 0 means that you want to display the listbox';
  $lang['conf_general_default_page'] = 'default page';
  $lang['conf_general_default_page_1'] = 'thumbnails';
  $lang['conf_general_default_page_2'] = 'identification';
  $lang['conf_general_default_page_info'] = 'page on which users are redirected when they go to the root of the site';
  $lang['conf_general_expand'] = 'expand all categories';
  $lang['conf_general_expand_1'] = 'no';
  $lang['conf_general_expand_2'] = 'yes';
  $lang['conf_general_expand_info'] = 'expand all categories by default in the menu ?';
  $lang['conf_comments'] = 'users comments';
  $lang['conf_comments_title'] = 'Configuration of '.$lang['conf_comments'];
  $lang['conf_comments_show_comments'] = $lang['conf_comments'];
  $lang['conf_comments_show_comments_info'] = 'display the users comments under each picture ?';
  $lang['conf_comments_comments_number'] = 'number of comments per page';
  $lang['conf_comments_comments_number_info'] = 'number of comments to display on each page. This number is unlimited for a picture. Enter a number between 5 and 50.';
  $lang['conf_err_comment_number'] = 'The number of comments a page must be between 5 and 50 included.';
  $lang['conf_remote_site_title'] = 'Remote server';
  $lang['conf_remote_site_delete_info'] = 'Deleting a remote server will delete all the image and the categories in relation with this server.';
  $lang['conf_upload_title'] = 'Configuration of the users upload';
  $lang['conf_upload_available'] = 'authorized the upload of pictures';
  $lang['conf_upload_available_info'] = 'Authorizing the upload of pictures by users on the categories of the website (not on a remote server).';
  $lang['conf_upload_maxfilesize'] = 'maximum filesize';
  $lang['conf_upload_maxfilesize_info'] = 'Maximum filesize for the uploaded pictures. Must be a number between 10 and 1000 Ko.';
  $lang['conf_err_upload_maxfilesize'] = 'Maximum filesize for the uploaded pictures must be a number between 10 and 1000 Ko.';
  $lang['conf_upload_maxwidth'] = 'maximum width';
  $lang['conf_upload_maxwidth_info'] = 'Maximum width authorized for the uploaded images. Must be a number superior to 10 pixels';
  $lang['conf_err_upload_maxwidth'] = 'maximum width authorized for the uploaded images must be a number superior to 10 pixels.';
  $lang['conf_upload_maxheight'] = 'maximum height';
  $lang['conf_upload_maxheight_info'] = 'Maximum height authorized for the uploaded images. Must be a number superior to 10 pixels';
  $lang['conf_err_upload_maxwidth'] = 'maximum height authorized for the uploaded images must be a number superior to 10 pixels.';
  $lang['conf_upload_maxwidth_thumbnail'] = 'thumbnails maximum width';
  $lang['conf_upload_maxwidth_thumbnail_info'] = 'Maximum width authorized for the uploaded thumbnails. Must be a number superior to 10 pixels';
  $lang['conf_err_upload_maxwidth_thumbnail'] = 'Maximum width authorized for the uploaded thumbnails must be a number superior to 10 pixels.';
  $lang['conf_upload_maxheight_thumbnail'] = 'thumbnails maximum height';
  $lang['conf_upload_maxheight_thumbnail_info'] = 'Maximum height authorized for the uploaded thumbnails. Must be a number superior to 10 pixels';
  $lang['conf_err_upload_maxheight_thumbnail'] = 'Maximum height authorized for the uploaded thumbnails must be a number superior to 10 pixels.';
		
  $lang['conf_default_title'] = 'Default display properties for unregistered visitors and new accounts';
  $lang['conf_default_language'] = 'default language';
  $lang['conf_default_language_info'] = 'default language';
  $lang['conf_default_image_per_row'] = 'number of images per row';
  $lang['conf_default_image_per_row_info'] = 'default number of images per row';
  $lang['conf_default_row_per_page'] = 'number of row per page';
  $lang['conf_default_row_per_page_info'] = 'default number of row per page';
  $lang['conf_default_theme'] = 'theme';
  $lang['conf_default_theme_info'] = 'default theme';
		
  $lang['conf_session_title'] = 'Sessions configuration';
  $lang['conf_session_size'] = 'identifier size';
  $lang['conf_session_size_info'] = '- the longer your identifier is, the more secure your site is<br />- enter a number between 4 and 50';
  $lang['conf_session_time'] = 'validity period';
  $lang['conf_session_time_info'] = '- the shorter the validity period is, the more secure your site is<br />- enter a number between 5 and 60, in minutes';
  $lang['conf_session_key'] = 'keyword';
  $lang['conf_session_key_info'] = '- the session keyword improve the encoding of the session identifier<br />- enter any sentence shorter than 255 caracters';
  $lang['conf_session_delete'] = 'delete out-of-date sessions';
  $lang['conf_session_delete_info'] = 'it is recommanded to empty the database table of session, because out-of-date sessions remains in the database (but it doesn\'t make any security trouble)';
		
  // page user, clés générales
  $lang['user_err_modify'] = 'This user can\'t be modified or deleted';
  $lang['user_err_unknown'] = 'This user doesn\'t exist in the database';
		
  // page d\'ajout/modification d\'utilisateur
  $lang['adduser_err_message'] = 'The number of mistakes you have done is ';
  $lang['adduser_info_message'] = 'Informations registered in the database for user ';
  $lang['adduser_info_password_updated'] = '(password updated)';
  $lang['adduser_info_back'] = 'back to the users list';
		
  $lang['adduser_fill_form'] = 'Please fill the following form';
  $lang['adduser_login'] = 'login';
  $lang['adduser_unmodify'] = 'unmodifiable';
  $lang['adduser_status'] = 'status';
  $lang['adduser_status_admin'] = 'admin';
  $lang['adduser_status_member'] = 'member';
  $lang['adduser_status_guest'] = 'guest';
		
  // page permissions
  $lang['permuser_info_message'] = 'Permissions registered';
  $lang['permuser_title'] = 'Restrictions for user';
  $lang['permuser_warning'] = 'Warning : a "<span style="font-weight:bold;">forbidden access</span>" to the root of a category prevent from accessing the whole category';
  $lang['permuser_authorized'] = 'authorized';
  $lang['permuser_forbidden'] = 'forbidden';
  $lang['permuser_parent_forbidden'] = 'parent category forbidden';
  $lang['permuser_cat_title'] = 'Modify permissions for ';
		
  // page list users
  $lang['listuser_confirm'] = 'Do you really want to delete this user';
  $lang['listuser_yes'] = 'yes';
  $lang['listuser_no'] = 'no';
  $lang['listuser_info_deletion'] = 'was removed from database';
  $lang['listuser_user_group'] = 'Users group';
  $lang['listuser_modify'] = 'modify';
  $lang['listuser_modify_hint'] = 'modify informations of';
  $lang['listuser_permission'] = 'permissions';
  $lang['listuser_permission_hint'] = 'modify permissions of';
  $lang['listuser_delete'] = 'delete';
  $lang['listuser_delete_hint'] = 'delete user';
  $lang['listuser_button_all'] = 'all';
  $lang['listuser_button_invert'] = 'invert';
  $lang['listuser_button_create_address'] = 'create mail address';
		
  // page categories
  $lang['cat_invisible'] = 'invisible';
  $lang['cat_edit'] = 'Edit';
  $lang['cat_up'] = 'Move up';
  $lang['cat_down'] = 'Move down';
  $lang['cat_image_info'] = 'Images info';
  $lang['cat_total'] = 'total';
		
  // page édition d\'une catégorie
  $lang['editcat_confirm'] = 'Information registered in the database';
  $lang['editcat_back'] = 'categories';
  $lang['editcat_title1'] = 'Options for the';
  $lang['editcat_name'] = 'Name';
  $lang['editcat_comment'] = 'Comment';
  $lang['editcat_status'] = 'Status';
  $lang['editcat_status_info'] = '(invisible except for the administrators)';
		
  // page info images
  $lang['infoimage_err_date'] = 'wrong date';
  $lang['infoimage_general'] = 'General options for the category';
  $lang['infoimage_useforall'] = 'use for all pictures ?';
  $lang['infoimage_creation_date'] = 'creation date';
  $lang['infoimage_detailed'] = 'Option for each picture';
  $lang['infoimage_title'] = 'title';
  $lang['infoimage_comment'] = 'comment';
		
  // page database update
  $lang['update_missing_tn'] = 'the thumbnail is missing for';
  $lang['update_disappeared_tn'] = 'the thumbnail disapeared';
  $lang['update_disappeared'] = 'doesn\'t exist';
  $lang['update_part_deletion'] = 'Deletion of images that have no thumbnail or that doesn\'t exist';
  $lang['update_deletion_conclusion'] = 'pictures removed from database';
  $lang['update_part_research'] = 'Search for new images in the directories';
  $lang['update_research_added'] = 'added';
  $lang['update_research_tn_ext'] = 'thumbnail in';
  $lang['update_research_conclusion'] = 'pictures added to the database';
  $lang['update_default_title'] = 'Choose an option';
  $lang['update_only_cat'] = 'update categories, not pictures';
  $lang['update_all'] = 'update all';
		
  // page de génération miniatures
  $lang['tn_width'] = 'width';
  $lang['tn_height'] = 'height';
		
  $lang['tn_no_support'] = 'Picture unreachable or no support';
  $lang['tn_format'] = 'for the file format';
  $lang['tn_thisformat'] = 'for this file format';
  $lang['tn_err_width'] = 'width must be a number superior to';
  $lang['tn_err_height'] = 'height must be a number superior to';
  $lang['tn_err_GD'] = 'you must choose a version of GD library';
		
  $lang['tn_results_title'] = 'Results of miniaturization';
  $lang['tn_picture'] = 'picture';
  $lang['tn_filesize'] = 'filesize';
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
		
  // help page
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
  $lang['help_infos'][0] = 'The webmaster has the possibility to forbid the access to galleries for a identified user. In ordre to do this, go in the user list, then clic on "permissions" for any user.';
  $lang['help_infos'][1] = 'As soon as you created your gallery, go in the user list and modify permissions for user "visiteur". Indeed, every new registered users will have by default the same permissions as "visiteur" user.';
  $lang['help_infos'][2] = 'If you have any question, do not hesitate to take a look at the forum or ask a question there. The forum (message board) is available on the presentation site of PhpWebGallery.';
		
  $lang['help_remote_title'] = 'Remote site';
  $lang['help_remote'][0] = 'PhpWebGallery offers the possibility to use several servers to store the images which will compose your gallery. It can be useful if your gallery is installed on one limited space and that you have a big quantity of images to be shown. Please , follow this procedure : ';
  $lang['help_remote'][1] = '1. edit file "create_listing_file.php" (you will find it in the directory "admin"), by modifying the line "$prefix_thumbnail = "TN-";" if the prefix for your thumbnails is not "TN-".';
  $lang['help_remote'][2] = '2. place file "create_listing_file.php" modified on your distant website, in the root directory of your directories of images  (as the directory "galleries" of this website) by ftp.';
  $lang['help_remote'][3] = '3. launch script using the url http://domaineDistant/repGalerie/create_listing_file.php, a file listing.xml has just been created.';
  $lang['help_remote'][4] = '4. get back file listing.xml from your distant website to place it in directory "admin" of this website.';
  $lang['help_remote'][5] = '5. please , launch an update of the data of images by the interface of administration, once the listing.xml used file, kill it from the directory "admin".';
  $lang['help_remote'][6] = 'You can update the contents of a distant website by redoing the described manipulation. You can also kill a distant website by choosing the option in the configuration section of the administration panel.'.
		
    $lang['help_upload_title'] = 'Added images by users';
  $lang['help_upload'][0] = 'PhpWebGallery offers the possibility for users to upload images. in order to do it :';
  $lang['help_upload'][1] = '1. authorize the option in the configuration zone of the administration panel';
  $lang['help_upload'][2] = '2. authorize the rights in writing in the images directories';
  $lang['help_upload'][3] = 'Uploaded images by the users are not directly visible on the website, they must be validated by an administrator.  For that purpose, an administrator must go on the page "en attente" of the administration panel, to validate or to refuse the images proposed, then launch an update of the images data.';
	
  // installation
  $lang['install_message'] = 'Message';
		
  $lang['step1_confirmation'] = 'Parameters are correct';
  $lang['step1_err_db'] = 'Connection to server succeed, but it was impossible to connect to database';
  $lang['step1_err_server'] = 'Can\'t connect to server';
  $lang['step1_err_copy'] = 'Copy the text between hyphens and paste it into the file "include/mysql.inc.php"(Warning : mysql.inc.php must only contain what is in blue)';
  $lang['step1_err_copy_2'] = 'The next step of the installation is now possible';
  $lang['step1_err_copy_next'] = 'next step';
  $lang['step1_title'] = 'Step 1/2';
  $lang['step1_host'] = 'MySQL host';
  $lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
  $lang['step1_user'] = 'user';
  $lang['step1_user_info'] = 'user login given by your host provider';
  $lang['step1_pass'] = 'Password';
  $lang['step1_pass_info'] = 'user password given by your host provider';
  $lang['step1_database'] = 'Database name';
  $lang['step1_database_info'] = 'also given by your host provider';
  $lang['step1_prefix'] = 'Database table prefix';
  $lang['step1_prefix_info'] = 'database tables names will be prefixed with it (enables you to manage better your tables)';
		
  $lang['step2_err_login1'] = 'enter a login for webmaster';
  $lang['step2_err_login2'] = 'webmaster login can\'t start or end with a space character';
  $lang['step2_err_login3'] = 'webmaster login can\'t contain characters \' or "';
  $lang['step2_err_pass'] = 'please enter your password again';
  $lang['step2_err_mail'] = $lang['conf_err_mail'];
		
  $lang['install_end_title'] = 'Installation finished';
  $lang['install_end_message'] = 'The configuration of PhpWebGallery is finished, here is the next step<br /><br />
For security reason, please delete file "install.php" in the directory "admin"<br />
Once this file deleted , follow this instructions :
<ul>
  <li>go to the identification page : [ <a href="../identification.php">identification</a> ] and use the login/password given for webmaster</li>
  <li>this login will enable you to access to the [ <a href="admin.php">administration panel</a> ] and to the instructions in order to place pictures in your directories</li>
</ul>';
  $lang['step2_title'] = 'Step 2/2';
  $lang['step2_pwd'] = 'webmaster password';
  $lang['step2_pwd_info'] = 'Keep it confidential, it enables you to access administration panel';
  $lang['step2_pwd_conf'] = 'confirm password';
  $lang['step2_pwd_conf_info'] = 'verification';
}
?>
