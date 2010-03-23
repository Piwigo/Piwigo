#!/usr/bin/php -qn
<?php
if (isset($_SERVER['argc']) && $_SERVER['argc']<=1) 
{
  echo "\n";
  echo 'usage : ', basename($_SERVER['argv'][0]), " <filename>\n";
  echo "\n";
  exit(1);
}

$filename = trim($_SERVER['argv'][1]);
$lines = file($filename);
$content = file_get_contents($filename);

$n = 0;
$nbLines = count($lines);

$pattern = "`(.*{')(.*)('\|@translate}.*)`Um";
$replace = "'{\''.keyReplace('\\1').'\'|@translate}'";

include "language/templates/common.lang.php";
include "language/templates/admin.lang.php";
include "language/templates/upgrade.lang.php";
include "language/templates/install.lang.php";

while ($n < $nbLines) {
  preg_match_all($pattern, $lines[$n], $matches);
  echo str_replace($matches[2], keyReplace($matches[2]), $lines[$n]);
  $n++;
}

function keyReplace($key) {
  global $lang;

  if (is_array($key)) {
    $translation = array();
    foreach ($key as $i => $k) {
      if (isset($lang[$k])) {
	$translation = addslashes($lang[$k]);
      } else {
	$translation = "$k";
      }      
    }
  } else {
    if (isset($lang[$key])) {
      $translation = addslashes($lang[$key]);
    } else {
      $translation = "$key";
    }
  }
  return $translation;
}
?>