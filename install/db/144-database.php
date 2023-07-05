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

$upgrade_description = 'add activation_key_expire';

// we use PREFIX_TABLE, in case Piwigo uses an external user table
pwg_query('
ALTER TABLE '.USER_INFOS_TABLE.'
  CHANGE activation_key activation_key VARCHAR(255) DEFAULT NULL,
  ADD COLUMN activation_key_expire DATETIME DEFAULT NULL AFTER activation_key
;');

// purge current expiration keys
pwg_query('UPDATE '.USER_INFOS_TABLE.' SET activation_key = NULL;');

echo "\n".$upgrade_description."\n";

?>
