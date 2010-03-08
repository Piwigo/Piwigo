<?php
define('PHPWG_ROOT_PATH','../../../');
define('IN_ADMIN', true);

$_COOKIE['pwg_id'] = $_POST['session_id'];

include_once(PHPWG_ROOT_PATH.'include/common.inc.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

// check_pwg_token();

ob_start();
print_r($_FILES);
print_r($_POST);
print_r($user);
$tmp = ob_get_contents(); 
ob_end_clean();
error_log($tmp, 3, "/tmp/php-".date('YmdHis').'-'.sprintf('%020u', rand()).".log");

$image_id = add_uploaded_file(
  $_FILES['Filedata']['tmp_name'],
  $_FILES['Filedata']['name'],
  null,
  8
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

echo "1";
?>