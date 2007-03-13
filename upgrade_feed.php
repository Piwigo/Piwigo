<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-09-21 00:04:57 +0200 (mer, 21 sep 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 870 $
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

define('PHPWG_ROOT_PATH', './');

include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');
include(PHPWG_ROOT_PATH.'include/template.php');
include(PHPWG_ROOT_PATH.'include/mysql.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when it is not ok                               |
// +-----------------------------------------------------------------------+
check_upgrade();

define('PREFIX_TABLE', $prefixeTable);
define('UPGRADES_PATH', PHPWG_ROOT_PATH.'install/db');

// +-----------------------------------------------------------------------+
// |                         Database connection                           |
// +-----------------------------------------------------------------------+

mysql_connect($cfgHote, $cfgUser, $cfgPassword)
or die("Could not connect to database server");
mysql_select_db($cfgBase)
or die("Could not connect to database");

// +-----------------------------------------------------------------------+
// |                              Upgrades                                 |
// +-----------------------------------------------------------------------+

// retrieve already applied upgrades
$query = '
SELECT id
  FROM '.PREFIX_TABLE.'upgrade
;';
$applied = array_from_query($query, 'id');

// retrieve existing upgrades
$existing = get_available_upgrade_ids();

// which upgrades need to be applied?
$to_apply = array_diff($existing, $applied);

echo '<pre>';
echo count($to_apply).' upgrades to apply';

foreach ($to_apply as $upgrade_id)
{
  unset($upgrade_description);
  
  echo "\n\n";
  echo '=== upgrade '.$upgrade_id."\n";

  // include & execute upgrade script. Each upgrade script must contain
  // $upgrade_description variable which describe briefly what the upgrade
  // script does.
  include(UPGRADES_PATH.'/'.$upgrade_id.'-database.php');

  // notify upgrade
  $query = '
INSERT INTO '.PREFIX_TABLE.'upgrade
  (id, applied, description)
  VALUES
  (\''.$upgrade_id.'\', NOW(), \''.$upgrade_description.'\')
;';
  pwg_query($query);
}

echo '</pre>';
?>
