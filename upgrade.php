<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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

define('IN_UPGRADE', true);
define('PHPWG_ROOT_PATH', './');

include_once(PHPWG_ROOT_PATH.'include/functions.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include(PHPWG_ROOT_PATH.'include/template.php');
include(PHPWG_ROOT_PATH.'include/mysql.inc.php');
include_once(PHPWG_ROOT_PATH.'include/constants.php');
define('PREFIX_TABLE', $table_prefix);

$conf['show_queries'] = false;

// Database connection
mysql_connect( $dbhost, $dbuser, $dbpasswd )
or die ( "Could not connect to database server" );
mysql_select_db( $dbname )
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
        mysql_query($query);
      }
      $query = '';
    }
  }
}
// +-----------------------------------------------------------------------+
// |                        template initialization                        |
// +-----------------------------------------------------------------------+
$template = setup_style('default');
$template->set_filenames(array('upgrade'=>'upgrade.tpl'));
$template->assign_vars(array('RELEASE'=>PHPWG_VERSION));
// +-----------------------------------------------------------------------+
// |                          versions upgradable                          |
// +-----------------------------------------------------------------------+
$versions = array();
$path = PHPWG_ROOT_PATH.'install';
if ($contents = opendir($path))
{
  while (($node = readdir($contents)) !== false)
  {
    if (is_file($path.'/'.$node)
        and preg_match('/^upgrade_(.*?)\.php$/', $node, $match))
    {
      array_push($versions, $match[1]);
    }
  }
}
natcasesort($versions);
// +-----------------------------------------------------------------------+
// |                            upgrade choice                             |
// +-----------------------------------------------------------------------+
if (!isset($_GET['version']))
{
  $template->assign_block_vars('choices', array());
  foreach ($versions as $version)
  {
    $template->assign_block_vars(
      'choices.choice',
      array(
        'URL' => PHPWG_ROOT_PATH.'upgrade.php?version='.$version,
        'VERSION' => $version
        ));
  }
}
// +-----------------------------------------------------------------------+
// |                            upgrade launch                             |
// +-----------------------------------------------------------------------+
else
{
  $upgrade_file = $path.'/upgrade_'.$_GET['version'].'.php';
  if (is_file($upgrade_file))
  {
    $page['upgrade_start'] = get_moment();
    include($upgrade_file);
    $page['upgrade_end'] = get_moment();

    $template->assign_block_vars(
      'upgrade',
      array(
        'VERSION' => $_GET['version'],
        'TOTAL_TIME' => get_elapsed_time($page['upgrade_start'],
                                         $page['upgrade_end']),
        'SQL_TIME' => number_format($page['queries_time'], 3, '.', ' ').' s',
        'NB_QUERIES' => $page['count_queries']
        ));

    if (!isset($infos))
    {
      $infos = array();
    }
    array_push(
      $infos,
      '[security] delete files "upgrade.php", "install.php" and "install"
directory');
    
    $template->assign_block_vars('upgrade.infos', array());
    
    foreach ($infos as $info)
    {
      $template->assign_block_vars('upgrade.infos.info',
                                   array('CONTENT' => $info));
    }
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