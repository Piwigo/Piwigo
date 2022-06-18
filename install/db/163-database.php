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

$upgrade_description = 'add user_infos.preferences';

pwg_query('
ALTER TABLE `'.PREFIX_TABLE.'user_infos`
  ADD COLUMN `preferences` TEXT default NULL
;');

echo "\n".$upgrade_description."\n";

?>
