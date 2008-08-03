<?php

/* -----------------------------------------------------------------------------
  class name: menu, blocks 
  class version: 1.0
  date: 2008-07-25

  ------------------------------------------------------------------------------
  Author     : Grum
    email    : grum@grum.dnsalias.com
    website  : http://photos.grum.dnsalias.com
    PWG user : http://forum.phpwebgallery.net/profile.php?id=3706

    << May the Little SpaceFrog be with you ! >>
  ------------------------------------------------------------------------------

  this classes provides base functions to manage the public gallery menu

  ** The Section class **
  This class allows you to easily make a section block for the menu
  Public methods
    get_id()
    get_name()
    get_template()
    get_position()
    get_tag()
    get_items()
    set_name($value)
    set_template($value)
    set_tag($value)
    set_items($value)
    items_count()
    get()
  Constructor
    $id       : section's id have to be unique in the menu
    $name     : section's name is displayed on the menu
    $template : name of smarty template file ; if file doesn't exist, use of the
                generic template
    $tag      : a facultative data ; use it as you want !
    $items    : items of the menu : no specfic structure because it depends of
                the template model

  ** The Menu class **
  This class allows to easily manage the menu
  Public methods
    add($section_datas)
    remove($id)
    replace($section_datas)
    clear()
    section($id)
    sections()
    ids()
    count()
    register($id, $name, $position, $owner)
    register_position($id, $position)
    unregister($id)
    registered()
    apply()
 
  How to use Menu class :
    1/ create an instance
          $menu = new Menu();
        instance have to be created by piwigo, plugin have to use of a global
        variable

    2/ register your menu section
        register a section allows to know sections even if $menu is not loaded
        register a section allows to manage position into menu
        register function only reference some information about section, it does
        not create the section of menu (an only registered section is not
        displayed in menu)
        best place to register a section : when plugin is activated
        when deactivate plugin, unregister the section

    3/ add section to menu
        add a section allows to really create section into menu
          
 


  ------------------------------------------------------------------------------
  :: HISTORY

  1.0.0     - 2008-07-25
              first lines of code...

  --------------------------------------------------------------------------- */

// theses constant ave to be adapted for a piwigo's integration
define('MENU_TEMPLATES_DIR' , dirname(__FILE__));
define('MENU_TEMPLATES_PATH' , MENU_TEMPLATES_DIR . '/menu_templates/');
define('MENU_REGISTERED_PATH' ,
        PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');


class Menu
{
  protected $sections=array();  //array key is unique ID of sections
  protected $registered_sections=array();
  protected $registered_file="registered.dat";

  public function Menu()
  {
    $this->load_registered();
  }

  /*
    public functions
  */
  public function add($section_datas)
  {
    // add a section to the menu ; datas can be made with the Section class
    if($this->section_is_valid($section_datas) and
        !isset($this->sections[$section_datas['ID']]) )
    {
      $this->sections[$section_datas['ID']]=$section_datas;

      if(isset($this->registered_sections[$section_datas['ID']]))
      {
        $this->sections[$section_datas['ID']]['POSITION']=$this->registered_sections[$section_datas['ID']]['POSITION'];
      }
      else
      {
        $this->sections[$section_datas['ID']]['POSITION']=0;
      }
      return(true);
    }
    return(false);
  }

  public function remove($id)
  {
    // remove a section from the menu
    unset($this->sections[$id]);
  }

  public function replace($section_datas)
  {
    // replace an existing section description by another one
    if($this->section_is_valid($section_datas) and
        isset($this->sections[$section_datas['ID']]) )
    {
      $this->sections[$section_datas['ID']]=$section_datas;
      return(true);
    }
    return(false);
  }

  public function clear()
  {
    // clear all sections of the menu
    $this->sections=array();
  }

  public function section($id)
  {
    //return a section structure or false if requested ID not exists
    if(isset($this->sections[$id]))
    {
      return($this->sections[$id]);
    }
    return(false);
  }

  public function sections()
  {
    //return all sections
    return($this->sections);
  }

  public function ids()
  {
    //return all section's ids
    return(array_keys($this->sections));
  }

  public function count()
  {
    //return number of sections
    return(count($this->sections));
  }

  public function register($id, $name, $position, $owner)
  {
    /*
     register section for menu ; register a section allows to know sections list
     even if menu is not made (ie: we are in admin interface)

     register a section is not adding a section : it's just for making a list of
     potential sections.
     you can add in the menu a section who is not registered

      $id       : section id
      $name     : name of section
      $position : position of section
      $owner    : owner of section (piwigo or plugin's name)
    */
    if(!isset($this->registered_sections[$id]))
    {
      if($position<1)
      {
        $position=1;
      }
      $this->registered_sections[$id]=array(
        'NAME' => $name,
        'POSITION'=>$position,
        'OWNER' => $owner
      );
      $this->register_position($id, $position);
      //$this->save_registered(); ==> made with register_position
      return(true);
    }
    return(false);
  }

  public function unregister($id)
  {
    // just unregister a section from menu
    if(isset($this->registered_sections[$id]))
    {
      unset($this->registered_sections[$id]);
      $this->registered_sections=$this->renum_position($this->registered_sections);
      $this->save_registered();
      return(true);
    }
    return(false);
  }

  public function register_position($id, $position)
  {
    // register a new position for section
    // if a section already have the same position, all section are shifted

    // sort registered sections by position
    $this->sort_registered();
    //preparing sections
    $incpos=false;
    foreach($this->registered_sections as $key => $val)
    {
      if(($val['POSITION']==$position)and($key!=$id))
      {
        $incpos=true;
      }
      if(($incpos)and($key!=$id))
      {
        $this->registered_sections[$key]['POSITION']++;
      }
    }
    //affect new position
    $this->registered_sections[$id]['POSITION']=$position;
    //sort
    $this->sort_registered();
    //renum positions
    $this->registered_sections=$this->renum_position($this->registered_sections);
    $this->save_registered();
  }

  public function registered()
  {
    // return list of registered sections
    return($this->registered_sections);
  }

  public function apply()
  {
    //apply datas on the template
    global $template;

    $template->set_filenames(
      array('menubar' => MENU_TEMPLATES_PATH.'menubar_main.tpl')
    );

    trigger_action('loc_begin_menubar');
    $this->sort();
    $template->assign('sections', $this->sections);
    trigger_action('loc_end_menubar');

    $template->assign_var_from_handle('MENUBAR', 'menubar');
  }

  /*
    protected functions
  */
  protected function section_is_valid($section_datas)
  {
    if(is_array($section_datas) and
      isset($section_datas['ID']) and
      isset($section_datas['NAME']) and
      isset($section_datas['TEMPLATE']) and
      isset($section_datas['ITEMS']) and
      isset($section_datas['TAG']))
    {
      return(true);
    }
    return(false);
  }

  protected function load_registered()
  {
    //load registered sections : database or file ??
    $this->registered_sections=array();

    $filename=MENU_REGISTERED_PATH.$this->registered_file;

    if(file_exists($filename))
    {
      $fhandle=fopen($filename, "r");
      if($fhandle)
      {
        $datas=fread($fhandle, filesize($filename));
        fclose($fhandle);
        $this->registered_sections=unserialize($datas);
        return(true);
      }
    }
    return(false);
  }

  protected function save_registered()
  {
    //save registered sections : database or file ??
    $filename=MENU_REGISTERED_PATH.$this->registered_file;

    $fhandle=fopen($filename, "w");
    if($fhandle)
    {
      $written=fwrite($fhandle, serialize($this->registered_sections));
      fclose($fhandle);
      return($written);
    }
    return(false);
  }


  protected function sort()
  {
    $tmp=$this->sections;
    uksort($tmp, array(&$this, 'sort_sections_cmpfct'));
    $this->sections=$tmp;
  }

  protected function sort_registered()
  {
    $tmp=$this->registered_sections;
    uksort($tmp, array(&$this, 'sort_registered_cmpfct'));
    $this->registered_sections=$tmp;
  }


  private function sort_sections_cmpfct($a, $b)
  {
    if($this->sections[$a]['POSITION']==$this->sections[$b]['POSITION'])
    {
      return(($this->sections[$a]['ID']<$this->sections[$b]['ID'])?-1:1);
    }
    return(($this->sections[$a]['POSITION']<$this->sections[$b]['POSITION'])?-1:1);
  }

  private function sort_registered_cmpfct($a, $b)
  {
    return(($this->registered_sections[$a]['POSITION']<$this->registered_sections[$b]['POSITION'])?-1:1);
  }

  private function renum_position($datas)
  {
    $i=1;
    foreach($datas as $key => $val)
    {
      $datas[$key]['POSITION']=$i;
      $i+=1;
    }
    return($datas);
  }


  

} // class Menu





class Section
{
  protected $name;
  protected $template="generic.tpl";
  protected $id;
  protected $tag;
  protected $items=array();

  public function Section($id, $name, $template="", $tag="")
  {
    $this->id = $id;
    $this->tag = $tag;
    $this->set_name($name);
    if(!$this->set_template($template))
    {
      $this->template=MENU_TEMPLATES_PATH."generic.tpl";
    }

  } // constructor

  /*
    public functions
  */
  public function get()
  {
    //this method returns a data structure ready to be used with the Menu class
    return(
      array(
        'ID' => $this->id,
        'NAME' => $this->name,
        'TEMPLATE' => $this->template,
        'ITEMS' => $this->items,
        'TAG' => $this->tag
      )
    );
  }

  public function get_id()
  {
    return($this->id);
  }

  public function get_name()
  {
    return($this->name);
  }

  public function get_template()
  {
    return($this->template);
  }

  public function get_tag()
  {
    return($this->tag);
  }

  public function get_items()
  {
    return($this->items);
  }

  public function set_name($value)
  {
    $this->name=$value;
  }

  public function set_template($value)
  {
    if($this->is_template($value))
    {
      $this->template=$value;
      return(true);
    }
    return(false);
  }

  public function set_tag($value)
  {
    $this->tag=$value;
  }

  public function set_items($value)
  {
    $this->items=$value;
  }

  public function items_count()
  {
    if(is_array($this->items))
    {
      return(count($this->items));
    }
    else
    {
      return(-1);
    }
  }

  /*
    protected functions
  */
  protected function is_template($templatename)
  {
    if(file_exists($templatename) and (preg_match('/.+\.tpl$/i',$templatename)))
    {
      return(true);
    }
    return(false);
  } // is_template

} //class Section




?>