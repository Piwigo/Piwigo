<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
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

/* Returns an array of plugins defined in the plugin directory
*/
function get_fs_plugins()
{
  $plugins = array();

  $dir = opendir(PHPWG_PLUGINS_PATH);
  while ($file = readdir($dir))
  {
    if ($file!='.' and $file!='..')
    {
      $path = PHPWG_PLUGINS_PATH.$file;
      if (is_dir($path) and !is_link($path)
          and preg_match('/^[a-zA-Z0-9-_]+$/', $file )
          and file_exists($path.'/main.inc.php')
          )
      {
        $plugin = array(
            'name'=>$file,
            'version'=>'0',
            'uri'=>'',
            'description'=>'',
            'author'=>'',
          );
        $plg_data = implode( '', file($path.'/main.inc.php') );

        if ( preg_match("|Plugin Name: (.*)|", $plg_data, $val) )
        {
          $plugin['name'] = trim( $val[1] );
        }
        if (preg_match("|Version: (.*)|", $plg_data, $val))
        {
          $plugin['version'] = trim($val[1]);
        }
        if ( preg_match("|Plugin URI: (.*)|", $plg_data, $val) )
        {
          $plugin['uri'] = trim($val[1]);
        }
        if ( preg_match("|Description: (.*)|", $plg_data, $val) )
        {
          $plugin['description'] = trim($val[1]);
        }
        if ( preg_match("|Author: (.*)|", $plg_data, $val) )
        {
          $plugin['author'] = trim($val[1]);
        }
        if ( preg_match("|Author URI: (.*)|", $plg_data, $val) )
        {
          $plugin['author uri'] = trim($val[1]);
        }
        // IMPORTANT SECURITY !
        $plugin = array_map('htmlspecialchars', $plugin);
        $plugins[$file] = $plugin;
      }
    }
  }
  closedir($dir);
  return $plugins;
}

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
    $real_plugin_path = realpath(PHPWG_PLUGINS_PATH);
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