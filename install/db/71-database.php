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

$upgrade_description = 'Delete unnecessary #history_summary.id, #history.year, #history.month, #history.day and #history.hour';

include_once(PHPWG_ROOT_PATH.'include/constants.php');

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

$query = 'ALTER TABLE '.HISTORY_SUMMARY_TABLE.'
DROP PRIMARY KEY,
DROP COLUMN id,
ADD UNIQUE KEY history_summary_ymdh (`year`, `month`, `day`, `hour`)
;';
pwg_query($query);

$query = 'ALTER TABLE '.HISTORY_TABLE.'
DROP COLUMN year,
DROP COLUMN month,
DROP COLUMN day,
DROP COLUMN hour
;';
pwg_query($query);

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;

?>
