<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-02-12 16:52:16 -0500 (Sun, 12 Feb 2006) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 1036 $
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

class BaseCalendarLevel
{
  function BaseCalendarLevel($sql, $allow_any=true, $labels=null)
  {
    $this->sql = $sql;
    $this->allow_any = $allow_any;
    $this->labels = $labels;
  }
  
  function sql()
  {
    return $this->sql;
  }
  function sql_equal($item)
  {
    return $this->sql.'='.$item;
  }
  function allow_any()
  {
    return $this->allow_any;
  }
  function get_label($item)
  {
    if ( isset($this->labels[$item]) )
    {
      return $this->labels[$item];
    }
    return $item;
  }

  var $sql;
  var $allow_any;
  var $labels;
}

class YearMonthCalendarLevel extends BaseCalendarLevel
{
  function YearMonthCalendarLevel()
  {
    global $conf;
    parent::BaseCalendarLevel('DATE_FORMAT('.$conf['calendar_datefield'].',"%Y%m")', false);
  }

  function sql_equal($item)
  {
    global $conf;
    $y = (int)($item/100);
    $m = (int)$item%100;
    // There seems to be much difference in performance between these:
    return $conf['calendar_datefield']." BETWEEN '$y-$m-01' AND '$y-$m-31'"; 
/*    return '(YEAR('.$conf['calendar_datefield'].')='.$y.' 
             AND MONTH('.$conf['calendar_datefield'].')='.$m.')';*/
//    return parent::sql_equal($item);
  }

  function get_label($item)
  {
    global $lang;
    if ( preg_match( '/(\d{4})(\d{2})/', $item, $matches) )
    {
      return $lang['month'][(int)$matches[2]].' '.$matches[1];
    }
    return $item;
  }
}

// just to optimize MySql query so that it uses the index
class YearCalendarLevel extends BaseCalendarLevel
{
  function YearCalendarLevel($sql, $allow_any=true)
  {
    parent::BaseCalendarLevel($sql, $allow_any);
  }

  function sql_equal($item)
  {
    global $conf;
    return $conf['calendar_datefield']." BETWEEN '$item-01-01' AND '$item-12-31'"; 
  }
}

/**
 * Parses $param and returns an array of calendar levels
 * @param requested array of requested items for each calendar level
 * @param cal_type is the requested calendar type
 */
function get_calendar_params($param, &$requested, &$cal_type)
{
  global $conf, $lang;
  $requested = explode('-', $param);
  $cal_struct = array();
  if ($requested[0]=='ywd')
  {
    array_push($cal_struct, new YearCalendarLevel( 
        'YEAR('.$conf['calendar_datefield'].')'  ) );
    array_push($cal_struct, new BaseCalendarLevel( 
        'WEEK('.$conf['calendar_datefield'].')+1' ) );
    array_push($cal_struct, new BaseCalendarLevel( 
        'DAYOFWEEK('.$conf['calendar_datefield'].')-1', true, $lang['day'] ) );
    $cal_type=array_shift($requested);
  }
  else if ($requested[0]=='md')
  {
    array_push($cal_struct, new YearMonthCalendarLevel() );
    array_push($cal_struct, new BaseCalendarLevel( 
        'DAY('.$conf['calendar_datefield'].')'  ) );
    $cal_type=array_shift($requested);
  }
  else
  {
    array_push($cal_struct, new YearCalendarLevel( 
        'YEAR('.$conf['calendar_datefield'].')'  ) );
    array_push($cal_struct, new BaseCalendarLevel( 
        'MONTH('.$conf['calendar_datefield'].')', true, $lang['month'] ) );
    array_push($cal_struct, new BaseCalendarLevel( 
        'DAY('.$conf['calendar_datefield'].')'  ) );

    if ($requested[0]=='ymd')
    {
      $cal_type=array_shift($requested);
    }
    else
    {
      $cal_type='';
    }
  }
  
  // perform a sanity check on $requested
  while (count($requested)>count($cal_struct))
  {
    array_pop($requested);
  }
  
  $any_count = 0;
  for ($i=0; $i<count($requested); $i++)
  {
    if ($requested[$i]=='any')
    {
      if (! $cal_struct[$i]->allow_any() )
      {
        while ($i<count($requested))
        {
          array_pop( $requested );
        }
        break;
      }
      $any_count++;
    }
    elseif ( empty($requested[$i]) )
    {
      while ($i<count($requested))
      {
        array_pop( $requested );
      }
    }
  }
  if ($any_count==count($cal_struct))
  {
    array_pop($requested);
  }
  return $cal_struct;
}



function initialize_calendar()
{
  global $page, $conf, $user, $template;

  if ( !isset($page['cat']) or is_numeric($page['cat']) )
  { // we will regenerate the items by including subcats elements
    $page['cat_nb_images']=0;
    $page['items']=array();
    if ( is_numeric($page['cat']) )
    {
      $sub_ids = get_subcat_ids(array($page['cat']));
      $sub_ids = array_diff($sub_ids, 
                            explode(',', $user['forbidden_categories']) );
      if (empty($sub_ids))
      {
        return; // nothing to do
      }
      $category_restriction .= ' IN ('.implode(',',$sub_ids).')';
    }
    else
    {
      $category_restriction = ' NOT IN ('.$user['forbidden_categories'].')';
    }
  }
  else
  {
    if ( empty($page['items']) )
    {
      return; // nothing to do
    }
  }

  pwg_debug('start initialize_calendar');
  
  $cal_struct = get_calendar_params($_GET['calendar'], $requested, $cal_type);

  //echo ('<pre>'. var_export($cal_struct, true) . '</pre>');
  //echo ('<pre>'. var_export($requested, true) . '</pre>');
  
  $category_calling = false;
  if (basename($_SERVER["PHP_SELF"]) == 'category.php')
  {
    $category_calling = true;
  }
  
  if ($category_calling)
  {
    $template->assign_block_vars('calendar', array());

    $url_base = get_query_string_diff(array('start','calendar'));
    $url_base .= empty($url_base) ? '?' : '&';
    $url_base .= 'calendar=';
    $url_base = PHPWG_ROOT_PATH.'category.php'.$url_base;
    
    // Build navigation bar for calendar styles
    $nav_bar = 'Styles: ';
    foreach ( array('ymd','md','ywd') as $type)
    {
      if ( $type==$cal_type or ($cal_type=='' and $type=='ymd') )
      {
        $nav_bar .= $type.' ';
      }
      else
      {
      	$nav_bar .= '<a href="'. $url_base.$type . '">'.$type.'</a> ';
      }
    }
    $template->assign_block_vars( 'calendar.navbar',  
           array( 'BAR' => $nav_bar) 
           );
           
    $url_base .= $cal_type;
    if ($cal_type!='')
    {
      $url_base .= '-';
    }
    
    
    $prev_level_query='
AND '.$conf['calendar_datefield'].' IS NOT NULL ';

    for ($i=0; $i<count($cal_struct); $i++)
    {
      $crt_cal_level = $cal_struct[$i];
      $query = '
SELECT DISTINCT('.$crt_cal_level->sql().') AS period, COUNT(id) as count
FROM '.IMAGES_TABLE;
      if ( isset($category_restriction) )
      {
        $query.= '
INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id 
WHERE category_id' . $category_restriction;
      }
      else
      {
        $query.= '
WHERE id IN (' . implode(',',$page['items']) .')';
      }
      $query.= $prev_level_query;
      $query.= '
GROUP BY period';

      $level_items=array();
      $result = pwg_query($query);
      $total_pics = 0;
      while ($row = mysql_fetch_array($result))
      {
        $level_items[$row['period']] = (int)$row['count'];
        $total_pics += $row['count'];
      }
      //echo ('<pre>'. var_export($level_items, true) . '</pre>');

      if ( $requested[$i] == 'any' and ! $crt_cal_level->allow_any() )
      {
        unset($requested[$i]);
      }
      
      // --- Build the navigation bar
      if ( $crt_cal_level->allow_any() )
      {
        $level_items['any'] = $total_pics;
      }
      $nav_bar='';
      foreach ($level_items as $item => $nb_images)
      {
        $label = $crt_cal_level->get_label($item);
        if ( $item==$requested[$i] )
        {
          $nav_bar .= ' <span class="dateSelected">';
          $nav_bar .= $label;
          $nav_bar.= '</span>';
        }
        else
        {
          $url = $url_base . $item;
          $nav_bar .= '<a href="'.$url.'">';
          $nav_bar .= $label;
          $nav_bar .= '</a>';
        }
        $nav_bar .= ' ';
      }
      $template->assign_block_vars( 'calendar.navbar',  
           array( 'BAR' => $nav_bar) 
           );

      if ( !isset($requested[$i]) )
        break;
      if ($requested[$i]!='any')
      {
        $prev_level_query.= ' AND '.$crt_cal_level->sql_equal($requested[$i]);
      }
      $url_base .= $requested[$i].'-';
    } // end for each calendar level


    if ( $i < count($cal_struct) )
    {
      $template->assign_block_vars('thumbnails', array());
      $template->assign_block_vars('thumbnails.line', array());
      foreach ($level_items as $level_item => $nb_pics)
      {
        if ($level_item=='any')
          continue;
        $query = '
SELECT file,tn_ext,'.$conf['calendar_datefield'].',path
FROM '.IMAGES_TABLE;
        if ( isset($category_restriction) )
        {
          $query.= '
INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id 
WHERE category_id' . $category_restriction;
        }
        else
        {
          $query.= '
WHERE id IN (' . implode(',',$page['items']) .')';
        }
        $query.= $prev_level_query;
        $query.= ' AND '.$crt_cal_level->sql_equal($level_item);
        $query.= '
ORDER BY RAND()
LIMIT 0,1';
        $row = mysql_fetch_array(pwg_query($query));
        
        $thumbnail_src = get_thumbnail_src($row['path'], @$row['tn_ext']);
        $thumbnail_title = $crt_cal_level->get_label($level_item);
        $name = $thumbnail_title .' ('.$nb_pics.')';
        
        $template->assign_block_vars(
          'thumbnails.line.thumbnail',
          array(
            'IMAGE'=>$thumbnail_src,
            'IMAGE_ALT'=>$row['file'],
            'IMAGE_TITLE'=>$thumbnail_title,
            'U_IMG_LINK'=>$url_base.$level_item
           )
         );
        $template->assign_block_vars(
          'thumbnails.line.thumbnail.category_name',
          array(
            'NAME' => $name
            )
          );
      }
      unset( $page['thumbnails_include'] ); // maybe move everything to a new include file ?
      pwg_debug('end initialize_calendar for thumbs');
      return;
    }
  }
  
  if (!$category_calling or $i==count($cal_struct) )
  {
    $query = 'SELECT DISTINCT(id) FROM '.IMAGES_TABLE;
    if ( isset($category_restriction) )
    {
      $query.= '
INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id 
WHERE category_id' . $category_restriction;
    }
    else
    {
      $query.= '
WHERE id IN ('.implode(',',$page['items']).')';
    }
    $query.= '
AND '.$conf['calendar_datefield'].' IS NOT NULL ';

    for ($i=0; $i<count($cal_struct); $i++)
    {
      assert( isset($requested[$i]) ); // otherwise we should not be here
      if ($requested[$i]!='any')
      {
      $query.= '
AND '. $cal_struct[$i]->sql_equal($requested[$i]);
      }
      
    }
    
    $page['items'] = array_from_query($query, 'id');
    $page['cat_nb_images'] = count($page['items']);
    $page['thumbnails_include'] = 'include/category_default.inc.php';
  }
  pwg_debug('end initialize_calendar');
}

?>