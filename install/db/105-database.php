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

$upgrade_description = 'Show menubar on picture page';

$query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment)
  VALUES (\'picture_menu\',\'false\', \''.$upgrade_description.'\')
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>