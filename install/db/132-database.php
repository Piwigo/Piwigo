<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'Enlarge #users.password to increase security.';

global $prefixeTable;

// we don't use USERS_TABLE because it might be an external table, here we
// want to change to users table specific to Piwigo
$query = 'ALTER TABLE '.$prefixeTable.'users CHANGE password password varchar(255) default NULL';
pwg_query($query);

echo "\n".$upgrade_description."\n";
?>