<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-07-23 14:17:00 +0200 (dim, 23 jui 2006) $
// | last modifier : $Author: nikrou $
// | revision      : $Revision: 1492 $
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

$upgrade_description = '#users.auto_login_key to #user_infos.auto_login_key';

$query = '
ALTER TABLE '.PREFIX_TABLE.'user_infos
  ADD auto_login_key varchar(64) NOT NULL
;';
pwg_query($query);

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');

$query = '
SELECT '.$conf['user_fields']['id'].' AS uid, auto_login_key
  FROM '.PREFIX_TABLE.'users
  WHERE auto_login_key != \'\'
;';
$result = pwg_query($query);

$datas = array();

while ($row = mysql_fetch_array($result))
{
  array_push(
    $datas,
    array(
      'user_id' => $row['uid'],
      'auto_login_key' => $row['auto_login_key'],
      )
    );
}

mass_updates(
  PREFIX_TABLE.'user_infos',
  array(
    'primary' => array('user_id'),
    'update'  => array('auto_login_key')
    ),
  $datas
  );

$query = '
ALTER TABLE '.PREFIX_TABLE.'users
  DROP COLUMN auto_login_key
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
