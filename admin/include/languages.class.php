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

class languages
{
  var $fs_languages = array();
  var $db_languages = array();
  var $server_languages = array();

  /**
   * Initialize $fs_languages and $db_languages
  */
  function languages($target_charset = null)
  {
    $this->fs_languages = $this->get_fs_languages($target_charset);
  }

  /**
   * Set tabsheet for languages pages.
   * @param string selected page.
   */
  function set_tabsheet($selected)
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

    $link = get_root_url().'admin.php?page=';

    $tabsheet = new tabsheet();
    $tabsheet->add('languages_installed', l10n('Installed Languages'), $link.'languages_installed');
    $tabsheet->add('languages_new', l10n('Add New Language'), $link.'languages_new');
    $tabsheet->select($selected);
    $tabsheet->assign();
  }

  /**
   * Perform requested actions
   * @param string - action
   * @param string - language id
   * @param array - errors
   */
  function perform_action($action, $language_id)
  {
    global $conf;

    if (isset($this->db_languages[$language_id]))
    {
      $crt_db_language = $this->db_languages[$language_id];
    }

    $errors = array();

    switch ($action)
    {
      case 'activate':
        if (isset($crt_db_language))
        {
          array_push($errors, 'CANNOT ACTIVATE - LANGUAGE IS ALREADY ACTIVATED');
          break;
        }

        $query = '
INSERT INTO '.LANGUAGES_TABLE.'
  (id, name)
  VALUES(\''.$language_id.'\', \''.$this->fs_languages[$language_id].'\')
;';
        pwg_query($query);
        break;

      case 'deactivate':
        if (!isset($crt_db_language))
        {
          array_push($errors, 'CANNOT DEACTIVATE - LANGUAGE IS ALREADY DEACTIVATED');
          break;
        }

        if ($language_id == get_default_language())
        {
          array_push($errors, 'CANNOT DEACTIVATE - LANGUAGE IS DEFAULT LANGUAGE');
          break;
        }

        $query = '
DELETE
  FROM '.LANGUAGES_TABLE.'
  WHERE id= \''.$language_id.'\'
;';
        pwg_query($query);
        break;

      case 'delete':
        if (!empty($crt_db_language))
        {
          array_push($errors, 'CANNOT DELETE - LANGUAGE IS ACTIVATED');
          break;
        }
        if (!isset($this->fs_languages[$language_id]))
        {
          array_push($errors, 'CANNOT DELETE - LANGUAGE DOES NOT EXIST');
          break;
        }

        // Set default language to user who are using this language
        $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET language = \''.get_default_language().'\'
  WHERE language = \''.$language_id.'\'
;';
        pwg_query($query);

        if (!$this->deltree(PHPWG_ROOT_PATH.'language/'.$language_id))
        {
          $this->send_to_trash(PHPWG_ROOT_PATH.'language/'.$language_id);
        }
        break;

      case 'set_default':
        $query = '
UPDATE '.USER_INFOS_TABLE.'
  SET language = \''.$language_id.'\'
  WHERE user_id IN ('.$conf['default_user_id'].', '.$conf['guest_id'].')
;';
        pwg_query($query);
        break;
    }
    return $errors;
  }

  /**
  *  Get languages defined in the language directory
  */
  function get_fs_languages($target_charset = null)
  {
    if ( empty($target_charset) )
    {
      $target_charset = get_pwg_charset();
    }
    $target_charset = strtolower($target_charset);

    $dir = opendir(PHPWG_ROOT_PATH.'language');

    while ($file = readdir($dir))
    {
      $path = PHPWG_ROOT_PATH.'language/'.$file;
      if (!is_link($path) and is_dir($path) and file_exists($path.'/iso.txt'))
      {
        list($language_name) = @file($path.'/iso.txt');

        $languages[$file] = convert_charset($language_name, 'utf-8', $target_charset);
      }
    }
    closedir($dir);
    @asort($languages);

    return $languages;
  }

  function get_db_languages()
  {
    $query = '
  SELECT id, name
    FROM '.LANGUAGES_TABLE.'
    ORDER BY name ASC
  ;';
    $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result))
    {
      $this->db_languages[ $row['id'] ] = $row['name'];
    }
  }

  /**
   * Retrieve PEM server datas to $server_languages
   */
  function get_server_languages()
  {
    global $user;

    $get_data = array(
      'category_id' => 8,
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

    // Retrieve PEM languages infos
    $url = PEM_URL . '/api/get_revision_list.php';
    $get_data = array_merge($get_data, array(
      'last_revision_only' => 'true',
      'version' => implode(',', $versions_to_check),
      'lang' => $user['language'],
      )
    );

    if (fetchRemote($url, $result, $get_data))
    {
      $pem_languages = @unserialize($result);
      if (!is_array($pem_languages))
      {
        return false;
      }
      foreach ($pem_languages as $language)
      {
        if (preg_match('/^.*? \[[A-Z]{2}\]$/', $language['extension_name'])
          and !in_array($language['extension_name'], $this->fs_languages))
        {
          $this->server_languages[] = $language;
        }
      }
      return true;
    }
    return false;
  }

  /**
   * Extract language files from archive
   *
   * @param string - install or upgrade
   * @param string - remote revision identifier (numeric)
   * @param string - language id or extension id
   */
  function extract_language_files($action, $revision, $dest='')
  {
    if ($archive = tempnam( PHPWG_ROOT_PATH.'language', 'zip'))
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
            // we search iso.txt in archive
            if (basename($file['filename']) == 'iso.txt'
              and (!isset($main_filepath)
              or strlen($file['filename']) < strlen($main_filepath)))
            {
              $main_filepath = $file['filename'];
            }
          }
          if (isset($main_filepath))
          {
            $root = basename(dirname($main_filepath)); // iso.txt path in archive
            if (preg_match('/^[a-z]{2}_[A-Z]{2}$/', $root))
            {
              if ($action == 'install')
              {
                $dest = $root;
              }
              $extract_path = PHPWG_ROOT_PATH.'language/'.$dest;
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
                if ($status == 'ok')
                {
                  $this->fs_languages = $this->get_fs_languages();
                  $this->perform_action('activate', $dest);
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
    $trash_path = PHPWG_ROOT_PATH . 'language/trash';
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
}
?>