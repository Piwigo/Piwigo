<?php
/***************************************************************************
 *                               edit_cat.php                              *
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
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/edit_cat.vtp' );
$tpl = array( 'remote_site','editcat_confirm','editcat_back','editcat_title1',
              'editcat_name', 'editcat_comment', 'editcat_status',
              'editcat_status_info', 'submit' );
templatize_array( $tpl, 'lang', $sub );
//--------------------------------------------------------- form criteria check
if ( isset( $_POST['submit'] ) )
{
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
  $url = add_session_id( './admin.php?page=cat' );
  $vtp->setVar( $sub, 'confirmation.back_url', $url );
  $vtp->closeSession( $sub, 'confirmation' );
}
//------------------------------------------------------------------------ form
$form_action = './admin.php?page=edit_cat&amp;cat='.$_GET['cat'];
$vtp->setVar( $sub, 'form_action', add_session_id( $form_action ) );

$query = 'SELECT a.id,name,dir,status,comment';
$query.= ',id_uppercat,site_id,galleries_url';
$query.= ' FROM '.PREFIX_TABLE.'categories as a, '.PREFIX_TABLE.'sites as b';
$query.= ' WHERE a.id = '.$_GET['cat'];
$query.= ' AND a.site_id = b.id';
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );
$result = get_cat_info( $row['id'] );

$cat_name = get_cat_display_name( $result['name'], ' - ', '' );
$vtp->setVar( $sub, 'cat:name', $cat_name );
$vtp->setVar( $sub, 'cat:dir', $row['dir'] );
if ( $row['site_id'] != 1 )
{
  $vtp->addSession( $sub, 'server' );
  $vtp->setVar( $sub, 'server.url', $row['galleries_url'] );
  $vtp->closeSession( $sub, 'server' );
}
$vtp->setVar( $sub, 'name',    $row['name'] );
$vtp->setVar( $sub, 'comment', $row['comment'] );
$options = get_enums( PREFIX_TABLE.'categories', 'status' );
foreach ( $options as $option  ) {
  $vtp->addSession( $sub, 'status_option' );
  $vtp->setVar( $sub, 'status_option.option', $option );
  if ( $option == $row['status'] )
  {
    $vtp->setVar( $sub, 'status_option.checked', ' checked="checked"' );  
  }
  $vtp->closeSession( $sub, 'status_option' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>