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
include_once( PHPWG_ROOT_PATH.'admin/include/isadmin.inc.php' );
//------------------------------------------------------------------- functions
// RatioResizeImg creates a new picture (a thumbnail since it is supposed to
// be smaller than original picture !) in the sub directory named
// "thumbnail".
function RatioResizeImg($path, $newWidth, $newHeight, $tn_ext)
{
  global $conf, $lang, $errors;

  $filename = basename($path);
  $dirname = dirname($path);
  
  // extension of the picture filename
  $extension = get_extension($filename);

  if ($extension == 'jpg' or $extension == 'JPG')
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
    
    $tndir = $dirname.'/thumbnail';
    if (!is_dir($tndir))
    {
      if (!is_writable($dirname))
      {
        array_push($errors, '['.$dirname.'] : '.$lang['no_write_access']);
        return false;
      }
      umask(0000);
      mkdir($tndir, 0777);
    }
    
    $dest_file = $tndir.'/'.$conf['prefix_thumbnail'];
    $dest_file.= get_filename_wo_extension($filename);
    $dest_file.= '.'.$tn_ext;
    
    // creation and backup of final picture
    if (!is_writable($tndir))
    {
      array_push($errors, '['.$tndir.'] : '.$lang['no_write_access']);
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
    echo $lang['tn_no_support']." ";
    if ( isset( $extenstion ) )
    {
      echo $lang['tn_format'].' '.$extension;
    }
    else
    {
      echo $lang['tn_thisformat'];
    }
    exit();
  }
}

$errors = array();
$pictures = array();
$stats = array();
// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+
$template->set_filenames( array('thumbnail'=>'admin/thumbnail.tpl') );

$template->assign_vars(array(
  'L_THUMBNAIL_TITLE'=>$lang['tn_dirs_title'],
  'L_UNLINK'=>$lang['tn_no_missing'],
  'L_MISSING_THUMBNAILS'=>$lang['tn_dirs_alone'],
  'L_RESULTS'=>$lang['tn_results_title'],
  'L_PATH'=>$lang['path'],
  'L_FILESIZE'=>$lang['filesize'],
  'L_WIDTH'=>$lang['tn_width'],
  'L_HEIGHT'=>$lang['tn_height'],
  'L_GENERATED'=>$lang['tn_results_gen_time'],
  'L_THUMBNAIL'=>$lang['thumbnail'],
  'L_PARAMS'=>$lang['tn_params_title'],
  'L_GD'=>$lang['tn_params_GD'],
  'L_GD_INFO'=>$lang['tn_params_GD_info'],
  'L_WIDTH_INFO'=>$lang['tn_params_width_info'],
  'L_HEIGHT_INFO'=>$lang['tn_params_height_info'],
  'L_CREATE'=>$lang['tn_params_create'],
  'L_CREATE_INFO'=>$lang['tn_params_create_info'],
  'L_FORMAT'=>$lang['tn_params_format'],
  'L_FORMAT_INFO'=>$lang['tn_params_format_info'],
  'L_SUBMIT'=>$lang['submit'],
  'L_REMAINING'=>$lang['tn_alone_title'],
  'L_TN_STATS'=>$lang['tn_stats'],
  'L_TN_NB_STATS'=>$lang['tn_stats_nb'],
  'L_TN_TOTAL'=>$lang['tn_stats_total'],
  'L_TN_MAX'=>$lang['tn_stats_max'],
  'L_TN_MIN'=>$lang['tn_stats_min'],
  'L_TN_AVERAGE'=>$lang['tn_stats_mean'],
  
  'T_STYLE'=>$user['template']
  ));
// +-----------------------------------------------------------------------+
// |                   search pictures without thumbnails                  |
// +-----------------------------------------------------------------------+
$wo_thumbnails = array();
$thumbnalized = array();

// what is the directory to search in ?
$query = '
SELECT galleries_url
  FROM '.SITES_TABLE.'
  WHERE id = 1
;';
list($galleries_url) = mysql_fetch_array(pwg_query($query));
$basedir = preg_replace('#/*$#', '', $galleries_url);

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
}
// +-----------------------------------------------------------------------+
// |                         thumbnails creation                           |
// +-----------------------------------------------------------------------+
if (isset($_POST['submit']))
{
  $errors = array();
  $times = array();
  $infos = array();
  
  // checking criteria
  if (!ereg('^[0-9]{2,3}$', $_POST['width']) or $_POST['width'] < 10)
  {
    array_push($errors, $lang['tn_err_width'].' 10');
  }
  if (!ereg('^[0-9]{2,3}$', $_POST['height']) or $_POST['height'] < 10)
  {
    array_push($errors, $lang['tn_err_height'].' 10');
  }
  
  // picture miniaturization
  if (count($errors) == 0)
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
      
      $template->assign_block_vars(
        'results',
        array(
          'TN_NB'=>count($infos),
          'TN_TOTAL'=>number_format($sum, 2, '.', ' ').' ms',
          'TN_MAX'=>number_format($max, 2, '.', ' ').' ms',
          'TN_MIN'=>number_format($min, 2, '.', ' ').' ms',
          'TN_AVERAGE'=>number_format($average, 2, '.', ' ').' ms'
          ));
      
      foreach ($infos as $i => $info)
      {
        if ($info['time'] == $max)
        {
          $class = 'worst_gen_time';
        }
        else if ($info['time'] == $min)
        {
          $class = 'best_gen_time';
        }
        else
        {
          $class = '';
        }
        
        $template->assign_block_vars(
          'results.picture',
          array(
            'PATH'=>$info['path'],
            'TN_FILE_IMG'=>$info['tn_file'],
            'TN_FILESIZE_IMG'=>$info['tn_size'],
            'TN_WIDTH_IMG'=>$info['tn_width'],
            'TN_HEIGHT_IMG'=>$info['tn_height'],
            'GEN_TIME'=>number_format($info['time'], 2, '.', ' ').' ms',
            
            'T_CLASS'=>$class
            ));
      }
    }
  }
}
// +-----------------------------------------------------------------------+
// |                            errors display                             |
// +-----------------------------------------------------------------------+
if (count($errors) != 0)
{
  $template->assign_block_vars('errors',array());
  foreach ($errors as $error)
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$error));
  }
}
// +-----------------------------------------------------------------------+
// |             form & pictures without thumbnails display                |
// +-----------------------------------------------------------------------+
$remainings = array_diff($wo_thumbnails, $thumbnalized);

if (count($remainings) > 0)
{
  $form_url = PHPWG_ROOT_PATH.'admin.php?page=thumbnail';
  $gd = !empty($_POST['gd']) ? $_POST['gd'] : 2;
  $width = !empty($_POST['width']) ? $_POST['width'] : $conf['tn_width'];
  $height = !empty($_POST['height']) ? $_POST['height'] : $conf['tn_height'];
  $n = !empty($_POST['n']) ? $_POST['n'] : 5;
  
  $gdlabel = 'GD'.$gd.'_CHECKED';
  $nlabel = 'n_'.$n.'_CHECKED';
  
  $template->assign_block_vars(
    'params',
    array(
      'F_ACTION'=>add_session_id($form_url),
      $gdlabel=>'checked="checked"',
      $nlabel=>'checked="checked"',
      'WIDTH_TN'=>$width,
      'HEIGHT_TN'=>$height
      ));

  $template->assign_block_vars(
    'remainings',
    array('TOTAL_IMG'=>count($remainings)));

  $num = 1;
  foreach ($remainings as $path)
  {
    $class = ($num % 2) ? 'row1' : 'row2';
    list($width, $height) = getimagesize($path);
    $size = floor(filesize($path) / 1024).' KB';

    $template->assign_block_vars(
      'remainings.remaining',
      array(
        'NB_IMG'=>($num),
        'PATH'=>$path,
        'FILESIZE_IMG'=>$size,
        'WIDTH_IMG'=>$width,
        'HEIGHT_IMG'=>$height,
        
        'T_CLASS'=>$class
        ));

    $num++;
  }
}
else
{
  $template->assign_block_vars('warning', array());
}
// +-----------------------------------------------------------------------+
// |                           return to admin                             |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'thumbnail');
?>
