<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2006-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id$
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

// global variable for filter
$filter = array();

// $filter['enabled']: Filter is enabled
// $filter['check_key']: Check key to valitade computed filter data
// $filter['recent_period']: Recent period used to computed filter data
// $filter['categories']: Computed data of filtered categories
// $filter['visible_categories']:
//  List of visible categories (count(visible) < count(forbidden) more often)
// $filter['visible_images']: List of visible images

if (!get_filter_page_value('cancel'))
{
  if (isset($_GET['filter']))
  {
    $filter['matches'] = array();
    $filter['enabled'] = 
      preg_match('/^start-recent-(\d+)$/', $_GET['filter'], $filter['matches']) === 1;
  }
  else
  {
    $filter['enabled'] = pwg_get_session_var('filter_enabled', false);
  }
}
else
{
  $filter['enabled'] = false;
}

if ($filter['enabled'])
{
  if (isset($filter['matches']))
  {
    $filter['recent_period'] = $filter['matches'][1];
  }
  else
  {
    $filter['recent_period'] = pwg_get_session_var('filter_recent_period', $user['recent_period']);
  }

  if (
      // New filter
      !pwg_get_session_var('filter_enabled', false) or
      // Cache data updated
      $user['need_update_done'] or
      // Date, period, user are changed
      (pwg_get_session_var('filter_check_key', '') != get_filter_check_key())
    )
  {
    // Need to compute dats
    $filter['check_key'] = get_filter_check_key();
    $filter['categories'] = get_computed_categories($user['id'], $user['forbidden_categories'], true, $filter['recent_period']);

    $filter['visible_categories'] = implode(',', array_keys($filter['categories']));
    if (empty($filter['visible_categories']))
    {
      // Must be not empty
      $filter['visible_categories'] = -1;
    }

    $query ='
SELECT
  distinct image_id
FROM '.
  IMAGE_CATEGORY_TABLE.' INNER JOIN '.IMAGES_TABLE.' ON image_id = id
WHERE ';
    if (!empty($filter['visible_categories']))
    {
    $query.= '
  category_id  IN ('.$filter['visible_categories'].') and';
    }
  $query.= '
    date_available  > SUBDATE(
      CURRENT_DATE,INTERVAL '.$filter['recent_period'].' DAY)';

    $filter['visible_images'] = implode(',', array_from_query($query, 'image_id'));

    if (empty($filter['visible_images']))
    {
      // Must be not empty
      $filter['visible_images'] = -1;
    }

    // Save filter data on session
    pwg_set_session_var('filter_enabled', $filter['enabled']);
    pwg_set_session_var('filter_check_key', $filter['check_key']);
    pwg_set_session_var('filter_recent_period', $filter['recent_period']);
    pwg_set_session_var('filter_categories', serialize($filter['categories']));
    pwg_set_session_var('filter_visible_categories', $filter['visible_categories']);
    pwg_set_session_var('filter_visible_images', $filter['visible_images']);

  }
  else
  {
    // Read only data
    $filter['check_key'] = pwg_get_session_var('filter_check_key', '');
    $filter['categories'] = unserialize(pwg_get_session_var('filter_categories', serialize(array())));
    $filter['visible_categories'] = pwg_get_session_var('filter_visible_categories', '');
    $filter['visible_images'] = pwg_get_session_var('filter_visible_images', '');
  }

  if (get_filter_page_value('add_notes'))
  {
    $header_notes[] = l10n_dec('note_filter_day', 'note_filter_days', $filter['recent_period']);
  }
}
else
{
  if (pwg_get_session_var('filter_enabled', false))
  {
    pwg_unset_session_var('filter_enabled');
    pwg_unset_session_var('filter_check_key');
    pwg_unset_session_var('filter_recent_period');
    pwg_unset_session_var('filter_categories');
    pwg_unset_session_var('filter_visible_categories');
    pwg_unset_session_var('filter_visible_images');
  }
}


?>
