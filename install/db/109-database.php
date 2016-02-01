<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
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