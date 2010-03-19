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

$upgrade_description = 'Update default template to default theme.';

$query = '
ALTER TABLE '.USER_INFOS_TABLE.'
  CHANGE COLUMN template theme varchar(255) NOT NULL default \'Sylvia\'
;';

pwg_query($query);

$query = '
SELECT user_id, theme
  FROM '.USER_INFOS_TABLE.'
;';

$result = pwg_query($query);

$users = array();
while ($row = pwg_db_fetch_assoc($result))
{
  list($user_template, $user_theme) = explode('/', $row['theme']);

  if ($user_template != 'yoga')
  {
    $user_theme = 'Sylvia'; // We can find better!
  }
  array_push($users, array(
    'user_id' => $row['user_id'],
    'theme' => $user_theme
    )
  );
}

mass_updates(
  USER_INFOS_TABLE,
  array(
    'primary' => array('user_id'),
    'update'  => array('theme')
    ),
  $users
  );

echo
"\n"
. $upgrade_description
."\n"
;
?>