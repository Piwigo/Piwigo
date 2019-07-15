<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = '#categories.site_id default value.';

$query = 'ALTER TABLE '.CATEGORIES_TABLE.' CHANGE site_id site_id tinyint(4) unsigned default NULL';
pwg_query($query);

$query = 'UPDATE '.CATEGORIES_TABLE.' SET site_id=NULL WHERE dir IS NULL';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>