<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

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
  load_conf_from_db();
}
elseif (count(unserialize( $conf['smartpocket'] ))!=2)
{
  $conff=unserialize($conf['smartpocket']);
  $config = array(
    'loop'            => (isset($conff['loop'])) ? $conff['loop'] :true,
    'autohide'            => (isset($conff['autohide'])) ? $conff['autohide'] :5000,
  );
  conf_update_param('smartpocket', pwg_db_real_escape_string(serialize($config)));
  load_conf_from_db();
}
?>