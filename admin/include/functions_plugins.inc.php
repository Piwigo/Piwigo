<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

/**
 * Retrieves an url for a plugin page.
 * @param string file - php script full name
 */
function get_admin_plugin_menu_link($file)
{
  global $page;
  $real_file = realpath($file);
  $url = get_root_url().'admin.php?page=plugin';
  if (false!==$real_file)
  {
    $real_plugin_path = rtrim(realpath(PHPWG_PLUGINS_PATH), '\\/');
    $file = substr($real_file, strlen($real_plugin_path)+1);
    $file = str_replace('\\', '/', $file);//Windows
    $url .= '&amp;section='.urlencode($file);
  }
  else if (isset($page['errors']))
  {
    array_push($page['errors'], 'PLUGIN ERROR: "'.$file.'" is not a valid file');
  }
  return $url;
}
?>