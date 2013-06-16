<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

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
VALUES ('elegant' , '".pwg_db_real_escape_string(serialize($config))."' , 'p_main_menu#');";
  pwg_query($query);
  load_conf_from_db();
}
elseif (count(unserialize( $conf['elegant'] ))!=3)
{
  $conff=unserialize($conf['elegant']);
  $config = array(
    'p_main_menu'            => (isset($conff['p_main_menu'])) ? $conff['p_main_menu'] :'on',
    'p_pict_descr'            => (isset($conff['p_pict_descr'])) ? $conff['p_pict_descr'] :'on',
    'p_pict_comment'            => (isset($conff['p_pict_comment'])) ? $conff['p_pict_comment'] :'off',
  );
  conf_update_param('elegant', pwg_db_real_escape_string(serialize($config)));
  load_conf_from_db();
}
?>