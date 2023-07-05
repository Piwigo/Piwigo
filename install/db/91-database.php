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

$upgrade_description = 'Remove adviser status.';

// Existing adviser become normal user
$query = "
UPDATE ".USER_INFOS_TABLE."
SET status = 'normal'
WHERE status IN ('webmaster', 'admin')
  AND adviser = 'true'
;";

pwg_query($query);

// Remove adviser column
$query = '
ALTER TABLE '.USER_INFOS_TABLE.'
DROP COLUMN adviser
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>