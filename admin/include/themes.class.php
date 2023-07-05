<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * class DummyTheme_maintain
 * used when a theme uses the old procedural declaration of maintenance methods
 */
class DummyTheme_maintain extends ThemeMaintain
{
  function activate($theme_version, &$errors=array())
  {
    if (is_callable('theme_activate'))
    {
      return theme_activate($this->theme_id, $theme_version, $errors);
    }
  }
  function deactivate()
  {
    if (is_callable('theme_deactivate'))
    {
      return theme_deactivate($this->theme_id);
    }
  }
  function delete()
  {
    if (is_callable('theme_delete'))
    {
      return theme_delete($this->theme_id);
    }
  }
}


class themes
{
  var $fs_themes = array();
  var $db_themes_by_id = array();
  var $server_themes = array();

  /**
   * Initialize $fs_themes and $db_themes_by_id
  */
  function __construct()
  {
    $this->get_fs_themes();

    foreach ($this->get_db_themes() as $db_theme)
    {
      $this->db_themes_by_id[$db_theme['id']] = $db_theme;
    }
  }

  /**
   * Returns the maintain class of a theme
   * or build a new class with the procedural methods
   * @param string $theme_id
   */
  private static function build_maintain_class($theme_id)
  {
    $file_to_include = PHPWG_THEMES_PATH.'/'.$theme_id.'/admin/maintain.inc.php';
    $classname = $theme_id.'_maintain';

    if (file_exists($file_to_include))
    {
      include_once($file_to_include);

      if (class_exists($classname))
      {
        return new $classname($theme_id);
      }
    }
    
    return new DummyTheme_maintain($theme_id);
  }

  /**
   * Perform requested actions
   * @param string - action
   * @param string - theme id
   * @param array - errors
   */
  function perform_action($action, $theme_id)
  {
    global $conf;

    if (!$conf['enable_extensions_install'] and 'delete' == $action)
    {
      die('Piwigo extensions install/update/delete system is disabled');
    }

    if (isset($this->db_themes_by_id[$theme_id]))
    {
      $crt_db_theme = $this->db_themes_by_id[$theme_id];
    }

    $theme_maintain = self::build_maintain_class($theme_id);

    $errors = array();
    $activity_details = array('theme_id'=>$theme_id);

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
          $errors[] = l10n(
            'Impossible to activate this theme, the parent theme is missing: %s',
            $missing_parent
            );

          break;
        }

        if ($this->fs_themes[$theme_id]['mobile']
            and !empty($conf['mobile_theme'])
            and $conf['mobile_theme'] != $theme_id)
        {
          $errors[] = l10n('You can activate only one mobile theme.');
          break;
        }

        $theme_maintain->activate($this->fs_themes[$theme_id]['version'], $errors);

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

          $activity_details['version'] = $this->fs_themes[$theme_id]['version'];

          if ($this->fs_themes[$theme_id]['mobile'])
          {
            conf_update_param('mobile_theme', $theme_id);
          }
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
          $errors[] = l10n('Impossible to deactivate this theme, you need at least one theme.');
          break;
        }

        if ($theme_id == get_default_theme())
        {
          // find a random theme to replace
          $new_theme = null;

          $query = '
SELECT id
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

        $theme_maintain->deactivate();

        $query = '
DELETE
  FROM '.THEMES_TABLE.'
  WHERE id= \''.$theme_id.'\'
;';
        pwg_query($query);

        if ($this->fs_themes[$theme_id]['mobile'])
        {
          conf_update_param('mobile_theme', '');
        }
        break;

      case 'delete':
        if (!empty($crt_db_theme))
        {
          $errors[] = 'CANNOT DELETE - THEME IS INSTALLED';
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
          $errors[] = l10n(
            'Impossible to delete this theme. Other themes depends on it: %s',
            implode(', ', $children)
            );
          break;
        }

        $theme_maintain->delete();

        include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
        deltree(PHPWG_THEMES_PATH.$theme_id, PHPWG_THEMES_PATH . 'trash');
        break;

      case 'set_default':
        // first we need to know which users are using the current default theme
        $this->set_default_theme($theme_id);
        break;
    }

    pwg_activity('system', ACTIVITY_SYSTEM_THEME, $action, $activity_details);

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
        $children[] = $test_child['name'];
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
      $themes[] = $row;
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
            'mobile' => false,
            );
          $theme_data = implode('', file($path.'/themeconf.inc.php'));

          if (preg_match("|Theme Name:\\s*(.+)|", $theme_data, $val))
          {
            $theme['name'] = trim( $val[1] );
          }
          if (preg_match("|Version:\\s*([\\w.-]+)|", $theme_data, $val))
          {
            $theme['version'] = trim($val[1]);
          }
          if (preg_match("|Theme URI:\\s*(https?:\\/\\/.+)|", $theme_data, $val))
          {
            $theme['uri'] = trim($val[1]);
          }
          if ($desc = load_language('description.txt', $path.'/', array('return' => true)))
          {
            $theme['description'] = trim($desc);
          }
          elseif (preg_match("|Description:\\s*(.+)|", $theme_data, $val))
          {
            $theme['description'] = trim($val[1]);
          }
          if (preg_match("|Author:\\s*(.+)|", $theme_data, $val))
          {
            $theme['author'] = trim($val[1]);
          }
          if (preg_match("|Author URI:\\s*(https?:\\/\\/.+)|", $theme_data, $val))
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
          if (preg_match('/["\']activable["\'].*?(true|false)/i', $theme_data, $val))
          {
            $theme['activable'] = get_boolean($val[1]);
          }
          if (preg_match('/["\']mobile["\'].*?(true|false)/i', $theme_data, $val))
          {
            $theme['mobile'] = get_boolean($val[1]);
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
              .userprefs_get_param('admin_theme', 'clear')
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
    global $user, $conf;

    $get_data = array(
      'category_id' => $conf['pem_themes_category'],
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
      $branch = get_branch_from_version($version);
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
    $url = PEM_URL . '/api/get_revision_list-next.php';
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
  function extract_theme_files($action, $revision, $dest, &$theme_id=null)
  {
    global $logger;

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
        include_once(PHPWG_ROOT_PATH.'admin/include/pclzip.lib.php');
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

          $logger->debug(__FUNCTION__.', $main_filepath = '.$main_filepath);

          if (isset($main_filepath))
          {
            $root = dirname($main_filepath); // main.inc.php path in archive
            if ($action == 'upgrade')
            {
              $theme_id = $dest;
            }
            else
            {
              $theme_id = ($root == '.' ? 'extension_' . $dest : basename($root));
            }
            $extract_path = PHPWG_THEMES_PATH . $theme_id;
            $logger->debug(__FUNCTION__.', $extract_path = '.$extract_path);

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
                $old_files[] = 'obsolete.list';

                $logger->debug(__FUNCTION__.', $old_files = {'.join('},{', $old_files).'}');

                $extract_path_realpath = realpath($extract_path);

                foreach($old_files as $old_file)
                {
                  $old_file = trim($old_file);
                  $old_file = trim($old_file, '/'); // prevent path starting with a "/"

                  if (empty($old_file)) // empty here means the extension itself
                  {
                    continue;
                  }

                  $path = $extract_path.'/'.$old_file;

                  // make sure the obsolete file is withing the extension directory, prevent traversal path
                  $realpath = realpath($path);
                  if ($realpath === false or strpos($realpath, $extract_path_realpath) !== 0)
                  {
                    continue;
                  }

                  $logger->debug(__FUNCTION__.', to delete = '.$path);

                  if (is_file($path))
                  {
                    @unlink($path);
                  }
                  elseif (is_dir($path))
                  {
                    deltree($path, PHPWG_THEMES_PATH . 'trash');
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
   * Sort functions
   */
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

}
?>
