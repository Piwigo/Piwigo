<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2006-03-16 23:58:16 +0100 (jeu, 16 mar 2006) $
// | last modifier : $Author: rub $
// | revision      : $Revision: 1085 $
// | revision      : $Revision: 1085 $
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
 * Tags available. Each return tag is represented as an array with its id,
 * its name, its weight (count), its url name. Tags are not sorted.
 *
 * The returned list can be a subset of all existing tags due to
 * permissions, only if a list of forbidden categories is provided
 *
 * @param array forbidden categories
 * @return array
 */
function get_available_tags()
{
  // we can find top fatter tags among reachable images
  $tags_query = '
SELECT tag_id, name, url_name, count(*) counter
  FROM '.IMAGE_TAG_TABLE.'
    INNER JOIN '.TAGS_TABLE.' ON tag_id = id';

  $where_tag_img =
    get_sql_condition_FandF
    (
      array
        (
          'forbidden_categories' => 'category_id',
          'visible_categories' => 'category_id',
          'visible_images' => 'image_id'
        ),
      'WHERE'
    );

  if (!is_null($where_tag_img))
  {
    // first we need all reachable image ids
    $images_query = '
SELECT DISTINCT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  '.$where_tag_img.'
;';
    $image_ids = array_from_query($images_query, 'image_id');
    if ( empty($image_ids) )
    {
      return array();
    }
    $tags_query.= '
  WHERE image_id IN ('.
      wordwrap(
        implode(', ', $image_ids),
        80,
        "\n"
        ).')';
  }

  $tags_query.= '
  GROUP BY tag_id
;';

  $result = pwg_query($tags_query);

  $tags = array();

  while ($row = mysql_fetch_array($result))
  {
    array_push($tags, $row);
  }

  return $tags;
}

/**
 * All tags, even tags associated to no image.
 *
 * @return array
 */
function get_all_tags()
{
  $query = '
SELECT id AS tag_id,
       name,
       url_name
  FROM '.TAGS_TABLE.'
;';
  $result = pwg_query($query);

  $tags = array();

  while ($row = mysql_fetch_array($result))
  {
    array_push($tags, $row);
  }

  usort($tags, 'name_compare');

  return $tags;
}

/**
 * Giving a set of tags with a counter for each one, calculate the display
 * level of each tag.
 *
 * The level of each tag depends on the average count of tags. This
 * calcylation method avoid having very different levels for tags having
 * nearly the same count when set are small.
 *
 * @param array tags
 * @return array
 */
function add_level_to_tags($tags)
{
  global $conf;

  if (count($tags) == 0)
  {
    return $tags;
  }

  $total_count = 0;

  foreach ($tags as $tag)
  {
    $total_count+= $tag['counter'];
  }

  // average count of available tags will determine the level of each tag
  $tag_average_count = $total_count / count($tags);

  // tag levels threshold calculation: a tag with an average rate must have
  // the middle level.
  for ($i = 1; $i < $conf['tags_levels']; $i++)
  {
    $threshold_of_level[$i] =
      2 * $i * $tag_average_count / $conf['tags_levels'];
  }

  // display sorted tags
  foreach (array_keys($tags) as $k)
  {
    $tags[$k]['level'] = 1;

    // based on threshold, determine current tag level
    for ($i = $conf['tags_levels'] - 1; $i >= 1; $i--)
    {
      if ($tags[$k]['counter'] > $threshold_of_level[$i])
      {
        $tags[$k]['level'] = $i + 1;
        break;
      }
    }
  }

  return $tags;
}

/**
 * return the list of image ids corresponding to given tags. AND & OR mode
 * supported.
 *
 * @param array tag ids
 * @param string mode
 * @return array
 */
function get_image_ids_for_tags($tag_ids, $mode = 'AND')
{
  switch ($mode)
  {
    case 'AND':
    {
      // strategy is to list images associated to each tag
      $tag_images = array();

      foreach ($tag_ids as $tag_id)
      {
        $query = '
SELECT image_id
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id = '.$tag_id.'
;';
        $tag_images[$tag_id] = array_from_query($query, 'image_id');
      }

      // then we calculate the intersection, the images that are associated to
      // every tags
      $items = array_shift($tag_images);
      foreach ($tag_images as $images)
      {
        $items = array_intersect($items, $images);
      }

      return array_unique($items);
      break;
    }
    case 'OR':
    {
      $query = '
SELECT DISTINCT image_id
  FROM '.IMAGE_TAG_TABLE.'
  WHERE tag_id IN ('.implode(',', $tag_ids).')
;';
      return array_from_query($query, 'image_id');
      break;
    }
    default:
    {
      die('get_image_ids_for_tags: unknown mode, only AND & OR are supported');
    }
  }
}

/**
 * return a list of tags corresponding to given items.
 *
 * @param array items
 * @param array max_tags
 * @param array excluded_tag_ids
 * @return array
 */
function get_common_tags($items, $max_tags, $excluded_tag_ids=null)
{
  if (empty($items))
  {
    return array();
  }
  $query = '
SELECT tag_id, name, url_name, count(*) counter
  FROM '.IMAGE_TAG_TABLE.'
    INNER JOIN '.TAGS_TABLE.' ON tag_id = id
  WHERE image_id IN ('.implode(',', $items).')';
  if (!empty($excluded_tag_ids))
  {
    $query.='
    AND tag_id NOT IN ('.implode(',', $excluded_tag_ids).')';
  }
  $query .='
  GROUP BY tag_id
  ORDER BY counter DESC';
  if ($max_tags>0)
  {
    $query .= '
  LIMIT 0,'.$max_tags;
  }

  $result = pwg_query($query);
  $tags = array();
  while($row = mysql_fetch_array($result))
  {
    array_push($tags, $row);
  }
  usort($tags, 'name_compare');
  return $tags;
}
?>