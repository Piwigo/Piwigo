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

$upgrade_description = 'Modification to the user_auth_key table to match the api keys';

// we are modifying the "auth_key" table structure to support the new API key system.
// the existing structure was too limited for our needs, this update ensures better 
// flexibility and security for managing API access tokens in the future.
pwg_query(
'ALTER TABLE `'.PREFIX_TABLE.'user_auth_keys` 
  ADD COLUMN `apikey_secret` VARCHAR(255) DEFAULT NULL AFTER auth_key,
  ADD COLUMN `apikey_name` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN `key_type` VARCHAR(40) DEFAULT NULL,
  ADD COLUMN `revoked_on`  datetime DEFAULT NULL,
  ADD COLUMN `last_used_on` datetime DEFAULT NULL
;');

// For rows that already exist in the table, we add a key_type
pwg_query(
'UPDATE `'.PREFIX_TABLE.'user_auth_keys` 
  SET `key_type` = \'auth_key\'
  WHERE `key_type` IS NULL
;');

echo "\n".$upgrade_description."\n";
?>