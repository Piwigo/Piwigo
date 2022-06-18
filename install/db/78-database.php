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

$upgrade_description = 'Add images.md5sum column, for web API uploaded photos';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = 'ALTER TABLE '.IMAGES_TABLE.' add column `md5sum` char(32) default NULL';
pwg_query($query);

$upgrade_description = $query;

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
