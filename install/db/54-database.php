<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'add column #categories.permalink and table #old_permalinks';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

defined('OLD_PERMALINKS_TABLE') or die('OLD_PERMALINKS_TABLE is not defined');

$query = "
CREATE TABLE `".OLD_PERMALINKS_TABLE."` (
  `cat_id` smallint(5) unsigned NOT NULL,
  `permalink` VARCHAR(64) NOT NULL,
  `date_deleted` datetime NOT NULL,
  `last_hit` datetime default NULL,
  `hit` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`permalink`)
) TYPE=MyISAM
;";
pwg_query($query);

$query = "
ALTER TABLE `".CATEGORIES_TABLE."`
  ADD COLUMN `permalink` VARCHAR(64) default NULL
;";
pwg_query($query);

$query = "
ALTER TABLE `".CATEGORIES_TABLE."`
  ADD UNIQUE INDEX `categories_i3` (`permalink`)
;";
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
