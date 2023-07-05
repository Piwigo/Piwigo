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

$upgrade_description = 'New colum user_cache_categories.user_representative_picture_id';

// Add column
$query = 'ALTER TABLE '.USER_CACHE_CATEGORIES_TABLE.' ADD COLUMN ';

if ('mysql' == $conf['dblayer'])
{
  $query.= ' `user_representative_picture_id` mediumint(8) unsigned default NULL';
}

if (in_array($conf['dblayer'], array('pgsql', 'sqlite', 'pdo-sqlite')))
{
  $query.= ' "user_representative_picture_id" INTEGER';
}

$query.= ';';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>