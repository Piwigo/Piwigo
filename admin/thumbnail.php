<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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
// get_subdirs returns an array containing all sub directory names,
// excepting : '.', '..' and 'thumbnail'.
function get_subdirs( $dir )
{
  $sub_dirs = array();
  if ( $opendir = opendir( $dir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ($file != 'thumbnail'
          and $file != 'pwg_representative'
          and $file != 'pwg_high'
          and $file != '.'
          and $file != '..'
          and is_dir($dir.'/'.$file))
      {
        array_push( $sub_dirs, $file );
      }
    }
  }
  return $sub_dirs;
}

// get_images_without_thumbnail returns an array with all the picture names
// that don't have associated thumbnail in the directory. Each picture name
// is associated with the width, heigh and filesize of the picture.
function get_images_without_thumbnail( $dir )
{
  $images = array();
  if ( $opendir = opendir( $dir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      $path = $dir.'/'.$file;
      if ( is_image( $path, true ) )
      {
        if ( !TN_exists( $dir, $file ) )
        {
          $image_infos = getimagesize( $path );
          $size = floor( filesize( $path ) / 1024 ). ' KB';
          array_push( $images, array( 'name' => $file,
                                      'width' => $image_infos[0],
                                      'height' => $image_infos[1],
                                      'size' => $size ) );
        }
      }
    }
  }
  return $images;
}

// phpwg_scandir scans a dir to find pictures without thumbnails. Once found,
// creation of the thumbnails (RatioResizeImg). Only the first $_POST['n']
// pictures without thumbnails are treated.
// scandir returns an array with the generation time of each thumbnail (for
// statistics purpose)
function phpwg_scandir( $dir, $width, $height )
{
  global $conf;
  $stats = array();
  if ( $opendir = opendir( $dir ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      $path = $dir.'/'.$file;
      if ( is_image( $path, true ) )
      {
        if ( count( $stats ) < $_POST['n'] and !TN_exists( $dir, $file ) )
        {
          $starttime = get_moment();
          $info = RatioResizeImg( $file, $width, $height, $dir.'/', 'jpg' );
          $endtime = get_moment();
          $info['time'] = ( $endtime - $starttime ) * 1000;
          array_push( $stats, $info );
        }
      }
    }
  }
  return $stats;
}

// RatioResizeImg creates a new picture (a thumbnail since it is supposed to
// be smaller than original picture !) in the sub directory named
// "thumbnail".
function RatioResizeImg( $filename, $newWidth, $newHeight, $path, $tn_ext )
{
  global $conf, $lang;
  // full path to picture
  $filepath = $path.$filename;
  // extension of the picture filename
  $extension = get_extension( $filepath );
  switch( $extension )
  {
  case 'jpg': $srcImage = @imagecreatefromjpeg( $filepath ); break; 
  case 'JPG': $srcImage = @imagecreatefromjpeg( $filepath ); break; 
  case 'png': $srcImage = @imagecreatefrompng(  $filepath ); break; 
  case 'PNG': $srcImage = @imagecreatefrompng(  $filepath ); break; 
  default : unset( $extension ); break;
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
			
			
    if( !is_dir( $path.'thumbnail' ) )
    {
      umask( 0000 );
      mkdir( $path.'thumbnail', 0777 );
    }
    $dest_file = $path.'thumbnail/'.$conf['prefix_thumbnail'];
    $dest_file.= get_filename_wo_extension( $filename );
    $dest_file.= '.'.$tn_ext;
			
    // creation and backup of final picture
    imagejpeg( $destImage, $dest_file );
    // freeing memory ressources
    imagedestroy( $srcImage );
    imagedestroy( $destImage );
			
    list( $width,$height ) = getimagesize( $filepath );
    $size = floor( filesize( $filepath ) / 1024 ).' KB';
    list( $tn_width,$tn_height ) = getimagesize( $dest_file );
    $tn_size = floor( filesize( $dest_file ) / 1024 ).' KB';
    $info = array( 'file'      => $filename,
                   'width'     => $width,
                   'height'    => $height,
                   'size'      => $size,
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

if ( isset( $_GET['dir'] ) &&  isset( $_POST['submit'] ))
{
  $pictures = get_images_without_thumbnail( $_GET['dir'] );
  // checking criteria
  if ( !ereg( "^[0-9]{2,3}$", $_POST['width'] ) or $_POST['width'] < 10 )
  {
    array_push( $errors, $lang['tn_err_width'].' 10' );
  }
  if ( !ereg( "^[0-9]{2,3}$", $_POST['height'] ) or $_POST['height'] < 10 )
  {
    array_push( $errors, $lang['tn_err_height'].' 10' );
  }
  
  // picture miniaturization
  if ( count( $errors ) == 0 )
  {
    $stats = phpwg_scandir( $_GET['dir'], $_POST['width'], $_POST['height'] );
  }
}
		
//----------------------------------------------------- template initialization
$template->set_filenames( array('thumbnail'=>'admin/thumbnail.tpl') );

$template->assign_vars(array(
  'L_THUMBNAIL_TITLE'=>$lang['tn_dirs_title'],
  'L_UNLINK'=>$lang['tn_dirs_alone'],
  'L_MISSING_THUMBNAILS'=>$lang['tn_dirs_alone'],
  'L_RESULTS'=>$lang['tn_results_title'],
  'L_TN_PICTURE'=>$lang['tn_picture'],
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

//----------------------------------------------------- miniaturization results
if ( sizeof( $errors ) != 0 )
{
  $template->assign_block_vars('errors',array());
  for ( $i = 0; $i < sizeof( $errors ); $i++ )
  {
    $template->assign_block_vars('errors.error',array('ERROR'=>$errors[$i]));
  }
}
else if (isset($_GET['dir']) and isset($_POST['submit']) and !empty($stats))
{
  $times = array();
  foreach ($stats as $stat)
  {
    array_push( $times, $stat['time'] );
  }
  $sum=array_sum( $times );
  $average = $sum/sizeof($times);
  sort( $times, SORT_NUMERIC );
  $max = array_pop($times);
  $min = array_shift( $times);
  
  $template->assign_block_vars(
    'results',
    array(
      'TN_NB'=>count( $stats ),
      'TN_TOTAL'=>number_format( $sum, 2, '.', ' ').' ms',
      'TN_MAX'=>number_format( $max, 2, '.', ' ').' ms',
      'TN_MIN'=>number_format( $min, 2, '.', ' ').' ms',
      'TN_AVERAGE'=>number_format( $average, 2, '.', ' ').' ms'
      ));
  if (!count($pictures))
  {
    $template->assign_block_vars('warning',array());
  }
  
  foreach ($stats as $i => $stat) 
  {
    $class = ($i % 2)? 'row1':'row2';
    $color='';
    if ($stat['time'] == $max)
    {
      $color = 'red';
    }
    else if ($stat['time'] == $min)
    {
      $color = '#33FF00';
    }
    
    $template->assign_block_vars(
      'results.picture',
      array(
        'NB_IMG'=>($i+1),
        'FILE_IMG'=>$stat['file'],
        'FILESIZE_IMG'=>$stat['size'],
        'WIDTH_IMG'=>$stat['width'],
        'HEIGHT_IMG'=>$stat['height'],
        'TN_FILE_IMG'=>$stat['tn_file'],
        'TN_FILESIZE_IMG'=>$stat['tn_size'],
        'TN_WIDTH_IMG'=>$stat['tn_width'],
        'TN_HEIGHT_IMG'=>$stat['tn_height'],
        'GEN_TIME'=>number_format( $stat['time'], 2, '.', ' ').' ms',
        
        'T_COLOR'=>$color,
        'T_CLASS'=>$class
        ));
  }
}
//-------------------------------------------------- miniaturization parameters
if (isset($_GET['dir']) and !sizeof($pictures))
{
  $form_url = PHPWG_ROOT_PATH.'admin.php?page=thumbnail&amp;dir='.$_GET['dir'];
  $gd = !empty( $_POST['gd'] )?$_POST['gd']:2;
  $width = !empty( $_POST['width'] )?$_POST['width']:128;
  $height = !empty( $_POST['height'] )?$_POST['height']:96;
  $gdlabel = 'GD'.$gd.'_CHECKED';
  
  $template->assign_block_vars(
    'params',
    array(
      'F_ACTION'=>add_session_id($form_url),
      $gdlabel=>'checked="checked"',
      'WIDTH_TN'=>$width,
      'HEIGHT_TN'=>$height
      ));
//---------------------------------------------------------- remaining pictures
  $pictures = get_images_without_thumbnail( $_GET['dir'] );
  $template->assign_block_vars(
    'remainings',
    array('TOTAL_IMG'=>count($pictures)));

  foreach ($pictures as $i => $picture)
  {
    $class = ($i % 2)? 'row1':'row2';
    $template->assign_block_vars(
      'remainings.remaining',
      array(
        'NB_IMG'=>($i+1),
        'FILE_TN'=>$picture['name'],
        'FILESIZE_IMG'=>$picture['size'],
        'WIDTH_IMG'=>$picture['width'],
        'HEIGHT_IMG'=>$picture['height'],
	  
        'T_CLASS'=>$class
        ));
  }
}
//-------------------------------------------------------------- directory list
else
{
  $wo_thumbnails = array();
  
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
        if (!isset($wo_thumbnails[$dirname]))
        {
          $wo_thumbnails[$dirname] = 1;
        }
        else
        {
          $wo_thumbnails[$dirname]++;
        }
      }
    }
  }

  if (count($wo_thumbnails) > 0)
  {
    $template->assign_block_vars('directory_list', array());
    foreach ($wo_thumbnails as $directory => $nb_missing)
    {
      $url = PHPWG_ROOT_PATH.'admin.php?page=thumbnail&amp;dir='.$directory;
      
      $template->assign_block_vars(
        'directory_list.directory',
        array(
          'DIRECTORY'=>$directory,
          'URL'=>add_session_id($url),
          'NB_MISSING'=>$nb_missing));
    }
  }
}
$template->assign_var_from_handle('ADMIN_CONTENT', 'thumbnail');
?>
