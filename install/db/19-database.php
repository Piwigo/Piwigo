<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

$upgrade_description = '#images.keywords moved to new table #tags';

// +-----------------------------------------------------------------------+
// |                              New tables                               |
// +-----------------------------------------------------------------------+

$query = '
CREATE TABLE '.PREFIX_TABLE.'tags (
  id smallint(5) UNSIGNED NOT NULL auto_increment,
  name varchar(255) BINARY NOT NULL,
  url_name varchar(255) BINARY NOT NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM
;';
pwg_query($query);

$query = '
CREATE TABLE '.PREFIX_TABLE.'image_tag (
  image_id mediumint(8) UNSIGNED NOT NULL,
  tag_id smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (image_id,tag_id)
) TYPE=MyISAM
;';
pwg_query($query);

// +-----------------------------------------------------------------------+
// |                        Move keywords to tags                          |
// +-----------------------------------------------------------------------+

// each tag label is associated to a numeric identifier
$tag_id = array();
// to each tag id (key) a list of image ids (value) is associated
$tag_images = array();

$current_id = 1;

$query = '
SELECT id, keywords
  FROM '.PREFIX_TABLE.'images
  WHERE keywords IS NOT NULL
;';
$result = pwg_query($query);
while ($row = mysql_fetch_array($result))
{
  foreach(preg_split('/[,]+/', $row['keywords']) as $keyword)
  {
    if (!isset($tag_id[$keyword]))
    {
      $tag_id[$keyword] = $current_id++;
    }

    if (!isset($tag_images[ $tag_id[$keyword] ]))
    {
      $tag_images[ $tag_id[$keyword] ] = array();
    }

    array_push($tag_images[ $tag_id[$keyword] ], $row['id']);
  }
}

$datas = array();
foreach ($tag_id as $tag_name => $tag_id)
{
  array_push(
    $datas,
    array(
      'id'       => $tag_id,
      'name'     => $tag_name,
      'url_name' => str2url($tag_name),
      )
    );
}
if (!empty($datas))
mass_inserts(
  PREFIX_TABLE.'tags',
  array_keys($datas[0]),
  $datas
  );

$datas = array();
foreach ($tag_images as $tag_id => $images)
{
  foreach (array_unique($images) as $image_id)
  {
    array_push(
      $datas,
      array(
        'tag_id'   => $tag_id,
        'image_id' => $image_id,
        )
      );
  }
}

if (!empty($datas))
mass_inserts(
  PREFIX_TABLE.'image_tag',
  array_keys($datas[0]),
  $datas
  );

// +-----------------------------------------------------------------------+
// |                         Delete images.keywords                        |
// +-----------------------------------------------------------------------+

$query = '
ALTER TABLE '.PREFIX_TABLE.'images DROP COLUMN keywords
;';
pwg_query($query);

// +-----------------------------------------------------------------------+
// |                           End notification                            |
// +-----------------------------------------------------------------------+

echo
"\n"
.'Table '.PREFIX_TABLE.'tags created and filled'."\n"
.'Table '.PREFIX_TABLE.'image_tag created and filled'."\n"
.'Column '.PREFIX_TABLE.'images.keywords dropped'."\n"
;
?>
