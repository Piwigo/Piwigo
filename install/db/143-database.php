<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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
