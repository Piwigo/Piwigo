<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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
  var $default_plugins = array('LocalFilesEditor', 'language_switch', 'c13y_upgrade', 'admin_multi_view');

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
        if (!empty($crt_db_plugin) or !isset($this->fs_plugins[$plugin_id]))
        {
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
          $errors = $this->perform_action('install', $plugin_id);
          list($crt_db_plugin) = get_db_plugins(null, $plugin_id);
          load_conf_from_db();
        }
        elseif ($crt_db_plugin['state'] == 'active')
        {
          break;
        }
        if (empty($errors) and file_exists($file_to_include))
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
        if (!isset($crt_db_plugin) or $crt_db_plugin['state'] != 'active')
        {
          break;
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
          break;
        }
        if ($crt_db_plugin['state'] == 'active')
        {
          $this->perform_action('deactivate', $plugin_id);
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

      case 'restore':
        $this->perform_action('uninstall', $plugin_id);
        unset($this->db_plugins_by_id[$plugin_id]);
        $errors = $this->perform_action('activate', $plugin_id);
        break;

      case 'delete':
        if (!empty($crt_db_plugin))
        {
          $this->perform_action('uninstall', $plugin_id);
        }
        if (!isset($this->fs_plugins[$plugin_id]))
        {
          break;
        }
        deltree(PHPWG_PLUGINS_PATH . $plugin_id, PHPWG_PLUGINS_PATH . 'trash');
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

  // Retrieve PEM versions
  function get_versions_to_check($version=PHPWG_VERSION)
  {
    global $conf;
    
    $versions_to_check = array();
    $url = PEM_URL . '/api/get_version_list.php?category_id='. $conf['pem_plugins_category'] .'&format=php';
    if (fetchRemote($url, $result) and $pem_versions = @unserialize($result))
    {
      if (!preg_match('/^\d+\.\d+\.\d+$/', $version))
      {
        $version = $pem_versions[0]['name'];
      }
      $branch = get_branch_from_version($version);
      foreach ($pem_versions as $pem_version)
      {
        if (strpos($pem_version['name'], $branch) === 0)
        {
          $versions_to_check[] = $pem_version['id'];
        }
      }
    }
    return $versions_to_check;
  }

  /**
   * Retrieve PEM server datas to $server_plugins
   */
  function get_server_plugins($new=false)
  {
    global $user, $conf;

    $versions_to_check = $this->get_versions_to_check();
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
    $get_data = array(
      'category_id' => $conf['pem_plugins_category'],
      'format' => 'php',
      'last_revision_only' => 'true',
      'version' => implode(',', $versions_to_check),
      'lang' => substr($user['language'], 0, 2),
      'get_nb_downloads' => 'true',
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

  function get_incompatible_plugins($actualize=false)
  {
    if (isset($_SESSION['incompatible_plugins']) and !$actualize
      and $_SESSION['incompatible_plugins']['~~expire~~'] > time())
    {
      return $_SESSION['incompatible_plugins'];
    }

    $_SESSION['incompatible_plugins'] = array('~~expire~~' => time() + 300);

    $versions_to_check = $this->get_versions_to_check();
    if (empty($versions_to_check))
    {
      return false;
    }
    
    global $conf;

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
    $get_data = array(
      'category_id' => $conf['pem_plugins_category'],
      'format' => 'php',
      'version' => implode(',', $versions_to_check),
      'extension_include' => implode(',', $plugins_to_check),
    );

    if (fetchRemote($url, $result, $get_data))
    {
      $pem_plugins = @unserialize($result);
      if (!is_array($pem_plugins))
      {
        return false;
      }

      $server_plugins = array();
      foreach ($pem_plugins as $plugin)
      {
        if (!isset($server_plugins[$plugin['extension_id']]))
        {
          $server_plugins[$plugin['extension_id']] = array();
        }
        $server_plugins[$plugin['extension_id']][] = $plugin['revision_name'];
      }

      foreach ($this->fs_plugins as $plugin_id => $fs_plugin)
      {
        if (isset($fs_plugin['extension'])
          and !in_array($plugin_id, $this->default_plugins)
          and $fs_plugin['version'] != 'auto'
          and (!isset($server_plugins[$fs_plugin['extension']]) or !in_array($fs_plugin['version'], $server_plugins[$fs_plugin['extension']])))
        {
          $_SESSION['incompatible_plugins'][$plugin_id] = $fs_plugin['version'];
        }
      }
      return $_SESSION['incompatible_plugins'];
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
        include_once(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
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
                $old_files[] = 'obsolete.list';
                foreach($old_files as $old_file)
                {
                  $path = $extract_path.'/'.$old_file;
                  if (is_file($path))
                  {
                    @unlink($path);
                  }
                  elseif (is_dir($path))
                  {
                    deltree($path, PHPWG_PLUGINS_PATH . 'trash');
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

  function get_merged_extensions($version=PHPWG_VERSION)
  {
    $file = PHPWG_ROOT_PATH.'install/obsolete_extensions.list';
    $merged_extensions = array();

    if (file_exists($file) and $obsolete_ext = file($file, FILE_IGNORE_NEW_LINES) and !empty($obsolete_ext))
    {
      foreach ($obsolete_ext as $ext)
      {
        if (preg_match('/^(\d+) ?: ?(.*?)$/', $ext, $matches))
        {
          $merged_extensions[$matches[1]] = $matches[2];
        }
      }
    }
    return $merged_extensions;
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