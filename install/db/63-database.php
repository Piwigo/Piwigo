<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
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

$upgrade_description = 'Add #user_infos.level, #images.level and #user_cache.forbidden_images';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.IMAGES_TABLE.' ADD COLUMN level TINYINT UNSIGNED NOT NULL DEFAULT 0
';
pwg_query($query);

$query = '
ALTER TABLE '.USER_INFOS_TABLE.' ADD COLUMN level TINYINT UNSIGNED NOT NULL DEFAULT 0
';
pwg_query($query);

$query = '
ALTER TABLE '.USER_CACHE_TABLE.' ADD COLUMN image_access_type enum("NOT IN","IN") NOT NULL default "NOT IN"
';
pwg_query($query);

$query = '
ALTER TABLE '.USER_CACHE_TABLE.' ADD COLUMN image_access_list TEXT DEFAULT NULL
';
pwg_query($query);

$query = '
UPDATE '.USER_INFOS_TABLE.' SET level=8 WHERE status="webmaster"
';
pwg_query($query);

$query = '
UPDATE '.USER_CACHE_TABLE.' SET need_update=true
';
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
