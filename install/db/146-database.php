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

$upgrade_description = 'add format_id in history table';

// we use PREFIX_TABLE, in case Piwigo uses an external user table
pwg_query('
ALTER TABLE `'.PREFIX_TABLE.'history`
  ADD COLUMN `format_id` int(11) unsigned DEFAULT NULL
;');

echo "\n".$upgrade_description."\n";

?>
