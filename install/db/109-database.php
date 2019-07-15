<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

$upgrade_description = 'Rename #images.average_rate to ratingscore.';
include_once(PHPWG_ROOT_PATH.'include/constants.php');

if ('mysql' == $conf['dblayer'])
  $q = 'ALTER TABLE '.IMAGES_TABLE.' CHANGE average_rate rating_score float(5,2) unsigned default NULL';
else
  $q = 'ALTER TABLE '.IMAGES_TABLE.' RENAME average_rate TO rating_score';
pwg_query($q);

$q="UPDATE ".CATEGORIES_TABLE." SET image_order=REPLACE(image_order, 'average_rate', 'rating_score')";
pwg_query($q);

$q="UPDATE ".CONFIG_TABLE." SET value=REPLACE(value, 'average_rate', 'rating_score')
WHERE param IN ('picture_informations', 'order_by', 'order_by_inside_category')";
pwg_query($q);
?>