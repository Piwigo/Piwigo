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

// see http://piwigo.org/doc/doku.php?id=user_documentation:htaccess_and_hotlink_in_2.4

$upgrade_description = 'add/append htaccess for hotlinks (cancelled, see plugin "Hotlink Compatibility")';


echo
"\n"
. $upgrade_description
."\n"
;
?>