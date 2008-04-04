<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
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
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2008 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | file          : $Id$
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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_waiting.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

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
  elseif (isset($_POST['validate-all']) and !empty($_POST['list']))
  {
    $to_validate = explode(',', $_POST['list']);
  }
  elseif (isset($_POST['reject-all']) and !empty($_POST['list']))
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
      $element_info = array(
        'path' => $dir.$row['file'],
        'tn_ext' =>
          (isset($row['tn_ext']) and $row['tn_ext']!='') ? $row['tn_ext']:'jpg'
        );
      $tn_path = get_thumbnail_path( $element_info );

      if ( @is_file($tn_path) )
      {
        unlink( $tn_path );
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
$template->set_filenames(array('upload'=>'admin/upload.tpl'));

// TabSheet initialization
waiting_tabsheet();

$template->assign(array(
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
while ( $row = mysql_fetch_array( $result ) )
{
  if ( !isset( $cat_names[$row['storage_category_id']] ) )
  {
    $cat = get_cat_info( $row['storage_category_id'] );
    $cat_names[$row['storage_category_id']] = array();
    $cat_names[$row['storage_category_id']]['dir'] =
      PHPWG_ROOT_PATH.get_complete_dir( $row['storage_category_id'] );
    $cat_names[$row['storage_category_id']]['display_name'] =
      get_cat_display_name($cat['upper_names']);
  }
  $preview_url = PHPWG_ROOT_PATH.$cat_names[$row['storage_category_id']]['dir'].$row['file'];

  $tpl_var =
    array(
      'CATEGORY_IMG'=>$cat_names[$row['storage_category_id']]['display_name'],
      'ID_IMG'=>$row['id'],
      'DATE_IMG' => date('Y-m-d H:i:s', $row['date']),
      'FILE_TITLE'=>$row['file'],
      'FILE_IMG' =>
        (strlen($row['file']) > 10) ?
          (substr($row['file'], 0, 10)).'...' : $row['file'],
      'PREVIEW_URL_IMG'=>$preview_url,
      'UPLOAD_EMAIL'=>get_email_address_as_display_text($row['mail_address']),
      'UPLOAD_USERNAME'=>$row['username']
    );

  // is there an existing associated thumnail ?
  if ( !empty( $row['tn_ext'] ))
  {
    $thumbnail = $conf['prefix_thumbnail'];
    $thumbnail.= get_filename_wo_extension( $row['file'] );
    $thumbnail.= '.'.$row['tn_ext'];
	$url = $cat_names[$row['storage_category_id']]['dir'];
    $url.= 'thumbnail/'.$thumbnail;

    $tpl_var['thumbnail'] =
      array(
        'PREVIEW_URL_TN_IMG' => $url,
        'FILE_TN_IMG' =>
          (strlen($thumbnail) > 10) ?
            (substr($thumbnail, 0, 10)).'...' : $thumbnail,
        'FILE_TN_TITLE' => $thumbnail
      );
  }
  $template->append('pictures', $tpl_var);
  array_push($list, $row['id']);
}

$template->assign('LIST',implode(',', $list) );

//----------------------------------------------------------- sending html code
$template->assign_var_from_handle('ADMIN_CONTENT', 'upload');
?>
