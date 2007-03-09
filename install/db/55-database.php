<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id: 45-database.php 1741 2007-01-22 21:47:03Z vdigital $
// | last update   : $Date: 2007-01-22 22:47:03 +0100 (lun., 22 janv. 2007) $
// | last modifier : $Author: vdigital $
// | revision      : $Revision: 1741 $
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

$upgrade_description =
  'Update #history.image_type to "picture" by default when image_id is not null';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = "
UPDATE ".HISTORY_TABLE."
  SET image_type = 'picture'
  WHERE image_id IS NOT NULL
    AND image_type IS NULL
;";
pwg_query($query);

$query = "
UPDATE ".HISTORY_TABLE."
  SET image_type = NULL
  WHERE image_id IS NULL
;";
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.', ended'
."\n"
;

?>
