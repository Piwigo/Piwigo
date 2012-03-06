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

define('PHPWG_ROOT_PATH','./');
session_cache_limiter('public');
include_once(PHPWG_ROOT_PATH.'include/common.inc.php');

// Check Access and exit when user status is not ok
check_status(ACCESS_GUEST);

function guess_mime_type($ext)
{
  switch ( strtolower($ext) )
  {
    case "jpe": case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    case "png": $ctype="image/png"; break;
    case "gif": $ctype="image/gif"; break;
    case "tiff":
    case "tif": $ctype="image/tiff"; break;
    case "txt": $ctype="text/plain"; break;
    case "html":
    case "htm": $ctype="text/html"; break;
    case "xml": $ctype="text/xml"; break;
    case "pdf": $ctype="application/pdf"; break;
    case "zip": $ctype="application/zip"; break;
    case "ogg": $ctype="application/ogg"; break;
    default: $ctype="application/octet-stream";
  }
  return $ctype;
}

function do_error( $code, $str )
{
  set_status_header( $code );
  echo $str ;
  exit();
}


if (!isset($_GET['id'])
    or !is_numeric($_GET['id'])
    or !isset($_GET['part'])
    or !in_array($_GET['part'], array('e','r') ) )
{
  do_error(400, 'Invalid request - id/part');
}

$query = '
SELECT * FROM '. IMAGES_TABLE.'
  WHERE id='.$_GET['id'].'
;';

$element_info = pwg_db_fetch_assoc(pwg_query($query));
if ( empty($element_info) )
{
  do_error(404, 'Requested id not found');
}

// $filter['visible_categories'] and $filter['visible_images']
// are not used because it's not necessary (filter <> restriction)
$query='
SELECT id
  FROM '.CATEGORIES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON category_id = id
  WHERE image_id = '.$_GET['id'].'
'.get_sql_condition_FandF(
  array(
      'forbidden_categories' => 'category_id',
      'forbidden_images' => 'image_id',
    ),
  '    AND'
  ).'
  LIMIT 1
;';
if ( pwg_db_num_rows(pwg_query($query))<1 )
{
  do_error(401, 'Access denied');
}

include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
$file='';
switch ($_GET['part'])
{
  case 'e':
    if ( $user['enabled_high']!='true' )
    {
      do_error(401, 'Access denied e');
    }
    $file = get_element_path($element_info);
    break;
  case 'r':
    $file = original_to_representative( get_element_path($element_info), $element_info['representative_ext'] );
    break;
}

if ( empty($file) )
{
  do_error(404, 'Requested file not found');
}

if ($_GET['part'] == 'e') {
  pwg_log($_GET['id'], 'high');
}
else if ($_GET['part'] == 'e')
{
  pwg_log($_GET['id'], 'other');
}

$http_headers = array();

$ctype = null;
if (!url_is_remote($file))
{
  if ( !@is_readable($file) )
  {
    do_error(404, "Requested file not found - $file");
  }
  $http_headers[] = 'Content-Length: '.@filesize($file);
  if ( function_exists('mime_content_type') )
  {
    $ctype = mime_content_type($file);
  }

  $gmt_mtime = gmdate('D, d M Y H:i:s', filemtime($file)).' GMT';
  $http_headers[] = 'Last-Modified: '.$gmt_mtime;

  // following lines would indicate how the client should handle the cache
  /* $max_age=300;
  $http_headers[] = 'Expires: '.gmdate('D, d M Y H:i:s', time()+$max_age).' GMT';
  // HTTP/1.1 only
  $http_headers[] = 'Cache-Control: private, must-revalidate, max-age='.$max_age;*/

  if ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) )
  {
    set_status_header(304);
    foreach ($http_headers as $header)
    {
      header( $header );
    }
    exit();
  }
}

if (!isset($ctype))
{ // give it a guess
  $ctype = guess_mime_type( get_extension($file) );
}

$http_headers[] = 'Content-Type: '.$ctype;

if (isset($_GET['download']))
{
  $http_headers[] = 'Content-Disposition: attachment; filename="'.$element_info['file'].'";';
  $http_headers[] = 'Content-Transfer-Encoding: binary';
}
else
{
  $http_headers[] = 'Content-Disposition: inline; filename="'
            .basename($file).'";';
}

foreach ($http_headers as $header)
{
  header( $header );
}

// Looking at the safe_mode configuration for execution time
if (ini_get('safe_mode') == 0)
{
  @set_time_limit(0);
}

@readfile($file);

?>