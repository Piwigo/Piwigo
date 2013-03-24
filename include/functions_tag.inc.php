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


/** returns the number of available tags for the connected user */
function get_nb_available_tags()
{
  global $user;
  if (!isset($user['nb_available_tags']))
  {
    $user['nb_available_tags'] = count(get_available_tags());
    single_update(USER_CACHE_TABLE, 
      array('nb_available_tags'=>$user['nb_available_tags']),
      array('user_id'=>$user['id'])
      );
  }
  return $user['nb_available_tags'];
}

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
  $query = '
SELECT tag_id, COUNT(DISTINCT(it.image_id)) AS counter
  FROM '.IMAGE_CATEGORY_TABLE.' ic
    INNER JOIN '.IMAGE_TAG_TABLE.' it ON ic.image_id=it.image_id'.get_sql_condition_FandF
    (
      array
        (
          'forbidden_categories' => 'category_id',
          'visible_categories' => 'category_id',
          'visible_images' => 'ic.image_id'
        ),
      '
  WHERE'
    ).'
  GROUP BY tag_id';
  $tag_counters = simple_hash_from_query($query, 'tag_id', 'counter');

  if ( empty($tag_counters) )
  {
    return array();
  }

  $query = '
SELECT *
  FROM '.TAGS_TABLE;
  $result = pwg_query($query);
  $tags = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $counter = intval(@$tag_counters[ $row['id'] ]);
    if ( $counter )
    {
      $row['counter'] = $counter;
      $row['name'] = trigger_event('render_tag_name', $row['name']);
      $tags[] = $row;
    }
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
SELECT *
  FROM '.TAGS_TABLE.'
;';
  $result = pwg_query($query);
  $tags = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['name'] = trigger_event('render_tag_name', $row['name']);
    $tags[] = $row;
  }

  usort($tags, 'tag_alpha_compare');

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
  foreach ($tags as &$tag)
  {
    $tag['level'] = 1;

    // based on threshold, determine current tag level
    for ($i = $conf['tags_levels'] - 1; $i >= 1; $i--)
    {
      if ($tag['counter'] > $threshold_of_level[$i])
      {
        $tag['level'] = $i + 1;
        break;
      }
    }
  }
  unset($tag);

  return $tags;
}

/**
 * return the list of image ids corresponding to given tags. AND & OR mode
 * supported.
 *
 * @param array tag ids
 * @param string mode
 * @param string extra_images_where_sql - optionally apply a sql where filter to retrieved images
 * @param string order_by - optionally overwrite default photo order
 * @return array
 */
function get_image_ids_for_tags($tag_ids, $mode='AND', $extra_images_where_sql='', $order_by='', $use_permissions=true)
{
  global $conf;
  if (empty($tag_ids))
  {
    return array();
  }

  $query = 'SELECT id
  FROM '.IMAGES_TABLE.' i ';

  if ($use_permissions)
  {
    $query.= '
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ic ON id=ic.image_id';
  }

  $query.= '
    INNER JOIN '.IMAGE_TAG_TABLE.' it ON id=it.image_id
    WHERE tag_id IN ('.implode(',', $tag_ids).')';

  if ($use_permissions)
  {
    $query.= get_sql_condition_FandF(
      array(
        'forbidden_categories' => 'category_id',
        'visible_categories' => 'category_id',
        'visible_images' => 'id'
        ),
      "\n  AND"
      );
  }

  $query.= (empty($extra_images_where_sql) ? '' : " \nAND (".$extra_images_where_sql.')').'
  GROUP BY id';
  
  if ($mode=='AND' and count($tag_ids)>1)
  {
    $query .= '
  HAVING COUNT(DISTINCT tag_id)='.count($tag_ids);
  }
  $query .= "\n".(empty($order_by) ? $conf['order_by'] : $order_by);

  return array_from_query($query, 'id');
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
SELECT t.*, count(*) AS counter
  FROM '.IMAGE_TAG_TABLE.'
    INNER JOIN '.TAGS_TABLE.' t ON tag_id = id
  WHERE image_id IN ('.implode(',', $items).')';
  if (!empty($excluded_tag_ids))
  {
    $query.='
    AND tag_id NOT IN ('.implode(',', $excluded_tag_ids).')';
  }
  $query .='
  GROUP BY t.id
  ORDER BY ';
  if ($max_tags>0)
  {
    $query .= 'counter DESC
  LIMIT '.$max_tags;
  }
  else
  {
    $query .= 'NULL';
  }

  $result = pwg_query($query);
  $tags = array();
  while($row = pwg_db_fetch_assoc($result))
  {
    $row['name'] = trigger_event('render_tag_name', $row['name']);
    $tags[] = $row;
  }
  usort($tags, 'tag_alpha_compare');
  return $tags;
}

/**
 * return a list of tags corresponding to any of ids, url_names, names
 *
 * @param array ids
 * @param array url_names
 * @param array names
 * @return array
 */
function find_tags($ids, $url_names=array(), $names=array() )
{
  $where_clauses = array();
  if ( !empty($ids) )
  {
    $where_clauses[] = 'id IN ('.implode(',', $ids).')';
  }
  if ( !empty($url_names) )
  {
    $where_clauses[] =
      'url_name IN ('.
      implode(
        ',',
        array_map(
          create_function('$s', 'return "\'".$s."\'";'),
          $url_names
          )
        )
      .')';
  }
  if ( !empty($names) )
  {
    $where_clauses[] =
      'name IN ('.
      implode(
        ',',
        array_map(
          create_function('$s', 'return "\'".$s."\'";'),
          $names
          )
        )
      .')';
  }
  if (empty($where_clauses))
  {
    return array();
  }

  $query = '
SELECT *
  FROM '.TAGS_TABLE.'
  WHERE '. implode( '
    OR ', $where_clauses);

  $result = pwg_query($query);
  $tags = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($tags, $row);
  }
  return $tags;
}
?>