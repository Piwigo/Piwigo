<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

include_once( PHPWG_ROOT_PATH .'include/functions_user.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_cookie.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_session.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_category.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_xml.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_html.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_tag.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_url.inc.php' );
include_once( PHPWG_ROOT_PATH .'include/functions_plugins.inc.php' );

//----------------------------------------------------------- generic functions

/**
 * stupidly returns the current microsecond since Unix epoch
 */
function micro_seconds()
{
  $t1 = explode(' ', microtime());
  $t2 = explode('.', $t1[0]);
  $t2 = $t1[1].substr($t2[1], 0, 6);
  return $t2;
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
  ; // -> 0
  $end   = strpos ( $remaining, '>' ); // -> 16
  // as long as a '<' and his friend '>' are found, we loop
  while ( ($start=strpos( $remaining, '<' )) !==false
        and ($end=strpos( $remaining, '>' )) !== false )
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
 * returns an array contening sub-directories, excluding ".svn"
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
          and $file != '.svn')
      {
        array_push($sub_dirs, $file);
      }
    }
    closedir($opendir);
  }
  return $sub_dirs;
}

define('MKGETDIR_NONE', 0);
define('MKGETDIR_RECURSIVE', 1);
define('MKGETDIR_DIE_ON_ERROR', 2);
define('MKGETDIR_PROTECT_INDEX', 4);
define('MKGETDIR_PROTECT_HTACCESS', 8);
define('MKGETDIR_DEFAULT', 7);
/**
 * creates directory if not exists; ensures that directory is writable
 * @param:
 *  string $dir
 *  int $flags combination of MKGETDIR_xxx
 * @return bool false on error else true
 */
function mkgetdir($dir, $flags=MKGETDIR_DEFAULT)
{
  if ( !is_dir($dir) )
  {
    if (substr(PHP_OS, 0, 3) == 'WIN')
    {
      $dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
    }
    $umask = umask(0);
    $mkd = @mkdir($dir, 0755, ($flags&MKGETDIR_RECURSIVE) ? true:false );
    umask($umask);
    if ($mkd==false)
    {
      !($flags&MKGETDIR_DIE_ON_ERROR) or fatal_error( "$dir ".l10n('no write access'));
      return false;
    }
    if( $flags&MKGETDIR_PROTECT_HTACCESS )
    {
      $file = $dir.'/.htaccess';
      file_exists($file) or @file_put_contents( $file, 'deny from all' );
    }
    if( $flags&MKGETDIR_PROTECT_INDEX )
    {
      $file = $dir.'/index.htm';
      file_exists($file) or @file_put_contents( $file, 'Not allowed!' );
    }
  }
  if ( !is_writable($dir) )
  {
    !($flags&MKGETDIR_DIE_ON_ERROR) or fatal_error( "$dir ".l10n('no write access'));
    return false;
  }
  return true;
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
  global $conf;

  $tndir = $dirname.'/'.$conf['dir_thumbnail'];
  if (! mkgetdir($tndir, MKGETDIR_NONE) )
  {
    array_push($errors,
          '['.$dirname.'] : '.l10n('no write access'));
    return false;
  }
  return $tndir;
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
    "\xc3\x80"=>'A', "\xc3\x81"=>'A',
    "\xc3\x82"=>'A', "\xc3\x83"=>'A',
    "\xc3\x84"=>'A', "\xc3\x85"=>'A',
    "\xc3\x87"=>'C', "\xc3\x88"=>'E',
    "\xc3\x89"=>'E', "\xc3\x8a"=>'E',
    "\xc3\x8b"=>'E', "\xc3\x8c"=>'I',
    "\xc3\x8d"=>'I', "\xc3\x8e"=>'I',
    "\xc3\x8f"=>'I', "\xc3\x91"=>'N',
    "\xc3\x92"=>'O', "\xc3\x93"=>'O',
    "\xc3\x94"=>'O', "\xc3\x95"=>'O',
    "\xc3\x96"=>'O', "\xc3\x99"=>'U',
    "\xc3\x9a"=>'U', "\xc3\x9b"=>'U',
    "\xc3\x9c"=>'U', "\xc3\x9d"=>'Y',
    "\xc3\x9f"=>'s', "\xc3\xa0"=>'a',
    "\xc3\xa1"=>'a', "\xc3\xa2"=>'a',
    "\xc3\xa3"=>'a', "\xc3\xa4"=>'a',
    "\xc3\xa5"=>'a', "\xc3\xa7"=>'c',
    "\xc3\xa8"=>'e', "\xc3\xa9"=>'e',
    "\xc3\xaa"=>'e', "\xc3\xab"=>'e',
    "\xc3\xac"=>'i', "\xc3\xad"=>'i',
    "\xc3\xae"=>'i', "\xc3\xaf"=>'i',
    "\xc3\xb1"=>'n', "\xc3\xb2"=>'o',
    "\xc3\xb3"=>'o', "\xc3\xb4"=>'o',
    "\xc3\xb5"=>'o', "\xc3\xb6"=>'o',
    "\xc3\xb9"=>'u', "\xc3\xba"=>'u',
    "\xc3\xbb"=>'u', "\xc3\xbc"=>'u',
    "\xc3\xbd"=>'y', "\xc3\xbf"=>'y',
    // Decompositions for Latin Extended-A
    "\xc4\x80"=>'A', "\xc4\x81"=>'a',
    "\xc4\x82"=>'A', "\xc4\x83"=>'a',
    "\xc4\x84"=>'A', "\xc4\x85"=>'a',
    "\xc4\x86"=>'C', "\xc4\x87"=>'c',
    "\xc4\x88"=>'C', "\xc4\x89"=>'c',
    "\xc4\x8a"=>'C', "\xc4\x8b"=>'c',
    "\xc4\x8c"=>'C', "\xc4\x8d"=>'c',
    "\xc4\x8e"=>'D', "\xc4\x8f"=>'d',
    "\xc4\x90"=>'D', "\xc4\x91"=>'d',
    "\xc4\x92"=>'E', "\xc4\x93"=>'e',
    "\xc4\x94"=>'E', "\xc4\x95"=>'e',
    "\xc4\x96"=>'E', "\xc4\x97"=>'e',
    "\xc4\x98"=>'E', "\xc4\x99"=>'e',
    "\xc4\x9a"=>'E', "\xc4\x9b"=>'e',
    "\xc4\x9c"=>'G', "\xc4\x9d"=>'g',
    "\xc4\x9e"=>'G', "\xc4\x9f"=>'g',
    "\xc4\xa0"=>'G', "\xc4\xa1"=>'g',
    "\xc4\xa2"=>'G', "\xc4\xa3"=>'g',
    "\xc4\xa4"=>'H', "\xc4\xa5"=>'h',
    "\xc4\xa6"=>'H', "\xc4\xa7"=>'h',
    "\xc4\xa8"=>'I', "\xc4\xa9"=>'i',
    "\xc4\xaa"=>'I', "\xc4\xab"=>'i',
    "\xc4\xac"=>'I', "\xc4\xad"=>'i',
    "\xc4\xae"=>'I', "\xc4\xaf"=>'i',
    "\xc4\xb0"=>'I', "\xc4\xb1"=>'i',
    "\xc4\xb2"=>'IJ', "\xc4\xb3"=>'ij',
    "\xc4\xb4"=>'J', "\xc4\xb5"=>'j',
    "\xc4\xb6"=>'K', "\xc4\xb7"=>'k',
    "\xc4\xb8"=>'k', "\xc4\xb9"=>'L',
    "\xc4\xba"=>'l', "\xc4\xbb"=>'L',
    "\xc4\xbc"=>'l', "\xc4\xbd"=>'L',
    "\xc4\xbe"=>'l', "\xc4\xbf"=>'L',
    "\xc5\x80"=>'l', "\xc5\x81"=>'L',
    "\xc5\x82"=>'l', "\xc5\x83"=>'N',
    "\xc5\x84"=>'n', "\xc5\x85"=>'N',
    "\xc5\x86"=>'n', "\xc5\x87"=>'N',
    "\xc5\x88"=>'n', "\xc5\x89"=>'N',
    "\xc5\x8a"=>'n', "\xc5\x8b"=>'N',
    "\xc5\x8c"=>'O', "\xc5\x8d"=>'o',
    "\xc5\x8e"=>'O', "\xc5\x8f"=>'o',
    "\xc5\x90"=>'O', "\xc5\x91"=>'o',
    "\xc5\x92"=>'OE', "\xc5\x93"=>'oe',
    "\xc5\x94"=>'R', "\xc5\x95"=>'r',
    "\xc5\x96"=>'R', "\xc5\x97"=>'r',
    "\xc5\x98"=>'R', "\xc5\x99"=>'r',
    "\xc5\x9a"=>'S', "\xc5\x9b"=>'s',
    "\xc5\x9c"=>'S', "\xc5\x9d"=>'s',
    "\xc5\x9e"=>'S', "\xc5\x9f"=>'s',
    "\xc5\xa0"=>'S', "\xc5\xa1"=>'s',
    "\xc5\xa2"=>'T', "\xc5\xa3"=>'t',
    "\xc5\xa4"=>'T', "\xc5\xa5"=>'t',
    "\xc5\xa6"=>'T', "\xc5\xa7"=>'t',
    "\xc5\xa8"=>'U', "\xc5\xa9"=>'u',
    "\xc5\xaa"=>'U', "\xc5\xab"=>'u',
    "\xc5\xac"=>'U', "\xc5\xad"=>'u',
    "\xc5\xae"=>'U', "\xc5\xaf"=>'u',
    "\xc5\xb0"=>'U', "\xc5\xb1"=>'u',
    "\xc5\xb2"=>'U', "\xc5\xb3"=>'u',
    "\xc5\xb4"=>'W', "\xc5\xb5"=>'w',
    "\xc5\xb6"=>'Y', "\xc5\xb7"=>'y',
    "\xc5\xb8"=>'Y', "\xc5\xb9"=>'Z',
    "\xc5\xba"=>'z', "\xc5\xbb"=>'Z',
    "\xc5\xbc"=>'z', "\xc5\xbd"=>'Z',
    "\xc5\xbe"=>'z', "\xc5\xbf"=>'s',
    // Euro Sign
    "\xe2\x82\xac"=>'E',
    // GBP (Pound) Sign
    "\xc2\xa3"=>'');

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
  $raw = $str;

  $str = remove_accents($str);
  $str = preg_replace('/[^a-z0-9_\s\'\:\/\[\],-]/','',strtolower($str));
  $str = preg_replace('/[\s\'\:\/\[\],-]+/',' ',trim($str));
  $res = str_replace(' ','_',$str);

  if (empty($res))
  {
    $res = str_replace(' ','_', $raw);
  }

  return $res;
}

//-------------------------------------------- Piwigo specific functions

/**
 * returns an array with a list of {language_code => language_name}
 *
 * @returns array
 */
function get_languages()
{
  $query = '
SELECT id, name
  FROM '.LANGUAGES_TABLE.'
  ORDER BY name ASC
;';
  $result = pwg_query($query);

  $languages = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (is_dir(PHPWG_ROOT_PATH.'language/'.$row['id']))
    {
      $languages[ $row['id'] ] = $row['name'];
    }
  }

  return $languages;
}

function pwg_log($image_id = null, $image_type = null)
{
  global $conf, $user, $page;

  $do_log = $conf['log'];
  if (is_admin())
  {
    $do_log = $conf['history_admin'];
  }
  if (is_a_guest())
  {
    $do_log = $conf['history_guest'];
  }

  $do_log = trigger_event('pwg_log_allowed', $do_log, $image_id, $image_type);

  if (!$do_log)
  {
    return false;
  }

  $tags_string = null;
  if ('tags'==@$page['section'])
  {
    $tags_string = implode(',', $page['tag_ids']);
  }

  $query = '
INSERT INTO '.HISTORY_TABLE.'
  (
    date,
    time,
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
    CURRENT_DATE,
    CURRENT_TIME,
    '.$user['id'].',
    \''.$_SERVER['REMOTE_ADDR'].'\',
    '.(isset($page['section']) ? "'".$page['section']."'" : 'NULL').',
    '.(isset($page['category']['id']) ? $page['category']['id'] : 'NULL').',
    '.(isset($image_id) ? $image_id : 'NULL').',
    '.(isset($image_type) ? "'".$image_type."'" : 'NULL').',
    '.(isset($tags_string) ? "'".$tags_string."'" : 'NULL').'
  )
;';
  pwg_query($query);

  return true;
}

// format_date returns a formatted date for display. The date given in
// argument must be an american format (2003-09-15). By option, you can show the time.
// The output is internationalized.
//
// format_date( "2003-09-15", true ) -> "Monday 15 September 2003 21:52"
function format_date($date, $show_time = false)
{
  global $lang;

  if (strpos($date, '0') == 0)
  {
    return l10n('N/A');
  }

  $ymdhms = array();
  $tok = strtok( $date, '- :');
  while ($tok !== false)
  {
    $ymdhms[] = $tok;
    $tok = strtok('- :');
  }

  if ( count($ymdhms)<3 )
  {
    return false;
  }

  $formated_date = '';
  // before 1970, Microsoft Windows can't mktime
  if ($ymdhms[0] >= 1970)
  {
    // we ask midday because Windows think it's prior to midnight with a
    // zero and refuse to work
    $formated_date.= $lang['day'][date('w', mktime(12,0,0,$ymdhms[1],$ymdhms[2],$ymdhms[0]))];
  }
  $formated_date.= ' '.$ymdhms[2];
  $formated_date.= ' '.$lang['month'][(int)$ymdhms[1]];
  $formated_date.= ' '.$ymdhms[0];
  if ($show_time and count($ymdhms)>=5 )
  {
    $formated_date.= ' '.$ymdhms[3].':'.$ymdhms[4];
  }
  return $formated_date;
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
  // default url is on html format
  $url = html_entity_decode($url);
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

  if (!isset($lang_info) || !isset($template) )
  {
    $user = build_user( $conf['guest_id'], true);
    load_language('common.lang');
    trigger_action('loading_lang');
    load_language('lang', PHPWG_ROOT_PATH.PWG_LOCAL_DIR, array('no_fallback'=>true, 'local'=>true) );
    $template = new Template(PHPWG_ROOT_PATH.'themes', get_default_theme());
  }
	elseif (defined('IN_ADMIN') and IN_ADMIN)
	{
		$template = new Template(PHPWG_ROOT_PATH.'themes', get_default_theme());
	}

  if (empty($msg))
  {
    $msg = nl2br(l10n('Redirection...'));
  }

  $refresh = $refresh_time;
  $url_link = $url;
  $title = 'redirection';

  $template->set_filenames( array( 'redirect' => 'redirect.tpl' ) );

  include( PHPWG_ROOT_PATH.'include/page_header.php' );

  $template->set_filenames( array( 'redirect' => 'redirect.tpl' ) );
  $template->assign('REDIRECT_MSG', $msg);

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
 * returns available themes
 */
function get_pwg_themes()
{
  global $conf;

  $themes = array();

  $query = '
SELECT
    id,
    name
  FROM '.THEMES_TABLE.'
  ORDER BY name ASC
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (check_theme_installed($row['id']))
    {
      $themes[ $row['id'] ] = $row['name'];
    }
  }

  // plugins want remove some themes based on user status maybe?
  $themes = trigger_event('get_pwg_themes', $themes);

  return $themes;
}

function check_theme_installed($theme_id)
{
  global $conf;

  return file_exists($conf['themes_dir'].'/'.$theme_id.'/'.'themeconf.inc.php');
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
  $loc = $url = get_thumbnail_location($element_info);
  if ( !url_is_remote($loc) )
  {
    $url = (get_root_url().$loc);
  }
  // plugins want another url ?
  $url = trigger_event('get_thumbnail_url', $url, $element_info, $loc);
  return embellish_url($url);
}

/* returns the relative path of the thumnail with regards to to the root
of piwigo (not the current page!).This function is not intended to be
called directly from code.*/
function get_thumbnail_location($element_info)
{
  global $conf;
  if ( !empty( $element_info['tn_ext'] ) )
  {
    $path = substr_replace(
      get_filename_wo_extension($element_info['path']),
      '/'.$conf['dir_thumbnail'].'/'.$conf['prefix_thumbnail'],
      strrpos($element_info['path'],'/'),
      1
      );
    $path.= '.'.$element_info['tn_ext'];
  }
  else
  {
    $path = get_themeconf('mime_icon_dir')
        .strtolower(get_extension($element_info['path'])).'.png';
    // plugins want another location ?
    $path = trigger_event( 'get_thumbnail_location', $path, $element_info);
  }
  return $path;
}

/**
 * returns the title of the thumbnail based on photo properties
 */
function get_thumbnail_title($info)
{
  global $conf, $user;

  $title = get_picture_title($info);

  $details = array();

  if ($info['hit'] != 0)
  {
    $details[] = $info['hit'].' '.strtolower(l10n('Visits'));
  }

  if ($conf['rate'] and !empty($info['rating_score']))
  {
    $details[] = strtolower(l10n('Rating score')).' '.$info['rating_score'];
  }

  if (isset($info['nb_comments']) and $info['nb_comments'] != 0)
  {
    $details[] = l10n_dec('%d comment', '%d comments', $info['nb_comments']);
  }

  if (count($details) > 0)
  {
    $title.= ' ('.implode(', ', $details).')';
  }

  if (!empty($info['comment']))
  {
    $title.= ' '.substr($info['comment'], 0, 100).'...';
  }

  $title = strip_tags($title);

  $title = trigger_event('get_thumbnail_title', $title, $info);

  return $title;
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
 */
function get_picture_title($info)
{
  if (isset($info['name']) and !empty($info['name']))
  {
    return $info['name'];
  }

  return  get_name_from_file($info['file']);
}

/**
 * returns the corresponding value from $lang if existing. Else, the key is
 * returned
 *
 * @param string key
 * @return string
 */
function l10n($key, $textdomain='messages')
{
  global $lang, $conf;

  if ($conf['debug_l10n'] and !isset($lang[$key]) and !empty($key))
  {
    trigger_error('[l10n] language key "'.$key.'" is not defined', E_USER_WARNING);
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
    fatal_error('l10n_args: Invalid arguments');
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
  list($email) = pwg_db_fetch_row(pwg_query($query));

  return $email;
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

  if ((pwg_db_num_rows($result) == 0) and !empty($condition))
  {
    fatal_error('No configuration data');
  }

  while ($row = pwg_db_fetch_assoc($result))
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

function conf_update_param($param, $value)
{
  $query = '
SELECT
    param,
    value
  FROM '.CONFIG_TABLE.'
  WHERE param = \''.$param.'\'
;';
  $params = array_from_query($query, 'param');

  if (count($params) == 0)
  {
    $query = '
INSERT
  INTO '.CONFIG_TABLE.'
  (param, value)
  VALUES(\''.$param.'\', \''.$value.'\')
;';
    pwg_query($query);
  }
  else
  {
    $query = '
UPDATE '.CONFIG_TABLE.'
  SET value = \''.$value.'\'
  WHERE param = \''.$param.'\'
;';
    pwg_query($query);
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
  while ($row = pwg_db_fetch_assoc($result))
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
  while ($row = pwg_db_fetch_assoc($result))
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
    if (!empty($_SERVER[$value]))
    {
      $filename = strtolower($_SERVER[$value]);
      if ($conf['php_extension_in_urls'] and get_extension($filename)!=='php')
        continue;
      $basename = basename($filename, '.php');
      if (!empty($basename))
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
  $pwg_charset = 'utf-8';
  if (defined('PWG_CHARSET'))
  {
    $pwg_charset = PWG_CHARSET;
  }
  return $pwg_charset;
}

/**
 * includes a language file or returns the content of a language file
 * availability of the file
 *
 * in descending order of preference:
 *   param language, user language, default language
 * Piwigo default language.
 *
 * @param string filename
 * @param string dirname
 * @param mixed options can contain
 *     language - language to load (if empty uses user language)
 *     return - if true the file content is returned otherwise the file is evaluated as php
 *     target_charset -
 *     no_fallback - the language must be respected
 *     local - if true, get local language file
 * @return boolean success status or a string if options['return'] is true
 */
function load_language($filename, $dirname = '',
    $options = array() )
{
  global $user;

  if (! @$options['return'] )
  {
    $filename .= '.php'; //MAYBE to do .. load .po and .mo localization files
  }
  if (empty($dirname))
  {
    $dirname = PHPWG_ROOT_PATH;
  }
  $dirname .= 'language/';

  $languages = array();
  if ( !empty($options['language']) )
  {
    $languages[] = $options['language'];
  }
  if ( !empty($user['language']) )
  {
    $languages[] = $user['language'];
  }
  if ( ! @$options['no_fallback'] )
  {
    if ( defined('PHPWG_INSTALLED') )
    {
      $languages[] = get_default_language();
    }
    $languages[] = PHPWG_DEFAULT_LANGUAGE;
  }

  $languages = array_unique($languages);

  if ( empty($options['target_charset']) )
  {
    $target_charset = get_pwg_charset();
  }
  else
  {
    $target_charset = $options['target_charset'];
  }
  $target_charset = strtolower($target_charset);
  $source_file    = '';
  foreach ($languages as $language)
  {
    $f = @$options['local'] ?
      $dirname.$language.'.'.$filename:
      $dirname.$language.'/'.$filename;

    if (file_exists($f))
    {
      $source_file = $f;
      break;
    }
  }

  if ( !empty($source_file) )
  {
    if (! @$options['return'] )
    {
      @include($source_file);
      $load_lang = @$lang;
      $load_lang_info = @$lang_info;

      global $lang, $lang_info;
      if ( !isset($lang) ) $lang=array();
      if ( !isset($lang_info) ) $lang_info=array();

      if ( 'utf-8'!=$target_charset)
      {
        if ( is_array($load_lang) )
        {
          foreach ($load_lang as $k => $v)
          {
            if ( is_array($v) )
            {
              $func = create_function('$v', 'return convert_charset($v, "utf-8", "'.$target_charset.'");' );
              $lang[$k] = array_map($func, $v);
            }
            else
              $lang[$k] = convert_charset($v, 'utf-8', $target_charset);
          }
        }
        if ( is_array($load_lang_info) )
        {
          foreach ($load_lang_info as $k => $v)
          {
            $lang_info[$k] = convert_charset($v, 'utf-8', $target_charset);
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
      $content = convert_charset($content, 'utf-8', $target_charset);
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

/**
 * makes sure a index.htm protects the directory from browser file listing
 *
 * @param string dir directory
 */
function secure_directory($dir)
{
  $file = $dir.'/index.htm';
  if (!file_exists($file))
  {
    @file_put_contents($file, 'Not allowed!');
  }
}

/**
 * returns a "secret key" that is to be sent back when a user posts a form
 *
 * @param int valid_after_seconds - key validity start time from now
 */
function get_ephemeral_key($valid_after_seconds, $aditionnal_data_to_hash = '')
{
	global $conf;
	$time = round(microtime(true), 1);
	return $time.':'.$valid_after_seconds.':'
		.hash_hmac(
			'md5', 
			$time.substr($_SERVER['REMOTE_ADDR'],0,5).$valid_after_seconds.$aditionnal_data_to_hash, 
			$conf['secret_key']);
}

function verify_ephemeral_key($key, $aditionnal_data_to_hash = '')
{
	global $conf;
	$time = microtime(true);
	$key = explode( ':', @$key );
	if ( count($key)!=3
		or $key[0]>$time-(float)$key[1] // page must have been retrieved more than X sec ago
		or $key[0]<$time-3600 // 60 minutes expiration
		or hash_hmac(
			  'md5', $key[0].substr($_SERVER['REMOTE_ADDR'],0,5).$key[1].$aditionnal_data_to_hash, $conf['secret_key']
			) != $key[2]
	  )
	{
		return false;
	}
	return true;
}

/**
 * return an array which will be sent to template to display navigation bar
 */
function create_navigation_bar($url, $nb_element, $start, $nb_element_page, $clean_url = false)
{
  global $conf;

  $navbar = array();
  $pages_around = $conf['paginate_pages_around'];
  $start_str = $clean_url ? '/start-' : (strpos($url, '?')===false ? '?':'&amp;').'start=';

  if (!isset($start) or !is_numeric($start) or (is_numeric($start) and $start < 0))
  {
    $start = 0;
  }

  // navigation bar useful only if more than one page to display !
  if ($nb_element > $nb_element_page)
  {
    $cur_page = ceil($start / $nb_element_page) + 1;
    $maximum = ceil($nb_element / $nb_element_page);
    $previous = $start - $nb_element_page;
    $next = $start + $nb_element_page;
    $last = ($maximum - 1) * $nb_element_page;

    $navbar['CURRENT_PAGE'] = $cur_page;

    // link to first page and previous page?
    if ($cur_page != 1)
    {
      $navbar['URL_FIRST'] = $url;
      $navbar['URL_PREV'] = $url.($previous > 0 ? $start_str.$previous : '');
    }
    // link on next page and last page?
    if ($cur_page != $maximum)
    {
      $navbar['URL_NEXT'] = $url.$start_str.$next;
      $navbar['URL_LAST'] = $url.$start_str.$last;
    }

    // pages to display
    $navbar['pages'] = array();
    $navbar['pages'][1] = $url;
    $navbar['pages'][$maximum] = $url.$start_str.$last;

    for ($i = max($cur_page - $pages_around , 2), $stop = min($cur_page + $pages_around + 1, $maximum);
         $i < $stop; $i++)
    {
      $navbar['pages'][$i] = $url.$start_str.(($i - 1) * $nb_element_page);
    }
    ksort($navbar['pages']);
  }
  return $navbar;
}

/**
 * return an array which will be sent to template to display recent icon
 */
function get_icon($date, $is_child_date = false)
{
  global $cache, $user;

  if (empty($date))
  {
    return false;
  }

  if (!isset($cache['get_icon']['title']))
  {
    $cache['get_icon']['title'] = sprintf(
      l10n('photos posted during the last %d days'),
      $user['recent_period']
      );
  }

  $icon = array(
    'TITLE' => $cache['get_icon']['title'],
    'IS_CHILD_DATE' => $is_child_date,
    );

  if (isset($cache['get_icon'][$date]))
  {
    return $cache['get_icon'][$date] ? $icon : array();
  }

  if (!isset($cache['get_icon']['sql_recent_date']))
  {
    // Use MySql date in order to standardize all recent "actions/queries"
    $cache['get_icon']['sql_recent_date'] = pwg_db_get_recent_period($user['recent_period']);
  }

  $cache['get_icon'][$date] = $date > $cache['get_icon']['sql_recent_date'];

  return $cache['get_icon'][$date] ? $icon : array();
}

/**
 * check token comming from form posted or get params to prevent csrf attacks
 * if pwg_token is empty action doesn't require token
 * else pwg_token is compare to server token
 *
 * @return void access denied if token given is not equal to server token
 */
function check_pwg_token()
{
  if (!empty($_REQUEST['pwg_token']))
  {
    if (get_pwg_token() != $_REQUEST['pwg_token'])
    {
      access_denied();
    }
  }
  else
    bad_request('missing token');
}

function get_pwg_token()
{
  global $conf;

  return hash_hmac('md5', session_id(), $conf['secret_key']);
}

/*
 * breaks the script execution if the given value doesn't match the given
 * pattern. This should happen only during hacking attempts.
 *
 * @param string param_name
 * @param array param_array
 * @param boolean is_array
 * @param string pattern
 *
 * @return void
 */
function check_input_parameter($param_name, $param_array, $is_array, $pattern)
{
  $param_value = null;
  if (isset($param_array[$param_name]))
  {
    $param_value = $param_array[$param_name];
  }

  // it's ok if the input parameter is null
  if (empty($param_value))
  {
    return true;
  }

  if ($is_array)
  {
    if (!is_array($param_value))
    {
      fatal_error('[Hacking attempt] the input parameter "'.$param_name.'" should be an array');
    }

    foreach ($param_value as $item_to_check)
    {
      if (!preg_match($pattern, $item_to_check))
      {
        fatal_error('[Hacking attempt] an item is not valid in input parameter "'.$param_name.'"');
      }
    }
  }
  else
  {
    if (!preg_match($pattern, $param_value))
    {
      fatal_error('[Hacking attempt] the input parameter "'.$param_name.'" is not valid');
    }
  }
}


function get_privacy_level_options()
{
  global $conf;

  $options = array();
  foreach (array_reverse($conf['available_permission_levels']) as $level)
  {
    $label = null;

    if (0 == $level)
    {
      $label = l10n('Everybody');
    }
    else
    {
      $labels = array();
      $sub_levels = array_reverse($conf['available_permission_levels']);
      foreach ($sub_levels as $sub_level)
      {
        if ($sub_level == 0 or $sub_level < $level)
        {
          break;
        }
        array_push(
          $labels,
          l10n(
            sprintf(
              'Level %d',
              $sub_level
              )
            )
          );
      }

      $label = implode(', ', $labels);
    }
    $options[$level] = $label;
  }
  return $options;
}


/**
 * return the branch from the version. For example version 2.2.4 is for branch 2.2
 */
function get_branch_from_version($version)
{
  return implode('.', array_slice(explode('.', $version), 0, 2));
}
?>