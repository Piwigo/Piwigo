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

$upgrade_description = 'add lounge table';

pwg_query('
CREATE TABLE `'.PREFIX_TABLE.'lounge` (
  `image_id` mediumint(8) unsigned NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`image_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');

echo "\n".$upgrade_description."\n";

?>
