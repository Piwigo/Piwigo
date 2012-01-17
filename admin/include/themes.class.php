<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

class themes
{
  var $fs_themes = array();
  var $db_themes_by_id = array();
  var $server_themes = array();

  /**
   * Initialize $fs_themes and $db_themes_by_id
  */
  function themes()
  {
    $this->get_fs_themes();

    foreach ($this->get_db_themes() as $db_theme)
    {
      $this->db_themes_by_id[$db_theme['id']] = $db_theme;
    }
  }

  /**
   * Perform requested actions
   * @param string - action
   * @param string - theme id
   * @param array - errors
   */
  function perform_action($action, $theme_id)
  {
    if (isset($this->db_themes_by_id[$theme_id]))
    {
      $crt_db_theme = $this->db_themes_by_id[$theme_id];
    }

    $file_to_include = PHPWG_THEMES_PATH.'/'.$theme_id.'/admin/maintain.inc.php';

    $errors = array();

    switch ($action)
    {
      case 'activate':
        if (isset($crt_db_theme))
        {
          // the theme is already active
          break;
        }

        if ('default' == $theme_id)
        {
          // you can't activate the "default" theme
          break;
        }

        $missing_parent = $this->missing_parent_theme($theme_id);
        if (isset($missing_parent))
        {
          array_push(
            $errors, 
            sprintf(
              l10n('Impossible to activate this theme, the parent theme is missing: %s'),
              $missing_parent
              )
            );
          
          break;
        }

        if (file_exists($file_to_include))
        {
          include($file_to_include);
          if (function_exists('theme_activate'))
          {
            theme_activate($theme_id, $this->fs_themes[$theme_id]['version'], $errors);
          }
        }

        if (empty($errors))
        {
          $query = '
INSERT INTO '.THEMES_TABLE.'
  (id, version, name)
  VALUES(\''.$theme_id.'\',
         \''.$this->fs_themes[$theme_id]['version'].'\',
         \''.$this->fs_themes[$theme_id]['name'].'\')
;';
          pwg_query($query);
        }
        break;

      case 'deactivate':
        if (!isset($crt_db_theme))
        {
          // the theme is already inactive
          break;
        }

        // you can't deactivate the last theme
        if (count($this->db_themes_by_id) <= 1)
        {
          array_push(
            $errors,
            l10n('Impossible to deactivate this theme, you need at least one theme.')
            );
          break;
        }

        if ($theme_id == get_default_theme())
        {
          // find a random theme to replace
          $new_theme = null;

          $query = '
SELECT
    id
  FROM '.THEMES_TABLE.'
  WHERE id != \''.$theme_id.'\'
;';
          $result = pwg_query($query);
          if (pwg_db_num_rows($result) == 0)
          {
            $new_theme = 'default';
          }
          else
          {
            list($new_theme) = pwg_db_fetch_row($result);
          }

          $this->set_default_theme($new_theme);
        }

        if (file_exists($file_to_include))
        {
          include($file_to_include);
          if (function_exists('theme_deactivate'))
          {
            theme_deactivate($theme_id);
          }
        }

        $query = '
DELETE
  FROM '.THEMES_TABLE.'
  WHERE id= \''.$theme_id.'\'
;';
        pwg_query($query);
        break;

      case 'delete':
        if (!empty($crt_db_theme))
        {
          array_push($errors, 'CANNOT DELETE - THEME IS INSTALLED');
          break;
        }
        if (!isset($this->fs_themes[$theme_id]))
        {
          // nothing to do here
          break;
        }

        $children = $this->get_children_themes($theme_id);
        if (count($children) > 0)
        {
          array_push(
            $errors,
            sprintf(
              l10n('Impossible to delete this theme. Other themes depends on it: %s'),
              implode(', ', $children)
              )
            );
          break;
        }
        
        if (!$this->deltree(PHPWG_THEMES_PATH.$theme_id))
        {
          $this->send_to_trash(PHPWG_THEMES_PATH.$theme_id);
        }
        break;

      case 'set_default':
        // first we need to know which users are using the current default theme
        $this->set_default_theme($theme_id);        
        break;
    }
    return $errors;
  }

  function missing_parent_theme($theme_id)
  {
    if (!isset($this->fs_themes[$theme_id]['parent']))
    {
      return null;
    }
    
    $parent = $this->fs_themes[$theme_id]['parent'];
      
    if ('default' == $parent)
    {
      return null;
    }
      
    if (!isset($this->fs_themes[$parent]))
    {
      return $parent;
    }

    return $this->missing_parent_theme($parent);
  }

  function get_children_themes($theme_id)
  {
    $children = array();
    
    foreach ($this->fs_themes as $test_child)
    {
      if (isset($test_child['parent']) and $test_child['parent'] == $theme_id)
      {
        array_push($children, $test_child['name']);
      }
    }

    return $children;
  } 

  function set_default_theme($theme_id)
  {
    global $conf;
    
    // first we need to know which users are using the current default theme
    $default_theme = get_default_theme();
    
    $query = '
SELECT
    user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE theme = \''.$default_theme.'\'
;';
    $user_ids = array_unique(
      array_merge(
        array_from_query($query, 'user_id'),
        array($conf['guest_id'], $conf['default_user_id'])
        )
      );

    // $user_ids can't be empty, at least the default user has the default
    // theme

    $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET theme = \''.$theme_id.'\'
  WHERE user_id IN ('.implode(',', $user_ids).')
;';
    pwg_query($query);
  }

  function get_db_themes($id='')
  {
    $query = '
SELECT
    *
  FROM '.THEMES_TABLE;
    
    $clauses = array();
    if (!empty($id))
    {
      $clauses[] = 'id = \''.$id.'\'';
    }
    if (count($clauses) > 0)
    {
      $query .= '
  WHERE '. implode(' AND ', $clauses);
    }

    $result = pwg_query($query);
    $themes = array();
    while ($row = pwg_db_fetch_assoc($result))
    {
      array_push($themes, $row);
    }
    return $themes;
  }

  
  /**
  *  Get themes defined in the theme directory
  */  
  function get_fs_themes()
  {
    $dir = opendir(PHPWG_THEMES_PATH);
    
    while ($file = readdir($dir))
    {
      if ($file!='.' and $file!='..')
      {
        $path = PHPWG_THEMES_PATH.$file;
        if (is_dir($path)
            and preg_match('/^[a-zA-Z0-9-_]+$/', $file)
            and file_exists($path.'/themeconf.inc.php')
            )
        {
          $theme = array(
            'id' => $file,
            'name' => $file,
            'version' => '0',
            'uri' => '',
            'description' => '',
            'author' => '',
            );
          $theme_data = implode( '', file($path.'/themeconf.inc.php') );

          if ( preg_match("|Theme Name: (.*)|", $theme_data, $val) )
          {
            $theme['name'] = trim( $val[1] );
          }
          if (preg_match("|Version: (.*)|", $theme_data, $val))
          {
            $theme['version'] = trim($val[1]);
          }
          if ( preg_match("|Theme URI: (.*)|", $theme_data, $val) )
          {
            $theme['uri'] = trim($val[1]);
          }
          if ($desc = load_language('description.txt', $path.'/', array('return' => true)))
          {
            $theme['description'] = trim($desc);
          }
          elseif ( preg_match("|Description: (.*)|", $theme_data, $val) )
          {
            $theme['description'] = trim($val[1]);
          }
          if ( preg_match("|Author: (.*)|", $theme_data, $val) )
          {
            $theme['author'] = trim($val[1]);
          }
          if ( preg_match("|Author URI: (.*)|", $theme_data, $val) )
          {
            $theme['author uri'] = trim($val[1]);
          }
          if (!empty($theme['uri']) and strpos($theme['uri'] , 'extension_view.php?eid='))
          {
            list( , $extension) = explode('extension_view.php?eid=', $theme['uri']);
            if (is_numeric($extension)) $theme['extension'] = $extension;
          }
          if (preg_match('/["\']parent["\'][^"\']+["\']([^"\']+)["\']/', $theme_data, $val))
          {
            $theme['parent'] = $val[1];
          }
          if (preg_match('/["\']activable["\'].*?(true|false)/', $theme_data, $val))
          {
            $theme['activable'] = get_boolean($val[1]);
          }

          // screenshot
          $screenshot_path = $path.'/screenshot.png';
          if (file_exists($screenshot_path))
          {
            $theme['screenshot'] = $screenshot_path;
          }
          else
          {
            global $conf;
            $theme['screenshot'] =
              PHPWG_ROOT_PATH.'admin/themes/'
              .$conf['admin_theme']
              .'/images/missing_screenshot.png'
              ;
          }

          $admin_file = $path.'/admin/admin.inc.php';
          if (file_exists($admin_file))
          {
            $theme['admin_uri'] = get_root_url().'admin.php?page=theme&theme='.$file;
          }

          // IMPORTANT SECURITY !
          $theme = array_map('htmlspecialchars', $theme);
          $this->fs_themes[$file] = $theme;
        }
      }
    }
    closedir($dir);
  }

  /**
   * Sort fs_themes
   */
  function sort_fs_themes($order='name')
  {
    switch ($order)
    {
      case 'name':
        uasort($this->fs_themes, 'name_compare');
        break;
      case 'status':
        $this->sort_themes_by_state();
        break;
      case 'author':
        uasort($this->fs_themes, array($this, 'theme_author_compare'));
        break;
      case 'id':
        uksort($this->fs_themes, 'strcasecmp');
        break;
    }
  }

  /**
   * Retrieve PEM server datas to $server_themes
   */
  function get_server_themes($new=false)
  {
    global $user;

    $get_data = array(
      'category_id' => 10,
      'format' => 'php',
    );

    // Retrieve PEM versions
    $version = PHPWG_VERSION;
    $versions_to_check = array();
    $url = PEM_URL . '/api/get_version_list.php';
    if (fetchRemote($url, $result, $get_data) and $pem_versions = @unserialize($result))
    {
      if (!preg_match('/^\d+\.\d+\.\d+$/', $version))
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

    // Themes to check
    $themes_to_check = array();
    foreach($this->fs_themes as $fs_theme)
    {
      if (isset($fs_theme['extension']))
      {
        $themes_to_check[] = $fs_theme['extension'];
      }
    }

    // Retrieve PEM themes infos
    $url = PEM_URL . '/api/get_revision_list.php';
    $get_data = array_merge($get_data, array(
      'last_revision_only' => 'true',
      'version' => implode(',', $versions_to_check),
      'lang' => substr($user['language'], 0, 2),
      'get_nb_downloads' => 'true',
      )
    );

    if (!empty($themes_to_check))
    {
      if ($new)
      {
        $get_data['extension_exclude'] = implode(',', $themes_to_check);
      }
      else
      {
        $get_data['extension_include'] = implode(',', $themes_to_check);
      }
    }
    if (fetchRemote($url, $result, $get_data))
    {
      $pem_themes = @unserialize($result);
      if (!is_array($pem_themes))
      {
        return false;
      }
      foreach ($pem_themes as $theme)
      {
        $this->server_themes[$theme['extension_id']] = $theme;
      }
      return true;
    }
    return false;
  }
  
  /**
   * Sort $server_themes
   */
  function sort_server_themes($order='date')
  {
    switch ($order)
    {
      case 'date':
        krsort($this->server_themes);
        break;
      case 'revision':
        usort($this->server_themes, array($this, 'extension_revision_compare'));
        break;
      case 'name':
        uasort($this->server_themes, array($this, 'extension_name_compare'));
        break;
      case 'author':
        uasort($this->server_themes, array($this, 'extension_author_compare'));
        break;
      case 'downloads':
        usort($this->server_themes, array($this, 'extension_downloads_compare'));
        break;
    }
  }

  /**
   * Extract theme files from archive
   *
   * @param string - install or upgrade
   * @param string - remote revision identifier (numeric)
   * @param string - theme id or extension id
   */
  function extract_theme_files($action, $revision, $dest)
  {
    if ($archive = tempnam( PHPWG_THEMES_PATH, 'zip'))
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
            if (basename($file['filename']) == 'themeconf.inc.php'
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
              $extract_path = PHPWG_THEMES_PATH . $dest;
            }
            else
            {
              $extract_path = PHPWG_THEMES_PATH . ($root == '.' ? 'extension_' . $dest : basename($root));
            }
            if (
              $result = $zip->extract(
                PCLZIP_OPT_PATH, $extract_path,
                PCLZIP_OPT_REMOVE_PATH, $root,
                PCLZIP_OPT_REPLACE_NEWER
                )
              )
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
    $trash_path = PHPWG_THEMES_PATH . 'trash';
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
  function theme_version_compare($a, $b)
  {
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

  function theme_author_compare($a, $b)
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

  function sort_themes_by_state()
  {
    uasort($this->fs_themes, 'name_compare');

    $active_themes = array();
    $inactive_themes = array();
    $not_installed = array();

    foreach($this->fs_themes as $theme_id => $theme)
    {
      if (isset($this->db_themes_by_id[$theme_id]))
      {
        $this->db_themes_by_id[$theme_id]['state'] == 'active' ?
          $active_themes[$theme_id] = $theme : $inactive_themes[$theme_id] = $theme;
      }
      else
      {
        $not_installed[$theme_id] = $theme;
      }
    }
    $this->fs_themes = $active_themes + $inactive_themes + $not_installed;
  }

  // themes specific methods
  function get_fs_themes_with_ini()
  {
    $themes_dir = PHPWG_ROOT_PATH.'themes';

    $fs_themes = array();

    foreach (get_dirs($themes_dir) as $theme)
    {
      $conf_file = $themes_dir.'/'.$theme.'/themeconf.inc.php';
      if (file_exists($conf_file))
      {
        $theme_data = array(
          'name' => $theme,
          );
        
        $ini_file = $themes_dir.'/'.$theme.'/theme.ini';
        if (file_exists($ini_file))
        {
          $theme_ini = parse_ini_file($ini_file);
          if (isset($theme_ini['extension_id']))
          {
            $theme_data['extension_id'] = $theme_ini['extension_id'];
          }
        }

        array_push($fs_themes, $theme_data);
      }
    }

    echo '<pre>'; print_r($fs_themes); echo '</pre>';
  }

  
}
?>