<?php
include('products.inc.php');

// Set default requierement //
$php_version = "/5.2.0";
$sql_version = "/5.0.0";

$all_versions = array();

if (isset($_GET['prerequisite']))
{
  $prerequisite = $_GET['prerequisite'];
}
else
{
  $prerequisite = 0;
}

foreach(array_keys($conf['product']) as $version) {
  if (preg_match('/^\d+\.\d+\.\d+$/', $version)) {
    array_push($all_versions, $version);
  }
}

natcasesort($all_versions);
$all_versions = array_reverse($all_versions);

// Old version before 2.9.2

if ($prerequisite == 0)
{
  $format = 'raw';
  if (isset($_GET['format']) and 'php' == $_GET['format']) {
    $format = $_GET['format'];
  }

  if ('raw' == $format) {
    echo implode("\n", $all_versions);
  }
  else if ('php' == $format) {
    echo serialize($all_versions);
  }
}

// New version until 2.9.2

if ($prerequisite == 1)
{
  $tmp_version = array();
  $all_versions2 = array();
  $all_versions = array_flip($all_versions);
  foreach($conf['product'] as $id_version => $version)
  {
    if (is_array($version))
    {
      foreach ($version as $key => $type)
      {
        if (preg_match('/\d+\.\d+\.\d+/', $type, $tmp))
        {
          $tmp_version[$key] = $tmp[0];
        }
      }
      $all_versions2[$id_version] = implode('/', $tmp_version);
    }
    else
    {
      if (preg_match('/\d+\.\d+\.\d+/', $version, $actual_version))
      {
        $version = $actual_version[0].$php_version.$sql_version;
        $all_versions2[$id_version] = $version;
      }
    }
  }
  $all_versions2 = array_values(array_intersect_key($all_versions2, $all_versions));

  $format = 'raw';
  if (isset($_GET['format']) and 'php' == $_GET['format']) {
    $format = $_GET['format'];
  }

  if ('raw' == $format) {
    echo implode("\n", $all_versions2);
  }
  else if ('php' == $format) {
    echo serialize($all_versions);
  }
}
?>
