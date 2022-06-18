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

$upgrade_description = 'set default date to 1970-01-01';

$queries = array(
  'ALTER TABLE `'.PREFIX_TABLE.'comments` CHANGE `date` `date` datetime NOT NULL default \'1970-01-01 00:00:00\';',
  'ALTER TABLE `'.PREFIX_TABLE.'history` CHANGE `date` `date` date NOT NULL default \'1970-01-01\';',
  'ALTER TABLE `'.PREFIX_TABLE.'images` CHANGE `date_available` `date_available` datetime NOT NULL default \'1970-01-01 00:00:00\';',
  'ALTER TABLE `'.PREFIX_TABLE.'old_permalinks` CHANGE  `date_deleted` `date_deleted` datetime NOT NULL default \'1970-01-01 00:00:00\';',
  'ALTER TABLE `'.PREFIX_TABLE.'rate` CHANGE `date` `date` date NOT NULL default \'1970-01-01\';',
  'ALTER TABLE `'.PREFIX_TABLE.'sessions` CHANGE `expiration` `expiration` datetime NOT NULL default \'1970-01-01 00:00:00\';',
  'ALTER TABLE `'.PREFIX_TABLE.'upgrade` CHANGE `applied` `applied` datetime NOT NULL default \'1970-01-01 00:00:00\';',
  'ALTER TABLE `'.PREFIX_TABLE.'user_infos` CHANGE `registration_date` `registration_date` datetime NOT NULL default \'1970-01-01 00:00:00\';',
);

foreach ($queries as $query)
{
  pwg_query($query);
}

echo "\n".$upgrade_description."\n";
?>
