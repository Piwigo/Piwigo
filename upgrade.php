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
    array_push(
      $tables,
      preg_replace('/^'.PREFIX_TABLE.'/', '', $row[0])
      );
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
DESC '.PREFIX_TABLE.$table.'
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

/**
 * replace old style #images.keywords by #tags. Requires a big data
 * migration.
 *
 * @return void
 */
function tag_replace_keywords()
{
  // code taken from upgrades 19 and 22
  
  $query = '
CREATE TABLE '.PREFIX_TABLE.'tags (
  id smallint(5) UNSIGNED NOT NULL auto_increment,
  name varchar(255) BINARY NOT NULL,
  url_name varchar(255) BINARY NOT NULL,
  PRIMARY KEY (id)
)
;';
  pwg_query($query);
  
  $query = '
CREATE TABLE '.PREFIX_TABLE.'image_tag (
  image_id mediumint(8) UNSIGNED NOT NULL,
  tag_id smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (image_id,tag_id)
)
;';
  pwg_query($query);
  
  //
  // Move keywords to tags
  //

  // each tag label is associated to a numeric identifier
  $tag_id = array();
  // to each tag id (key) a list of image ids (value) is associated
  $tag_images = array();

  $current_id = 1;

  $query = '
SELECT id, keywords
  FROM '.PREFIX_TABLE.'images
  WHERE keywords IS NOT NULL
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    foreach(preg_split('/[,]+/', $row['keywords']) as $keyword)
    {
      if (!isset($tag_id[$keyword]))
      {
        $tag_id[$keyword] = $current_id++;
      }

      if (!isset($tag_images[ $tag_id[$keyword] ]))
      {
        $tag_images[ $tag_id[$keyword] ] = array();
      }

      array_push(
        $tag_images[ $tag_id[$keyword] ],
        $row['id']
        );
    }
  }

  $datas = array();
  foreach ($tag_id as $tag_name => $tag_id)
  {
    array_push(
      $datas,
      array(
        'id'       => $tag_id,
        'name'     => $tag_name,
        'url_name' => str2url($tag_name),
        )
      );
  }
  
  if (!empty($datas))
  {
    mass_inserts(
      PREFIX_TABLE.'tags',
      array_keys($datas[0]),
      $datas
      );
  }

  $datas = array();
  foreach ($tag_images as $tag_id => $images)
  {
    foreach (array_unique($images) as $image_id)
    {
      array_push(
        $datas,
        array(
          'tag_id'   => $tag_id,
          'image_id' => $image_id,
          )
        );
    }
  }
  
  if (!empty($datas))
  {
    mass_inserts(
      PREFIX_TABLE.'image_tag',
      array_keys($datas[0]),
      $datas
      );
  }

  //
  // Delete images.keywords
  //
  $query = '
ALTER TABLE '.PREFIX_TABLE.'images DROP COLUMN keywords
;';
  pwg_query($query);

  //
  // Add useful indexes
  //
  $query = '
ALTER TABLE '.PREFIX_TABLE.'tags
  ADD INDEX tags_i1(url_name)
;';
  pwg_query($query);


  $query = '
ALTER TABLE '.PREFIX_TABLE.'image_tag
  ADD INDEX image_tag_i1(tag_id)
;';
  pwg_query($query);

  print_time('tags have replaced keywords');
}

// +-----------------------------------------------------------------------+
// |                             playing zone                              |
// +-----------------------------------------------------------------------+

// echo implode('<br>', get_tables());
// echo '<pre>'; print_r(get_columns_of(get_tables())); echo '</pre>';

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

  if (!in_array('param', $columns_of['config']))
  {
    // we're in branch 1.3, important upgrade, isn't it?
    if (in_array('user_category', $tables))
    {
      $current_release = '1.3.1';
    }
    else
    {
      $current_release = '1.3.0';
    }
  }
  else if (!in_array('user_cache', $tables))
  {
    $current_release = '1.4.0';
  }
  else if (!in_array('tags', $tables))
  {
    $current_release = '1.5.0';
  }
  else
  {
    die('You are already on branch 1.6, no upgrade required');
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
    $page['upgrade_start'] = get_moment();
    $conf['die_on_sql_error'] = false;
    include($upgrade_file);
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

    if (!isset($infos))
    {
      $infos = array();
    }
    array_push(
      $infos,
      '[security] delete files "upgrade.php", "install.php" and "install"
directory'
      );

    array_push(
      $infos,
      'in include/mysql.inc.php, remove
<pre style="background-color:lightgray">
define(\'PHPWG_IN_UPGRADE\', true);
</pre>'
      );

    array_push(
      $infos,
      'Perform a maintenance check in [Administration>General>Maintenance]
if you encounter any problem.'
      );
    
    $template->assign_block_vars('upgrade.infos', array());
    
    foreach ($infos as $info)
    {
      $template->assign_block_vars(
        'upgrade.infos.info',
        array(
          'CONTENT' => $info,
          )
        );
    }
  }
  else
  {
    die('Hacking attempt');
  }
}

$query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\'
;';
pwg_query($query);

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+

$template->pparse('upgrade');
?>