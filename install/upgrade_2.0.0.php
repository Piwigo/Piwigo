<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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
  die ('This page cannot be loaded directly, load upgrade.php');
}
else
{
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    die ('Hacking attempt!');
  }
}

// +-----------------------------------------------------------------------+
// |             Fill upgrade table without applying upgrade               |
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
$inserts = array();
foreach ($to_apply as $upgrade_id)
{
  if ($upgrade_id >= 81)
  {
    break;
  }
  
  array_push(
    $inserts,
    array(
      'id' => $upgrade_id,
      'applied' => CURRENT_DATE,
      'description' => '[migration from 2.0.0 to '.PHPWG_VERSION.'] not applied',
      )
    );
}

if (!empty($inserts))
{
  mass_inserts(
    '`'.UPGRADE_TABLE.'`',
    array_keys($inserts[0]),
    $inserts
    );
}

// +-----------------------------------------------------------------------+
// |                          Perform upgrades                             |
// +-----------------------------------------------------------------------+

ob_start();
echo '<pre>';

for ($upgrade_id = 81; $upgrade_id <= 90; $upgrade_id++)
{
  if (!file_exists(UPGRADES_PATH.'/'.$upgrade_id.'-database.php'))
  {
    break;
  }
  
  unset($upgrade_description);

  echo "\n\n";
  echo '=== upgrade '.$upgrade_id."\n";

  // include & execute upgrade script. Each upgrade script must contain
  // $upgrade_description variable which describe briefly what the upgrade
  // script does.
  include(UPGRADES_PATH.'/'.$upgrade_id.'-database.php');

  // notify upgrade
  $query = '
INSERT INTO `'.PREFIX_TABLE.'upgrade`
  (id, applied, description)
  VALUES
  (\''.$upgrade_id.'\', NOW(), \'[migration from 2.0.0 to '.PHPWG_VERSION.'] '.$upgrade_description.'\')
;';
  pwg_query($query);
}

echo '</pre>';
ob_end_clean();

// now we upgrade from 2.1.0
include_once(PHPWG_ROOT_PATH.'install/upgrade_2.1.0.php');
?>
