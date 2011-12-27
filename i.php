<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2012 Piwigo Team                  http://piwigo.org |
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

define('PHPWG_ROOT_PATH','./');

// fast bootstrap - no db connection
include(PHPWG_ROOT_PATH . 'include/config_default.inc.php');
@include(PHPWG_ROOT_PATH. 'local/config/config.inc.php');

defined('PWG_LOCAL_DIR') or define('PWG_LOCAL_DIR', 'local/');
defined('PWG_DERIVATIVE_DIR') or define('PWG_DERIVATIVE_DIR', PWG_LOCAL_DIR.'i/');

function trigger_action() {}
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
    if ($mkd==false)
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
  if ($code==301 || $code==302)
  {
    if (ob_get_length () !== FALSE)
    {
      ob_clean();
    }
    // default url is on html format
    $url = html_entity_decode($msg);
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
  exit;
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
    /*foreach (array_keys($_GET) as $keynum => $key)
    {
      $req = $key;
      break;
    }*/
    $page['root_path'] = PHPWG_ROOT_PATH;
  }

  $req = ltrim($req, '/');
  !preg_match('#[^a-zA-Z0-9/_.-]#', $req) or ierror('Invalid chars in request', 400);

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

  $page['coi'] = '';
  if (count($deriv) && $deriv[0][0]=='c' && $deriv[0][1]=='i')
  {
    $page['coi'] = substr(array_shift($deriv), 2);
    preg_match('#^[a-z]{4}$#', $page['coi']) or ierror('Invalid center of interest', 400);
  }

  if ($page['derivative_type'] == IMG_CUSTOM)
  {
    try
    {
      $page['derivative_params'] = ImageParams::from_url_tokens($deriv);
    }
    catch (Exception $e)
    {
      ierror($e->getMessage(), 400);
    }
  }

  if ($req[0]!='g' && $req[0]!='u')
    $req = '../'.$req;

  $page['src_location'] = $req.$ext;
  $page['src_path'] = PHPWG_ROOT_PATH.$page['src_location'];
  $page['src_url'] = $page['root_path'].$page['src_location'];
}



$page=array();

include_once( PHPWG_ROOT_PATH .'/include/derivative_params.inc.php');
include_once( PHPWG_ROOT_PATH .'/include/derivative_std_params.inc.php');

ImageStdParams::load_from_file();


parse_request();
//var_export($page);

$params = $page['derivative_params'];
if ($params->sizing->ideal_size[0] < 20 or $params->sizing->ideal_size[1] < 20)
{
  ierror('Invalid size', 400);
}
if ($params->sizing->max_crop < 0 or $params->sizing->max_crop > 1)
{
  ierror('Invalid crop', 400);
}

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

if (!$need_generate)
{
  if ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] )
    and strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $derivative_mtime)
  {// send the last mod time of the file back
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $derivative_mtime).' GMT', true, 304);
    header('Expires: '.gmdate('D, d M Y H:i:s', time()+10*24*3600).' GMT', true, 304);
    exit;
  }
  // todo send pass-through
}


include_once(PHPWG_ROOT_PATH . 'admin/include/image.class.php');
$image = new pwg_image($page['src_path']);

if (!mkgetdir(dirname($page['derivative_path'])))
{
  ierror("dir create error", 500);
}

$changes = 0;

// todo rotate

// Crop & scale
$params->sizing->compute( array($image->get_width(),$image->get_height()), $page['coi'], $crop_rect, $scale_width );
if ($crop_rect)
{
  $changes++;
  $image->crop( $crop_rect->width(), $crop_rect->height(), $crop_rect->l, $crop_rect->t);
}

if ($scale_width)
{
  $changes++;
  $image->resize( $scale_width[0], $scale_width[1] );
}

// no change required - redirect to source
if (!$changes)
{
  header("X-i: No change");
  ierror( $page['src_url'], 301);
}

$image->write( $page['derivative_path'] );
$image->destroy();

$fp = fopen($page['derivative_path'], 'rb');

$fstat = fstat($fp);
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fstat['mtime']).' GMT');
header('Expires: '.gmdate('D, d M Y H:i:s', time()+10*24*3600).' GMT');
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
?>