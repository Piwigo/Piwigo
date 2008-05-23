<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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

$upgrade_description = 'add index on #tags.url_name and #image_tag.tag_id ';



$query = '
ALTER TABLE '.PREFIX_TABLE.'tags ADD INDEX `tags_i1`(`url_name`);
;';
pwg_query($query);


$query = '
ALTER TABLE '.PREFIX_TABLE.'image_tag ADD INDEX `image_tag_i1`(`tag_id`);
;';
pwg_query($query);


// +-----------------------------------------------------------------------+
// |                           End notification                            |
// +-----------------------------------------------------------------------+

echo
"\n"
.'Tables '.PREFIX_TABLE.'tags and '.PREFIX_TABLE.'image_tag updated'."\n"
;
echo $upgrade_description;
?>
