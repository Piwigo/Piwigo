<?php
// +-----------------------------------------------------------------------+
// |                           functions.inc.php                           |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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
 * possible values of an "enum" field
 *
 * get_enums returns an array containing the possible values of a enum field
 * in a table of the database.
 *
 * @param string table in the database
 * @param string field name in this table
 * @uses str_replace
 * @uses explode
 * @uses sizeof
 * @uses substr
 */
function get_enums( $table, $field )
{
  // retrieving the properties of the table. Each line represents a field :
  // columns are 'Field', 'Type'
  $result=mysql_query("desc $table");
  while ( $row = mysql_fetch_array( $result ) ) 
  {
    // we are only interested in the the field given in parameter for the
    // function
    if ( $row['Field']==$field )
    {
      // retrieving possible values of the enum field
      // enum('blue','green','black')
      $option = explode( ',', substr($row['Type'], 5, -1 ) );
      for ( $i = 0; $i < sizeof( $option ); $i++ )
      {
        // deletion of quotation marks
        $option[$i] = str_replace( "'", '',$option[$i] );
      }                 
    }
  }
  mysql_free_result( $result );
  return $option;
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

// array_remove removes a value from the given array if the value existed in
// this array.
function array_remove( $array, $value )
{
  $output = array();
  foreach ( $array as $v ) {
    if ( $v != $value ) array_push( $output, $v );
  }
  return $output;
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
 * returns an array contening sub-directories
 *
 * @param string $dir
 * @return array
 */
function get_dirs( $directory )
{
  $sub_dirs = array();

  if ( $opendir = opendir( $directory ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( $file != '.' and $file != '..' and is_dir ( $directory.'/'.$file ) )
      {
        array_push( $sub_dirs, $file );
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

// get_languages retourne un tableau contenant tous les languages
// disponibles pour PhpWebGallery
function get_languages( $rep_language )
{
  global $lang;
  $languages = array();
  $i = 0;
  if ( $opendir = opendir ( $rep_language ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( is_dir ( $rep_language.$file )&& !substr_count($file,'.') && isset($lang['lang'][$file]))
      {
        $languages[$i++] =$lang['lang'][$file];
      }
    }
  }
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

  if ( $conf['log'] )
  {
    $query = 'insert into '.PREFIX_TABLE.'history';
    $query.= ' (date,login,IP,file,category,picture) values';
    $query.= " (".time().", '".$user['username']."'";
    $query.= ",'".$_SERVER['REMOTE_ADDR']."'";
    $query.= ",'".$file."','".$category."','".$picture."');";
    mysql_query( $query );
  }
}

function templatize_array( $array, $global_array_name, $handle )
{
  global $vtp, $lang, $page, $user, $conf;

  foreach ( $array as $value ) {
  if (isset(${$global_array_name}[$value]))
    $vtp->setGlobalVar( $handle, $value, ${$global_array_name}[$value] );
  }
}

// format_date returns a formatted date for display. The date given in
// argument can be a unixdate (number of seconds since the 01.01.1970) or an
// american format (2003-09-15). By option, you can show the time. The
// output is internationalized.
//
// format_date( "2003-09-15", 'us', true ) -> "Monday 15 September 2003 21:52"
function format_date( $date, $type = 'us', $show_time = false )
{
  global $lang;

  switch ( $type )
  {
  case 'us' :
    list( $year,$month,$day ) = explode( '-', $date );
    $unixdate = mktime(0,0,0,$month,$day,$year);
    break;
  case 'unix' :
    $unixdate = $date;
    break;
  case 'mysql_datetime' :
    preg_match( '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/',
                $date, $matches );
    $unixdate = mktime($matches[4],$matches[5],$matches[6],
                       $matches[2],$matches[3],$matches[1]);
    break;
  }
  $formated_date = $lang['day'][date( "w", $unixdate )];
  $formated_date.= date( " j ", $unixdate );
  $formated_date.= $lang['month'][date( "n", $unixdate )];
  $formated_date.= date( ' Y', $unixdate );
  if ( $show_time )
  {
    $formated_date.= date( ' G:i', $unixdate );
  }

  return $formated_date;
}

// notify sends a email to every admin of the gallery
function notify( $type, $infos = '' )
{
  global $conf;

  $headers = 'From: '.$conf['webmaster'].' <'.$conf['mail_webmaster'].'>'."\n";
  $headers.= 'Reply-To: '.$conf['mail_webmaster']."\n";
  $headers.= 'X-Mailer: PhpWebGallery, PHP '.phpversion();

  $options = '-f '.$conf['mail_webmaster'];
  // retrieving all administrators
  $query = 'SELECT username,mail_address,language';
  $query.= ' FROM '.USERS_TABLE;
  $query.= " WHERE status = 'admin'";
  $query.= ' AND mail_address IS NOT NULL';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $to = $row['mail_address'];
    include( PHPWG_ROOT_PATH.'language/'.$row['language'].'.php' );
    $content = $lang['mail_hello']."\n\n";
    switch ( $type )
    {
    case 'upload' :
      $subject = $lang['mail_new_upload_subject'];
      $content.= $lang['mail_new_upload_content'];
      break;
    case 'comment' :
      $subject = $lang['mail_new_comment_subject'];
      $content.= $lang['mail_new_comment_content'];
      break;
    }
    $infos = str_replace( '&nbsp;',  ' ', $infos );
    $infos = str_replace( '&minus;', '-', $infos );
    $content.= "\n\n".$infos;
    $content.= "\n\n-- \nPhpWebGallery ".$conf['version'];
    $content = wordwrap( $content, 72 );
    @mail( $to, $subject, $content, $headers, $options );
  }
}

function pwg_write_debug()
{
  global $debug;
  
  $fp = @fopen( './log/debug.log', 'a+' );
  fwrite( $fp, "\n\n" );
  fwrite( $fp, $debug );
  fclose( $fp );
}

function pwg_query( $query )
{
  global $count_queries,$queries_time;

  $start = get_moment();
  $output = '';
  
  $count_queries++;
  $output.= '<br /><br />['.$count_queries.'] '.$query;
  $result = mysql_query( $query );
  $time = get_moment() - $start;
  $queries_time+= $time;
  $output.= '<b>('.number_format( $time, 3, '.', ' ').' s)</b>';
  $output.= '('.number_format( $queries_time, 3, '.', ' ').' s)';

  // echo $output;
  
  return $result;
}

function pwg_debug( $string )
{
  global $debug,$t2,$count_queries;

  $now = explode( ' ', microtime() );
  $now2 = explode( '.', $now[0] );
  $now2 = $now[1].'.'.$now2[1];
  $time = number_format( $now2 - $t2, 3, '.', ' ').' s';
  $debug.= '['.$time.', ';
  $debug.= $count_queries.' queries] : '.$string;
  $debug.= "\n";
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
  global $user, $template, $lang_info, $conf, $lang, $t2;

  // $refresh, $url_link and $title are required for creating an automated
  // refresh page in header.tpl
  $refresh = 1;
  $url_link = $url;
  $title = 'redirection';
  include( PHPWG_ROOT_PATH.'include/page_header.php' );
  
  $template->set_filenames( array( 'redirect' => 'redirect.tpl' ) );
  $template->pparse('redirect');
  
  include( PHPWG_ROOT_PATH.'include/page_tail.php' );

  exit();
}
?>