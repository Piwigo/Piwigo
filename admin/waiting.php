<?php
/***************************************************************************
 *                                waiting.php                              *
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
//--------------------------------------------------------------------- updates
if ( isset( $_POST['submit'] ) )
{
  $query = 'SELECT id,cat_id,file,tn_ext';
  $query.= ' FROM '.PREFIX_TABLE.'waiting';
  $query.= " WHERE validated = 'false'";
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
  {
    $key = 'validate-'.$row['id'];
    if ( isset( $_POST[$key] ) )
    {
      if ( $_POST[$key] == 'true' )
      {
        // The uploaded element was validated, we have to set the
        // "validated" field to "true"
        $query = 'UPDATE '.PREFIX_TABLE.'waiting';
        $query.= " SET validated = 'true'";
        $query.= ' WHERE id = '.$row['id'];
        $query.= ';';
        mysql_query( $query );
      }
      else
      {
        // The uploaded element was refused, we have to delete its reference
        // in the database and to delete the element as well.
        $query = 'DELETE FROM '.PREFIX_TABLE.'waiting';
        $query.= ' WHERE id = '.$row['id'];
        $query.= ';';
        mysql_query( $query );
        // deletion of the associated files
        $cat = get_cat_info( $row['cat_id'] );
        unlink( '.'.$cat['dir'].$row['file'] );
        if ( $row['tn_ext'] != '' )
        {
          $thumbnail = $conf['prefix_thumbnail'];
          $thumbnail.= get_filename_wo_extension( $row['file'] );
          $thumbnail.= '.'.$row['tn_ext'];
          $url = '.'.$cat['dir'].'thumbnail/'.$thumbnail;
          unlink( $url );
        }
      }
    }
  }
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open( '../template/'.$user['template'].'/admin/waiting.vtp' );
$tpl = array( 'category','date','author','thumbnail','file','delete',
              'submit' );
templatize_array( $tpl, 'lang', $sub );
//---------------------------------------------------------------- form display
$cat_names = array();
$query = 'SELECT id,cat_id,file,username,mail_address,date,tn_ext';
$query.= ' FROM '.PREFIX_TABLE.'waiting';
$query.= " WHERE validated = 'false'";
$query.= ' ORDER BY cat_id';
$query.= ';';
$result = mysql_query( $query );
$i = 0;
while ( $row = mysql_fetch_array( $result ) )
{
  $vtp->addSession( $sub, 'picture' );
  $vtp->setVar( $sub, 'picture.id', $row['id'] );
  if ( $i++ % 2 == 0 )
  {
    $vtp->setVar( $sub, 'picture.class', 'row2' );
  }
  if ( !isset( $cat_names[$row['cat_id']] ) )
  {
    $cat = get_cat_info( $row['cat_id'] );
    $cat_names[$row['cat_id']] = array();
    $cat_names[$row['cat_id']]['dir'] = '.'.$cat['dir'];
    $cat_names[$row['cat_id']]['display_name'] =
      get_cat_display_name( $cat['name'], ' &gt; ', 'font-weight:bold;' );
  }
  // category name
  $vtp->setVar( $sub, 'picture.cat_name',
                $cat_names[$row['cat_id']]['display_name'] );
  // date displayed like this (in English ) :
  //                     Sunday 15 June 2003 21:29
  $date = $lang['day'][date( 'w', $row['date'] )];   // Sunday
  $date.= date( ' j ', $row['date'] );               // 15
  $date.= $lang['month'][date( 'n', $row['date'] )]; // June
  $date.= date( ' Y G:i', $row['date'] );            // 2003 21:29
  $vtp->setVar( $sub, 'picture.date', $date );
  // file preview link
  $url = $cat_names[$row['cat_id']]['dir'].$row['file'];
  $vtp->setVar( $sub, 'picture.preview_url', $url );
  // file name
  $vtp->setVar( $sub, 'picture.file', $row['file'] );
  // is there an existing associated thumnail ?
  if ( $row['tn_ext'] != '' )
  {
    $vtp->addSession( $sub, 'thumbnail' );
    $thumbnail = $conf['prefix_thumbnail'];
    $thumbnail.= get_filename_wo_extension( $row['file'] );
    $thumbnail.= '.'.$row['tn_ext'];
    $url = $cat_names[$row['cat_id']]['dir'].'thumbnail/'.$thumbnail;
    $vtp->setVar( $sub, 'thumbnail.preview_url', $url );
    $vtp->setVar( $sub, 'thumbnail.file', $thumbnail );
    $vtp->closeSession( $sub, 'thumbnail' );
  }
  else
  {
    $vtp->addSession( $sub, 'no_thumbnail' );
    $vtp->closeSession( $sub, 'no_thumbnail' );
  }
  // username and associated mail address
  $vtp->setVar( $sub, 'picture.mail_address', $row['mail_address'] );
  $vtp->setVar( $sub, 'picture.username', $row['username'] );

  $vtp->closeSession( $sub, 'picture' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>