<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'enlarge your user_id (16 millions possible users)';

// we use PREFIX_TABLE, in case Piwigo uses an external user table
pwg_query('ALTER TABLE '.PREFIX_TABLE.'users CHANGE id id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT;');
pwg_query('ALTER TABLE '.IMAGES_TABLE.' CHANGE added_by added_by MEDIUMINT UNSIGNED NOT NULL DEFAULT \'0\';');
pwg_query('ALTER TABLE '.COMMENTS_TABLE.' CHANGE author_id author_id MEDIUMINT UNSIGNED DEFAULT NULL;');

$tables = array(
  USER_ACCESS_TABLE,
  USER_CACHE_TABLE,
  USER_FEED_TABLE,
  USER_GROUP_TABLE,
  USER_INFOS_TABLE,
  USER_CACHE_CATEGORIES_TABLE,
  USER_MAIL_NOTIFICATION_TABLE,
  RATE_TABLE,
  CADDIE_TABLE,
  FAVORITES_TABLE,
  HISTORY_TABLE,
  );

foreach ($tables as $table)
{
  pwg_query('
ALTER TABLE '.$table.'
  CHANGE user_id user_id MEDIUMINT UNSIGNED NOT NULL DEFAULT \'0\'
;');
}

echo "\n".$upgrade_description."\n";

?>
