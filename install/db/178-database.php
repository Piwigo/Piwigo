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

// Add line of conf to help with the display of filters and default filters in the gallery
conf_update_param(
  'filters_views',
  'a:14:{s:5:"words";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:1;}s:4:"tags";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:9:"post_date";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:13:"creation_date";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:1;}s:5:"album";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:1;}s:6:"author";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:8:"added_by";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:9:"file_type";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:5:"ratio";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:6:"rating";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:9:"file_size";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:6:"height";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:5:"width";a:2:{s:6:"access";s:9:"everybody";s:7:"default";b:0;}s:17:"last_filters_conf";b:1;}'
);

echo "\n".$upgrade_description."\n";

?>
