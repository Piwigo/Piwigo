<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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
  global $page, $conf, $user, $template;

//------------------ initialize the condition on items to take into account ---
  $inner_sql = ' FROM ' . IMAGES_TABLE;

  if ($page['section']=='categories' or
      ( isset($page['category']) and is_numeric($page['category']) ) )
  { // we will regenerate the items by including subcats elements
    $page['cat_nb_images'] = 0;
    $page['items'] = array();
    $inner_sql .= '
INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';

    if (isset($page['category']) and is_numeric($page['category']))
    {
      $sub_ids = array_diff(
        get_subcat_ids(array($page['category'])),
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
  if ( !isset( $fields[ $page['chronology_field'] ] ) )
  {
    die('bad chronology field');
  }

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
  if (basename($_SERVER['SCRIPT_FILENAME']) != 'picture.php')
  {
    $template->assign_block_vars('calendar', array());

    if ($calendar->generate_category_content())
    {
      unset(
        $page['thumbnails_include'],
        $page['items'],
        $page['cat_nb_images']
        );

      $must_show_list = false;
    }

    $template->assign_block_vars( 'calendar.views', array() );
    foreach ($styles as $style => $style_data)
    {
      foreach ($views as $view)
      {
        if ( $style_data['view_calendar'] or $view != CAL_VIEW_CALENDAR)
        {
          $selected = '';

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
            $selected = 'SELECTED';
          }

          $template->assign_block_vars(
            'calendar.views.view',
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
    //this should be an assign_block_vars, but I need to assign 'calendar'
    //above and at that point I don't have the title yet.
    $template->_tpldata['calendar.'][0]['TITLE'] = $calendar_title;
  } // end category calling

  if ($must_show_list)
  {
    $query = 'SELECT DISTINCT(id)';
    $query .= $calendar->inner_sql.'
  '.$calendar->get_date_where();
    if ( isset($page['super_order_by']) )
    {
      $query .= '
  '.$conf['order_by'];
    }
    else
    {
      $order_by = str_replace(
        'ORDER BY ',
        'ORDER BY '.$calendar->date_field.' DESC,', $conf['order_by']
        );
      $query .= '
  '.$order_by;
    }

    $page['items']              = array_from_query($query, 'id');
    $page['cat_nb_images']      = count($page['items']);
    $page['thumbnails_include'] = 'include/category_default.inc.php';
  }
  pwg_debug('end initialize_calendar');
}

?>