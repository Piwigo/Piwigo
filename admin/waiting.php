<?php
// +-----------------------------------------------------------------------+
// |                              waiting.php                              |
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
if( !defined("PHPWG_ROOT_PATH") )
{
	die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
//--------------------------------------------------------------------- updates
if ( isset( $_POST['submit'] ) )
{
  $query = 'SELECT * FROM '.WAITING_TABLE;
  $query.= " WHERE validated = 'false';";
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
        $query = 'UPDATE '.WAITING_TABLE;
        $query.= " SET validated = 'true'";
        $query.= ' WHERE id = '.$row['id'];
        $query.= ';';
        mysql_query( $query );
        // linking logically the picture to its storage category
        $query = 'INSERT INTO';
      }
      else
      {
        // The uploaded element was refused, we have to delete its reference
        // in the database and to delete the element as well.
        $query = 'DELETE FROM '.WAITING_TABLE;
        $query.= ' WHERE id = '.$row['id'];
        $query.= ';';
        mysql_query( $query );
        // deletion of the associated files
        $dir = get_complete_dir( $row['storage_category_id'] );
        unlink( '.'.$dir.$row['file'] );
        if ( $row['tn_ext'] != '' )
        {
          $thumbnail = $conf['prefix_thumbnail'];
          $thumbnail.= get_filename_wo_extension( $row['file'] );
          $thumbnail.= '.'.$row['tn_ext'];
          $url = PHPWG_ROOT_PATH.$dir.'thumbnail/'.$thumbnail;
          unlink( $url );
        }
      }
    }
  }
}

//----------------------------------------------------- template initialization
$template->set_filenames(array('waiting'=>'admin/waiting.tpl'));
$template->assign_vars(array(
  'L_WAITING_CONFIRMATION'=>$lang['waiting_update'],
  'L_AUTHOR'=>$lang['author'],
  'L_THUMBNAIL'=>$lang['thumbnail'],
  'L_DATE'=>$lang['date'],
  'L_FILE'=>$lang['file'],
  'L_CATEGORY'=>$lang['category'],
  'L_SUBMIT'=>$lang['submit'],
  'L_DELETE'=>$lang['delete'],
  
  'F_ACTION'=>add_session_id(str_replace( '&', '&amp;', $_SERVER['REQUEST_URI'] ))
  ));
  
//-------------------------------------------------------- confirmation message
if (isset($_POST['submit']))
{
  $template->assign_block_vars('confirmation' ,array());
}
//---------------------------------------------------------------- form display
$cat_names = array();
$query = 'SELECT * FROM '.WAITING_TABLE;
$query.= " WHERE validated = 'false'";
$query.= ' ORDER BY storage_category_id';
$query.= ';';
$result = mysql_query( $query );
$i = 0;
while ( $row = mysql_fetch_array( $result ) )
{
  if ( !isset( $cat_names[$row['storage_category_id']] ) )
  {
    $cat = get_cat_info( $row['storage_category_id'] );
    $cat_names[$row['storage_category_id']] = array();
    $cat_names[$row['storage_category_id']]['dir'] =
      PHPWG_ROOT_PATH.get_complete_dir( $row['storage_category_id'] );
    $cat_names[$row['storage_category_id']]['display_name'] =
      get_cat_display_name( $cat['name'], ' &gt; ', 'font-weight:bold;' );
  }
  $preview_url = PHPWG_ROOT_PATH.$cat_names[$row['storage_category_id']]['dir'].$row['file'];
  $class='row1';
  if ( $i++ % 2== 0 ) $class='row2';
  
  $template->assign_block_vars('picture' ,array(
    'WAITING_CLASS'=>$class,
    'CATEGORY_IMG'=>$cat_names[$row['storage_category_id']]['display_name'],
    'ID_IMG'=>$row['id'],
	'DATE_IMG'=>format_date( $row['date'], 'unix', true ),
	'FILE_IMG'=>$row['file'],
	'PREVIEW_URL_IMG'=>$preview_url, 
	'UPLOAD_EMAIL'=>$row['mail_address'],
	'UPLOAD_USERNAME'=>$row['username']
	));

  // is there an existing associated thumnail ?
  if ( !empty( $row['tn_ext'] ))
  {
    $thumbnail = $conf['prefix_thumbnail'];
    $thumbnail.= get_filename_wo_extension( $row['file'] );
    $thumbnail.= '.'.$row['tn_ext'];
	$url = $cat_names[$row['storage_category_id']]['dir'];
    $url.= 'thumbnail/'.$thumbnail;
	
    $template->assign_block_vars('picture.thumbnail' ,array(
	  'PREVIEW_URL_TN_IMG'=>$url,
	  'FILE_TN_IMG'=>$thumbnail
	  ));
  }
}
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'waiting');
?>
