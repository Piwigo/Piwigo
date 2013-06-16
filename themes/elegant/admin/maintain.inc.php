<?php

function theme_activate($id, $version, &$errors)
{
  global $prefixeTable, $conf;

  if (!isset($conf['elegant']))
  {
    $config = array(
      'p_main_menu'            => 'on',//on - off - disabled
      'p_pict_descr'            => 'on',//on - off - disabled
      'p_pict_comment'            => 'off',//on - off - disabled
    );
      
    $query = "
INSERT INTO " . CONFIG_TABLE . " (param,value,comment)
VALUES ('elegant' , '".pwg_db_real_escape_string(serialize($config))."' , 'p_main_menu#p_pict_descr#p_pict_comment');";
    pwg_query($query);
  }
}

function theme_delete()
{
  global $prefixeTable;

  $query = 'DELETE FROM ' . CONFIG_TABLE . ' WHERE param="elegant" ;';
  pwg_query($query);
}

?>