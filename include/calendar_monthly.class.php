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

include_once(PHPWG_ROOT_PATH.'include/calendar_base.class.php');

define ('CYEAR',  0);
define ('CMONTH', 1);
define ('CDAY',   2);

/**
 * Monthly calendar style (composed of years/months and days)
 */
class Calendar extends CalendarBase
{

  /**
   * Initialize the calendar
   * @param string inner_sql used for queries (INNER JOIN or normal)
   */
  function initialize($inner_sql)
  {
    parent::initialize($inner_sql);
    global $lang;
    $this->calendar_levels = array(
      array(
          'sql'=> pwg_db_get_year($this->date_field),
          'labels' => null
        ),
      array(
	  'sql'=> pwg_db_get_month($this->date_field),
	  'labels' => $lang['month']
        ),
      array(
          'sql'=> pwg_db_get_dayofmonth($this->date_field),
          'labels' => null
        ),
     );
  }

/**
 * Generate navigation bars for category page
 * @return boolean false to indicate that thumbnails
 * where not included here, true otherwise
 */
function generate_category_content()
{
  global $conf, $page;

  $view_type = $page['chronology_view'];
  if ($view_type==CAL_VIEW_CALENDAR)
  {
    global $template;
    $tpl_var = array();
    if ( count($page['chronology_date'])==0 )
    {//case A: no year given - display all years+months
      if ($this->build_global_calendar($tpl_var))
      {
        $template->assign('chronology_calendar', $tpl_var);
        return true;
      }
    }

    if ( count($page['chronology_date'])==1 )
    {//case B: year given - display all days in given year
      if ($this->build_year_calendar($tpl_var))
      {
        $template->assign('chronology_calendar', $tpl_var);
        $this->build_nav_bar(CYEAR); // years
        return true;
      }
    }

    if ( count($page['chronology_date'])==2 )
    {//case C: year+month given - display a nice month calendar
      if ( $this->build_month_calendar($tpl_var) )
      {
        $template->assign('chronology_calendar', $tpl_var);
      }
      $this->build_next_prev();
      return true;
    }
  }

  if ($view_type==CAL_VIEW_LIST or count($page['chronology_date'])==3)
  {
    if ( count($page['chronology_date'])==0 )
    {
      $this->build_nav_bar(CYEAR); // years
    }
    if ( count($page['chronology_date'])==1)
    {
      $this->build_nav_bar(CMONTH); // month
    }
    if ( count($page['chronology_date'])==2 )
    {
      $day_labels = range( 1, $this->get_all_days_in_month(
              $page['chronology_date'][CYEAR] ,$page['chronology_date'][CMONTH] ) );
      array_unshift($day_labels, 0);
      unset( $day_labels[0] );
      $this->build_nav_bar( CDAY, $day_labels ); // days
    }
    $this->build_next_prev();
  }
  return false;
}


/**
 * Returns a sql where subquery for the date field
 * @param int max_levels return the where up to this level
 * (e.g. 2=only year and month)
 * @return string
 */
function get_date_where($max_levels=3)
{
  global $page;

  $date = $page['chronology_date'];
  while (count($date)>$max_levels)
  {
    array_pop($date);
  }
  $res = '';
  if (isset($date[CYEAR]) and $date[CYEAR]!=='any')
  {
    $b = $date[CYEAR] . '-';
    $e = $date[CYEAR] . '-';
    if (isset($date[CMONTH]) and $date[CMONTH]!=='any')
    {
      $b .= sprintf('%02d-', $date[CMONTH]);
      $e .= sprintf('%02d-', $date[CMONTH]);
      if (isset($date[CDAY]) and $date[CDAY]!=='any')
      {
        $b .= sprintf('%02d', $date[CDAY]);
        $e .= sprintf('%02d', $date[CDAY]);
      }
      else
      {
        $b .= '01';
        $e .= $this->get_all_days_in_month($date[CYEAR], $date[CMONTH]);
      }
    }
    else
    {
      $b .= '01-01';
      $e .= '12-31';
      if (isset($date[CMONTH]) and $date[CMONTH]!=='any')
      {
        $res .= ' AND '.$this->calendar_levels[CMONTH]['sql'].'='.$date[CMONTH];
      }
      if (isset($date[CDAY]) and $date[CDAY]!=='any')
      {
        $res .= ' AND '.$this->calendar_levels[CDAY]['sql'].'='.$date[CDAY];
      }
    }
    $res = " AND $this->date_field BETWEEN '$b' AND '$e 23:59:59'" . $res;
  }
  else
  {
    $res = ' AND '.$this->date_field.' IS NOT NULL';
    if (isset($date[CMONTH]) and $date[CMONTH]!=='any')
    {
      $res .= ' AND '.$this->calendar_levels[CMONTH]['sql'].'='.$date[CMONTH];
    }
    if (isset($date[CDAY]) and $date[CDAY]!=='any')
    {
      $res .= ' AND '.$this->calendar_levels[CDAY]['sql'].'='.$date[CDAY];
    }
  }
  return $res;
}



//--------------------------------------------------------- private members ---

// returns an array with all the days in a given month
function get_all_days_in_month($year, $month)
{
  $md= array(1=>31,28,31,30,31,30,31,31,30,31,30,31);

  if ( is_numeric($year) and $month==2)
  {
    $nb_days = $md[2];
    if ( ($year%4==0)  and ( ($year%100!=0) or ($year%400!=0) ) )
    {
      $nb_days++;
    }
  }
  elseif ( is_numeric($month) )
  {
    $nb_days = $md[ $month ];
  }
  else
  {
    $nb_days = 31;
  }
  return $nb_days;
}

function build_global_calendar(&$tpl_var)
{
  global $page;

  assert( count($page['chronology_date']) == 0 );
  $query='
SELECT '.pwg_db_get_date_YYYYMM($this->date_field).' as period,'
    .pwg_db_get_year($this->date_field).' as year, '
    .pwg_db_get_month($this->date_field).' as month, 
            count(distinct id) as count';
  $query.= $this->inner_sql;
  $query.= $this->get_date_where();
  $query.= '
  GROUP BY period, year, month
  ORDER BY year DESC, month ASC';

  $result = pwg_query($query);
  $items=array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $y = substr($row['period'], 0, 4);
    $m = (int)substr($row['period'], 4, 2);
    if ( ! isset($items[$y]) )
    {
      $items[$y] = array('nb_images'=>0, 'children'=>array() );
    }
    $items[$y]['children'][$m] = $row['count'];
    $items[$y]['nb_images'] += $row['count'];
  }
  //echo ('<pre>'. var_export($items, true) . '</pre>');
  if (count($items)==1)
  {// only one year exists so bail out to year view
    list($y) = array_keys($items);
    $page['chronology_date'][CYEAR] = $y;
    return false;
  }

  global $lang;
  foreach ( $items as $year=>$year_data)
  {
    $chronology_date = array( $year );
    $url = duplicate_index_url( array('chronology_date'=>$chronology_date) );

    $nav_bar = $this->get_nav_bar_from_items( $chronology_date,
            $year_data['children'], false, false, $lang['month'] );

    $tpl_var['calendar_bars'][] =
      array(
        'U_HEAD'  => $url,
        'NB_IMAGES' => $year_data['nb_images'],
        'HEAD_LABEL' => $year,
        'items' => $nav_bar,
      );
  }
  return true;
}

function build_year_calendar(&$tpl_var)
{
  global $page;

  assert( count($page['chronology_date']) == 1 );
  $query='SELECT '.pwg_db_get_date_MMDD($this->date_field).' as period,
            COUNT(DISTINCT id) as count';
  $query.= $this->inner_sql;
  $query.= $this->get_date_where();
  $query.= '
  GROUP BY period
  ORDER BY period ASC';

  $result = pwg_query($query);
  $items=array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $m = (int)substr($row['period'], 0, 2);
    $d = substr($row['period'], 2, 2);
    if ( ! isset($items[$m]) )
    {
      $items[$m] = array('nb_images'=>0, 'children'=>array() );
    }
    $items[$m]['children'][$d] = $row['count'];
    $items[$m]['nb_images'] += $row['count'];
  }
  if (count($items)==1)
  { // only one month exists so bail out to month view
    list($m) = array_keys($items);
    $page['chronology_date'][CMONTH] = $m;
    return false;
  }
  global $lang;
  foreach ( $items as $month=>$month_data)
  {
    $chronology_date = array( $page['chronology_date'][CYEAR], $month );
    $url = duplicate_index_url( array('chronology_date'=>$chronology_date) );

    $nav_bar = $this->get_nav_bar_from_items( $chronology_date,
                     $month_data['children'], false );

    $tpl_var['calendar_bars'][] =
      array(
        'U_HEAD'  => $url,
        'NB_IMAGES' => $month_data['nb_images'],
        'HEAD_LABEL' => $lang['month'][$month],
        'items' => $nav_bar,
      );
  }
  return true;

}

function build_month_calendar(&$tpl_var)
{
  global $page;

  $query='SELECT '.pwg_db_get_dayofmonth($this->date_field).' as period,
            COUNT(DISTINCT id) as count';
  $query.= $this->inner_sql;
  $query.= $this->get_date_where();
  $query.= '
  GROUP BY period
  ORDER BY period ASC';

  $items=array();
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $d = (int)$row['period'];
    $items[$d] = array('nb_images'=>$row['count']);
  }

  foreach ( $items as $day=>$data)
  {
    $page['chronology_date'][CDAY]=$day;
    $query = '
SELECT id, file,tn_ext,path, width, height, '.pwg_db_get_dayofweek($this->date_field).'-1 as dow';
    $query.= $this->inner_sql;
    $query.= $this->get_date_where();
    $query.= '
  ORDER BY '.DB_RANDOM_FUNCTION.'()
  LIMIT 1';
    unset ( $page['chronology_date'][CDAY] );

    $row = pwg_db_fetch_assoc(pwg_query($query));
    $items[$day]['tn_url'] = get_thumbnail_url($row);
    $items[$day]['file'] = $row['file'];
    $items[$day]['path'] = $row['path'];
    $items[$day]['tn_ext'] = @$row['tn_ext'];
    $items[$day]['width'] = $row['width'];
    $items[$day]['height'] = $row['height'];
    $items[$day]['dow'] = $row['dow'];
  }

  global $lang, $conf;

  if ( !empty($items)
      and $conf['calendar_month_cell_width']>0
      and $conf['calendar_month_cell_height']>0)
  {
    list($known_day) = array_keys($items);
    $known_dow = $items[$known_day]['dow'];
    $first_day_dow = ($known_dow-($known_day-1))%7;
    if ($first_day_dow<0)
    {
      $first_day_dow += 7;
    }
    //first_day_dow = week day corresponding to the first day of this month
    $wday_labels = $lang['day'];

    // BEGIN - pass now in week starting Monday
    if ($first_day_dow==0)
    {
      $first_day_dow = 6;
    }
    else
    {
      $first_day_dow -= 1;
    }
    array_push( $wday_labels, array_shift($wday_labels) );
    // END - pass now in week starting Monday

    $cell_width = $conf['calendar_month_cell_width'];
    $cell_height = $conf['calendar_month_cell_height'];

    $tpl_weeks    = array();
    $tpl_crt_week = array();

    //fill the empty days in the week before first day of this month
    for ($i=0; $i<$first_day_dow; $i++)
    {
      $tpl_crt_week[] = array();
    }

    for ( $day = 1;
          $day <= $this->get_all_days_in_month(
            $page['chronology_date'][CYEAR], $page['chronology_date'][CMONTH]
              );
          $day++)
    {
      $dow = ($first_day_dow + $day-1)%7;
      if ($dow==0 and $day!=1)
      {
        $tpl_weeks[]    = $tpl_crt_week; // add finished week to week list
        $tpl_crt_week   = array(); // start new week
      }

      if ( !isset($items[$day]) )
      {// empty day
        $tpl_crt_week[]   =
          array(
              'DAY' => $day
            );
      }
      else
      {
        $thumb = get_thumbnail_path($items[$day]);
        $tn_size = @getimagesize($thumb);

        $tn_width = $tn_size[0];
        $tn_height = $tn_size[1];

        // now need to fit the thumbnail of size tn_size within
        // a cell of size cell_size by playing with CSS position (left/top)
        // and the width and height of <img>.
        $ratio_w = $tn_width/$cell_width;
        $ratio_h = $tn_height/$cell_height;

        $pos_top=$pos_left=0;
        $css_style = '';

        if ( $ratio_w>1 and $ratio_h>1)
        {// cell completely smaller than the thumbnail so we will let the browser
         // resize the thumbnail
          if ($ratio_w > $ratio_h )
          {// thumbnail ratio compared to cell -> wide format
            $css_style = 'height:'.$cell_height.'px;';
            $browser_img_width = $cell_height*$tn_width/$tn_height;
            $pos_left = ($browser_img_width-$cell_width)/2;
          }
          else
          {
            $css_style = 'width:'.$cell_width.'px;';
            $browser_img_height = $cell_width*$tn_height/$tn_width;
            $pos_top = ($browser_img_height-$cell_height)/2;
          }
        }
        else
        {
          $pos_left = ($tn_width-$cell_width)/2;
          $pos_top = ($tn_height-$cell_height)/2;
        }

        if ( round($pos_left)!=0)
        {
          $css_style.='left:'.round(-$pos_left).'px;';
        }
        if ( round($pos_top)!=0)
        {
          $css_style.='top:'.round(-$pos_top).'px;';
        }
        $url = duplicate_index_url(
            array(
              'chronology_date' =>
                array(
                  $page['chronology_date'][CYEAR],
                  $page['chronology_date'][CMONTH],
                  $day
                )
            )
          );

        $tpl_crt_week[]   =
          array(
              'DAY'         => $day,
              'DOW'         => $dow,
              'NB_ELEMENTS' => $items[$day]['nb_images'],
              'IMAGE'       => $items[$day]['tn_url'],
              'U_IMG_LINK'  => $url,
              'IMAGE_STYLE' => $css_style,
              'IMAGE_ALT'   => $items[$day]['file'],
            );
      }
    }
    //fill the empty days in the week after the last day of this month
    while ( $dow<6 )
    {
      $tpl_crt_week[] = array();
      $dow++;
    }
    $tpl_weeks[]    = $tpl_crt_week;

    $tpl_var['month_view'] =
        array(
           'CELL_WIDTH'   => $cell_width,
           'CELL_HEIGHT' => $cell_height,
           'wday_labels' => $wday_labels,
           'weeks' => $tpl_weeks,
          );
  }

  return true;
}

}
?>
