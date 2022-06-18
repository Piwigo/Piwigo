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

$upgrade_description = 'Display new icons next albums and pictures';

$query = '
INSERT INTO '.PREFIX_TABLE.'config (param,value,comment) 
  VALUES (\'index_new_icon\',\'true\',\'Display new icons next albums and pictures\')
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>