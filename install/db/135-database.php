<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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