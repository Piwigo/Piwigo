<?php

/* -----------------------------------------------------------------------------
  class name: allowed_access, groups, users
  class version: 1.0
  date: 2007-10-31
  ------------------------------------------------------------------------------
  author: grum at grum.dnsalias.com
  << May the Little SpaceFrog be with you >>
  ------------------------------------------------------------------------------

   this classes provides base functions to manage users/groups access
  groups and users classes extends allowed_access classes

    - constructor allowed_access($alloweds="")
    - constructor groups($alloweds="")
    - constructor users($alloweds="")
    - (public) function get_list()
    - (public) function set_allowed($id, $allowed)
    - (public) function set_alloweds()
    - (public) function get_alloweds($return_type)
    - (public) function is_allowed($id)
    - (public) function html_view($sep=", ", $empty="")
    - (public) function html_form($basename)
    - (private) function init_list()
   ---------------------------------------------------------------------- */
class allowed_access
{
  var $access_list;

  /*
    constructor initialize the groups_list
  */
  function allowed_access($alloweds = "")
  {
    $this->init_list();
    $this->set_alloweds($alloweds);
  }

  /*
    initialize the groups list
  */
  function init_list()
  {
    $this->access_list=array();
  }

  /*
    returns list (as an array)
  */
  function get_list()
  {
    return($this->access_list);
  }

  /*
    set element an allowed state
  */
  function set_allowed($id, $allowed)
  {
    if(isset($this->access_list[$id]))
    {
      $this->access_list[$id]['allowed']=$allowed;
    }
  }

  /*
    set a group enabled/disabled state
  */
  function set_state($id, $enabled)
  {
    if(isset($this->access_list[$id]))
    {
      $this->access_list[$id]['enabled']=$enabled;
    }
  }

  /*
    set alloweds list
    $list is string of id, separated with "/"
  */
  function set_alloweds($list)
  {
    $alloweds=explode("/", $list);
    $alloweds=array_flip($alloweds);
    foreach($this->access_list as $key => $val)
    {
      if(isset($alloweds[$key]))
      {
        $this->access_list[$key]['allowed']=true;
      }
      else
      {
        $this->access_list[$key]['allowed']=false;
      }
    }
  }

  /*
    get alloweds list
    return a string of groups, separated with "/"
  */
  function get_alloweds($return_type = 'name')
  {
    $returned="";
    foreach($this->access_list as $key => $val)
    {
      if($val['allowed'])
      { $returned.=$val[$return_type]."/"; }
    }
    return($returned);
  }


  /*
    returns true if is allowed
  */
  function is_allowed($id)
  {
    if(isset($this->access_list[$id]))
    { return($this->access_list[$id]['allowed']); }
    else
    { return(false); }
  }

  /*
    returns true if all or one is allowed
      ids is an array
  */
  function are_allowed($ids, $all=false)
  {
    foreach($ids as $val)
    {
      if($all)
      {
        if(!$this->is_allowed($val))
        {
          return(false);
        }
      }
      else
      {
        if($this->is_allowed($val))
        {
          return(true);
        }        
      }
    }
    return(false);
  }

  /*
    returns an HTML list with label rather than id
  */
  function html_view($sep=", ", $empty="")
  {
    $returned="";
    foreach($this->access_list as $key => $val)
    {
      if($val['allowed'])
      {
        if($returned!="")
        {
          $returned.=$sep;
        }
        $returned.=$val['name'];
      }
    }
    if($returned=="")
    {
      $returned=$empty;
    }
    return($returned);
  }
  /*
    returns a generic HTML form to manage the groups access
  */
  function html_form($basename)
  {
    /*
    <!-- BEGIN allowed_group_row -->
    <label><input type="checkbox" name="fmypolls_att_allowed_groups_{allowed_group_row.ID}" {allowed_group_row.CHECKED}/>&nbsp;{allowed_group_row.NAME}</label>
    <!-- END allowed_group_row -->
    */
    $text='';
    foreach($this->access_list as $key => $val)
    {
      if($val['allowed'])
      {
        $checked=' checked';
      }
      else
      {
        $checked='';
      }

      if($val['enabled'])
      {
        $enabled='';
      }
      else
      {
        $enabled=' disabled';
      }

      $text.='<label><input type="checkbox" name="'.$basename.$val['id'].'" '.$checked.$enabled.'/>
          &nbsp;'.$val['name'].'</label>&nbsp;';
    }
    return($text);
  }
} //allowed_access








/* ----------------------------------------------------------------------
   this class provides base functions to manage groups access
    init_list redefined to initialize access_list from database GROUPS
   ---------------------------------------------------------------------- */
class groups extends allowed_access
{
  /*
    initialize the groups list
  */
  function init_list()
  {
    $this->access_list=array();
    $sql="SELECT id, name FROM ".GROUPS_TABLE." ORDER BY name";
    $result=pwg_query($sql);
    if($result)
    {
      while($row=mysql_fetch_assoc($result))
      {
        $this->access_list[$row['id']] =
                   array('id' => $row['id'],
                         'name' => $row['name'],
                         'allowed' => false,
                         'enabled' => true);
      }
    }
  }
}








/* -----------------------------------------------------------------------------
   this class provides base functions to manage users access
----------------------------------------------------------------------------- */
class users extends allowed_access
{
  /*
    constructor
  */
  function users($alloweds = "")
  {
    parent::allowed_access($alloweds);
    $this->set_state('admin', false);
    $this->set_allowed('admin', true);
  }

  /*
    initialize the groups list
  */
  function init_list()
  {
    $users_list = array('guest', 'generic', 'normal', 'admin');
    $this->access_list=array();
    foreach($users_list as $val)
    {
      $this->access_list[$val] =
                  array('id' => $val,
                        'name' => l10n('user_status_'.$val),
                        'allowed' => false,
                        'enabled' => true);
    }
  }
} //class users



?>