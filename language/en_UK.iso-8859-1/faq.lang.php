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

// Admin FAQ
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

 ?>