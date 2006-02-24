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

include_once(PHPWG_ROOT_PATH.'include/calendar_base.class.php');

define ('CYEAR', 0);
define ('CWEEK', 1);
define ('CDAY',  2);

/**
 * Weekly calendar style (composed of years/week in years and days in week)
 */
class Calendar extends CalendarBase
{

/**
 * Generate navigation bars for category page
 * @return boolean false to indicate that thumbnails where not included here
 */
function generate_category_content($url_base, $view_type)
{
  global $lang, $conf;

  $this->url_base = $url_base;

  assert($view_type==CAL_VIEW_LIST);

  if ( $conf['calendar_multi_bar'] or count($this->date_components)==0 )
  {
    $this->build_nav_bar(CYEAR, 'YEAR'); // years
  }
  if ( count($this->date_components)>=1 and
      ( $conf['calendar_multi_bar'] or count($this->date_components)==1 )
     )
  {
    $this->build_nav_bar(CWEEK, 'WEEK', '+1' ); // month
  }
  if ( count($this->date_components)>=2 )
  {
    $this->build_nav_bar(CDAY, 'DAYOFWEEK', '-1',
                         $lang['day'] ); // days
  }
  return false;
}


/**
 * Returns a sql where subquery for the date field
 * @param int max_levels return the where up to this level
 * (e.g. 2=only year and week in year)
 * @return string
 */
function get_date_where($max_levels=3)
{
  $date_components = $this->date_components;
  while (count($date_components)>$max_levels)
  {
    array_pop($date_components);
  }
  $res = '';
  if (isset($date_components[CYEAR]) and $date_components[CYEAR]!='any')
  {
    $y = $date_components[CYEAR];
    $res = " AND $this->date_field BETWEEN '$y-01-01' AND '$y-12-31 23:59:59'";
  }

  if (isset($date_components[CWEEK]) and $date_components[CWEEK]!='any')
  {
    $res .= ' AND WEEK('.$this->date_field.')+1='.$date_components[CWEEK];
  }
  if (isset($date_components[CDAY]) and $date_components[CDAY]!='any')
  {
    $res .= ' AND DAYOFWEEK('.$this->date_field.')-1='
            .$date_components[CDAY];
  }
  if (empty($res))
  {
    $res = ' AND '.$this->date_field.' IS NOT NULL';
  }
  return $res;
}

function get_display_name()
{
  global $conf,$lang;
  $res = '';
  $url = $this->url_base;
  if ( isset($this->date_components[CYEAR]) )
  {
    $res .= $conf['level_separator'];
    $url .= $this->date_components[CYEAR].'-';
    $res .= 
      '<a href="'.$url.'">'
      .$this->get_date_component_label($this->date_components[CYEAR])
      .'</a>';
  }
  if ( isset($this->date_components[CWEEK]) )
  {
    $res .= $conf['level_separator'];
    $url .= $this->date_components[CWEEK].'-';
    $res .= 
      '<a href="'.$url.'">'
      .$this->get_date_component_label($this->date_components[CWEEK])
      .'</a>';
  }
  if ( isset($this->date_components[CDAY]) )
  {
    $res .= $conf['level_separator'];
    $url .= $this->date_components[CDAY].'-';
    $res .= 
      '<a href="'.$url.'">'
      .$this->get_date_component_label(
                    $this->date_components[CDAY], 
                    $lang['day']
                    )
      .'</a>';
  }
  return $res;
}

}

?>