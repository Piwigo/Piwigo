<?php
class smartpocket_maintain extends ThemeMaintain
{
  private $installed = false;
  
  private $default_conf = array(
    'loop'            => true,//true - false
    'autohide'            => 5000,//5000 - 0
  );
  
  function activate($theme_version, &$errors=array())
  {
    global $conf, $prefixeTable;

    if (empty($conf['smartpocket']))
    {
      $conf['smartpocket'] = serialize($this->default_conf);
      $query = "
  INSERT INTO " . CONFIG_TABLE . " (param,value,comment)
  VALUES ('smartpocket' , '".pwg_db_real_escape_string($conf['smartpocket'])."' , 'loop#autohide');";
      pwg_query($query);
    }
    elseif (count(unserialize( $conf['smartpocket'] ))!=2)
    {
      $conff=unserialize($conf['smartpocket']);
      $config = array(
        'loop'            => (!empty($conff['loop'])) ? $conff['loop'] :true,
        'autohide'            => (!empty($conff['autohide'])) ? $conff['autohide'] :5000,
      );
      conf_update_param('smartpocket', pwg_db_real_escape_string(serialize($config)));
      load_conf_from_db();
    }
    $this->installed = true;
  }

  function deactivate()
  { }

  function delete()
  {
    // delete configuration
    conf_delete_param('smartpocket');
  }
}
?>