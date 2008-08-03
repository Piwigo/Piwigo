<?php 

/* -----------------------------------------------------------------------------
  class name: public_integration
  class version: 1.0
  date: 2007-10-31
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------
  
  this class provides base functions to manage an integration into main index
  page
  the class use plugin MenuBarManager function if installed

  - constructor public_integration($section)
  - (public) function init_events()
  - (public) function set_callback_page_function($value)
  - (public) function set_callback_init_menu_function($value)
  - (public) function set_menu_tpl($tpl_code)
  - (public) function set_menu_list($list)
  - (public) function set_menu_title($title)
  - (public) function set_lnk_admin_add($text, $link)
  - (public) function set_lnk_admin_edit($text, $link)
  - (private) function update_menubar()
  - (private) function init_section()
  - (private) function call_page()

  use init_events() function to initialize needed triggers for updating menubar
  use set_menu_tpl() function to initialize the template of menu
  use set_menu_title() function to initialize the title of menu
  use set_menu_list() function to initialize the elements of menu (see function for use)
  use set_lnk_admin_add() and set_lnk_admin_edit() functions for displaying specific admin links

  the "callback_page_function" is called everytime a specific page is displayed
  the "callback_init_menu_function" is called everytime the menu is made (allows
              for example to prepare menu's title and list using user's language)

----------------------------------------------------------------------------- */

class public_integration
{
  var $menu_tpl;    //template definition for the menu
  var $menu_list;   //an array of arrays array(array('text' => '', 'id' => '', 'link' => ''), array... )
  var $menu_title;  //menu's title 
  var $lnk_admin_add;   //if set array('text'=>'', 'link'=>''), add a link "add" to the menu
  var $lnk_admin_edit;  //if set array('text'=>'', 'link'=>''), add a link "edit" to the menu's elements
  var $section;         //section applied to the page viewed
  var $callback_page_function;        //called function to display page
  var $callback_init_menu_function;   //called function to initialize menu

  function public_integration($section)
  {
    $this->menu_tpl="";
    $this->menu_list=array();
    $this->menu_title="";
    $this->lnk_admin_add=array();
    $this->lnk_admin_edit=array();
    $this->section=$section;
    $this->callback_page_function='';
    $this->callback_init_menu_function='';
  }

  //initialize events to manage menu & page integration
  function init_events()
  {
    add_event_handler('loc_begin_menubar', array(&$this, 'init_smarty'));
    add_event_handler('loc_end_menubar', array(&$this, 'update_menubar'));
    add_event_handler('loc_end_section_init', array(&$this, 'init_section'));
    add_event_handler('loc_end_index', array(&$this, 'call_page'));
  }
  
  function set_callback_page_function($value)
  {
    $this->callback_page_function=$value;
  }

  function set_callback_init_menu_function($value)
  {
    $this->callback_init_menu_function=$value;
  }

  // set template definition for menu
  function set_menu_tpl($tpl_code)
  {
    $this->menu_tpl = $tpl_code;
  }

  //set menu list
  function set_menu_list($list)
  {
    $this->menu_list = $list;
  }

  //set menu title
  function set_menu_title($title)
  {
    $this->menu_title = $title;
  }

  //set 'add' link to menu
  function set_lnk_admin_add($text, $link)
  {
    $this->lnk_admin_add = array('text' => $text, 'link' => $link);
  }

  //set 'edit' link to menu
  function set_lnk_admin_edit($text, $link)
  {
    $this->lnk_admin_edit = array('text' => $text, 'link' => $link);
  }

  function init_smarty()
  {
    global $template;

    $template->smarty->register_prefilter(array(&$this, 'modify_tpl'));
  }

  function modify_tpl($tpl_source, &$smarty)
  {
    return(str_replace('<div id="menubar">', '<div id="menubar">(test3)'.$this->menu_tpl, $tpl_source));
  }

  /*
    Update PWG menubar
      - add a MyPolls block
      - add a MyPolls menu inside
  */
  function update_menubar()
  {
    global $template;

    @call_user_func($this->callback_init_menu_function);

    //echo "update_menubar****".$this->menu_tpl."****".$this->menu_title."****".count($this->menu_list)."****";
    //do not do nothing because nothing to do
    if((($this->menu_tpl=="") ||
        (count($this->menu_list)==0) ||
        ($this->menu_title=="")
      ) and !is_admin())
    {
      return(false);
    }

    $template_datas=array();
    $template_datas['links']=array();
    $template_datas['TITLE']='toto'.$this->menu_title;

    if(is_admin() && (count($this->lnk_admin_add)>0))
    {
      $template_datas['links'][]=array(
        'LABEL' => "<i>".$this->lnk_admin_add['text']."</i>",
        'URL' => $this->lnk_admin_add['link']
      );
    }

    foreach($this->menu_list as $key => $val)
    {
      if(is_admin() && (count($this->lnk_admin_edit)>0))
      { $lnk_edit = "</a> --- <a href='".$this->lnk_admin_edit['link'].
                    $val['id']."'>[".$this->lnk_admin_edit['text']."]"; }
      else
      { $lnk_edit = ''; }

      $template_datas['links'][]=array(
        'LABEL' => $val['text'].$lnk_edit,
        'URL' => $val['link']
      );
    }

    $template->assign("datas", $template_datas);
  }

  /*
    init section
  */
  function init_section()
  {
    global $tokens, $page;
    
    if ($tokens[0] == $this->section)
    { $page['section'] = $this->section; }
  }

  /*
    loads a page
  */
  function call_page()
  {
    global $page, $user;

    if($page['section'] == $this->section)
    {
      @call_user_func($this->callback_page_function);
    }
  }

} //class public_integration


?>