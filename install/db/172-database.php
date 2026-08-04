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

$upgrade_description = 'reduce sessions.id length to 50 chars';

$query = 'ALTER TABLE `'.PREFIX_TABLE.'sessions` CHANGE `id` `id` varchar(50) binary NOT NULL default \'\';';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>
