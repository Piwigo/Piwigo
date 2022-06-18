<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH','./');

// fast bootstrap - no db connection
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');

defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');
defined('PWG_DERIVATIVE_DIR') or define('PWG_DERIVATIVE_DIR', $conf['data_location'].'i/');

@include(PHPWG_ROOT_PATH.PWG_LOCAL_DIR .'config/database.inc.php');

include(PHPWG_ROOT_PATH . 'include/Logger.class.php');

$logger = new Logger(array(
  'directory' => PHPWG_ROOT_PATH . $conf['data_location'] . $conf['log_dir'],
  'severity' => $conf['log_level'],
  // we use an hashed filename to prevent direct file access, and we salt with
  // the db_password instead of secret_key because the log must be usable in i.php
  // (secret_key is in the database)
  'filename' => 'log_' . date('Y-m-d') . '_' . sha1(date('Y-m-d') . $conf['db_password']) . '.txt',
  ));


function trigger_notify() {}
function get_extension( $filename )
{
  return substr( strrchr( $filename, '.' ), 1, strlen ( $filename ) );
}

function mkgetdir($dir)
{
  if ( !is_dir($dir) )
  {
    global $conf;
    if (substr(PHP_OS, 0, 3) == 'WIN')
    {
      $dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
    }
    $umask = umask(0);
    $mkd = @mkdir($dir, $conf['chmod_value'], true);
    umask($umask);
    if ($mkd==false && !is_dir($dir) /* retest existence because of potential concurrent i.php with slow file systems*/)
    {
      return false;
    }

    $file = $dir.'/index.htm';
    file_exists($file) or @file_put_contents( $file, 'Not allowed!' );
  }
  if ( !is_writable($dir) )
  {
    return false;
  }
  return true;
}

// end fast bootstrap

function ierror($msg, $code)
{
  global $logger;
  if ($code==301 || $code==302)
  {
    if (ob_get_length () !== FALSE)
    {
      ob_clean();
    }
    // default url is on html format
    $url = html_entity_decode($msg);
    $logger->debug($code . ' ' . $url, 'i.php', array(
      'url' => $_SERVER['REQUEST_URI'],
      ));
    header('Request-URI: '.$url);
    header('Content-Location: '.$url);
    header('Location: '.$url);
    exit;
  }
  if ($code>=400)
  {
    $protocol = $_SERVER["SERVER_PROTOCOL"];
    if ( ('HTTP/1.1' != $protocol) && ('HTTP/1.0' != $protocol) )
      $protocol = 'HTTP/1.0';

    header( "$protocol $code $msg", true, $code );
  }
  //todo improve
  echo $msg;
  $logger->error($code . ' ' . $msg, 'i.php', array(
      'url' => $_SERVER['REQUEST_URI'],
      ));
  exit;
}

function time_step( &$step )
{
  $tmp = $step;
  $step = microtime(true);
  return intval(1000*($step - $tmp));
}

function url_to_size($s)
{
  $pos = strpos($s, 'x');
  if ($pos===false)
  {
    return array((int)$s, (int)$s);
  }
  return array((int)substr($s,0,$pos), (int)substr($s,$pos+1));
}

function parse_custom_params($tokens)
{
  if (count($tokens)<1)
    ierror('Empty array while parsing Sizing', 400);

  $crop = 0;
  $min_size = null;

  $token = array_shift($tokens);
  if ($token[0]=='s')
  {
    $size = url_to_size( substr($token,1) );
  }
  elseif ($token[0]=='e')
  {
    $crop = 1;
    $size = $min_size = url_to_size( substr($token,1) );
  }
  else
  {
    $size = url_to_size( $token );
    if (count($tokens)<2)
      ierror('Sizing arr', 400);

    $token = array_shift($tokens);
    $crop = char_to_fraction($token);

    $token = array_shift($tokens);
    $min_size = url_to_size( $token );
  }
  return new DerivativeParams( new SizingParams($size, $crop, $min_size) );
}

function parse_request()
{
  global $conf, $page;

  if ( $conf['question_mark_in_urls']==false and
       isset($_SERVER["PATH_INFO"]) and !empty($_SERVER["PATH_INFO"]) )
  {
    $req = $_SERVER["PATH_INFO"];
    $req = str_replace('//', '/', $req);
    $path_count = count( explode('/', $req) );
    $page['root_path'] = PHPWG_ROOT_PATH.str_repeat('../', $path_count-1);
  }
  else
  {
    $req = $_SERVER["QUERY_STRING"];
    if ($pos=strpos($req, '&'))
    {
      $req = substr($req, 0, $pos);
    }
    $req = rawurldecode($req);
    /*foreach (array_keys($_GET) as $keynum => $key)
    {
      $req = $key;
      break;
    }*/
    $page['root_path'] = PHPWG_ROOT_PATH;
  }

  $req = ltrim($req, '/');

  foreach (preg_split('#/+#', $req) as $token)
  {
    preg_match($conf['sync_chars_regex'], $token) or ierror('Invalid chars in request', 400);
  }

  $page['derivative_path'] = PHPWG_ROOT_PATH.PWG_DERIVATIVE_DIR.$req;

  $pos = strrpos($req, '.');
  $pos!== false || ierror('Missing .', 400);
  $ext = substr($req, $pos);
  $page['derivative_ext'] = $ext;
  $req = substr($req, 0, $pos);

  $pos = strrpos($req, '-');
  $pos!== false || ierror('Missing -', 400);
  $deriv = substr($req, $pos+1);
  $req = substr($req, 0, $pos);

  $deriv = explode('_', $deriv);
  foreach (ImageStdParams::get_defined_type_map() as $type => $params)
  {
    if ( derivative_to_url($type) == $deriv[0])
    {
      $page['derivative_type'] = $type;
      $page['derivative_params'] = $params;
      break;
    }
  }

  if (!isset($page['derivative_type']))
  {
    if (derivative_to_url(IMG_CUSTOM) == $deriv[0])
    {
      $page['derivative_type'] = IMG_CUSTOM;
    }
    else
    {
      ierror('Unknown parsing type', 400);
    }
  }
  array_shift($deriv);

  if ($page['derivative_type'] == IMG_CUSTOM)
  {
    $params = $page['derivative_params'] = parse_custom_params($deriv);
    ImageStdParams::apply_global($params);

    if ($params->sizing->ideal_size[0] < 20 or $params->sizing->ideal_size[1] < 20)
    {
      ierror('Invalid size', 400);
    }
    if ($params->sizing->max_crop < 0 or $params->sizing->max_crop > 1)
    {
      ierror('Invalid crop', 400);
    }
    $greatest = ImageStdParams::get_by_type(IMG_XXLARGE);

    $key = array();
    $params->add_url_tokens($key);
    $key = implode('_', $key);
    if (!isset(ImageStdParams::$custom[$key]))
    {
      ierror('Size not allowed', 403);
    }
  }

  if (is_file(PHPWG_ROOT_PATH.$req.$ext))
  {
    $req = './'.$req; // will be used to match #iamges.path
  }
  elseif (is_file(PHPWG_ROOT_PATH.'../'.$req.$ext))
  {
    $req = '../'.$req;
  }

  $page['src_location'] = $req.$ext;
  $page['src_path'] = PHPWG_ROOT_PATH.$page['src_location'];
  $page['src_url'] = $page['root_path'].$page['src_location'];
}

function try_switch_source(DerivativeParams $params, $original_mtime)
{
  global $page;
  if (!isset($page['original_size']))
    return false;

  $original_size = $page['original_size'];
  if ($page['rotation_angle']==90 || $page['rotation_angle']==270)
  {
    $tmp = $original_size[0];
    $original_size[0] = $original_size[1];
    $original_size[1] = $tmp;
  }
  $dsize = $params->compute_final_size($original_size);

  $use_watermark = $params->use_watermark;
  if ($use_watermark)
  {
    $use_watermark = $params->will_watermark($dsize);
  }

  $candidates = array();
  foreach(ImageStdParams::get_defined_type_map() as $candidate)
  {
    if ($candidate->type == $params->type)
      continue;
    if ($candidate->use_watermark != $use_watermark)
      continue;
    if ($candidate->max_width() < $params->max_width() || $candidate->max_height() < $params->max_height())
      continue;
    $candidate_size = $candidate->compute_final_size($original_size);
    if ($dsize != $params->compute_final_size($candidate_size))
      continue;

    if ($params->sizing->max_crop==0)
    {
      if ($candidate->sizing->max_crop!=0)
        continue;
    }
    else
    {
      if ($use_watermark && $candidate->use_watermark)
        continue; //a square that requires watermark should not be generated from a larger derivative with watermark, because if the watermark is not centered on the large image, it will be cropped.
      if ($candidate->sizing->max_crop!=0)
        continue; // this could be optimized
      if ($candidate_size[0] < $params->sizing->min_size[0] || $candidate_size[1] < $params->sizing->min_size[1] )
        continue;
    }
    $candidates[] = $candidate;
  }

  foreach( array_reverse($candidates) as $candidate)
  {
    $candidate_path = $page['derivative_path'];
    $candidate_path = str_replace( '-'.derivative_to_url($params->type), '-'.derivative_to_url($candidate->type), $candidate_path);
    $candidate_mtime = @filemtime($candidate_path);
    if ($candidate_mtime === false
      || $candidate_mtime < $original_mtime
      || $candidate_mtime < $candidate->last_mod_time)
      continue;
    $params->use_watermark = false;
    $params->sharpen = min(1, $params->sharpen);
    $page['src_path'] = $candidate_path;
    $page['src_url'] = $page['root_path'] . substr($candidate_path, strlen(PHPWG_ROOT_PATH));
    $page['rotation_angle'] = 0;
		return true;
  }
	return false;
}

function send_derivative($expires)
{
  global $page;

  if (isset($_GET['ajaxload']) and $_GET['ajaxload'] == 'true')
  {
    include_once(PHPWG_ROOT_PATH.'include/functions_cookie.inc.php');
    include_once(PHPWG_ROOT_PATH.'include/functions_url.inc.php');

    echo json_encode( array( 'url'=>embellish_url(get_absolute_root_url().$page['derivative_path']) ) );
    return;
  }
  $fp = fopen($page['derivative_path'], 'rb');

  $fstat = fstat($fp);
  header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fstat['mtime']).' GMT');
  if ($expires!==false)
  {
    header('Expires: '.gmdate('D, d M Y H:i:s', $expires).' GMT');
  }
  header('Content-length: '.$fstat['size']);
  header('Connection: close');

  $ctype="application/octet-stream";
  switch (strtolower($page['derivative_ext']))
  {
    case ".jpe": case ".jpeg": case ".jpg": $ctype="image/jpeg"; break;
    case ".png": $ctype="image/png"; break;
    case ".gif": $ctype="image/gif"; break;
  }
  header("Content-Type: $ctype");

  fpassthru($fp);
  fclose($fp);
}

$page=array();
$begin = $step = microtime(true);
$timing=array();
foreach( explode(',','load,rotate,crop,scale,sharpen,watermark,save,send') as $k )
{
  $timing[$k] = '';
}

include_once(PHPWG_ROOT_PATH .'include/dblayer/functions_'.$conf['dblayer'].'.inc.php');
include_once( PHPWG_ROOT_PATH .'/include/derivative_params.inc.php');
include_once( PHPWG_ROOT_PATH .'/include/derivative_std_params.inc.php');

try
{
  pwg_db_connect($conf['db_host'], $conf['db_user'],
                 $conf['db_password'], $conf['db_base']);
}
catch (Exception $e)
{
  $logger->error($e->getMessage(), 'i.php');
}
pwg_db_check_charset();

list($conf['derivatives']) = pwg_db_fetch_row(pwg_query('SELECT value FROM '.$prefixeTable.'config WHERE param=\'derivatives\''));
ImageStdParams::load_from_db();


parse_request();
//var_export($page);

$params = $page['derivative_params'];

$src_mtime = @filemtime($page['src_path']);
if ($src_mtime === false)
{
  ierror('Source not found', 404);
}

$need_generate = false;
$derivative_mtime = @filemtime($page['derivative_path']);
if ($derivative_mtime === false or
    $derivative_mtime < $src_mtime or
    $derivative_mtime < $params->last_mod_time)
{
  $need_generate = true;
}

$expires=false;
$now = time();
if ( isset($_GET['b']) )
{
  $expires = $now + 100;
  header("Cache-control: no-store, max-age=100");
}
elseif ( $now > (max($src_mtime, $params->last_mod_time) + 24*3600) )
{// somehow arbitrary - if derivative params or src didn't change for the last 24 hours, we send an expire header for several days
  $expires = $now + 10*24*3600;
}

if (!$need_generate)
{
  if ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] )
    and strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $derivative_mtime)
  {// send the last mod time of the file back
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $derivative_mtime).' GMT', true, 304);
    header('Expires: '.gmdate('D, d M Y H:i:s', time()+10*24*3600).' GMT', true, 304);
    exit;
  }
  send_derivative($expires);
  exit;
}

include_once(PHPWG_ROOT_PATH . 'admin/include/image.class.php');
$page['coi'] = null;
if (strpos($page['src_location'], '/pwg_representative/')===false
    && strpos($page['src_location'], 'themes/')===false
    && strpos($page['src_location'], 'plugins/')===false)
{
  try
  {
    $query = '
SELECT *
  FROM '.$prefixeTable.'images
  WHERE path=\''.addslashes($page['src_location']).'\'
;';

    if ( ($row=pwg_db_fetch_assoc(pwg_query($query))) )
    {
      if (isset($row['width']))
      {
        $page['original_size'] = array($row['width'],$row['height']);
      }
      $page['coi'] = $row['coi'];

      if (!isset($row['rotation']))
      {
        $page['rotation_angle'] = pwg_image::get_rotation_angle($page['src_path']);

        single_update(
          $prefixeTable.'images',
          array('rotation' => pwg_image::get_rotation_code_from_angle($page['rotation_angle'])),
          array('id' => $row['id'])
          );
      }
      else
      {
        $page['rotation_angle'] = pwg_image::get_rotation_angle_from_code($row['rotation']);
      }
    }
    if (!$row)
    {
      ierror('Db file path not found', 404);
    }
  }
  catch (Exception $e)
  {
    $logger->error($e->getMessage(), 'i.php');
  }
}
else
{
  $page['rotation_angle'] = 0;
}
pwg_db_close();

if (!try_switch_source($params, $src_mtime) && $params->type==IMG_CUSTOM)
{
	$sharpen = 0;
	foreach (ImageStdParams::get_defined_type_map() as $std_params)
	{
		$sharpen += $std_params->sharpen;
	}
	$params->sharpen = round($sharpen / count(ImageStdParams::get_defined_type_map()) );
}

if (!mkgetdir(dirname($page['derivative_path'])))
{
  ierror("dir create error", 500);
}

ignore_user_abort(true);
@set_time_limit(0);

$image = new pwg_image($page['src_path']);
$timing['load'] = time_step($step);

$changes = 0;

// rotate
if (0 != $page['rotation_angle'])
{
  $image->rotate($page['rotation_angle']);
  $changes++;
  $timing['rotate'] = time_step($step);
}

// Crop & scale
$o_size = $d_size = array($image->get_width(),$image->get_height());
$params->sizing->compute($o_size , $page['coi'], $crop_rect, $scaled_size );
if ($crop_rect)
{
  $changes++;
  $image->crop( $crop_rect->width(), $crop_rect->height(), $crop_rect->l, $crop_rect->t);
  $timing['crop'] = time_step($step);
}

if ($scaled_size)
{
  $changes++;
  $image->resize( $scaled_size[0], $scaled_size[1] );
  $d_size = $scaled_size;
  $timing['scale'] = time_step($step);
}

if ($params->sharpen)
{
  $changes += $image->sharpen( $params->sharpen );
  $timing['sharpen'] = time_step($step);
}

if ($params->will_watermark($d_size))
{
  $wm = ImageStdParams::get_watermark();
  $wm_image = new pwg_image(PHPWG_ROOT_PATH.$wm->file);
  $wm_size = array($wm_image->get_width(),$wm_image->get_height());
  if ($d_size[0]<$wm_size[0] or $d_size[1]<$wm_size[1])
  {
    $wm_scaling_params = SizingParams::classic($d_size[0], $d_size[1]);
    $wm_scaling_params->compute($wm_size, null, $tmp, $wm_scaled_size);
    $wm_size = $wm_scaled_size;
    $wm_image->resize( $wm_scaled_size[0], $wm_scaled_size[1] );
  }
  $x = round( ($wm->xpos/100)*($d_size[0]-$wm_size[0]) );
  $y = round( ($wm->ypos/100)*($d_size[1]-$wm_size[1]) );
  if ($image->compose($wm_image, $x, $y, $wm->opacity))
  {
    $changes++;
    if ($wm->xrepeat || $wm->yrepeat)
    {
      $xpad = $wm_size[0] + max(30, round($wm_size[0]/4));
      $ypad = $wm_size[1] + max(30, round($wm_size[1]/4));

      for($i=-$wm->xrepeat; $i<=$wm->xrepeat; $i++)
      {
        for($j=-$wm->yrepeat; $j<=$wm->yrepeat; $j++)
        {
          if (!$i && !$j) continue;
          $x2 = $x + $i * $xpad;
          $y2 = $y + $j * $ypad;
          if ($x2>=0 && $x2+$wm_size[0]<$d_size[0] &&
              $y2>=0 && $y2+$wm_size[1]<$d_size[1] )
            if (!$image->compose($wm_image, $x2, $y2, $wm->opacity))
              break;
        }
      }
    }
  }
  $wm_image->destroy();
  $timing['watermark'] = time_step($step);
}

// no change required - redirect to source
if (!$changes)
{
  header("X-i: No change");
  ierror( $page['src_url'], 301);
}

if ($d_size[0]*$d_size[1] < $conf['derivatives_strip_metadata_threshold'])
{// strip metadata for small images
  $image->strip();
}

$image->set_compression_quality( ImageStdParams::$quality );
$image->write( $page['derivative_path'] );
$image->destroy();
@chmod($page['derivative_path'], 0644);
$timing['save'] = time_step($step);

send_derivative($expires);
$timing['send'] = time_step($step);

$timing['total'] = time_step($begin);

if ($logger->severity() >= Logger::DEBUG)
{
  $logger->debug('', 'i.php', array(
    'src_path' => basename($page['src_path']),
    'derivative_path' => basename($page['derivative_path']),
    'o_size' => $o_size[0] . ' ' . $o_size[1] . ' ' . ($o_size[0]*$o_size[1]),
    'd_size' => $d_size[0] . ' ' . $d_size[1] . ' ' . ($d_size[0]*$d_size[1]),
    'mem_usage' => function_exists('memory_get_peak_usage') ? round( memory_get_peak_usage()/(1024*1024), 1) : '',
    'timing' => $timing,
    ));
}