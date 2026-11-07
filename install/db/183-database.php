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

$upgrade_description = 'add search.last_used_on, used to purge guest searches';

$query = '
ALTER TABLE `'.PREFIX_TABLE.'search`
  ADD COLUMN `last_used_on` DATETIME DEFAULT NULL,
  ADD KEY `search_i1` (`created_by`, `last_used_on`)
;';
pwg_query($query);

$query = '
UPDATE `'.PREFIX_TABLE.'search`
  SET last_used_on = created_on
;';
pwg_query($query);

echo "\n".$upgrade_description."\n";

?>
