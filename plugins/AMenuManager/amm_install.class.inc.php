<?php
/* -----------------------------------------------------------------------------
  Plugin     : Advanced Menu Manager
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------
  See main.inc.php for release information

  MyPolls_Install : classe to manage plugin install

  --------------------------------------------------------------------------- */
  @include_once('amm_root.class.inc.php');
  include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/tables.class.inc.php');


  class AMM_install extends AMM_root
  {
    private $tablef;
    private $exportfile;

    public function AMM_install($prefixeTable, $filelocation)
    {
      parent::__construct($prefixeTable, $filelocation);
      $this->tablef= new manage_tables($this->tables);
      $this->exportfile=dirname($this->filelocation).'/'.$this->plugin_name_files.'.sql';
    }

    /* 
        function for installation process 
        return true if install process is ok, otherwise false 
    */ 
    public function install()
    {

      $tables_def=array(
"CREATE TABLE  `".$this->tables['urls']."` (
  `id` int(11) NOT NULL auto_increment,
  `label` varchar(50) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `mode` int(11) NOT NULL default '0',
  `icon` varchar(50) NOT NULL default '',
  `position` int(11) NOT NULL default '0',
  `visible` char(1) NOT NULL default 'y',
  PRIMARY KEY  (`id`),
  KEY `order_key` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1"
      );
      //$table_def array

      $result=$this->tablef->create_tables($tables_def);
      return($result);
    }


    /*
        function for uninstall process
    */
    public function uninstall()
    {
      $this->tablef->export($this->exportfile);
      $this->delete_config();
      $this->tablef->drop_tables();
    }

    public function activate()
    {
      global $template;

      $this->init_config();
      $this->load_config();
      $this->save_config();
    }

    public function deactivate()
    {
    }

  } //class

?>
