<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

// +-----------------------------------------------------------------------+
// | UTILITIES                                                             |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Add image to the list
 * @param mixed[] $params
 *    @option int image_id
 */
function ws_favorites_add_image($params, $service)
{
  global $user, $conf;

  $query = '
      INSERT INTO '.FAVORITES_TABLE.'
        (image_id,user_id)
        VALUES
        ('.$params['image_id'].','.$user['id'].')
      ;';
  pwg_query($query);
}

/**
 * API method
 * Remove image from the list
 * @param mixed[] $params
 *    @option int image_id
 */
function ws_favorites_remove_image($params, $service)
{

  global $user, $conf;
  $query = '
      DELETE FROM '.FAVORITES_TABLE.'
         WHERE user_id = '.$user['id'].'
         AND image_id = '.$params['image_id'].'
         ;';
  pwg_query($query);
}

/**
 * API method
 * Returns all images on the list
 * @param mixed[] $params
 *    @option int per_page
 *    @option int page
 */
function ws_favorites_get_list($params, $service)
{
  global $user, $conf;

  $images = array();

  $query = '
  SELECT SQL_CALC_FOUND_ROWS image_id FROM '.FAVORITES_TABLE.'
    WHERE user_id = '.$user['id'].'
    ORDER BY image_id ASC
    LIMIT '. $params['per_page'] .'
    OFFSET '. ($params['per_page']*$params['page']) .'
  ;';

  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $image = array();
    foreach (array('image_id') as $k)
    {
      if (isset($row[$k]))
      {
        $image[$k] = (int)$row[$k];
      }
    }
    $images[] = $image;
  }

  list($total_images) = pwg_db_fetch_row(pwg_query('SELECT FOUND_ROWS()'));

    return array(
      'paging' => new PwgNamedStruct(
        array(
          'page' => $params['page'],
          'per_page' => $params['per_page'],
          'count' => count($images),
          'total_count' => $total_images
          )
        ),
      'images' => new PwgNamedArray(
        $images, 'image',
        ws_std_get_image_xml_attributes()
        )
      );
}

function ws_favorites_remove_all($params, $service)
{
  global $user, $conf;
  $query = '
      DELETE FROM '.FAVORITES_TABLE.'
         WHERE user_id = '.$user['id'].'
         ;';
  pwg_query($query);
}

?>