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

$upgrade_description = 'increase history.IP length from VARCHAR(16) to CHAR(39), IPv6 compatible';

$query = 'ALTER TABLE `'.PREFIX_TABLE.'history` CHANGE `IP` `IP` char(39) NOT NULL default \'\';';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>
