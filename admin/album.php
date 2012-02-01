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

$admin_album_base_url = get_root_url().'admin.php?page=album-'.$_GET['cat_id'];

$query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$_GET['cat_id'].'
;';
$category = pwg_db_fetch_assoc(pwg_query($query));

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
$tabsheet->add('properties', l10n('Properties'), $admin_album_base_url.'-properties');
$tabsheet->add('sort_order', l10n('Manage photo ranks'), $admin_album_base_url.'-sort_order');

if ('private' == $category['status'])
{
  $tabsheet->add('permissions', l10n('Permissions'), $admin_album_base_url.'-permissions');
}

$tabsheet->select($page['tab']);
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// | Load the tab                                                          |
// +-----------------------------------------------------------------------+

if ('properties' == $page['tab'])
{
  include(PHPWG_ROOT_PATH.'admin/cat_modify.php');
}
elseif ('sort_order' == $page['tab'])
{
  include(PHPWG_ROOT_PATH.'admin/element_set_ranks.php');
}
elseif ('permissions' == $page['tab'])
{
  $_GET['cat'] = $_GET['cat_id'];
  include(PHPWG_ROOT_PATH.'admin/cat_perm.php');
}
else
{
  include(PHPWG_ROOT_PATH.'admin/album_'.$page['tab'].'.php');
}
?>