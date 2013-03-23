<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'add nb available comments/tags';

$query = 'ALTER TABLE '.USER_INFOS_TABLE.'
ADD PRIMARY KEY (`user_id`) 
, DROP INDEX `user_infos_ui1`';
pwg_query($query);

$query = 'ALTER TABLE '.USER_CACHE_TABLE.'
 ADD COLUMN `last_photo_date` datetime DEFAULT NULL AFTER `nb_total_images`';
pwg_query($query);
invalidate_user_cache();

$query = 'ALTER TABLE '.USER_CACHE_TABLE.'
 ADD COLUMN `nb_available_tags` INT(5) DEFAULT NULL AFTER `last_photo_date`';
pwg_query($query);

$query = 'ALTER TABLE '.USER_CACHE_TABLE.'
 ADD COLUMN `nb_available_comments` INT(5) DEFAULT NULL AFTER `nb_available_tags`';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>