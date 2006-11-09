<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

define('PHPWG_ROOT_PATH', './');

include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upgrade.php');
include(PHPWG_ROOT_PATH.'include/template.php');

include(PHPWG_ROOT_PATH.'include/mysql.inc.php');
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');

check_upgrade();

// concerning upgrade, we use the default users table
$conf['users_table'] = $prefixeTable.'users';

include_once(PHPWG_ROOT_PATH.'include/constants.php');
define('PREFIX_TABLE', $prefixeTable);

// Database connection
mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
or die ( "Could not connect to database server" );
mysql_select_db( $cfgBase )
or die ( "Could not connect to database" );
// +-----------------------------------------------------------------------+
// |                            tricky output                              |
// +-----------------------------------------------------------------------+
echo '<!-- This is an HTML comment given in order to make IE outputs';
echo ' the code.'."\n";
echo ' Indeed, IE doesn\'t start to send output until a limit';
echo ' of XXX bytes '."\n";
echo str_repeat( ' ', 80 )."\n";
echo str_repeat( ' ', 80 )."\n";
echo str_repeat( ' ', 80 )."\n";
echo '-->'."\n";
flush();
// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+

/**
 * list all tables in an array
 *
 * @return array
 */
function get_tables()
{
  $tables = array();
  
  $query = '
SHOW TABLES
;';
  $result = mysql_query($query);

  while ($row = mysql_fetch_row($result))
  {
    if (preg_match('/^'.PREFIX_TABLE.'/', $row[0]))
    {
      array_push($tables, $row[0]);
    }
  }

  return $tables;
}

/**
 * list all columns of each given table
 *
 * @return array of array
 */
function get_columns_of($tables)
{
  $columns_of = array();

  foreach ($tables as $table)
  {
    $query = '
DESC '.$table.'
;';
    $result = mysql_query($query);

    $columns_of[$table] = array();

    while ($row = mysql_fetch_row($result))
    {
      array_push($columns_of[$table], $row[0]);
    }
  }

  return $columns_of;
}

/**
 */
function print_time($message)
{
  global $last_time;
  
  $new_time = get_moment();
  echo '<pre>['.get_elapsed_time($last_time, $new_time).']';
  echo ' '.$message;
  echo '</pre>';
  flush();
  $last_time = $new_time;
}

// +-----------------------------------------------------------------------+
// |                             playing zone                              |
// +-----------------------------------------------------------------------+

// echo implode('<br>', get_tables());
// echo '<pre>'; print_r(get_columns_of(get_tables())); echo '</pre>';

// foreach (get_available_upgrade_ids() as $upgrade_id)
// {
//   echo $upgrade_id, '<br>';
// }

// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+

$template = new Template(PHPWG_ROOT_PATH.'template/yoga');
$template->set_filenames(array('upgrade'=>'upgrade.tpl'));
$template->assign_vars(array('RELEASE'=>PHPWG_VERSION));

// +-----------------------------------------------------------------------+
// |                            upgrade choice                             |
// +-----------------------------------------------------------------------+

if (!isset($_GET['version']))
{
  // find the current release
  $tables = get_tables();
  $columns_of = get_columns_of($tables);

  if (!in_array('param', $columns_of[PREFIX_TABLE.'config']))
  {
    // we're in branch 1.3, important upgrade, isn't it?
    if (in_array(PREFIX_TABLE.'user_category', $tables))
    {
      $current_release = '1.3.1';
    }
    else
    {
      $current_release = '1.3.0';
    }
  }
  else if (!in_array(PREFIX_TABLE.'user_cache', $tables))
  {
    $current_release = '1.4.0';
  }
  else if (!in_array(PREFIX_TABLE.'tags', $tables))
  {
    $current_release = '1.5.0';
  }
  else if (!in_array('auto_login_key', $columns_of[PREFIX_TABLE.'user_infos']))
  {
    $current_release = '1.6.0';
  }
  else
  {
    die('No upgrade required, the database structure is up to date');
  }
  
  $template->assign_block_vars(
    'introduction',
    array(
      'CURRENT_RELEASE' => $current_release,
      'RUN_UPGRADE_URL' =>
        PHPWG_ROOT_PATH.'upgrade.php?version='.$current_release,
      )
    );
}

// +-----------------------------------------------------------------------+
// |                            upgrade launch                             |
// +-----------------------------------------------------------------------+

else
{
  $upgrade_file = PHPWG_ROOT_PATH.'install/upgrade_'.$_GET['version'].'.php';
  if (is_file($upgrade_file))
  {
    $page['infos'] = array();
    $page['upgrade_start'] = get_moment();
    $conf['die_on_sql_error'] = false;
    include($upgrade_file);

    // Available upgrades must be ignored after a fresh installation. To
    // make PWG avoid upgrading, we must tell it upgrades have already been
    // made.
    list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));
    define('CURRENT_DATE', $dbnow);
    $datas = array();
    foreach (get_available_upgrade_ids() as $upgrade_id)
    {
      array_push(
        $datas,
        array(
          'id'          => $upgrade_id,
          'applied'     => CURRENT_DATE,
          'description' => 'upgrade included in migration',
          )
        );
    }
    mass_inserts(
      UPGRADE_TABLE,
      array_keys($datas[0]),
      $datas
      );
    
    $page['upgrade_end'] = get_moment();

    $template->assign_block_vars(
      'upgrade',
      array(
        'VERSION' => $_GET['version'],
        'TOTAL_TIME' => get_elapsed_time(
          $page['upgrade_start'],
          $page['upgrade_end']
          ),
        'SQL_TIME' => number_format(
          $page['queries_time'],
          3,
          '.',
          ' '
          ).' s',
        'NB_QUERIES' => $page['count_queries']
        )
      );

    array_push(
      $page['infos'],
      '[security] delete files "upgrade.php", "install.php" and "install"
directory'
      );

    array_push(
      $page['infos'],
      'in include/mysql.inc.php, remove
<pre style="background-color:lightgray">
define(\'PHPWG_IN_UPGRADE\', true);
</pre>'
      );

    array_push(
      $page['infos'],
      'Perform a maintenance check in [Administration>General>Maintenance]
if you encounter any problem.'
      );
    
    $template->assign_block_vars('upgrade.infos', array());
    
    foreach ($page['infos'] as $info)
    {
      $template->assign_block_vars(
        'upgrade.infos.info',
        array(
          'CONTENT' => $info,
          )
        );
    }

    $query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\'
;';
    pwg_query($query);
  }
  else
  {
    die('Hacking attempt');
  }
}

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->pparse('upgrade');
?>
