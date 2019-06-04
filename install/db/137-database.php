<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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