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

  function get_date_where()
  {
    die("get_date_where not extended");
  }

  /**
   * Initialize the calendar
   * @param string date_field db column on which this calendar works
   * @param string inner_sql used for queries (INNER JOIN or normal)
   */
  function initialize($date_field, $inner_sql)
  {
    $this->date_field = $date_field;
    $this->inner_sql = $inner_sql;
  }

//--------------------------------------------------------- private members ---

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

    if ($allow_any and count($items) > 1)
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
   * @param string view_type - list or calendar (e.g. 'l' or 'c')
   * @param array requested - array of current selected elements (e.g. 2005,10)
   * @param string sql_func - YEAR/MONTH/DAY/WEEK/DAYOFWEEK ...
   * @param string sql_offset - (e.g. +1 for WEEK - first in year is 1)
   * @param array labels - optional labels to show in the navigation bar
   * @return void
   */
  function build_nav_bar($view_type, $requested, $level, $sql_func,
                         $sql_offset='', $labels=null)
  {
    global $template;
    
    $query = '
SELECT DISTINCT('.$sql_func.'('.$this->date_field.')'.$sql_offset
      .') as period';
    $query.= $this->inner_sql;
    $query.= $this->get_date_where($requested, $level);
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
    $url_base .= $view_type.'-';
    for ($i=0; $i<$level; $i++)
    {
      if (isset($requested[$i]))
      {
        $url_base .= $requested[$i].'-';
      }
    }

    $nav_bar = $this->get_nav_bar_from_items(
      $url_base,
      $level_items,
      isset($requested[$level]) ? $requested[$level] : null,
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