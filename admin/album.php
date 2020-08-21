<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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

if (!isset($category['id']))
{
  die("unknown album");
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
$tabsheet->set_id('album');
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