<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

function upgrade65_change_table_to_blob($table, $field_definitions)
{
  $types = array('varchar'  => 'varbinary',
    'text' => 'blob',
    'mediumtext' => 'mediumblob',
    'longtext' => 'longblob');

  $changes=array();
  foreach( $field_definitions as $row)
  {
    if ( !isset($row['Collation']) or $row['Collation']=='NULL' )
      continue;
    list ($type) = explode('(', $row['Type']);
    if (!isset($types[$type]))
      continue; // no need
    $binaryType = preg_replace('/'. $type .'/i', $types[$type], $row['Type'] );
    $changes[] = 'MODIFY COLUMN '.$row['Field'].' '.$binaryType;
  }
  if (count($changes))
  {
    $query = 'ALTER TABLE '.$table.' '.implode(', ', $changes);
    pwg_query($query);
  }
}

function upgrade65_change_table_to_charset($table, $field_definitions, $db_charset)
{
  $changes=array();
  foreach( $field_definitions as $row)
  {
    if ( !isset($row['Collation']) or $row['Collation']=='NULL' )
      continue;
    $query = $row['Field'].' '.$row['Type'];
    $query .= ' CHARACTER SET '.$db_charset;
    if (strpos($row['Collation'],'_bin')!==false)
    {
      $query .= ' BINARY';
    }
    if ($row['Null']!='YES')
    {
      $query.=' NOT NULL';
      if (isset($row['Default']))
        $query.=' DEFAULT "'.addslashes($row['Default']).'"';
    }
    else
    {
      if (!isset($row['Default']))
        $query.=' DEFAULT NULL';
      else
        $query.=' DEFAULT "'.addslashes($row['Default']).'"';
    }

    if ($row['Extra']=='auto_increment')
    {
      $query.=' auto_increment';
    }
    $changes[] = 'MODIFY COLUMN '.$query;
  }

  if (count($changes))
  {
    $query = 'ALTER TABLE `'.$table.'` '.implode(', ', $changes);
    pwg_query($query);
  }
}


$upgrade_description = 'PWG charset migration';
// +-----------------------------------------------------------------------+
// |                            Upgrade content                            |
// +-----------------------------------------------------------------------+
if ( !defined('PWG_CHARSET') )
{
  $upgrade_log = '';

// +-----------------------------------------------------------------------+
// load the config file
  $config_file = PHPWG_ROOT_PATH.'include/mysql.inc.php';
  $config_file_contents = @file_get_contents($config_file);
  if ($config_file_contents === false)
  {
    die('CANNOT LOAD '.$config_file);
  }
  $php_end_tag = strrpos($config_file_contents, '?'.'>');
  if ($php_end_tag === false)
  {
    die('CANNOT FIND PHP END TAG IN '.$config_file);
  }
  if (!is_writable($config_file))
  {
    die('FILE NOT WRITABLE '.$config_file);
  }


// +-----------------------------------------------------------------------+
// load all the user languages
  $all_langs=array();
  $query='
SELECT language, COUNT(user_id) AS count FROM '.USER_INFOS_TABLE.'
  GROUP BY language';
  $result = pwg_query($query);
  while ( $row=mysql_fetch_assoc($result) )
  {
    $lang = $row["language"];
    $lang_def = explode('.', $lang);
    if ( count($lang_def)==2 )
    {
      $new_lang = $lang_def[0];
      $charset = strtolower($lang_def[1]);
    }
    else
    {
      $new_lang = 'en_UK';
      $charset = 'iso-8859-1';
    }
    $all_langs[$lang] = array(
      'count' => $row['count'],
      'new_lang' => $new_lang,
      'charset' => $charset,
      );
    $upgrade_log .= ">>user_lang\t".$lang."\t".$row['count']."\n";
  }
  $upgrade_log .= "\n";


// +-----------------------------------------------------------------------+
// get admin charset
  include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
  @include(PHPWG_ROOT_PATH. 'include/config_local.inc.php');
  $admin_charset='iso-8859-1';
  $query='
SELECT language FROM '.USER_INFOS_TABLE.'
  WHERE user_id='.$conf['webmaster_id'];
  $result = pwg_query($query);
  if (mysql_num_rows($result)==0)
  {
    $query='
SELECT language FROM '.USER_INFOS_TABLE.'
  WHERE status="webmaster" and adviser="false"
  LIMIT 1';
    $result = pwg_query($query);
  }

  if ( $row=mysql_fetch_assoc($result) )
  {
    $admin_charset = $all_langs[$row['language']]['charset'];
  }
  $upgrade_log .= ">>admin_charset\t".$admin_charset."\n";


// +-----------------------------------------------------------------------+
// get mysql version and structure of tables
  $mysql_version = mysql_get_server_info();
  $upgrade_log .= ">>mysql_ver\t".$mysql_version."\n";

  $all_tables = array();
  $query = 'SHOW TABLES LIKE "'.$prefixeTable.'%"';
  $result = pwg_query($query);
  while ( $row=mysql_fetch_array($result) )
  {
    array_push($all_tables, $row[0]);
  }

  $all_tables_definition = array();
  foreach( $all_tables as $table)
  {
    $query = 'SHOW FULL COLUMNS FROM '.$table;
    $result = pwg_query($query);
    $field_definitions=array();
    while ( $row=mysql_fetch_array($result) )
    {
      if ( !isset($row['Collation']) or $row['Collation']=='NULL' )
        continue;
      array_push($field_definitions, $row);
    }
    $all_tables_definition[$table] = $field_definitions;
  }

// +-----------------------------------------------------------------------+
// calculate the result and convert the tables

//tables that can be converted without going through binary (they contain only ascii data)
  $safe_tables=array('history','history_backup','history_summary','old_permalinks','plugins','rate','upgrade','user_cache','user_feed','user_infos','user_mail_notification', 'users', 'waiting','ws_access');
  $safe_tables=array_flip($safe_tables);

  $pwg_charset = 'iso-8859-1';
  $db_charset = 'latin1';
  $db_collate = '';
  if ( version_compare($mysql_version, '4.1', '<') )
  { // below 4.1 no charset support
    $upgrade_log .= "< conversion\tnothing\n";
  }
  elseif ($admin_charset=='iso-8859-1')
  {
    $pwg_charset = 'utf-8';
    $db_charset = 'utf8';
    foreach($all_tables as $table)
    {
      upgrade65_change_table_to_charset($table, $all_tables_definition[$table], 'utf8' );
      $query = 'ALTER TABLE '.$table.' DEFAULT CHARACTER SET utf8';
      pwg_query($query);
    }
    $upgrade_log .= "< conversion\tchange utf8\n";
  }
/*ALTER TABLE tbl_name CONVERT TO CHARACTER SET charset_name; (or change column character set)

Warning: The preceding operation converts column values between the character sets. This is not what you want if you have a column in one character set (like latin1) but the stored values actually use some other, incompatible character set (like utf8). In this case, you have to do the following for each such column:

ALTER TABLE t1 CHANGE c1 c1 BLOB;
ALTER TABLE t1 CHANGE c1 c1 TEXT CHARACTER SET utf8;
*/
  elseif ( $admin_charset=='utf-8')
  {
    $pwg_charset = 'utf-8';
    $db_charset = 'utf8';
    foreach($all_tables as $table)
    {
      if ( !isset($safe_tables[ substr($table, strlen($prefixeTable)) ]) )
        upgrade65_change_table_to_blob($table, $all_tables_definition[$table] );
      upgrade65_change_table_to_charset($table, $all_tables_definition[$table], 'utf8' );
      $query = 'ALTER TABLE '.$table.' DEFAULT CHARACTER SET utf8';
      pwg_query($query);
    }
    $upgrade_log .= "< conversion\tchange binary\n";
    $upgrade_log .= "< conversion\tchange utf8\n";
  }
  elseif ( $admin_charset=='iso-8859-2'/*Central European*/)
  {
    $pwg_charset = 'utf-8';
    $db_charset = 'utf8';
    foreach($all_tables as $table)
    {
      if ( !isset($safe_tables[ substr($table, strlen($prefixeTable)) ]) )
      {
        upgrade65_change_table_to_blob($table, $all_tables_definition[$table] );
        upgrade65_change_table_to_charset($table, $all_tables_definition[$table], 'latin2' );
      }
      upgrade65_change_table_to_charset($table, $all_tables_definition[$table], 'utf8' );
      $query = 'ALTER TABLE '.$table.' DEFAULT CHARACTER SET utf8';
      pwg_query($query);
    }
    $upgrade_log .= "< conversion\tchange binary\n";
    $upgrade_log .= "< conversion\tchange latin2\n";
    $upgrade_log .= "< conversion\tchange utf8\n";
  }


// +-----------------------------------------------------------------------+
// write the result to file and update #user_infos.language
  $config_file_contents =
    substr($config_file_contents, 0, $php_end_tag).'
define(\'PWG_CHARSET\', \''.$pwg_charset.'\');
define(\'DB_CHARSET\',  \''.$db_charset.'\');
define(\'DB_COLLATE\',  \'\');
'.substr($config_file_contents, $php_end_tag);

  $fp = @fopen( $config_file, 'w' );
  @fputs($fp, $config_file_contents, strlen($config_file_contents));
  @fclose($fp);

  foreach ($all_langs as $old_lang=>$lang_data)
  {
    $query='
  UPDATE '.USER_INFOS_TABLE.' SET language="'.$lang_data['new_lang'].'"
    WHERE language="'.$old_lang.'"';
    pwg_query($query);
  }

  define('PWG_CHARSET', $pwg_charset);
  define('DB_CHARSET',  $db_charset);
  define('DB_COLLATE',  '');

  echo $upgrade_log;
  $fp = @fopen( PHPWG_ROOT_PATH.'upgrade65.log', 'w' );
  if ($fp)
  {
    @fputs($fp, $upgrade_log, strlen($upgrade_log));
    @fclose($fp);
  }

echo
"\n"
.'"'.$upgrade_description.'"'.' ended'
."\n"
;
}
else
{
  echo 'PWG_CHARSET already defined - nada';
}
?>
