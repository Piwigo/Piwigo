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

define('PHPWG_ROOT_PATH', '../../');
include_once(PHPWG_ROOT_PATH . 'include/common.inc.php');
include_once(LOCALEDIT_PATH.'functions.inc.php');
check_status(ACCESS_ADMINISTRATOR);

if (isset($_GET['file']))
{
  $path = $_GET['file'];
  if (!is_admin() or (!substr_count($path, 'config_default.inc.php') and !substr_count($path, '.lang.php')))
  {
  	die('Hacking attempt!');
  }
    
  $template->set_filename('show_default', dirname(__FILE__) . '/show_default.tpl');
  
  // Editarea
  if (!isset($conf['editarea_options']) or $conf['editarea_options'] !== false)
  {
    $editarea = array(
      'syntax' => 'php',
      'start_highlight' => true,
      'is_editable' => false,
      'language' => substr($user['language'], 0, 2));

    $template->assign('editarea', array(
      'URL' => LOCALEDIT_PATH . 'editarea/edit_area_full.js',
      'OPTIONS' => $editarea));
  }

  $file = file_get_contents(PHPWG_ROOT_PATH . $path);

  $template->assign(array('DEFAULT_CONTENT' => $file));

  $title = $path;
  $page['page_banner'] = '<h1>'.str_replace('/', ' / ', $path).'</h1>';
  $page['body_id'] = 'thePopuphelpPage';

  include(PHPWG_ROOT_PATH.'include/page_header.php');

  $template->pparse('show_default');

  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}

?>