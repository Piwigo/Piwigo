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

define('PHPWG_ROOT_PATH', '../../');
define('IN_ADMIN', true);
include_once(PHPWG_ROOT_PATH . 'include/common.inc.php');
include_once(LOCALEDIT_PATH.'include/functions.inc.php');
load_language('plugin.lang', LOCALEDIT_PATH);
check_status(ACCESS_ADMINISTRATOR);

if (isset($_GET['file']))
{
  $path = $_GET['file'];
  if (!is_admin() or (!substr_count($path, 'config_default.inc.php') and !substr_count($path, '.lang.php')))
  {
  	die('Hacking attempt!');
  }
    
  $template->set_filename('show_default', dirname(__FILE__) . '/template/show_default.tpl');
  
  $file = file_get_contents(PHPWG_ROOT_PATH . $path);
  $title = str_replace('/', ' / ', $path);

  $template->assign(array(
    'TITLE' => $title,
    'DEFAULT_CONTENT' => $file,
    )
  );

  $page['body_id'] = 'thePopuphelpPage';

  include(PHPWG_ROOT_PATH.'include/page_header.php');

  $template->pparse('show_default');

  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}

?>