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

$upgrade_description = 'Add image_category.rank column';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = 'ALTER TABLE '.IMAGE_CATEGORY_TABLE.' add column `rank` mediumint(8) unsigned default NULL';
pwg_query($query);

$upgrade_description = $query;

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
