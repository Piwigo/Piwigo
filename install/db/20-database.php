<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$upgrade_description =
  '#image_category.is_storage replaced by #image.storage_category_id';

// +-----------------------------------------------------------------------+
// |                              New column                               |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.PREFIX_TABLE.'images
  ADD storage_category_id smallint(5) unsigned default NULL
;';
pwg_query($query);

$query = '
SELECT category_id, image_id
  FROM '.PREFIX_TABLE.'image_category
  WHERE is_storage = \'true\'
;';
$result = pwg_query($query);

$datas = array();
while ($row = pwg_db_fetch_assoc($result))
{
  array_push(
    $datas,
    array(
      'id' => $row['image_id'],
      'storage_category_id' => $row['category_id'],
      )
    );
}
mass_updates(
  PREFIX_TABLE.'images',
  array(
    'primary' => array('id'),
    'update' => array('storage_category_id'),
    ),
  $datas
  );

// +-----------------------------------------------------------------------+
// |                         Delete obsolete column                        |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.PREFIX_TABLE.'image_category DROP COLUMN is_storage
;';
pwg_query($query);

// +-----------------------------------------------------------------------+
// |                           End notification                            |
// +-----------------------------------------------------------------------+

echo
"\n"
.'Column '.PREFIX_TABLE.'image_category'
.' replaced by '.PREFIX_TABLE.'images.storage_category_id'."\n"
;
?>
