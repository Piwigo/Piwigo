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

$upgrade_description = 'Create new column search_id in visits history table.';

pwg_query('ALTER TABLE `'.PREFIX_TABLE.'history` ADD COLUMN `search_id` int(10) unsigned default NULL AFTER `category_id`;');

echo "\n".$upgrade_description."\n";

?>
