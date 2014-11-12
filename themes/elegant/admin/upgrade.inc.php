<?php

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable, $conf;

if (!isset($conf['elegant']))
{
  $config = array(
    'p_main_menu' => 'on', //on - off - disabled
    'p_pict_descr' => 'on', //on - off - disabled
    'p_pict_comment' => 'off', //on - off - disabled
  );
  
  conf_update_param('elegant', $config, true);
}
elseif (count(safe_unserialize( $conf['elegant'] ))!=3)
{
  $conff = safe_unserialize($conf['elegant']);
  $config = array(
    'p_main_menu' => (isset($conff['p_main_menu'])) ? $conff['p_main_menu'] :'on',
    'p_pict_descr' => (isset($conff['p_pict_descr'])) ? $conff['p_pict_descr'] :'on',
    'p_pict_comment' => (isset($conff['p_pict_comment'])) ? $conff['p_pict_comment'] :'off',
  );
  
  conf_update_param('elegant', $config, true);
}
?>