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

$upgrade_description = 'New colum images.added_by, reference to users.id';

// Add column
$query = 'ALTER TABLE '.IMAGES_TABLE.' ADD COLUMN ';

if ('mysql' == $conf['dblayer'])
{
  $query.= ' added_by smallint(5)';
}

if (in_array($conf['dblayer'], array('pgsql', 'sqlite', 'pdo-sqlite')))
{
  $query.= ' "added_by" INTEGER default 0';
}

$query.= ' NOT NULL;';

pwg_query($query);

// set the existing photos with the webmaster_id as added_by
$query = 'UPDATE '.IMAGES_TABLE.' SET added_by = '.$conf['webmaster_id'].';';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>