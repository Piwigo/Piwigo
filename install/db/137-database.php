<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'add ASC keyword to categories image_order field';


$query = '
SELECT id, image_order
  FROM '.CATEGORIES_TABLE.'
  WHERE image_order != ""
;';
$cats = hash_from_query($query, 'id');

foreach ($cats as $id => &$data)
{
  $image_order = explode(',',$data['image_order']);
  foreach ($image_order as &$order)
  {
    if (strpos($order, ' ASC')===false && strpos($order, ' DESC')===false)
    {
      $order.= ' ASC';
    }
  }
  unset($order);
  $data['image_order'] = implode(',',$image_order);
}
unset($data);

mass_updates(CATEGORIES_TABLE,
  array(
    'primary' => array('id'),
    'update' => array('image_order'),
  ),
  $cats
  );
  
  
echo "\n".$upgrade_description."\n";

?>