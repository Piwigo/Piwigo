<?php

function theme_activate($id, $version, &$errors)
{
  global $prefixeTable, $conf;

  if (!isset($conf['smartpocket']))
  {
    $config = array(
      'loop'            => true,//true - false
      'autohide'            => 5000,//5000 - 0
    );
      
    $query = "
INSERT INTO " . CONFIG_TABLE . " (param,value,comment)
VALUES ('smartpocket' , '".pwg_db_real_escape_string(serialize($config))."' , 'loop#autohide');";
    pwg_query($query);
  }
}

function theme_delete()
{
  global $prefixeTable;

  $query = 'DELETE FROM ' . CONFIG_TABLE . ' WHERE param="smartpocket" ;';
  pwg_query($query);
}

?>