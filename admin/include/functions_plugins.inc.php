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

/**
 * Sort plugins by status
 */
function sort_plugins_by_state($plugins, $db_plugins_by_id)
{
  $active_plugins = array();
  $inactive_plugins = array();
  $not_installed = array();

  foreach($plugins as $plugin_id => $plugin)
  {
    if (isset($db_plugins_by_id[$plugin_id]))
    {
      $db_plugins_by_id[$plugin_id]['state'] == 'active' ?
        $active_plugins[$plugin_id] = $plugin : $inactive_plugins[$plugin_id] = $plugin;
    }
    else
    {
      $not_installed[$plugin_id] = $plugin;
    }
  }
  return $active_plugins + $inactive_plugins + $not_installed;
}


/**
 * Retrieve PEM server datas
 * @param bool (true for retrieve new extensions)
 */
function check_server_plugins($newext=false)
{
  global $fs_plugins;
  
  foreach($fs_plugins as $plugin_id => $fs_plugin)
  {
    if (!empty($fs_plugin['uri']) and strpos($fs_plugin['uri'] , 'extension_view.php?eid='))
    {
      list( , $extension) = explode('extension_view.php?eid=', $fs_plugin['uri']);
      if (!is_numeric($extension)) continue;
      $plugins_to_check[] = $extension;
      $fs_plugins[$plugin_id]['extension'] = $extension;
    }
  }
  
  $url = PEM_URL . '/uptodate.php?version=' . rawurlencode(PHPWG_VERSION) . '&extensions=' . implode(',', $plugins_to_check);
  $url .= $newext ? '&newext=Plugin' : '';

  if (!empty($plugins_to_check) and $source = @file_get_contents($url))
  {
    return @unserialize($source);
  }
  return false;
}


/**
 * Extract plugin files from archive
 * @param string - install or upgrade
 *  @param string - archive URL
  * @param string - destination path
 */
function extract_plugin_files($action, $source, $dest)
{
  global $archive;
  if ($archive = tempnam( PHPWG_PLUGINS_PATH, 'zip'))
  {
    if (@copy(PEM_URL . str_replace(' ', '%20', $source), $archive))
    {
      $zip = new PclZip($archive);
      if ($list = $zip->listContent())
      {
        foreach ($list as $file)
        {
          // we search main.inc.php in archive
          if (basename($file['filename']) == 'main.inc.php'
            and (!isset($main_filepath)
            or strlen($file['filename']) < strlen($main_filepath)))
          {
            $main_filepath = $file['filename'];
          }
        }
        if (isset($main_filepath))
        {
          $root = dirname($main_filepath); // main.inc.php path in archive
          if ($action == 'upgrade')
          {          
            $extract_path = PHPWG_PLUGINS_PATH.$dest;
          }
          else
          {
            $extract_path = PHPWG_PLUGINS_PATH 
                . ($root == '.' ? 'extension_' . $dest : basename($root));
          }
          if($result = $zip->extract(PCLZIP_OPT_PATH, $extract_path,
                                     PCLZIP_OPT_REMOVE_PATH, $root,
                                     PCLZIP_OPT_REPLACE_NEWER))
          {
            foreach ($result as $file)
            {
              if ($file['stored_filename'] == $main_filepath)
              {
                $status = $file['status'];
                break;
              }
            }
          }
          else $status = 'extract_error';
        }
        else $status = 'archive_error';
      }
      else $status = 'archive_error';
    }
    else $status = 'dl_archive_error';
  }
  else $status = 'temp_path_error';
  
  @unlink($archive);
  return $status;
}


/**
 * delete $path directory
 * @param string - path
 */
function deltree($path)
{
  if (is_dir($path))
  {
    $fh = opendir($path);
    while ($file = readdir($fh))
    {
      if ($file != '.' and $file != '..')
      {
        $pathfile = $path . '/' . $file;
        if (is_dir($pathfile))
        {
          deltree($pathfile);
        }
        else
        {
          @unlink($pathfile);
        }
      }
    }
    closedir($fh);
    return @rmdir($path);
  }
}


/**
 * send $path to trash directory
  * @param string - path
 */
function send_to_trash($path)
{
  $trash_path = PHPWG_PLUGINS_PATH . 'trash';
  if (!is_dir($trash_path))
  {
    @mkdir($trash_path);
    $file = @fopen($trash_path . '/.htaccess', 'w');
    @fwrite($file, 'deny from all');
    @fclose($file);
  }
  while ($r = $trash_path . '/' . md5(uniqid(rand(), true)))
  {
    if (!is_dir($r))
    {
      @rename($path, $r);
      break;
    }
  }
}


/**
 * Sort functions
 */
function extension_name_compare($a, $b)
{
  return strcmp(strtolower($a['ext_name']), strtolower($b['ext_name']));
}
function extension_author_compare($a, $b)
{
  $r = strcmp(strtolower($a['author']), strtolower($b['author']));
  if ($r == 0) return extension_name_compare($a, $b);
  else return $r;
}

?>