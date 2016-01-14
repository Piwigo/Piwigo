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

/**
 * @package functions\tag
 */


/**
 * Returns the number of available tags for the connected user.
 *
 * @return int
 */
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
 * Returns all available tags for the connected user (not sorted).
 * The returned list can be a subset of all existing tags due to permissions,
 * also tags with no images are not returned.
 *
 * @return array [id, name, counter, url_name]
 */
function get_available_tags()
{
  // we can find top fatter tags among reachable images
  $query = '
SELECT tag_id, COUNT(DISTINCT(it.image_id)) AS counter
  FROM '.IMAGE_CATEGORY_TABLE.' ic
    INNER JOIN '.IMAGE_TAG_TABLE.' it
    ON ic.image_id=it.image_id
  '.get_sql_condition_FandF(
    array(
      'forbidden_categories' => 'category_id',
      'visible_categories' => 'category_id',
      'visible_images' => 'ic.image_id'
      ),
    ' WHERE '
    ).'
  GROUP BY tag_id
;';
  $tag_counters = query2array($query, 'tag_id', 'counter');

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
      $row['name'] = trigger_change('render_tag_name', $row['name'], $row);
      $tags[] = $row;
    }
  }
  return $tags;
}

/**
 * Returns all tags even associated to no image.
 *
 * @return array [id, name, url_name]
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
    $row['name'] = trigger_change('render_tag_name', $row['name'], $row);
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
 * calculation method avoid having very different levels for tags having
 * nearly the same count when set are small.
 *
 * @param array $tags at least [id, counter]
 * @return array [..., level]
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
 * Return the list of image ids corresponding to given tags.
 * AND & OR mode supported.
 *
 * @param int[] $tag_ids
 * @param string mode
 * @param string $extra_images_where_sql - optionally apply a sql where filter to retrieved images
 * @param string $order_by - optionally overwrite default photo order
 * @param bool $user_permissions
 * @return array
 */
function get_image_ids_for_tags($tag_ids, $mode='AND', $extra_images_where_sql='', $order_by='', $use_permissions=true)
{
  global $conf;
  if (empty($tag_ids))
  {
    return array();
  }

  $query = '
SELECT id
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

  return query2array($query, null, 'id');
}

/**
 * Return a list of tags corresponding to given items.
 *
 * @param int[] $items
 * @param int $max_tags
 * @param int[] $excluded_tag_ids
 * @return array [id, name, counter, url_name]
 */
function get_common_tags($items, $max_tags, $excluded_tag_ids=array())
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
  { // TODO : why ORDER field is in the if ?
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
    $row['name'] = trigger_change('render_tag_name', $row['name'], $row);
    $tags[] = $row;
  }
  usort($tags, 'tag_alpha_compare');
  return $tags;
}

/**
 * Return a list of tags corresponding to any of ids, url_names or names.
 *
 * @param int[] $ids
 * @param string[] $url_names
 * @param string[] $names
 * @return array [id, name, url_name]
 */
function find_tags($ids=array(), $url_names=array(), $names=array() )
{
  $where_clauses = array();
  if (!empty($ids))
  {
    $where_clauses[] = 'id IN ('.implode(',', $ids).')';
  }
  if (!empty($url_names))
  {
    $where_clauses[] =
      'url_name IN (\''. implode('\', \'', $url_names) .'\')';
  }
  if (!empty($names))
  {
    $where_clauses[] =
      'name IN (\''. implode('\', \'', $names) .'\')';
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

  return query2array($query);
}

?>