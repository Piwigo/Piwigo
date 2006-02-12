<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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

include_once( PHPWG_ROOT_PATH .'include/functions_user.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_session.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_category.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_xml.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_group.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_html.inc.php' );

//----------------------------------------------------------- generic functions

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
  while ($row = mysql_fetch_array($result))
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
  mysql_free_result($result);
  return $options;
}

// get_boolean transforms a string to a boolean value. If the string is
// "false" (case insensitive), then the boolean value false is returned. In
// any other case, true is returned.
function get_boolean( $string )
{
  $boolean = true;
  if ( preg_match( '/^false$/i', $string ) )
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
    if ($var)
    {
      return 'true';
    }
    else
    {
      return 'false';
    }
  }
  else
  {
    return $var;
  }
}

// The function get_moment returns a float value coresponding to the number
// of seconds since the unix epoch (1st January 1970) and the microseconds
// are precised : e.g. 1052343429.89276600
function get_moment()
{
  $t1 = explode( ' ', microtime() );
  $t2 = explode( '.', $t1[0] );
  $t2 = $t1[1].'.'.$t2[1];
  return $t2;
}

// The function get_elapsed_time returns the number of seconds (with 3
// decimals precision) between the start time and the end time given.
function get_elapsed_time( $start, $end )
{
  return number_format( $end - $start, 3, '.', ' ').' s';
}

// - The replace_space function replaces space and '-' characters
//   by their HTML equivalent  &nbsb; and &minus;
// - The function does not replace characters in HTML tags
// - This function was created because IE5 does not respect the
//   CSS "white-space: nowrap;" property unless space and minus
//   characters are replaced like this function does.
// - Example :
//                 <div class="foo">My friend</div>
//               ( 01234567891111111111222222222233 )
//               (           0123456789012345678901 )
// becomes :
//             <div class="foo">My&nbsp;friend</div>
function replace_space( $string )
{
  //return $string;
  $return_string = '';
  // $remaining is the rest of the string where to replace spaces characters
  $remaining = $string;
  // $start represents the position of the next '<' character
  // $end   represents the position of the next '>' character
  $start = 0;
  $end = 0;
  $start = strpos ( $remaining, '<' ); // -> 0
  $end   = strpos ( $remaining, '>' ); // -> 16
  // as long as a '<' and his friend '>' are found, we loop
  while ( is_numeric( $start ) and is_numeric( $end ) )
  {
    // $treatment is the part of the string to treat
    // In the first loop of our example, this variable is empty, but in the
    // second loop, it equals 'My friend'
    $treatment = substr ( $remaining, 0, $start );
    // Replacement of ' ' by his equivalent '&nbsp;'
    $treatment = str_replace( ' ', '&nbsp;', $treatment );
    $treatment = str_replace( '-', '&minus;', $treatment );
    // composing the string to return by adding the treated string and the
    // following HTML tag -> 'My&nbsp;friend</div>'
    $return_string.= $treatment.substr( $remaining, $start, $end-$start+1 );
    // the remaining string is deplaced to the part after the '>' of this
    // loop
    $remaining = substr ( $remaining, $end + 1, strlen( $remaining ) );
    $start = strpos ( $remaining, '<' );
    $end   = strpos ( $remaining, '>' );
  }
  $treatment = str_replace( ' ', '&nbsp;', $remaining );
  $treatment = str_replace( '-', '&minus;', $treatment );
  $return_string.= $treatment;

  return $return_string;
}

// get_extension returns the part of the string after the last "."
function get_extension( $filename )
{
  return substr( strrchr( $filename, '.' ), 1, strlen ( $filename ) );
}

// get_filename_wo_extension returns the part of the string before the last
// ".".
// get_filename_wo_extension( 'test.tar.gz' ) -> 'test.tar'
function get_filename_wo_extension( $filename )
{
  return substr( $filename, 0, strrpos( $filename, '.' ) );
}

/**
 * returns an array contening sub-directories, excluding "CVS"
 *
 * @param string $dir
 * @return array
 */
function get_dirs($directory)
{
  $sub_dirs = array();

  if ($opendir = opendir($directory))
  {
    while ($file = readdir($opendir))
    {
      if ($file != '.'
          and $file != '..'
          and is_dir($directory.'/'.$file)
          and $file != 'CVS'
	  and $file != '.svn')
      {
        array_push($sub_dirs, $file);
      }
    }
  }
  return $sub_dirs;
}

// The get_picture_size function return an array containing :
//      - $picture_size[0] : final width
//      - $picture_size[1] : final height
// The final dimensions are calculated thanks to the original dimensions and
// the maximum dimensions given in parameters.  get_picture_size respects
// the width/height ratio
function get_picture_size( $original_width, $original_height,
                           $max_width, $max_height )
{
  $width = $original_width;
  $height = $original_height;
  $is_original_size = true;
                
  if ( $max_width != "" )
  {
    if ( $original_width > $max_width )
    {
      $width = $max_width;
      $height = floor( ( $width * $original_height ) / $original_width );
    }
  }
  if ( $max_height != "" )
  {
    if ( $original_height > $max_height )
    {
      $height = $max_height;
      $width = floor( ( $height * $original_width ) / $original_height );
      $is_original_size = false;
    }
  }
  if ( is_numeric( $max_width ) and is_numeric( $max_height )
       and $max_width != 0 and $max_height != 0 )
  {
    $ratioWidth = $original_width / $max_width;
    $ratioHeight = $original_height / $max_height;
    if ( ( $ratioWidth > 1 ) or ( $ratioHeight > 1 ) )
    {
      if ( $ratioWidth < $ratioHeight )
      { 
        $width = floor( $original_width / $ratioHeight );
        $height = $max_height;
      }
      else
      { 
        $width = $max_width; 
        $height = floor( $original_height / $ratioWidth );
      }
      $is_original_size = false;
    }
  }
  $picture_size = array();
  $picture_size[0] = $width;
  $picture_size[1] = $height;
  return $picture_size;
}
//-------------------------------------------- PhpWebGallery specific functions

/**
 * returns an array with a list of {language_code => language_name}
 *
 * @returns array
 */
function get_languages()
{
  $dir = opendir(PHPWG_ROOT_PATH.'language');
  $languages = array();

  while ($file = readdir($dir))
  {
    $path = PHPWG_ROOT_PATH.'language/'.$file;
    if (is_dir($path) and !is_link($path) and file_exists($path.'/iso.txt'))
    {
      list($language_name) = @file($path.'/iso.txt');
      $languages[$file] = $language_name;
    }
  }
  closedir($dir);
  @asort($languages);
  @reset($languages);

  return $languages;
}

/**
 * replaces the $search into <span style="$style">$search</span> in the
 * given $string.
 *
 * case insensitive replacements, does not replace characters in HTML tags
 *
 * @param string $string
 * @param string $search
 * @param string $style
 * @return string
 */
function add_style( $string, $search, $style )
{
  //return $string;
  $return_string = '';
  $remaining = $string;

  $start = 0;
  $end = 0;
  $start = strpos ( $remaining, '<' );
  $end   = strpos ( $remaining, '>' );
  while ( is_numeric( $start ) and is_numeric( $end ) )
  {
    $treatment = substr ( $remaining, 0, $start );
    $treatment = preg_replace( '/('.$search.')/i',
                               '<span style="'.$style.'">\\0</span>',
                               $treatment );
    $return_string.= $treatment.substr( $remaining, $start, $end-$start+1 );
    $remaining = substr ( $remaining, $end + 1, strlen( $remaining ) );
    $start = strpos ( $remaining, '<' );
    $end   = strpos ( $remaining, '>' );
  }
  $treatment = preg_replace( '/('.$search.')/i',
                             '<span style="'.$style.'">\\0</span>',
                             $remaining );
  $return_string.= $treatment;
                
  return $return_string;
}

// replace_search replaces a searched words array string by the search in
// another style for the given $string.
function replace_search( $string, $search )
{
  // FIXME : with new advanced search, this function needs a rewrite
  return $string;
  
  $words = explode( ',', $search );
  $style = 'background-color:white;color:red;';
  foreach ( $words as $word ) {
    $string = add_style( $string, $word, $style );
  }
  return $string;
}

function pwg_log( $file, $category, $picture = '' )
{
  global $conf, $user;

  if ($conf['log'])
  {
   if ( ($conf['history_admin'] ) or  ( (! $conf['history_admin'])  and ($user['status'] != 'admin')  ) )
	  {
    $login = ($user['id'] == $conf['guest_id'])
      ? 'guest' : addslashes($user['username']);
    
    $query = '
INSERT INTO '.HISTORY_TABLE.'
  (date,login,IP,file,category,picture)
  VALUES
  (NOW(),
  \''.$login.'\',
  \''.$_SERVER['REMOTE_ADDR'].'\',
  \''.addslashes($file).'\',
  \''.addslashes(strip_tags($category)).'\',
  \''.addslashes($picture).'\')
;';
    pwg_query($query);
  }
  }
}

// format_date returns a formatted date for display. The date given in
// argument can be a unixdate (number of seconds since the 01.01.1970) or an
// american format (2003-09-15). By option, you can show the time. The
// output is internationalized.
//
// format_date( "2003-09-15", 'us', true ) -> "Monday 15 September 2003 21:52"
function format_date($date, $type = 'us', $show_time = false)
{
  global $lang;

  list($year,$month,$day,$hour,$minute,$second) = array(0,0,0,0,0,0);
  
  switch ( $type )
  {
    case 'us' :
    {
      list($year,$month,$day) = explode('-', $date);
      break;
    }
    case 'unix' :
    {
      list($year,$month,$day,$hour,$minute) =
        explode('.', date('Y.n.j.G.i', $date));
      break;
    }
    case 'mysql_datetime' :
    {
      preg_match('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/',
                 $date, $out);
      list($year,$month,$day,$hour,$minute,$second) =
        array($out[1],$out[2],$out[3],$out[4],$out[5],$out[6]);
      break;
    }
  }
  $formated_date = '';
  // before 1970, Microsoft Windows can't mktime
  if ($year >= 1970)
  {
    // we ask midday because Windows think it's prior to midnight with a
    // zero and refuse to work
    $formated_date.= $lang['day'][date('w', mktime(12,0,0,$month,$day,$year))];
  }
  $formated_date.= ' '.$day;
  $formated_date.= ' '.$lang['month'][(int)$month];
  $formated_date.= ' '.$year;
  if ($show_time)
  {
    $formated_date.= ' '.$hour.':'.$minute;
  }

  return $formated_date;
}

function pwg_query($query)
{
  global $conf,$page,$debug,$t2;
  
  $start = get_moment();
  $result = mysql_query($query) or my_error($query."\n");
  
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
    $output.= "</pre>\n";
    
    $debug .= $output;
  }
  
  return $result;
}

function pwg_debug( $string )
{
  global $debug,$t2,$page;

  $now = explode( ' ', microtime() );
  $now2 = explode( '.', $now[0] );
  $now2 = $now[1].'.'.$now2[1];
  $time = number_format( $now2 - $t2, 3, '.', ' ').' s';
  $debug .= '<p>';
  $debug.= '['.$time.', ';
  $debug.= $page['count_queries'].' queries] : '.$string;
  $debug.= "</p>\n";
}

/**
 * Redirects to the given URL
 *
 * Note : once this function called, the execution doesn't go further
 * (presence of an exit() instruction.
 *
 * @param string $url
 * @return void
 */
function redirect( $url )
{
  global $user, $template, $lang_info, $conf, $lang, $t2, $page, $debug;

  // $refresh, $url_link and $title are required for creating an automated
  // refresh page in header.tpl
  $refresh = 0;
  $url_link = $url;
  $title = 'redirection';

  include( PHPWG_ROOT_PATH.'include/page_header.php' );
  
  $template->set_filenames( array( 'redirect' => 'redirect.tpl' ) );
  $template->parse('redirect');
  
  include( PHPWG_ROOT_PATH.'include/page_tail.php' );

  exit();
}

/**
 * returns $_SERVER['QUERY_STRING'] whitout keys given in parameters
 *
 * @param array $rejects
 * @returns string
 */
function get_query_string_diff($rejects = array())
{
  $query_string = '';
  
  $str = $_SERVER['QUERY_STRING'];
  parse_str($str, $vars);
  
  $is_first = true;
  foreach ($vars as $key => $value)
  {
    if (!in_array($key, $rejects))
    {
      $query_string.= $is_first ? '?' : '&amp;';
      $is_first = false;
      $query_string.= $key.'='.$value;
    }
  }

  return $query_string;
}

function url_is_remote($url)
{
  if (preg_match('/^https?:\/\/[~\/\.\w-]+$/', $url))
  {
    return true;
  }
  return false;
}

/**
 * returns available templates/themes
 */
function get_templates()
{
  return get_dirs(PHPWG_ROOT_PATH.'theme');
}
function get_themes()
{
  $themes = array();

  foreach (get_dirs(PHPWG_ROOT_PATH.'template') as $template)
  {
    foreach (get_dirs(PHPWG_ROOT_PATH.'template/'.$template.'/theme') as $theme)
    {
      array_push($themes, $template.'/'.$theme);
    }
  }

  return $themes;
}

/**
 * returns thumbnail filepath (or distant URL if thumbnail is remote) for a
 * given element
 *
 * the returned string can represente the filepath of the thumbnail or the
 * filepath to the corresponding icon for non picture elements
 *
 * @param string path
 * @param string tn_ext
 * @return string
 */
function get_thumbnail_src($path, $tn_ext = '')
{
  global $conf, $user;

  if ($tn_ext != '')
  {
    $src = substr_replace(get_filename_wo_extension($path),
                          '/thumbnail/'.$conf['prefix_thumbnail'],
                          strrpos($path,'/'),
                          1);
    $src.= '.'.$tn_ext;
  }
  else
  {
    $src = get_themeconf('mime_icon_dir');
    $src.= strtolower(get_extension($path)).'.png';
  }
  
  return $src;
}

// my_error returns (or send to standard output) the message concerning the
// error occured for the last mysql query.
function my_error($header)
{
  $error = '<pre>';
  $error.= $header;
  $error.= '[mysql error '.mysql_errno().'] ';
  $error.= mysql_error();
  $error.= '</pre>';
  die ($error);
}

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
  while ($row = mysql_fetch_array($result))
  {
    array_push($array, $row[$fieldname]);
  }

  return $array;
}

/**
 * instantiate number list for days in a template block
 *
 * @param string blockname
 * @param string selection
 */
function get_day_list($blockname, $selection)
{
  global $template;
  
  $template->assign_block_vars(
    $blockname, array('SELECTED' => '', 'VALUE' => 0, 'OPTION' => '--'));
  
  for ($i = 1; $i <= 31; $i++)
  {
    $selected = '';
    if ($i == (int)$selection)
    {
      $selected = 'selected="selected"';
    }
    $template->assign_block_vars(
      $blockname, array('SELECTED' => $selected,
                        'VALUE' => $i,
                        'OPTION' => str_pad($i, 2, '0', STR_PAD_LEFT)));
  }
}

/**
 * instantiate month list in a template block
 *
 * @param string blockname
 * @param string selection
 */
function get_month_list($blockname, $selection)
{
  global $template, $lang;
  
  $template->assign_block_vars(
    $blockname, array('SELECTED' => '',
                      'VALUE' => 0,
                      'OPTION' => '------------'));

  for ($i = 1; $i <= 12; $i++)
  {
    $selected = '';
    if ($i == (int)$selection)
    {
      $selected = 'selected="selected"';
    }
    $template->assign_block_vars(
      $blockname, array('SELECTED' => $selected,
                        'VALUE' => $i,
                        'OPTION' => $lang['month'][$i]));
  }
}

/**
 * fill the current user caddie with given elements, if not already in
 * caddie
 *
 * @param array elements_id
 */
function fill_caddie($elements_id)
{
  global $user;
  
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  
  $query = '
SELECT element_id
  FROM '.CADDIE_TABLE.'
  WHERE user_id = '.$user['id'].'
;';
  $in_caddie = array_from_query($query, 'element_id');

  $caddiables = array_diff($elements_id, $in_caddie);

  $datas = array();

  foreach ($caddiables as $caddiable)
  {
    array_push($datas, array('element_id' => $caddiable,
                             'user_id' => $user['id']));
  }

  if (count($caddiables) > 0)
  {
    mass_inserts(CADDIE_TABLE, array('element_id','user_id'), $datas);
  }
}

/**
 * returns the element name from its filename
 *
 * @param string filename
 * @return string name
 */
function get_name_from_file($filename)
{
  return str_replace('_',' ',get_filename_wo_extension($filename));
}

/**
 * returns the corresponding value from $lang if existing. Else, the key is
 * returned
 *
 * @param string key
 * @return string
 */
function l10n($key)
{
  global $lang, $conf;

  if ($conf['debug_l10n'] and !isset($lang[$key]))
  {
    echo '[l10n] language key "'.$key.'" is not defined<br />';
  }
  
  return isset($lang[$key]) ? $lang[$key] : $key;
}

/**
 * returns the corresponding value from $themeconf if existing. Else, the
 * key is returned
 *
 * @param string key
 * @return string
 */
function get_themeconf($key)
{
  global $themeconf;

  return $themeconf[$key];
}

/**
 * Prepends and appends a string at each value of the given array.
 *
 * @param array
 * @param string prefix to each array values
 * @param string suffix to each array values
 */
function prepend_append_array_items($array, $prepend_str, $append_str)
{
  array_walk(
    $array,
    create_function('&$s', '$s = "'.$prepend_str.'".$s."'.$append_str.'";')
    );

  return $array;
}

/**
 * returns search rules stored into a serialized array in "search"
 * table. Each search rules set is numericaly identified.
 *
 * @param int search_id
 * @return array
 */
function get_search_array($search_id)
{
  if (!is_numeric($search_id))
  {
    die('Search id must be an integer');
  }
  
  $query = '
SELECT rules
  FROM '.SEARCH_TABLE.'
  WHERE id = '.$search_id.'
;';
  list($serialized_rules) = mysql_fetch_row(pwg_query($query));
  
  return unserialize($serialized_rules);
}

/**
 * returns the SQL clause from a search identifier
 *
 * Search rules are stored in search table as a serialized array. This array
 * need to be transformed into an SQL clause to be used in queries.
 *
 * @param int search_id
 * @return string
 */
function get_sql_search_clause($search_id)
{
  $search = get_search_array($search_id);
  
  // SQL where clauses are stored in $clauses array during query
  // construction
  $clauses = array();
  
  foreach (array('file','name','comment','keywords','author') as $textfield)
  {
    if (isset($search['fields'][$textfield]))
    {
      $local_clauses = array();
      foreach ($search['fields'][$textfield]['words'] as $word)
      {
        array_push($local_clauses, $textfield." LIKE '%".$word."%'");
      }

      // adds brackets around where clauses
      $local_clauses = prepend_append_array_items($local_clauses, '(', ')');

      array_push(
        $clauses,
        implode(
          ' '.$search['fields'][$textfield]['mode'].' ',
          $local_clauses
          )
        );
    }
  }
  
  if (isset($search['fields']['allwords']))
  {
    $fields = array('file', 'name', 'comment', 'keywords', 'author');
    // in the OR mode, request bust be :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // OR (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    //
    // in the AND mode :
    // ((field1 LIKE '%word1%' OR field2 LIKE '%word1%')
    // AND (field1 LIKE '%word2%' OR field2 LIKE '%word2%'))
    $word_clauses = array();
    foreach ($search['fields']['allwords']['words'] as $word)
    {
      $field_clauses = array();
      foreach ($fields as $field)
      {
        array_push($field_clauses, $field." LIKE '%".$word."%'");
      }
      // adds brackets around where clauses
      array_push(
        $word_clauses,
        implode(
          "\n          OR ",
          $field_clauses
          )
        );
    }
    
    array_walk(
      $word_clauses,
      create_function('&$s','$s="(".$s.")";')
      );
    
    array_push(
      $clauses,
      "\n         ".
      implode(
        "\n         ".
              $search['fields']['allwords']['mode'].
        "\n         ",
        $word_clauses
        )
      );
  }
  
  foreach (array('date_available', 'date_creation') as $datefield)
  {
    if (isset($search['fields'][$datefield]))
    {
      array_push(
        $clauses,
        $datefield." = '".$search['fields'][$datefield]['date']."'"
        );
    }
    
    foreach (array('after','before') as $suffix)
    {
      $key = $datefield.'-'.$suffix;
      
      if (isset($search['fields'][$key]))
      {
        array_push(
          $clauses,
          
          $datefield.
          ($suffix == 'after'             ? ' >' : ' <').
          ($search['fields'][$key]['inc'] ? '='  : '').
          " '".$search['fields'][$key]['date']."'"
          
          );
      }
    }
  }
  
  if (isset($search['fields']['cat']))
  {
    if ($search['fields']['cat']['sub_inc'])
    {
      // searching all the categories id of sub-categories
      $cat_ids = get_subcat_ids($search['fields']['cat']['words']);
    }
    else
    {
      $cat_ids = $search['fields']['cat']['words'];
    }
    
    $local_clause = 'category_id IN ('.implode(',', $cat_ids).')';
    array_push($clauses, $local_clause);
  }
  
  // adds brackets around where clauses
  $clauses = prepend_append_array_items($clauses, '(', ')');
  
  $where_separator =
    implode(
      "\n    ".$search['mode'].' ',
      $clauses
      );
  
  $search_clause = $where_separator;
  
  if (isset($forbidden))
  {
    $search_clause.= "\n    AND ".$forbidden;
  }

  return $search_clause;
}

/**
 * Returns webmaster mail address depending on $conf['webmaster_id']
 *
 * @return string
 */
function get_webmaster_mail_address()
{
  global $conf;

  $query = '
SELECT '.$conf['user_fields']['email'].'
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.$conf['webmaster_id'].'
;';
  list($email) = mysql_fetch_array(pwg_query($query));

  return $email;
}

/**
 * which upgrades are available ?
 *
 * @return array
 */
function get_available_upgrade_ids()
{
  $upgrades_path = PHPWG_ROOT_PATH.'install/db';

  $available_upgrade_ids = array();
  
  if ($contents = opendir($upgrades_path))
  {
    while (($node = readdir($contents)) !== false)
    {
      if (is_file($upgrades_path.'/'.$node)
          and preg_match('/^(.*?)-database\.php$/', $node, $match))
      {
        array_push($available_upgrade_ids, $match[1]);
      }
    }
  }
  natcasesort($available_upgrade_ids);

  return $available_upgrade_ids;
}
?>
