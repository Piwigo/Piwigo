<?php
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

if (!isset($_SESSION['uploads']))
{
  $_SESSION['uploads'] = array();
}

if (!isset($_SESSION['uploads'][ $_POST['upload_id'] ]))
{
  $_SESSION['uploads'][ $_POST['upload_id'] ] = array();
}

array_push(
  $_SESSION['uploads'][ $_POST['upload_id'] ],
  $image_id
  );

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