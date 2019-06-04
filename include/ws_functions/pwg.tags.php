<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Returns a list of tags
 * @param mixed[] $params
 *    @option bool sort_by_counter
 */
function ws_tags_getList($params, &$service)
{
  $tags = get_available_tags();
  if ($params['sort_by_counter'])
  {
    usort($tags, function($a, $b) {  return -$a["counter"]+$b["counter"]; });
  }
  else
  {
    usort($tags, 'tag_alpha_compare');
  }

  for ($i=0; $i<count($tags); $i++)
  {
    $tags[$i]['id'] = (int)$tags[$i]['id'];
    $tags[$i]['counter'] = (int)$tags[$i]['counter'];
    $tags[$i]['url'] = make_index_url(
      array(
        'section'=>'tags',
        'tags'=>array($tags[$i])
        )
      );
  }

  return array(
    'tags' => new PwgNamedArray(
      $tags,
      'tag',
      ws_std_get_tag_xml_attributes()
      )
    );
}

/**
 * API method
 * Returns the list of tags as you can see them in administration
 * @param mixed[] $params
 *
 * Only admin can run this method and permissions are not taken into
 * account.
 */
function ws_tags_getAdminList($params, &$service)
{
  return array(
    'tags' => new PwgNamedArray(
      get_all_tags(),
      'tag',
      ws_std_get_tag_xml_attributes()
      )
    );
}

/**
 * API method
 * Returns a list of images for tags
 * @param mixed[] $params
 *    @option int[] tag_id (optional)
 *    @option string[] tag_url_name (optional)
 *    @option string[] tag_name (optional)
 *    @option bool tag_mode_and
 *    @option int per_page
 *    @option int page
 *    @option string order
 */
function ws_tags_getImages($params, &$service)
{
  // first build all the tag_ids we are interested in
  $tags = find_tags($params['tag_id'], $params['tag_url_name'], $params['tag_name']);
  $tags_by_id = array();
  foreach ($tags as $tag)
  {
    $tags['id'] = (int)$tag['id'];
    $tags_by_id[ $tag['id'] ] = $tag;
  }
  unset($tags);
  $tag_ids = array_keys($tags_by_id);

  $where_clauses = ws_std_image_sql_filter($params);
  if (!empty($where_clauses))
  {
    $where_clauses = implode(' AND ', $where_clauses);
  }

  $order_by = ws_std_image_sql_order($params, 'i.');
  if (!empty($order_by))
  {
    $order_by = 'ORDER BY '.$order_by;
  }
  $image_ids = get_image_ids_for_tags(
    $tag_ids,
    $params['tag_mode_and'] ? 'AND' : 'OR',
    $where_clauses,
    $order_by
    );

  $count_set = count($image_ids);
  $image_ids = array_slice($image_ids, $params['per_page']*$params['page'], $params['per_page'] );

  $image_tag_map = array();
  // build list of image ids with associated tags per image
  if (!empty($image_ids) and !$params['tag_mode_and'])
  {
    $query = '
SELECT image_id, GROUP_CONCAT(tag_id) AS tag_ids
  FROM '. IMAGE_TAG_TABLE .'
  WHERE tag_id IN ('. implode(',', $tag_ids) .')
    AND image_id IN ('. implode(',', $image_ids) .')
  GROUP BY image_id
;';
    $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result))
    {
      $row['image_id'] = (int)$row['image_id'];
      $image_tag_map[ $row['image_id'] ] = explode(',', $row['tag_ids']);
    }
  }

  $images = array();
  if (!empty($image_ids))
  {
    $rank_of = array_flip($image_ids);

    $query = '
SELECT *
  FROM '. IMAGES_TABLE .'
  WHERE id IN ('. implode(',',$image_ids) .')
;';
    $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result))
    {
      $image = array();
      $image['rank'] = $rank_of[ $row['id'] ];

      foreach (array('id', 'width', 'height', 'hit') as $k)
      {
        if (isset($row[$k]))
        {
          $image[$k] = (int)$row[$k];
        }
      }
      foreach (array('file', 'name', 'comment', 'date_creation', 'date_available') as $k)
      {
        $image[$k] = $row[$k];
      }
      $image = array_merge( $image, ws_std_get_urls($row) );

      $image_tag_ids = ($params['tag_mode_and']) ? $tag_ids : $image_tag_map[$image['id']];
      $image_tags = array();
      foreach ($image_tag_ids as $tag_id)
      {
        $url = make_index_url(
          array(
            'section'=>'tags',
            'tags'=> array($tags_by_id[$tag_id])
            )
          );
        $page_url = make_picture_url(
          array(
            'section'=>'tags',
            'tags'=> array($tags_by_id[$tag_id]),
            'image_id' => $row['id'],
            'image_file' => $row['file'],
            )
          );
        $image_tags[] = array(
          'id' => (int)$tag_id,
          'url' => $url,
          'page_url' => $page_url,
          );
      }

      $image['tags'] = new PwgNamedArray($image_tags, 'tag', ws_std_get_tag_xml_attributes() );
      $images[] = $image;
    }

    usort($images, 'rank_compare');
    unset($rank_of);
  }

  return array(
    'paging' => new PwgNamedStruct(
      array(
        'page' => $params['page'],
        'per_page' => $params['per_page'],
        'count' => count($images),
        'total_count' => $count_set,
        )
      ),
    'images' => new PwgNamedArray(
      $images,
      'image',
      ws_std_get_image_xml_attributes()
      )
    );
}

/**
 * API method
 * Adds a tag
 * @param mixed[] $params
 *    @option string name
 */
function ws_tags_add($params, &$service)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  $creation_output = create_tag($params['name']);

  if (isset($creation_output['error']))
  {
    return new PwgError(500, $creation_output['error']);
  }

  return $creation_output;
}

?>
