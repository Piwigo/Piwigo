<?php
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

include_once( PHPWG_ROOT_PATH .'include/functions_user.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_cookie.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_session.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_category.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_xml.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_group.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_html.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_tag.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_url.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_plugins.inc.php' );

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
  $pos = strrpos( $filename, '.' );
  return ($pos===false) ? $filename : substr( $filename, 0, $pos);
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

/**
 * returns thumbnail directory name of input diretoty name
 * make thumbnail directory is necessary
 * set error messages on array messages
 *
 * @param:
 *  string $dirname
 *  arrayy $errors
 * @return bool false on error else string directory name
 */
function mkget_thumbnail_dir($dirname, &$errors)
{
  $tndir = $dirname.'/thumbnail';
  if (!is_dir($tndir))
  {
    if (!is_writable($dirname))
    {
      array_push($errors,
                 '['.$dirname.'] : '.l10n('no_write_access'));
      return false;
    }
    umask(0000);
    mkdir($tndir, 0777);
  }

  return $tndir;
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

/* Returns true if the string appears to be encoded in UTF-8. (from wordpress)
 * @param string Str
 */
function seems_utf8($Str) { # by bmorel at ssi dot fr
  for ($i=0; $i<strlen($Str); $i++) {
    if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
    elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
    elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
    elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
    elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
    elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
    else return false; # Does not match any model
    for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
      if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
      return false;
    }
  }
  return true;
}

/* Remove accents from a UTF-8 or ISO-859-1 string (from wordpress)
 * @param string sstring - an UTF-8 or ISO-8859-1 string
 */
function remove_accents($string)
{
  if ( !preg_match('/[\x80-\xff]/', $string) )
    return $string;

  if (seems_utf8($string)) {
    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
    // Euro Sign
    chr(226).chr(130).chr(172) => 'E',
    // GBP (Pound) Sign
    chr(194).chr(163) => '');

    $string = strtr($string, $chars);
  } else {
    // Assume ISO-8859-1 if not UTF-8
    $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
      .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
      .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
      .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
      .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
      .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
      .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
      .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
      .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
      .chr(252).chr(253).chr(255);

    $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

    $string = strtr($string, $chars['in'], $chars['out']);
    $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
    $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
    $string = str_replace($double_chars['in'], $double_chars['out'], $string);
  }

  return $string;
}

/**
 * simplify a string to insert it into an URL
 *
 * @param string
 * @return string
 */
function str2url($str)
{
  $str = remove_accents($str);
  $str = preg_replace('/[^a-z0-9_\s\'\:\/\[\],-]/','',strtolower($str));
  $str = preg_replace('/[\s\'\:\/\[\],-]+/',' ',trim($str));
  $res = str_replace(' ','_',$str);

  return $res;
}

//-------------------------------------------- PhpWebGallery specific functions

/**
 * returns an array with a list of {language_code => language_name}
 *
 * @returns array
 */
function get_languages($target_charset = null)
{
  if ( empty($target_charset) )
  {
    $target_charset = get_pwg_charset();
  }
  $target_charset = strtolower($target_charset);

  $dir = opendir(PHPWG_ROOT_PATH.'language');
  $languages = array();

  while ($file = readdir($dir))
  {
    $path = PHPWG_ROOT_PATH.'language/'.$file;
    if (is_dir($path) and !is_link($path) and file_exists($path.'/iso.txt'))
    {
      list($language_name) = @file($path.'/iso.txt');

      $langdef = explode('.',$file);
      if (count($langdef)>1) // (langCode,encoding)
      {
        $langdef[1] = strtolower($langdef[1]);

        if (
          $target_charset==$langdef[1]
         or
          ($target_charset=='utf-8' and $langdef[1]=='iso-8859-1')
         or
          ($target_charset=='iso-8859-1' and
          in_array( substr($langdef[0],2), array('en','fr','de','es','it','nl')))
        )
        {
          $language_name = convert_charset($language_name,
              $langdef[1], $target_charset);
          $languages[ $langdef[0] ] = $language_name;
        }
        else
          continue; // the language encoding is not compatible with our charset
      }
      else
      { // UTF-8
        $language_name = convert_charset($language_name,
              'utf-8', $target_charset);
        $languages[$file] = $language_name;
      }
    }
  }
  closedir($dir);
  @asort($languages);
  @reset($languages);

  return $languages;
}

function pwg_log($image_id = null, $image_type = null)
{
  global $conf, $user, $page;

  $do_log = true;
  if (!$conf['log'])
  {
    $do_log = false;
  }
  if (is_admin() and !$conf['history_admin'])
  {
    $do_log = false;
  }
  if (is_a_guest() and !$conf['history_guest'])
  {
    $do_log = false;
  }

  $do_log = trigger_event('pwg_log_allowed', $do_log, $image_id, $image_type);

  if (!$do_log)
  {
    return false;
  }

  $tags_string = null;
  if (isset($page['section']) and $page['section'] == 'tags')
  {
    $tag_ids = array();
    foreach ($page['tags'] as $tag)
    {
      array_push($tag_ids, $tag['id']);
    }

    $tags_string = implode(',', $tag_ids);
  }

  // here we ask the database the current date and time, and we extract
  // {year, month, day} from the current date. We could do this during the
  // insert query with a CURDATE(), CURTIME(), DATE_FORMAT(CURDATE(), '%Y')
  // ... but I (plg) think it would cost more than a double query and a PHP
  // extraction.
  $query = '
SELECT CURDATE(), CURTIME()
;';
  list($curdate, $curtime) = mysql_fetch_row(pwg_query($query));

  list($curyear, $curmonth, $curday) = explode('-', $curdate);
  list($curhour) = explode(':', $curtime);

  $query = '
INSERT INTO '.HISTORY_TABLE.'
  (
    date,
    time,
    year,
    month,
    day,
    hour,
    user_id,
    IP,
    section,
    category_id,
    image_id,
    image_type,
    tag_ids
  )
  VALUES
  (
    \''.$curdate.'\',
    \''.$curtime.'\',
    '.$curyear.',
    '.$curmonth.',
    '.$curday.',
    '.$curhour.',
    '.$user['id'].',
    \''.$_SERVER['REMOTE_ADDR'].'\',
    '.(isset($page['section']) ? "'".$page['section']."'" : 'NULL').',
    '.(isset($page['category']) ? $page['category']['id'] : 'NULL').',
    '.(isset($image_id) ? $image_id : 'NULL').',
    '.(isset($image_type) ? "'".$image_type."'" : 'NULL').',
    '.(isset($tags_string) ? "'".$tags_string."'" : 'NULL').'
  )
;';
  pwg_query($query);

  return true;
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
 * Redirects to the given URL (HTTP method)
 *
 * Note : once this function called, the execution doesn't go further
 * (presence of an exit() instruction.
 *
 * @param string $url
 * @return void
 */
function redirect_http( $url )
{
  if (ob_get_length () !== FALSE)
  {
    ob_clean();
  }
  header('Request-URI: '.$url);
  header('Content-Location: '.$url);
  header('Location: '.$url);
  exit();
}

/**
 * Redirects to the given URL (HTML method)
 *
 * Note : once this function called, the execution doesn't go further
 * (presence of an exit() instruction.
 *
 * @param string $url
 * @param string $title_msg
 * @param integer $refreh_time
 * @return void
 */
function redirect_html( $url , $msg = '', $refresh_time = 0)
{
  global $user, $template, $lang_info, $conf, $lang, $t2, $page, $debug;

  if (!isset($lang_info))
  {
    $user = build_user( $conf['guest_id'], true);
    load_language('common.lang');
    trigger_action('loading_lang');
    load_language('local.lang');
    list($tmpl, $thm) = explode('/', get_default_template());
    $template = new Template(PHPWG_ROOT_PATH.'template/'.$tmpl, $thm);
  }
  else
  {
    $template = new Template(PHPWG_ROOT_PATH.'template/'.$user['template'], $user['theme']);
  }

  if (empty($msg))
  {
    $redirect_msg = l10n('redirect_msg');
  }
  else
  {
    $redirect_msg = $msg;
  }
  $redirect_msg = nl2br($redirect_msg);

  $refresh = $refresh_time;
  $url_link = $url;
  $title = 'redirection';

  $template->set_filenames( array( 'redirect' => 'redirect.tpl' ) );

  include( PHPWG_ROOT_PATH.'include/page_header.php' );

  $template->set_filenames( array( 'redirect' => 'redirect.tpl' ) );
  $template->parse('redirect');

  include( PHPWG_ROOT_PATH.'include/page_tail.php' );

  exit();
}

/**
 * Redirects to the given URL (Switch to HTTP method or HTML method)
 *
 * Note : once this function called, the execution doesn't go further
 * (presence of an exit() instruction.
 *
 * @param string $url
 * @param string $title_msg
 * @param integer $refreh_time
 * @return void
 */
function redirect( $url , $msg = '', $refresh_time = 0)
{
  global $conf;

  // with RefeshTime <> 0, only html must be used
  if ($conf['default_redirect_method']=='http'
      and $refresh_time==0
      and !headers_sent()
    )
  {
    redirect_http($url);
  }
  else
  {
    redirect_html($url, $msg, $refresh_time);
  }
}

/**
 * returns $_SERVER['QUERY_STRING'] whitout keys given in parameters
 *
 * @param array $rejects
 * @param boolean $escape - if true escape & to &amp; (for html)
 * @returns string
 */
function get_query_string_diff($rejects=array(), $escape=true)
{
  $query_string = '';

  $str = $_SERVER['QUERY_STRING'];
  parse_str($str, $vars);

  $is_first = true;
  foreach ($vars as $key => $value)
  {
    if (!in_array($key, $rejects))
    {
      $query_string.= $is_first ? '?' : ($escape ? '&amp;' : '&' );
      $is_first = false;
      $query_string.= $key.'='.$value;
    }
  }

  return $query_string;
}

function url_is_remote($url)
{
  if ( strncmp($url, 'http://', 7)==0
    or strncmp($url, 'https://', 8)==0 )
  {
    return true;
  }
  return false;
}

/**
 * returns available template/theme
 */
function get_pwg_themes()
{
  $themes = array();

  $template_dir = PHPWG_ROOT_PATH.'template';

  foreach (get_dirs($template_dir) as $template)
  {
    foreach (get_dirs($template_dir.'/'.$template.'/theme') as $theme)
    {
      array_push($themes, $template.'/'.$theme);
    }
  }

  return $themes;
}

/* Returns the PATH to the thumbnail to be displayed. If the element does not
 * have a thumbnail, the default mime image path is returned. The PATH can be
 * used in the php script, but not sent to the browser.
 * @param array element_info assoc array containing element info from db
 * at least 'path', 'tn_ext' and 'id' should be present
 */
function get_thumbnail_path($element_info)
{
  $path = get_thumbnail_location($element_info);
  if ( !url_is_remote($path) )
  {
    $path = PHPWG_ROOT_PATH.$path;
  }
  return $path;
}

/* Returns the URL of the thumbnail to be displayed. If the element does not
 * have a thumbnail, the default mime image url is returned. The URL can be
 * sent to the browser, but not used in the php script.
 * @param array element_info assoc array containing element info from db
 * at least 'path', 'tn_ext' and 'id' should be present
 */
function get_thumbnail_url($element_info)
{
  $path = get_thumbnail_location($element_info);
  if ( !url_is_remote($path) )
  {
    $path = embellish_url(get_root_url().$path);
  }

  // plugins want another url ?
  $path = trigger_event('get_thumbnail_url', $path, $element_info);
  return $path;
}

/* returns the relative path of the thumnail with regards to to the root
of phpwebgallery (not the current page!).This function is not intended to be
called directly from code.*/
function get_thumbnail_location($element_info)
{
  global $conf;
  if ( !empty( $element_info['tn_ext'] ) )
  {
    $path = substr_replace(
      get_filename_wo_extension($element_info['path']),
      '/thumbnail/'.$conf['prefix_thumbnail'],
      strrpos($element_info['path'],'/'),
      1
      );
    $path.= '.'.$element_info['tn_ext'];
  }
  else
  {
    $path = get_themeconf('mime_icon_dir')
        .strtolower(get_extension($element_info['path'])).'.png';
  }

  // plugins want another location ?
  $path = trigger_event( 'get_thumbnail_location', $path, $element_info);
  return $path;
}

/* returns the title of the thumnail */
function get_thumbnail_title($element_info)
{
  // message in title for the thumbnail
  if (isset($element_info['file']))
  {
    $thumbnail_title = $element_info['file'];
  }
  else
  {
    $thumbnail_title = '';
  }

  if (!empty($element_info['filesize']))
  {
    $thumbnail_title .= ' : '.l10n_dec('%d Kb', '%d Kb', $element_info['filesize']);
  }

  return $thumbnail_title;
}

// my_error returns (or send to standard output) the message concerning the
// error occured for the last mysql query.
function my_error($header)
{
  global $conf;

  $error = '<pre>';
  $error.= $header;
  $error.= '[mysql error '.mysql_errno().'] ';
  $error.= mysql_error();
  $error.= '</pre>';

  if ($conf['die_on_sql_error'])
  {
    die($error);
  }
  else
  {
    echo $error;
  }
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
    $blockname,
    array(
      'SELECTED' => '',
      'VALUE' => 0,
      'OPTION' => '--'
      )
    );

  for ($i = 1; $i <= 31; $i++)
  {
    $selected = '';
    if ($i == (int)$selection)
    {
      $selected = 'selected="selected"';
    }
    $template->assign_block_vars(
      $blockname,
      array(
        'SELECTED' => $selected,
        'VALUE' => $i,
        'OPTION' => str_pad($i, 2, '0', STR_PAD_LEFT)
        )
      );
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
    $blockname,
    array(
      'SELECTED' => '',
      'VALUE' => 0,
      'OPTION' => '------------')
    );

  for ($i = 1; $i <= 12; $i++)
  {
    $selected = '';
    if ($i == (int)$selection)
    {
      $selected = 'selected="selected"';
    }
    $template->assign_block_vars(
      $blockname,
      array(
        'SELECTED' => $selected,
        'VALUE' => $i,
        'OPTION' => $lang['month'][$i])
      );
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

  if ($conf['debug_l10n'] and !isset($lang[$key]) and !empty($key))
  {
    echo '[l10n] language key "'.$key.'" is not defined<br />';
  }

  return isset($lang[$key]) ? $lang[$key] : $key;
}

/**
 * returns the prinft value for strings including %d
 * return is concorded with decimal value (singular, plural)
 *
 * @param singular string key
 * @param plural string key
 * @param decimal value
 * @return string
 */
function l10n_dec($singular_fmt_key, $plural_fmt_key, $decimal)
{
  global $lang_info;

  return
    sprintf(
      l10n((
        (($decimal > 1) or ($decimal == 0 and $lang_info['zero_plural']))
          ? $plural_fmt_key
          : $singular_fmt_key
        )), $decimal);
}
/*
 * returns a single element to use with l10n_args
 *
 * @param string key: translation key
 * @param array/string/../number args:
 *   arguments to use on sprintf($key, args)
 *   if args is a array, each values are used on sprintf
 * @return string
 */
function get_l10n_args($key, $args)
{
  if (is_array($args))
  {
    $key_arg = array_merge(array($key), $args);
  }
  else
  {
    $key_arg = array($key,  $args);
  }
  return array('key_args' => $key_arg);
}

/*
 * returns a string with formated with l10n_args elements
 *
 * @param element/array $key_args: element or array of l10n_args elements
 * @param $sep: if $key_args is array,
 *   separator is used when translated l10n_args elements are concated
 * @return string
 */
function l10n_args($key_args, $sep = "\n")
{
  if (is_array($key_args))
  {
    foreach ($key_args as $key => $element)
    {
      if (isset($result))
      {
        $result .= $sep;
      }
      else
      {
        $result = '';
      }

      if ($key === 'key_args')
      {
        array_unshift($element, l10n(array_shift($element)));
        $result .= call_user_func_array('sprintf', $element);
      }
      else
      {
        $result .= l10n_args($element, $sep);
      }
    }
  }
  else
  {
    die('l10n_args: Invalid arguments');
  }

  return $result;
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
  global $template;

  return $template->get_themeconf($key);
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

/**
 * Add configuration parameters from database to global $conf array
 *
 * @return void
 */
function load_conf_from_db($condition = '')
{
  global $conf;

  $query = '
SELECT param, value
 FROM '.CONFIG_TABLE.'
 '.(!empty($condition) ? 'WHERE '.$condition : '').'
;';
  $result = pwg_query($query);

  if ((mysql_num_rows($result) == 0) and !empty($condition))
  {
    die('No configuration data');
  }

  while ($row = mysql_fetch_array($result))
  {
    $conf[ $row['param'] ] = isset($row['value']) ? $row['value'] : '';

    // If the field is true or false, the variable is transformed into a
    // boolean value.
    if ($conf[$row['param']] == 'true' or $conf[$row['param']] == 'false')
    {
      $conf[ $row['param'] ] = get_boolean($conf[ $row['param'] ]);
    }
  }
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
 * creates an hashed based on a query, this function is a very common
 * pattern used here. Among the selected columns fetched, choose one to be
 * the key, another one to be the value.
 *
 * @param string $query
 * @param string $keyname
 * @param string $valuename
 * @return array
 */
function simple_hash_from_query($query, $keyname, $valuename)
{
  $array = array();

  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $array[ $row[$keyname] ] = $row[$valuename];
  }

  return $array;
}

/**
 * creates an hashed based on a query, this function is a very common
 * pattern used here. The key is given as parameter, the value is an associative
 * array.
 *
 * @param string $query
 * @param string $keyname
 * @return array
 */
function hash_from_query($query, $keyname)
{
  $array = array();
  $result = pwg_query($query);
  while ($row = mysql_fetch_assoc($result))
  {
    $array[ $row[$keyname] ] = $row;
  }
  return $array;
}

/**
 * Return basename of the current script
 * Lower case convertion is applied on return value
 * Return value is without file extention ".php"
 *
 * @param void
 *
 * @return script basename
 */
function script_basename()
{
  global $conf;

  foreach (array('SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF') as $value)
  {
    $continue = !empty($_SERVER[$value]);
    if ($continue)
    {
      $filename = strtolower($_SERVER[$value]);

      if ($conf['php_extension_in_urls'])
      {
        $continue = get_extension($filename) ===  'php';
      }

      if ($continue)
      {
        $basename = basename($filename, '.php');
        $continue = !empty($basename);
      }

      if ($continue)
      {
        return $basename;
      }
    }
  }

  return '';
}

/**
 * Return value for the current page define on $conf['filter_pages']
 * Îf value is not defined, default value are returned
 *
 * @param value name
 *
 * @return filter page value
 */
function get_filter_page_value($value_name)
{
  global $conf;

  $page_name = script_basename();

  if (isset($conf['filter_pages'][$page_name][$value_name]))
  {
    return $conf['filter_pages'][$page_name][$value_name];
  }
  else if (isset($conf['filter_pages']['default'][$value_name]))
  {
    return $conf['filter_pages']['default'][$value_name];
  }
  else
  {
    return null;
  }
}

/**
 * returns the character set of data sent to browsers / received from forms
 */
function get_pwg_charset()
{
  defined('PWG_CHARSET') or die('load_language PWG_CHARSET undefined');
  return PWG_CHARSET;
}

/**
 * includes a language file or returns the content of a language file
 * availability of the file
 *
 * in descending order of preference:
 *   param language, user language, default language
 * PhpWebGallery default language.
 *
 * @param string filename
 * @param string dirname
 * @param string language
 * @param bool return_content - if true the file content is returned otherwise
 *  the file is evaluated as php
 * @return boolean success status or a string if return_content is true
 */
function load_language($filename, $dirname = '', $language = '',
    $return_content=false, $target_charset=null)
{
  global $user;

  if (!$return_content)
  {
    $filename .= '.php'; //MAYBE to do .. load .po and .mo localization files
  }
  if (empty($dirname))
  {
    $dirname = PHPWG_ROOT_PATH;
  }
  $dirname .= 'language/';

  $languages = array();
  if ( !empty($language) )
  {
    $languages[] = $language;
  }
  if ( !empty($user['language']) )
  {
    $languages[] = $user['language'];
  }
  if ( defined('PHPWG_INSTALLED') )
  {
    $languages[] = get_default_language();
  }
  $languages[] = PHPWG_DEFAULT_LANGUAGE;
  $languages = array_unique($languages);

  if ( empty($target_charset) )
  {
    $target_charset = get_pwg_charset();
  }
  $target_charset = strtolower($target_charset);
  $source_charset = '';
  $source_file    = '';
  foreach ($languages as $language)
  {
    $dir = $dirname.$language;

    // exact charset match - no conversion required
    $f = $dir.'.'.$target_charset.'/'.$filename;
    if (file_exists($f))
    {
      $source_file = $f;
      break;
    }

    // UTF-8 ?
    $f = $dir.'/'.$filename;
    if (file_exists($f))
    {
      $source_charset = 'utf-8';
      $source_file = $f;
      break;
    }

    if ($target_charset=='utf-8')
    { // we accept conversion from ISO-8859-1 to UTF-8
      $f = $dir.'.iso-8859-1/'.$filename;
      if (file_exists($f))
      {
        $source_charset = 'iso-8859-1';
        $source_file = $f;
        break;
      }
    }
  }

  if ( !empty($source_file) )
  {
    if (!$return_content)
    {
      @include($source_file);
      $load_lang = @$lang;
      $load_lang_info = @$lang_info;

      global $lang, $lang_info;
      if ( !isset($lang) ) $lang=array();
      if ( !isset($lang_info) ) $lang_info=array();

      if ( !empty($source_charset) and $source_charset!=$target_charset)
      {
        if ( is_array($load_lang) )
        {
          foreach ($load_lang as $k => $v)
          {
            if ( is_array($v) )
            {
              $func = create_function('$v', 'return convert_charset($v, "'.$source_charset.'","'.$target_charset.'");' );
              $lang[$k] = array_map($func, $v);
            }
            else
              $lang[$k] = convert_charset($v, $source_charset, $target_charset);
          }
        }
        if ( is_array($load_lang_info) )
        {
          foreach ($load_lang_info as $k => $v)
          {
            $lang_info[$k] = convert_charset($v, $source_charset, $target_charset);
          }
        }
      }
      else
      {
        $lang = array_merge( $lang, (array)$load_lang );
        $lang_info = array_merge( $lang_info, (array)$load_lang_info );
      }
      return true;
    }
    else
    {
      $content = @file_get_contents($source_file);
      if ( !empty($source_charset) and $source_charset!=$target_charset)
      {
        $content = convert_charset($content, $source_charset, $target_charset);
      }
      return $content;
    }
  }
  return false;
}

/**
 * converts a string from a character set to another character set
 * @param string str the string to be converted
 * @param string source_charset the character set in which the string is encoded
 * @param string dest_charset the destination character set
 */
function convert_charset($str, $source_charset, $dest_charset)
{
  if ($source_charset==$dest_charset)
    return $str;
  if ($source_charset=='iso-8859-1' and $dest_charset=='utf-8')
  {
    return utf8_encode($str);
  }
  if ($source_charset=='utf-8' and $dest_charset=='iso-8859-1')
  {
    return utf8_decode($str);
  }
  if (function_exists('iconv'))
  {
    return iconv($source_charset, $dest_charset, $str);
  }
  if (function_exists('mb_convert_encoding'))
  {
    return mb_convert_encoding( $str, $dest_charset, $source_charset );
  }
  return $str; //???
}
?>