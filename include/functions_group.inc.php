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

// get_group_restrictions returns an array containing all unaccessible
// category ids.
function get_group_restrictions( $group_id )
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
  // 2. retrieving all authorized categories for the group
  $authorized = array();
  $query = 'SELECT cat_id FROM '.GROUP_ACCESS_TABLE;
  $query.= ' WHERE group_id = '.$group_id;
  $query.= ';';
  $result = pwg_query( $query );
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
    $query = 'SELECT id_uppercat FROM '.CATEGORIES_TABLE;
    $query.= ' WHERE id = '.$category_id;
    $query.= ';';
    $row = mysql_fetch_array( pwg_query( $query ) );
    if ( !isset( $row['id_uppercat'] ) ) $row['id_uppercat'] = '';
    if ( $row['id_uppercat'] == '' ) $is_root = true;
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
