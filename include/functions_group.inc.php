<?php
/***************************************************************************
 *                           functions_group.inc.php                       *
 *                            --------------------                         *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
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

// get_group_restrictions returns an array containing all unaccessible
// category ids.
function get_group_restrictions( $group_id )
{
  // 1. retrieving ids of private categories
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= " WHERE status = 'private'";
  $query.= ';';
  $result = mysql_query( $query );
  $privates = array();
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $privates, $row['id'] );
  }
  // 2. retrieving all authorized categories for the group
  $authorized = array();
  $query = 'SELECT cat_id';
  $query.= ' FROM '.PREFIX_TABLE.'group_access';
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    array_push( $authorized, $row['cat_id'] );
  }

  $forbidden = array();
  foreach ( $privates as $private ) {
    if ( !in_array( $private, $authorized ) )
    {
      array_push( $forbidden, $private );
    }
  }

  return $forbidden;
}

// get_all_group_restrictions returns an array with ALL unaccessible
// category ids, including sub-categories
function get_all_group_restrictions( $group_id )
{
  $restricted_cats = get_group_restrictions( $group_id );
  foreach ( $restricted_cats as $restricted_cat ) {
    $sub_restricted_cats = get_subcats_id( $restricted_cat );
    foreach ( $sub_restricted_cats as $sub_restricted_cat ) {
      array_push( $restricted_cats, $sub_restricted_cat );
    }
  }
  return $restricted_cats;
}

// The function is_group_allowed returns :
//      - 0 : if the category is allowed with this $restrictions array
//      - 1 : if this category is not allowed
//      - 2 : if an uppercat category is not allowed
function is_group_allowed( $category_id, $restrictions )
{
  $lowest_category_id = $category_id;
                
  $is_root = false;
  while ( !$is_root and !in_array( $category_id, $restrictions ) )
  {
    $query = 'SELECT id_uppercat';
    $query.= ' FROM '.PREFIX_TABLE.'categories';
    $query.= ' WHERE id = '.$category_id;
    $query.= ';';
    $row = mysql_fetch_array( mysql_query( $query ) );
    if ( $row['id_uppercat'] == '' )
    {
      $is_root = true;
    }
    $category_id = $row['id_uppercat'];
  }
                
  if ( in_array( $lowest_category_id, $restrictions ) )
  {
    return 1;
  }
  if ( in_array( $category_id, $restrictions ) )
  {
    return 2;
  }
  // this group is allowed to go in this category
  return 0;
}
?>