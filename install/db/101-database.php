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

$upgrade_description = 'merge nb_line_page and nb_image_line into nb_image_page';

// add column
if ('mysql' == $conf['dblayer'])
{
  pwg_query('
    ALTER TABLE '.USER_INFOS_TABLE.' 
      ADD COLUMN `nb_image_page` smallint(3) unsigned NOT NULL default \'15\'
  ;');
}
else if (in_array($conf['dblayer'], array('pgsql', 'sqlite', 'pdo-sqlite')))
{
  pwg_query('
    ALTER TABLE '.USER_INFOS_TABLE.' 
      ADD COLUMN "nb_image_page" INTEGER default 15 NOT NULL
  ;');
}

// merge datas
pwg_query('
  UPDATE '.USER_INFOS_TABLE.' 
  SET nb_image_page = nb_line_page*nb_image_line
;');

// delete old columns
pwg_query('
  ALTER TABLE '.USER_INFOS_TABLE.' 
    DROP `nb_line_page`,
    DROP `nb_image_line`
;');

echo
"\n"
. $upgrade_description
."\n"
;
?>