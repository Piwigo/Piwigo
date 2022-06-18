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

$upgrade_description = 'Move #categories.date_last and nb_images to #user_cache_categories';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.USER_CACHE_CATEGORIES_TABLE.'
  ADD COLUMN date_last datetime default NULL AFTER cat_id,
  ADD COLUMN nb_images mediumint(8) unsigned NOT NULL default 0 AFTER max_date_last';
pwg_query($query);

$query = '
ALTER TABLE '.CATEGORIES_TABLE.'
  DROP COLUMN date_last,
  DROP COLUMN nb_images
  ';
pwg_query($query);

invalidate_user_cache(); // just to force recalculation

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
