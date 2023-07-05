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

$upgrade_description = 'Change type from text to mediumtext for #sessions.data #user_cache.forbidden_categories and #user_cache.image_access_list';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.SESSIONS_TABLE.'
  MODIFY COLUMN data MEDIUMTEXT NOT NULL';
pwg_query($query);

$query = '
ALTER TABLE '.USER_CACHE_TABLE.'
  MODIFY COLUMN forbidden_categories MEDIUMTEXT,
  MODIFY COLUMN image_access_list MEDIUMTEXT
  ';
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
