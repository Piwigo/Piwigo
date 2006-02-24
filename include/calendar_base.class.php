<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-01-27 02:11:43 +0100 (ven, 27 jan 2006) $
// | last modifier : $Author: rvelices $
// | revision      : $Revision: 1014 $
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

/**
 * Base class for monthly and weekly calendar styles
 */
class CalendarBase
{
  // db column on which this calendar works
  var $date_field;
  // used for queries (INNER JOIN or normal)
  var $inner_sql;
  // base url used when generating html links
  var $url_base;
  // array of date components e.g. (2005,10,12) ...
  var $date_components;


  /**
   * Initialize the calendar
   * @param string date_field db column on which this calendar works
   * @param string inner_sql used for queries (INNER JOIN or normal)
   * @param array date_components
   */
  function initialize($date_field, $inner_sql, $date_components)
  {
    $this->date_field = $date_field;
    $this->inner_sql = $inner_sql;
    $this->date_components = $date_components;
  }

//--------------------------------------------------------- private members ---
  /**
   * Returns a display name for a date component optionally using labels
  */
  function get_date_component_label($date_component, $labels=null)
  {
    $label = $date_component;
    if (isset($labels[$date_component]))
    {
      $label = $labels[$date_component];
    }
    elseif ($date_component == 'any' )
    {
      $label = l10n('calendar_any');
    }
    return $label;
  }
  
  /**
   * Creates a calendar navigation bar.
   *
   * @param string url_base - links start with this root
   * @param array items - hash of items to put in the bar (e.g. 2005,2006)
   * @param array selected_item - item currently selected (e.g. 2005)
   * @param string class_prefix - html class attribute prefix for span elements
   * @param bool allow_any - adds any to the end of the bar
   * @param array labels - optional labels for items (e.g. Jan,Feb,...)
   * @return string the navigation bar
   */
  function get_nav_bar_from_items($url_base, $items, $selected_item,
                                  $class_prefix, $allow_any, $labels=null)
  {
    $nav_bar = '';
      
    foreach ($items as $item => $nb_images)
    {
      $label = $item;
      if (isset($labels[$item]))
      {
        $label = $labels[$item];
      }
      if (isset($selected_item) and $item == $selected_item)
      {
        $nav_bar .= '<span class="'.$class_prefix.'Sel">';
        $nav_bar .= $label;
      }
      else
      {
        $nav_bar .= '<span class="'.$class_prefix.'">';
        $url = $url_base . $item;
        $nav_bar .= '<a href="'.$url.'">';
        $nav_bar .= $label;
        $nav_bar .= '</a>';
      }
      if ($nb_images > 0)
      {
        $nav_bar .= '('.$nb_images.')';
      }
      $nav_bar.= '</span>';
    }
    global $conf;
    if ($conf['calendar_show_any'] and $allow_any and count($items) > 1)
    {
      $label = l10n('calendar_any');
      if (isset($selected_item) and 'any' == $selected_item)
      {
        $nav_bar .= '<span class="'.$class_prefix.'Sel">';
        $nav_bar .= $label;
      }
      else
      {
        $nav_bar .= '<span class="'.$class_prefix.'">';
        $url = $url_base . 'any';
        $nav_bar .= '<a href="'.$url.'">';
        $nav_bar .= $label;
        $nav_bar .= '</a>';
      }
      $nav_bar.= '</span>';
    }
    return $nav_bar;
  }

  /**
   * Creates a calendar navigation bar for a given level.
   *
   * @param string sql_func - YEAR/MONTH/DAY/WEEK/DAYOFWEEK ...
   * @param string sql_offset - (e.g. +1 for WEEK - first in year is 1)
   * @param array labels - optional labels to show in the navigation bar
   * @return void
   */
  function build_nav_bar($level, $sql_func,
                         $sql_offset='', $labels=null)
  {
    global $template;
    
    $query = '
SELECT DISTINCT('.$sql_func.'('.$this->date_field.')'.$sql_offset
      .') as period';
    $query.= $this->inner_sql;
    $query.= $this->get_date_where($level);
    $query.= '
  GROUP BY period
;';

    $level_items = array();
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      $level_items[$row['period']] = 0;
    }

    $url_base = $this->url_base;
    for ($i=0; $i<$level; $i++)
    {
      if (isset($this->date_components[$i]))
      {
        $url_base .= $this->date_components[$i].'-';
      }
    }
    $selected = null;
    if ( isset($this->date_components[$level]) )
    {
      $selected = $this->date_components[$level];
    }
    $nav_bar = $this->get_nav_bar_from_items(
      $url_base,
      $level_items,
      $selected,
      'cal',
      true,
      $labels
      );

    $template->assign_block_vars(
      'calendar.navbar',
      array(
        'BAR' => $nav_bar
        )
      );
  }
}
?>