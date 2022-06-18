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

$upgrade_description = 'Update default template';

// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+

// set yoga/Sylvia as default value for user_infos.template column
$query = '
ALTER TABLE '.PREFIX_TABLE.'user_infos
  CHANGE COLUMN template template varchar(255) NOT NULL default \'yoga/Sylvia\'
;';
pwg_query($query);

echo
"\n"
.'Default template modified to yoga/Sylvia'
."\n"
;
?>
