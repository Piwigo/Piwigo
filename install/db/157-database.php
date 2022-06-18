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

$upgrade_description = 'add config parameters to display smart app banner';

conf_update_param('show_mobile_app_banner_in_admin', true);
conf_update_param('show_mobile_app_banner_in_gallery', false);

echo "\n".$upgrade_description."\n";

?>
