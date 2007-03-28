<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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


/**
 * Upgrade from 1.3.0 to 1.3.1
 */

if (!defined('PHPWG_ROOT_PATH'))
{
  die ('This page cannot be loaded directly, load upgrade.php');
}
else
{
  if (!defined('PHPWG_IN_UPGRADE') or !PHPWG_IN_UPGRADE)
  {
    die ('Hacking attempt!');
  }
}

$queries = array(
  "
ALTER TABLE phpwebgallery_categories
  ADD COLUMN uppercats varchar(255) NOT NULL default ''
;",

  "
CREATE TABLE phpwebgallery_user_category (
  user_id smallint(5) unsigned NOT NULL default '0'
)
;",

  "
ALTER TABLE phpwebgallery_categories
  ADD INDEX id (id)
;",

  "
ALTER TABLE phpwebgallery_categories
  ADD INDEX id_uppercat (id_uppercat)
;",

  "
ALTER TABLE phpwebgallery_image_category
  ADD INDEX category_id (category_id)
;",

  "
ALTER TABLE phpwebgallery_image_category
  ADD INDEX image_id (image_id)
;",
  );

foreach ($queries as $query)
{
  $query = str_replace('phpwebgallery_', PREFIX_TABLE, $query);
  pwg_query($query);
}
// filling the new column categories.uppercats
$id_uppercats = array();

$query = '
SELECT id, id_uppercat
  FROM '.CATEGORIES_TABLE.'
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  if (!isset($row['id_uppercat']) or $row['id_uppercat'] == '')
  {
    $row['id_uppercat'] = 'NULL';
  }
  $id_uppercats[$row['id']] = $row['id_uppercat'];
}

$datas = array();

foreach (array_keys($id_uppercats) as $id)
{
  $data = array();
  $data['id'] = $id;
  $uppercats = array();
  
  array_push($uppercats, $id);
  while (isset($id_uppercats[$id]) and $id_uppercats[$id] != 'NULL')
  {
    array_push($uppercats, $id_uppercats[$id]);
    $id = $id_uppercats[$id];
  }
  $data['uppercats'] = implode(',', array_reverse($uppercats));

  array_push($datas, $data);
}

mass_updates(
  CATEGORIES_TABLE,
  array(
    'primary' => array('id'),
    'update' => array('uppercats')
    ),
  $datas
  );

// now we upgrade from 1.3.1 to 1.6.0
include_once(PHPWG_ROOT_PATH.'install/upgrade_1.3.1.php');
?>