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
 * loads an sql file and executes all queries
 *
 * Before executing a query, $replaced is... replaced by $replacing. This is
 * useful when the SQL file contains generic words. Drop table queries are
 * not executed.
 *
 * @param string filepath
 * @param string replaced
 * @param string replacing
 * @return void
 */
function execute_sqlfile($filepath, $replaced, $replacing)
{
  $sql_lines = file($filepath);
  $query = '';
  foreach ($sql_lines as $sql_line)
  {
    $sql_line = trim($sql_line);
    if (preg_match('/(^--|^$)/', $sql_line))
    {
      continue;
    }
    $query.= ' '.$sql_line;
    // if we reached the end of query, we execute it and reinitialize the
    // variable "query"
    if (preg_match('/;$/', $sql_line))
    {
      $query = trim($query);
      $query = str_replace($replaced, $replacing, $query);
      // we don't execute "DROP TABLE" queries
      if (!preg_match('/^DROP TABLE/i', $query))
      {
        global $install_charset_collate;
        if ( !empty($install_charset_collate) )
        {
          if ( preg_match('/^(CREATE TABLE .*)[\s]*;[\s]*/im', $query, $matches) )
          {
            $query = $matches[1].' '.$install_charset_collate.';';
          }
        }
        pwg_query($query);
      }
      $query = '';
    }
  }
}

/**
 * Search for database engines available
 *
 * We search for functions_DATABASE_ENGINE.inc.php 
 * and we check if the connect function for that database exists 
 *
 * @return array
 */
function available_engines()
{
  $engines = array();

  $pattern = PHPWG_ROOT_PATH. 'include/dblayer/functions_%s.inc.php';
  include_once PHPWG_ROOT_PATH. 'include/dblayer/dblayers.inc.php';

  foreach ($dblayers as $engine_name => $engine)
  {
    if (file_exists(sprintf($pattern, $engine_name))) 
    {
      $engines[$engine_name]['label'] = $engine['engine'];
      $engines[$engine_name]['available'] = false;

      if (isset($engine['function_available'])
	  && function_exists($engine['function_available']))
      {
	$engines[$engine_name]['available'] = true;
      }
      elseif (isset($engine['class_available']) 
	      && class_exists($engine['class_available']))
      {
	$engines[$engine_name]['available'] = true;
      } 
    }
  }

  if ($engines['sqlite']['available'] and !$engines['pdo-sqlite']['available'])
  {
    unset($engines['pdo-sqlite']);
  }
  elseif ($engines['pdo-sqlite']['available'] and !$engines['sqlite']['available'])
  {
    unset($engines['sqlite']);
  }
  elseif (DEFAULT_DB_SQLITE=='native')
  {
    unset($engines['pdo-sqlite']);
  }
  else
  {
    unset($engines['sqlite']);
  }

  return $engines;
}

/**
 * Automatically activate all themes in the "themes" directory.
 *
 * @return void
 */
function activate_all_themes()
{
  include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
  $themes = new themes();
  foreach ($themes->fs_themes as $theme_id => $fs_theme)
  {
    $themes->perform_action('activate', $theme_id);
  }
}

function install_db_connect(&$infos, &$errors)
{
  try
  {
    $pwg_db_link = pwg_db_connect($_POST['dbhost'], $_POST['dbuser'], 
                                  $_POST['dbpasswd'], $_POST['dbname']);
 
    return true;
  }
  catch (Exception $e)
  {
    array_push( $errors, l10n($e->getMessage()));
  }
  return false;
}
?>