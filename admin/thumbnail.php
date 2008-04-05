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

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);

//------------------------------------------------------------------- functions
// RatioResizeImg creates a new picture (a thumbnail since it is supposed to
// be smaller than original picture !) in the sub directory named
// "thumbnail".
function RatioResizeImg($path, $newWidth, $newHeight, $tn_ext)
{
  global $conf, $lang, $page;

  if (!function_exists('gd_info'))
  {
    return;
  }

  $filename = basename($path);
  $dirname = dirname($path);
  
  // extension of the picture filename
  $extension = get_extension($filename);

  if (in_array($extension, array('jpg', 'JPG', 'jpeg', 'JPEG')))
  {
    $srcImage = @imagecreatefromjpeg($path);
  }
  else if ($extension == 'png' or $extension == 'PNG')
  {
    $srcImage = @imagecreatefrompng($path);
  }
  else
  {
    unset($extension);
  }

  if ( isset( $srcImage ) )
  {
    // width/height
    $srcWidth    = imagesx( $srcImage ); 
    $srcHeight   = imagesy( $srcImage ); 
    $ratioWidth  = $srcWidth/$newWidth;
    $ratioHeight = $srcHeight/$newHeight;

    // maximal size exceeded ?
    if ( ( $ratioWidth > 1 ) or ( $ratioHeight > 1 ) )
    {
      if ( $ratioWidth < $ratioHeight)
      { 
        $destWidth = $srcWidth/$ratioHeight;
        $destHeight = $newHeight; 
      }
      else
      { 
        $destWidth = $newWidth; 
        $destHeight = $srcHeight/$ratioWidth;
      }
    }
    else
    {
      $destWidth = $srcWidth;
      $destHeight = $srcHeight;
    }
    // according to the GD version installed on the server
    if ( $_POST['gd'] == 2 )
    {
      // GD 2.0 or more recent -> good results (but slower)
      $destImage = imagecreatetruecolor( $destWidth, $destHeight); 
      imagecopyresampled( $destImage, $srcImage, 0, 0, 0, 0,
                          $destWidth,$destHeight,$srcWidth,$srcHeight );
    }
    else
    {
      // GD prior to version  2 -> pretty bad results :-/ (but fast)
      $destImage = imagecreate( $destWidth, $destHeight);
      imagecopyresized( $destImage, $srcImage, 0, 0, 0, 0,
                        $destWidth,$destHeight,$srcWidth,$srcHeight );
    }

    if (($tndir = mkget_thumbnail_dir($dirname, $page['errors'])) == false)
    {
      return false;
    }

    $dest_file = $tndir.'/'.$conf['prefix_thumbnail'];
    $dest_file.= get_filename_wo_extension($filename);
    $dest_file.= '.'.$tn_ext;
    
    // creation and backup of final picture
    if (!is_writable($tndir))
    {
      array_push($page['errors'], '['.$tndir.'] : '.l10n('no_write_access'));
      return false;
    }
    imagejpeg($destImage, $dest_file);
    // freeing memory ressources
    imagedestroy( $srcImage );
    imagedestroy( $destImage );
    
    list($tn_width, $tn_height) = getimagesize($dest_file);
    $tn_size = floor(filesize($dest_file) / 1024).' KB';
    
    $info = array( 'path'      => $path,
                   'tn_file'   => $dest_file,
                   'tn_width'  => $tn_width,
                   'tn_height' => $tn_height,
                   'tn_size'   => $tn_size );
    return $info;
  }
  // error
  else
  {
    echo l10n('tn_no_support')." ";
    if ( isset( $extenstion ) )
    {
      echo l10n('tn_format').' '.$extension;
    }
    else
    {
      echo l10n('tn_thisformat');
    }
    exit();
  }
}

$pictures = array();
$stats = array();
// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filenames( array('thumbnail'=>'admin/thumbnail.tpl') );

$template->assign(array(
  'U_HELP' => PHPWG_ROOT_PATH.'popuphelp.php?page=thumbnail',
  ));
// +-----------------------------------------------------------------------+
// |                   search pictures without thumbnails                  |
// +-----------------------------------------------------------------------+
$wo_thumbnails = array();
$thumbnalized = array();


// what is the directory to search in ?
$query = '
SELECT galleries_url FROM '.SITES_TABLE.'
  WHERE galleries_url NOT LIKE "http://%"
;';
$result = pwg_query($query);
while ( $row=mysql_fetch_assoc($result) )
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
      $base_test = $dirname.'/thumbnail/';
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
// |                         thumbnails creation                           |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']))
{
  $times = array();
  $infos = array();
  
  // checking criteria
  if (!ereg('^[0-9]{2,3}$', $_POST['width']) or $_POST['width'] < 10)
  {
    array_push($page['errors'], l10n('tn_err_width').' 10');
  }
  if (!ereg('^[0-9]{2,3}$', $_POST['height']) or $_POST['height'] < 10)
  {
    array_push($page['errors'], l10n('tn_err_height').' 10');
  }
  
  // picture miniaturization
  if (count($page['errors']) == 0)
  {
    $num = 1;
    foreach ($wo_thumbnails as $path)
    {
      if (is_numeric($_POST['n']) and $num > $_POST['n'])
      {
        break;
      }
      
      $starttime = get_moment();
      if ($info = RatioResizeImg($path,$_POST['width'],$_POST['height'],'jpg'))
      {
        $endtime = get_moment();
        $info['time'] = ($endtime - $starttime) * 1000;
        array_push($infos, $info);
        array_push($times, $info['time']);
        array_push($thumbnalized, $path);
        $num++;
      }
      else
      {
        break;
      }
    }

    if (count($infos) > 0)
    {
      $sum = array_sum($times);
      $average = $sum / count($times);
      sort($times, SORT_NUMERIC);
      $max = array_pop($times);
      if (count($thumbnalized) == 1)
      {
        $min = $max;
      }
      else
      {
        $min = array_shift($times);
      }
      
      $tpl_var = 
        array(
          'TN_NB'=>count($infos),
          'TN_TOTAL'=>number_format($sum, 2, '.', ' ').' ms',
          'TN_MAX'=>number_format($max, 2, '.', ' ').' ms',
          'TN_MIN'=>number_format($min, 2, '.', ' ').' ms',
          'TN_AVERAGE'=>number_format($average, 2, '.', ' ').' ms',
          'elements' => array()
          );
      
      foreach ($infos as $i => $info)
      {
        $tpl_var['elements'][] =
          array(
            'PATH'=>$info['path'],
            'TN_FILE_IMG'=>$info['tn_file'],
            'TN_FILESIZE_IMG'=>$info['tn_size'],
            'TN_WIDTH_IMG'=>$info['tn_width'],
            'TN_HEIGHT_IMG'=>$info['tn_height'],
            'GEN_TIME'=>number_format($info['time'], 2, '.', ' ').' ms',
            );
      }
      $template->assign('results', $tpl_var);
    }
  }
}
// +-----------------------------------------------------------------------+
// |             form & pictures without thumbnails display                |
// +-----------------------------------------------------------------------+
$remainings = array_diff($wo_thumbnails, $thumbnalized);

if (count($remainings) > 0)
{
  $form_url = get_root_url().'admin.php?page=thumbnail';
  $gd = !empty($_POST['gd']) ? $_POST['gd'] : 2;
  $width = !empty($_POST['width']) ? $_POST['width'] : $conf['tn_width'];
  $height = !empty($_POST['height']) ? $_POST['height'] : $conf['tn_height'];
  $n = !empty($_POST['n']) ? $_POST['n'] : 5;
  
  $template->assign(
    'params',
    array(
      'F_ACTION'=> $form_url,
      'GD_SELECTED' => $gd,
      'N_SELECTED' => $n,
      'WIDTH_TN'=>$width,
      'HEIGHT_TN'=>$height
      ));

  $template->assign(
    'TOTAL_NB_REMAINING',
    count($remainings));

  foreach ($remainings as $path)
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
        ));
  }
}

// +-----------------------------------------------------------------------+
// |                           return to admin                             |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'thumbnail');
?>
