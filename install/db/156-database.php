<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description = 'bug fixing, change column type for activity.occured_on';

$row = pwg_db_fetch_assoc(pwg_query('SHOW COLUMNS FROM `'.PREFIX_TABLE.'activity` LIKE "occured_on";'));
if (!preg_match('/^TIMESTAMP/i', $row['Type']))
{
  $query = 'ALTER TABLE `'.PREFIX_TABLE.'activity` CHANGE `occured_on` `occured_on` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;';
  pwg_query($query);
}

echo "\n".$upgrade_description."\n";

?>
