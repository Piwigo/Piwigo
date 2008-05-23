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

$upgrade_description = 'Replace #history.is_high by #history.image_type';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = "
ALTER TABLE ".HISTORY_TABLE."
  ADD COLUMN image_type ENUM('picture', 'high', 'other') DEFAULT NULL
;";
pwg_query($query);

$query = "
UPDATE ".HISTORY_TABLE."
  SET image_type = 'high'
  WHERE is_high = 'true'
;";
pwg_query($query);

$query = "
UPDATE ".HISTORY_TABLE."
  SET image_type = 'picture'
  WHERE is_high = 'false'
;";
pwg_query($query);

$query = "
ALTER TABLE ".HISTORY_TABLE."
  DROP COLUMN is_high
;";
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.', ended'
."\n"
;

?>
