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

$upgrade_description = 'add last_visit+last_visit_from_history in user_infos table';

// we use PREFIX_TABLE, in case Piwigo uses an external user table
pwg_query('
ALTER TABLE `'.PREFIX_TABLE.'user_infos`
  ADD COLUMN `last_visit` datetime default NULL,
  ADD COLUMN `last_visit_from_history` enum(\'true\',\'false\') NOT NULL default \'false\'
;');

echo "\n".$upgrade_description."\n";

?>
