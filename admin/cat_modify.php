<?php
/***************************************************************************
 *                               cat_modify.php                            *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
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
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/cat_modify.vtp' );
$tpl = array( 'remote_site','editcat_confirm','editcat_back','editcat_title1',
              'editcat_name','editcat_comment','editcat_status',
              'editcat_visible','editcat_status_info', 'submit' );
templatize_array( $tpl, 'lang', $sub );
//---------------------------------------------------------------- verification
if ( !is_numeric( $_GET['cat'] ) )
{
  $_GET['cat'] = '-1';
}
//--------------------------------------------------------- form criteria check
if ( isset( $_POST['submit'] ) )
{
  // if new status is different from previous one, deletion of all related
  // links for access rights
  $query = 'SELECT status';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ' WHERE id = '.$_GET['cat'];
  $query.= ';';
  $row = mysql_fetch_array( mysql_query( $query ) );

  if ( $_POST['status'] != $row['status'] )
  {
    // deletion of all access for groups concerning this category
    $query = 'DELETE';
    $query.= ' FROM '.PREFIX_TABLE.'group_access';
    $query.= ' WHERE cat_id = '.$_GET['cat'];
    mysql_query( $query );
    // deletion of all access for users concerning this category
    $query = 'DELETE';
    $query.= ' FROM '.PREFIX_TABLE.'user_access';
    $query.= ' WHERE cat_id = '.$_GET['cat'];
    mysql_query( $query );
  }
  
  $query = 'UPDATE '.PREFIX_TABLE.'categories';
  if ( $_POST['name'] == '' )
  {
    $query.= ' SET name = NULL';
  }
  else
  {
    $query.= " SET name = '".htmlentities( $_POST['name'], ENT_QUOTES)."'";
  }
  if ( $_POST['comment'] == '' )
  {
    $query.= ', comment = NULL';
  }
  else
  {
    $query.= ", comment = '".htmlentities( $_POST['comment'], ENT_QUOTES )."'";
  }
  $query.= ", status = '".$_POST['status']."'";
  $query.= ", visible = '".$_POST['visible']."'";
  $query.= " WHERE id = '".$_GET['cat']."'";
  $query.= ';';
  mysql_query( $query );

  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= " WHERE username != '".$conf['webmaster']."'";
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array ( $result ) )
  {
    check_favorites( $row['id'] );
  }
  $vtp->addSession( $sub, 'confirmation' );
  $url = add_session_id( './admin.php?page=cat_list' );
  $vtp->setVar( $sub, 'confirmation.back_url', $url );
  $vtp->closeSession( $sub, 'confirmation' );
}
//------------------------------------------------------------------------ form
$form_action = './admin.php?page=cat_modify&amp;cat='.$_GET['cat'];
$vtp->setVar( $sub, 'form_action', add_session_id( $form_action ) );

$query = 'SELECT a.id,name,dir,status,comment';
$query.= ',id_uppercat,site_id,galleries_url,visible';
$query.= ' FROM '.PREFIX_TABLE.'categories as a, '.PREFIX_TABLE.'sites as b';
$query.= ' WHERE a.id = '.$_GET['cat'];
$query.= ' AND a.site_id = b.id';
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );
$result = get_cat_info( $row['id'] );
// cat name
$cat_name = get_cat_display_name( $result['name'], ' - ', '' );
$vtp->setVar( $sub, 'cat:name', $cat_name );
// cat dir
$vtp->setVar( $sub, 'cat:dir', $row['dir'] );
// remote site ?
if ( $row['site_id'] != 1 )
{
  $vtp->addSession( $sub, 'server' );
  $vtp->setVar( $sub, 'server.url', $row['galleries_url'] );
  $vtp->closeSession( $sub, 'server' );
}
$vtp->setVar( $sub, 'name',    $row['name'] );
$vtp->setVar( $sub, 'comment', $row['comment'] );
// status : public, private...
$options = get_enums( PREFIX_TABLE.'categories', 'status' );
foreach ( $options as $option  ) {
  $vtp->addSession( $sub, 'status_option' );
  $vtp->setVar( $sub, 'status_option.option', $lang[$option] );
  $vtp->setVar( $sub, 'status_option.value', $option );
  if ( $option == $row['status'] )
  {
    $vtp->setVar( $sub, 'status_option.checked', ' checked="checked"' );  
  }
  $vtp->closeSession( $sub, 'status_option' );
}
// visible : true or false
$vtp->addSession( $sub, 'visible_option' );
$vtp->setVar( $sub, 'visible_option.value', 'true' );
$vtp->setVar( $sub, 'visible_option.option', $lang['yes'] );
$checked = '';
if ( $row['visible'] == 'true' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'visible_option.checked', $checked );
$vtp->closeSession( $sub, 'visible_option' );
$vtp->addSession( $sub, 'visible_option' );
$vtp->setVar( $sub, 'visible_option.value', 'false' );
$vtp->setVar( $sub, 'visible_option.option', $lang['no'] );
$checked = '';
if ( $row['visible'] == 'false' )
{
  $checked = ' checked="checked"';
}
$vtp->setVar( $sub, 'visible_option.checked', $checked );
$vtp->closeSession( $sub, 'visible_option' );
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>