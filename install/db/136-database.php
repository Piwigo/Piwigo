<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'add nb direct child categories';

$query = '
ALTER TABLE '.USER_CACHE_CATEGORIES_TABLE.'
  ADD COLUMN nb_categories mediumint(8) unsigned NOT NULL default 0 AFTER count_images';
pwg_query($query);

invalidate_user_cache();


echo "\n".$upgrade_description."\n";
?>