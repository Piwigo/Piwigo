<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

define('DB_ENGINE', 'MySQL');
define('REQUIRED_MYSQL_VERSION', '5.0.0');

define('DB_REGEX_OPERATOR', 'REGEXP');
define('DB_RANDOM_FUNCTION', 'RAND');

/**
 *
 * simple functions
 *
 */

function pwg_db_connect($host, $user, $password, $database)
{  
  $link = @mysql_connect($host, $user, $password);
  if (!$link)
  {
    throw new Exception("Can't connect to server");
  }
  if (mysql_select_db($database, $link))
  {
    return $link;
  }
  else
  {
    throw new Exception('Connection to server succeed, but it was impossible to connect to database');
  }
}

function pwg_db_check_charset() 
{
  $db_charset = 'utf8';
  if (defined('DB_CHARSET') and DB_CHARSET != '')
  {
    $db_charset = DB_CHARSET;
  }
  pwg_query('SET NAMES "'.$db_charset.'"');
}

function pwg_db_check_version()
{
  $current_mysql = pwg_get_db_version();
  if (version_compare($current_mysql, REQUIRED_MYSQL_VERSION, '<'))
  {
    fatal_error(
      sprintf(
        'your MySQL version is too old, you have "%s" and you need at least "%s"',
        $current_mysql,
        REQUIRED_MYSQL_VERSION
        )
      );
  }
}

function pwg_get_db_version() 
{
  return mysql_get_server_info();
}

function pwg_query($query)
{
  global $conf,$page,$debug,$t2;

  $start = microtime(true);
  ($result = mysql_query($query)) or my_error($query, $conf['die_on_sql_error']);

  $time = microtime(true) - $start;

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
      $output.= mysql_num_rows($result).' )';
    }
    elseif ( $result!=null
      and preg_match('/\s*INSERT|UPDATE|REPLACE|DELETE\s+/i',$query) )
    {
      $output.= "\n".'(affected rows   : ';
      $output.= mysql_affected_rows().' )';
    }
    $output.= "</pre>\n";

    $debug .= $output;
  }

  return $result;
}

function pwg_db_nextval($column, $table)
{
  $query = '
SELECT IF(MAX('.$column.')+1 IS NULL, 1, MAX('.$column.')+1)
  FROM '.$table;
  list($next) = pwg_db_fetch_row(pwg_query($query));

  return $next;
}

function pwg_db_changes($result) 
{
  return mysql_affected_rows();
}

function pwg_db_num_rows($result) 
{
  return mysql_num_rows($result);
}

function pwg_db_fetch_assoc($result)
{
  return mysql_fetch_assoc($result);
}

function pwg_db_fetch_row($result)
{
  return mysql_fetch_row($result);
}

function pwg_db_fetch_object($result)
{
  return mysql_fetch_object($result);
}

function pwg_db_free_result($result) 
{
  return mysql_free_result($result);
}

function pwg_db_real_escape_string($s)
{
  return mysql_real_escape_string($s);
}

function pwg_db_insert_id($table=null, $column='id')
{
  return mysql_insert_id();
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
  while ($row = mysql_fetch_assoc($result))
  {
    $array[] = $row[$fieldname];
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
  
  // depending on the MySQL version, we use the multi table update or N update queries
  if (count($datas) < 10)
  {
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
          if ( $flags & MASS_UPDATES_SKIP_EMPTY )
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
    } // foreach update
  } // if mysql_ver or count<X
  else
  {
    // creation of the temporary table
    $query = '
SHOW FULL COLUMNS FROM '.$tablename;
    $result = pwg_query($query);
    $columns = array();
    $all_fields = array_merge($dbfields['primary'], $dbfields['update']);
    while ($row = pwg_db_fetch_assoc($result))
    {
      if (in_array($row['Field'], $all_fields))
      {
        $column = $row['Field'];
        $column.= ' '.$row['Type'];

        $nullable = true;
        if (!isset($row['Null']) or $row['Null'] == '' or $row['Null']=='NO')
        {
          $column.= ' NOT NULL';
          $nullable = false;
        }
        if (isset($row['Default']))
        {
          $column.= " default '".$row['Default']."'";
        }
        elseif ($nullable)
        {
          $column.= " default NULL";
        }
        if (isset($row['Collation']) and $row['Collation'] != 'NULL')
        {
          $column.= " collate '".$row['Collation']."'";
        }
        array_push($columns, $column);
      }
    }

    $temporary_tablename = $tablename.'_'.micro_seconds();

    $query = '
CREATE TABLE '.$temporary_tablename.'
(
  '.implode(",\n  ", $columns).',
  UNIQUE KEY the_key ('.implode(',', $dbfields['primary']).')
)';

    pwg_query($query);
    mass_inserts($temporary_tablename, $all_fields, $datas);
    if ( $flags & MASS_UPDATES_SKIP_EMPTY )
      $func_set = create_function('$s', 'return "t1.$s = IFNULL(t2.$s, t1.$s)";');
    else
      $func_set = create_function('$s', 'return "t1.$s = t2.$s";');

    // update of images table by joining with temporary table
    $query = '
UPDATE '.$tablename.' AS t1, '.$temporary_tablename.' AS t2
  SET '.
      implode(
        "\n    , ",
        array_map($func_set,$dbfields['update'])
        ).'
  WHERE '.
      implode(
        "\n    AND ",
        array_map(
          create_function('$s', 'return "t1.$s = t2.$s";'),
          $dbfields['primary']
          )
        );
    pwg_query($query);
    $query = '
DROP TABLE '.$temporary_tablename;
    pwg_query($query);
  }
}

/**
 * updates one line in a table
 *
 * @param string table_name
 * @param array set_fields
 * @param array where_fields
 * @param int flags - if MASS_UPDATES_SKIP_EMPTY - empty values do not overwrite existing ones
 * @return void
 */
function single_update($tablename, $set_fields, $where_fields, $flags=0)
{
  if (count($set_fields) == 0)
  {
    return;
  }

  $query = '
UPDATE '.$tablename.'
  SET ';
  $is_first = true;
  foreach ($set_fields as $key => $value)
  {
    $separator = $is_first ? '' : ",\n    ";

    if (isset($value) and $value != '')
    {
      $query.= $separator.$key.' = \''.$value.'\'';
    }
    else
    {
      if ( $flags & MASS_UPDATES_SKIP_EMPTY )
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
    foreach ($where_fields as $key => $value)
    {
      if (!$is_first)
      {
        $query.= ' AND ';
      }
      if ( isset($value) )
      {
        $query.= $key.' = \''.$value.'\'';
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


/**
 * inserts multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array inserts
 * @return void
 */
function mass_inserts($table_name, $dbfields, $datas, $options=array())
{
  $ignore = '';
  if (isset($options['ignore']) and $options['ignore'])
  {
    $ignore = 'IGNORE';
  }
  
  if (count($datas) != 0)
  {
    $first = true;

    $query = 'SHOW VARIABLES LIKE \'max_allowed_packet\'';
    list(, $packet_size) = pwg_db_fetch_row(pwg_query($query));
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
INSERT '.$ignore.' INTO '.$table_name.'
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
 * inserts one line in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array insert
 * @return void
 */
function single_insert($table_name, $data)
{
  if (count($data) != 0)
  {
    $query = '
INSERT INTO '.$table_name.'
  ('.implode(',', array_keys($data)).')
  VALUES';

    $query .= '(';
    $is_first = true;
    foreach ($data as $key => $value)
    {
      if (!$is_first)
      {
        $query .= ',';
      }
      else
      {
        $is_first = false;
      }
      
      if ($value === '')
      {
        $query .= 'NULL';
      }
      else
      {
        $query .= "'".$value."'";
      }
    }
    $query .= ')';
    
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
  $query = 'SHOW TABLES LIKE \''.$prefixeTable.'%\'';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_row($result))
  {
    array_push($all_tables, $row[0]);
  }

  // Repair all tables
  $query = 'REPAIR TABLE '.implode(', ', $all_tables);
  $mysql_rc = pwg_query($query);

  // Re-Order all tables
  foreach ($all_tables as $table_name)
  {
    $all_primary_key = array();

    $query = 'DESC '.$table_name.';';
    $result = pwg_query($query);
    while ($row = pwg_db_fetch_assoc($result))
    {
      if ($row['Key'] == 'PRI')
      {
        array_push($all_primary_key, $row['Field']);
      }
    }

    if (count($all_primary_key) != 0)
    {
      $query = 'ALTER TABLE '.$table_name.' ORDER BY '.implode(', ', $all_primary_key).';';
      $mysql_rc = $mysql_rc && pwg_query($query);
    }
  }

  // Optimize all tables
  $query = 'OPTIMIZE TABLE '.implode(', ', $all_tables);
  $mysql_rc = $mysql_rc && pwg_query($query);
  if ($mysql_rc)
  {
    array_push(
          $page['infos'],
          l10n('All optimizations have been successfully completed.')
          );
  }
  else
  {
    array_push(
          $page['errors'],
          l10n('Optimizations have been completed with some errors.')
          );
  }
}

function pwg_db_concat($array)
{
  $string = implode($array, ',');
  return 'CONCAT('. $string.')';
}

function pwg_db_concat_ws($array, $separator)
{
  $string = implode($array, ',');
  return 'CONCAT_WS(\''.$separator.'\','. $string.')';
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
  // retrieving the properties of the table. Each line represents a field :
  // columns are 'Field', 'Type'
  $result = pwg_query('desc '.$table);
  while ($row = pwg_db_fetch_assoc($result))
  {
    // we are only interested in the the field given in parameter for the
    // function
    if ($row['Field'] == $field)
    {
      // retrieving possible values of the enum field
      // enum('blue','green','black')
      $options = explode(',', substr($row['Type'], 5, -1));
      foreach ($options as $i => $option)
      {
        $options[$i] = str_replace("'", '',$option);
      }
    }
  }
  pwg_db_free_result($result);
  return $options;
}

/**
 * Smartly checks if a variable is equivalent to true or false
 *
 * @param mixed input
 * @return bool
 */
function get_boolean($input)
{
  if ('false' === strtolower($input))
  {
    return false;
  }

  return (bool)$input;
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

  return 'SUBDATE('.$date.',INTERVAL '.$period.' DAY)';
}

function pwg_db_get_recent_period($period, $date='CURRENT_DATE')
{
  $query = '
SELECT '.pwg_db_get_recent_period_expression($period);
  list($d) = pwg_db_fetch_row(pwg_query($query));

  return $d;
}

function pwg_db_get_flood_period_expression($seconds)
{
  return 'SUBDATE(now(), INTERVAL '.$seconds.' SECOND)';
}

function pwg_db_get_hour($date) 
{
  return 'hour('.$date.')';
}

function pwg_db_get_date_YYYYMM($date)
{
  return 'DATE_FORMAT('.$date.', \'%Y%m\')';
}

function pwg_db_get_date_MMDD($date)
{
  return 'DATE_FORMAT('.$date.', \'%m%d\')';
}

function pwg_db_get_year($date)
{
  return 'YEAR('.$date.')';
}

function pwg_db_get_month($date)
{
  return 'MONTH('.$date.')';
}

function pwg_db_get_week($date, $mode=null)
{
  if ($mode)
  {
    return 'WEEK('.$date.', '.$mode.')';
  }
  else
  {
    return 'WEEK('.$date.')';
  }
}

function pwg_db_get_dayofmonth($date)
{
  return 'DAYOFMONTH('.$date.')';
}

function pwg_db_get_dayofweek($date)
{
  return 'DAYOFWEEK('.$date.')';
}

function pwg_db_get_weekday($date)
{
  return 'WEEKDAY('.$date.')';
}

function pwg_db_date_to_ts($date) 
{
  return 'UNIX_TIMESTAMP('.$date.')';
}

// my_error returns (or send to standard output) the message concerning the
// error occured for the last mysql query.
function my_error($header, $die)
{
  $error = "[mysql error ".mysql_errno().'] '.mysql_error()."\n";
  $error .= $header;

  if ($die)
  {
    fatal_error($error);
  }
  echo("<pre>");
  trigger_error($error, E_USER_WARNING);
  echo("</pre>");
}

?>