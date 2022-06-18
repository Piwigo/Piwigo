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

$upgrade_description = 'add new column to save author_id. 
Guest users names are saved in author column';

$query = '
ALTER TABLE '.PREFIX_TABLE.'comments
  ADD COLUMN author_id smallint(5) DEFAULT NULL
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>
