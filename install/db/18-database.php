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

$upgrade_description = 'Reduce length of #_user_mail_notification.check_key';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_notification_by_mail.inc.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+
echo "Compute new check_key";
$query = '
select
  user_id
from
  '.USER_MAIL_NOTIFICATION_TABLE.'
;';
$result = pwg_query($query);

$datas = array();

while ($row = mysql_fetch_assoc($result))
{
  array_push(
    $datas,
    array(
      'user_id'    => $row['user_id'],
      'check_key' => find_available_check_key()
      )
    );
}

mass_updates(
  USER_MAIL_NOTIFICATION_TABLE,
  array(
    'primary' => array('user_id'),
    'update'  => array('check_key')
    ),
  $datas
  );

echo "Alter table ".USER_MAIL_NOTIFICATION_TABLE;
$query = "
alter table ".USER_MAIL_NOTIFICATION_TABLE."
  modify column `check_key` varchar(16) binary NOT NULL default ''
;";
pwg_query($query);


// +-----------------------------------------------------------------------+
// |                           End notification                            |
// +-----------------------------------------------------------------------+

echo
"\n"
.'Column '.USER_MAIL_NOTIFICATION_TABLE.'.check_key changed'
."\n"
;

?>
