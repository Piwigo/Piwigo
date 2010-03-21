<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'Field "Status" Table #user_infos changed';

include_once(PHPWG_ROOT_PATH.'include/constants.php');
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

echo "Alter table ".USER_INFOS_TABLE;
$query = "
alter table ".USER_INFOS_TABLE."
  modify column `status` enum('webmaster', 'admin', 'normal', 'generic', 'guest') NOT NULL default 'guest'
;";
pwg_query($query);

echo "Define webmaster";
$query = '
update
  '.USER_INFOS_TABLE.'
set status = \'webmaster\'
where
  user_id = '.$conf['webmaster_id'].' and status = \'admin\'
;';
$result = pwg_query($query);

echo "Define normal";
$query = '
select
  user_id
from
  '.USER_INFOS_TABLE.'
where
  user_id != '.$conf['guest_id'].' and status = \'guest\'
;';
$result = pwg_query($query);

$datas = array();

while ($row = pwg_db_fetch_assoc($result))
{
  array_push(
    $datas,
    array(
      'user_id'    => $row['user_id'],
      'status' => 'normal'
      )
    );
}

mass_updates(
  USER_INFOS_TABLE,
  array(
    'primary' => array('user_id'),
    'update'  => array('status')
    ),
  $datas
  );

// +-----------------------------------------------------------------------+
// |                           End notification                            |
// +-----------------------------------------------------------------------+

echo
"\n"
.'Column '.USER_INFOS_TABLE.'.status changed'
."\n"
;

?>
