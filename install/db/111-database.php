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

$upgrade_description = 'New colum user_infos.activation_key';

// Add column
$query = 'ALTER TABLE '.USER_INFOS_TABLE.' ADD COLUMN ';

if ('mysql' == $conf['dblayer'])
{
  $query.= ' `activation_key` char(20) default NULL';
}

if (in_array($conf['dblayer'], array('pgsql', 'sqlite', 'pdo-sqlite')))
{
  $query.= ' "activation_key" CHAR(20) default NULL';
}

$query.= ';';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>