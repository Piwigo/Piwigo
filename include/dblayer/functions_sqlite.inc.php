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

define('REQUIRED_SQLITE_VERSION', '3.0.0');
define('DB_ENGINE', 'SQLite');

define('DB_REGEX_OPERATOR', 'REGEXP');
define('DB_RANDOM_FUNCTION', 'RANDOM');

/**
 *
 * simple functions
 *
 */

function pwg_db_connect($host, $user, $password, $database)
{
  global $conf;

  $db_file = sprintf('%s/%s.db', $conf['local_data_dir'], $database);

  if (script_basename()=='install') 
  {
    $sqlite_open_mode = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
  }
  else 
  {
    $sqlite_open_mode = SQLITE3_OPEN_READWRITE;
  }
  
  $link = new SQLite3($db_file, $sqlite_open_mode);
  if (!$link)
  {
    throw new  Exception('Connection to server succeed, but it was impossible to connect to database');
  }

  $link->createFunction('now', 'pwg_now', 0);
  $link->createFunction('unix_timestamp', 'pwg_unix_timestamp', 0);
  $link->createFunction('md5', 'md5', 1);
  $link->createFunction('if', 'pwg_if', 3);

  $link->createFunction('regexp', 'pwg_regexp', 2);

  return $link;
}

function pwg_db_check_version()
{
  $current_version = pwg_get_db_version();
  if (version_compare($current_version, REQUIRED_SQLITE_VERSION, '<'))
  {
    fatal_error(
      sprintf(
        'your database version is too old, you have "%s" and you need at least "%s"',
        $current_version,
        REQUIRED_SQLITE_VERSION
        )
      );
  }
}

function pwg_db_check_charset() 
{
  return true;
}

function pwg_get_db_version() 
{
  global $pwg_db_link;

  $versionInfos = $pwg_db_link->version();
  return $versionInfos['versionString'];
}

function pwg_query($query)
{
  global $conf,$page,$debug,$t2,$pwg_db_link;

  $start = get_moment();

  $truncate_pattern = '`truncate(.*)`i';
  $insert_pattern = '`(INSERT INTO [^)]*\)\s*VALUES)(\([^)]*\))\s*,\s*(.*)`mi';  

  if (preg_match($truncate_pattern, $query, $matches))
  {
    $query = str_replace('TRUNCATE TABLE', 'DELETE FROM', $query);
    $truncate_query = true;
    ($result = $pwg_db_link->exec($query)) or die($query."\n<br>".$pwg_db_link->lastErrorMsg());
  }
  elseif (preg_match($insert_pattern, $query, $matches))
  {
    $base_query = substr($query, 0, strlen($matches[1])+1);
    $values_pattern = '`\)\s*,\s*\(`';
    $values = preg_split($values_pattern, substr($query, strlen($matches[1])+1));
    $values[0] = substr($values[0], 1);
    $values[count($values)-1] = substr($values[count($values)-1], 
				     0, 
				     strlen($values[count($values)-1])-1
				     );
    for ($n=0;$n<count($values);$n++)
    {
      $query = $base_query . '('. $values[$n] . ")\n;";
      ($result = $pwg_db_link->query($query)) 
	or die($query."\n<br>".$pwg_db_link->lastErrorMsg());
    }
  }
  else 
  {
    ($result = $pwg_db_link->query($query)) 
      or die($query."\n<br>".$pwg_db_link->lastErrorMsg());
  }

  $time = get_moment() - $start;

  if (!isset($page['count_queries']))
  {
    $page['count_queries'] = 0;
    $page['queries_time'] = 0;
  }

  $page['count_queries']++;
  $page['queries_time']+= $time;

  if ($conf['show_queries'])
  {
    $output = '';
    $output.= '<pre>['.$page['count_queries'].'] ';
    $output.= "\n".$query;
    $output.= "\n".'(this query time : ';
    $output.= '<b>'.number_format($time, 3, '.', ' ').' s)</b>';
    $output.= "\n".'(total SQL time  : ';
    $output.= number_format($page['queries_time'], 3, '.', ' ').' s)';
    $output.= "\n".'(total time      : ';
    $output.= number_format( ($time+$start-$t2), 3, '.', ' ').' s)';
    if ( $result!=null and preg_match('/\s*SELECT\s+/i',$query) )
    {
      $output.= "\n".'(num rows        : ';
      $output.= pwg_db_num_rows($result).' )';
    }
    elseif ( $result!=null
      and preg_match('/\s*INSERT|UPDATE|REPLACE|DELETE\s+/i',$query) 
      and !isset($truncate_query))
    {
      $output.= "\n".'(affected rows   : ';
      $output.= pwg_db_changes($result).' )';
    }
    $output.= "</pre>\n";

    $debug .= $output;
  }

  return $result;
}

function pwg_db_nextval($column, $table)
{
  $query = '
SELECT MAX('.$column.')+1
  FROM '.$table;
  list($next) = pwg_db_fetch_row(pwg_query($query));
  if (is_null($next))
  {
    $next = 1;
  }
  return $next;
}

/**
 *
 * complex functions
 *
 */

function pwg_db_changes(SQLite3Result $result=null) 
{
  global $pwg_db_link;

  return $pwg_db_link->changes();
}

function pwg_db_num_rows($result) 
{ 
  return $result->numColumns();
}

function pwg_db_fetch_assoc($result)
{
  return $result->fetchArray(SQLITE3_ASSOC);
}

function pwg_db_fetch_row($result)
{
  return $result->fetchArray(SQLITE3_NUM);
}

function pwg_db_fetch_object($result)
{
  return $result;
}

function pwg_db_free_result($result) 
{
}

function pwg_db_real_escape_string($s)
{
  global $pwg_db_link;

  return $pwg_db_link->escapeString($s);
}

function pwg_db_insert_id($table=null, $column='id')
{
  global $pwg_db_link;

  return $pwg_db_link->lastInsertRowID();
}

/**
 *
 * complex functions
 *
 */

/**
 * creates an array based on a query, this function is a very common pattern
 * used here
 *
 * @param string $query
 * @param string $fieldname
 * @return array
 */
function array_from_query($query, $fieldname)
{
  $array = array();

  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($array, $row[$fieldname]);
  }

  return $array;
}

define('MASS_UPDATES_SKIP_EMPTY', 1);
/**
 * updates multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array datas
 * @param int flags - if MASS_UPDATES_SKIP_EMPTY - empty values do not overwrite existing ones
 * @return void
 */
function mass_updates($tablename, $dbfields, $datas, $flags=0)
{
  if (count($datas) == 0)
    return;

  foreach ($datas as $data)
  {
    $query = '
UPDATE '.$tablename.'
  SET ';
    $is_first = true;
    foreach ($dbfields['update'] as $key)
    {
      $separator = $is_first ? '' : ",\n    ";
      
      if (isset($data[$key]) and $data[$key] != '')
      {
	$query.= $separator.$key.' = \''.$data[$key].'\'';
      }
      else
      {
	if ($flags & MASS_UPDATES_SKIP_EMPTY )
	  continue; // next field
	$query.= "$separator$key = NULL";
      }
      $is_first = false;
    }
    if (!$is_first)
    {// only if one field at least updated
      $query.= '
  WHERE ';
      $is_first = true;
      foreach ($dbfields['primary'] as $key)
      {
	if (!$is_first)
        {
	  $query.= ' AND ';
	}
	if ( isset($data[$key]) )
        {
	  $query.= $key.' = \''.$data[$key].'\'';
	}
	else
        {
	  $query.= $key.' IS NULL';
	}
	$is_first = false;
      }
      pwg_query($query);
    }
  }
}


/**
 * inserts multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array inserts
 * @return void
 */

function mass_inserts($table_name, $dbfields, $datas)
{
  if (count($datas) != 0)
  {
    $first = true;

    $packet_size = 16777216;
    $packet_size = $packet_size - 2000; // The last list of values MUST not exceed 2000 character*/
    $query = '';

    foreach ($datas as $insert)
    {
      if (strlen($query) >= $packet_size)
      {
        pwg_query($query);
        $first = true;
      }

      if ($first)
      {
        $query = '
INSERT INTO '.$table_name.'
  ('.implode(',', $dbfields).')
  VALUES';
        $first = false;
      }
      else
      {
        $query .= '
  , ';
      }

      $query .= '(';
      foreach ($dbfields as $field_id => $dbfield)
      {
        if ($field_id > 0)
        {
          $query .= ',';
        }

        if (!isset($insert[$dbfield]) or $insert[$dbfield] === '')
        {
          $query .= 'NULL';
        }
        else
        {
          $query .= "'".$insert[$dbfield]."'";
        }
      }
      $query .= ')';
    }
    pwg_query($query);
  }
}

/**
 * Do maintenance on all PWG tables
 *
 * @return none
 */
function do_maintenance_all_tables()
{
  global $prefixeTable, $page;

  $all_tables = array();

  // List all tables
  $query = 'SELECT name FROM SQLITE_MASTER
WHERE name LIKE \''.$prefixeTable.'%\'';

  $all_tables = array_from_query($query, 'name');
  foreach ($all_tables as $table_name)
  {
    $query = 'VACUUM '.$table_name.';';
    $result = pwg_query($query);
  }
  
  array_push($page['infos'],
	     l10n('All optimizations have been successfully completed.')
	     );
}

function pwg_db_concat($array)
{
  return implode($array, ' || ');
}

function pwg_db_concat_ws($array, $separator)
{
  $glue = sprintf(' || \'%s\' || ', $separator);

  return implode($array, $glue);
}

function pwg_db_cast_to_text($string)
{
  return $string;
}

/**
 * returns an array containing the possible values of an enum field
 *
 * @param string tablename
 * @param string fieldname
 */
function get_enums($table, $field)
{
  $Enums['categories']['status'] = array('public', 'private');
  $Enums['history']['section'] = array('categories','tags','search','list','favorites','most_visited','best_rated','recent_pics','recent_cats');
  $Enums['user_infos']['status'] = array('webmaster','admin','normal','generic','guest');
  $Enums['image']['type'] = array('picture','high','other');
  $Enums['plugins']['state'] = array('active', 'inactive');
  $Enums['user_cache_image']['access_type'] = array('NOT IN','IN');

  $table = str_replace($GLOBALS['prefixeTable'], '', $table);
  if (isset($Enums[$table][$field])) {
    return $Enums[$table][$field];
  } else {
    return array();
  }
}

// get_boolean transforms a string to a boolean value. If the string is
// "false" (case insensitive), then the boolean value false is returned. In
// any other case, true is returned.
function get_boolean( $string )
{
  $boolean = true;
  if ('f' === $string || 'false' === $string)
  {
    $boolean = false;
  }
  return $boolean;
}

/**
 * returns boolean string 'true' or 'false' if the given var is boolean
 *
 * @param mixed $var
 * @return mixed
 */
function boolean_to_string($var)
{
  if (is_bool($var))
  {
    return $var ? 'true' : 'false';
  }
  else
  {
    return $var;
  }
}

/**
 *
 * interval and date functions 
 *
 */

function pwg_db_get_recent_period_expression($period, $date='CURRENT_DATE')
{
  if ($date!='CURRENT_DATE')
  {
    $date = '\''.$date.'\'';
  }

  return 'date('.$date.',\''.-$period.' DAY\')';
}

function pwg_db_get_recent_period($period, $date='CURRENT_DATE')
{
  $query = 'select '.pwg_db_get_recent_period_expression($period, $date);
  list($d) = pwg_db_fetch_row(pwg_query($query));

  return $d;
}

function pwg_db_get_date_YYYYMM($date)
{
  return 'strftime(\'%Y%m\','.$date.')';
}

function pwg_db_get_date_MMDD($date)
{
  return 'strftime(\'%m%d\','.$date.')';
}

function pwg_db_get_year($date)
{
  return 'strftime(\'%Y\','.$date.')';
}

function pwg_db_get_month($date)
{
  return 'strftime(\'%m\','.$date.')';
}

function pwg_db_get_week($date, $mode=null)
{
  return 'strftime(\'%W\','.$date.')';
}

function pwg_db_get_dayofmonth($date)
{
  return 'strftime(\'%d\','.$date.')';
}

function pwg_db_get_dayofweek($date)
{
  return 'strftime(\'%w\','.$date.')';
}

function pwg_db_get_weekday($date)
{
  return 'strftime(\'%w\',date('.$date.',\'-1 DAY\'))';
}

// my_error returns (or send to standard output) the message concerning the
// error occured for the last mysql query.
function my_error($header, $die)
{
  global $pwg_db_link;

  $error = '';
  if (isset($pwg_db_link)) 
  {
    $error .= '[sqlite error]'.$pwg_db_link->lastErrorMsg()."\n";
  }

  $error .= $header;

  if ($die)
  {
    fatal_error($error);
  }
  echo("<pre>");
  trigger_error($error, E_USER_WARNING);
  echo("</pre>");
}

// sqlite create functions
function pwg_now()
{
  return date('Y-m-d H:i:s');
}

function pwg_unix_timestamp()
{
  return time();
}

function pwg_if($expression, $value1, $value2) 
{
  if ($expression)
  {
    return $value1;
  }
  else
  {
    return $value2;
  }
} 

function pwg_regexp($pattern, $string)
{
  $pattern = sprintf('`%s`', $pattern);
  return preg_match($pattern, $string);
}
?>
