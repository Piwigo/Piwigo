<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'Cache user categories update';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

echo "Create table ".USER_CACHE_CATEGORIES_TABLE;
$query = '
CREATE TABLE '.USER_CACHE_CATEGORIES_TABLE.' (
  `user_id` smallint(5) NOT NULL default \'0\',
  `cat_id` smallint(5) unsigned NOT NULL default \'0\',
  `is_child_date_last` enum(\'true\',\'false\') NOT NULL default \'false\',
  `max_date_last` datetime default NULL,
  `count_images` mediumint(8) unsigned default 0,
  `count_categories` mediumint(8) unsigned default 0,
  PRIMARY KEY  (`user_id`, `cat_id`)
) TYPE=MyISAM;';
pwg_query($query);

echo "Update table cache ".USER_CACHE_TABLE;
$query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\'
;';
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
