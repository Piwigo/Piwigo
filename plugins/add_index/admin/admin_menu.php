<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// | Copyright (C) 2007 Ruben ARNAUD - team@phpwebgallery.net              |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-07-18 23:38:54 +0200 (mar., 18 juil. 2006) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1481 $
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

if ((!defined('PHPWG_ROOT_PATH')) or (!(defined('IN_ADMIN') and IN_ADMIN)))
{
  die('Hacking attempt!');
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | Main                                                                  |
// +-----------------------------------------------------------------------+
global $template, $conf;

// +-----------------------------------------------------------------------+
// | template initialization                                               |
// +-----------------------------------------------------------------------+
$template->set_filenames(array('plugin_admin_content' => dirname(__FILE__).'/admin_menu.tpl'));

/*
if ( isset($_POST['eventTracer_filters']) )
{
  $v = $_POST['eventTracer_filters'];
  $v = str_replace( "\r\n", "\n", $v );
  $v = str_replace( "\n\n", "\n", $v );
  $v = stripslashes($v);
  if (!empty($v))
    $this->my_config['filters'] = explode("\n", $v);
  else
    $this->my_config['filters'] = array();
  $this->my_config['show_args'] = isset($_POST['eventTracer_show_args']);
  $this->save_config();
  global $page;
  array_push($page['infos'], 'event tracer options saved');
}
$template->assign_var('EVENT_TRACER_FILTERS', implode("\n", $this->my_config['filters'] ) );
$template->assign_var('EVENT_TRACER_SHOW_ARGS', $this->my_config['show_args'] ? 'checked="checked"' : '' );*/
$template->assign_var('filename', $conf['add_index_filename']);
$template->assign_var('source_directory_path', $conf['add_index_source_directory_path']);
$template->assign_var('F_ACTION', $my_url);

// +-----------------------------------------------------------------------+
// | Sending html code                                                     |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('PLUGIN_ADMIN_CONTENT', 'plugin_admin_content');

?>