<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'derivatives: new organization of "upload" and "galleries" directories';

$query = '
SELECT
    id,
    path,
    tn_ext,
    has_high,
    high_filesize,
    high_width,
    high_height
  FROM '.IMAGES_TABLE.'
;';
$result = pwg_query($query);
$starttime = get_moment();

$updates = array();

while ($row = pwg_db_fetch_assoc($result))
{
  if ('true' == $row['has_high'])
  {
    $high_path = dirname($row['path']).'/pwg_high/'.basename($row['path']);
    rename($high_path, $row['path']);

    array_push(
      $updates,
      array(
        'id' => $row['id'],
        'width' => $row['high_width'],
        'height' => $row['high_height'],
        'filesize' => $row['high_filesize'],
        )
      );
  }
}

if (count($updates) > 0)
{
  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('width', 'height', 'filesize'),
      ),
    $updates
    );
}

echo
"\n"
. $upgrade_description.sprintf(' (execution in %.3fs)', (get_moment() - $starttime))
."\n"
;
?>