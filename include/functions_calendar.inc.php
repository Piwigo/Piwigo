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

function get_calendar_parameter($options, &$parameters )
{
  if ( count($parameters) and isset($options[$parameters[0]]) )
  {
    return array_shift($parameters);
  }
  else
  {
    foreach ($options as $option => $data)
    {
       if ( empty( $data['default_link'] ) )
       {
         break;
       }
    }
    return $option;
  }
}

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
  // the parameters look like (FIELD)?(STYLE)?(VIEW)?(DATE COMPONENTS)?
  // FIELD = (created-|posted-)
  // STYLE = (m-|w-)
  // VIEW  = (l-|c-)
  // DATE COMPONENTS= YEAR(-MONTH/WEEK)?(-DAY)?

  $fields = array(
    // Created
    'created' => array(
      'default_link'   => 'created-',
      'label'          => l10n('Creation date'),
      'db_field'       => 'date_creation',
      ),
    // Posted
    'posted' => array(
      'default_link'   => 'posted-',
      'label'          => l10n('Post date'),
      'db_field'       => 'date_available',
      ),
    );

  $styles = array(
    // Monthly style
    'monthly' => array(
      'default_link'   => '',
      'include'        => 'calendar_monthly.class.php',
      'view_calendar'  => true,
      ),
    // Weekly style
    'weekly' => array(
      'default_link'   => 'weekly-',
      'include'        => 'calendar_weekly.class.php',
      'view_calendar'  => false,
      ),
    );

  $views = array(
    // list view
    CAL_VIEW_LIST => array(
      'default_link'   => '',
      ),
    // calendar view
    CAL_VIEW_CALENDAR => array(
      'default_link'   => CAL_VIEW_CALENDAR.'-',
      ),
    );

  $requested = explode('-', $_GET['calendar']);

  // Retrieve calendar field
  $cal_field = get_calendar_parameter($fields, $requested);

  // Retrieve style
  $cal_style = get_calendar_parameter($styles, $requested);
  include(PHPWG_ROOT_PATH.'include/'. $styles[$cal_style]['include']);
  $calendar = new Calendar();

  // Retrieve view
  $cal_view = get_calendar_parameter($views, $requested);
  if ( CAL_VIEW_CALENDAR==$cal_view and !$styles[$cal_style]['view_calendar'] )
  {
    $cal_view=CAL_VIEW_LIST;
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
      if ($cal_view == CAL_VIEW_CALENDAR)
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
    else
    {
      $requested[$i] = (int)$requested[$i];
    }
  }
  if ($any_count == 3)
  {
    array_pop($requested);
  }

  $calendar->initialize($fields[$cal_field]['db_field'], $inner_sql, $requested);

  //echo ('<pre>'. var_export($calendar, true) . '</pre>');

  $url_base = get_query_string_diff(array('start', 'calendar'));
  $url_base =
    PHPWG_ROOT_PATH.'category.php'
    .$url_base
    .(empty($url_base) ? '?' : '&')
    .'calendar='.$cal_field.'-'
    ;
  $must_show_list = true; // true until calendar generates its own display
  if (basename($_SERVER["PHP_SELF"]) == 'category.php')
  {
    $template->assign_block_vars('calendar', array());

    if ($calendar->generate_category_content(
          $url_base.$cal_style.'-'.$cal_view.'-',
          $cal_view
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

    $template->assign_block_vars( 'calendar.views', array() );
    foreach ($styles as $style => $style_data)
    {
      foreach ($views as $view => $view_data)
      {
        if ( $style_data['view_calendar'] or $view != CAL_VIEW_CALENDAR)
        {
          $selected = '';
          $url = $url_base.$style.'-'.$view;
          if ($style==$cal_style)
          {
            $url .= '-'.implode('-', $calendar->date_components);
            if ( $view==$cal_view )
            {
              $selected = 'SELECTED';
            }
          }
          else
          {
            if (isset($calendar->date_components[0]))
            {
              $url .= '-' . $calendar->date_components[0];
            }
          }
          $template->assign_block_vars(
            'calendar.views.view',
            array(
              'VALUE' => $url,
              'CONTENT' => l10n('calendar_'.$style.'_'.$view),
              'SELECTED' => $selected,
              )
            );
        }
      }
    }
    $calendar_title =
        '<a href="'.$url_base.$cal_style.'-'.$cal_view.'">'
        .$fields[$cal_field]['label'].'</a>';
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