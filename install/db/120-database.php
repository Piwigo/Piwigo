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

$upgrade_description = 'rotation mode (code, not angle) is stored in the database';

$query = 'ALTER TABLE '.IMAGES_TABLE.' ADD COLUMN rotation tinyint unsigned DEFAULT NULL';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>