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
          'sql'=> 'YEAR('.$this->date_field.')',
          'labels' => null
        ),
      array(
          'sql'=> 'MONTH('.$this->date_field.')',
          'labels' => $lang['month']
        ),
      array(
          'sql'=> 'DAYOFMONTH('.$this->date_field.')',
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

  $view_type = $page['chronology']['view'];
  if ($view_type==CAL_VIEW_CALENDAR)
  {
    if ( count($page['chronology_date'])==0 )
    {//case A: no year given - display all years+months
      if ($this->build_global_calendar())
        return true;
    }

    if ( count($page['chronology_date'])==1 )
    {//case B: year given - display all days in given year
      if ($this->build_year_calendar())
      {
        $this->build_nav_bar(CYEAR); // years
        return true;
      }
    }

    if ( count($page['chronology_date'])==2 )
    {//case C: year+month given - display a nice month calendar
      $this->build_month_calendar();
      //$this->build_nav_bar(CYEAR); // years
      //$this->build_nav_bar(CMONTH); // month
      $this->build_next_prev();
      return true;
    }
  }

  if ($view_type==CAL_VIEW_LIST or count($page['chronology_date'])==3)
  {
    $has_nav_bar = false;
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
      $b .= $date[CMONTH] . '-';
      $e .= $date[CMONTH] . '-';
      if (isset($date[CDAY]) and $date[CDAY]!=='any')
      {
        $b .= $date[CDAY];
        $e .= $date[CDAY];
      }
      else
      {
        $b .= '01';
        $e .= '31';
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

// returns an array with alll the days in a given month
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

function build_global_calendar()
{
  global $page;
  assert( count($page['chronology_date']) == 0 );
  $query='SELECT DISTINCT(DATE_FORMAT('.$this->date_field.',"%Y%m")) as period,
            COUNT( DISTINCT(id) ) as count';
  $query.= $this->inner_sql;
  $query.= $this->get_date_where();
  $query.= '
  GROUP BY period
  ORDER BY YEAR('.$this->date_field.') DESC';

  $result = pwg_query($query);
  $items=array();
  while ($row = mysql_fetch_array($result))
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

  global $lang, $template;
  foreach ( $items as $year=>$year_data)
  {
    $chronology_date = array( $year );
    $url = duplicate_index_url( array('chronology_date'=>$chronology_date) );

    $nav_bar = '<span class="calCalHead"><a href="'.$url.'">'.$year.'</a>';
    $nav_bar .= ' ('.$year_data['nb_images'].')';
    $nav_bar .= '</span><br>';

    $nav_bar .= $this->get_nav_bar_from_items( $chronology_date,
            $year_data['children'], null, 'calCal', false, false, $lang['month'] );

    $template->assign_block_vars( 'calendar.calbar',
         array( 'BAR' => $nav_bar)
         );
  }
  return true;
}

function build_year_calendar()
{
  global $page;
  assert( count($page['chronology_date']) == 1 );
  $query='SELECT DISTINCT(DATE_FORMAT('.$this->date_field.',"%m%d")) as period,
            COUNT( DISTINCT(id) ) as count';
  $query.= $this->inner_sql;
  $query.= $this->get_date_where();
  $query.= '
  GROUP BY period';

  $result = pwg_query($query);
  $items=array();
  while ($row = mysql_fetch_array($result))
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
  global $lang, $template;
  foreach ( $items as $month=>$month_data)
  {
    $chronology_date = array( $page['chronology_date'][CYEAR], $month );
    $url = duplicate_index_url( array('chronology_date'=>$chronology_date) );

    $nav_bar = '<span class="calCalHead"><a href="'.$url.'">';
    $nav_bar .= $lang['month'][$month].'</a>';
    $nav_bar .= ' ('.$month_data['nb_images'].')';
    $nav_bar .= '</span><br>';

    $nav_bar .= $this->get_nav_bar_from_items( $chronology_date,
                     $month_data['children'], null, 'calCal', false );

    $template->assign_block_vars( 'calendar.calbar',
         array( 'BAR' => $nav_bar)
         );
  }
  return true;

}

function build_month_calendar()
{
  global $page;
  $query='SELECT DISTINCT(DAYOFMONTH('.$this->date_field.')) as period,
            COUNT( DISTINCT(id) ) as count';
  $query.= $this->inner_sql;
  $query.= $this->get_date_where();
  $query.= '
  GROUP BY period';

  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $d = (int)$row['period'];
    $items[$d] = array('nb_images'=>$row['count']);
  }

  foreach ( $items as $day=>$data)
  {
    $page['chronology_date'][CDAY]=$day;
    $query = '
SELECT file,tn_ext,path, width, height, DAYOFWEEK('.$this->date_field.')-1 as dow';
    $query.= $this->inner_sql;
    $query.= $this->get_date_where();
    $query.= '
  ORDER BY RAND()
  LIMIT 0,1';
    unset ( $page['chronology_date'][CDAY] );

    $row = mysql_fetch_array(pwg_query($query));
    $items[$day]['tn_path'] = get_thumbnail_src($row['path'], @$row['tn_ext']);
    $items[$day]['tn_file'] = $row['file'];
    $items[$day]['width'] = $row['width'];
    $items[$day]['height'] = $row['height'];
    $items[$day]['dow'] = $row['dow'];
  }

  global $lang, $template, $conf;

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

    $template->set_filenames(
      array(
        'month_calendar'=>'month_calendar.tpl',
        )
      );

    $template->assign_block_vars('calendar.thumbnails',
        array(
           'WIDTH'=>$cell_width,
           'HEIGHT'=>$cell_height,
          )
      );

    //fill the heading with day names
    $template->assign_block_vars('calendar.thumbnails.head', array());
    foreach( $wday_labels as $d => $label)
    {
      $template->assign_block_vars('calendar.thumbnails.head.col',
                    array('LABEL'=>$label)
                  );
    }

    $template->assign_block_vars('calendar.thumbnails.row', array());

    //fill the empty days in the week before first day of this month
    for ($i=0; $i<$first_day_dow; $i++)
    {
      $template->assign_block_vars('calendar.thumbnails.row.col', array());
      $template->assign_block_vars('calendar.thumbnails.row.col.blank', array());
    }
    for ( $day = 1;
          $day <= $this->get_all_days_in_month(
            $page['chronology_date'][CYEAR], $page['chronology_date'][CMONTH]
              );
          $day++)
    {
      $dow = ($first_day_dow + $day-1)%7;
      if ($dow==0)
      {
        $template->assign_block_vars('calendar.thumbnails.row', array());
      }
      $template->assign_block_vars('calendar.thumbnails.row.col', array());
      if ( !isset($items[$day]) )
      {
        $template->assign_block_vars('calendar.thumbnails.row.col.empty',
              array('LABEL'=>$day));
      }
      else
      {
        // first try to guess thumbnail size
        if ( !empty($items[$day]['width']) )
        {
          $tn_size = get_picture_size(
                 $items[$day]['width'], $items[$day]['height'],
                 $conf['tn_width'], $conf['tn_height'] );
        }
        else
        {// item not an image (tn is either mime type or an image)
          $tn_size = @getimagesize($items[$day]['tn_path']);
        }
        $tn_width = $tn_size[0];
        $tn_height = $tn_size[1];

        // now need to fit the thumbnail of size tn_size within
        // a cell of size cell_size by playing with CSS position (left/top)
        // and the width and height of <img>.
        $ratio_w = $tn_width/$cell_width;
        $ratio_h = $tn_height/$cell_height;


        $pos_top=$pos_left=0;
        $img_width=$img_height='';
        if ( $ratio_w>1 and $ratio_h>1)
        {// cell completely smaller than the thumbnail so we will let the browser
         // resize the thumbnail
          if ($ratio_w > $ratio_h )
          {// thumbnail ratio compared to cell -> wide format
            $img_height = 'height="'.$cell_height.'"';
            $browser_img_width = $cell_height*$tn_width/$tn_height;
            $pos_left = ($browser_img_width-$cell_width)/2;
          }
          else
          {
            $img_width = 'width="'.$cell_width.'"';
            $browser_img_height = $cell_width*$tn_height/$tn_width;
            $pos_top = ($browser_img_height-$cell_height)/2;
          }
        }
        else
        {
          $pos_left = ($tn_width-$cell_width)/2;
          $pos_top = ($tn_height-$cell_height)/2;
        }

        $css_style = '';
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
        $alt = $wday_labels[$dow] . ' ' . $day.
               ' ('.$items[$day]['nb_images'].')';
        $template->assign_block_vars('calendar.thumbnails.row.col.full',
              array(
                'LABEL'     => $day,
                'IMAGE'     => $items[$day]['tn_path'],
                'U_IMG_LINK'=> $url,
                'STYLE'     => $css_style,
                'IMG_WIDTH' => $img_width,
                'IMG_HEIGHT'=> $img_height,
                'IMAGE_ALT' => $alt,
              )
            );
      }
    }
    //fill the empty days in the week after the last day of this month
    while ( $dow<6 )
    {
      $template->assign_block_vars('calendar.thumbnails.row.col', array());
      $template->assign_block_vars('calendar.thumbnails.row.col.blank', array());
      $dow++;
    }
    $template->assign_var_from_handle('MONTH_CALENDAR', 'month_calendar');
  }
  else
  {
    $template->assign_block_vars('thumbnails', array());
    $template->assign_block_vars('thumbnails.line', array());
    foreach ( $items as $day=>$data)
    {
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

      $thumbnail_title = $lang['day'][$data['dow']] . ' ' . $day;
      $name = $thumbnail_title .' ('.$data['nb_images'].')';

      $template->assign_block_vars(
          'thumbnails.line.thumbnail',
          array(
            'IMAGE'=>$data['tn_path'],
            'IMAGE_ALT'=>$data['tn_file'],
            'IMAGE_TITLE'=>$thumbnail_title,
            'U_IMG_LINK'=>$url
           )
          );
      $template->assign_block_vars(
          'thumbnails.line.thumbnail.category_name',
          array(
            'NAME' => $name
            )
          );
    }
  }

  return true;
}

}
?>