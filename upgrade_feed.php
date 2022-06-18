<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

//check php version
if (version_compare(PHP_VERSION, '5', '<'))
{
  die('Piwigo requires PHP 5 or above.');
}

define('PHPWG_ROOT_PATH', './');

include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');
defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');

include(PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php');
include(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');

include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');


// +-----------------------------------------------------------------------+
// | Check Access and exit when it is not ok                               |
// +-----------------------------------------------------------------------+

if (!$conf['check_upgrade_feed'])
{
  die("upgrade feed is not active");
}

prepare_conf_upgrade();

define('PREFIX_TABLE', $prefixeTable);
define('UPGRADES_PATH', PHPWG_ROOT_PATH.'install/db');

// +-----------------------------------------------------------------------+
// |                         Database connection                           |
// +-----------------------------------------------------------------------+
try
{
  pwg_db_connect($conf['db_host'], $conf['db_user'],
                 $conf['db_password'], $conf['db_base']);
}
catch (Exception $e)
{
  my_error(l10n($e->getMessage(), true)); 
}

pwg_db_check_charset();

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
