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

function get_subcats_id( $cat_id )
{
  $restricted_cats = array();
                
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id_uppercat = '.$cat_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $restricted_cats, $row['id'] );
    $sub_restricted_cats = get_subcats_id( $row['id'] );
    foreach ( $sub_restricted_cats as $sub_restricted_cat ) {
      array_push( $restricted_cats, $sub_restricted_cat );
    }
  }
  return $restricted_cats;
}

function check_restrictions( $category_id )
{
  global $user,$lang;

  if ( is_user_allowed( $category_id, $user['restrictions'] ) > 0 )
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
    if ( isset( $page['plain_structure'] ) )
    {
      if ( isset( $page['plain_structure'][$cat] ) )
      {
        $page['cat'] = $cat;
      }
    }
    else if ( is_numeric( $cat ) )
    {
      $query = 'SELECT id';
      $query.= ' FROM '.PREFIX_TABLE.'categories';
      $query.= ' WHERE id = '.$cat;
      $query. ';';
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

function get_plain_structure()
{
  $infos = array( 'name','id','date_last','nb_images','dir','id_uppercat',
                  'rank','site_id');
  
  $query = 'SELECT ';
  foreach ( $infos as $i => $info ) {
    if ( $i > 0 ) $query.= ',';
    $query.= $info;
  }
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' ORDER BY id_uppercat ASC, rank ASC';
  $query.= ';';

  $plain_structure = array();
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $category = array();
    foreach ( $infos as $info ) {
      $category[$info] = $row[$info];
      if ( $info == 'date_last' )
      {
        list($year,$month,$day) = explode( '-', $row[$info] );
        $category[$info] = mktime(0,0,0,$month,$day,$year);
      }
    }
    $plain_structure[$row['id']] = $category;
  }

  return $plain_structure;
}

function create_structure( $id_uppercat, $restrictions )
{
  global $page;

  $structure = array();
  $ids = get_subcat_ids( $id_uppercat );
  foreach ( $ids as $id ) {
    if ( !in_array( $id, $restrictions ) )
    {
      $category = $page['plain_structure'][$id];
      $category['subcats'] = create_structure( $id, $restrictions );
      array_push( $structure, $category );
    }
  }
  return $structure;
}

function get_subcat_ids( $id_uppercat )
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
// structure : the last date, should the category be expanded in the menu ?,
// the associated expand string "48,14,54"
//
// 1. last date
// for each category of the structure, we have to find the most recent
// subcat so that the parent cat has the same last_date info.
// For example : we have :
// > pets       (2003.02.15)
//    > dogs    (2003.06.14)
//       > rex  (2003.06.18)
//       > toby (2003.06.13)
//    > kitten  (2003.07.05)
// We finally want to have :
// > pets       (2003.07.05) <- changed to pets > kitten last date
//    > dogs    (2003.06.18) <- changed to pets > dogs > rex last date
//       > rex  (2003.06.18)
//       > toby (2003.06.13)
//    > kitten  (2003.07.05)
//
// 2. should the category be expanded in the menu ?
// If the category has to be expanded (ie its id is in the
// $page['tab_expand'] or all the categories must be expanded by default),
// $category['expanded'] is set to true.
//
// 3. associated expand string
// in the menu, there is a expand string (used in the URL) to tell which
// categories must be expanded in the menu if this category is chosen
function update_structure( $categories )
{
  global $page, $user;

  $updated_categories = array();

  foreach ( $categories as $category ) {
    // update the last date of the category
    $last_date = search_last_date( $category );
    $category['date_last'] = $last_date;
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
    // update the  "expand_string" key
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
      else if ( count( $category['subcats'] ) > 0 )
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

// search_last_date searchs the last date for a given category. If we take
// back the example given for update_last_dates, we should have :
// search_last_date( pets )        --> 2003.07.05
// search_last_date( pets > dogs ) --> 2003.06.18
// and so on
function search_last_date( $category )
{
  $date_last = $category['date_last'];
  foreach ( $category['subcats'] as $subcat ) {
    $subcat_date_last = search_last_date( $subcat );
    if ( $subcat_date_last > $date_last )
    {
      $date_last = $subcat_date_last;
    }
  }
  return $date_last;
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
  $total = 0;
  foreach ( $categories as $category ) {
    $total+= $category['nb_images'];
    $total+= count_images( $category['subcats'] );
  }
  return $total;
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

  $cat = array();
                
  $query = 'SELECT nb_images,id_uppercat,comment,site_id,galleries_url,dir';
  $query.= ',date_last,uploadable,status,visible';
  $query.= ' FROM '.PREFIX_TABLE.'categories AS a';
  $query.= ', '.PREFIX_TABLE.'sites AS b';
  $query.= ' WHERE a.id = '.$id;
  $query.= ' AND a.site_id = b.id;';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $cat['site_id']     = $row['site_id'];
  $cat['id_uppercat'] = $row['id_uppercat'];
  $cat['comment']     = nl2br( $row['comment'] );
  $cat['nb_images']   = $row['nb_images'];
  $cat['dir']         = $row['dir'];
  $cat['date_last']   = $row['date_last'];
  $cat['uploadable']  = get_boolean( $row['uploadable'] );
  $cat['status']      = $row['status'];
  $cat['visible']     = get_boolean( $row['visible'] );

  $cat['name'] = array();
  array_push( $cat['name'], $page['plain_structure'][$id]['name'] );
  while ( $page['plain_structure'][$id]['id_uppercat'] != '' )
  {
    $id = $page['plain_structure'][$id]['id_uppercat'];
    array_push( $cat['name'], $page['plain_structure'][$id]['name'] );
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

  // creating the local path : "root_cat/sub_cat/sub_sub_cat/"
  $dir = $page['plain_structure'][$category_id]['dir'].'/';
  while ( $page['plain_structure'][$category_id]['id_uppercat'] != '' )
  {
    $category_id = $page['plain_structure'][$category_id]['id_uppercat'];
    $dir = $page['plain_structure'][$category_id]['dir'].'/'.$dir;
  }
  return $dir;
}

// retrieving the site url : "http://domain.com/gallery/" or
// simply "./galleries/"
function get_site_url( $category_id )
{
  global $page;

  $query = 'SELECT galleries_url';
  $query.= ' FROM '.PREFIX_TABLE.'sites';
  $query.= ' WHERE id = '.$page['plain_structure'][$category_id]['site_id'];
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
function get_cat_display_name( $array_cat_names, $separation, $style )
{
  $output = "";
  for ( $i = sizeof( $array_cat_names ) - 1; $i >= 0; $i-- )
  {
    if ( $i != sizeof( $array_cat_names ) - 1 )
    {
      $output.= $separation;
    }
    if ( $i != 0 )
    {
      $output.= $array_cat_names[$i];
    }
    else
    {
      if ( $style != "" )
      {
        $output.= '<span style="'.$style.'">';
      }
      $output.= $array_cat_names[$i];
      if ( $style != "" )
      {
        $output.= "</span>";
      }
    }
  }
  return replace_space( $output );
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
  global $page,$lang,$user,$conf;

  if ( isset( $page['cat'] ) )
  {
    // $page['nb_image_page'] is the number of picture to display on this page
    // By default, it is the same as the $user['nb_image_page']
    $page['nb_image_page'] = $user['nb_image_page'];
    // $url is used to create the navigation bar
    $url = './category.php?cat='.$page['cat'].'&amp;expand='.$page['expand'];
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
      $page['title'] = get_cat_display_name( $page['cat_name'], ' - ', '' );
      $page['where'] = ' WHERE category_id = '.$page['cat'];
    }
    else
    {
      if ( $page['cat'] == 'search' or $page['cat'] == 'most_visited'
           or $page['cat'] == 'recent' or $page['cat'] == 'best_rated' )
      {
        // we must not show pictures of a forbidden category
        $restricted_cats = get_all_restrictions( $user['id'],$user['status'] );
        foreach ( $restricted_cats as $restricted_cat ) {
          $where_append.= ' AND category_id != '.$restricted_cat;
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
        $page['where'].= $where_append;

        $query = 'SELECT COUNT(*) AS nb_total_images';
        $query.= ' FROM '.PREFIX_TABLE.'images';
        $query.= $page['where'];
        $query.= ';';

        $url.= '&amp;search='.$_GET['search'].'&amp;mode='.$_GET['mode'];
      }
      // favorites displaying
      else if ( $page['cat'] == 'fav' )
      {
        $page['title'] = $lang['favorites'];

        $page['where'] = ', '.PREFIX_TABLE.'favorites';
        $page['where'].= ' WHERE user_id = '.$user['id'];
        $page['where'].= ' AND image_id = id';
      
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
        $page['where'].= $where_append;

        $query = 'SELECT COUNT(*) AS nb_total_images';
        $query.= ' FROM '.PREFIX_TABLE.'images';
        $query.= $page['where'];
        $query.= ';';
      }
      // most visited pictures
      else if ( $page['cat'] == 'most_visited' )
      {
        $page['title'] = $conf['top_number'].' '.$lang['most_visited_cat'];
        $page['where'] = ' WHERE category_id != -1'.$where_append;
        $conf['order_by'] = ' ORDER BY hit DESC, file ASC';
        $page['cat_nb_images'] = $conf['top_number'];
        if ( $page['start'] + $user['nb_image_page'] >= $conf['top_number'] )
        {
          $page['nb_image_page'] = $conf['top_number'] - $page['start'];
        }
      }
      
      if ( $query != '' )
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
  foreach ( $user['restrictions'] as $restricted_cat ) {
    $query.= ' AND id != '.$restricted_cat;
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
  foreach ( $user['restrictions'] as $restricted_cat ) {
    $query.= ' AND id != '.$restricted_cat;
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