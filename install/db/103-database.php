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

$upgrade_description = 'Extensions ignored for update';

$query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment)
  VALUES (\'updates_ignored\',\'a:3:{s:7:"plugins";a:0:{}s:6:"themes";a:0:{}s:9:"languages";a:0:{}}\', \''.$upgrade_description.'\')
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>