<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'Update template preference for every user';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

// configuration update
$query = '
UPDATE '.PREFIX_TABLE.'config
  SET value = \'yoga/clear\'
  WHERE param = \'default_template\'
;';
pwg_query($query);

// set yoga/clear as default value for user_infos.template column
$query = '
ALTER TABLE '.PREFIX_TABLE.'user_infos
  CHANGE COLUMN template template varchar(255) NOT NULL default \'yoga/clear\'
;';
pwg_query($query);

// users having yoga-dark for template now have yoga/dark
$query = '
UPDATE '.PREFIX_TABLE.'user_infos
  SET template = \'yoga/dark\'
  WHERE template = \'yoga-dark\'
;';
pwg_query($query);

// all other users have yoga/clear
$query = '
UPDATE '.PREFIX_TABLE.'user_infos
  SET template = \'yoga/clear\'
  WHERE template != \'yoga/dark\'
;';
pwg_query($query);

echo
"\n"
.'Default template modified to yoga/clear'
."\n"
.'Template preference modified for every users : yoga/dark'
.' (for yoga-dark users) and yoga/clear as default'
."\n"
;
?>
