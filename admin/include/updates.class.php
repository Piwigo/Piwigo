<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

class updates
{
  var $types = array();
  var $plugins;
  var $themes;
  var $languages;
  var $missing = array();
  var $default_plugins = array();
  var $default_themes = array();
  var $default_languages = array();
  var $merged_extensions = array();
  var $merged_extension_url = 'http://piwigo.org/download/merged_extensions.txt';

  function __construct($page='updates')
  {
    $this->types = array('plugins', 'themes', 'languages');

    if (in_array($page, $this->types))
    {
      $this->types = array($page);
    }
    $this->default_themes = array('clear', 'dark', 'Sylvia', 'elegant');
    $this->default_plugins = array('admin_multi_view', 'c13y_upgrade', 'language_switch', 'LocalFilesEditor');

    foreach ($this->types as $type)
    {
      include_once(PHPWG_ROOT_PATH.'admin/include/'.$type.'.class.php');
      $this->$type = new $type();
    }
  }

  static function check_piwigo_upgrade()
  {
    $_SESSION['need_update'] = null;

    if (preg_match('/(\d+\.\d+)\.(\d+)/', PHPWG_VERSION, $matches)
      and @fetchRemote(PHPWG_URL.'/download/all_versions.php?rand='.md5(uniqid(rand(), true)), $result))
    {
      $all_versions = @explode("\n", $result);
      $new_version = trim($all_versions[0]);
      $_SESSION['need_update'] = version_compare(PHPWG_VERSION, $new_version, '<');
    }
  }

  function get_server_extensions($version=PHPWG_VERSION)
  {
    global $user;

    $get_data = array(
      'format' => 'php',
    );

    // Retrieve PEM versions
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

    // Extensions to check
    $ext_to_check = array();
    foreach ($this->types as $type)
    {
      $fs = 'fs_'.$type;
      foreach ($this->$type->$fs as $ext)
      {
        if (isset($ext['extension']))
        {
          $ext_to_check[$ext['extension']] = $type;
        }
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

    $post_data = array();
    if (!empty($ext_to_check))
    {
      $post_data['extension_include'] = implode(',', array_keys($ext_to_check));
    }

    if (fetchRemote($url, $result, $get_data, $post_data))
    {
      $pem_exts = @unserialize($result);
      if (!is_array($pem_exts))
      {
        return false;
      }
      foreach ($pem_exts as $ext)
      {
        if (isset($ext_to_check[$ext['extension_id']]))
        {
          $server = 'server_'.$ext_to_check[$ext['extension_id']];
          $this->$ext_to_check[$ext['extension_id']]->$server += array($ext['extension_id'] => $ext);
          unset($ext_to_check[$ext['extension_id']]);
        }
      }
      $this->check_missing_extensions($ext_to_check);
      return true;
    }
    return false;
  }

  // Check all extensions upgrades
  function check_extensions()
  {
    global $conf;

    if (!$this->get_server_extensions())
    {
      return false;
    }

    $_SESSION['extensions_need_update'] = array();

    foreach ($this->types as $type)
    {
      $fs = 'fs_'.$type;
      $server = 'server_'.$type;
      $server_ext = $this->$type->$server;
      $fs_ext = $this->$type->$fs;

      $ignore_list = array();
      $need_upgrade = array();

      foreach($fs_ext as $ext_id => $fs_ext)
      {
        if (isset($fs_ext['extension']) and isset($server_ext[$fs_ext['extension']]))
        {
          $ext_info = $server_ext[$fs_ext['extension']];

          if (!$this->version_compare($fs_ext['version'], $ext_info['revision_name'], $type))
          {
            if (in_array($ext_id, $conf['updates_ignored'][$type]))
            {
              $ignore_list[] = $ext_id;
            }
            else
            {
              $_SESSION['extensions_need_update'][$type][$ext_id] = $ext_info['revision_name'];
            }
          }
        }
      }
      $conf['updates_ignored'][$type] = $ignore_list;
    }
    conf_update_param('updates_ignored', pwg_db_real_escape_string(serialize($conf['updates_ignored'])));
  }

  // Check if extension have been upgraded since last check
  function check_updated_extensions()
  {
    foreach ($this->types as $type)
    {
      if (!empty($_SESSION['extensions_need_update'][$type]))
      {
        $fs = 'fs_'.$type;
        foreach($this->$type->$fs as $ext_id => $fs_ext)
        {
          if (isset($_SESSION['extensions_need_update'][$type][$ext_id])
            and $this->version_compare($fs_ext['version'], $_SESSION['extensions_need_update'][$type][$ext_id], $type))
          {
            // Extension have been upgraded
            $this->check_extensions();
            break;
          }
        }
      }
    }
  }

  function check_missing_extensions($missing)
  {
    foreach ($missing as $id => $type)
    {
      $fs = 'fs_'.$type;
      $default = 'default_'.$type;
      foreach ($this->$type->$fs as $ext_id => $ext)
      {
        if (isset($ext['extension']) and $id == $ext['extension']
          and !in_array($ext_id, $this->$default)
          and !in_array($ext['extension'], $this->merged_extensions))
        {
          $this->missing[$type][] = $ext;
          break;
        }
      }
    }
  }

  function get_merged_extensions($version)
  {
    if (fetchRemote($this->merged_extension_url, $result))
    {
      $rows = explode("\n", $result);
      foreach ($rows as $row)
      {
        if (preg_match('/^(\d+\.\d+): *(.*)$/', $row, $match))
        {
          if (version_compare($version, $match[1], '>='))
          {
            $extensions = explode(',', trim($match[2]));
            $this->merged_extensions = array_merge($this->merged_extensions, $extensions);
          }
        }
      }
    }
  }

  function version_compare($a, $b, $type)
  {
    $version_compare = rtrim($type, 's').'_version_compare';

    return $this->$type->$version_compare($a, $b);
  }

  static function process_obsolete_list($file)
  {
    if (file_exists(PHPWG_ROOT_PATH.$file)
      and $old_files = file(PHPWG_ROOT_PATH.$file, FILE_IGNORE_NEW_LINES)
      and !empty($old_files))
    {
      $old_files[] = $file;
      foreach($old_files as $old_file)
      {
        $path = PHPWG_ROOT_PATH.$old_file;
        if (is_file($path))
        {
          @unlink($path);
        }
        elseif (is_dir($path))
        {
          deltree($path, PHPWG_ROOT_PATH.'_trash');
        }
      }
    }
  }

  static function dump_database($include_history=false)
  {
    global $page, $conf, $cfgBase;

    if (version_compare(PHPWG_VERSION, '2.1', '<'))
    {
      $conf['db_base'] = $cfgBase;
    }

    include(PHPWG_ROOT_PATH.'admin/include/mysqldump.php');

    $path = PHPWG_ROOT_PATH.$conf['data_location'].'update';

    if (@mkgetdir($path)
      and ($backupFile = tempnam($path, 'sql'))
      and ($dumper = new MySQLDump($conf['db_base'],$backupFile,false,false)))
    {
      foreach (get_defined_constants() as $constant => $value)
      {
        if (preg_match('/_TABLE$/', $constant))
        {
          $dumper->getTableStructure($value);
          if ($constant == 'HISTORY_TABLE' and !$include_history)
          {
            continue;
          }
          $dumper->getTableData($value);
        }
      }
    }

    if (@filesize($backupFile))
    {
      $http_headers = array(
        'Content-Length: '.@filesize($backupFile),
        'Content-Type: text/x-sql',
        'Content-Disposition: attachment; filename="database.sql";',
        'Content-Transfer-Encoding: binary',
        );

      foreach ($http_headers as $header) {
        header($header);
      }

      @readfile($backupFile);
      deltree(PHPWG_ROOT_PATH.$conf['data_location'].'update');
      exit();
    }
    else
    {
      $page['errors'][] = l10n('Unable to dump database.');
    }
  }

  static function upgrade_to($upgrade_to, &$step, $check_current_version=true)
  {
    global $page, $conf, $template;

    if ($check_current_version and !version_compare($upgrade_to, PHPWG_VERSION, '>'))
    {
      redirect(get_root_url().'admin.php?page=plugin-'.basename(dirname(__FILE__)));
    }

    if ($step == 2)
    {
      preg_match('/(\d+\.\d+)\.(\d+)/', PHPWG_VERSION, $matches);
      $code =  $matches[1].'.x_to_'.$upgrade_to;
      $dl_code = str_replace(array('.', '_'), '', $code);
      $remove_path = $code;
      $obsolete_list = 'obsolete.list';
    }
    else
    {
      $code = $upgrade_to;
      $dl_code = $code;
      $remove_path = version_compare($code, '2.0.8', '>=') ? 'piwigo' : 'piwigo-'.$code;
      $obsolete_list = PHPWG_ROOT_PATH.'install/obsolete.list';
    }

    if (empty($page['errors']))
    {
      $path = PHPWG_ROOT_PATH.$conf['data_location'].'update';
      $filename = $path.'/'.$code.'.zip';
      @mkgetdir($path);

      $chunk_num = 0;
      $end = false;
      $zip = @fopen($filename, 'w');

      while (!$end)
      {
        $chunk_num++;
        if (@fetchRemote(PHPWG_URL.'/download/dlcounter.php?code='.$dl_code.'&chunk_num='.$chunk_num, $result)
          and $input = @unserialize($result))
        {
          if (0 == $input['remaining'])
          {
            $end = true;
          }
          @fwrite($zip, base64_decode($input['data']));
        }
        else
        {
          $end = true;
        }
      }
      @fclose($zip);

      if (@filesize($filename))
      {
        $zip = new PclZip($filename);
        if ($result = $zip->extract(PCLZIP_OPT_PATH, PHPWG_ROOT_PATH,
                                    PCLZIP_OPT_REMOVE_PATH, $remove_path,
                                    PCLZIP_OPT_SET_CHMOD, 0755,
                                    PCLZIP_OPT_REPLACE_NEWER))
        {
          //Check if all files were extracted
          $error = '';
          foreach($result as $extract)
          {
            if (!in_array($extract['status'], array('ok', 'filtered', 'already_a_directory')))
            {
              // Try to change chmod and extract
              if (@chmod(PHPWG_ROOT_PATH.$extract['filename'], 0777)
                and ($res = $zip->extract(PCLZIP_OPT_BY_NAME, $remove_path.'/'.$extract['filename'],
                                          PCLZIP_OPT_PATH, PHPWG_ROOT_PATH,
                                          PCLZIP_OPT_REMOVE_PATH, $remove_path,
                                          PCLZIP_OPT_SET_CHMOD, 0755,
                                          PCLZIP_OPT_REPLACE_NEWER))
                and isset($res[0]['status'])
                and $res[0]['status'] == 'ok')
              {
                continue;
              }
              else
              {
                $error .= $extract['filename'].': '.$extract['status']."\n";
              }
            }
          }

          if (empty($error))
          {
            self::process_obsolete_list($obsolete_list);
            deltree(PHPWG_ROOT_PATH.$conf['data_location'].'update');
            invalidate_user_cache(true);
            $template->delete_compiled_templates();
            unset($_SESSION['need_update']);
            if ($step == 2)
            {
              $page['infos'][] = l10n('Update Complete');
              $page['infos'][] = $upgrade_to;
              $step = -1;
            }
            else
            {
              redirect(PHPWG_ROOT_PATH.'upgrade.php?now=');
            }
          }
          else
          {
            file_put_contents(PHPWG_ROOT_PATH.$conf['data_location'].'update/log_error.txt', $error);
            
            $page['errors'][] = l10n(
              'An error has occured during extract. Please check files permissions of your piwigo installation.<br><a href="%s">Click here to show log error</a>.',
              get_root_url().$conf['data_location'].'update/log_error.txt'
              );
          }
        }
        else
        {
          deltree(PHPWG_ROOT_PATH.$conf['data_location'].'update');
          $page['errors'][] = l10n('An error has occured during upgrade.');
        }
      }
      else
      {
        $page['errors'][] = l10n('Piwigo cannot retrieve upgrade file from server');
      }
    }
  }
}

?>