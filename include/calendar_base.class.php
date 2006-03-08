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
  //
  var $calendar_levels;

  var $has_nav_bar;

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
    $this->has_nav_bar = false;
  }

  function get_display_name()
  {
    global $conf;
    $res = '';
    $url = $this->url_base;

    for ($i=0; $i<count($this->date_components); $i++)
    {
      $res .= $conf['level_separator'];
      if ($i>0)
      {
        $url .= '-';
      }
      $url .= $this->date_components[$i];
      if ( isset($this->date_components[$i+1]) )
      {
        $res .=
          '<a href="'.$url.'">'
          .$this->get_date_component_label($i, $this->date_components[$i])
          .'</a>';
      }
      else
      {
        $res .=
          '<span class="calInHere">'
          .$this->get_date_component_label($i, $this->date_components[$i])
          .'</span>';
      }
    }
    return $res;
  }

//--------------------------------------------------------- private members ---
  /**
   * Returns a display name for a date component optionally using labels
  */
  function get_date_component_label($level, $date_component)
  {
    $label = $date_component;
    if (isset($this->calendar_levels[$level]['labels'][$date_component]))
    {
      $label = $this->calendar_levels[$level]['labels'][$date_component];
    }
    elseif ('any' === $date_component )
    {
      $label = l10n('calendar_any');
    }
    return $label;
  }

  /**
   * Gets a nice display name for a date to be shown in previos/next links.
   */
  function get_date_nice_name($date)
  {
    $date_components = explode('-', $date);
    $res = '';
    for ($i=count($date_components)-1; $i>=0; $i--)
    {
      if ('any' !== $date_components[$i])
      {
        $label = $this->get_date_component_label($i, $date_components[$i] );
        if ( $res!='' )
        {
          $res .= ' ';
        }
        $res .= $label;
      }
    }
    return $res;
  }

  /**
   * Creates a calendar navigation bar.
   *
   * @param string url_base - links start with this root
   * @param array items - hash of items to put in the bar (e.g. 2005,2006)
   * @param array selected_item - item currently selected (e.g. 2005)
   * @param string class_prefix - html class attribute prefix for span elements
   * @param bool show_any - adds any link to the end of the bar
   * @param bool show_empty - shows all labels even those without items
   * @param array labels - optional labels for items (e.g. Jan,Feb,...)
   * @return string the navigation bar
   */
  function get_nav_bar_from_items($url_base, $items, $selected_item,
                                  $class_prefix, $show_any,
                                  $show_empty=false, $labels=null)
  {
    global $conf;

    $nav_bar = '';

    if ($conf['calendar_show_empty'] and $show_empty and !empty($labels) )
    {
      foreach ($labels as $item => $label)
      {
        if ( ! isset($items[$item]) )
        {
          $items[$item] = -1;
        }
      }
      ksort($items);
    }

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
      elseif ($nb_images==-1)
      {
        $nav_bar .= '<span class="'.$class_prefix.'Empty">';
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

    if ($conf['calendar_show_any'] and $show_any and count($items) > 1)
    {
      $label = l10n('calendar_any');
      if (isset($selected_item) and 'any' === $selected_item)
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
   * @param int level - the level (0-year,1-month/week,2-day)
   * @return void
   */
  function build_nav_bar($level, $labels=null)
  {
    global $template, $conf;

    $query = '
SELECT DISTINCT('.$this->calendar_levels[$level]['sql']
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

    if ( count($level_items)==1 and
         count($this->date_components)<count($this->calendar_levels)-1)
    {
      if ( ! isset($this->date_components[$level]) )
      {
        list($key) = array_keys($level_items);
        $this->date_components[$level] = (int)$key;

        if ( $level<count($this->date_components) and
             $level!=count($this->calendar_levels)-1 )
        {
          return;
        }
      }
    }

    $url_base = $this->url_base;
    for ($i=0; $i<$level; $i++)
    {
      if (isset($this->date_components[$i]))
      {
        $url_base .= $this->date_components[$i].'-';
      }
    }
    $nav_bar = $this->get_nav_bar_from_items(
      $url_base,
      $level_items,
      null,
      'calItem',
      true,
      true,
      isset($labels) ? $labels : $this->calendar_levels[$level]['labels']
      );

    $template->assign_block_vars(
      'calendar.navbar',
      array(
        'BAR' => $nav_bar,
        )
      );
    $this->has_nav_bar = true;
  }

  /**
   * Assigns the next/previous link to the template with regards to
   * the currently choosen date.
   */
  function build_next_prev()
  {
    global $template;
    $prev = $next =null;
    if ( empty($this->date_components) )
      return;
    $query = 'SELECT CONCAT_WS("-"';
    for ($i=0; $i<count($this->date_components); $i++)
    {
      if ( 'any' === $this->date_components[$i] )
      {
        $query .= ','.'"any"';
      }
      else
      {
        $query .= ','.$this->calendar_levels[$i]['sql'];
      }
    }
    $current = implode('-', $this->date_components );

    $query.=') as period' . $this->inner_sql .'
AND ' . $this->date_field . ' IS NOT NULL
GROUP BY period';

    $upper_items = array_from_query( $query, 'period');

    usort($upper_items, 'version_compare');
    $upper_items_rank = array_flip($upper_items);
    if ( !isset($upper_items_rank[$current]) )
    {
      array_push($upper_items, $current);// just in case (external link)
      usort($upper_items, 'version_compare');
      $upper_items_rank = array_flip($upper_items);
    }
    $current_rank = $upper_items_rank[$current];

    if (!$this->has_nav_bar and
        ($current_rank>0 or $current_rank < count($upper_items)-1 ) )
    {
      $template->assign_block_vars( 'calendar.navbar', array() );
    }
    if ( $current_rank>0 )
    { // has previous
      $prev = $upper_items[$current_rank-1];
      $template->assign_block_vars(
        'calendar.navbar.prev',
        array(
          'LABEL' => $this->get_date_nice_name($prev),
          'URL' => $this->url_base . $prev,
          )
        );
    }
    if ( $current_rank < count($upper_items)-1 )
    {
      // has next
      $next = $upper_items[$current_rank+1];
      $template->assign_block_vars(
        'calendar.navbar.next',
        array(
          'LABEL' => $this->get_date_nice_name($next),
          'URL' => $this->url_base . $next,
          )
        );
    }
  }
}
?>