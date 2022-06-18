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

$upgrade_description = '#tags.name is not binary';

// add fields
$query = 'ALTER TABLE '.TAGS_TABLE.' CHANGE COLUMN `name` `name` VARCHAR(255) NOT NULL DEFAULT \'\'';
pwg_query($query);

echo "\n".$upgrade_description."\n";

?>
