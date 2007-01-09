<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);

$section = explode('~', $_GET['section'] );
if (count($section)!=2)
{
  die('Invalid plugin URL');
}

$plugin_id = $section[0];
$check_db_plugin = get_db_plugins('active', $plugin_id );
if (empty($check_db_plugin))
{
  die('Invalid URL - plugin '.$plugin_id.' not active');
}
$section[1]=str_replace('./', '', $section[1]); // no up in dir structure

$filename = PHPWG_PLUGINS_PATH.$plugin_id.'/'.$section[1].'.php';
if (is_file($filename))
{
  include_once($filename);
}
else
{
  die('Missing '.$filename);
}
?>