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
function execute_sqlfile($filepath, $replaced, $replacing, $dblayer)
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
        if ('mysql' == $dblayer)
        {
          if ( preg_match('/^(CREATE TABLE .*)[\s]*;[\s]*/im', $query, $matches) )
          {
            $query = $matches[1].' DEFAULT CHARACTER SET utf8'.';';
          }
        }
        pwg_query($query);
      }
      $query = '';
    }
  }
}

/**
 * Automatically activate all core themes in the "themes" directory.
 *
 * @return void
 */
function activate_core_themes()
{
  include_once(PHPWG_ROOT_PATH.'admin/include/themes.class.php');
  $themes = new themes();
  foreach ($themes->fs_themes as $theme_id => $fs_theme)
  {
    if (in_array($theme_id, array('elegant', 'Sylvia', 'clear', 'dark', 'smartpocket')))
    {
      $themes->perform_action('activate', $theme_id);
    }
  }
}

function install_db_connect(&$infos, &$errors)
{
  try
  {
    pwg_db_connect($_POST['dbhost'], $_POST['dbuser'],
                   $_POST['dbpasswd'], $_POST['dbname']);
    pwg_db_check_version();
  }
  catch (Exception $e)
  {
    $errors[] = l10n($e->getMessage());
  }
}
?>