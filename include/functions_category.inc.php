<?php
/***************************************************************************
 *                         functions_category.inc.php                      *
 *                            --------------------                         *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
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
    if ( is_numeric( $cat ) )
    {
      $query = 'select id';
      $query.= ' from '.PREFIX_TABLE.'categories';
      $query.= ' where id = '.$cat;
      $query. ';';
      $result = mysql_query( $query );
      if ( mysql_num_rows( $result ) != 0 )
      {
        $page['cat'] = $cat;
      }
    }
    if ( $cat == 'fav' or $cat == 'search' or $cat == 'most_visited'
         or $cat == 'best_rated' or $cat == 'recent' )
    {
      $page['cat'] = $cat;
    }
  }
}

function display_cat( $id_uppercat, $indent, $restriction, $tab_expand )
{
  global $user,$lang,$conf,$page,$vtp,$handle;
  
  $query = 'select name,id,date_dernier,nb_images,dir';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where id_uppercat';
  if ( $id_uppercat == "" )
  {
    $query.= ' is NULL';
  }
  else
  {
    $query.= ' = '.$id_uppercat;
  }
  $query.= ' order by rank asc;';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( !in_array( $row['id'], $restriction ) )
    {
      $nb_subcats = get_nb_subcats( $row['id'] );
                                
      $expand = "";
      // si la catégorie n'a pas de sous catégorie
      // ou que l'on doit développer toutes les catégories par défaut
      // alors on utilise l'expand par défaut
      if ( $nb_subcats == 0 or $user['expand'] == "true" )
      {
        $expand = $page['expand'];
      }
      // si la catégorie n'est pas dans les catégories à développer
      // alors on l'ajoute aux catégories à développer
      else if ( !in_array( $row['id'], $tab_expand ) )
      {
        $expand = implode( ",", $tab_expand );
        if ( strlen( $expand ) > 0 )
        {
          $expand.= ",";
        }
        $expand.= $row['id'];
      }
      // si la catégorie est déjà dans les catégories à développer
      // alors on la retire des catégories à développer
      else
      {
        $expand = array_remove( $tab_expand, $row['id'] );
      }
      $url = "./category.php?cat=".$page['cat']."&amp;expand=$expand";
      if ( $page['cat'] == 'search' )
      {
        $url.= "&amp;search=".$_GET['search'].'&amp;mode='.$_GET['mode'];
      }
      $lien_cat = add_session_id( $url );
      if ( $row['name'] == "" )
      {
        $name = str_replace( "_", " ", $row['dir'] );
      }
      else
      {
        $name = $row['name'];
      }

      $vtp->addSession( $handle, 'category' );
      $vtp->setVar( $handle, 'category.indent', $indent );

      if ( $user['expand'] == "true" or $nb_subcats == 0 )
      {
        $vtp->addSession( $handle, 'bullet_wo_link' );
        $vtp->setVar( $handle, 'bullet_wo_link.bullet_url',
                      $user['lien_collapsed'] );
        $vtp->setVar( $handle, 'bullet_wo_link.bullet_alt', '&gt;' );
        $vtp->closeSession( $handle, 'bullet_wo_link' );
      }
      else
      {
        $vtp->addSession( $handle, 'bullet_w_link' );
        $vtp->setVar( $handle, 'bullet_w_link.bullet_link', $lien_cat );
        $vtp->setVar( $handle, 'bullet_w_link.bullet_alt', '&gt;' );
        if ( in_array( $row['id'], $tab_expand ) )
        {
          $vtp->setVar( $handle, 'bullet_w_link.bullet_url',
                        $user['lien_expanded'] );
        }
        else
        {
          $vtp->setVar( $handle, 'bullet_w_link.bullet_url',
                        $user['lien_collapsed'] );
        }
        $vtp->closeSession( $handle, 'bullet_w_link' );
      }
      $vtp->setVar( $handle, 'category.link_url',
                    add_session_id( './category.php?cat='.
                                    $row['id'].'&amp;expand='.$expand ) );
      $vtp->setVar( $handle, 'category.link_name', $name );
      if ( $id_uppercat == "" )
      {
        $vtp->setVar( $handle, 'category.name_style', 'font-weight:bold;' );
      }
      if ( $nb_subcats > 0 )
      {
        $vtp->addSession( $handle, 'subcat' );
        $vtp->setVar( $handle, 'subcat.nb_subcats', $nb_subcats );
        $vtp->closeSession( $handle, 'subcat' );
      }
      $vtp->setVar( $handle, 'category.total_cat', $row['nb_images'] );
      $date_dispo = explode( "-", $row['date_dernier'] );
      $date_cat = mktime( 0, 0, 0, $date_dispo[1], $date_dispo[2],
                          $date_dispo[0] );
      $vtp->setVar( $handle, 'category.cat_icon', get_icon( $date_cat ) );
      $vtp->closeSession( $handle, 'category' );

      if ( in_array( $row['id'], $tab_expand ) or $user['expand'] == "true" )
      {
        display_cat( $row['id'], $indent.'&nbsp;&nbsp;&nbsp;&nbsp;',
                     $restriction, $tab_expand );
      }
    }
  }
}
        
function get_nb_subcats( $id )
{
  global $user;
                
  $query = 'select count(*) as count';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where id_uppercat = '.$id;
  for ( $i = 0; $i < sizeof( $user['restrictions'] ); $i++ )
  {
    $query.= " and id != ".$user['restrictions'][$i];
  }
  $query.= ';';
  $result = mysql_query( $query );
  $row = mysql_fetch_array( $result );
  return $row['count'];
}
        
function get_total_image( $id, $restriction )
{
  $total = 0;
                
  $query = 'select id,nb_images';
  $query.= ' from '.PREFIX_TABLE.'categories';
  $query.= ' where id_uppercat';
  if ( !is_numeric( $id ) )
  {
    $query.= ' is NULL';
  }
  else
  {
    $query.= ' = '.$id;
  }
  $query.= ";";
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    if ( !in_array( $row['id'], $restriction ) )
    {
      $total+= $row['nb_images'];
      $total+= get_total_image( $row['id'], $restriction );
    }
  }
  return $total;
}

// variables :
// $cat['comment']
// $cat['dir']
// $cat['last_dir']
// $cat['name'] is an array :
//      - $cat['name'][0] is the lowest cat name
//      and
//      - $cat['name'][n] is the most uppercat name findable
// $cat['nb_images']
// $cat['id_uppercat']
// $cat['site_id']
function get_cat_info( $id )
{
  $cat = array();
  $cat['name'] = array();
                
  $query = 'select nb_images,id_uppercat,comment,site_id,galleries_url,dir';
  $query.= ' from '.PREFIX_TABLE.'categories as a';
  $query.= ', '.PREFIX_TABLE.'sites as b';
  $query.= ' where a.id = '.$id;
  $query.= ' and a.site_id = b.id;';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $cat['site_id']     = $row['site_id'];
  $cat['id_uppercat'] = $row['id_uppercat'];
  $cat['comment']     = nl2br( $row['comment'] );
  $cat['nb_images']   = $row['nb_images'];
  $cat['last_dir']    = $row['dir'];
  $galleries_url = $row['galleries_url'];
                
  $cat['dir'] = "";
  $i = 0;
  $is_root = false;
  $row['id_uppercat'] = $id;
  while ( !$is_root )
  {
    $query = 'select name,dir,id_uppercat';
    $query.= ' from '.PREFIX_TABLE.'categories';
    $query.= ' where id = '.$row['id_uppercat'].';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    $cat['dir'] = $row['dir']."/".$cat['dir'];
    if ( $row['name'] == "" )
    {
      $cat['name'][$i] = str_replace( "_", " ", $row['dir'] );
    }
    else
    {
      $cat['name'][$i] = $row['name'];
    }
    if ( $row['id_uppercat'] == "" )
    {
      $is_root = true;
    }
    $i++;
  }
  $cat['local_dir'] = substr( $cat['dir'], 0 , strlen( $cat['dir'] ) - 1 );
  $cat['dir'] = $galleries_url.$cat['dir'];
                
  return $cat;
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
      $page['comment']       = $result['comment'];
      $page['cat_dir']       = $result['dir'];
      $page['cat_name']      = $result['name'];
      $page['cat_nb_images'] = $result['nb_images'];
      $page['cat_site_id']   = $result['site_id'];
      $page['title'] = get_cat_display_name( $page['cat_name'], ' - ', '' );
      $page['where'] = ' WHERE cat_id = '.$page['cat'];
    }
    else
    {
      if ( $page['cat'] == 'search' or $page['cat'] == 'most_visited'
           or $page['cat'] == 'recent' or $page['cat'] == 'best_rated' )
      {
        // we must not show pictures of a forbidden category
        $restricted_cats = get_all_restrictions( $user['id'],$user['status'] );
        foreach ( $restricted_cats as $restricted_cat ) {
          $where_append.= ' AND cat_id != '.$restricted_cat;
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
        $fields = array( 'file', 'name', 'comment' );
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
        $page['where'] = ' WHERE cat_id != -1'.$where_append;
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

// get_non_empty_sub_cat_ids returns an array composing of the infos of the
// direct sub-categories of the given uppercat id. Each of these infos is
// associated to the first found non empty category id. eg :
//
// - catname [cat_id]
// - cat1 [1] -> given uppercat
//   - cat1.1 [2] (empty)
//     - cat1.1.1 [5] (empty)
//     - cat1.1.2 [6]
//   - cat1.2 [3]
//   - cat1.3 [4]
//
// get_non_empty_sub_cat_ids will return :
//   $cats[0]['id']            = 2;
//   $cats[0]['name']          = '';
//   $cats[0]['dir']           = 'cat1';
//   $cats[0]['date_dernier']  = '2003-05-17';
//   $cats[0]['non_empty_cat'] = 6;
//
//   $cats[1]['id']            = 3;
//   $cats[1]['non_empty_cat'] = 3;
//
//   $cats[1]['id']            = 4;
//   $cats[1]['non_empty_cat'] = 4;
function get_non_empty_sub_cat_ids( $id_uppercat )
{
  global $user;

  $cats = array();

  $query = 'SELECT id,name,dir,date_dernier,nb_images';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id_uppercat = '.$id_uppercat;
  // we must not show pictures of a forbidden category
  $restricted_cats = get_all_restrictions( $user['id'],$user['status'] );
  foreach ( $restricted_cats as $restricted_cat ) {
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
      $temp_cat = array(
        'id'            => $row['id'],
        'name'          => $row['name'],
        'dir'           => $row['dir'],
        'date_dernier'  => $row['date_dernier'],
        'non_empty_cat' => $non_empty_cat );
      array_push( $cats, $temp_cat );
    }
  }
  return $cats;
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
  $restricted_cats = get_all_restrictions( $user['id'],$user['status'] );
  foreach ( $restricted_cats as $restricted_cat ) {
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