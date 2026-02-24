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

$upgrade_description = 'Modification to the user_auth_key table to add last_notified_on';

// For API KEY, add a column last_notified_on, to know when the last email (for the moment)
// notifying of an upcoming expiration date was sent.
pwg_query(
'ALTER TABLE `'.PREFIX_TABLE.'user_auth_keys` 
  ADD COLUMN `last_notified_on` datetime DEFAULT NULL
;');

echo "\n".$upgrade_description."\n";
?>