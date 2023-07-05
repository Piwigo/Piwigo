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

$upgrade_description = 'Reset derivative configuration to include XXS and XS sizes.';

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

clear_derivative_cache();

$derivative_conf_file = PHPWG_ROOT_PATH.$conf['data_location'].'derivatives.dat';
if (is_file($derivative_conf_file))
{
  unlink($derivative_conf_file);
}

conf_update_param('derivatives', '');

echo
"\n"
. $upgrade_description
."\n"
;
?>