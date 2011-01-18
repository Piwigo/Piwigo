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

class plugins
{
  var $fs_plugins = array();
  var $db_plugins_by_id = array();
  var $server_plugins = array();

  /**
   * Initialize $fs_plugins and $db_plugins_by_id
  */
  function plugins()
  {
    $this->get_fs_plugins();

    foreach (get_db_plugins() as $db_plugin)
    {
      $this->db_plugins_by_id[$db_plugin['id']] = $db_plugin;
    }
  }

  /**
   * Set tabsheet for plugins pages.
   * @param string selected page.
   */
  function set_tabsheet($selected)
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

    $link = get_root_url().'admin.php?page=';

    $tabsheet = new tabsheet();
    $tabsheet->add('plugins_list', l10n('Plugin list'), $link.'plugins_list');
    $tabsheet->add('plugins_update', l10n('Check for updates'), $link.'plugins_update');
    $tabsheet->add('plugins_new', l10n('Other plugins'), $link.'plugins_new');
    $tabsheet->select($selected);
    $tabsheet->assign();
  }

 /**
   * Perform requested actions
  *  @param string - action
  * @param string - plugin id
  * @param array - errors
  */
  function perform_action($action, $plugin_id)
  {
    if (isset($this->db_plugins_by_id[$plugin_id]))
    {
      $crt_db_plugin = $this->db_plugins_by_id[$plugin_id];
    }
    $file_to_include = PHPWG_PLUGINS_PATH . $plugin_id . '/maintain.inc.php';

    $errors = array();

    switch ($action)
    {
      case 'install':
        if (!empty($crt_db_plugin))
        {
          array_push($errors, 'CANNOT INSTALL - ALREADY INSTALLED');
          break;
        }
        if (!isset($this->fs_plugins[$plugin_id]))
        {
          array_push($errors, 'CANNOT INSTALL - NO SUCH PLUGIN');
          break;
        }
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_install'))
          {
            plugin_install($plugin_id, $this->fs_plugins[$plugin_id]['version'], $errors);
          }
        }
        if (empty($errors))
        {
          $query = '
INSERT INTO ' . PLUGINS_TABLE . ' (id,version) VALUES (\''
. $plugin_id . '\',\'' . $this->fs_plugins[$plugin_id]['version'] . '\'
)';
          pwg_query($query);
        }
        break;

      case 'activate':
        if (!isset($crt_db_plugin))
        {
          array_push($errors, 'CANNOT ACTIVATE - NOT INSTALLED');
          break;
        }
        if ($crt_db_plugin['state'] != 'inactive')
        {
          array_push($errors, 'invalid current state ' . $crt_db_plugin['state']);
          break;
        }
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_activate'))
          {
            plugin_activate($plugin_id, $crt_db_plugin['version'], $errors);
          }
        }
        if (empty($errors))
        {
          $query = '
UPDATE ' . PLUGINS_TABLE . '
SET state=\'active\', version=\''.$this->fs_plugins[$plugin_id]['version'].'\'
WHERE id=\'' . $plugin_id . '\'';
          pwg_query($query);
        }
        break;

      case 'deactivate':
        if (!isset($crt_db_plugin))
        {
          die ('CANNOT DEACTIVATE - NOT INSTALLED');
        }
        if ($crt_db_plugin['state'] != 'active')
        {
          die('invalid current state ' . $crt_db_plugin['state']);
        }
        $query = '
UPDATE ' . PLUGINS_TABLE . ' SET state=\'inactive\' WHERE id=\'' . $plugin_id . '\'';
        pwg_query($query);
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_deactivate'))
          {
            plugin_deactivate($plugin_id);
          }
        }
        break;

      case 'uninstall':
        if (!isset($crt_db_plugin))
        {
          die ('CANNOT UNINSTALL - NOT INSTALLED');
        }
        $query = '
DELETE FROM ' . PLUGINS_TABLE . ' WHERE id=\'' . $plugin_id . '\'';
        pwg_query($query);
        if (file_exists($file_to_include))
        {
          include_once($file_to_include);
          if (function_exists('plugin_uninstall'))
          {
            plugin_uninstall($plugin_id);
          }
        }
        break;

      case 'delete':
        if (!empty($crt_db_plugin))
        {
          array_push($errors, 'CANNOT DELETE - PLUGIN IS INSTALLED');
          break;
        }
        if (!isset($this->fs_plugins[$plugin_id]))
        {
          array_push($errors, 'CANNOT DELETE - NO SUCH PLUGIN');
          break;
        }
        if (!$this->deltree(PHPWG_PLUGINS_PATH . $plugin_id))
        {
          $this->send_to_trash(PHPWG_PLUGINS_PATH . $plugin_id);
        }
        break;
    }
    return $errors;
  }

  /**
  *  Get plugins defined in the plugin directory
  */  
  function get_fs_plugins()
  {
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
          if ($desc = load_language('description.txt', $path.'/', array('return' => true)))
          {
            $plugin['description'] = trim($desc);
          }
          elseif ( preg_match("|Description: (.*)|", $plg_data, $val) )
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
          if (!empty($plugin['uri']) and strpos($plugin['uri'] , 'extension_view.php?eid='))
          {
            list( , $extension) = explode('extension_view.php?eid=', $plugin['uri']);
            if (is_numeric($extension)) $plugin['extension'] = $extension;
          }
          // IMPORTANT SECURITY !
          $plugin = array_map('htmlspecialchars', $plugin);
          $this->fs_plugins[$file] = $plugin;
        }
      }
    }
    closedir($dir);
  }

  /**
   * Sort fs_plugins
   */
  function sort_fs_plugins($order='name')
  {
    switch ($order)
    {
      case 'name':
        uasort($this->fs_plugins, 'name_compare');
        break;
      case 'status':
        $this->sort_plugins_by_state();
        break;
      case 'author':
        uasort($this->fs_plugins, array($this, 'plugin_author_compare'));
        break;
      case 'id':
        uksort($this->fs_plugins, 'strcasecmp');
        break;
    }
  }

  /**
   * Retrieve PEM server datas to $server_plugins
   */
  function get_server_plugins($new=false)
  {
    global $user;

    $get_data = array(
      'category_id' => 12,
      'format' => 'php',
    );

    // Retrieve PEM versions
    $version = PHPWG_VERSION;
    $versions_to_check = array();
    $url = PEM_URL . '/api/get_version_list.php';
    if (fetchRemote($url, $result, $get_data) and $pem_versions = @unserialize($result))
    {
      if (!preg_match('/^\d+\.\d+\.\d+/', $version))
      {
        $version = $pem_versions[0]['name'];
      }
      $branch = substr($version, 0, strrpos($version, '.'));
      foreach ($pem_versions as $pem_version)
      {
        if (strpos($pem_version['name'], $branch) === 0)
        {
          $versions_to_check[] = $pem_version['id'];
        }
      }
    }
    if (empty($versions_to_check))
    {
      return false;
    }

    // Plugins to check
    $plugins_to_check = array();
    foreach($this->fs_plugins as $fs_plugin)
    {
      if (isset($fs_plugin['extension']))
      {
        $plugins_to_check[] = $fs_plugin['extension'];
      }
    }

    // Retrieve PEM plugins infos
    $url = PEM_URL . '/api/get_revision_list.php';
    $get_data = array_merge($get_data, array(
      'last_revision_only' => 'true',
      'version' => implode(',', $versions_to_check),
      'lang' => substr($user['language'], 0, 2),
      'get_nb_downloads' => 'true',
      )
    );

    if (!empty($plugins_to_check))
    {
      if ($new)
      {
        $get_data['extension_exclude'] = implode(',', $plugins_to_check);
      }
      else
      {
        $get_data['extension_include'] = implode(',', $plugins_to_check);
      }
    }
    if (fetchRemote($url, $result, $get_data))
    {
      $pem_plugins = @unserialize($result);
      if (!is_array($pem_plugins))
      {
        return false;
      }
      foreach ($pem_plugins as $plugin)
      {
        $this->server_plugins[$plugin['extension_id']] = $plugin;
      }
      return true;
    }
    return false;
  }
  
  /**
   * Sort $server_plugins
   */
  function sort_server_plugins($order='date')
  {
    switch ($order)
    {
      case 'date':
        krsort($this->server_plugins);
        break;
      case 'revision':
        usort($this->server_plugins, array($this, 'extension_revision_compare'));
        break;
      case 'name':
        uasort($this->server_plugins, array($this, 'extension_name_compare'));
        break;
      case 'author':
        uasort($this->server_plugins, array($this, 'extension_author_compare'));
        break;
      case 'downloads':
        usort($this->server_plugins, array($this, 'extension_downloads_compare'));
        break;
    }
  }

  /**
   * Extract plugin files from archive
   * @param string - install or upgrade
   *  @param string - archive URL
    * @param string - plugin id or extension id
   */
  function extract_plugin_files($action, $revision, $dest)
  {
    if ($archive = tempnam( PHPWG_PLUGINS_PATH, 'zip'))
    {
      $url = PEM_URL . '/download.php';
      $get_data = array(
        'rid' => $revision,
        'origin' => 'piwigo_'.$action,
      );

      if ($handle = @fopen($archive, 'wb') and fetchRemote($url, $handle, $get_data))
      {
        fclose($handle);
        include(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
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
              $extract_path = PHPWG_PLUGINS_PATH . $dest;
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
              if (file_exists($extract_path.'/obsolete.list')
                and $old_files = file($extract_path.'/obsolete.list', FILE_IGNORE_NEW_LINES)
                and !empty($old_files))
              {
                array_push($old_files, 'obsolete.list');
                foreach($old_files as $old_file)
                {
                  $path = $extract_path.'/'.$old_file;
                  if (is_file($path))
                  {
                    @unlink($path);
                  }
                  elseif (is_dir($path))
                  {
                    if (!$this->deltree($path))
                    {
                      $this->send_to_trash($path);
                    }
                  }
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
            $this->deltree($pathfile);
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
  function plugin_version_compare($a, $b)
  {
    if (strtolower($a) == 'auto') return false;

    $pattern = array('/([a-z])/ei', '/\.+/', '/\.\Z|\A\./');
    $replacement = array( "'.'.intval('\\1', 36).'.'", '.', '');

    $array = preg_replace($pattern, $replacement, array($a, $b));

    return version_compare($array[0], $array[1], '>=');
  }

  function extension_revision_compare($a, $b)
  {
    if ($a['revision_date'] < $b['revision_date']) return 1;
    else return -1;
  }

  function extension_name_compare($a, $b)
  {
    return strcmp(strtolower($a['extension_name']), strtolower($b['extension_name']));
  }

  function extension_author_compare($a, $b)
  {
    $r = strcasecmp($a['author_name'], $b['author_name']);
    if ($r == 0) return $this->extension_name_compare($a, $b);
    else return $r;
  }

  function plugin_author_compare($a, $b)
  {
    $r = strcasecmp($a['author'], $b['author']);
    if ($r == 0) return name_compare($a, $b);
    else return $r;
  }

  function extension_downloads_compare($a, $b)
  {
    if ($a['extension_nb_downloads'] < $b['extension_nb_downloads']) return 1;
    else return -1;
  }

  function sort_plugins_by_state()
  {
    uasort($this->fs_plugins, 'name_compare');

    $active_plugins = array();
    $inactive_plugins = array();
    $not_installed = array();

    foreach($this->fs_plugins as $plugin_id => $plugin)
    {
      if (isset($this->db_plugins_by_id[$plugin_id]))
      {
        $this->db_plugins_by_id[$plugin_id]['state'] == 'active' ?
          $active_plugins[$plugin_id] = $plugin : $inactive_plugins[$plugin_id] = $plugin;
      }
      else
      {
        $not_installed[$plugin_id] = $plugin;
      }
    }
    $this->fs_plugins = $active_plugins + $inactive_plugins + $not_installed;
  }
}
?>