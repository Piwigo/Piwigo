<?php
//php 5
function file_put_contents($filename, $data)
{
  $fp = fopen($filename, 'w');
  if ($fp)
  {
    $ret = fwrite($fp, $data);
    fclose($fp);
    return $ret;
  }
  return false;
}
?>