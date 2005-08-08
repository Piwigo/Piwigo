<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
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

include(PHPWG_ROOT_PATH.'admin/include/functions_metadata.php');

$tab_ext_create_TN = array ( 'jpg', 'png', 'JPG', 'PNG' );

// is_image returns true if the given $filename (including the path) is a
// picture according to its format and its extension.
// As GD library can only generate pictures from jpeg and png files, if you
// ask if the filename is an image for thumbnail creation (second parameter
// set to true), the only authorized formats are jpeg and png.
function is_image( $filename, $create_thumbnail = false )
{
  global $conf, $tab_ext_create_TN;

  if (is_file($filename)
      and in_array(get_extension($filename), $conf['picture_ext']))
  {
    $size = getimagesize( $filename );
    // $size[2] == 1 means GIF
    // $size[2] == 2 means JPG
    // $size[2] == 3 means PNG
    if ( !$create_thumbnail )
    {
      if ( in_array( get_extension( $filename ), $conf['picture_ext'] )
           and ( $size[2] == 1 or $size[2] == 2 or $size[2] == 3 ) )
      {
        return true;
      }
    }
    else
    {
      if ( in_array( get_extension( $filename ), $tab_ext_create_TN )
           and ( $size[2] == 2 or $size[2] == 3 ) )
      {
        return true;
      }
    }
  }
  return false;
}

/**
 * returns an array with all picture files according to $conf['file_ext']
 *
 * @param string $dir
 * @return array
 */
function get_pwg_files($dir)
{
  global $conf;

  $pictures = array();
  if ($opendir = opendir($dir))
  {
    while ($file = readdir($opendir))
    {
      if (in_array(get_extension($file), $conf['file_ext']))
      {
        array_push($pictures, $file);
      }
    }
  }
  return $pictures;
}

/**
 * returns an array with all thumbnails according to $conf['picture_ext']
 * and $conf['prefix_thumbnail']
 *
 * @param string $dir
 * @return array
 */
function get_thumb_files($dir)
{
  global $conf;

  $prefix_length = strlen($conf['prefix_thumbnail']);
  
  $thumbnails = array();
  if ($opendir = @opendir($dir.'/thumbnail'))
  {
    while ($file = readdir($opendir))
    {
      if (in_array(get_extension($file), $conf['picture_ext'])
          and substr($file, 0, $prefix_length) == $conf['prefix_thumbnail'])
      {
        array_push($thumbnails, $file);
      }
    }
  }
  return $thumbnails;
}

/**
 * returns an array with representative picture files of a directory
 * according to $conf['picture_ext']
 *
 * @param string $dir
 * @return array
 */
function get_representative_files($dir)
{
  global $conf;

  $pictures = array();
  if ($opendir = @opendir($dir.'/pwg_representative'))
  {
    while ($file = readdir($opendir))
    {
      if (in_array(get_extension($file), $conf['picture_ext']))
      {
        array_push($pictures, $file);
      }
    }
  }
  return $pictures;
}

function TN_exists( $dir, $file )
{
  global $conf;

  $filename = get_filename_wo_extension( $file );
  foreach ( $conf['picture_ext'] as $ext ) {
    $test = $dir.'/thumbnail/'.$conf['prefix_thumbnail'].$filename.'.'.$ext;
    if ( is_file ( $test ) )
    {
      return $ext;
    }
  }
  return false;
}
	

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
  while ($row = mysql_fetch_array($result))
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
//    - all the elements of the category (delete_elements, see further)
//    - all the links between elements and this category
//    - all the restrictions linked to the category
// The function works recursively.
function delete_categories($ids)
{
  global $counts;

  if (count($ids) == 0)
  {
    return;
  }

  // add sub-category ids to the given ids : if a category is deleted, all
  // sub-categories must be so
  $ids = get_subcat_ids($ids);
  
  // destruction of all the related elements
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN (
'.wordwrap(implode(', ', $ids), 80, "\n").')
;';
  $result = pwg_query($query);
  $element_ids = array();
  while ($row = mysql_fetch_array($result))
  {
    array_push($element_ids, $row['id']);
  }
  delete_elements($element_ids);

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

  if (isset($counts['del_categories']))
  {
    $counts['del_categories']+= count($ids);
  }
}

// The function delete_elements deletes the elements identified by the
// (numeric) values of the array $ids. It also deletes (in the database) :
//    - all the comments related to elements
//    - all the links between categories and elements
//    - all the favorites associated to elements
function delete_elements($ids)
{
  global $counts;

  if (count($ids) == 0)
  {
    return;
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

  if (isset($counts['del_elements']))
  {
    $counts['del_elements']+= count($ids);
  }
}

// The delete_user function delete a user identified by the $user_id
// It also deletes :
//     - all the access linked to this user
//     - all the links to any group
//     - all the favorites linked to this user
//     - all sessions linked to this user
//     - calculated permissions linked to the user
function delete_user($user_id)
{
  global $conf;
  
  // destruction of the access linked to the user
  $query = '
DELETE FROM '.USER_ACCESS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  // destruction of the group links for this user
  $query = '
DELETE FROM '.USER_GROUP_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  // destruction of the favorites associated with the user
  $query = '
DELETE FROM '.FAVORITES_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  // destruction of the sessions linked with the user
  $query = '
DELETE FROM '.SESSIONS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  // deletion of calculated permissions linked to the user
  $query = '
DELETE FROM '.USER_FORBIDDEN_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  // deletion of phpwebgallery specific informations
  $query = '
DELETE FROM '.USER_INFOS_TABLE.'
  WHERE user_id = '.$user_id.'
;';
  pwg_query($query);

  // destruction of the user
  $query = '
DELETE FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['id'].' = '.$user_id.'
;';
  pwg_query($query);
}

/**
 * updates calculated informations about a set of categories : date_last and
 * nb_images. It also verifies that the representative picture is really
 * linked to the category. Optionnaly recursive.
 *
 * @param mixed category id
 * @param boolean recursive
 * @returns void
 */
function update_category($ids = 'all', $recursive = false)
{
  // retrieving all categories to update
  $cat_ids = array();
  
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE;
  if (is_array($ids))
  {
    if ($recursive)
    {
      foreach ($ids as $num => $id)
      {
        if ($num == 0)
        {
          $query.= '
  WHERE ';
        }
        else
        {
          $query.= '
  OR    ';
        }
        $query.= 'uppercats REGEXP \'(^|,)'.$id.'(,|$)\'';
      }
    }
    else
    {
      $query.= '
  WHERE id IN ('.wordwrap(implode(', ', $ids), 80, "\n").')';
    }
  }
  $query.= '
;';
  $result = pwg_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push($cat_ids, $row['id']);
  }
  $cat_ids = array_unique($cat_ids);

  if (count($cat_ids) == 0)
  {
    return false;
  }
  
  // calculate informations about categories retrieved
  $query = '
SELECT category_id,
       COUNT(image_id) AS nb_images,
       MAX(date_available) AS date_last
  FROM '.IMAGES_TABLE.' INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id
  WHERE category_id IN ('.wordwrap(implode(', ', $cat_ids), 80, "\n").')
  GROUP BY category_id
;';
  $result = pwg_query($query);
  $datas = array();
  $query_ids = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push($query_ids, $row['category_id']);
    array_push($datas, array('id' => $row['category_id'],
                             'date_last' => $row['date_last'],
                             'nb_images' => $row['nb_images']));
  }
  // if all links between a category and elements have disappeared, no line
  // is returned but the update must be done !
  foreach (array_diff($cat_ids, $query_ids) as $id)
  {
    array_push($datas, array('id' => $id, 'nb_images' => 0));
  }
  
  $fields = array('primary' => array('id'),
                  'update'  => array('date_last', 'nb_images'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);

  $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE nb_images = 0
;';
  pwg_query($query);
  
  if (count($cat_ids) > 0)
  {
    $categories = array();
    // find all categories where the setted representative is not possible
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.' LEFT JOIN '.IMAGE_CATEGORY_TABLE.'
    ON id = category_id AND representative_picture_id = image_id
  WHERE representative_picture_id IS NOT NULL
    AND id IN ('.wordwrap(implode(', ', $cat_ids), 80, "\n").')
    AND category_id IS NULL
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($categories, $row['id']);
    }
    // find categories with elements and with no representant
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE representative_picture_id IS NULL
    AND nb_images != 0
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      array_push($categories, $row['id']);
    }

    $categories = array_unique($categories);
    set_random_representant($categories);
  }
}

function check_date_format( $date )
{
  // date arrives at this format : DD/MM/YYYY
  @list($day,$month,$year) = explode( '/', $date );
  return @checkdate( $month, $day, $year );
}

function date_convert( $date )
{
  // date arrives at this format : DD/MM/YYYY
  // It must be transformed in YYYY-MM-DD
  list($day,$month,$year) = explode( '/', $date );
  return $year.'-'.$month.'-'.$day;
}

function date_convert_back( $date )
{
  // date arrives at this format : YYYY-MM-DD
  // It must be transformed in DD/MM/YYYY
  if ( $date != '' )
  {
    list($year,$month,$day) = explode( '-', $date );
    return $day.'/'.$month.'/'.$year;
  }
  else
  {
    return '';
  }
}

// get_keywords returns an array with relevant keywords found in the string
// given in argument. Keywords must be separated by comma in this string.
// keywords must :
//   - be longer or equal to 3 characters
//   - not contain ', " or blank characters
//   - unique in the string ("test,test" -> "test")
function get_keywords( $keywords_string )
{
  $keywords = array();

  $candidates = explode( ',', $keywords_string );
  foreach ( $candidates as $candidate ) {
    if ( strlen($candidate) >= 3 and !preg_match( '/(\'|"|\s)/', $candidate ) )
      array_push( $keywords, $candidate );
  }

  return array_unique( $keywords );
}

/**
 * returns an array containing sub-directories which can be a category
 *
 * directories nammed "thumbnail", "pwg_high" or "pwg_representative" are
 * omitted
 *
 * @param string $basedir
 * @return array
 */
function get_category_directories( $basedir )
{
  $sub_dirs = array();
  
  if ( $opendir = opendir( $basedir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ($file != '.'
          and $file != '..'
          and $file != 'thumbnail'
          and $file != 'pwg_high'
          and $file != 'pwg_representative'
          and is_dir($basedir.'/'.$file))
      {
        array_push( $sub_dirs, $file );
      }
    }
  }
  return $sub_dirs;
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
    }
  }

  return $dirs;
}

/**
 * inserts multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array inserts
 * @return void
 */
function mass_inserts($table_name, $dbfields, $datas)
{
  // inserts all found categories
  $query = '
INSERT INTO '.$table_name.'
  ('.implode(',', $dbfields).')
   VALUES';
  foreach ($datas as $insert_id => $insert)
  {
    $query.= '
  ';
    if ($insert_id > 0)
    {
      $query.= ',';
    }
    $query.= '(';
    foreach ($dbfields as $field_id => $dbfield)
    {
      if ($field_id > 0)
      {
        $query.= ',';
      }
      
      if (!isset($insert[$dbfield]) or $insert[$dbfield] == '')
      {
        $query.= 'NULL';
      }
      else
      {
        $query.= "'".$insert[$dbfield]."'";
      }
    }
    $query.=')';
  }
  $query.= '
;';
  pwg_query($query);
}

/**
 * updates multiple lines in a table
 *
 * @param string table_name
 * @param array dbfields
 * @param array datas
 * @return void
 */
function mass_updates($tablename, $dbfields, $datas)
{
  // depending on the MySQL version, we use the multi table update or N
  // update queries
  $query = 'SELECT VERSION() AS version;';
  list($mysql_version) = mysql_fetch_array(pwg_query($query));
  if (count($datas) < 10 or version_compare($mysql_version, '4.0.4') < 0)
  {
    // MySQL is prior to version 4.0.4, multi table update feature is not
    // available
    foreach ($datas as $data)
    {
      $query = '
UPDATE '.$tablename.'
  SET ';
      foreach ($dbfields['update'] as $num => $key)
      {
        if ($num >= 1)
        {
          $query.= ",\n      ";
        }
        $query.= $key.' = ';
        if (isset($data[$key]))
        {
          $query.= '\''.$data[$key].'\'';
        }
        else
        {
          $query.= 'NULL';
        }
      }
      $query.= '
  WHERE ';
      foreach ($dbfields['primary'] as $num => $key)
      {
        if ($num > 1)
        {
          $query.= ' AND ';
        }
        $query.= $key.' = \''.$data[$key].'\'';
      }
      $query.= '
;';
      pwg_query($query);
    }
  }
  else
  {
    // creation of the temporary table
    $query = '
DESCRIBE '.$tablename.'
;';
    $result = pwg_query($query);
    $columns = array();
    $all_fields = array_merge($dbfields['primary'], $dbfields['update']);
    while ($row = mysql_fetch_array($result))
    {
      if (in_array($row['Field'], $all_fields))
      {
        $column = $row['Field'];
        $column.= ' '.$row['Type'];
        if (!isset($row['Null']) or $row['Null'] == '')
        {
          $column.= ' NOT NULL';
        }
        if (isset($row['Default']))
        {
          $column.= " default '".$row['Default']."'";
        }
        array_push($columns, $column);
      }
    }
    
    $temporary_tablename = $tablename.'_'.micro_seconds();
    
    $query = '
CREATE TABLE '.$temporary_tablename.'
(
'.implode(",\n", $columns).',
PRIMARY KEY (id)
)
;';
    pwg_query($query);
    mass_inserts($temporary_tablename, $all_fields, $datas);
    // update of images table by joining with temporary table
    $query = '
UPDATE '.$tablename.' AS t1, '.$temporary_tablename.' AS t2
  SET '.implode("\n    , ",
                array_map(
                  create_function('$s', 'return "t1.$s = t2.$s";')
                  , $dbfields['update'])).'
  WHERE '.implode("\n    AND ",
                array_map(
                  create_function('$s', 'return "t1.$s = t2.$s";')
                  , $dbfields['primary'])).'
;';
    pwg_query($query);
    $query = '
DROP TABLE '.$temporary_tablename.'
;';
    pwg_query($query);
  }
}

/**
 * updates the global_rank of categories under the given id_uppercat
 *
 * @param int id_uppercat
 * @return void
 */
function update_global_rank($id_uppercat = 'all')
{
  $query = '
SELECT id,rank
  FROM '.CATEGORIES_TABLE.'
;';
  $result = pwg_query($query);
  $ranks_array = array();
  while ($row = mysql_fetch_array($result))
  {
    $ranks_array[$row['id']] = $row['rank'];
  }

  // which categories to update ?
  $uppercats_array = array();

  $query = '
SELECT id,uppercats
  FROM '.CATEGORIES_TABLE;
  if (is_numeric($id_uppercat))
  {
    $query.= '
  WHERE uppercats REGEXP \'(^|,)'.$id_uppercat.'(,|$)\'
    AND id != '.$id_uppercat.'
';
  }
  $query.= '
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
  {
    $uppercats_array[$row['id']] =  $row['uppercats'];
  }
  
  $datas = array();
  foreach ($uppercats_array as $id => $uppercats)
  {
    $data = array();
    $data['id'] = $id;
    $global_rank = preg_replace('/(\d+)/e',
                                "\$ranks_array['$1']",
                                str_replace(',', '.', $uppercats));
    $data['global_rank'] = $global_rank;
    array_push($datas, $data);
  }

  $fields = array('primary' => array('id'), 'update' => array('global_rank'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
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
    return false;
  }

  // unlocking a category => all its parent categories become unlocked
  if ($value == 'true')
  {
    $uppercats = get_uppercat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'true\'
  WHERE id IN ('.implode(',', $uppercats).')
;';
    pwg_query($query);
  }
  // locking a category   => all its child categories become locked
  if ($value == 'false')
  {
    $subcats = get_subcat_ids($categories);
    $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET visible = \'false\'
  WHERE id IN ('.implode(',', $subcats).')
;';
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
  WHERE id IN ('.implode(',', $subcats).')
;';
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
  while ($row = mysql_fetch_array($result))
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
  ORDER BY RAND()
  LIMIT 0,1
;';
    list($representative) = mysql_fetch_array(pwg_query($query));
    $data = array('id' => $category_id,
                  'representative_picture_id' => $representative);
    array_push($datas, $data);
  }

  $fields = array('primary' => array('id'),
                  'update' => array('representative_picture_id'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
}

/**
 * order categories (update categories.rank and global_rank database fields)
 *
 * the purpose of this function is to give a rank for all categories
 * (insides its sub-category), even the newer that have none at te
 * beginning. For this, ordering function selects all categories ordered by
 * rank ASC then name ASC for each uppercat.
 *
 * @returns void
 */
function ordering()
{
  $current_rank = 0;
  $current_uppercat = '';
		
  $query = '
SELECT id, if(id_uppercat is null,\'\',id_uppercat) AS id_uppercat
  FROM '.CATEGORIES_TABLE.'
  ORDER BY id_uppercat,rank,name
;';
  $result = pwg_query($query);
  $datas = array();
  while ($row = mysql_fetch_array($result))
  {
    if ($row['id_uppercat'] != $current_uppercat)
    {
      $current_rank = 0;
      $current_uppercat = $row['id_uppercat'];
    }
    $data = array('id' => $row['id'], 'rank' => ++$current_rank);
    array_push($datas, $data);
  }

  $fields = array('primary' => array('id'), 'update' => array('rank'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);
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
  $result = pwg_query($query);
  $cat_dirs = array();
  while ($row = mysql_fetch_array($result))
  {
    $cat_dirs[$row['id']] = $row['dir'];
  }

  // caching galleries_url
  $query = '
SELECT id, galleries_url
  FROM '.SITES_TABLE.'
;';
  $result = pwg_query($query);
  $galleries_url = array();
  while ($row = mysql_fetch_array($result))
  {
    $galleries_url[$row['id']] = $row['galleries_url'];
  }

  // categories : id, site_id, uppercats
  $categories = array();
  
  $query = '
SELECT id, uppercats, site_id
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN (
'.wordwrap(implode(', ', $cat_ids), 80, "\n").')
;';
  $result = pwg_query($query);
  while ($row = mysql_fetch_array($result))
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
 * stupidly returns the current microsecond since Unix epoch
 */
function micro_seconds()
{
  $t1 = explode(' ', microtime());
  $t2 = explode('.', $t1[0]);
  $t2 = $t1[1].substr($t2[1], 0, 6);
  return $t2;
}

/**
 * compares and synchronizes USERS_TABLE and USER_INFOS_TABLE : each user in
 * USERS_TABLE must be present in USER_INFOS_TABLE.
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
    $inserts = array();

    list($dbnow) = mysql_fetch_row(pwg_query('SELECT NOW();'));

    foreach ($to_create as $user_id)
    {
      $insert = array();
      $insert['user_id'] = $user_id;
      $insert['status'] = 'guest';
      $insert['template'] = $conf['default_template'];
      $insert['nb_image_line'] = $conf['nb_image_line'];
      $insert['nb_line_page'] = $conf['nb_line_page'];
      $insert['language'] = $conf['default_language'];
      $insert['recent_period'] = $conf['recent_period'];
      $insert['feed_id'] = find_available_feed_id();
      $insert['expand'] = boolean_to_string($conf['auto_expand']);
      $insert['show_nb_comments'] =
        boolean_to_string($conf['show_nb_comments']);
      $insert['maxwidth'] = $conf['default_maxwidth'];
      $insert['maxheight'] = $conf['default_maxheight'];
      $insert['registration_date'] = $dbnow;

      array_push($inserts, $insert);
    }

    mass_inserts(USER_INFOS_TABLE,
                 array_keys($inserts[0]),
                 $inserts);
  }

  // users present in $infos_users and not in $base_users must be deleted
  $to_delete = array_diff($infos_users, $base_users);

  if (count($to_delete) > 0)
  {
    $query = '
DELETE
  FROM '.USER_INFOS_TABLE.'
  WHERE user_id in ('.implode(',', $to_delete).')
;';
    pwg_query($query);
  } 
}
?>
