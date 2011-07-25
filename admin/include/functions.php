<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

include(PHPWG_ROOT_PATH.'admin/include/functions_metadata.php');

// The function delete_site deletes a site and call the function
// delete_categories for each primary category of the site
function delete_site( $id )
{
  // destruction of the categories of the site
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE site_id = '.$id.'
;';
  $result = pwg_query($query);
  $category_ids = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($category_ids, $row['id']);
  }
  delete_categories($category_ids);

  // destruction of the site
  $query = '
DELETE FROM '.SITES_TABLE.'
  WHERE id = '.$id.'
;';
  pwg_query($query);
}


// The function delete_categories deletes the categories identified by the
// (numeric) key of the array $ids. It also deletes (in the database) :
//    - all the elements physically linked to the category (delete_elements, see further)
//    - all the links between elements and this category
//    - all the restrictions linked to the category
// The function works recursively.
//
// the $photo_deletion_mode is for photos virtually linked to the categorty
//   * no_delete : delete no photo, may create orphans
//   * delete_orphans : delete photos that are no longer linked to any category
//   * force_delete : delete photos even if they are linked to another category
function delete_categories($ids, $photo_deletion_mode='no_delete')
{
  if (count($ids) == 0)
  {
    return;
  }

  // add sub-category ids to the given ids : if a category is deleted, all
  // sub-categories must be so
  $ids = get_subcat_ids($ids);

  // destruction of all photos physically linked to the category
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  $result = pwg_query($query);
  $element_ids = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($element_ids, $row['id']);
  }
  delete_elements($element_ids);

  // now, should we delete photos that are virtually linked to the category?
  if ('delete_orphans' == $photo_deletion_mode or 'force_delete' == $photo_deletion_mode)
  {
    $query = '
SELECT
    DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $ids).')
;';
    $image_ids_linked = array_from_query($query, 'image_id');

    if (count($image_ids_linked) > 0)
    {
      if ('delete_orphans' == $photo_deletion_mode)
      {
        $query = '
SELECT
    DISTINCT(image_id)
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id IN ('.implode(',', $image_ids_linked).')
    AND category_id NOT IN ('.implode(',', $ids).')
;';
        $image_ids_not_orphans = array_from_query($query, 'image_id');
        $image_ids_to_delete = array_diff($image_ids_linked, $image_ids_not_orphans);
      }

      if ('force_delete' == $photo_deletion_mode)
      {
        $image_ids_to_delete = $image_ids_linked;
      }

      delete_elements($image_ids_to_delete, true);
    }
  }

  // destruction of the links between images and this category
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the access linked to the category
  $query = '
DELETE FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  $query = '
DELETE FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the category
  $query = '
DELETE FROM '.CATEGORIES_TABLE.'
  WHERE id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  $query='
DELETE FROM '.OLD_PERMALINKS_TABLE.'
  WHERE cat_id IN ('.implode(',',$ids).')';
  pwg_query($query);

  $query='
DELETE FROM '.USER_CACHE_CATEGORIES_TABLE.'
  WHERE cat_id IN ('.implode(',',$ids).')';
  pwg_query($query);

  trigger_action('delete_categories', $ids);
}

// Deletes all files (on disk) related to given image ids
// @return image ids where files are deleted successfully
function delete_element_files($ids)
{
  if (count($ids) == 0)
  {
    return 0;
  }

  include_once(PHPWG_ROOT_PATH.'include/functions_picture.inc.php');
  
  $new_ids = array();

  $query = '
SELECT
    id,
    path,
    tn_ext,
    has_high,
    representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (url_is_remote($row['path']))
    {
      continue;
    }
    
    $files = array();
    $files[] = get_element_path($row);

    if (!empty($row['tn_ext']))
    {
      $files[] = get_thumbnail_path($row);
    }
    
    if (!empty($row['has_high']) and get_boolean($row['has_high']))
    {
      $files[] = get_high_path($row);
    }
    
    if (!empty($row['representative_ext']))
    {
      $pi = pathinfo($row['path']);
      $file_wo_ext = get_filename_wo_extension($pi['basename']);
      $files[] = PHPWG_ROOT_PATH.$pi['dirname'].'/pwg_representative/'.$file_wo_ext.'.'.$row['representative_ext'];
    }

    $ok = true;
    foreach ($files as $path)
    {
      if (is_file($path) and !unlink($path))
      {
        $ok = false;
        trigger_error('"'.$path.'" cannot be removed', E_USER_WARNING);
        break;
      }
    }
    
    if ($ok)
    {
      $new_ids[] += $row['id'];
    }
    else
    {
      break;
    }
  }
  return $new_ids;
}

// The function delete_elements deletes the elements identified by the
// (numeric) values of the array $ids. It also deletes (in the database) :
//    - all the comments related to elements
//    - all the links between categories and elements
//    - all the favorites associated to elements
// @return number of deleted elements
function delete_elements($ids, $physical_deletion=false)
{
  if (count($ids) == 0)
  {
    return 0;
  }
  trigger_action('begin_delete_elements', $ids);

  if ($physical_deletion)
  {
    $ids = delete_element_files($ids);
    if (count($ids)==0)
    {
      return 0;
    }
  }

  // destruction of the comments on the image
  $query = '
DELETE FROM '.COMMENTS_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the links between images and this category
  $query = '
DELETE FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the links between images and tags
  $query = '
DELETE FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the favorites associated with the picture
  $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE image_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the rates associated to this element
  $query = '
DELETE FROM '.RATE_TABLE.'
  WHERE element_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the rates associated to this element
  $query = '
DELETE FROM '.CADDIE_TABLE.'
  WHERE element_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // destruction of the image
  $query = '
DELETE FROM '.IMAGES_TABLE.'
  WHERE id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  pwg_query($query);

  // are the photo used as category representant?
  $query = '
SELECT
    id
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  $category_ids = array_from_query($query, 'id');
  if (count($category_ids) > 0)
  {
    update_category($category_ids);
  }

  trigger_action('delete_elements', $ids);
  return count($ids);
}

// The delete_user function delete a user identified by the $user_id
// It also deletes :
//     - all the access linked to this user
//     - all the links to any group
//     - all the favorites linked to this user
//     - calculated permissions linked to the user
//     - all datas about notifications for the user
function delete_user($user_id)
{
  global $conf;
  $tables = array(
    // destruction of the access linked to the user
    USER_ACCESS_TABLE,
    // destruction of data notification by mail for this user
    USER_MAIL_NOTIFICATION_TABLE,
    // destruction of data RSS notification for this user
    USER_FEED_TABLE,
    // deletion of calculated permissions linked to the user
    USER_CACHE_TABLE,
    // deletion of computed cache data linked to the user
    USER_CACHE_CATEGORIES_TABLE,
    // destruction of the group links for this user
    USER_GROUP_TABLE,
    // destruction of the favorites associated with the user
    FAVORITES_TABLE,
    // destruction of the caddie associated with the user
    CADDIE_TABLE,
    // deletion of piwigo specific informations
    USER_INFOS_TABLE,
    );

  foreach ($tables as $table)
  {
    $query = '
DELETE FROM '.$table.'
  WHERE user_id = '.$user_id.'
;';
    pwg_query($query);
  }

  // destruction of the user
  $query = '
DELETE FROM '.SESSIONS_TABLE.'
  WHERE data LIKE \'pwg_uid|i:'.(int)$user_id.';%\'
;';
  pwg_query($query);

  // destruction of the user
  $query = '
DELETE FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.$user_id.'
;';
  pwg_query($query);

  trigger_action('delete_user', $user_id);
}

/**
 * Deletes all tags linked to no photo
 */
function delete_orphan_tags()
{
  $orphan_tags = get_orphan_tags();
  
  if (count($orphan_tags) > 0)
  {
    $orphan_tag_ids = array();
    foreach ($orphan_tags as $tag)
    {
      array_push($orphan_tag_ids, $tag['id']);
    }

    $query = '
DELETE
  FROM '.TAGS_TABLE.'
  WHERE id IN ('.implode(',', $orphan_tag_ids).')
;';
    pwg_query($query);
  }
}

/**
 * Get all tags (id + name) linked to no photo
 */
function get_orphan_tags()
{
  $orphan_tags = array();
  
  $query = '
SELECT
    id,
    name
  FROM '.TAGS_TABLE.'
    LEFT JOIN '.IMAGE_TAG_TABLE.' ON id = tag_id
  WHERE tag_id IS NULL
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($orphan_tags, $row);
  }

  return $orphan_tags;
}

/**
 * Verifies that the representative picture really exists in the db and
 * picks up a random represantive if possible and based on config.
 *
 * @param mixed category id
 * @returns void
 */
function update_category($ids = 'all')
{
  global $conf;

  if ($ids=='all')
  {
    $where_cats = '1=1';
  }
  elseif ( !is_array($ids) )
  {
    $where_cats = '%s='.$ids;
  }
  else
  {
    if (count($ids) == 0)
    {
      return false;
    }
    $where_cats = '%s IN('.wordwrap(implode(', ', $ids), 120, "\n").')';
  }

  // find all categories where the setted representative is not possible :
  // the picture does not exist
  $query = '
SELECT DISTINCT c.id
  FROM '.CATEGORIES_TABLE.' AS c LEFT JOIN '.IMAGES_TABLE.' AS i
    ON c.representative_picture_id = i.id
  WHERE representative_picture_id IS NOT NULL
    AND '.sprintf($where_cats, 'c.id').'
    AND i.id IS NULL
;';
  $wrong_representant = array_from_query($query, 'id');

  if (count($wrong_representant) > 0)
  {
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id IN ('.wordwrap(implode(', ', $wrong_representant), 120, "\n").')
;';
    pwg_query($query);
  }

  if (!$conf['allow_random_representative'])
  {
    // If the random representant is not allowed, we need to find
    // categories with elements and with no representant. Those categories
    // must be added to the list of categories to set to a random
    // representant.
    $query = '
SELECT DISTINCT id
  FROM '.CATEGORIES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.'
    ON id = category_id
  WHERE representative_picture_id IS NULL
    AND '.sprintf($where_cats, 'category_id').'
;';
    $to_rand = array_from_query($query, 'id');
    if (count($to_rand) > 0)
    {
      set_random_representant($to_rand);
    }
  }
}

/**
 * returns an array containing sub-directories which can be a category,
 * recursive by default
 *
 * directories nammed "thumbnail", "pwg_high" or "pwg_representative" are
 * omitted
 *
 * @param string $basedir
 * @return array
 */
function get_fs_directories($path, $recursive = true)
{
  $dirs = array();

  if (is_dir($path))
  {
    if ($contents = opendir($path))
    {
      while (($node = readdir($contents)) !== false)
      {
        if (is_dir($path.'/'.$node)
            and $node != '.'
            and $node != '..'
            and $node != '.svn'
            and $node != 'thumbnail'
            and $node != 'pwg_high'
            and $node != 'pwg_representative')
        {
          array_push($dirs, $path.'/'.$node);
          if ($recursive)
          {
            $dirs = array_merge($dirs, get_fs_directories($path.'/'.$node));
          }
        }
      }
      closedir($contents);
    }
  }

  return $dirs;
}

/**
 * order categories (update categories.rank and global_rank database fields)
 * so that rank field are consecutive integers starting at 1 for each child
 * @return void
 */
function update_global_rank()
{
  $query = '
SELECT id, id_uppercat, uppercats, rank, global_rank
  FROM '.CATEGORIES_TABLE.'
  ORDER BY id_uppercat,rank,name';

  $cat_map = array();

  $current_rank = 0;
  $current_uppercat = '';

  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    if ($row['id_uppercat'] != $current_uppercat)
    {
      $current_rank = 0;
      $current_uppercat = $row['id_uppercat'];
    }
    ++$current_rank;
    $cat =
      array(
        'rank' =>        $current_rank,
        'rank_changed' =>$current_rank!=$row['rank'],
        'global_rank' => $row['global_rank'],
        'uppercats' =>   $row['uppercats'],
        );
    $cat_map[ $row['id'] ] = $cat;
  }

  $datas = array();

  foreach( $cat_map as $id=>$cat )
  {
    $new_global_rank = preg_replace(
          '/(\d+)/e',
          "\$cat_map['$1']['rank']",
          str_replace(',', '.', $cat['uppercats'] )
          );
    if ( $cat['rank_changed']
      or $new_global_rank!=$cat['global_rank']
      )
    {
      $datas[] = array(
          'id' => $id,
          'rank' => $cat['rank'],
          'global_rank' => $new_global_rank,
        );
    }
  }

  mass_updates(
    CATEGORIES_TABLE,
    array(
      'primary' => array('id'),
      'update'  => array('rank', 'global_rank')
      ),
    $datas
    );
  return count($datas);
}

/**
 * change the visible property on a set of categories
 *
 * @param array categories
 * @param string value
 * @return void
 */
function set_cat_visible($categories, $value)
{
  if (!in_array($value, array('true', 'false')))
  {
    trigger_error("set_cat_visible invalid param $value", E_USER_WARNING);
    return false;
  }

  // unlocking a category => all its parent categories become unlocked
  if ($value == 'true')
  {
    $uppercats = get_uppercat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'true\'
  WHERE id IN ('.implode(',', $uppercats).')';
    pwg_query($query);
  }
  // locking a category   => all its child categories become locked
  if ($value == 'false')
  {
    $subcats = get_subcat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'false\'
  WHERE id IN ('.implode(',', $subcats).')';
    pwg_query($query);
  }
}

/**
 * change the status property on a set of categories : private or public
 *
 * @param array categories
 * @param string value
 * @return void
 */
function set_cat_status($categories, $value)
{
  if (!in_array($value, array('public', 'private')))
  {
    trigger_error("set_cat_status invalid param $value", E_USER_WARNING);
    return false;
  }

  // make public a category => all its parent categories become public
  if ($value == 'public')
  {
    $uppercats = get_uppercat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'public\'
  WHERE id IN ('.implode(',', $uppercats).')
;';
    pwg_query($query);
  }
  // make a category private => all its child categories become private
  if ($value == 'private')
  {
    $subcats = get_subcat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET status = \'private\'
  WHERE id IN ('.implode(',', $subcats).')';
    pwg_query($query);
  }
}

/**
 * returns all uppercats category ids of the given category ids
 *
 * @param array cat_ids
 * @return array
 */
function get_uppercat_ids($cat_ids)
{
  if (!is_array($cat_ids) or count($cat_ids) < 1)
  {
    return array();
  }

  $uppercats = array();

  $query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $cat_ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $uppercats = array_merge($uppercats,
                             explode(',', $row['uppercats']));
  }
  $uppercats = array_unique($uppercats);

  return $uppercats;
}

/**
 * set a new random representant to the categories
 *
 * @param array categories
 */
function set_random_representant($categories)
{
  $datas = array();
  foreach ($categories as $category_id)
  {
    $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$category_id.'
  ORDER BY '.DB_RANDOM_FUNCTION.'()
  LIMIT 1
;';
    list($representative) = pwg_db_fetch_row(pwg_query($query));

    array_push(
      $datas,
      array(
        'id' => $category_id,
        'representative_picture_id' => $representative,
        )
      );
  }

  mass_updates(
    CATEGORIES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('representative_picture_id')
      ),
    $datas
    );
}

/**
 * returns the fulldir for each given category id
 *
 * @param array cat_ids
 * @return array
 */
function get_fulldirs($cat_ids)
{
  if (count($cat_ids) == 0)
  {
    return array();
  }

  // caching directories of existing categories
  $query = '
SELECT id, dir
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
;';
  $cat_dirs = simple_hash_from_query($query, 'id', 'dir');

  // caching galleries_url
  $query = '
SELECT id, galleries_url
  FROM '.SITES_TABLE.'
;';
  $galleries_url = simple_hash_from_query($query, 'id', 'galleries_url');

  // categories : id, site_id, uppercats
  $categories = array();

  $query = '
SELECT id, uppercats, site_id
  FROM '.CATEGORIES_TABLE.'
  WHERE dir IS NOT NULL
    AND id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($categories, $row);
  }

  // filling $cat_fulldirs
  $cat_fulldirs = array();
  foreach ($categories as $category)
  {
    $uppercats = str_replace(',', '/', $category['uppercats']);
    $cat_fulldirs[$category['id']] = $galleries_url[$category['site_id']];
    $cat_fulldirs[$category['id']].= preg_replace('/(\d+)/e',
                                                  "\$cat_dirs['$1']",
                                                  $uppercats);
  }

  return $cat_fulldirs;
}

/**
 * returns an array with all file system files according to
 * $conf['file_ext']
 *
 * @param string $path
 * @param bool recursive
 * @return array
 */
function get_fs($path, $recursive = true)
{
  global $conf;

  // because isset is faster than in_array...
  if (!isset($conf['flip_picture_ext']))
  {
    $conf['flip_picture_ext'] = array_flip($conf['picture_ext']);
  }
  if (!isset($conf['flip_file_ext']))
  {
    $conf['flip_file_ext'] = array_flip($conf['file_ext']);
  }

  $fs['elements'] = array();
  $fs['thumbnails'] = array();
  $fs['representatives'] = array();
  $subdirs = array();

  if (is_dir($path))
  {
    if ($contents = opendir($path))
    {
      while (($node = readdir($contents)) !== false)
      {
        if (is_file($path.'/'.$node))
        {
          $extension = get_extension($node);

//          if (in_array($extension, $conf['picture_ext']))
          if (isset($conf['flip_picture_ext'][$extension]))
          {
            if (basename($path) == 'thumbnail')
            {
              array_push($fs['thumbnails'], $path.'/'.$node);
            }
            else if (basename($path) == 'pwg_representative')
            {
              array_push($fs['representatives'], $path.'/'.$node);
            }
            else
            {
              array_push($fs['elements'], $path.'/'.$node);
            }
          }
//          else if (in_array($extension, $conf['file_ext']))
          else if (isset($conf['flip_file_ext'][$extension]))
          {
            array_push($fs['elements'], $path.'/'.$node);
          }
        }
        else if (is_dir($path.'/'.$node)
                 and $node != '.'
                 and $node != '..'
                 and $node != 'pwg_high'
                 and $recursive)
        {
          array_push($subdirs, $node);
        }
      }
    }
    closedir($contents);

    foreach ($subdirs as $subdir)
    {
      $tmp_fs = get_fs($path.'/'.$subdir);

      $fs['elements']        = array_merge($fs['elements'],
                                           $tmp_fs['elements']);

      $fs['thumbnails']      = array_merge($fs['thumbnails'],
                                           $tmp_fs['thumbnails']);

      $fs['representatives'] = array_merge($fs['representatives'],
                                           $tmp_fs['representatives']);
    }
  }
  return $fs;
}

/**
 * synchronize base users list and related users list
 *
 * compares and synchronizes base users table (USERS_TABLE) with its child
 * tables (USER_INFOS_TABLE, USER_ACCESS, USER_CACHE, USER_GROUP) : each
 * base user must be present in child tables, users in child tables not
 * present in base table must be deleted.
 *
 * @return void
 */
function sync_users()
{
  global $conf;

  $query = '
SELECT '.$conf['user_fields']['id'].' AS id
  FROM '.USERS_TABLE.'
;';
  $base_users = array_from_query($query, 'id');

  $query = '
SELECT user_id
  FROM '.USER_INFOS_TABLE.'
;';
  $infos_users = array_from_query($query, 'user_id');

  // users present in $base_users and not in $infos_users must be added
  $to_create = array_diff($base_users, $infos_users);

  if (count($to_create) > 0)
  {
    create_user_infos($to_create);
  }

  // users present in user related tables must be present in the base user
  // table
  $tables = array(
    USER_MAIL_NOTIFICATION_TABLE,
    USER_FEED_TABLE,
    USER_INFOS_TABLE,
    USER_ACCESS_TABLE,
    USER_CACHE_TABLE,
    USER_CACHE_CATEGORIES_TABLE,
    USER_GROUP_TABLE
    );

  foreach ($tables as $table)
  {
    $query = '
SELECT DISTINCT user_id
  FROM '.$table.'
;';
    $to_delete = array_diff(
      array_from_query($query, 'user_id'),
      $base_users
      );

    if (count($to_delete) > 0)
    {
      $query = '
DELETE
  FROM '.$table.'
  WHERE user_id in ('.implode(',', $to_delete).')
;';
      pwg_query($query);
    }
  }
}

/**
 * updates categories.uppercats field based on categories.id +
 * categories.id_uppercat
 *
 * @return void
 */
function update_uppercats()
{
  $query = '
SELECT id, id_uppercat, uppercats
  FROM '.CATEGORIES_TABLE.'
;';
  $cat_map = hash_from_query($query, 'id');

  $datas = array();
  foreach ($cat_map as $id => $cat)
  {
    $upper_list = array();

    $uppercat = $id;
    while ($uppercat)
    {
      array_push($upper_list, $uppercat);
      $uppercat = $cat_map[$uppercat]['id_uppercat'];
    }

    $new_uppercats = implode(',', array_reverse($upper_list));
    if ($new_uppercats != $cat['uppercats'])
    {
      array_push(
        $datas,
        array(
          'id' => $id,
          'uppercats' => $new_uppercats
          )
        );
    }
  }
  $fields = array('primary' => array('id'), 'update' => array('uppercats'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
}

/**
 * update images.path field
 *
 * @return void
 */
function update_path()
{
  $query = '
SELECT DISTINCT(storage_category_id)
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IS NOT NULL
;';
  $cat_ids = array_from_query($query, 'storage_category_id');
  $fulldirs = get_fulldirs($cat_ids);

  foreach ($cat_ids as $cat_id)
  {
    $query = '
UPDATE '.IMAGES_TABLE.'
  SET path = '.pwg_db_concat(array("'".$fulldirs[$cat_id]."/'",'file')).'
  WHERE storage_category_id = '.$cat_id.'
;';
    pwg_query($query);
  }
}

/**
 * change the parent category of the given categories. The categories are
 * supposed virtual.
 *
 * @param array category identifiers
 * @param int parent category identifier
 * @return void
 */
function move_categories($category_ids, $new_parent = -1)
{
  global $page;

  if (count($category_ids) == 0)
  {
    return;
  }

  $new_parent = $new_parent < 1 ? 'NULL' : $new_parent;

  $categories = array();

  $query = '
SELECT id, id_uppercat, status, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $categories[$row['id']] =
      array(
        'parent' => empty($row['id_uppercat']) ? 'NULL' : $row['id_uppercat'],
        'status' => $row['status'],
        'uppercats' => $row['uppercats']
        );
  }

  // is the movement possible? The movement is impossible if you try to move
  // a category in a sub-category or itself
  if ('NULL' != $new_parent)
  {
    $query = '
SELECT uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$new_parent.'
;';
    list($new_parent_uppercats) = pwg_db_fetch_row(pwg_query($query));

    foreach ($categories as $category)
    {
      // technically, you can't move a category with uppercats 12,125,13,14
      // into a new parent category with uppercats 12,125,13,14,24
      if (preg_match('/^'.$category['uppercats'].'(,|$)/', $new_parent_uppercats))
      {
        array_push(
          $page['errors'],
          l10n('You cannot move an album in its own sub album')
          );
        return;
      }
    }
  }

  $tables =
    array(
      USER_ACCESS_TABLE => 'user_id',
      GROUP_ACCESS_TABLE => 'group_id'
      );

  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET id_uppercat = '.$new_parent.'
  WHERE id IN ('.implode(',', $category_ids).')
;';
  pwg_query($query);

  update_uppercats();
  update_global_rank();

  // status and related permissions management
  if ('NULL' == $new_parent)
  {
    $parent_status = 'public';
  }
  else
  {
    $query = '
SELECT status
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$new_parent.'
;';
    list($parent_status) = pwg_db_fetch_row(pwg_query($query));
  }

  if ('private' == $parent_status)
  {
    foreach ($categories as $cat_id => $category)
    {
      switch ($category['status'])
      {
        case 'public' :
        {
          set_cat_status(array($cat_id), 'private');
          break;
        }
        case 'private' :
        {
          $subcats = get_subcat_ids(array($cat_id));

          foreach ($tables as $table => $field)
          {
            $query = '
SELECT '.$field.'
  FROM '.$table.'
  WHERE cat_id = '.$cat_id.'
;';
            $category_access = array_from_query($query, $field);

            $query = '
SELECT '.$field.'
  FROM '.$table.'
  WHERE cat_id = '.$new_parent.'
;';
            $parent_access = array_from_query($query, $field);

            $to_delete = array_diff($parent_access, $category_access);

            if (count($to_delete) > 0)
            {
              $query = '
DELETE FROM '.$table.'
  WHERE '.$field.' IN ('.implode(',', $to_delete).')
    AND cat_id IN ('.implode(',', $subcats).')
;';
              pwg_query($query);
            }
          }
          break;
        }
      }
    }
  }

  array_push(
    $page['infos'],
    l10n_dec(
      '%d album moved', '%d albums moved',
      count($categories)
      )
    );
}

/**
 * create a virtual category
 *
 * @param string category name
 * @param int parent category id
 * @return array with ('info' and 'id') or ('error') key
 */
function create_virtual_category($category_name, $parent_id=null)
{
  global $conf, $user;

  // is the given category name only containing blank spaces ?
  if (preg_match('/^\s*$/', $category_name))
  {
    return array('error' => l10n('The name of an album must not be empty'));
  }

  $parent_id = !empty($parent_id) ? $parent_id : 'NULL';

  $query = '
SELECT MAX(rank)
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat '.(is_numeric($parent_id) ? '= '.$parent_id : 'IS NULL').'
;';
  list($current_rank) = pwg_db_fetch_row(pwg_query($query));

  $insert = array(
    'name' => $category_name,
    'rank' => ++$current_rank,
    'commentable' => boolean_to_string($conf['newcat_default_commentable']),
    );

  if ($parent_id != 'NULL')
  {
    $query = '
SELECT id, uppercats, global_rank, visible, status
  FROM '.CATEGORIES_TABLE.'
  WHERE id = '.$parent_id.'
;';
    $parent = pwg_db_fetch_assoc(pwg_query($query));

    $insert['id_uppercat'] = $parent['id'];
    $insert['global_rank'] = $parent['global_rank'].'.'.$insert['rank'];

    // at creation, must a category be visible or not ? Warning : if the
    // parent category is invisible, the category is automatically create
    // invisible. (invisible = locked)
    if ('false' == $parent['visible'])
    {
      $insert['visible'] = 'false';
    }
    else
    {
      $insert['visible'] = boolean_to_string($conf['newcat_default_visible']);
    }

    // at creation, must a category be public or private ? Warning : if the
    // parent category is private, the category is automatically create
    // private.
    if ('private' == $parent['status'])
    {
      $insert['status'] = 'private';
    }
    else
    {
      $insert['status'] = $conf['newcat_default_status'];
    }
  }
  else
  {
    $insert['visible'] = boolean_to_string($conf['newcat_default_visible']);
    $insert['status'] = $conf['newcat_default_status'];
    $insert['global_rank'] = $insert['rank'];
  }

  // we have then to add the virtual category
  mass_inserts(
    CATEGORIES_TABLE,
    array(
      'site_id', 'name', 'id_uppercat', 'rank', 'commentable',
      'visible', 'status', 'global_rank',
      ),
    array($insert)
    );

  $inserted_id = pwg_db_insert_id(CATEGORIES_TABLE);

  $query = '
UPDATE
  '.CATEGORIES_TABLE.'
  SET uppercats = \''.
    (isset($parent) ? $parent{'uppercats'}.',' : '').
    $inserted_id.
    '\'
  WHERE id = '.$inserted_id.'
;';
  pwg_query($query);

  if ('private' == $insert['status'])
  {
    add_permission_on_category($inserted_id, array_unique(array_merge(get_admins(), array($user['id']))));
  }

  return array(
    'info' => l10n('Virtual album added'),
    'id'   => $inserted_id,
    );
}

/**
 * Set tags to an image. Warning: given tags are all tags associated to the
 * image, not additionnal tags.
 *
 * @param array tag ids
 * @param int image id
 * @return void
 */
function set_tags($tags, $image_id)
{
  $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id = '.$image_id.'
;';
  pwg_query($query);

  if (count($tags) > 0)
  {
    $inserts = array();
    foreach ($tags as $tag_id)
    {
      array_push(
        $inserts,
        array(
          'tag_id' => $tag_id,
          'image_id' => $image_id
          )
        );
    }
    mass_inserts(
      IMAGE_TAG_TABLE,
      array_keys($inserts[0]),
      $inserts
      );
  }
}

/**
 * Add new tags to a set of images.
 *
 * @param array tag ids
 * @param array image ids
 * @return void
 */
function add_tags($tags, $images)
{
  if (count($tags) == 0 or count($images) == 0)
  {
    return;
  }

  // we can't insert twice the same {image_id,tag_id} so we must first
  // delete lines we'll insert later
  $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', $images).')
    AND tag_id IN ('.implode(',', $tags).')
;';
  pwg_query($query);

  $inserts = array();
  foreach ($images as $image_id)
  {
    foreach ($tags as $tag_id)
    {
      array_push(
        $inserts,
        array(
          'image_id' => $image_id,
          'tag_id' => $tag_id,
          )
        );
    }
  }
  mass_inserts(
    IMAGE_TAG_TABLE,
    array_keys($inserts[0]),
    $inserts
    );
}

function tag_id_from_tag_name($tag_name)
{
  global $page;

  $tag_name = trim($tag_name);
  if (isset($page['tag_id_from_tag_name_cache'][$tag_name]))
  {
    return $page['tag_id_from_tag_name_cache'][$tag_name];
  }

  // does the tag already exists?
  $query = '
SELECT id
  FROM '.TAGS_TABLE.'
  WHERE name = \''.$tag_name.'\'
;';
  $existing_tags = array_from_query($query, 'id');

  if (count($existing_tags) == 0)
  {
    mass_inserts(
      TAGS_TABLE,
      array('name', 'url_name'),
      array(
        array(
          'name' => $tag_name,
          'url_name' => str2url($tag_name),
          )
        )
      );

    $page['tag_id_from_tag_name_cache'][$tag_name] = pwg_db_insert_id(TAGS_TABLE);
  }
  else
  {
    $page['tag_id_from_tag_name_cache'][$tag_name] = $existing_tags[0];
  }

  return $page['tag_id_from_tag_name_cache'][$tag_name];
}

function set_tags_of($tags_of)
{
  if (count($tags_of) > 0)
  {
    $query = '
DELETE
  FROM '.IMAGE_TAG_TABLE.'
  WHERE image_id IN ('.implode(',', array_keys($tags_of)).')
;';
    pwg_query($query);

    $inserts = array();

    foreach ($tags_of as $image_id => $tag_ids)
    {
      foreach ($tag_ids as $tag_id)
      {
        array_push(
          $inserts,
          array(
            'image_id' => $image_id,
            'tag_id' => $tag_id,
            )
          );
      }
    }

    mass_inserts(
      IMAGE_TAG_TABLE,
      array_keys($inserts[0]),
      $inserts
      );
  }
}

/**
 * Associate a list of images to a list of categories.
 *
 * The function will not duplicate links
 *
 * @param array images
 * @param array categories
 * @return void
 */
function associate_images_to_categories($images, $categories)
{
  if (count($images) == 0
      or count($categories) == 0)
  {
    return false;
  }

  $query = '
DELETE
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE image_id IN ('.implode(',', $images).')
    AND category_id IN ('.implode(',', $categories).')
;';
  pwg_query($query);

  $inserts = array();
  foreach ($categories as $category_id)
  {
    foreach ($images as $image_id)
    {
      array_push(
        $inserts,
        array(
          'image_id' => $image_id,
          'category_id' => $category_id,
          )
        );
    }
  }

  mass_inserts(
    IMAGE_CATEGORY_TABLE,
    array_keys($inserts[0]),
    $inserts
    );

  update_category($categories);
}

/**
 * Associate images associated to a list of source categories to a list of
 * destination categories.
 *
 * @param array sources
 * @param array destinations
 * @return void
 */
function associate_categories_to_categories($sources, $destinations)
{
  if (count($sources) == 0)
  {
    return false;
  }

  $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id IN ('.implode(',', $sources).')
;';
  $images = array_from_query($query, 'image_id');

  associate_images_to_categories($images, $destinations);
}

/**
 * Refer main Piwigo URLs (currently PHPWG_DOMAIN domain)
 *
 * @param void
 * @return array like $conf['links']
 */
function pwg_URL()
{
  $urls = array(
    'HOME'       => 'http://'.PHPWG_DOMAIN,
    'WIKI'       => 'http://'.PHPWG_DOMAIN.'/doc',
    'DEMO'       => 'http://'.PHPWG_DOMAIN.'/demo',
    'FORUM'      => 'http://'.PHPWG_DOMAIN.'/forum',
    'BUGS'       => 'http://'.PHPWG_DOMAIN.'/bugs',
    'EXTENSIONS' => 'http://'.PHPWG_DOMAIN.'/ext',
    );
  return $urls;
}

/**
 * Invalidates cahed data (permissions and category counts) for all users.
 */
function invalidate_user_cache($full = true)
{
  if ($full)
  {
    $query = '
TRUNCATE TABLE '.USER_CACHE_CATEGORIES_TABLE.';';
    pwg_query($query);
    $query = '
TRUNCATE TABLE '.USER_CACHE_TABLE.';';
    pwg_query($query);
  }
  else
  {
    $query = '
UPDATE '.USER_CACHE_TABLE.'
  SET need_update = \'true\';';
    pwg_query($query);
  }
  trigger_action('invalidate_user_cache', $full);
}

/**
 * adds the caracter set to a create table sql query.
 * all CREATE TABLE queries must call this function
 * @param string query - the sql query
 */
function create_table_add_character_set($query)
{
  defined('DB_CHARSET') or fatal_error('create_table_add_character_set DB_CHARSET undefined');
  if ('DB_CHARSET'!='')
  {
    if ( version_compare(mysql_get_server_info(), '4.1.0', '<') )
    {
      return $query;
    }
    $charset_collate = " DEFAULT CHARACTER SET ".DB_CHARSET;
    if (DB_COLLATE!='')
    {
      $charset_collate .= " COLLATE ".DB_COLLATE;
    }
    if ( is_array($query) )
    {
      foreach( $query as $id=>$q)
      {
        $q=trim($q);
        $q=trim($q, ';');
        if (preg_match('/^CREATE\s+TABLE/i',$q))
        {
          $q.=$charset_collate;
        }
        $q .= ';';
        $query[$id] = $q;
      }
    }
    else
    {
      $query=trim($query);
      $query=trim($query, ';');
      if (preg_match('/^CREATE\s+TABLE/i',$query))
      {
        $query.=$charset_collate;
      }
      $query .= ';';
    }
  }
  return $query;
}

/**
 * Returns array use on template with html_options method
 * @param Min and Max access to use
 * @return array of user access level
 */
function get_user_access_level_html_options($MinLevelAccess = ACCESS_FREE, $MaxLevelAccess = ACCESS_CLOSED)
{
  $tpl_options = array();
  for ($level = $MinLevelAccess; $level <= $MaxLevelAccess; $level++)
  {
    $tpl_options[$level] = l10n(sprintf('ACCESS_%d', $level));
  }
  return $tpl_options;
}

/**
 * returns a list of templates currently available in template-extension
 * Each .tpl file is extracted from template-extension.
 * @return array
 */
function get_extents($start='')
{
  if ($start == '') { $start = './template-extension'; }
  $dir = opendir($start);
  $extents = array();

  while (($file = readdir($dir)) !== false)
  {
    if ( $file == '.' or $file == '..' or $file == '.svn') continue;
    $path = $start . '/' . $file;
    if (is_dir($path))
    {
      $extents = array_merge($extents, get_extents($path));
    }
    elseif ( !is_link($path) and file_exists($path)
            and get_extension($path) == 'tpl' )
    {
      $extents[] = substr($path, 21);
    }
  }
  return $extents;
}

function create_tag($tag_name)
{
  // does the tag already exists?
  $query = '
SELECT id
  FROM '.TAGS_TABLE.'
  WHERE name = \''.$tag_name.'\'
;';
  $existing_tags = array_from_query($query, 'id');

  if (count($existing_tags) == 0)
  {
    mass_inserts(
      TAGS_TABLE,
      array('name', 'url_name'),
      array(
        array(
          'name' => $tag_name,
          'url_name' => str2url($tag_name),
          )
        )
      );

    $inserted_id = pwg_db_insert_id(TAGS_TABLE);

    return array(
      'info' => sprintf(
        l10n('Tag "%s" was added'),
        stripslashes($tag_name)
        ),
      'id' => $inserted_id,
      );
  }
  else
  {
    return array(
      'error' => sprintf(
        l10n('Tag "%s" already exists'),
        stripslashes($tag_name)
        )
      );
  }
}


/**
 * Retrieve data from external URL
 *
 * @param string $src: URL
 * @param global $dest: can be a file ressource or string
 * @return bool
 */
function fetchRemote($src, &$dest, $get_data=array(), $post_data=array(), $user_agent='Piwigo', $step=0)
{
  // Try to retrieve data from local file?
  if (!url_is_remote($src))
  {
    $content = @file_get_contents($src);
    if ($content !== false)
    {
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true;
    }
    else
    {
      return false;
    }
  }

  // After 3 redirections, return false
  if ($step > 3) return false;

  // Initialization
  $method  = empty($post_data) ? 'GET' : 'POST';
  $request = empty($post_data) ? '' : http_build_query($post_data, '', '&');
  if (!empty($get_data))
  {
    $src .= strpos($src, '?') === false ? '?' : '&';
    $src .= http_build_query($get_data, '', '&');
  }

  // Initialize $dest
  is_resource($dest) or $dest = '';

  // Try curl to read remote file
  if (function_exists('curl_init'))
  {
    $ch = @curl_init();
    @curl_setopt($ch, CURLOPT_URL, $src);
    @curl_setopt($ch, CURLOPT_HEADER, 1);
    @curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($method == 'POST')
    {
      @curl_setopt($ch, CURLOPT_POST, 1);
      @curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    }
    $content = @curl_exec($ch);
    $header_length = @curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
    @curl_close($ch);
    if ($content !== false and $status >= 200 and $status < 400)
    {
      if (preg_match('/Location:\s+?(.+)/', substr($content, 0, $header_length), $m))
      {
        return fetchRemote($m[1], $dest, array(), array(), $user_agent, $step+1);
      }
      $content = substr($content, $header_length);
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true;
    }
  }

  // Try file_get_contents to read remote file
  if (ini_get('allow_url_fopen'))
  {
    $opts = array(
      'http' => array(
        'method' => $method,
        'user_agent' => $user_agent,
      )
    );
    if ($method == 'POST')
    {
      $opts['http']['content'] = $request;
    }
    $context = @stream_context_create($opts);
    $content = @file_get_contents($src, false, $context);
    if ($content !== false)
    {
      is_resource($dest) ? @fwrite($dest, $content) : $dest = $content;
      return true;
    }
  }

  // Try fsockopen to read remote file
  $src = parse_url($src);
  $host = $src['host'];
  $path = isset($src['path']) ? $src['path'] : '/';
  $path .= isset($src['query']) ? '?'.$src['query'] : '';

  if (($s = @fsockopen($host,80,$errno,$errstr,5)) === false)
  {
    return false;
  }

  $http_request  = $method." ".$path." HTTP/1.0\r\n";
  $http_request .= "Host: ".$host."\r\n";
  if ($method == 'POST')
  {
    $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
    $http_request .= "Content-Length: ".strlen($request)."\r\n";
  }
  $http_request .= "User-Agent: ".$user_agent."\r\n";
  $http_request .= "Accept: */*\r\n";
  $http_request .= "\r\n";
  $http_request .= $request;

  fwrite($s, $http_request);

  $i = 0;
  $in_content = false;
  while (!feof($s))
  {
    $line = fgets($s);

    if (rtrim($line,"\r\n") == '' && !$in_content)
    {
      $in_content = true;
      $i++;
      continue;
    }
    if ($i == 0)
    {
      if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/',rtrim($line,"\r\n"), $m))
      {
        fclose($s);
        return false;
      }
      $status = (integer) $m[2];
      if ($status < 200 || $status >= 400)
      {
        fclose($s);
        return false;
      }
    }
    if (!$in_content)
    {
      if (preg_match('/Location:\s+?(.+)$/',rtrim($line,"\r\n"),$m))
      {
        fclose($s);
        return fetchRemote(trim($m[1]),$dest,array(),array(),$user_agent,$step+1);
      }
      $i++;
      continue;
    }
    is_resource($dest) ? @fwrite($dest, $line) : $dest .= $line;
    $i++;
  }
  fclose($s);
  return true;
}


/**
 * returns the groupname corresponding to the given group identifier if
 * exists
 *
 * @param int group_id
 * @return mixed
 */
function get_groupname($group_id)
{
  $query = '
SELECT name
  FROM '.GROUPS_TABLE.'
  WHERE id = '.intval($group_id).'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) > 0)
  {
    list($groupname) = pwg_db_fetch_row($result);
  }
  else
  {
    return false;
  }

  return $groupname;
}

/**
 * returns the username corresponding to the given user identifier if exists
 *
 * @param int user_id
 * @return mixed
 */
function get_username($user_id)
{
  global $conf;

  $query = '
SELECT '.$conf['user_fields']['username'].'
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.intval($user_id).'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) > 0)
  {
    list($username) = pwg_db_fetch_row($result);
  }
  else
  {
    return false;
  }

  return stripslashes($username);
}

function get_newsletter_subscribe_base_url($language) {
  $subscribe_domain = 'piwigo.org';

  $domain_of = array(
    'fr_FR' => 'fr.piwigo.org',
    'it_IT' => 'it.piwigo.org',
    'de_DE' => 'de.piwigo.org',
    'es_ES' => 'es.piwigo.org',
    'zh_CN' => 'cn.piwigo.org',
    'pl_PL' => 'pl.piwigo.org',
    'hu_HU' => 'hu.piwigo.org',
    'ru_RU' => 'ru.piwigo.org',
	'nl_NL' => 'nl.piwigo.org',
    );

  if (isset($domain_of[$language])) {
    $subscribe_domain = $domain_of[$language];
  }

  return 'http://'.$subscribe_domain.'/announcement/subscribe/';
}

/**
 * Accordion menus need to know which section to open by default when
 * loading the page
 */
function get_active_menu($menu_page)
{
  global $page;

  if (isset($page['active_menu']))
  {
    return $page['active_menu'];
  }

  switch ($menu_page)
  {
    case 'photos_add':
    case 'rating':
    case 'tags':
    case 'picture_modify':
    case 'batch_manager':
      return 0;

    case 'cat_list':
    case 'cat_modify':
    case 'cat_move':
    case 'cat_options':
    case 'cat_perm':
    case 'permalinks':
      return 1;

    case 'user_list':
    case 'user_perm':
    case 'group_list':
    case 'group_perm':
    case 'notification_by_mail':
      return 2;

    case 'plugins':
    case 'plugin':
      return 3;

    case 'site_manager':
    case 'site_update':
    case 'stats':
    case 'history':
    case 'maintenance':
    case 'thumbnail':
    case 'comments':
    case 'updates':
      return 4;

    case 'configuration':
    case 'extend_for_templates':
    case 'menubar':
    case 'themes':
    case 'theme':
    case 'languages':
      return 5;
  }
  return 0;
}

function get_taglist($query)
{
  $result = pwg_query($query);
  
  $taglist = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    if (preg_match_all('#\[lang=(.*?)\](.*?)\[/lang\]#is', $row['tag_name'], $matches))
    {
      foreach ($matches[2] as $tag_name)
      {
        array_push(
          $taglist,
          array(
            'name' => trigger_event('render_tag_name', $tag_name),
            'id' => '~~'.$row['tag_id'].'~~',
            )
          );
      }

      $row['tag_name'] = preg_replace('#\[lang=(.*?)\](.*?)\[/lang\]#is', null, $row['tag_name']);
    }
    
    if (strlen($row['tag_name']) > 0)
    {
      array_push(
        $taglist,
        array(
          'name' => trigger_event('render_tag_name', $row['tag_name']),
          'id' => '~~'.$row['tag_id'].'~~',
          )
        );
    }
  }
  
  $cmp = create_function('$a,$b', 'return strcasecmp($a["name"], $b["name"]);');
  usort($taglist, $cmp);

  return $taglist;
}

function get_tag_ids($raw_tags)
{
  // In $raw_tags we receive something like array('~~6~~', '~~59~~', 'New
  // tag', 'Another new tag') The ~~34~~ means that it is an existing
  // tag. I've added the surrounding ~~ to permit creation of tags like "10"
  // or "1234" (numeric characters only)

  $tag_ids = array();
  $raw_tags = explode(',',$raw_tags);

  foreach ($raw_tags as $raw_tag)
  {
    if (preg_match('/^~~(\d+)~~$/', $raw_tag, $matches))
    {
      array_push($tag_ids, $matches[1]);
    }
    else
    {
      // we have to create a new tag
      array_push(
        $tag_ids,
        tag_id_from_tag_name($raw_tag)
        );
    }
  }

  return $tag_ids;
}

/** returns the argument_ids array with new sequenced keys based on related
 * names. Sequence is not case sensitive.
 * Warning: By definition, this function breaks original keys
 */
function order_by_name($element_ids,$name)
{
  $ordered_element_ids = array();
  foreach ($element_ids as $k_id => $element_id)
  {
    $key = strtolower($name[$element_id]) .'-'. $name[$element_id] .'-'. $k_id;
    $ordered_element_ids[$key] = $element_id;
  }
  ksort($ordered_element_ids);
  return $ordered_element_ids;
}

function add_permission_on_category($category_ids, $user_ids)
{
  // array-ify categories and users
  if (!is_array($category_ids))
  {
    $category_ids = array($category_ids);
  }

  if (!is_array($user_ids))
  {
    $user_ids = array($user_ids);
  }

  // check for emptiness
  if (count($category_ids) == 0 or count($user_ids) == 0)
  {
    return;
  }
  
  // make sure categories are private and select uppercats
  $query = '
SELECT
    id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', get_uppercat_ids($category_ids)).')
    AND status = \'private\'
;';
  $private_uppercats = array_from_query($query, 'id');

  if (count($private_uppercats) == 0)
  {
    return;
  }
  
  // We must not reinsert already existing lines in user_access table
  $granteds = array();
  foreach ($private_uppercats as $cat_id)
  {
    $granteds[$cat_id] = array();
  }
  
  $query = '
SELECT
    user_id,
    cat_id
  FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id IN ('.implode(',', $private_uppercats).')
    AND user_id IN ('.implode(',', $user_ids).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($granteds[$row['cat_id']], $row['user_id']);
  }

  $inserts = array();
  
  foreach ($private_uppercats as $cat_id)
  {
    $grant_to_users = array_diff($user_ids, $granteds[$cat_id]);
    
    foreach ($grant_to_users as $user_id)
    {
      array_push(
        $inserts,
        array(
          'user_id' => $user_id,
          'cat_id' => $cat_id
          )
        );
    }
  }

  if (count($inserts) > 0)
  {
    mass_inserts(USER_ACCESS_TABLE, array_keys($inserts[0]), $inserts);
  }
}


function get_admins($include_webmaster=true)
{
  $status_list = array('admin');

  if ($include_webmaster)
  {
    $status_list[] = 'webmaster';
  }
  
  $query = '
SELECT
    user_id
  FROM '.USER_INFOS_TABLE.'
  WHERE status in (\''.implode("','", $status_list).'\')
;';

  return array_from_query($query, 'user_id');
}
?>