<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2006-2007 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $Id: filter.inc.php 1651 2006-12-13 00:05:16Z rub $
// | last update   : $Date: 2006-12-13 01:05:16 +0100 (mer., 13 déc. 2006) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1651 $
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
// $filter['categories']: Computed data of filtered categories
// $filter['visible_categories']: List of visible categories (count(visible) < count(forbidden) more often)
// $filter['visible_images']: List of visible images


$filter['enabled'] =
  (in_array(basename($_SERVER['SCRIPT_FILENAME']), $conf['filter_pages'])) and
  (
    (isset($_GET['filter']) and ($_GET['filter'] == 'start')) or
    pwg_get_session_var('filter_enabled', false)
  );

if (in_array(basename($_SERVER['SCRIPT_FILENAME']), $conf['filter_pages']))
{
  if (isset($_GET['filter']))
  {
    $filter['enabled'] = ($_GET['filter'] == 'start');
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
    $filter['categories'] = get_computed_categories($user['id'], $user['forbidden_categories'], true, $user['recent_period']);
    $filter['visible_categories'] = implode(',', array_keys($filter['categories']));

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
      CURRENT_DATE,INTERVAL '.$user['recent_period'].' DAY)';

    $filter['visible_images'] = implode(',', array_from_query($query, 'image_id'));
  }
  else
  {
    // Read only data
    $filter['check_key'] = pwg_get_session_var('filter_check_key', '');
    $filter['categories'] = unserialize(pwg_get_session_var('filter_categories', serialize(array())));
    $filter['visible_categories'] = pwg_get_session_var('filter_visible_categories', '');
    $filter['visible_images'] = pwg_get_session_var('filter_visible_images', '');
  }

  $header_notes[] = l10n_dec('note_filter_day', 'note_filter_days', $user['recent_period']);
}
else
{
  $filter['check_key'] = '';
  $filter['categories'] = array();
  $filter['visible_categories'] = '';
  $filter['visible_images'] = '';
}

pwg_set_session_var('filter_enabled', $filter['enabled']);
pwg_set_session_var('filter_check_key', $filter['check_key']);
pwg_set_session_var('filter_categories', serialize($filter['categories']));
pwg_set_session_var('filter_visible_categories', $filter['visible_categories']);
pwg_set_session_var('filter_visible_images', $filter['visible_images']);

?>
