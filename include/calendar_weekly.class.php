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

/**
 * Weekly calendar style (composed of years/week in years and days in week)
 */
class Calendar extends CalendarBase
{

/**
 * Generate navigation bars for category page
 * @return boolean false to indicate that thumbnails where not included here
 */
function generate_category_content($url_base, $view_type, &$requested)
{
  global $lang;

  $this->url_base = $url_base;

  assert($view_type==CAL_VIEW_LIST);

  $this->build_nav_bar($view_type, $requested, 0, 'YEAR'); // years
  if (count($requested)>0)
    $this->build_nav_bar($view_type, $requested, 1, 'WEEK', '+1' ); // month
  if (count($requested)>1)
    $this->build_nav_bar($view_type, $requested, 2, 'DAYOFWEEK', '-1',
                         $lang['day'] ); // days
  return false;
}


/**
 * Returns a sql where subquery for the date field
 * @param array requested selected levels for this calendar
 * (e.g. 2005,42,1 for 41st week of 2005, Monday)
 * @param int max_levels return the where up to this level
 * (e.g. 2=only year and week in year)
 * @return string
 */
function get_date_where($requested, $max_levels=3)
{
  while (count($requested)>$max_levels)
  {
    array_pop($requested);
  }
  $res = '';
  if (isset($requested[0]) and $requested[0]!='any')
  {
    $y = $requested[0];
    $res = " AND $this->date_field BETWEEN '$y-01-01' AND '$y-12-31'";
  }

  if (isset($requested[1]) and $requested[1]!='any')
  {
    $res .= ' AND WEEK('.$this->date_field.')+1='.$requested[1];
  }
  if (isset($requested[2]) and $requested[2]!='any')
  {
    $res .= ' AND DAYOFWEEK('.$this->date_field.')-1='.$requested[2];
  }
  if (empty($res))
  {
    $res = ' AND '.$this->date_field.' IS NOT NULL';
  }
  return $res;
}

}

?>