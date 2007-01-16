<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-09-21 00:04:57 +0200 (mer, 21 sep 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 870 $
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

$upgrade_description = 'History table new model and new table history_summary';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

echo "Recreate table ".HISTORY_TABLE."\n";

$query = 'DROP TABLE '.HISTORY_TABLE.';';
pwg_query($query);

$query = "
CREATE TABLE `".HISTORY_TABLE."` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `time` time NOT NULL default '00:00:00',
  `year` smallint(4) NOT NULL default '0',
  `month` tinyint(2) NOT NULL default '0',
  `day` tinyint(2) NOT NULL default '0',
  `hour` tinyint(2) NOT NULL default '0',
  `user_id` smallint(5) NOT NULL default '0',
  `IP` varchar(15) NOT NULL default '',
  `section` enum('categories','tags','search','list','favorites','most_visited','best_rated','recent_pics','recent_cats') default NULL,
  `category_id` smallint(5) default NULL,
  `tag_ids` varchar(50) default NULL,
  `image_id` mediumint(8) default NULL,
  `summarized` enum('true','false') default 'false',
  PRIMARY KEY  (`id`),
  KEY `history_i1` (`summarized`)
) TYPE=MyISAM
;";
pwg_query($query);

echo "Create table ".HISTORY_SUMMARY_TABLE."\n";
$query = "
CREATE TABLE `".HISTORY_SUMMARY_TABLE."` (
  `id` varchar(13) NOT NULL default '',
  `year` smallint(4) NOT NULL default '0',
  `month` tinyint(2) default NULL,
  `day` tinyint(2) default NULL,
  `hour` tinyint(2) default NULL,
  `nb_pages` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM
;";
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
