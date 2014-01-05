<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2014 Piwigo Team                  http://piwigo.org |
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

define('PHPWG_ROOT_PATH','../../../');
define('IN_ADMIN', true);

$_COOKIE['pwg_id'] = $_POST['session_id'];

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

check_pwg_token();

ob_start();
echo '$_FILES'."\n";
print_r($_FILES);
echo '$_POST'."\n";
print_r($_POST);
echo '$user'."\n";
print_r($user);
$tmp = ob_get_contents(); 
ob_end_clean();
// error_log($tmp, 3, "/tmp/php-".date('YmdHis').'-'.sprintf('%020u', rand()).".log");

if ($_FILES['Filedata']['error'] !== UPLOAD_ERR_OK)
{
  $error_message = file_upload_error_message($_FILES['Filedata']['error']);
  
  add_upload_error(
    $_POST['upload_id'],
    sprintf(
      l10n('Error on file "%s" : %s'),
      $_FILES['Filedata']['name'],
      $error_message
      )
    );

  echo "File Size Error";
  exit();
}

ob_start();

$image_id = add_uploaded_file(
  $_FILES['Filedata']['tmp_name'],
  $_FILES['Filedata']['name'],
  array($_POST['category_id']),
  $_POST['level']
  );

$_SESSION['uploads'][ $_POST['upload_id'] ][] = $image_id;

$query = '
SELECT
    id,
    path
  FROM '.IMAGES_TABLE.'
  WHERE id = '.$image_id.'
;';
$image_infos = pwg_db_fetch_assoc(pwg_query($query));

$thumbnail_url = preg_replace('#^'.PHPWG_ROOT_PATH.'#', './', DerivativeImage::thumb_url($image_infos));

$return = array(
  'image_id' => $image_id,
  'category_id' => $_POST['category_id'],
  'thumbnail_url' => $thumbnail_url,
  );

$output = ob_get_contents(); 
ob_end_clean();
if (!empty($output))
{
  add_upload_error($_POST['upload_id'], $output);
  $return['error_message'] = $output;
}

echo json_encode($return);
?>