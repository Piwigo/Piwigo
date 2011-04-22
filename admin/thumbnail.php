<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                          Load configuration                           |
// +-----------------------------------------------------------------------+
prepare_upload_configuration();

$upload_form_config = get_upload_form_config();

$form_values = array();

foreach ($upload_form_config as $param_shortname => $param)
{
  $param_name = 'upload_form_'.$param_shortname;
  $form_values[$param_shortname] = $conf[$param_name];
}

// +-----------------------------------------------------------------------+
// |                   search pictures without thumbnails                  |
// +-----------------------------------------------------------------------+
$wo_thumbnails = array();

// what is the directory to search in ?
$query = '
SELECT galleries_url FROM '.SITES_TABLE.'
  WHERE galleries_url NOT LIKE \'http://%\'
;';
$result = pwg_query($query);
while ( $row=pwg_db_fetch_assoc($result) )
{
  $basedir = preg_replace('#/*$#', '', $row['galleries_url']);
  $fs = get_fs($basedir);

  // because isset is one hundred time faster than in_array
  $fs['thumbnails'] = array_flip($fs['thumbnails']);

  foreach ($fs['elements'] as $path)
  {
    // only pictures need thumbnails
    if (in_array(get_extension($path), $conf['picture_ext']))
    {
      $dirname = dirname($path);
      $filename = basename($path);
  
      // only files matching the authorized filename pattern can be considered
      // as "without thumbnail"
      if (!preg_match('/^[a-zA-Z0-9-_.]+$/', $filename))
      {
        continue;
      }
      
      // searching the element
      $filename_wo_ext = get_filename_wo_extension($filename);
      $tn_ext = '';
      $base_test = $dirname.'/'.$conf['dir_thumbnail'].'/';
      $base_test.= $conf['prefix_thumbnail'].$filename_wo_ext.'.';
      foreach ($conf['picture_ext'] as $ext)
      {
        if (isset($fs['thumbnails'][$base_test.$ext]))
        {
          $tn_ext = $ext;
          break;
        }
      }
      
      if (empty($tn_ext))
      {
        array_push($wo_thumbnails, $path);
      }
    }
  } // next element
} // next site id

// +-----------------------------------------------------------------------+
// |             form & pictures without thumbnails display                |
// +-----------------------------------------------------------------------+
$template->set_filenames( array('thumbnail'=>'thumbnail.tpl') );

if (count($wo_thumbnails) > 0)
{
  foreach ($wo_thumbnails as $path)
  {
    list($width, $height) = getimagesize($path);
    $size = floor(filesize($path) / 1024).' KB';

    $template->append(
      'remainings',
      array(
        'PATH'=>$path,
        'FILESIZE_IMG'=>$size,
        'WIDTH_IMG'=>$width,
        'HEIGHT_IMG'=>$height,
      )
    );
  }
}

foreach (array_keys($upload_form_config) as $field)
{
  if (is_bool($upload_form_config[$field]['default']))
  {
    $form_values[$field] = $form_values[$field] ? 'checked="checked"' : '';
  }
}

$template->assign(
  array(
    'F_ACTION' => get_root_url().'admin.php?page=thumbnail',
    'values' => $form_values,
    'TOTAL_NB_REMAINING' => count($wo_thumbnails),
    'U_HELP' => get_root_url().'admin/popuphelp.php?page=thumbnail',
  )
);

$template->assign_var_from_handle('ADMIN_CONTENT', 'thumbnail');
?>
