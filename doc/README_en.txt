=============
PhpWebGallery
=============

http://phpwebgallery.net


Installation
============

1. extract files from the downloaded file (using tar or unzip command, or
   softwares like 7-zip or winzip)

2. place de source files on your website in the directory of your choice
   ("gallery" for example)

3. go to the URL http://your.domain/gallery/install.php and follow the
   instructions of installation

Upgrade
=======

1. elements to save :

 - file "include/mysql.inc.php"
 - file "include/config_local.inc.php" if you have one
 - directory "galleries"
 - your database (create a dump, using PhpMyAdmin for instance)

2. delete all files and directories of your previous installation (but not
   the previous listed elements)

3. extract files from the downloaded file

4. upload all the new version files to your website but the previous listed
   elements. The only elements coming from the previous installed version
   are the elements listed above.

5. go to the URL http://your.domain/gallery/upgrade.php and follow the
   instructions

How to start
============

Once installed or upgraded, your gallery is ready to run. Start by
displaying the installation directory in your browser :

http://your.domain/gallery

Then identify as an administrator. A new link in Identification menu of main
page will appear : Administration. Enter the administration panel.

In the administration panel, take all your time for reading instructions
explaining how to use your gallery.

Communication
=============

Newsletter
----------

https://gna.org/mail/?group=phpwebgallery

It is *highly* recommended to subscribe to PhpWebGallery newsletter. This is
extremely low-traffic, but will provide you with announcements of new
PhpWebGallery releases and serious bug notification. You will find available
mailing lists at this URL :

No spam, no commercial use.

Freshmeat
---------

http://freshmeat.net/projects/phpwebgallery

Want to stay informed at each release, stable and development
release. Development releases notification are not send in the newsletter.

Bugtracker
----------

http://bugs.phpwebgallery.net

Bugs and change requests tracking. The best way to have your bug corrected:
it won't be forgotten (as in the forum).

Wiki
----

http://phpwebgallery.net/doc

Wiki documentation: everyone can participate to improve documentation
content.

Message board
-------------

http://forum.phpwebgallery.net

All communications (installation help, technical discussions) that can't be
done in other channels.
