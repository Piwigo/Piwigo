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

$upgrade_description = 'delete the config parameter comments_update_validation';

$query = 'DELETE FROM '.CONFIG_TABLE.' WHERE param = \'comments_update_validation\';';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>