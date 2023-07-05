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

$upgrade_description = 'Add blk_menubar config';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = 'DROP TABLE IF EXISTS '.$prefixeTable.'ws_access';
pwg_query($query);

$upgrade_description = $query;

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
