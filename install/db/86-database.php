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

$upgrade_description = 'Automatically activate core themes and used themes.';

$themes_core = array('Sylvia', 'dark', 'clear');

$query = '
SELECT
    DISTINCT(theme)
  FROM '.PREFIX_TABLE.'user_infos
;';
$themes_used = array_from_query($query, 'theme');

$query = '
SELECT
    id
  FROM '.PREFIX_TABLE.'themes
;';
$themes_active = array_from_query($query, 'id');


$themes_to_activate = array_diff(
  array_unique(array_merge($themes_used, $themes_core)),
  $themes_active
  );

// echo '<pre>'; print_r($themes_to_activate); echo '</pre>'; exit();

foreach ($themes_to_activate as $theme)
{
  $query = '
INSERT INTO '.PREFIX_TABLE.'themes
  (id) VALUES(\''.$theme.'\'
;';
  pwg_query($query);
}

echo
"\n"
. $upgrade_description
."\n"
;
?>