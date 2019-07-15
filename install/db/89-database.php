<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined("PHPWG_ROOT_PATH"))
{
  die('Hacking attempt!');
}

$upgrade_description = 'Admins can activate/deactivate user customization.';

$query = '
INSERT INTO '.CONFIG_TABLE.' (param,value,comment)
  VALUES
    ("allow_user_customization","true","allow users to customize their gallery?")
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>