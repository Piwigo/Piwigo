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

$upgrade_description = 'Add #user_infos.level, #images.level and #user_cache.forbidden_images';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.IMAGES_TABLE.' ADD COLUMN level TINYINT UNSIGNED NOT NULL DEFAULT 0
';
pwg_query($query);

$query = '
ALTER TABLE '.USER_INFOS_TABLE.' ADD COLUMN level TINYINT UNSIGNED NOT NULL DEFAULT 0
';
pwg_query($query);

$query = '
ALTER TABLE '.USER_CACHE_TABLE.' ADD COLUMN image_access_type enum("NOT IN","IN") NOT NULL default "NOT IN"
';
pwg_query($query);

$query = '
ALTER TABLE '.USER_CACHE_TABLE.' ADD COLUMN image_access_list TEXT DEFAULT NULL
';
pwg_query($query);

$query = '
UPDATE '.USER_INFOS_TABLE.' SET level=8 WHERE status="webmaster"
';
pwg_query($query);

$query = '
UPDATE '.USER_CACHE_TABLE.' SET need_update=true
';
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
