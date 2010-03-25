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

        $query = "
INSERT INTO ".LANGUAGES_TABLE."
  SET id = '".$language_id."',
      name = '".$this->fs_languages[$language_id]."'
;";
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
        
        $query = "
DELETE
  FROM ".LANGUAGES_TABLE."
  WHERE id= '".$language_id."'
;";
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
  SET language = "'.get_default_language().'"
  WHERE language = "'.$language_id.'"
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
  SET language = "'.$language_id.'"
  WHERE user_id = '.$conf['default_user_id'].'
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

        $languages[$file] = convert_charset($language_name, $target_charset);
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