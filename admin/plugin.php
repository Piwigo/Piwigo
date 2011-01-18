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

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
check_status(ACCESS_ADMINISTRATOR);

$sections = explode('/', $_GET['section'] );
for ($i=0; $i<count($sections); $i++)
{
  if (empty($sections[$i]) or $sections[$i]=='..')
  {
    unset($sections[$i]);
    $i--;
  }
}

if (count($sections)<2)
{
  die('Invalid plugin URL');
}

$plugin_id = $sections[0];
if ( !isset($pwg_loaded_plugins[$plugin_id]) )
{
  die('Invalid URL - plugin '.$plugin_id.' not active');
}

$filename = PHPWG_PLUGINS_PATH.implode('/', $sections);
if (is_file($filename))
{
  include_once($filename);
}
else
{
  die('Missing file '.$filename);
}
?>