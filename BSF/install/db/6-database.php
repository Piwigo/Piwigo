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

$upgrade_description = 'Table #user_mail_notification is required for NBM';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

// Creating table user_mail_notification
$query = '
CREATE TABLE '.PREFIX_TABLE.'user_mail_notification
(
  user_id smallint(5) NOT NULL default \'0\',
  check_key varchar(128) binary NOT NULL,
  enabled enum(\'true\',\'false\') NOT NULL default \'false\',
  last_send datetime default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `uidx_check_key` (`check_key`)
) TYPE=MyISAM;';
pwg_query($query);

echo
"\n"
.'Table '.PREFIX_TABLE.'user_mail_notification created'
."\n"
;
?>
