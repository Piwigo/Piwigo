<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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
  
  // destruction of all the related elements
  $query = '
SELECT id
  FROM '.IMAGES_TABLE.'
  WHERE storage_category_id IN ('.implode(',', $ids).')
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
  WHERE category_id IN ('.implode(',', $ids).')
;';
  pwg_query($query);

  // destruction of the access linked to the category
  $query = '
DELETE FROM '.USER_ACCESS_TABLE.'
  WHERE cat_id IN ('.implode(',', $ids).')
;';
  pwg_query($query);
  $query = '
DELETE FROM '.GROUP_ACCESS_TABLE.'
  WHERE cat_id IN ('.implode(',', $ids).')
;';
  pwg_query($query);

  // destruction of the sub-categories
  $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.'
  WHERE id_uppercat IN ('.implode(',', $ids).')
;';
  $result = pwg_query($query);
  $subcat_ids = array();
  while($row = mysql_fetch_array($result))
  {
    array_push($subcat_ids, $row['id']);
  }
  if (count($subcat_ids) > 0)
  {
    delete_categories($subcat_ids);
  }

  // destruction of the category
  $query = '
DELETE FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', $ids).')
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
//     - all categories informations linked to this user
function delete_user( $user_id )
{
  // destruction of the access linked to the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_access';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  pwg_query( $query );

  // destruction of the group links for this user
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  pwg_query( $query );

  // destruction of the favorites associated with the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  pwg_query( $query );

  // destruction of the sessions linked with the user
  $query = 'DELETE FROM '.PREFIX_TABLE.'sessions';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  pwg_query( $query );

  // destruction of the user
  $query = 'DELETE FROM '.USERS_TABLE;
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  pwg_query( $query );
}

// delete_group deletes a group identified by its $group_id.
// It also deletes :
//     - all the access linked to this group
//     - all the links between this group and any user
function delete_group( $group_id )
{
  // destruction of the access linked to the group
  $query = 'DELETE FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  pwg_query( $query );

  // synchronize all users linked to the group
  synchronize_group( $group_id );

  // destruction of the users links for this group
  $query = 'DELETE FROM '.PREFIX_TABLE.'user_group';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  pwg_query( $query );

  // destruction of the group
  $query = 'DELETE FROM '.PREFIX_TABLE.'groups';
  $query.= ' WHERE id = '.$group_id;
  $query.= ';';
  pwg_query( $query );
}

// The check_favorites function deletes all the favorites of a user if he is
// not allowed to see them (the category or an upper category is restricted
// or invisible)
function check_favorites( $user_id )
{
  $query = 'SELECT status,forbidden_categories';
  $query.= ' FROM '.USERS_TABLE;
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );
  $status = $row['status'];
  // retrieving all the restricted categories for this user
  if ( isset( $row['forbidden_categories'] ) )
    $restricted_cat = explode( ',', $row['forbidden_categories'] );
  else
    $restricted_cat = array();
  // retrieving all the favorites for this user and comparing their
  // categories to the restricted categories
  $query = 'SELECT image_id';
  $query.= ' FROM '.PREFIX_TABLE.'favorites';
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  $result = pwg_query ( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // for each picture, we have to check all the categories it belongs
    // to. Indeed if a picture belongs to category_1 and category_2 and that
    // category_2 is not restricted to the user, he can have the picture as
    // favorite.
    $query = 'SELECT DISTINCT(category_id) as category_id';
    $query.= ' FROM '.PREFIX_TABLE.'image_category';
    $query.= ' WHERE image_id = '.$row['image_id'];
    $query.= ';';
    $picture_result = pwg_query( $query );
    $picture_cat = array();
    while ( $picture_row = mysql_fetch_array( $picture_result ) )
    {
      array_push( $picture_cat, $picture_row['category_id'] );
    }
    if ( count( array_diff( $picture_cat, $restricted_cat ) ) == 0 )
    {
      $query = 'DELETE FROM '.PREFIX_TABLE.'favorites';
      $query.= ' WHERE image_id = '.$row['image_id'];
      $query.= ' AND user_id = '.$user_id;
      $query.= ';';
      pwg_query( $query );
    }
  }
}

/**
 * updates calculated informations about a category : date_last and
 * nb_images. It also verifies that the representative picture is really
 * linked to the category. Recursive.
 *
 * @param mixed category id
 * @returns void
 */
function update_category($id = 'all')
{
  $cat_ids = array();
  
  $query = '
SELECT category_id, COUNT(image_id) AS count, max(date_available) AS date_last
  FROM '.IMAGES_TABLE.'
    INNER JOIN '.IMAGE_CATEGORY_TABLE.' ON id = image_id';
  if (is_numeric($id))
  {
    $query.= '
  WHERE uppercats REGEXP \'(^|,)'.$id.'(,|$)\'';
  }
  $query.= '
  GROUP BY category_id
;';
  $result = pwg_query( $query );
  $datas = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push($cat_ids, $row['category_id']);
    array_push($datas, array('id' => $row['category_id'],
                             'date_last' => $row['date_last'],
                             'count' => $row['count']));
  }
  $fields = array('primary' => array('id'),
                  'update'  => array('date_last', 'count'));
  mass_updates(CATEGORIES_TABLE, $fields, $datas);

  if (count($cat_ids) > 0)
  {
    // find all categories where the setted representative is not possible
    $query = '
SELECT id
  FROM '.CATEGORIES_TABLE.' LEFT JOIN '.IMAGE_CATEGORY_TABLE.'
    ON id = category_id AND representative_picture_id = image_id
  WHERE representative_picture_id IS NOT NULL
    AND id IN ('.implode(',', $cat_ids).')
    AND category_id IS NULL
;';
    $result = pwg_query($query);
    while ($row = mysql_fetch_array($result))
    {
      // set a new representative element for this category
      $query = '
SELECT image_id
  FROM '.IMAGE_CATEGORY_TABLE.'
  WHERE category_id = '.$row['id'].'
  ORDER BY RAND()
  LIMIT 0,1
;';
      $sub_result = pwg_query($query);
      if (mysql_num_rows($sub_result) > 0)
      {
        list($representative) = mysql_fetch_array($sub_result);
        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = '.$representative.'
  WHERE id = '.$row['id'].'
;';
        pwg_query($query);
      }
      else
      {
        $query = '
UPDATE '.CATEGORIES_TABLE.'
  SET representative_picture_id = NULL
  WHERE id = '.$row['id'].'
;';
        pwg_query($query);
      }
    }
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
 * returns an array with the ids of the restricted categories for the user
 *
 * Returns an array with the ids of the restricted categories for the
 * user. If the $check_invisible parameter is set to true, invisible
 * categorie are added to the restricted one in the array.
 *
 * @param int $user_id
 * @param string $user_status
 * @param bool $check_invisible
 * @param bool $use_groups
 * @return array
 */
function get_user_restrictions( $user_id, $user_status,
                                $check_invisible, $use_groups = true )
{
  // 1. retrieving ids of private categories
  $query = 'SELECT id FROM '.CATEGORIES_TABLE;
  $query.= " WHERE status = 'private'";
  $query.= ';';
  $result = pwg_query( $query );
  $privates = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $privates, $row['id'] );
  }
  // 2. retrieving all authorized categories for the user
  $authorized = array();
  // 2.1. retrieving authorized categories thanks to personnal user
  //      authorization
  $query = 'SELECT cat_id FROM '.USER_ACCESS_TABLE;
  $query.= ' WHERE user_id = '.$user_id;
  $query.= ';';
  $result = pwg_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $authorized, $row['cat_id'] );
  }
  // 2.2. retrieving authorized categories thanks to group authorization to
  //      which the user is a member
  if ( $use_groups )
  {
    $query = 'SELECT ga.cat_id';
    $query.= ' FROM '.USER_GROUP_TABLE.' as ug';
    $query.= ', '.GROUP_ACCESS_TABLE.' as ga';
    $query.= ' WHERE ug.group_id = ga.group_id';
    $query.= ' AND ug.user_id = '.$user_id;
    $query.= ';';
    $result = pwg_query( $query );
    while ( $row = mysql_fetch_array( $result ) )
    {
      array_push( $authorized, $row['cat_id'] );
    }
    $authorized = array_unique( $authorized );
  }

  $forbidden = array();
  foreach ( $privates as $private ) {
    if ( !in_array( $private, $authorized ) )
    {
      array_push( $forbidden, $private );
    }
  }

  if ( $check_invisible )
  {
    // 3. adding to the restricted categories, the invisible ones
    if ( $user_status != 'admin' )
    {
      $query = 'SELECT id FROM '.CATEGORIES_TABLE;
      $query.= " WHERE visible = 'false';";
      $result = pwg_query( $query );
      while ( $row = mysql_fetch_array( $result ) )
      {
        array_push( $forbidden, $row['id'] );
      }
    }
  }
  return array_unique( $forbidden );
}

/**
 * updates the calculated data users.forbidden_categories, it includes
 * sub-categories of the direct forbidden categories
 *
 * @param nt $user_id
 * @return array
 */
function update_user_restrictions( $user_id )
{
  $restrictions = get_user_all_restrictions( $user_id );

  // update the users.forbidden_categories in database
  $query = 'UPDATE '.USERS_TABLE;
  $query.= ' SET forbidden_categories = ';
  if ( count( $restrictions ) > 0 )
    $query.= "'".implode( ',', $restrictions )."'";
  else
    $query.= 'NULL';
  $query .= ' WHERE id = '.$user_id;
  $query.= ';';
  pwg_query( $query );

  return $restrictions;
}

/**
 * returns all the restricted categories ids including sub-categories
 *
 * @param int $user_id
 * @return array
 */
function get_user_all_restrictions( $user_id )
{
  global $page;
  
  $query = 'SELECT status';
  $query.= ' FROM '.USERS_TABLE;
  $query.= ' WHERE id = '.$user_id;
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );
  
  $base_restrictions=get_user_restrictions($user_id,$row['status'],true,true);

  $restrictions = $base_restrictions;
  foreach ( $base_restrictions as $category_id ) {
    echo $category_id.' is forbidden to user '.$user_id.'<br />';
    $restrictions =
      array_merge( $restrictions,
                   $page['plain_structure'][$category_id]['all_subcats_ids'] );
  }

  return array_unique( $restrictions );
}

// The function is_user_allowed returns :
//      - 0 : if the category is allowed with this $restrictions array
//      - 1 : if this category is not allowed
//      - 2 : if an uppercat category is not allowed
// Note : the restrictions array must represent ONLY direct forbidden
// categories, not all forbidden categories
function is_user_allowed( $category_id, $restrictions )
{
  if ( in_array( $category_id, $restrictions ) ) return 1;

  $query = 'SELECT uppercats';
  $query.= ' FROM '.CATEGORIES_TABLE;
  $query.= ' WHERE id = '.$category_id;
  $query.= ';';
  $row = mysql_fetch_array( pwg_query( $query ) );
  $uppercats = explode( ',', $row['uppercats'] );
  foreach ( $uppercats as $category_id ) {
    if ( in_array( $category_id, $restrictions ) ) return 2;
  }

  // no restriction found : the user is allowed to access this category
  return 0;
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

// my_error returns (or send to standard output) the message concerning the
// error occured for the last mysql query.
function my_error($header, $echo = true)
{
  $error = $header.'<span style="font-weight:bold;">N°= '.mysql_errno();
  $error.= ' -->> '.mysql_error()."</span><br /><br />\n";
  if ($echo)
  {
    echo $error;
  }
  else
  {
    return $error;
  }
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
  $row = mysql_fetch_array(pwg_query($query));
  if (version_compare($row['version'],'4.0.4') < 0)
  {
    // MySQL is prior to version 4.0.4, multi table update feature is not
    // available
    echo 'MySQL is prior to version 4.0.4, multi table update feature is not available<br />';
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
    $query = '
CREATE TEMPORARY TABLE '.$tablename.'_temporary
(
'.implode(",\n", $columns).',
PRIMARY KEY (id)
)
;';
    pwg_query($query);
    mass_inserts($tablename, $all_fields, $datas);
    // update of images table by joining with temporary table
    $query = '
UPDATE '.$tablename.' AS t1, '.$tablename.'_temporary AS t2
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
DROP TABLE '.$tablename.'_temporary
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
?>
