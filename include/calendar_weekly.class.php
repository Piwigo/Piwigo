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
   * Initialize the calendar
   * @param string date_field db column on which this calendar works
   * @param string inner_sql used for queries (INNER JOIN or normal)
   * @param array date_components
   */
  function initialize($date_field, $inner_sql, $date_components)
  {
    parent::initialize($date_field, $inner_sql, $date_components);
    global $lang;
    $this->calendar_levels = array(
      array(
          'sql'=> 'YEAR('.$this->date_field.')',
          'labels' => null
        ),
      array(
          'sql'=> 'WEEK('.$this->date_field.')+1',
          'labels' => null
        ),
      array(
          'sql'=> 'DAYOFWEEK('.$this->date_field.')-1',
          'labels' => $lang['day']
        ),
     );
    //Comment next lines for week starting on Sunday or if MySQL version<4.0.17
    //WEEK(date,5) = "0-53 - Week 1=the first week with a Monday in this year"
    $this->calendar_levels[CWEEK]['sql'] = 'WEEK('.$this->date_field.',5)+1';
    $this->calendar_levels[CDAY]['sql'] = 'WEEKDAY('.$this->date_field.')';
    array_push( $this->calendar_levels[CDAY]['labels'],
                array_shift( $this->calendar_levels[CDAY]['labels'] ) );
  }

/**
 * Generate navigation bars for category page
 * @return boolean false to indicate that thumbnails where not included here
 */
function generate_category_content($url_base, $view_type)
{
  global $conf;

  $this->url_base = $url_base;

  assert($view_type==CAL_VIEW_LIST);

  if ( count($this->date_components)==0 )
  {
    $this->build_nav_bar(CYEAR); // years
  }
  if ( count($this->date_components)==1 )
  {
    $this->build_nav_bar(CWEEK); // week nav bar 1-53
  }
  if ( count($this->date_components)==2 )
  {
    $this->build_nav_bar(CDAY); // days nav bar Mon-Sun
  }
  $this->build_next_prev();
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
  $date = $this->date_components;
  while (count($date)>$max_levels)
  {
    array_pop($date);
  }
  $res = '';
  if (isset($date[CYEAR]) and $date[CYEAR]!='any')
  {
    $y = $date[CYEAR];
    $res = " AND $this->date_field BETWEEN '$y-01-01' AND '$y-12-31 23:59:59'";
  }

  if (isset($date[CWEEK]) and $date[CWEEK]!='any')
  {
    $res .= ' AND '.$this->calendar_levels[CWEEK]['sql'].'='.$date[CWEEK];
  }
  if (isset($date[CDAY]) and $date[CDAY]!='any')
  {
    $res .= ' AND '.$this->calendar_levels[CDAY]['sql'].'='.$date[CDAY];
  }
  if (empty($res))
  {
    $res = ' AND '.$this->date_field.' IS NOT NULL';
  }
  return $res;
}

}

?>