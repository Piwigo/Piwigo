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

$upgrade_description = 'add high_width and high_height fields into IMAGES_TABLE';

if ('mysql' == $conf['dblayer'])
{
  $query = 'ALTER TABLE '.IMAGES_TABLE.' 
    ADD COLUMN `high_width` smallint(9) unsigned default NULL, 
    ADD COLUMN `high_height` smallint(9) unsigned default NULL;';
}

if (in_array($conf['dblayer'], array('pgsql', 'sqlite', 'pdo-sqlite')))
{
  $query = 'ALTER TABLE '.IMAGES_TABLE.' 
    ADD COLUMN "high_width" INTEGER, 
    ADD COLUMN "high_height" INTEGER;';
}

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>