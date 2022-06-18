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

$upgrade_description = 'add anonymous_id in comments table';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

$query = 'ALTER TABLE `'.COMMENTS_TABLE.'` ADD `anonymous_id` VARCHAR( 45 ) DEFAULT NULL;';
pwg_query($query);

echo "\n".$upgrade_description."\n";

?>