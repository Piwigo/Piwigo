<?php
/***************************************************************************
 *                                  cat_list.php                           *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   website              : http://www.phpwebgallery.net                   *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include_once( './include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/cat_list.vtp' );
$tpl = array( 'cat_edit','cat_up','cat_down','cat_image_info',
              'cat_permission','cat_update','cat_add','cat_parent','submit',
              'cat_virtual','delete','cat_first','cat_last' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//--------------------------------------------------- adding a virtual category
$errors = array();
if ( isset( $_POST['submit'] ) )
{
  if ( !preg_match( '/^\s*$/', $_POST['virtual_name'] ) )
  {
    // we have then to add the virtual category
    $query = 'INSERT INTO '.PREFIX_TABLE.'categories';
    $query.= ' (name,id_uppercat) VALUES ';
    if ( $_POST['associate'] == -1 )
    {
      $_POST['associate'] = 'NULL';
    }
    $query.= " ('".$_POST['virtual_name']."',".$_POST['associate'].")";
    $query.= ';';
    echo $query;
    mysql_query( $query );
  }
  else
  {
    array_push( $errors, $lang['cat_error_name'] );
  }
}
//---------------------------------------------------------------  rank updates
if ( isset( $_GET['up'] ) and is_numeric( $_GET['up'] ) )
{
  // 1. searching level (id_uppercat)
  //    and rank of the category to move
  $query = 'SELECT id_uppercat,rank';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id = '.$_GET['up'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $level = $row['id_uppercat'];
  $rank  = $row['rank'];
  // 2. searching the id and the rank of the category
  //    just above at the same level
  $query = 'SELECT id,rank';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE rank < '.$rank;
  if ( $level == '' )
  {
    $query.= ' AND id_uppercat IS NULL';
  }
  else
  {
    $query.= ' AND id_uppercat = '.$level;
  }
  $query.= ' ORDER BY rank DESC';
  $query.= ' LIMIT 0,1';
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $new_rank     = $row['rank'];
  $replaced_cat = $row['id'];
  // 3. exchanging ranks between the two categories
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= ' SET rank = '.$new_rank;
  $query.= ' WHERE id = '.$_GET['up'];
  $query.= ';';
  mysql_query( $query );
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= ' SET rank = '.$rank;
  $query.= ' WHERE id = '.$replaced_cat;
  $query.= ';';
  mysql_query( $query );
}
if ( isset( $_GET['down'] ) and is_numeric( $_GET['down'] ) )
{
  // 1. searching level (id_uppercat)
  //    and rank of the category to move
  $query = 'SELECT id_uppercat,rank';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id = '.$_GET['down'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $level = $row['id_uppercat'];
  $rank  = $row['rank'];
  // 2. searching the id and the rank of the category
  //    just below at the same level
  $query = 'SELECT id,rank';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE rank > '.$rank;
  if ( $level == '' )
  {
    $query.= ' AND id_uppercat IS NULL';
  }
  else
  {
    $query.= ' AND id_uppercat = '.$level;
  }
  $query.= ' ORDER BY rank ASC';
  $query.= ' LIMIT 0,1';
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $new_rank     = $row['rank'];
  $replaced_cat = $row['id'];
  // 3. exchanging ranks between the two categories
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= ' SET rank = '.$new_rank;
  $query.= ' WHERE id = '.$_GET['down'];
  $query.= ';';
  mysql_query( $query );
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= ' SET rank = '.$rank;
  $query.= ' WHERE id = '.$replaced_cat;
  $query.= ';';
  mysql_query( $query );
}
if ( isset( $_GET['last'] ) and is_numeric( $_GET['last'] ) )
{
  // 1. searching level (id_uppercat) of the category to move
  $query = 'SELECT id_uppercat,rank';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id = '.$_GET['last'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $level = $row['id_uppercat'];
  // 2. searching the highest rank of the categories of the same parent
  $query = 'SELECT MAX(rank) AS max_rank';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id_uppercat';
  if ( $level == '' ) $query.= ' IS NULL';
  else                $query.= ' = '.$level;
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );
  $max_rank = $row['max_rank'];
  // 3. updating the rank of our category to be after the previous max rank
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= ' SET rank = '.($max_rank + 1);
  $query.= ' WHERE id = '.$_GET['last'];
  $query.= ';';
  mysql_query( $query );
}
if ( isset( $_GET['first'] ) and is_numeric( $_GET['first'] ) )
{
  // to place our category as first, we simply say that is rank is 0, then
  // reordering will move category ranks correctly (first rank should be 1
  // and not 0)
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  $query.= ' SET rank = 0';
  $query.= ' WHERE id = '.$_GET['first'];
  $query.= ';';
  mysql_query( $query );
}
if ( isset( $_GET['delete'] ) and is_numeric( $_GET['delete'] ) )
{
  delete_category( $_GET['delete'] );
}
//------------------------------------------------------------------ reordering
function ordering( $id_uppercat )
{
  $rank = 1;
		
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' WHERE id_uppercat IS NULL';
  }
  else
  {
    $query.= ' WHERE id_uppercat = '.$id_uppercat;
  }
  $query.= ' ORDER BY rank ASC, dir ASC';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $query = 'UPDATE '.PREFIX_TABLE.'categories';
    $query.= ' SET rank = '.$rank;
    $query.= ' WHERE id = '.$row['id'];
    $query.= ';';
    mysql_query( $query );
    $rank++;
    ordering( $row['id'] );
  }
}
ordering( 'NULL' );
//-------------------------------------------------------------- errors display
if ( count( $errors ) != 0 )
{
  $vtp->addSession( $sub, 'errors' );
  foreach ( $errors as $error ) {
    $vtp->addSession( $sub, 'li' );
    $vtp->setVar( $sub, 'li.content', $error );
    $vtp->closeSession( $sub, 'li' );
  }
  $vtp->closeSession( $sub, 'errors' );
}
//---------------------------------------------------------------- page display
function display_cat_manager( $id_uppercat, $indent,
                              $uppercat_visible, $level )
{
  global $lang,$conf,$sub,$vtp,$page;
		
  // searching the min_rank and the max_rank of the category
  $query = 'SELECT MIN(rank) AS min, MAX(rank) AS max';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' WHERE id_uppercat IS NULL';
  }
  else
  {
    $query.= ' WHERE id_uppercat = '.$id_uppercat;
  }
  $query.= ';';
  $result = mysql_query( $query );
  $row    = mysql_fetch_array( $result );
  $min_rank = $row['min'];
  $max_rank = $row['max'];
		
  // will we use <th> or <td> lines ?
  $td    = 'td';
  $class = '';
  if ( $level > 0 ) $class = 'row'.$level;
  else              $td = 'th';
		
  $query = 'SELECT id,name,dir,nb_images,status,rank,site_id,visible';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  if ( !is_numeric( $id_uppercat ) )
  {
    $query.= ' WHERE id_uppercat IS NULL';
  }
  else
  {
    $query.= ' WHERE id_uppercat = '.$id_uppercat;
  }
  $query.= ' ORDER BY rank ASC';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $subcat_visible = true;

    $vtp->addSession( $sub, 'cat' );
    $vtp->setVar( $sub, 'cat.td', $td );
    $vtp->setVar( $sub, 'cat.class', $class );
    $vtp->setVar( $sub, 'cat.indent', $indent );
    $vtp->setVar( $sub, 'cat.name', $row['name'] );
    if ( $row['dir'] != '' )
    {
      $vtp->addSession( $sub, 'storage' );
      $vtp->setVar( $sub, 'storage.dir', $row['dir'] );
      $vtp->closeSession( $sub, 'storage' );
      // category can't be deleted
      $vtp->addSession( $sub, 'no_delete' );
      $vtp->closeSession( $sub, 'no_delete' );
    }
    else
    {
      $vtp->addSession( $sub, 'virtual' );
      $vtp->closeSession( $sub, 'virtual' );
      // category can be deleted
      $vtp->addSession( $sub, 'delete' );
      $url = './admin.php?page=cat_list&amp;delete='.$row['id'];
      $vtp->setVar( $sub, 'delete.delete_url', add_session_id( $url ) );
      $vtp->closeSession( $sub, 'delete' );
    }
    if ( $row['visible'] == 'false' or !$uppercat_visible )
    {
      $subcat_visible = false;
      $vtp->setVar( $sub, 'cat.invisible', $lang['cat_invisible'] );
    }
    if ( $row['status'] == 'private' )
    {
      $vtp->setVar( $sub, 'cat.private', $lang['private'] );
    }
    $vtp->setVar( $sub, 'cat.nb_picture', $row['nb_images'] );
    $url = add_session_id( './admin.php?page=cat_modify&amp;cat='.$row['id'] );
    $vtp->setVar( $sub, 'cat.edit_url', $url );
    if ( $row['rank'] != $min_rank )
    {
      $vtp->addSession( $sub, 'up' );
      $url = add_session_id( './admin.php?page=cat_list&amp;up='.$row['id'] );
      $vtp->setVar( $sub, 'up.up_url', $url );
      $vtp->closeSession( $sub, 'up' );
    }
    else if ( $min_rank != $max_rank )
    {
      $vtp->addSession( $sub, 'no_up' );
      $url = add_session_id( './admin.php?page=cat_list&amp;last='.$row['id']);
      $vtp->setVar( $sub, 'no_up.last_url', $url );
      $vtp->closeSession( $sub, 'no_up' );
    }
    if ( $row['rank'] != $max_rank )
    {
      $vtp->addSession( $sub, 'down' );
      $url = add_session_id( './admin.php?page=cat_list&amp;down='.$row['id']);
      $vtp->setVar( $sub, 'down.down_url', $url );
      $vtp->closeSession( $sub, 'down' );
    }
    else if ( $min_rank != $max_rank )
    {
      $vtp->addSession( $sub, 'no_down' );
      $url = add_session_id('./admin.php?page=cat_list&amp;first='.$row['id']);
      $vtp->setVar( $sub, 'no_down.first_url', $url );
      $vtp->closeSession( $sub, 'no_down' );
    }
    if ( $row['nb_images'] > 0 )
    {
      $vtp->addSession( $sub, 'image_info' );
      $url = add_session_id( './admin.php?page=infos_images&amp;cat_id='
                             .$row['id'] );
      $vtp->setVar( $sub, 'image_info.image_info_url', $url );
      $vtp->closeSession( $sub, 'image_info' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_image_info' );
      $vtp->closeSession( $sub, 'no_image_info' );
    }
    if ( $row['status'] == 'private' )
    {
      $vtp->addSession( $sub, 'permission' );
      $url=add_session_id('./admin.php?page=cat_perm&amp;cat_id='.$row['id']);
      $vtp->setVar( $sub, 'permission.url', $url );
      $vtp->closeSession( $sub, 'permission' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_permission' );
      $vtp->closeSession( $sub, 'no_permission' );
    }
    // you can individually update a category only if it is on the main site
    // and if it's not a virtual category (a category is virtual if there is
    // no directory associated)
    if ( $row['site_id'] == 1 and $row['dir'] != '' )
    {
      $vtp->addSession( $sub, 'update' );
      $url = add_session_id('./admin.php?page=update&amp;update='.$row['id']);
      $vtp->setVar( $sub, 'update.update_url', $url );
      $vtp->closeSession( $sub, 'update' );
    }
    else
    {
      $vtp->addSession( $sub, 'no_update' );
      $vtp->closeSession( $sub, 'no_update' );
    }

    $vtp->closeSession( $sub, 'cat' );

    display_cat_manager( $row['id'], $indent.str_repeat( '&nbsp', 4 ),
                         $subcat_visible, $level + 1 );
  }
}
display_cat_manager( 'NULL', str_repeat( '&nbsp', 4 ), true, 0 );
// add a virtual category ?
$vtp->addSession( $sub, 'associate_cat' );
$vtp->setVar( $sub, 'associate_cat.value', '-1' );
$vtp->setVar( $sub, 'associate_cat.content', '' );
$vtp->closeSession( $sub, 'associate_cat' );
$page['plain_structure'] = get_plain_structure();
$structure = create_structure( '', array() );
display_categories( $structure, '&nbsp;' );
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>