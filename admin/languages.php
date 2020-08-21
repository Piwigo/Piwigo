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

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=languages';

if (isset($_GET['tab']))
{
  check_input_parameter('tab', $_GET, false, '/^(installed|update|new)$/');
  $page['tab'] = $_GET['tab'];
}
else
{
  $page['tab'] = 'installed';
}

$tabsheet = new tabsheet();
$tabsheet->set_id('languages');
$tabsheet->select($page['tab']);
$tabsheet->assign();

if ($page['tab'] == 'update')
  include(PHPWG_ROOT_PATH.'admin/updates_ext.php');
else
  include(PHPWG_ROOT_PATH.'admin/languages_'.$page['tab'].'.php');

?>