<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
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


/**
 * Get a check key for filtered data
 * Check key are composed of elements witch force to compute data
 *
 * @param null
 * @return strinf check_key
 */
function get_filter_check_key()
{
  global $user, $filter;

  return $user['id'].$filter['recent_period'].date('Ymd');
}

/**
 * update data of categories with filtered values
 *
 * @param array list of categories
 * @return null
 */
function update_cats_with_filtered_data(&$cats)
{
  global $filter;

  if ($filter['enabled'])
  {
    $upd_fields = array('max_date_last', 'count_images', 'count_categories', 'nb_images');

    foreach ($cats as $cat_id => $category)
    {
      foreach ($upd_fields as $upd_field)
      {
        $cats[$cat_id][$upd_field] = $filter['categories'][$category['id']][$upd_field];
      }
    }
  }
}

?>
