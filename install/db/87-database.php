<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

if (!defined("PHPWG_ROOT_PATH"))
{
  die('Hacking attempt!');
}

$upgrade_description = 'Add display configuration options.';

$query = '
INSERT INTO '.CONFIG_TABLE.' (param,value,comment)
  VALUES
    ("menubar_filter_icon","true","Display filter icon"),
    ("index_sort_order_input","true","Display image order selection list"),
    ("index_flat_icon","true","Display flat icon"),
    ("index_posted_date_icon","true","Display calendar by posted date"),
    ("index_created_date_icon","true","Display calendar by creation date icon"),
    ("index_slideshow_icon","true","Display slideshow icon"),
    ("picture_metadata_icon","true","Display metadata icon on picture page"),
    ("picture_slideshow_icon","true","Display slideshow icon on picture page"),
    ("picture_favorite_icon","true","Display favorite icon on picture page"),
    ("picture_navigation_icons","true","Display navigation icons on picture page"),
    ("picture_navigation_thumb","true","Display navigation thumbnails on picture page")
;';

pwg_query($query);

echo
"\n"
. $upgrade_description
."\n"
;
?>