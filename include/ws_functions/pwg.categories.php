<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Returns images per category
 * @param mixed[] $params
 *    @option int[] cat_id (optional)
 *    @option bool recursive
 *    @option int per_page
 *    @option int page
 *    @option string order (optional)
 */
function ws_categories_getImages($params, &$service)
{
  global $user, $conf;

  $images = array();
  $image_ids = array();

  //------------------------------------------------- get the related categories
  $where_clauses = array();
  foreach ($params['cat_id'] as $cat_id)
  {
    if ($params['recursive'])
    {
      $where_clauses[] = 'uppercats '.DB_REGEX_OPERATOR.' \'(^|,)'.$cat_id.'(,|$)\'';
    }
    else
    {
      $where_clauses[] = 'id='.$cat_id;
    }
  }
  if (!empty($where_clauses))
  {
    $where_clauses = array('('. implode("\n    OR ", $where_clauses) . ')');
  }
  $where_clauses[] = get_sql_condition_FandF(
    array('forbidden_categories' => 'id'),
    null, true
    );

  $query = '
SELECT
    id,
    image_order
  FROM '. CATEGORIES_TABLE .'
  WHERE '. implode("\n    AND ", $where_clauses) .'
;';
  $result = pwg_query($query);

  $cats = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['id'] = (int)$row['id'];
    $cats[ $row['id'] ] = $row;
  }

  //-------------------------------------------------------- get the images
  if (!empty($cats))
  {
    $where_clauses = ws_std_image_sql_filter($params, 'i.');
    $where_clauses[] = 'category_id IN ('. implode(',', array_keys($cats)) .')';
    $where_clauses[] = get_sql_condition_FandF(
      array('visible_images' => 'i.id'),
      null, true
      );

    $order_by = ws_std_image_sql_order($params, 'i.');
    if ( empty($order_by)
          and count($params['cat_id'])==1
          and isset($cats[ $params['cat_id'][0] ]['image_order'])
        )
    {
      $order_by = $cats[ $params['cat_id'][0] ]['image_order'];
    }
    $order_by = empty($order_by) ? $conf['order_by'] : 'ORDER BY '.$order_by;
    $favorite_ids = get_user_favorites();

    $query = '
SELECT SQL_CALC_FOUND_ROWS i.*
  FROM '. IMAGES_TABLE .' i
    INNER JOIN '. IMAGE_CATEGORY_TABLE .' ON i.id=image_id
  WHERE '. implode("\n    AND ", $where_clauses) .'
  GROUP BY i.id
  '. $order_by .'
  LIMIT '. $params['per_page'] .'
  OFFSET '. ($params['per_page']*$params['page']) .'
;';
    $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result))
    {
      $image_ids[] = $row['id'];

      $image = array();
      $image['is_favorite'] = isset($favorite_ids[ $row['id'] ]);
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
      $image = array_merge($image, ws_std_get_urls($row));

      $images[] = $image;
    }

    list($total_images) = pwg_db_fetch_row(pwg_query('SELECT FOUND_ROWS()'));

    // let's take care of adding the related albums to each photo
    if (count($image_ids) > 0)
    {
      $category_ids = array();

      // find the complete list (given permissions) of albums linked to photos
      $query = '
SELECT
    image_id,
    category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id IN ('.implode(',', $image_ids).')
    AND '.get_sql_condition_FandF(array('forbidden_categories' => 'category_id'), null, true).'
;';
      $result = pwg_query($query);
      while ($row = pwg_db_fetch_assoc($result))
      {
        $category_ids[] = $row['category_id'];
        @$categories_of_image[ $row['image_id'] ][] = $row['category_id'];
      }

      if (count($category_ids) > 0)
      {
        // find details (for URL generation) about each album
        $query = '
SELECT
    id,
    name,
    permalink
  FROM '. CATEGORIES_TABLE .'
  WHERE id IN ('. implode(',', $category_ids) .')
;';
        $details_for_category = query2array($query, 'id');
      }

      foreach ($images as $idx => $image)
      {
        $image_cats = array();

        // it should not be possible at this point, but let's consider a photo can be in no album
        if (!isset($categories_of_image[ $image['id'] ]))
        {
          continue;
        }

        foreach ($categories_of_image[ $image['id'] ] as $cat_id)
        {
          $url = make_index_url(array('category' => $details_for_category[$cat_id]));

          $page_url = make_picture_url(
            array(
              'category' => $details_for_category[$cat_id],
              'image_id' => $image['id'],
              'image_file' => $image['file'],
            )
          );

          $image_cats[] = array(
            'id' => (int)$cat_id,
            'url' => $url,
            'page_url' => $page_url,
          );
        }

        $images[$idx]['categories'] = new PwgNamedArray(
          $image_cats,
          'category',
          array('id', 'url', 'page_url')
        );
      }
    }
  }

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

/**
 * API method
 * Returns a list of categories
 * @param mixed[] $params
 *    @option int cat_id (optional)
 *    @option bool recursive
 *    @option bool public
 *    @option bool tree_output
 *    @option bool fullname
 */
function ws_categories_getList($params, &$service)
{
  global $user, $conf;

  if (!in_array($params['thumbnail_size'], array_keys(ImageStdParams::get_defined_type_map())))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, "Invalid thumbnail_size");
  }

  $where = array('1=1');
  $join_type = 'INNER';
  $join_user = $user['id'];

  if (!$params['recursive'])
  {
    if ($params['cat_id']>0)
    {
      $where[] = '(
        id_uppercat = '. (int)($params['cat_id']) .'
        OR id='.(int)($params['cat_id']).'
      )';
    }
    else
    {
      $where[] = 'id_uppercat IS NULL';
    }
  }
  elseif ($params['cat_id']>0)
  {
    $where[] = 'uppercats '. DB_REGEX_OPERATOR .' \'(^|,)'.
      (int)($params['cat_id']) .'(,|$)\'';
  }

  if ($params['public'])
  {
    $where[] = 'status = "public"';
    $where[] = 'visible = "true"';

    $join_user = $conf['guest_id'];
  }
  elseif (is_admin())
  {
    // in this very specific case, we don't want to hide empty
    // categories. Function calculate_permissions will only return
    // categories that are either locked or private and not permitted
    //
    // calculate_permissions does not consider empty categories as forbidden
    $forbidden_categories = calculate_permissions($user['id'], $user['status']);
    $where[]= 'id NOT IN ('.$forbidden_categories.')';
    $join_type = 'LEFT';
  }

  $query = '
SELECT
    id, name, comment, permalink, status,
    uppercats, global_rank, id_uppercat,
    nb_images, count_images AS total_nb_images,
    representative_picture_id, user_representative_picture_id, count_images, count_categories,
    date_last, max_date_last, count_categories AS nb_categories
  FROM '. CATEGORIES_TABLE .'
    '.$join_type.' JOIN '. USER_CACHE_CATEGORIES_TABLE .'
    ON id=cat_id AND user_id='.$join_user.'
  WHERE '. implode("\n    AND ", $where) .'
;';
  $result = pwg_query($query);

  // management of the album thumbnail -- starts here
  $image_ids = array();
  $categories = array();
  $user_representative_updates_for = array();
  // management of the album thumbnail -- stops here

  $cats = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['url'] = make_index_url(
      array(
        'category' => $row
        )
      );
    foreach (array('id','nb_images','total_nb_images','nb_categories') as $key)
    {
      $row[$key] = (int)$row[$key];
    }

    if ($params['fullname'])
    {
      $row['name'] = strip_tags(get_cat_display_name_cache($row['uppercats'], null));
    }
    else
    {
      $row['name'] = strip_tags(
        trigger_change(
          'render_category_name',
          $row['name'],
          'ws_categories_getList'
          )
        );
    }

    $row['comment'] = strip_tags(
      (string) trigger_change(
        'render_category_description',
        $row['comment'],
        'ws_categories_getList'
        )
      );

    // management of the album thumbnail -- starts here
    //
    // on branch 2.3, the algorithm is duplicated from
    // include/category_cats, but we should use a common code for Piwigo 2.4
    //
    // warning : if the API method is called with $params['public'], the
    // album thumbnail may be not accurate. The thumbnail can be viewed by
    // the connected user, but maybe not by the guest. Changing the
    // filtering method would be too complicated for now. We will simply
    // avoid to persist the user_representative_picture_id in the database
    // if $params['public']
    if (!empty($row['user_representative_picture_id']))
    {
      $image_id = $row['user_representative_picture_id'];
    }
    elseif (!empty($row['representative_picture_id']))
    { // if a representative picture is set, it has priority
      $image_id = $row['representative_picture_id'];
    }
    elseif ($conf['allow_random_representative'])
    {
      // searching a random representant among elements in sub-categories
      $image_id = get_random_image_in_category($row);
    }
    else
    { // searching a random representant among representant of sub-categories
      if ($row['count_categories']>0 and $row['count_images']>0)
      {
        $query = '
SELECT representative_picture_id
  FROM '. CATEGORIES_TABLE .'
    INNER JOIN '. USER_CACHE_CATEGORIES_TABLE .'
    ON id=cat_id AND user_id='.$user['id'].'
  WHERE uppercats LIKE \''.$row['uppercats'].',%\'
    AND representative_picture_id IS NOT NULL
        '.get_sql_condition_FandF(
          array('visible_categories' => 'id'),
          "\n  AND"
          ).'
  ORDER BY '. DB_RANDOM_FUNCTION .'()
  LIMIT 1
;';
        $subresult = pwg_query($query);

        if (pwg_db_num_rows($subresult) > 0)
        {
          list($image_id) = pwg_db_fetch_row($subresult);
        }
      }
    }

    if (isset($image_id))
    {
      if ($conf['representative_cache_on_subcats'] and $row['user_representative_picture_id'] != $image_id)
      {
        $user_representative_updates_for[ $row['id'] ] = $image_id;
      }

      $row['representative_picture_id'] = $image_id;
      $image_ids[] = $image_id;
      $categories[] = $row;
    }
    unset($image_id);
    // management of the album thumbnail -- stops here

    $cats[] = $row;
  }
  usort($cats, 'global_rank_compare');

  // management of the album thumbnail -- starts here
  if (count($categories) > 0)
  {
    $thumbnail_src_of = array();
    $new_image_ids = array();

    $query = '
SELECT id, path, representative_ext, level
  FROM '. IMAGES_TABLE .'
  WHERE id IN ('. implode(',', $image_ids) .')
;';
    $result = pwg_query($query);

    while ($row = pwg_db_fetch_assoc($result))
    {
      if ($row['level'] <= $user['level'])
      {
        $thumbnail_src_of[$row['id']] = DerivativeImage::url($params['thumbnail_size'], $row);
      }
      else
      {
        // problem: we must not display the thumbnail of a photo which has a
        // higher privacy level than user privacy level
        //
        // * what is the represented category?
        // * find a random photo matching user permissions
        // * register it at user_representative_picture_id
        // * set it as the representative_picture_id for the category
        foreach ($categories as &$category)
        {
          if ($row['id'] == $category['representative_picture_id'])
          {
            // searching a random representant among elements in sub-categories
            $image_id = get_random_image_in_category($category);

            if (isset($image_id) and !in_array($image_id, $image_ids))
            {
              $new_image_ids[] = $image_id;
            }
            if ($conf['representative_cache_on_level'])
            {
              $user_representative_updates_for[ $category['id'] ] = $image_id;
            }

            $category['representative_picture_id'] = $image_id;
          }
        }
        unset($category);
      }
    }

    if (count($new_image_ids) > 0)
    {
      $query = '
SELECT id, path, representative_ext
  FROM '. IMAGES_TABLE .'
  WHERE id IN ('. implode(',', $new_image_ids) .')
;';
      $result = pwg_query($query);

      while ($row = pwg_db_fetch_assoc($result))
      {
        $thumbnail_src_of[ $row['id'] ] = DerivativeImage::url($params['thumbnail_size'], $row);
      }
    }
  }

  // compared to code in include/category_cats, we only persist the new
  // user_representative if we have used $user['id'] and not the guest id,
  // or else the real guest may see thumbnail that he should not
  if (!$params['public'] and count($user_representative_updates_for))
  {
    $updates = array();

    foreach ($user_representative_updates_for as $cat_id => $image_id)
    {
      $updates[] = array(
        'user_id' => $user['id'],
        'cat_id' => $cat_id,
        'user_representative_picture_id' => $image_id,
        );
    }

    mass_updates(
      USER_CACHE_CATEGORIES_TABLE,
      array(
        'primary' => array('user_id', 'cat_id'),
        'update'  => array('user_representative_picture_id')
        ),
      $updates
      );
  }

  foreach ($cats as &$cat)
  {
    foreach ($categories as $category)
    {
      if ($category['id'] == $cat['id'] and isset($category['representative_picture_id']))
      {
        $cat['tn_url'] = $thumbnail_src_of[$category['representative_picture_id']];
      }
    }
    // we don't want them in the output
    unset($cat['user_representative_picture_id'], $cat['count_images'], $cat['count_categories']);
  }
  unset($cat);
  // management of the album thumbnail -- stops here

  if ($params['tree_output'])
  {
    return categories_flatlist_to_tree($cats);
  }

  return array(
    'categories' => new PwgNamedArray(
      $cats,
      'category',
      ws_std_get_category_xml_attributes()
      )
    );
}

/**
 * API method
 * Returns the list of categories as you can see them in administration
 * @param mixed[] $params
 *
 * Only admin can run this method and permissions are not taken into
 * account.
 */
function ws_categories_getAdminList($params, &$service)
{

  global $conf;

  if (!isset($params['additional_output'])) {
    $params['additional_output'] = "";
  }
  $params['additional_output'] = array_map('trim', explode(',', $params['additional_output']));

  $query = '
SELECT category_id, COUNT(*) AS counter
  FROM '. IMAGE_CATEGORY_TABLE .'
  GROUP BY category_id
;';
  $nb_images_of = query2array($query, 'category_id', 'counter');

  // pwg_db_real_escape_string

  $query = '
SELECT SQL_CALC_FOUND_ROWS id, name, comment, uppercats, global_rank, dir, status
  FROM '. CATEGORIES_TABLE;

  if (isset($params["search"]) and $params['search'] != "") 
  {
    $query .= '
  WHERE name LIKE \'%'.pwg_db_real_escape_string($params["search"]).'%\'
  LIMIT '.$conf["linked_album_search_limit"];
  }

  $query .= '
;';
  $result = pwg_query($query);

  list($counter) = pwg_db_fetch_row(pwg_query('SELECT FOUND_ROWS()'));

  $cats = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    $id = $row['id'];
    $row['nb_images'] = isset($nb_images_of[$id]) ? $nb_images_of[$id] : 0;

    $cat_display_name = get_cat_display_name_cache(
      $row['uppercats'],
      'admin.php?page=album-'
    );

    $row['name'] = strip_tags(
      trigger_change(
        'render_category_name',
        $row['name'],
        'ws_categories_getAdminList'
        )
      );
    $row['fullname'] = strip_tags($cat_display_name);
    isset($row['comment']) ? false : $row['comment'] = "";
    $row['comment'] = strip_tags(
      trigger_change(
        'render_category_description',
        $row['comment'],
        'ws_categories_getAdminList'
        )
      );

    if (in_array('full_name_with_admin_links', $params['additional_output']))
    {
      $row["full_name_with_admin_links"] = $cat_display_name;
    }

    $cats[] = $row;
  }

  $limit_reached = false;
  if ($counter > $conf["linked_album_search_limit"]) {
    $limit_reached = true;
  }

  usort($cats, 'global_rank_compare');
  return array(
    'categories' => new PwgNamedArray(
      $cats,
      'category',
      array('id', 'nb_images', 'name', 'uppercats', 'global_rank', 'status', 'test')
    ),
    'limit' => $conf["linked_album_search_limit"],
    'limit_reached' => $limit_reached,
    );
}

/**
 * API method
 * Adds a category
 * @param mixed[] $params
 *    @option string name
 *    @option int parent (optional)
 *    @option string comment (optional)
 *    @option bool visible
 *    @option string status (optional)
 *    @option bool commentable
 */
function ws_categories_add($params, &$service)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

  global $conf;

  if (!empty($params['position']) and in_array($params['position'], array('first','last')))
  {
    //TODO make persistent with user prefs
    $conf['newcat_default_position'] = $params["position"];
  }

  $options = array();
  if (!empty($params['status']) and in_array($params['status'], array('private','public')))
  {
    $options['status'] = $params['status'];
  }

  if (!empty($params['comment']))
  {
    // TODO do not strip tags if pwg_token is provided (and valid)
    $options['comment'] = strip_tags($params['comment']);
  }
  
  $creation_output = create_virtual_category(
    strip_tags($params['name']), // TODO do not strip tags if pwg_token is provided (and valid)
    $params['parent'],
    $options
    );

  if (isset($creation_output['error']))
  {
    return new PwgError(500, $creation_output['error']);
  }

  invalidate_user_cache();

  return $creation_output;
}

/**
 * API method
 * Set the rank of a category
 * @param mixed[] $params
 *    @option int cat_id
 *    @option int rank
 */
function ws_categories_setRank($params, &$service)
{
  // does the category really exist?
  $query = '
SELECT id, id_uppercat, `rank`
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',',$params['category_id']).')
;';
  $categories = query2array($query);

  if (count($categories) == 0)
  {
    return new PwgError(404, 'category_id not found');
  }

  $category = $categories[0];

  //check the number of category given by the user
  if(count($params['category_id']) > 1)
  {
    $order_new = $params['category_id'];
    $order_new_by_id = $order_new;
    sort($order_new_by_id, SORT_NUMERIC);

    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.(empty($category['id_uppercat']) ? "IS NULL" : "= ".$category['id_uppercat']).'
  ORDER BY `id` ASC
;';

    $cat_asc = query2array($query, null, 'id');

    if(strcmp(implode(',',$cat_asc), implode(',',$order_new_by_id)) !==0)
    {
      return new PwgError(WS_ERR_INVALID_PARAM, 'you need to provide all sub-category ids for a given category');
    }
  }
  else
  {
    $params['category_id'] = implode($params['category_id']);

    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.(empty($category['id_uppercat']) ? "IS NULL" : "= ".$category['id_uppercat']).'
    AND id != '.$params['category_id'].'
  ORDER BY `rank` ASC
;';

    $order_old = query2array($query, null, 'id');
    $order_new = array();
    $was_inserted = false;
    $i = 1;
    foreach ($order_old as $category_id)
    {
      if($i == $params['rank'])
      {
        $order_new[] = $params['category_id'];
        $was_inserted = true;
      }
      $order_new[] = $category_id;
      ++$i;
    }

    if (!$was_inserted)
    {
      $order_new[] = $params['category_id'];
    }
  }
  // include function to set the global rank
  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  save_categories_order($order_new);
}

/**
 * API method
 * Sets details of a category
 * @param mixed[] $params
 *    @option int cat_id
 *    @option string name (optional)
 *    @option string comment (optional)
 */
function ws_categories_setInfo($params, &$service)
{
  // does the category really exist?
  $query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$params['category_id'].'
;';
  $categories = query2array($query);
  if (count($categories) == 0)
  {
    return new PwgError(404, 'category_id not found');
  }

  $category = $categories[0];

  if (!empty($params['status']))
  {
    if (!in_array($params['status'], array('private','public')))
    {
      return new PwgError(WS_ERR_INVALID_PARAM, "Invalid status, only public/private");
    }

    if ($params['status'] != $category['status'])
    {
      include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
      set_cat_status(array($params['category_id']), $params['status']);
    }
  }

  $update = array(
    'id' => $params['category_id'],
    );

  $info_columns = array('name', 'comment',);

  $perform_update = false;
  foreach ($info_columns as $key)
  {
    if (isset($params[$key]))
    {
      $perform_update = true;
      // TODO do not strip tags if pwg_token is provided (and valid)
      $update[$key] = strip_tags($params[$key]);
    }
  }

  if ($perform_update)
  {
    single_update(
      CATEGORIES_TABLE,
      $update,
      array('id' => $update['id'])
      );
  }

  pwg_activity('album', $params['category_id'], 'edit', array('fields' => implode(',', array_keys($update))));
}

/**
 * API method
 * Sets representative image of a category
 * @param mixed[] $params
 *    @option int category_id
 *    @option int image_id
 */
function ws_categories_setRepresentative($params, &$service)
{
  // does the category really exist?
  $query = '
SELECT COUNT(*)
  FROM '. CATEGORIES_TABLE .'
  WHERE id = '. $params['category_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(404, 'category_id not found');
  }

  // does the image really exist?
  $query = '
SELECT COUNT(*)
  FROM '. IMAGES_TABLE .'
  WHERE id = '. $params['image_id'] .'
;';
  list($count) = pwg_db_fetch_row(pwg_query($query));
  if ($count == 0)
  {
    return new PwgError(404, 'image_id not found');
  }

  // apply change
  $query = '
UPDATE '. CATEGORIES_TABLE .'
  SET representative_picture_id = '. $params['image_id'] .'
  WHERE id = '. $params['category_id'] .'
;';
  pwg_query($query);

  $query = '
UPDATE '. USER_CACHE_CATEGORIES_TABLE .'
  SET user_representative_picture_id = NULL
  WHERE cat_id = '. $params['category_id'] .'
;';
  pwg_query($query);

  pwg_activity('album', $params['category_id'], 'edit', array('image_id'=>$params['image_id']));
}

/**
 * API method
 *
 * Deletes the album thumbnail. Only possible if
 * $conf['allow_random_representative'] or if the album has no direct photos.
 *
 * @param mixed[] $params
 *    @option int category_id
 */
function ws_categories_deleteRepresentative($params, &$service)
{
  global $conf;
  
  // does the category really exist?
  $query = '
SELECT id
  FROM '. CATEGORIES_TABLE .'
  WHERE id = '. $params['category_id'] .'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) == 0)
  {
    return new PwgError(404, 'category_id not found');
  }

  $query = '
SELECT COUNT(*)
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$params['category_id'].'
;';
  list($nb_images) = pwg_db_fetch_row(pwg_query($query));

  if (!$conf['allow_random_representative'] and $nb_images != 0)
  {
    return new PwgError(401, 'not permitted');
  }

  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id = '.$params['category_id'].'
;';
  pwg_query($query);

  pwg_activity('album', $params['category_id'], 'edit');
}

/**
 * API method
 *
 * Find a new album thumbnail.
 *
 * @param mixed[] $params
 *    @option int category_id
 */
function ws_categories_refreshRepresentative($params, &$service)
{
  global $conf;
  
  // does the category really exist?
  $query = '
SELECT id
  FROM '. CATEGORIES_TABLE .'
  WHERE id = '. $params['category_id'] .'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) == 0)
  {
    return new PwgError(404, 'category_id not found');
  }

  $query = '
SELECT
    DISTINCT category_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$params['category_id'].'
  LIMIT 1
;';
  $result = pwg_query($query);
  $has_images = pwg_db_num_rows($result) > 0 ? true : false;

  if (!$has_images)
  {
    return new PwgError(401, 'not permitted');
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  
  set_random_representant(array($params['category_id']));

  pwg_activity('album', $params['category_id'], 'edit');

  // return url of the new representative
  $query = '
SELECT *
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$params['category_id'].'
;';
  $category = pwg_db_fetch_assoc(pwg_query($query));

  return get_category_representant_properties($category['representative_picture_id'], IMG_SMALL);
}

/**
 * API method
 * Deletes a category
 * @param mixed[] $params
 *    @option string|int[] category_id
 *    @option string photo_deletion_mode
 *    @option string pwg_token
 */
function ws_categories_delete($params, &$service)
{
  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  $modes = array('no_delete', 'delete_orphans', 'force_delete');
  if (!in_array($params['photo_deletion_mode'], $modes))
  {
    return new PwgError(500,
      '[ws_categories_delete]'
      .' invalid parameter photo_deletion_mode "'.$params['photo_deletion_mode'].'"'
      .', possible values are {'.implode(', ', $modes).'}.'
      );
  }

  if (!is_array($params['category_id']))
  {
    $params['category_id'] = preg_split(
      '/[\s,;\|]/',
      $params['category_id'],
      -1,
      PREG_SPLIT_NO_EMPTY
      );
  }
  $params['category_id'] = array_map('intval', $params['category_id']);

  $category_ids = array();
  foreach ($params['category_id'] as $category_id)
  {
    if ($category_id > 0)
    {
      $category_ids[] = $category_id;
    }
  }

  if (count($category_ids) == 0)
  {
    return;
  }

  $query = '
SELECT id
  FROM '. CATEGORIES_TABLE .'
  WHERE id IN ('. implode(',', $category_ids) .')
;';
  $category_ids = array_from_query($query, 'id');

  if (count($category_ids) == 0)
  {
    return;
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  delete_categories($category_ids, $params['photo_deletion_mode']);
  update_global_rank();
  invalidate_user_cache();
}

/**
 * API method
 * Moves a category
 * @param mixed[] $params
 *    @option string|int[] category_id
 *    @option int parent
 *    @option string pwg_token
 */
function ws_categories_move($params, &$service)
{
  global $page;

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, 'Invalid security token');
  }

  if (!is_array($params['category_id']))
  {
    $params['category_id'] = preg_split(
      '/[\s,;\|]/',
      $params['category_id'],
      -1,
      PREG_SPLIT_NO_EMPTY
      );
  }
  $params['category_id'] = array_map('intval', $params['category_id']);

  $category_ids = array();
  foreach ($params['category_id'] as $category_id)
  {
    if ($category_id > 0)
    {
      $category_ids[] = $category_id;
    }
  }

  if (count($category_ids) == 0)
  {
    return new PwgError(403, 'Invalid category_id input parameter, no category to move');
  }

  // we can't move physical categories
  $categories_in_db = array();

  $query = '
SELECT id, name, dir
  FROM '. CATEGORIES_TABLE .'
  WHERE id IN ('. implode(',', $category_ids) .')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $categories_in_db[ $row['id'] ] = $row;

    // we break on error at first physical category detected
    if (!empty($row['dir']))
    {
      $row['name'] = strip_tags(
        trigger_change(
          'render_category_name',
          $row['name'],
          'ws_categories_move'
          )
        );

      return new PwgError(403,
        sprintf(
          'Category %s (%u) is not a virtual category, you cannot move it',
          $row['name'],
          $row['id']
          )
        );
    }
  }

  if (count($categories_in_db) != count($category_ids))
  {
    $unknown_category_ids = array_diff($category_ids, array_keys($categories_in_db));

    return new PwgError(403,
      sprintf(
        'Category %u does not exist',
        $unknown_category_ids[0]
        )
      );
  }

  // does this parent exists? This check should be made in the
  // move_categories function, not here
  // 0 as parent means "move categories at gallery root"
  if (0 != $params['parent'])
  {
    $subcat_ids = get_subcat_ids(array($params['parent']));
    if (count($subcat_ids) == 0)
    {
      return new PwgError(403, 'Unknown parent category id');
    }
  }

  $page['infos'] = array();
  $page['errors'] = array();

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  move_categories($category_ids, $params['parent']);
  invalidate_user_cache();

  if (count($page['errors']) != 0)
  {
    return new PwgError(403, implode('; ', $page['errors']));
  }
}

/**
 * API method
 * Return the number of orphan photos if an album is deleted
 * @since 12
 */
function ws_categories_calculateOrphans($param, &$service)
{
  global $conf;

  $category_id = $param['category_id'][0];

  $query = '
SELECT DISTINCT 
    category_id
  FROM 
    '.IMAGE_CATEGORY_TABLE.'
  WHERE 
    category_id = '.$category_id.'
  LIMIT 1';
  $result = pwg_query($query);
  $category['has_images'] = pwg_db_num_rows($result)>0 ? true : false;

  // number of sub-categories
  $subcat_ids = get_subcat_ids(array($category_id));

  $category['nb_subcats'] = count($subcat_ids) - 1;

  // total number of images under this category (including sub-categories)
  $query = '
SELECT DISTINCT
    (image_id)
  FROM 
    '.IMAGE_CATEGORY_TABLE.'
  WHERE 
    category_id IN ('.implode(',', $subcat_ids).')
  ;';
  $image_ids_recursive = query2array($query, null, 'image_id');

  $category['nb_images_recursive'] = count($image_ids_recursive);

  // number of images that would become orphan on album deletion
  $category['nb_images_becoming_orphan'] = 0;
  $category['nb_images_associated_outside'] = 0;

  if ($category['nb_images_recursive'] > 0)
  {
    // if we don't have "too many" photos, it's faster to compute the orphans with MySQL
    if ($category['nb_images_recursive'] < 1000)
    {
      $query = '
  SELECT DISTINCT
      (image_id)
    FROM 
      '.IMAGE_CATEGORY_TABLE.'
    WHERE 
      category_id 
    NOT IN 
      ('.implode(',', $subcat_ids).')
    AND 
      image_id 
    IN 
      ('.implode(',', $image_ids_recursive).')
  ;';

    $image_ids_associated_outside = query2array($query, null, 'image_id');
    $category['nb_images_associated_outside'] = count($image_ids_associated_outside);

    $image_ids_becoming_orphan = array_diff($image_ids_recursive, $image_ids_associated_outside);
    $category['nb_images_becoming_orphan'] = count($image_ids_becoming_orphan);
  }
  // else it's better to avoid sending a huge SQL request, we compute the orphan list with PHP
    else
    {
    $image_ids_recursive_keys = array_flip($image_ids_recursive);

    $query = '
  SELECT
      image_id
    FROM 
      '.IMAGE_CATEGORY_TABLE.'
    WHERE 
      category_id 
    NOT IN 
      ('.implode(',', $subcat_ids).')
  ;';
    $image_ids_associated_outside = query2array($query, null, 'image_id');
    $image_ids_not_orphan = array();

    foreach ($image_ids_associated_outside as $image_id)
    {
      if (isset($image_ids_recursive_keys[$image_id]))
      {
        $image_ids_not_orphan[] = $image_id;
      }
    }

    $category['nb_images_associated_outside'] = count(array_unique($image_ids_not_orphan));
    $image_ids_becoming_orphan = array_diff($image_ids_recursive, $image_ids_not_orphan);
    $category['nb_images_becoming_orphan'] = count($image_ids_becoming_orphan);
  }
}

  $output[] = array(
    'nb_images_associated_outside' => $category['nb_images_associated_outside'],
    'nb_images_becoming_orphan' => $category['nb_images_becoming_orphan'],
    'nb_images_recursive' => $category['nb_images_recursive'],
  );
  
  return $output;
}

?>
