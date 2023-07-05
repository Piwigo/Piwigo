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

$upgrade_description = 'add the config parameter comments_update_validation';

$query = '
INSERT INTO '.CONFIG_TABLE.'
  (
    param,
    value,
    comment
  )
  VALUES (
    \'comments_update_validation\',
    false,
    \'administrators validate users updated comments before becoming visible\'
   )
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>