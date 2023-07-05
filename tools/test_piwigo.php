<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

define('PHPWG_ROOT_PATH', '../');

//Add a Clone piwigo from github//


// Usage : php test_piwigo.php --url "localhost/piwigo" --db_user "root" --db_password "password" --db_name "database" --file "picture.png"

$option = getopt("", array('url:', 'db_user:', 'db_password:', 'db_name:', 'file:', 'drop_db:'));

//Check mandatory option and test to perfom. 
//For example : php test_piwigo.php --user "user" --password "123" --db_name "db123" --install "true"

$mandatory_fields = array(
  'url',
  'db_user',
  'db_password',
);

foreach ($mandatory_fields as $field) {
  if (!isset($option[$field])) {
    die ('Missing --'.$field);
  }
}

$option['url'] = 'http://'.$option['url'];

//Creat cookie file

$cookies = dirname(__DIR__).'/cookies.txt';

// Connection to mySQL

$mysqli = new mysqli('localhost', $option['db_user'], $option['db_password']);
if (!$mysqli) {
  die ('Cannot connect to mySQL');
}

// Check if database name is set otherwise we use a random name.
// Then we create the database.

$option['db_name'] = create_database($option);

install_piwigo($option);
$pwg_token = log_user($option, $cookies);
create_album($option, $cookies);
add_picture($option, $cookies, $pwg_token);

//+-----------------------+
//| Create a new database |
//+-----------------------+

function create_database($option)
{
  global $mysqli;

  if (!isset($option['db_name'])) {
    $option['db_name'] = uniqid();
  }

  $query = '
    SHOW DATABASES
  ;';

  $res = $mysqli->query($query);

  while (($row = $res->fetch_row())) {
    if ($row[0] == $option['db_name']) {
      die ('Database name already exist');
    }
  }

  $query = '
    CREATE DATABASE '.$option['db_name'].'
  ;';

  $mysqli->query($query);

  return $option['db_name'];
}


//+--------------------------+
//| Script Installing Piwigo |
//+--------------------------+

function install_piwigo($option)
{
  $data = array(
    'install'     =>  1,
    'dbhost'      =>  'localhost',
    'dbuser'      =>  $option['db_user'],
    'dbpasswd'    =>  $option['db_password'],
    'dbname'      =>  $option['db_name'],
    'prefix'      =>  'piwigo_',
    'admin_name'  =>  'admin',
    'admin_pass1' =>  'pwg123',
    'admin_pass2' =>  'pwg123',
    'admin_mail'  =>  'test@gmail.com',

  );

  $ch = curl_init();

  $curLopt = array(
    CURLOPT_URL             =>  $option['url'].'/install.php',
    CURLOPT_COOKIESESSION   =>  true,
    CURLOPT_RETURNTRANSFER  =>  true,
    CURLOPT_POST            =>  1,
    CURLOPT_POSTFIELDS      =>  $data,
  );

  curl_setopt_array($ch, $curLopt);

  $content = curl_exec($ch);
  $err = curl_errno($ch);
  $errmsg = curl_error($ch);
  $response = curl_getinfo($ch);
  curl_close($ch);

  sleep(2);

  require_once(PHPWG_ROOT_PATH.'/local/config/database.inc.php');

  if (PHPWG_INSTALLED === true) {
    echo "Installation OK!\n";
  }
  else {
    echo "Installation KO!\n";
  }
}

//+----------------------+
//| Script Login an User |
//+----------------------+

function log_user($option, $cookies)
{
  //Log an user - Admin here
  $data = array(
    'method'    =>  'pwg.session.login',
    'password'  =>  'pwg123',
    'username'  =>  'admin',
  );

  $ch = curl_init();

  $curLopt = array(
    CURLOPT_URL             => $option['url'].'/ws.php?format=json',
    CURLOPT_COOKIEJAR       => $cookies,
    CURLOPT_COOKIEFILE      => $cookies,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_POST            => 1,
    CURLOPT_POSTFIELDS      => $data,
  );

  curl_setopt_array($ch, $curLopt);

  $content = curl_exec($ch);
  $err = curl_errno($ch);
  $errmsg = curl_error($ch);
  $response = curl_getinfo($ch);
  curl_close($ch);

  //Gets information about the current session
  $data = array(
    'method'    =>  'pwg.session.getStatus',
  );

  $ch = curl_init();

  $curLopt = array(
    CURLOPT_URL             => $option['url'].'/ws.php?format=json',
    CURLOPT_COOKIEJAR       => $cookies,
    CURLOPT_COOKIEFILE      => $cookies,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_POST            => 1,
    CURLOPT_POSTFIELDS      => $data,
  );

  curl_setopt_array($ch, $curLopt);

  $content = curl_exec($ch);
  $err = curl_errno($ch);
  $errmsg = curl_error($ch);
  $response = curl_getinfo($ch);
  curl_close($ch);

  $result = json_decode($content, true);
  if ($result['stat'] == 'ok') {
    echo "Login OK!\n";
  }
  else {
    echo "Login KO!\n";
  }
  return $result['result']['pwg_token'];
}

//+-----------------------+
//| Script Creating Album |
//+-----------------------+

function create_album($option, $cookies)
{
  $data = array(
    'method'    =>  'pwg.categories.add',
    'name'      =>  'AlbumExample',
  );

  $ch = curl_init();

  $curLopt = array(
    CURLOPT_URL             => $option['url'].'/ws.php?format=json',
    CURLOPT_COOKIEJAR       => $cookies,
    CURLOPT_COOKIEFILE      => $cookies,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_POST            => 1,
    CURLOPT_POSTFIELDS      => $data,
  );

  curl_setopt_array($ch, $curLopt);

  $content = curl_exec($ch);
  $err = curl_errno($ch);
  $errmsg = curl_error($ch);
  $response = curl_getinfo($ch);
  curl_close($ch);

  $result = json_decode($content, true);
  if ($result['stat'] == 'ok') {
    echo "Album creation OK!\n";
  }
  else {
    echo "Album creation KO!\n";
  }
}

//+-------------------------+
//|  Script adding picture  |
//+-------------------------+

function add_picture($option, $cookies, $pwg_token)
{
  global $mysqli;

  exec("perl piwigo_upload.pl --url=".$option['url']." --user=admin --password=pwg123 --file=temp.png --album_id=1");

  $mysqli->select_db($option['db_name']);

  $query = '
    SELECT count(*)
    FROM piwigo_images
  ;';

  $res = $mysqli->query($query);
  if (($row = $res->fetch_row())[0] > 0) {
    echo "Add a Picture OK!\n";
  }
  else {
    echo "Add a picture KO!\n";
  }
  /*$content = readfile($option['file']);
  $content_lenght = strlen($content);
  $nb_chunks = ceil($content_lenght / 500);

  $chunk_pos = 0;
  $chunk_id = 0;
  while ($chunk_pos < $content_lenght)
  {
    $chunk = substr($content, $chunk_pos, 500);
  }

  $chunk_path = '/tmp/'.md5($option['file']).'.chunk';

  $chunk_pos += 500;*/

  /*if (function_exists('curl_file_create'))
  {
    $cFile = curl_file_create($option['file']);
  }
  else
  {
    $cFile = '@'.realpath($option['file']);
  }

  $data = array(
    'method'    =>  'pwg.images.upload',
    //'name'      =>  $option['file'],
    //'chunk'     =>  $chunk_id,
    //'chunks'    =>  $nb_chunks,
    'category'    =>  1,
    'file_contents' =>  $cFile,
    'pwg_token'   =>  $pwg_token,
  );

  $ch = curl_init();

  $curLopt = array(
    CURLOPT_URL       => $option['url'].'/ws.php?format=json',
    //CURLOPT_HTTPHEADER    => $headers,
    CURLOPT_COOKIEJAR   => $cookies,
    CURLOPT_COOKIEFILE    => $cookies,
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_POST      => 1,
    CURLOPT_POSTFIELDS    => $data,
  );

  curl_setopt_array($ch, $curLopt);

  $content = curl_exec($ch);
  $err = curl_errno($ch);
  $errmsg = curl_error($ch);
  $response = curl_getinfo($ch);
  curl_close($ch);

  $result = json_decode($content);
  print_r($result);
  print_r($response);*/
}

if (isset($option['drop_db']) && $option['drop_db'] == true) {
  $query = '
    DROP DATABASE '.$option['db_name'].'
  ;';

  $mysqli->query($query);
}

$mysqli->close();

?>