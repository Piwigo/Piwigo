<?php
/* -----------------------------------------------------------------------------
  class name: common_plugin
  class version: 2.0
  date: 2008-07-13

  ------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------

  this class provides base functions to manage a plugin
  public
    ADMINISTRATION RELATED
    - manage()
    - plugin_admin_menu($menu)
    INITIALIZATION RELATED
    - init_events()
    CONFIG RELATED
    - get_filelocation()
    - get_admin_link()
    - init_config()
    - load_config()
    - save_config()
    - delete_config()

  protected
    INITIALIZATION RELATED
    - set_tables_list($list)
  ------------------------------------------------------------------------------
  :: HISTORY

  2.0.0     - 2008-07-13
              migrate to piwigo 2.0 ; use of PHP5 classes possibilities

  --------------------------------------------------------------------------- */

class common_plugin
{
  protected $prefixeTable;  // prefixe for tables names
  protected $page_link; //link to admin page
  protected $filelocation; //files plugin location on server
  protected $display_result_ok;
  protected $display_result_ko;
  protected $plugin_name;   // used for interface display
  protected $plugin_name_files;   // used for files
  protected $plugin_admin_file = "plugin_admin";
  protected $tables;   // list of all tables names used by plugin
  public $my_config;     // array of config parameters

  /* constructor allows to initialize $prefixeTable value */
  public function common_plugin($prefixeTable, $filelocation)
  {
    $this->filelocation=$filelocation;
    $this->prefixeTable=$prefixeTable;
    $this->page_link="admin.php?page=plugin&section=".                        basename(dirname($this->filelocation))."/admin/".$this->plugin_admin_file.".php";
    //$this->page_link=get_admin_plugin_menu_link($filelocation);
    $this->init_config();
    $this->display_result_ok="OK";
    $this->display_result_ko="KO";
  }

  public function get_filelocation()
  {
    return($this->filelocation);
  }

  public function get_admin_link()
  {
    return($this->page_link);
  }


  /* ---------------------------------------------------------------------------
     CONFIGURATION RELATED FUNCTIONS 
  --------------------------------------------------------------------------- */

  /* this function initialize var $my_config with default values */
  public function init_config()
  {
    $this->my_config=array();
  }

  /* load config from CONFIG_TABLE into var $my_config */
  public function load_config()
  {
    $this->init_config();
    $sql="SELECT value FROM ".CONFIG_TABLE."
          WHERE param = '".$this->plugin_name_files."_config'";
    $result=pwg_query($sql);
    if($result)
    {
      $row=mysql_fetch_row($result);
      if(is_string($row[0])) 
      {
        $config = unserialize($row[0]);
        reset($config);
        while (list($key, $val) = each($config)) 
        { $this->my_config[$key] =$val; }
      }
    }
  }

  /* save var $my_config into CONFIG_TABLE */
  public function save_config()
  {
    $sql="REPLACE INTO ".CONFIG_TABLE." 
           VALUES('".$this->plugin_name_files."_config', '"
           .serialize($this->my_config)."', '')"; 
    $result=pwg_query($sql);
    if($result) 
    { return true; } 
    else 
    { return false; }
  }

  /* delete config from CONFIG_TABLE */
  public function delete_config()
  {
    $sql="DELETE FROM ".CONFIG_TABLE." 
          WHERE param='".$this->plugin_name_files."_config'"; 
    $result=pwg_query($sql);
    if($result) 
    { return true; } 
    else 
    { return false; }
  }

  /* ---------------------------------------------------------------------------
     PLUGIN INITIALIZATION RELATED FUNCTIONS
  --------------------------------------------------------------------------- */

  /*
      initialize tables list used by the plugin
        $list = array('table1', 'table2')
        $this->tables_list['table1'] = $prefixeTable.$plugin_name.'_table1'
  */
  protected function set_tables_list($list)
  {
    for($i=0;$i<count($list);$i++)
    {
      $this->tables[$list[$i]]=$this->prefixeTable.$this->plugin_name_files.'_'.$list[$i];
    }
  }

  /* ---------------------------------------------------------------------------
     ADMINISTRATOR CONSOLE RELATED FUNCTIONS 
  --------------------------------------------------------------------------- */

  /* add plugin into administration menu */
  public function plugin_admin_menu($menu)
  {
    array_push($menu,
               array(
                  'NAME' => $this->plugin_name,
                  'URL' => get_admin_plugin_menu_link(dirname($this->filelocation).
                                '/admin/'.$this->plugin_admin_file.'.php')
                   ));
    return $menu;
  }

  /*
    manage plugin integration into piwigo's admin interface

    to be surcharged by child's classes
  */
  public function manage()
  {
  }

  /*
    intialize plugin's events
    to be surcharged by child's classes
  */
  public function init_events()
  {
  }

  protected function debug($text)
  {
    global $page;
    array_push($page['infos'], "DEBUG MODE: ".$text);
  }

  /*
    manage infos & errors display
  */
  protected function display_result($action_msg, $result)
  {
    global $page;

    if($result)
    {
      array_push($page['infos'], $action_msg);
      array_push($page['infos'], $this->display_result_ok);
    }
    else
    {
      array_push($page['errors'], $action_msg);
      array_push($page['errors'], $this->display_result_ko);
    }
  }
} //class common_plugin

?>
