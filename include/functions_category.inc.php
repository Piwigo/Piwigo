<?php
/***************************************************************************
 *                         functions_category.inc.php                      *
 *                            --------------------                         *
 *   application          : PhpWebGallery 1.3 <http://phpwebgallery.net>   *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/

function check_restrictions( $category_id )
{
  global $user,$lang;

  if ( in_array( $category_id, $user['restrictions'] ) )
  {
    echo '<div style="text-align:center;">'.$lang['access_forbiden'].'<br />';
    echo '<a href="'.add_session_id( './category.php' ).'">';
    echo $lang['thumbnails'].'</a></div>';
    exit();
  }
}

// the check_cat_id function check whether the $cat is a right parameter :
//  - $cat is numeric and corresponds to a category in the database
//  - $cat equals 'fav' (for favorites)
//  - $cat equals 'search' (when the result of a search is displayed)
function check_cat_id( $cat )
{
  global $page;

  unset( $page['cat'] );
  if ( isset( $cat ) )
  {
    if ( isset( $page['plain_structure'][$cat] ) )
    {
      $page['cat'] = $cat;
    }
    else if ( is_numeric( $cat ) )
    {
      $query = 'SELECT id';
      $query.= ' FROM '.PREFIX_TABLE.'categories';
      $query.= ' WHERE id = '.$cat;
      $query.= ';';
      $result = mysql_query( $query );
      if ( mysql_num_rows( $result ) != 0 )
      {
        $page['cat'] = $cat;
      }
    }
    if ( $cat == 'fav'
         or $cat == 'search'
         or $cat == 'most_visited'
         or $cat == 'best_rated'
         or $cat == 'recent' )
    {
      $page['cat'] = $cat;
    }
  }
}

function get_user_plain_structure()
{
  global $page,$user;
  
  $infos = array( 'name','id','uc.date_last','nb_images','dir','id_uppercat',
                  'rank','site_id','nb_sub_categories','uppercats');
  
  $query = 'SELECT '.implode( ',', $infos );
  $query.= ' FROM '.PREFIX_TABLE.'categories AS c';
//  $query.= ' ,'.PREFIX_TABLE.'user_category AS uc';
  $query.= ' INNER JOIN '.PREFIX_TABLE.'user_category AS uc';
  $query.= ' ON c.id = uc.category_id';
  $query.= ' WHERE user_id = '.$user['id'];
  if ( $page['expand'] != 'all' )
  {
    $query.= ' AND (id_uppercat is NULL';
    if ( count( $page['tab_expand'] ) > 0 )
    {
      $query.= ' OR id_uppercat IN ('.$page['expand'].')';
    }
    $query.= ')';
  }
  if ( $user['forbidden_categories'] != '' )
  {
    $query.= ' AND id NOT IN ';
    $query.= '('.$user['forbidden_categories'].')';
  }
//  $query.= ' AND c.id = uc.category_id';
  $query.= ' ORDER BY id_uppercat ASC, rank ASC';
  $query.= ';';

  $plain_structure = array();
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $category = array();
    foreach ( $infos as $info ) {
      if ( $info == 'uc.date_last' )
      {
        if ( isset( $row['date_last'] ) and $row['date_last'] != '' )
        {
          list($year,$month,$day) = explode( '-', $row['date_last'] );
          $category['date_last'] = mktime(0,0,0,$month,$day,$year);
        }
      }
      else if ( isset( $row[$info] ) ) $category[$info] = $row[$info];
      else                             $category[$info] = '';
    }
    $plain_structure[$row['id']] = $category;
  }

  return $plain_structure;
}

function create_user_structure( $id_uppercat )
{
  global $page;

  if ( !isset( $page['plain_structure'] ) )
    $page['plain_structure'] = get_user_plain_structure();

  $structure = array();
  $ids = get_user_subcat_ids( $id_uppercat );
  foreach ( $ids as $id ) {
    $category = $page['plain_structure'][$id];
    $category['subcats'] = create_user_structure( $id );
    array_push( $structure, $category );
  }
  return $structure;
}

function get_user_subcat_ids( $id_uppercat )
{
  global $page;

  $ids = array();
  foreach ( $page['plain_structure'] as $id => $category ) {
    if ( $category['id_uppercat'] == $id_uppercat ) array_push( $ids, $id );
    else if ( count( $ids ) > 0 )                   return $ids;
  }
  return $ids;
}

// update_structure updates or add informations about each node of the
// structure :
//
// 1. should the category be expanded in the menu ?
// If the category has to be expanded (ie its id is in the
// $page['tab_expand'] or all the categories must be expanded by default),
// $category['expanded'] is set to true.
//
// 2. associated expand string
// in the menu, there is a expand string (used in the URL) to tell which
// categories must be expanded in the menu if this category is chosen
function update_structure( $categories )
{
  global $page, $user;

  $updated_categories = array();

  foreach ( $categories as $category ) {
    // update the "expanded" key
    if ( $user['expand']
         or $page['expand'] == 'all'
         or in_array( $category['id'], $page['tab_expand'] ) )
    {
      $category['expanded'] = true;
    }
    else
    {
      $category['expanded'] = false;
    }
    // update the "expand_string" key
    if ( $page['expand'] == 'all' )
    {
      $category['expand_string'] = 'all';
    }
    else
    {
      $tab_expand = $page['tab_expand'];
      if ( in_array( $category['id'], $page['tab_expand'] ) )
      {
        // the expand string corresponds to the $page['tab_expand'] without
        // the $category['id']
        $tab_expand = array_diff( $page['tab_expand'],array($category['id']) );
      }
      else if ( $category['nb_sub_categories'] > 0 )
      {
        // we have this time to add the $category['id']...
        $tab_expand = array_merge($page['tab_expand'],array($category['id']));
      }
      $category['expand_string'] = implode( ',', $tab_expand );
    }
    // recursive call
    $category['subcats'] = update_structure( $category['subcats'] );
    // adding the updated category
    array_push( $updated_categories, $category );
  }

  return $updated_categories;
}

// count_images returns the number of pictures contained in the given
// category represented by an array, in this array, we have (among other
// things) :
// $category['nb_images'] -> number of pictures in this category
// $category['subcats'] -> array of sub-categories
// count_images goes to the deepest sub-category to find the total number of
// pictures contained in the given given category
function count_images( $categories )
{
  return count_user_total_images();
  $total = 0;
  foreach ( $categories as $category ) {
    $total+= $category['nb_images'];
    $total+= count_images( $category['subcats'] );
  }
  return $total;
}

function count_user_total_images()
{
  global $user;

  $query = 'SELECT SUM(nb_images) AS total';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  if ( count( $user['restrictions'] ) > 0 )
    $query.= ' WHERE id NOT IN ('.$user['forbidden_categories'].')';
  $query.= ';';
  
  $row = mysql_fetch_array( mysql_query( $query ) );

  if ( !isset( $row['total'] ) ) $row['total'] = 0;

  return $row['total'];
}

// variables :
// $cat['comment']
// $cat['dir']
// $cat['dir']
// $cat['name'] is an array :
//      - $cat['name'][0] is the lowest cat name
//      and
//      - $cat['name'][n] is the most uppercat name findable
// $cat['nb_images']
// $cat['id_uppercat']
// $cat['site_id']
function get_cat_info( $id )
{
  global $page;

  $infos = array( 'nb_images','id_uppercat','comment','site_id','galleries_url'
                  ,'dir','date_last','uploadable','status','visible'
                  ,'representative_picture_id','uppercats' );

  $query = 'SELECT '.implode( ',', $infos );
  $query.= ' FROM '.PREFIX_TABLE.'categories AS a';
  $query.= ', '.PREFIX_TABLE.'sites AS b';
  $query.= ' WHERE a.id = '.$id;
  $query.= ' AND a.site_id = b.id';
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );

  $cat = array();
  // affectation of each field of the table "config" to an information of the
  // array $cat.
  foreach ( $infos as $info ) {
    if ( isset( $row[$info] ) ) $cat[$info] = $row[$info];
    else                        $cat[$info] = '';
    // If the field is true or false, the variable is transformed into a
    // boolean value.
    if ( $cat[$info] == 'true' or $cat[$info] == 'false' )
    {
      $cat[$info] = get_boolean( $cat[$info] );
    }
  }
  $cat['comment'] = nl2br( $cat['comment'] );

  $cat['name'] = array();

  $query = 'SELECT name';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id IN ('.$cat['uppercats'].')';
  $query.= ' ORDER BY id ASC';
  $query.= ';';
  $result = mysql_query( $query );
  while( $row = mysql_fetch_array( $result ) )
  {
    array_push( $cat['name'], $row['name'] );
  }
  
  return $cat;
}

// get_complete_dir returns the concatenation of get_site_url and
// get_local_dir
// Example : "pets > rex > 1_year_old" is on the the same site as the
// PhpWebGallery files and this category has 22 for identifier
// get_complete_dir(22) returns "./galleries/pets/rex/1_year_old/"
function get_complete_dir( $category_id )
{
  return get_site_url( $category_id ).get_local_dir( $category_id );
}

// get_local_dir returns an array with complete path without the site url
// Example : "pets > rex > 1_year_old" is on the the same site as the
// PhpWebGallery files and this category has 22 for identifier
// get_local_dir(22) returns "pets/rex/1_year_old/"
function get_local_dir( $category_id )
{
  global $page;

  $uppercats = '';
  $local_dir = '';

  if ( isset( $page['plain_structure'][$category_id]['uppercats'] ) )
  {
    $uppercats = $page['plain_structure'][$category_id]['uppercats'];
  }
  else
  {
    $query = 'SELECT uppercats';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$category_id;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    $uppercats = $row['uppercats'];
  }

  $upper_array = explode( ',', $uppercats );

  $database_dirs = array();
  $query = 'SELECT id,dir';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id IN ('.$uppercats.')';
  $query.= ';';
  $result = mysql_query( $query );
  while( $row = mysql_fetch_array( $result ) )
  {
    $database_dirs[$row['id']] = $row['dir'];
  }
  foreach ( $upper_array as $id ) {
    $local_dir.= $database_dirs[$id].'/';
  }

  return $local_dir;
}

// retrieving the site url : "http://domain.com/gallery/" or
// simply "./galleries/"
function get_site_url( $category_id )
{
  global $page;

  $query = 'SELECT galleries_url';
  $query.= ' FROM '.PREFIX_TABLE.'sites AS s,'.PREFIX_TABLE.'categories AS c';
  $query.= ' WHERE s.id = c.site_id';
  $query.= ' AND c.id = '.$category_id;
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  return $row['galleries_url'];
}

// The function get_cat_display_name returns a string containing the list
// of upper categories to the root category from the lowest category shown
// example : "anniversaires - fete mere 2002 - animaux - erika"
// You can give two parameters :
//   - $separation : the string between each category name " - " for example
//   - $style : the style of the span tag for the lowest category,
//     "font-style:italic;" for example
function get_cat_display_name( $array_cat_names, $separation,
                               $style, $replace_space = true )
{
  $output = '';
  foreach ( $array_cat_names as $i => $name ) {
    if ( $i > 0 ) $output.= $separation;
    if ( $i < count( $array_cat_names ) - 1 or $style == '')
      $output.= $name;
    else
      $output.= '<span style="'.$style.'">'.$name.'</span>';
  }
  if ( $replace_space ) return replace_space( $output );
  else                  return $output;
}

// initialize_category initializes ;-) the variables in relation
// with category :
// 1. calculation of the number of pictures in the category
// 2. determination of the SQL query part to ask to find the right category
//    $page['where'] is not the same if we are in
//       - simple category
//       - search result
//       - favorites displaying
//       - most visited pictures
//       - best rated pictures
//       - recent pictures
// 3. determination of the title of the page
// 4. creation of the navigation bar
function initialize_category( $calling_page = 'category' )
{
  pwg_debug( 'start initialize_category' );
  global $page,$lang,$user,$conf;

  if ( isset( $page['cat'] ) )
  {
    // $page['nb_image_page'] is the number of picture to display on this page
    // By default, it is the same as the $user['nb_image_page']
    $page['nb_image_page'] = $user['nb_image_page'];
    // $url is used to create the navigation bar
    $url = './category.php?cat='.$page['cat'];
    if ( isset($page['expand']) ) $url.= '&amp;expand='.$page['expand'];
    // simple category
    if ( is_numeric( $page['cat'] ) )
    {
      $result = get_cat_info( $page['cat'] );
      $page['comment']        = $result['comment'];
      $page['cat_dir']        = $result['dir'];
      $page['cat_name']       = $result['name'];
      $page['cat_nb_images']  = $result['nb_images'];
      $page['cat_site_id']    = $result['site_id'];
      $page['cat_uploadable'] = $result['uploadable'];
      $page['uppercats']      = $result['uppercats'];
      $page['title'] = get_cat_display_name( $page['cat_name'],' - ','',false);
      $page['where'] = ' WHERE category_id = '.$page['cat'];
    }
    else
    {
      if ( $page['cat'] == 'search' or $page['cat'] == 'most_visited'
           or $page['cat'] == 'recent' or $page['cat'] == 'best_rated' )
      {
        // we must not show pictures of a forbidden category
        if ( $user['forbidden_categories'] != '' )
        {
          $forbidden = ' category_id NOT IN ';
          $forbidden.= '('.$user['forbidden_categories'].')';
        }
      }
      // search result
      if ( $page['cat'] == 'search' )
      {
        $page['title'] = $lang['search_result'];
        if ( $calling_page == 'picture' )
        {
          $page['title'].= ' : <span style="font-style:italic;">';
          $page['title'].= $_GET['search']."</span>";
        }

        $page['where'] = ' WHERE (';
        $fields = array( 'file', 'name', 'comment', 'keywords' );
        $words = explode( ',', $_GET['search'] );
        $sql_search = array();
        foreach ( $words as $i => $word ) {
          // if the user searchs any of the words, the where statement must
          // be :
          // field1 LIKE '%$word1%' OR field2 LIKE '%$word1%' ...
          // OR field1 LIKE '%$word2%' OR field2 LIKE '%$word2%' ...
          if ( $_GET['mode'] == 'OR' )
          {
            if ( $i != 0 ) $page['where'].= ' OR';
            foreach ( $fields as $j => $field ) {
              if ( $j != 0 ) $page['where'].= ' OR';
              $page['where'].= ' '.$field." LIKE '%".$word."%'";
            }
          }
          // if the user searchs all the words :
          // ( field1 LIKE '%$word1%' OR field2 LIKE '%$word1%' )
          // AND ( field1 LIKE '%$word2%' OR field2 LIKE '%$word2%' )
          else if ( $_GET['mode'] == 'AND' )
          {
            if ( $i != 0 ) $page['where'].= ' AND';
            $page['where'].= ' (';
            foreach ( $fields as $j => $field ) {
              if ( $j != 0 ) $page['where'].= ' OR';
              $page['where'].= ' '.$field." LIKE '%".$word."%'";
            }
            $page['where'].= ' )';
          }
        }
        $page['where'].= ' )';
        if ( isset( $forbidden ) ) $page['where'].= ' AND '.$forbidden;

        $query = 'SELECT COUNT(DISTINCT(id)) AS nb_total_images';
        $query.= ' FROM '.PREFIX_TABLE.'images';
        $query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
        $query.= ' ON id = ic.image_id';
        $query.= $page['where'];
        $query.= ';';

        $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
      }
      // favorites displaying
      else if ( $page['cat'] == 'fav' )
      {
        $page['title'] = $lang['favorites'];

        $page['where'] = ', '.PREFIX_TABLE.'favorites AS fav';
        $page['where'].= ' WHERE user_id = '.$user['id'];
        $page['where'].= ' AND fav.image_id = id';
      
        $query = 'SELECT COUNT(*) AS nb_total_images';
        $query.= ' FROM '.PREFIX_TABLE.'favorites';
        $query.= ' WHERE user_id = '.$user['id'];
        $query.= ';';
      }
      // pictures within the short period
      else if ( $page['cat'] == 'recent' )
      {
        $page['title'] = $lang['recent_cat_title'];
        // We must find the date corresponding to :
        // today - $conf['periode_courte']
        $date = time() - 60*60*24*$user['short_period'];
        $page['where'] = " WHERE date_available > '";
        $page['where'].= date( 'Y-m-d', $date )."'";
        if ( isset( $forbidden ) ) $page['where'].= ' AND '.$forbidden;

        $query = 'SELECT COUNT(DISTINCT(id)) AS nb_total_images';
        $query.= ' FROM '.PREFIX_TABLE.'images';
        $query.= ' INNER JOIN '.PREFIX_TABLE.'image_category AS ic';
        $query.= ' ON id = ic.image_id';
        $query.= $page['where'];
        $query.= ';';
      }
      // most visited pictures
      else if ( $page['cat'] == 'most_visited' )
      {
        $page['title'] = $conf['top_number'].' '.$lang['most_visited_cat'];
        
        if ( isset( $forbidden ) ) $page['where'] = ' WHERE '.$forbidden;
        else                       $page['where'] = '';
        $conf['order_by'] = ' ORDER BY hit DESC, file ASC';
        $page['cat_nb_images'] = $conf['top_number'];
        if ( isset( $page['start'] )
             and ($page['start']+$user['nb_image_page']>=$conf['top_number']))
        {
          $page['nb_image_page'] = $conf['top_number'] - $page['start'];
        }
      }

      if ( isset($query))
      {
        $result = mysql_query( $query );
        $row = mysql_fetch_array( $result );
        $page['cat_nb_images'] = $row['nb_total_images'];
      }
    }
    if ( $calling_page == 'category' )
    {
      $page['navigation_bar'] =
        create_navigation_bar( $url, $page['cat_nb_images'], $page['start'],
                               $user['nb_image_page'], 'back' );
    }
  }
  else
  {
    $page['title'] = $lang['diapo_default_page_title'];
  }
  pwg_debug( 'end initialize_category' );
}

// get_non_empty_subcat_ids returns an array with sub-categories id
// associated with their first non empty category id.
//
//                          example :
//
// - catname [cat_id]
// - cat1 [1] -> given uppercat
//   - cat1.1 [12] (empty)
//     - cat1.1.1 [5] (empty)
//     - cat1.1.2 [6]
//   - cat1.2 [3]
//   - cat1.3 [4]
//
// get_non_empty_sub_cat_ids will return :
//   $ids[12] = 6;
//   $ids[3]  = 3;
//   $ids[4]  = 4;
function get_non_empty_subcat_ids( $id_uppercat )
{
  global $user;

  $ids = array();

  $query = 'SELECT id,nb_images';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id_uppercat ';
  if ( !is_numeric( $id_uppercat ) ) $query.= 'is NULL';
  else                               $query.= '= '.$id_uppercat;
  // we must not show pictures of a forbidden category
  if ( $user['forbidden_categories'] != '' )
  {
    $query.= ' AND id NOT IN ('.$user['forbidden_categories'].')';
  }
  $query.= ' ORDER BY rank';
  $query.= ';';

  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // only categories with findable picture in any of its subcats is
    // represented.
    if ( ( $row['nb_images'] != 0 and $non_empty_cat = $row['id'] )
         or $non_empty_cat = get_first_non_empty_cat_id( $row['id'] ) )
    {
      $ids[$row['id']] = $non_empty_cat;
    }
  }
  return $ids;
}

// get_first_non_empty_cat_id returns the id of the first non empty
// sub-category to the given uppercat. If no picture is found in any
// subcategory, false is returned.
function get_first_non_empty_cat_id( $id_uppercat )
{
  global $user;

  $query = 'SELECT id,nb_images';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id_uppercat = '.$id_uppercat;
  // we must not show pictures of a forbidden category
  if ( $user['forbidden_categories'] != '' )
  {
    $query.= ' AND id NOT IN ('.$user['forbidden_categories'].')';
  }
  $query.= ' ORDER BY RAND()';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( $row['nb_images'] > 0 )
    {
      return $row['id'];
    }
  }
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    // recursive call
    if ( $subcat = get_first_non_empty_cat_id( $row['id'] ) )
    {
      return $subcat;
    }
  }
  return false;
}
?>