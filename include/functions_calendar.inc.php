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

define('CAL_VIEW_LIST',     'l');
define('CAL_VIEW_CALENDAR', 'c');

function initialize_calendar()
{
  global $page, $conf, $user, $template;

//------------------ initialize the condition on items to take into account ---
  $inner_sql = ' FROM ' . IMAGES_TABLE;
  
  if (!isset($page['cat']) or is_numeric($page['cat']))
  { // we will regenerate the items by including subcats elements
    $page['cat_nb_images'] = 0;
    $page['items'] = array();
    $inner_sql .= '
INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';
    
    if (isset($page['cat']) and is_numeric($page['cat']))
    {
      $sub_ids = array_diff(
        get_subcat_ids(array($page['cat'])),
        explode(',', $user['forbidden_categories'])
        );
      
      if (empty($sub_ids))
      {
        return; // nothing to do
      }
      $inner_sql .= '
WHERE category_id IN ('.implode(',',$sub_ids).')';
    }
    else
    {
      $inner_sql .= '
WHERE category_id NOT IN ('.$user['forbidden_categories'].')';
    }
  }
  else
  {
    if ( empty($page['items']) )
    {
      return; // nothing to do
    }
    $inner_sql .= '
WHERE id IN (' . implode(',',$page['items']) .')';
  }

//-------------------------------------- initialize the calendar parameters ---
  pwg_debug('start initialize_calendar');
  
  $cal_styles = array(
    // Weekly style
    array(
      'link'           => 'm',
      'default_link'   => '',
      'name'           => l10n('Monthly'),
      'include'        => 'calendar_monthly.class.php',
      'view_calendar'  => true,
      ),
    // Monthly style
    array(
      'link'           => 'w',
      'default_link'   => 'w-',
      'name'           => l10n('Weekly'),
      'include'        => 'calendar_weekly.class.php',
      ),
    );

  $requested = explode('-', $_GET['calendar']);
  $calendar = null;
  foreach ($cal_styles as $cal_style)
  {
    if ($requested[0] == $cal_style['link'])
    {
      include(PHPWG_ROOT_PATH.'include/'.$cal_style['include']);
      $calendar = new Calendar();
      array_shift($requested);
      break;
    }
  }
  
  if (!isset($calendar))
  {
    foreach($cal_styles as $cal_style)
    {
      if ('' == $cal_style['default_link'])
      {
        break;
      }
    }
    include( PHPWG_ROOT_PATH.'include/'.$cal_style['include']);
    $calendar = new Calendar();
  }

  $view_type = CAL_VIEW_LIST;
  if ($requested[0] == CAL_VIEW_LIST)
  {
    array_shift($requested);
  }
  elseif ($requested[0] == CAL_VIEW_CALENDAR)
  {
    if ($cal_style['view_calendar'])
    {
      $view_type = CAL_VIEW_CALENDAR;
    }
    array_shift($requested);
  }
  // perform a sanity check on $requested
  while (count($requested) > 3)
  {
    array_pop($requested);
  }

  $any_count = 0;
  for ($i = 0; $i < count($requested); $i++)
  {
    if ($requested[$i] == 'any')
    {
      if ($view_type == CAL_VIEW_CALENDAR)
      {// we dont allow any in calendar view
        while ($i < count($requested))
        {
          array_pop($requested);
        }
        break;
      }
      $any_count++;
    }
    elseif ($requested[$i] == '')
    {
      while ($i < count($requested))
      {
        array_pop($requested);
      }
    }
  }
  if ($any_count == 3)
  {
    array_pop($requested);
  }

  $calendar->initialize($conf['calendar_datefield'], $inner_sql);
  //echo ('<pre>'. var_export($requested, true) . '</pre>');
  //echo ('<pre>'. var_export($calendar, true) . '</pre>');

  // TODO: what makes the list view required?
  $must_show_list = true;
  
  if (basename($_SERVER["PHP_SELF"]) == 'category.php')
  {
    $template->assign_block_vars('calendar', array());

    $url_base =
      PHPWG_ROOT_PATH.'category.php'
      .get_query_string_diff(array('start', 'calendar'))
      .(empty($url_base) ? '?' : '&')
      .'calendar='
      ;

    if ($calendar->generate_category_content(
          $url_base.$cal_style['default_link'],
          $view_type,
          $requested
          )
       )
    {
      unset(
        $page['thumbnails_include'],
        $page['items'],
        $page['cat_nb_images']
        );
      
      $must_show_list = false;
    }

    if ($cal_style['view_calendar'])
    { // Build bar for views (List/Calendar)
      $views = array(
        // list view
        array(
          'type'  => CAL_VIEW_LIST,
          'label' => l10n('List')
          ),
        // calendar view
        array(
          'type'  => CAL_VIEW_CALENDAR,
          'label' => l10n('calendar')
          ),
        );
      
      $views_bar = '';

      foreach ($views as $view)
      {
        if ($view_type != $view['type'])
        {
          $views_bar.=
            '<a href="'
            .$url_base.$cal_style['default_link'].$view['type'].'-'
            .implode('-', $requested)
            .'">'.$view['label'].'</a> ';
        }
        else
        {
          $views_bar.= $view['label'].' ';
        }
        
        $views_bar.= ' ';
      }
      
      $template->assign_block_vars(
        'calendar.views',
        array(
          'BAR' => $views_bar,
          )
        );
    }

    // Build bar for calendar styles (Monthly, Weekly)
    $styles_bar = '';
    foreach ($cal_styles as $style)
    {
      if ($cal_style['link'] != $style['link'])
      {
        $url = $url_base.$style['default_link'];
        $url .= $view_type;
        if (isset($requested[0]))
        {
          $url .= '-' . $requested[0];
        }
        $styles_bar .= '<a href="'. $url . '">'.$style['name'].'</a> ';
      }
      else
      {
        $styles_bar .=  $style['name'].' ';
      }
    }
    $template->assign_block_vars(
      'calendar.styles',
      array(
        'BAR' => $styles_bar,
        )
      );
  } // end category calling

  if ($must_show_list)
  {
    $query = 'SELECT DISTINCT(id)';
    $query .= $calendar->inner_sql;
    $query .= $calendar->get_date_where($requested);
    if ( isset($page['super_order_by']) )
    {
      $query .= '
  '.$conf['order_by'];
    }
    else
    {
      $order_by = str_replace(
        'ORDER BY ',
        'ORDER BY '.$calendar->date_field.',', $conf['order_by']
        );
      $query .= $order_by;
    }

    $page['items']              = array_from_query($query, 'id');
    $page['cat_nb_images']      = count($page['items']);
    $page['thumbnails_include'] = 'include/category_default.inc.php';
  }
  pwg_debug('end initialize_calendar');
}

?>