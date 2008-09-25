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
load_language('plugin.lang', LOCALEDIT_PATH);
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
  $editarea_options = array(
    'syntax' => 'php',
    'start_highlight' => true,
    'allow_toggle' => false,
    'is_editable' => false,
    'language' => substr($user['language'], 0, 2));

  $file = file_get_contents(PHPWG_ROOT_PATH . $path);
  
  $template->assign(array(
    'DEFAULT_CONTENT' => $file,
    'LOCALEDIT_PATH' => LOCALEDIT_PATH,
    'LOAD_EDITAREA' => isset($conf['LocalFilesEditor']) ? $conf['LocalFilesEditor'] : 'on',
    'EDITAREA_OPTIONS' => $editarea_options));

  $title = $path;
  $page['page_banner'] = '<h1>'.str_replace('/', ' / ', $path).'</h1>';
  $page['body_id'] = 'thePopuphelpPage';

  include(PHPWG_ROOT_PATH.'include/page_header.php');

  $template->pparse('show_default');

  include(PHPWG_ROOT_PATH.'include/page_tail.php');
}

?>