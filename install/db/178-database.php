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

$upgrade_description = 'add config parameters to the gallery filters';

// let the $conf['filters_views'] be written in config table when the admin will change settings in administration.
//
// conf_update_param('filters_views', $conf['default_filters_views']);

echo "\n".$upgrade_description."\n";

?>
