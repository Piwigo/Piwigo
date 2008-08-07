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
        'icons' => $this->my_config['amm_links_show_icons']
      ));
      $menu->add($section->get());
    }


    /*
      Add a new random picture section
    */
    if($this->my_config['amm_randompicture_active']=='y')
    {
      $sql="SELECT i.id as image_id, i.file as image_file, i.comment, i.path, i.tn_ext, c.id as catid, c.name, c.permalink, RAND() as rndvalue, i.name as imgname
FROM ".CATEGORIES_TABLE." c, ".IMAGES_TABLE." i, ".IMAGE_CATEGORY_TABLE." ic
WHERE c.status='public'
  AND c.id = ic.category_id
  AND ic.image_id = i.id
ORDER BY rndvalue
LIMIT 0,1
";
      $result=pwg_query($sql);
      if($result)
      {
        $nfo = mysql_fetch_array($result);
        $nfo['section']='category';
        $nfo['category']=array(
          'id' => $nfo['catid'],
          'name' => $nfo['name'],
          'permalink' => $nfo['permalink']
        );

        $section = new Section('mbAMM_randompict', base64_decode($this->my_config['amm_randompicture_title'][$user['language']]), dirname(__FILE__).'/menu_templates/menubar_randompic.tpl');
        $section->set_items(array(
          'LINK' => make_picture_url($nfo),
          'IMG' => get_thumbnail_url($nfo),
          'IMGNAME' => $nfo['imgname'],
          'IMGCOMMENT' => $nfo['comment'],
          'SHOWNAME' => $this->my_config['amm_randompicture_showname'],
          'SHOWCOMMENT' => $this->my_config['amm_randompicture_showcomment']
        ));
        $menu->add($section->get());
      }
    }

    /*
      Add personnal blocks random picture section
    */
    $sections=$this->get_sections(true);

    if(count($sections))
    {
      $id_done=array();
      foreach($sections as $key => $val)
      {
        if(!isset($id_done[$val['id']]))
        {
          $section = new Section('mbAMM_personalised'.$val['id'], $val['title'], dirname(__FILE__).'/menu_templates/menubar_personalised.tpl');
          $section->set_items(array(
            'CONTENT' => stripslashes($val['content'])));
          $menu->add($section->get());

          $id_done[$val['id']]="";
        }
      }
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

    /*
      hide items from special & menu sections
    */
    foreach(array('mbMenu' => 'amm_sections_modmenu', 'mbSpecial' =>'amm_sections_modspecial') as $key0 => $val0)
    {
      $section_menu=$menu->section($key0);
      foreach($this->my_config[$val0] as $key => $val)
      {
        if($val=='n')
        {
          unset($section_menu['ITEMS'][$key]);
        }
      }
      $menu->replace($section_menu);
    }

  }


} // AMM_PIP class


?>
