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

define('PHPWG_ROOT_PATH', '../../');
include_once(PHPWG_ROOT_PATH . 'include/common.inc.php');
include_once(LOCALEDIT_PATH.'functions.inc.php');
check_status(ACCESS_ADMINISTRATOR);

$possible_values = array('on', 'off');

if (isset($_POST['editarea']) and in_array($_POST['editarea'], $possible_values))
{
  if (!isset($conf['LocalFilesEditor']))
  {
    include_once(LOCALEDIT_PATH.'maintain.inc.php');
    plugin_install();
  }
  $query = '
UPDATE ' . CONFIG_TABLE . '
SET value = "' . $_POST['editarea'] . '"
WHERE param="LocalFilesEditor"
LIMIT 1';
  pwg_query($query);
}

?>