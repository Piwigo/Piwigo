<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'derivatives: remove useless configuration settings and columns';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

//
// Clean useless configuration settings
//
pwg_query('DELETE FROM '.CONFIG_TABLE.' WHERE param like \'upload_form_%\';');

//
// Remove useless columns
//

$query = '
ALTER TABLE '.USER_INFOS_TABLE.'
  DROP `maxwidth`,
  DROP `maxheight`
;';
pwg_query($query);

$query = '
ALTER TABLE '.IMAGES_TABLE.'
  DROP `high_width`,
  DROP `high_height`,
  DROP `high_filesize`,
  DROP `has_high`,
  DROP `tn_ext`
;';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>