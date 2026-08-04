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

$upgrade_description = 'Delete old column search.last_seen, never really used.';

pwg_query('UPDATE `'.PREFIX_TABLE.'search` SET `created_on` = `last_seen` where `created_on` IS NULL AND `last_seen` IS NOT NULL;');
pwg_query('ALTER TABLE `'.PREFIX_TABLE.'search` DROP COLUMN `last_seen`;');

echo "\n".$upgrade_description."\n";

?>
