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

$upgrade_description = 'add columns session_idx+ip_address in activity table';

pwg_query('alter table `'.PREFIX_TABLE.'activity` add column `session_idx` varchar(255) NOT NULL after `performed_by`;');
pwg_query('alter table `'.PREFIX_TABLE.'activity` add column `ip_address` varchar(50) default null after session_idx;');

echo "\n".$upgrade_description."\n";

?>
