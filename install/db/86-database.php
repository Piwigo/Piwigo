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

$upgrade_description = 'Automatically activate core themes.';

include_once(PHPWG_ROOT_PATH . 'admin/include/functions_install.inc.php');
activate_core_themes();

echo
"\n"
. $upgrade_description
."\n"
;
?>