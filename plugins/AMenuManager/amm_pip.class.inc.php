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
    //TODELETE: add_event_handler('loc_begin_menubar', array(&$this, 'modify_menu') );
    parent::init_events();
    add_event_handler('blockmanager_apply', array(&$this, 'blockmanager_apply') );
  }

  public function blockmanager_apply( $menu_ref_arr )
  {
    $menu = & $menu_ref_arr[0];

    /*
      Add a new random picture section
    */
    if ( ($block = $menu->get_block( 'mbAMM_randompict' ) ) != null )
    {
      $sql="SELECT i.id as image_id, i.file as image_file, i.comment, i.path, i.tn_ext, c.id as catid, c.name, c.permalink, RAND() as rndvalue, i.name as imgname
FROM ".CATEGORIES_TABLE." c, ".IMAGES_TABLE." i, ".IMAGE_CATEGORY_TABLE." ic
WHERE c.status='public'
  AND c.id = ic.category_id
  AND ic.image_id = i.id
ORDER BY rndvalue
LIMIT 0,1
";
      $result = pwg_query($sql);
      if($result and $nfo = mysql_fetch_array($result))
      {
        $nfo['section']='category';
        $nfo['category']=array(
          'id' => $nfo['catid'],
          'name' => $nfo['name'],
          'permalink' => $nfo['permalink']
        );
        global $user;
        $block->set_title(  base64_decode($this->my_config['amm_randompicture_title'][$user['language']]) );
        $block->template = dirname(__FILE__).'/menu_templates/menubar_randompic.tpl';
        $block->data = array(
          'LINK' => make_picture_url($nfo),
          'IMG' => get_thumbnail_url($nfo),
          'IMGNAME' => $nfo['imgname'],
          'IMGCOMMENT' => $nfo['comment'],
          'SHOWNAME' => $this->my_config['amm_randompicture_showname'],
          'SHOWCOMMENT' => $this->my_config['amm_randompicture_showcomment']
        );
      }
    }

    /*
      Add a new section (links)
    */
    if ( ($block = $menu->get_block( 'mbAMM_links' ) ) != null )
    {
      $urls=$this->get_urls(true);
      if ( count($urls)>0 )
      {
        if($this->my_config['amm_links_show_icons']=='y')
        {
          for($i=0;$i<count($urls);$i++)
          {
            $urls[$i]['icon']=get_root_url().'plugins/'.AMM_DIR."/links_pictures/".$urls[$i]['icon'];
          }
        }
        
        $block->set_title( base64_decode($this->my_config['amm_links_title'][$user['language']]) );
        $block->template = dirname(__FILE__).'/menu_templates/menubar_links.tpl';

        $block->data = array(
          'LINKS' => $urls,
          'icons' => $this->my_config['amm_links_show_icons']
        );
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
          if ( ($block = $menu->get_block( 'mbAMM_personalised'.$val['id'] ) ) != null )
          {
            $block->set_title( $val['title'] );
            $block->template = dirname(__FILE__).'/menu_templates/menubar_personalised.tpl';
            $block->data = stripslashes($val['content']);
          }
          $id_done[$val['id']]="";
        }
      }
    }

    /*
      hide items from special & menu sections
    */
    foreach(array('mbMenu' => 'amm_sections_modmenu', 'mbSpecials' =>'amm_sections_modspecials') as $key0 => $val0)
    {
      if ( ($block = $menu->get_block( $key0 ) ) != null )
      {
        foreach($this->my_config[$val0] as $key => $val)
        {
          if($val=='n')
          {
            unset( $block->data[$key] );
          }
        }
      }
    }
	}

} // AMM_PIP class


?>
