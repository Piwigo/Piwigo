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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// | Basic checks                                                          |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

check_input_parameter('cat_id', $_GET, false, PATTERN_ID);
check_input_parameter('image_id', $_GET, false, PATTERN_ID);

$admin_photo_base_url = get_root_url().'admin.php?page=photo-'.$_GET['image_id'];

if (isset($_GET['cat_id']))
{
  $query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_GET['cat_id'].'
;';
  $category = pwg_db_fetch_assoc(pwg_query($query));
}

// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$page['tab'] = 'properties';

if (isset($_GET['tab']))
{
  $page['tab'] = $_GET['tab'];
}

$tabsheet = new tabsheet();
$tabsheet->set_id('photo');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// | Load the tab                                                          |
// +-----------------------------------------------------------------------+

if ('properties' == $page['tab'])
{
  include(PHPWG_ROOT_PATH.'admin/picture_modify.php');
}
elseif ('coi' == $page['tab'])
{
  include(PHPWG_ROOT_PATH.'admin/picture_coi.php');
}
else
{
  include(PHPWG_ROOT_PATH.'admin/photo_'.$page['tab'].'.php');
}
?>