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

$upgrade_description = 'Add a table to manage themes.';

$query = "
CREATE TABLE ".PREFIX_TABLE."themes (
  `id` varchar(64) NOT NULL default '',
  `version` varchar(64) NOT NULL default '0',
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

if (DB_CHARSET == 'utf8')
{
  $query .= " DEFAULT CHARACTER SET utf8";
}

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
