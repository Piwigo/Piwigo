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

$upgrade_description = 'images.file categories.permalink old_permalinks.permalink - become binary';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = 'ALTER TABLE '.CATEGORIES_TABLE.'
  MODIFY COLUMN permalink varchar(64) binary default NULL';
pwg_query($query);

$query = 'ALTER TABLE '.OLD_PERMALINKS_TABLE.'
  MODIFY COLUMN permalink varchar(64) binary NOT NULL default ""';
pwg_query($query);

$query = 'ALTER TABLE '.IMAGES_TABLE.'
  MODIFY COLUMN file varchar(255) binary NOT NULL default ""';
pwg_query($query);


echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
