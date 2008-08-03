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

  PIP classe => manage integration in public interface

  --------------------------------------------------------------------------- */
if (!defined('PHPWG_ROOT_PATH')) { die('Hacking attempt!'); }

include_once(PHPWG_PLUGINS_PATH.'AMenuManager/amm_root.class.inc.php');

class AMM_PIP extends AMM_root
{ 
  function AMM_PIP($prefixeTable, $filelocation)
  {
    parent::__construct($prefixeTable, $filelocation);

    $this->load_config();
    $this->init_events();
  }


  /* ---------------------------------------------------------------------------
  Public classe functions
  --------------------------------------------------------------------------- */


  /*
    initialize events call for the plugin
  */
  public function init_events()
  {
    add_event_handler('loc_begin_menubar', array(&$this, 'modify_menu') );
  }

  /* ---------------------------------------------------------------------------
  protected classe functions
  --------------------------------------------------------------------------- */
  public function modify_menu()
  {
    global $menu, $user;



    /*
      Add a new section (links)
    */
    $urls=$this->get_urls(true);
    if(($this->my_config['amm_links_active']=='y')and(count($urls)>0))
    {
      if($this->my_config['amm_links_show_icons']=='y')
      {
        for($i=0;$i<count($urls);$i++)
        {
          $urls[$i]['icon']=AMM_PATH."links_pictures/".$urls[$i]['icon'];
        }
      }

      $section = new Section('mbAMM_links', base64_decode($this->my_config['amm_links_title'][$user['language']]), dirname(__FILE__).'/menu_templates/menubar_links.tpl');
      $section->set_items(array(
        'LINKS' => $urls,
        'icons' => 'y'
      ));
      $menu->add($section->get());
    }

    /*
      Hide sections
    */
    foreach($this->my_config['amm_sections_visible'] as $key => $val)
    {
      if($val=='n')
      {
        $menu->remove($key);
      }
    }

  }


} // AMM_PIP class


?>
