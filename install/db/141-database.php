<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'add lastmodified field for categories, images, groups, users, tags';

$tables = array(
  CATEGORIES_TABLE,
  GROUPS_TABLE,
  IMAGES_TABLE,
  TAGS_TABLE,
  USER_INFOS_TABLE
  );
 
foreach ($tables as $table)
{
  pwg_query('
ALTER TABLE '. $table .'
  ADD `lastmodified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD INDEX `lastmodified` (`lastmodified`)
;');
}

echo "\n".$upgrade_description."\n";
