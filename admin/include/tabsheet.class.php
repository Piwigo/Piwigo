<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

class tabsheet
{
  var $sheets;
  var $name;
  var $titlename;
  var $selected;

  /*
    $name is the tabsheet's name inside the template .tpl file
    $titlename in the template is affected by $titlename value
  */
  function tabsheet($name = 'TABSHEET', $titlename = 'TABSHEET_TITLE')
  {
    $this->sheets = array();
    $this->name = $name;
    $this->titlename = $titlename;
    $this->selected = "";
  }

  /*
     add a tab
  */
  function add($name, $caption, $url, $selected = false)
  {
    if (!isset($this->sheets[$name]))
    {
      $this->sheets[$name] = array('caption' => $caption,
                                   'url' => $url);
      if($selected)
      {
        $this->selected=$name;
      }
      return true;
    }
    return false;
  }

  /*
     remove a tab
  */
  function delete($name)
  {
    if (isset($this->sheets[$name]))
    {
      array_splice($this->sheets, $name, 1);

      if ($this->selected == $name)
      {
        $this->selected = "";
      }
      return true;
    }
    return false;
  }

  /*
     select a tab to be active
  */
  function select($name)
  {
    $this->selected = $name;
  }

  /*
    set $titlename value
  */
  function set_titlename($titlename)
  {
    $this->titlename = $titlename;
    return $this->titlename;
  }

  /*
    returns $titlename value
  */
  function get_titlename()
  {
    return $this->titlename;
  }

  /*
    returns properties of selected tab
  */
  function get_selected()
  {
    if (!empty($this->selected))
    {
      return $this->sheets[$this->selected];
    }
    else
    {
      return null;
    }
  }

  /*
   * Build TabSheet and assign this content to current page
   *
   * Fill $this->$name {default value = TABSHEET} with HTML code for tabsheet
   * Fill $this->titlename {default value = TABSHEET_TITLE} with formated caption of the selected tab
   */
  function assign()
  {
    global $template;

    $template->set_filename('tabsheet', 'tabsheet.tpl');
    $template->assign('tabsheet', $this->sheets);
    $template->assign('tabsheet_selected', $this->selected);

    $selected_tab = $this->get_selected();

    if (isset($selected_tab))
    {
      $template->assign(
        array($this->titlename => '['.$selected_tab['caption'].']'));
    }

    $template->assign_var_from_handle($this->name, 'tabsheet');
    $template->clear_assign('tabsheet');
  }
}

?>
