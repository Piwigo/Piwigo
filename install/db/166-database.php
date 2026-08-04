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

$upgrade_description = 'Create new columns for search (search_uuid, created_on, user_idx, forked_from).';

pwg_query('
ALTER TABLE `'.PREFIX_TABLE.'search`
  ADD COLUMN `search_uuid` CHAR(23) DEFAULT NULL,
  ADD COLUMN `created_on` DATETIME DEFAULT NULL,
  ADD COLUMN `created_by` MEDIUMINT(8) UNSIGNED, 
  ADD COLUMN `forked_from` INT(10) UNSIGNED
;');

echo "\n".$upgrade_description."\n";

?>
