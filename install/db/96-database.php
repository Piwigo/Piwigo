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

$upgrade_description = 'add time in images.date_creation column';

// Only MySQL is concerned, other DB engines are already "timestamp"
if ('mysql' == $conf['dblayer'])
{
  $query = '
ALTER TABLE '.IMAGES_TABLE.'
  MODIFY date_creation datetime
;';
  pwg_query($query);
}

echo
"\n"
. $upgrade_description
."\n"
;
?>