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

  AMM_root : root classe for plugin 

  --------------------------------------------------------------------------- */

if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'grum_plugins_classes-2/common_plugin.class.inc.php');

class AMM_root extends common_plugin
{ 
  function AMM_root($prefixeTable, $filelocation)
  {
    $this->plugin_name="Advanced Menu Manager";
    $this->plugin_name_files="amm";
    parent::__construct($prefixeTable, $filelocation);

    $list=array('urls');
    $this->set_tables_list($list);
  }

  /* ---------------------------------------------------------------------------
  common AIP & PIP functions
  --------------------------------------------------------------------------- */

  /* this function initialize var $my_config with default values */
  public function init_config()
  {
    global $menu;

    $this->my_config=array(
      'amm_links_show_icons' => 'y',
      'amm_links_active' => 'y',
      'amm_links_title' => array(),
      'amm_sections_visible' => array()
    );

    $languages=get_languages();
    foreach($languages as $key => $val)
    {
      if($key=='fr_FR')
      {
        $this->my_config['amm_links_title'][$key]='Liens';
      }
      else
      {
        $this->my_config['amm_links_title'][$key]='Links';
      }
    }

    $sections=$menu->registered();
    foreach($sections as $key => $val)
    {
      $this->my_config['amm_sections_visible'][$key]='y';
    }
  }


  // return an array of urls (each url is an array)
  protected function get_urls($only_visible=false)
  {
    $returned=array();
    $sql="SELECT * FROM ".$this->tables['urls'];
    if($only_visible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $sql.=" ORDER BY position";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_array($result))
      {
        $returned[]=$row;
      }
    }
    return($returned);
  }

  //return number of url
  protected function get_count_url($only_visible=false)
  {
    $returned=0;
    $sql="SELECT count(id) FROM ".$this->tables['urls'];
    if($only_visible)
    {
      $sql.=" WHERE visible = 'y' ";
    }
    $result=pwg_query($sql);
    if($result)
    {
      $tmp=mysql_fetch_row($result);
      $returned=$tmp[0];
    }
    return($returned);
  }


} // amm_root  class


?>
