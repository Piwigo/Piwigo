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

$upgrade_description = 'Create dedicated user agent column for activity.';

pwg_query('
ALTER TABLE `'.PREFIX_TABLE.'activity`
  ADD COLUMN `user_agent` varchar(255) default NULL
;');

echo "\n".$upgrade_description."\n";

?>
