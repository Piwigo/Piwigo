<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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
$applied = query2array($query, null, 'id');

// retrieve existing upgrades
$existing = get_available_upgrade_ids();

// which upgrades need to be applied?
$to_apply = array_diff($existing, $applied);
$inserts = array();
foreach ($to_apply as $upgrade_id)
{
  if ($upgrade_id >= 153) // TODO change on each release (first task to run)
  {
    break;
  }
  
  array_push(
    $inserts,
    array(
      'id' => $upgrade_id,
      'applied' => CURRENT_DATE,
      'description' => '[migration from 2.9.0 to '.PHPWG_VERSION.'] not applied', // TODO change on each release
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

for ($upgrade_id = 153; $upgrade_id <= 156; $upgrade_id++) // TODO change on each release
{
  if (!file_exists(UPGRADES_PATH.'/'.$upgrade_id.'-database.php'))
  {
    continue;
  }

  // maybe the upgrade task has already been applied in a previous and
  // incomplete upgrade
  if (in_array($upgrade_id, $applied))
  {
    continue;
  }
  
  unset($upgrade_description);

  echo "\n\n";
  echo '=== upgrade '.$upgrade_id."\n";

  // include & execute upgrade script. Each upgrade script must contain
  // $upgrade_description variable which describe briefly what the upgrade
  // script does.
  $up_start = get_moment();
  include(UPGRADES_PATH.'/'.$upgrade_id.'-database.php');

  // notify upgrade (TODO change on each release)
  $query = '
INSERT INTO `'.PREFIX_TABLE.'upgrade`
  (id, applied, description)
  VALUES
  (\''.$upgrade_id.'\', NOW(), \'[migration from 2.9.0 to '.PHPWG_VERSION.', '.get_elapsed_time($up_start, get_moment()).'] '.$upgrade_description.'\')
;';
  pwg_query($query);
}

echo '</pre>';
ob_end_clean();

// now we upgrade from 2.10.0
include_once(PHPWG_ROOT_PATH.'install/upgrade_2.10.0.php');
?>
