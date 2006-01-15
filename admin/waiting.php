<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
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
if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php');
//--------------------------------------------------------------------- updates

if (isset($_POST))
{
  $to_validate = array();
  $to_reject = array();
  
  if (isset($_POST['submit']))
  {    
    foreach (explode(',', $_POST['list']) as $waiting_id)
    {
      if (isset($_POST['action-'.$waiting_id]))
      {
        switch ($_POST['action-'.$waiting_id])
        {
          case 'reject' :
          {
            array_push($to_reject, $waiting_id);
            break;
          }
          case 'validate' :
          {
            array_push($to_validate, $waiting_id);
            break;
          }
        }
      }
    }
  }
  else if (isset($_POST['validate-all']))
  {
    $to_validate = explode(',', $_POST['list']);
  }
  else if (isset($_POST['reject-all']))
  {
    $to_reject = explode(',', $_POST['list']);
  }

  if (count($to_validate) > 0)
  {
    $query = '
UPDATE '.WAITING_TABLE.'
  SET validated = \'true\'
  WHERE id IN ('.implode(',', $to_validate).')
;';
    pwg_query($query);

    array_push(
      $page['infos'],
      sprintf(
        l10n('%d waiting pictures validated'),
        count($to_validate)
        )
      );
  }

  if (count($to_reject) > 0)
  {
    // The uploaded element was refused, we have to delete its reference in
    // the database and to delete the element as well.
    $query = '
SELECT id, storage_category_id, file, tn_ext
  FROM '.WAITING_TABLE.'
  WHERE id IN ('.implode(',', $to_reject).')
;';
    $result = pwg_query($query);
    while($row = mysql_fetch_array($result))
    {
      $dir = get_complete_dir($row['storage_category_id']);
      unlink($dir.$row['file']);
      if (isset($row['tn_ext']) and $row['tn_ext'] != '')
      {
        unlink(
          get_thumbnail_src(
            $dir.$row['file'],
            $row['tn_ext']
            )
          );
      }
      else if (@is_file(get_thumbnail_src($dir.$row['file'], 'jpg')))
      {
        unlink(
          get_thumbnail_src(
            $dir.$row['file'],
            'jpg'
            )
          );
      }
    }
    
    $query = '
DELETE
  FROM '.WAITING_TABLE.'
  WHERE id IN ('.implode(',', $to_reject).')
;';
    pwg_query($query);

    array_push(
      $page['infos'],
      sprintf(
        l10n('%d waiting pictures rejected'),
        count($to_reject)
        )
      );
  }
}

//----------------------------------------------------- template initialization
$template->set_filenames(array('waiting'=>'admin/waiting.tpl'));
$template->assign_vars(array(
  'L_AUTHOR'=>$lang['author'],
  'L_THUMBNAIL'=>$lang['thumbnail'],
  'L_DATE'=>$lang['date'],
  'L_FILE'=>$lang['file'],
  'L_CATEGORY'=>$lang['category'],
  'L_SUBMIT'=>$lang['submit'],
  'L_RESET'=>$lang['reset'],
  'L_DELETE'=>$lang['delete'],
  
  'F_ACTION'=>str_replace( '&', '&amp;', $_SERVER['REQUEST_URI'])
  ));
  
//---------------------------------------------------------------- form display
$cat_names = array();
$list = array();

$query = 'SELECT * FROM '.WAITING_TABLE;
$query.= " WHERE validated = 'false'";
$query.= ' ORDER BY storage_category_id';
$query.= ';';
$result = pwg_query( $query );
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
      get_cat_display_name($cat['name']);
  }
  $preview_url = PHPWG_ROOT_PATH.$cat_names[$row['storage_category_id']]['dir'].$row['file'];
  $class='row1';
  if ( $i++ % 2== 0 ) $class='row2';
  
  $template->assign_block_vars(
    'picture',
    array(
      'WAITING_CLASS'=>$class,
      'CATEGORY_IMG'=>$cat_names[$row['storage_category_id']]['display_name'],
      'ID_IMG'=>$row['id'],
      'DATE_IMG' => date('Y-m-d H:i:s', $row['date']),
      'FILE_TITLE'=>$row['file'],
      'FILE_IMG' =>
        (strlen($row['file']) > 10) ?
          (substr($row['file'], 0, 10)).'...' : $row['file'],
      'PREVIEW_URL_IMG'=>$preview_url, 
      'UPLOAD_EMAIL'=>$row['mail_address'],
      'UPLOAD_USERNAME'=>$row['username']
      )
    );

  // is there an existing associated thumnail ?
  if ( !empty( $row['tn_ext'] ))
  {
    $thumbnail = $conf['prefix_thumbnail'];
    $thumbnail.= get_filename_wo_extension( $row['file'] );
    $thumbnail.= '.'.$row['tn_ext'];
	$url = $cat_names[$row['storage_category_id']]['dir'];
    $url.= 'thumbnail/'.$thumbnail;
	
    $template->assign_block_vars(
      'picture.thumbnail',
      array(
        'PREVIEW_URL_TN_IMG' => $url,
        'FILE_TN_IMG' =>
          (strlen($thumbnail) > 10) ?
            (substr($thumbnail, 0, 10)).'...' : $thumbnail,
        'FILE_TN_TITLE' => $thumbnail
        )
      );
  }

  array_push($list, $row['id']);
}

$template->assign_vars(
  array(
    'LIST' => implode(',', $list)
    )
  );
  
//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'waiting');
?>
