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
$lang['help_images_title'] = 'Adding elements';
$lang['help_images'] =
array(
  'Category directories are in the PhpWebGallery directory "galleries". Here
follow the directory tree of a very small gallery (but using many features)
: <br />
<pre>
.
|-- admin
|-- doc
|-- galleries
|   |-- category-1
|   |   |-- category-1.1
|   |   |   |-- category-1.1.1
|   |   |   |   |-- category-1.1.1.1
|   |   |   |   |   |-- pwg_high
|   |   |   |   |   |   +-- wedding.jpg
|   |   |   |   |   |-- thumbnail
|   |   |   |   |   |   +-- TN-wedding.jpg
|   |   |   |   |   +-- wedding.jpg
|   |   |   |   +-- category-1.1.1.2
|   |   |   +-- category-1.1.2
|   |   |-- category-1.2
|   |   |   |-- pookie.jpg
|   |   |   +-- thumbnail
|   |   |       +-- TN-pookie.jpg
|   |   +-- category-1.3
|   +-- category-2
|       |-- piglet.gif
|       |-- pwg_representative
|       |   +-- video.avi
|       |-- thumbnail
|       |   +-- TN-piglet.jpg
|       +-- video.avi
|-- include
|-- install
|-- language
|-- template
+-- tool
</pre>',
  
  'Basically, a category is represented by a directory at any level in
PhpWebGallery directory "galleries". Each category can contain as many
sub-level as you wish. In the example above, category-1.1.1.1 is at level 4
of deepness.',
  
  'Basically, an element is represented by a file. A file can be a
PhpWebGallery element if its extenstion is among $conf[\'file_ext\']
possibilities (see include/config.inc.php file). A file can be a picture if
its extension is among $conf[\'picture_ext\'] (see include/config.inc.php
file).',
  
  'Picture elements must have an associated thumbnail (see section below about
thumbnails)',

  'Picture elements can have a high quality file associated. As for wedding.jpg
in the example above. No prefix on the high quality picture is required.',

  'Non picture elements (video, sounds, file texts, what you want...) are by
default represented by an icon corresponding to the filename
extension. Optionaly, you can associate a thumbnail and a representative
file (see video.avi in the example above).',
  
  'Warning : the name of directories and files must be composed of letters,
figures, "-", "_" or ".". No blank space, no accentuated characters',
  
  'Advise : a category can contain elements and sub-categories in the same
time. Nerverthless, you are strongly advised for each category to choose
between category containing elements OR category containing sub-categories.'
  );

$lang['help_thumbnails_title'] = 'Thumbnails';
$lang['help_thumbnails'] =
array(
  'As said earlier, each element of picture type must be associated with a
thumbnail.',

  'Thumbnails are stored in the sub-directory "thumbnail" of the category
directory. The thumbnail is a picture (same filename extensions possible
than picture files) which filename is prefixed by the configured "Thumbnail
prefix" (see admin panel, Configuration, General).',

  'Thumbnails don\'t need to have the same extension as their associated
picture (a picture with .jpg extension can have a thumbnail in .GIF
extention for instance).',

  'I advise you to use an external module for thumbnails creation (such as
ThumbClic available on the presentation site of PhpWebGallery).',

  'You can also use the thumbnail creation page integrated in PhpWebGallery,
but I don\'t advise you so, because thumbnail quality may be poor and it
uses a high CPU load which can be a problem if you use free web hosting.',

  'If you choose to use your hosting provider to create thumbnails, you must
give write rights on all category directories and sub-directories
"thumbnail" for ugo (user, group, other).'
  
  );

$lang['help_database_title'] = 'Synchronize filesystem and database';
$lang['help_database'] =
array(
  'Once files, thumbnails and representatives are correctly placed in the
directories, go to : administration panel, General, Synchronize',

  'There are 2 synchronizations possible : directories/files and file
metadata. directories/files is about synchronizing your directories tree
with the category tree in the database. metadata is about updating elements
informations such as filesize, dimensions in pixels, EXIF or IPTC
informations.',

  'The first synchronization must be the directories/files one.',

  'Synchronization process may take long (depending on your server load and
quantity of elements to manage) so it is possible to progress by step :
category by category.'
  
  );

$lang['help_access_title'] = 'Access authorization';
$lang['help_access'] =
array(
  'You can forbid access to categories. Categories can be "public" or
"private". Permissions (for groups and users) can be set only if the
category is private.',

  'You can set a category to private by editing a single category
(administration panel, Categories, Manage, edit) or by setting options to
your whole category tree (administration panel, Categories, Public/Private)',

  'Once the category is private, you can manage permissions for groups and
users (administration panel, Permissions).'
  
  );

$lang['help_groups_title'] = 'Users Groups';
$lang['help_groups'] =
array(
  
  'PhpWebGallery is able to manage groups of users. It can be very useful to
have common permission access for private categories.',

  'You can create groups and add users to a group in administration panel,
Identification, Groups.',

  'A user can belong to several groups. The authorization is stronger than
prohibition : if user "jack" belongs to groups "family" and "friends", and
that only group "family" can see category "Christmas 2003", "jack" will be
able to see "Christmas 2003".'
  
  );

$lang['help_remote_title'] = 'Remote site';
$lang['help_remote'] =
array(

  'PhpWebGallery offers the possibility to use several servers to store the
images which will compose your gallery. It can be useful if your gallery is
installed on one limited space and that you have a big quantity of images to
be shown.',

  '1. edit file tools/create_listing_file.php, by modifying parameters section
such as $conf[\'prefix_thumbnail\'] or $conf[\'use_exif\'].',

  '2. place file "tools/create_listing_file.php" modified on your distant
website, in the same directory than your category directories (as the
directory "galleries" of this website) by ftp. For the example, let\'s say
that you can access http://example.com/galleries/create_listing_file.php.',

  '3. go to administration panel, General, Remote sites. Ask to create a new
site, for example http://example.com/galleries',

  '4. a new remote site is registered. You can perform 4 actions :
<ol>

  <li>generate listing : launches a distant request to generate a distant
  file listing</li>

  <li>update : reads the distant listing.xml file and synchronizes with
  database informations</li>

  <li>clean : removes distant listing.xml file</li>

  <li>delete : deletes the site (and all related categories and elements) in
  the database</li>

</ol>',

  'You can do all this by hand by generating yourself the listing.xml file,
moving it from your distant server to you local PhpWebGallery "admin"
directory and opening the remote site management screen : PhpWebGallery will
propose you to use the found listing.xml file.'
  
  );

$lang['help_upload_title'] = 'Files upload by users';
$lang['help_upload'] =
array(
  'PhpWebGallery offers the possibility for users to upload images. in order to
do it :',
  
  '1. authorize upload on any categories (administration panel, Categories,
Manage, edit or administration panel, Categories, Upload)',

  '2. give write rights on directories for ugo (user, group, other)',
  
  'Files uploaded by users are not directly visible on the website, they must
be validated by an administrator. For that purpose, an administrator must go
on administration panel, Pictures, Waiting in order to validate or to refuse
the files proposed, then to synchronize filesystem with database.'
  );

$lang['help_virtual_title'] = 'Links between elements and categories, virtual categories';
$lang['help_virtual'] =
array(
  'PhpWebGallery dissociates categories where elements are stored and
categories where they are shown.',
  
  'By default, elements are shown only in their real categories : the ones
corresponding to directories on the web server.',

  'To link an element to a category, you just have to make the association on
the page of element edition (link to this screen on picture.php logged as an
administrator) or on the informations of all elements of a category.',
  
  'Using this principle, it is possible to create virtual categories : no
directory coresponds to this category. You can create virtual categories in
administration panel, Categories, Manage.'
  );

$lang['help_infos_title'] = 'Miscellanous informations';
$lang['help_infos'] =
array(
  'As soon as you created your gallery, modify default display properties in
administration panel, Configuration, Default. Indeed, every new registered
user will have by default the same display properties.',
  
  'If you have any question, do not hesitate to take a look at the forum or ask
a question there. The <a href="http://forum.phpwebgallery.net"
style="text-decoration:underline">forum</a> (message board) is available on
the presentation site of PhpWebGallery. Check the <a
href="http://doc.phpwebgallery.net"
style="text-decoration:underline">official PhpWebGallery documentation</a> for
further reading.'
  );
?>