<?php
// +-----------------------------------------------------------------------+
// |                            cat_modify.php                             |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                     |
// +-----------------------------------------------------------------------+
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

include_once( './admin/include/isadmin.inc.php' );
//----------------------------------------------------- template initialization
$sub = $vtp->Open( './template/'.$user['template'].'/admin/cat_modify.vtp' );
$tpl = array( 'remote_site','editcat_confirm','editcat_back','editcat_title1',
              'editcat_name','editcat_comment','editcat_status',
              'editcat_visible','editcat_visible_info', 'submit',
              'editcat_uploadable','cat_virtual','cat_parent' );
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
  
  $query = 'UPDATE '.PREFIX_TABLE.'categories';

  $query.= ' SET name = ';
  if ( $_POST['name'] == '' )
    $query.= 'NULL';
  else
    $query.= "'".htmlentities( $_POST['name'], ENT_QUOTES)."'";

  $query.= ', comment = ';
  if ( $_POST['comment'] == '' )
    $query.= 'NULL';
  else
    $query.= "'".htmlentities( $_POST['comment'], ENT_QUOTES )."'";

  $query.= ", status = '".$_POST['status']."'";
  $query.= ", visible = '".$_POST['visible']."'";

  if ( isset( $_POST['uploadable'] ) )
    $query.= ", uploadable = '".$_POST['uploadable']."'";

  if ( isset( $_POST['associate'] ) )
  {
    $query.= ', id_uppercat = ';
    if ( $_POST['associate'] == -1 or $_POST['associate'] == '' )
      $query.= 'NULL';
    else
      $query.= $_POST['associate'];
  }
  $query.= ' WHERE id = '.$_GET['cat'];
  $query.= ';';
  mysql_query( $query );

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
    // resynchronize all users
    synchronize_all_users();
  }

  // checking users favorites
  $query = 'SELECT id';
  $query.= ' FROM '.PREFIX_TABLE.'users';
  $query.= ';';
  $result = mysql_query( $query );
  while ( $row = mysql_fetch_array( $result ) )
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

$query = 'SELECT a.id,name,dir,status,comment,uploadable';
$query.= ',id_uppercat,site_id,galleries_url,visible';
$query.= ' FROM '.PREFIX_TABLE.'categories as a, '.PREFIX_TABLE.'sites as b';
$query.= ' WHERE a.id = '.$_GET['cat'];
$query.= ' AND a.site_id = b.id';
$query.= ';';
$row = mysql_fetch_array( mysql_query( $query ) );

if ( !isset( $row['dir'] ) ) $row['dir'] = '';
if ( !isset( $row['id_uppercat'] ) ) $row['id_uppercat'] = '';

$result = get_cat_info( $row['id'] );
// cat name
$cat_name = get_cat_display_name( $result['name'], ' - ', '' );
$vtp->setVar( $sub, 'cat:name', $cat_name );
// cat dir
if ( $row['dir'] != '' )
{
  $vtp->addSession( $sub, 'storage' );
  $vtp->setVar( $sub, 'storage.dir', $row['dir'] );
  $vtp->closeSession( $sub, 'storage' );
}
else
{
  $vtp->addSession( $sub, 'virtual' );
  $vtp->closeSession( $sub, 'virtual' );
}
// remote site ?
if ( $row['site_id'] != 1 )
{
  $vtp->addSession( $sub, 'server' );
  $vtp->setVar( $sub, 'server.url', $row['galleries_url'] );
  $vtp->closeSession( $sub, 'server' );
}
$vtp->setVar( $sub, 'name',    $row['name'] );
if ( !isset( $row['comment'] ) ) $row['comment'] = '';
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
// uploadable : true or false
// a category can be uploadable if :
//  1. upload is authorized
//  2. category is not virtual
//  3. category is on the main site
if ( $conf['upload_available'] and $row['dir'] != '' and $row['site_id'] == 1 )
{
  $vtp->addSession( $sub, 'uploadable' );
  $vtp->addSession( $sub, 'uploadable_option' );
  $vtp->setVar( $sub, 'uploadable_option.value', 'true' );
  $vtp->setVar( $sub, 'uploadable_option.option', $lang['yes'] );
  $checked = '';
  if ( $row['uploadable'] == 'true' )
  {
    $checked = ' checked="checked"';
  }
  $vtp->setVar( $sub, 'uploadable_option.checked', $checked );
  $vtp->closeSession( $sub, 'uploadable_option' );
  $vtp->addSession( $sub, 'uploadable_option' );
  $vtp->setVar( $sub, 'uploadable_option.value', 'false' );
  $vtp->setVar( $sub, 'uploadable_option.option', $lang['no'] );
  $checked = '';
  if ( $row['uploadable'] == 'false' )
  {
    $checked = ' checked="checked"';
  }
  $vtp->setVar( $sub, 'uploadable_option.checked', $checked );
  $vtp->closeSession( $sub, 'uploadable_option' );
  $vtp->closeSession( $sub, 'uploadable' );
}
// can the parent category be changed ? (is the category virtual ?)
if ( $row['dir'] == '' )
{
  $vtp->addSession( $sub, 'parent' );
  // We only show a List Of Values if the number of categories is less than
  // $conf['max_LOV_categories']
  $query = 'SELECT COUNT(id) AS nb_total_categories';
  $query.= ' FROM '.PREFIX_TABLE.'categories';
  $query.= ';';
  $countrow = mysql_fetch_array( mysql_query( $query ) );
  if ( $countrow['nb_total_categories'] < $conf['max_LOV_categories'] )
  {
    $vtp->addSession( $sub, 'associate_LOV' );
    $vtp->addSession( $sub, 'associate_cat' );
    $vtp->setVar( $sub, 'associate_cat.value', '-1' );
    $vtp->setVar( $sub, 'associate_cat.content', '' );
    $vtp->closeSession( $sub, 'associate_cat' );
    $page['plain_structure'] = get_plain_structure( true );
    $structure = create_structure( '', array() );
    display_categories( $structure, '&nbsp;', $row['id_uppercat'],$row['id'] );
    $vtp->closeSession( $sub, 'associate_LOV' );
  }
  // else, we only display a small text field, we suppose the administrator
  // knows the id of its category
  else
  {
    $vtp->addSession( $sub, 'associate_text' );
    $vtp->setVar( $sub, 'associate_text.value', $row['id_uppercat'] );
    $vtp->closeSession( $sub, 'associate_text' );
  }
  $vtp->closeSession( $sub, 'parent' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>
