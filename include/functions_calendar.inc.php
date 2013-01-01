<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
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

define('CAL_VIEW_LIST',     'list');
define('CAL_VIEW_CALENDAR', 'calendar');

function initialize_calendar()
{
  global $page, $conf, $user, $template, $filter;

//------------------ initialize the condition on items to take into account ---
  $inner_sql = ' FROM ' . IMAGES_TABLE;

  if ($page['section']=='categories')
  { // we will regenerate the items by including subcats elements
    $page['items'] = array();
    $inner_sql .= '
INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';

    if ( isset($page['category']) )
    {
      $sub_ids = array_diff(
        get_subcat_ids(array($page['category']['id'])),
        explode(',', $user['forbidden_categories'])
        );

      if (empty($sub_ids))
      {
        return; // nothing to do
      }
      $inner_sql .= '
WHERE category_id IN ('.implode(',',$sub_ids).')';
      $inner_sql .= '
    '.get_sql_condition_FandF
      (
        array
          (
            'visible_images' => 'id'
          ),
        'AND', false
      );
    }
    else
    {
      $inner_sql .= '
    '.get_sql_condition_FandF
      (
        array
          (
            'forbidden_categories' => 'category_id',
            'visible_categories' => 'category_id',
            'visible_images' => 'id'
          ),
        'WHERE', true
      );
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

  $fields = array(
    // Created
    'created' => array(
      'label'          => l10n('Creation date'),
      ),
    // Posted
    'posted' => array(
      'label'          => l10n('Post date'),
      ),
    );

  $styles = array(
    // Monthly style
    'monthly' => array(
      'include'        => 'calendar_monthly.class.php',
      'view_calendar'  => true,
      ),
    // Weekly style
    'weekly' => array(
      'include'        => 'calendar_weekly.class.php',
      'view_calendar'  => false,
      ),
    );

  $views = array(CAL_VIEW_LIST,CAL_VIEW_CALENDAR);

  // Retrieve calendar field
  isset( $fields[ $page['chronology_field'] ] ) or fatal_error('bad chronology field');

  // Retrieve style
  if ( !isset( $styles[ $page['chronology_style'] ] ) )
  {
    $page['chronology_style'] = 'monthly';
  }
  $cal_style = $page['chronology_style'];
  include(PHPWG_ROOT_PATH.'include/'. $styles[$cal_style]['include']);
  $calendar = new Calendar();

  // Retrieve view

  if ( !isset($page['chronology_view']) or
       !in_array( $page['chronology_view'], $views ) )
  {
    $page['chronology_view'] = CAL_VIEW_LIST;
  }

  if ( CAL_VIEW_CALENDAR==$page['chronology_view'] and
        !$styles[$cal_style]['view_calendar'] )
  {

    $page['chronology_view'] = CAL_VIEW_LIST;
  }

  // perform a sanity check on $requested
  if (!isset($page['chronology_date']))
  {
    $page['chronology_date'] = array();
  }
  while ( count($page['chronology_date']) > 3)
  {
    array_pop($page['chronology_date']);
  }

  $any_count = 0;
  for ($i = 0; $i < count($page['chronology_date']); $i++)
  {
    if ($page['chronology_date'][$i] == 'any')
    {
      if ($page['chronology_view'] == CAL_VIEW_CALENDAR)
      {// we dont allow any in calendar view
        while ($i < count($page['chronology_date']))
        {
          array_pop($page['chronology_date']);
        }
        break;
      }
      $any_count++;
    }
    elseif ($page['chronology_date'][$i] == '')
    {
      while ($i < count($page['chronology_date']))
      {
        array_pop($page['chronology_date']);
      }
    }
    else
    {
      $page['chronology_date'][$i] = (int)$page['chronology_date'][$i];
    }
  }
  if ($any_count == 3)
  {
    array_pop($page['chronology_date']);
  }

  $calendar->initialize($inner_sql);

  //echo ('<pre>'. var_export($calendar, true) . '</pre>');

  $must_show_list = true; // true until calendar generates its own display
  if (script_basename() != 'picture') // basename without file extention
  {
    if ($calendar->generate_category_content())
    {
      $page['items'] = array();
      $must_show_list = false;
    }

    $page['comment'] = '';
    $template->assign('FILE_CHRONOLOGY_VIEW', 'month_calendar.tpl');

    foreach ($styles as $style => $style_data)
    {
      foreach ($views as $view)
      {
        if ( $style_data['view_calendar'] or $view != CAL_VIEW_CALENDAR)
        {
          $selected = false;

          if ($style!=$cal_style)
          {
            $chronology_date = array();
            if ( isset($page['chronology_date'][0]) )
            {
              array_push($chronology_date, $page['chronology_date'][0]);
            }
          }
          else
          {
            $chronology_date = $page['chronology_date'];
          }
          $url = duplicate_index_url(
              array(
                'chronology_style' => $style,
                'chronology_view' => $view,
                'chronology_date' => $chronology_date,
                )
             );

          if ($style==$cal_style and $view==$page['chronology_view'] )
          {
            $selected = true;
          }

          $template->append(
            'chronology_views',
            array(
              'VALUE' => $url,
              'CONTENT' => l10n('chronology_'.$style.'_'.$view),
              'SELECTED' => $selected,
              )
            );
        }
      }
    }
    $url = duplicate_index_url(
              array(), array('start', 'chronology_date')
            );
    $calendar_title = '<a href="'.$url.'">'
        .$fields[$page['chronology_field']]['label'].'</a>';
    $calendar_title.= $calendar->get_display_name();
    $template->assign('chronology',
        array(
          'TITLE' => $calendar_title
        )
      );
  } // end category calling

  if ($must_show_list)
  {
    $query = 'SELECT DISTINCT id ';
    $query .= ','.$calendar->date_field;
    $query .= $calendar->inner_sql.'
  '.$calendar->get_date_where();
    if ( isset($page['super_order_by']) )
    {
      $query .= '
  '.$conf['order_by'];
    }
    else
    {
      if ( count($page['chronology_date'])==0
           or in_array('any', $page['chronology_date']) )
      {// selected period is very big so we show newest first
        $order = ' DESC, ';
      }
      else
      {// selected period is small (month,week) so we show oldest first
        $order = ' ASC, ';
      }
      $order_by = str_replace(
        'ORDER BY ',
        'ORDER BY '.$calendar->date_field.$order, $conf['order_by']
        );
      $query .= '
  '.$order_by;
    }
    $page['items'] = array_from_query($query, 'id');
  }
  pwg_debug('end initialize_calendar');
}
?>