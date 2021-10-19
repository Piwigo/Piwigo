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

$upgrade_description = 'change activity.performed_by for logout';

$query = '
UPDATE '.PREFIX_TABLE.'activity
  SET performed_by = object_id
  WHERE action = \'logout\'
;';
pwg_query($query);

echo "\n".$upgrade_description."\n";

?>
