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

$upgrade_description = 'add image formats table';

// we use PREFIX_TABLE, in case Piwigo uses an external user table
pwg_query('
CREATE TABLE `'.PREFIX_TABLE.'image_format` (
  `format_id` int(11) unsigned NOT NULL auto_increment,
  `image_id` mediumint(8) unsigned NOT NULL DEFAULT \'0\',
  `ext` varchar(255) NOT NULL,
  `filesize` mediumint(9) unsigned DEFAULT NULL,
  PRIMARY KEY  (`format_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');

echo "\n".$upgrade_description."\n";

?>
