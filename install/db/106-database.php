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

$upgrade_description = 'add order parameters to bdd';

$query = '
INSERT INTO '.PREFIX_TABLE.'config(param,value,comment) 
  VALUES (\'order_by\', \''.$conf['order_by'].'\', \'default photos order\')
;';
pwg_query($query);

$query = '
INSERT INTO '.PREFIX_TABLE.'config(param,value,comment) 
  VALUES (\'order_by_inside_category\', \''.$conf['order_by_inside_category'].'\', \'default photos order inside category\')
;';
pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>