<?php
/***************************************************************************
 *                              thumbnail.php                              *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

/***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include_once( './admin/include/isadmin.inc.php' );
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
      if ( $file != 'thumbnail' and $file != '.'
           and $file != '..' and is_dir( $dir.'/'.$file ) )
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

// scandir scans a dir to find pictures without thumbnails. Once found,
// creation of the thumbnails (RatioResizeImg). Only the first $_POST['n']
// pictures without thumbnails are treated.
// scandir returns an array with the generation time of each thumbnail (for
// statistics purpose)
function scandir( $dir, $width, $height )
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

// array_max returns the highest value of the given array
function array_max( $array )
{
  sort( $array, SORT_NUMERIC );
  return array_pop( $array );
}

// array_min returns the lowest value of the given array
function array_min( $array )
{
  sort( $array, SORT_NUMERIC );
  return array_shift( $array );
}

// array_avg returns the average value of the array
function array_avg( $array )
{
  return array_sum( $array ) / sizeof( $array );
}
	
// get_displayed_dirs builds the tree of dirs under "galleries". If a
// directory contains pictures without thumbnails, the become linked to the
// page of thumbnails creation.
function get_displayed_dirs( $dir, $indent )
{
  global $conf,$lang,$vtp,$sub;
		
  $sub_dirs = get_subdirs( $dir );
  // write of the dirs
  foreach ( $sub_dirs as $sub_dir ) {
    $pictures = get_images_without_thumbnail( $dir.'/'.$sub_dir );
    $vtp->addSession( $sub, 'dir' );
    $vtp->setVar( $sub, 'dir.indent', $indent );
    if ( count( $pictures ) > 0 )
    {
      $vtp->addSession( $sub, 'linked' );
      $url = './admin.php?page=thumbnail&amp;dir='.$dir."/".$sub_dir;
      $vtp->setVar( $sub, 'linked.url', add_session_id( $url ) );
      $vtp->setVar( $sub, 'linked.name', $sub_dir );
      $vtp->setVar( $sub, 'linked.nb_pic', count( $pictures ) );
      $vtp->closeSession( $sub, 'linked' );
    }
    else
    {
      $vtp->addSession( $sub, 'unlinked' );
      $vtp->setVar( $sub, 'unlinked.name', $sub_dir );
      $vtp->closeSession( $sub, 'unlinked' );
    }
    $vtp->closeSession( $sub, 'dir' );
    // recursive call
    $dirs.= get_displayed_dirs( $dir.'/'.$sub_dir,
                                $indent+30 );
    
  }
}
//----------------------------------------------------- template initialization
$sub = $vtp->Open( './template/'.$user['template'].'/admin/thumbnail.vtp' );
$tpl = array(
  'tn_dirs_title','tn_dirs_alone','tn_params_title','tn_params_GD',
  'tn_params_GD_info','tn_width','tn_params_width_info','tn_height',
  'tn_params_height_info','tn_params_create','tn_params_create_info',
  'tn_params_format','tn_params_format_info','submit','tn_alone_title',
  'filesize','tn_picture','tn_results_title','thumbnail',
  'tn_results_gen_time','tn_stats','tn_stats_nb','tn_stats_total',
  'tn_stats_max','tn_stats_min','tn_stats_mean' );
templatize_array( $tpl, 'lang', $sub );
$vtp->setGlobalVar( $sub, 'user_template', $user['template'] );
//----------------------------------------------------- miniaturization results
if ( isset( $_GET['dir'] ) )
{
  $pictures = get_images_without_thumbnail( $_GET['dir'] );
  if ( count( $pictures ) == 0 )
  {
    $vtp->addSession( $sub, 'warning' );
    $vtp->closeSession( $sub, 'warning' );
  }
  elseif ( isset( $_POST['submit'] ) )
  {
    // checking criteria
    $errors = array();
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
      $vtp->addSession( $sub, 'results' );
      $stats = scandir( $_GET['dir'], $_POST['width'], $_POST['height'] );
      $times = array();
      foreach ( $stats as $stat ) {
        array_push( $times, $stat['time'] );
      }
      $max = array_max( $times );
      $min = array_min( $times );
      foreach ( $stats as $i => $stat ) {
        $vtp->addSession( $sub, 'picture' );
        if ( $i % 2 == 1 )
        {
          $vtp->setVar( $sub, 'picture.class', 'row2' );
        }
        $vtp->setVar( $sub, 'picture.num',            ($i+1) );
        $vtp->setVar( $sub, 'picture.file',           $stat['file'] );
        $vtp->setVar( $sub, 'picture.filesize',       $stat['size'] );
        $vtp->setVar( $sub, 'picture.width',          $stat['width'] );
        $vtp->setVar( $sub, 'picture.height',         $stat['height'] );
        $vtp->setVar( $sub, 'picture.thumb_file',     $stat['tn_file'] );
        $vtp->setVar( $sub, 'picture.thumb_filesize', $stat['tn_size'] );
        $vtp->setVar( $sub, 'picture.thumb_width',    $stat['tn_width'] );
        $vtp->setVar( $sub, 'picture.thumb_height',   $stat['tn_height'] );
        $vtp->setVar( $sub, 'picture.time',
                      number_format( $stat['time'], 2, '.', ' ').' ms' );
        if ( $stat['time'] == $max )
        {
          $vtp->setVar( $sub, 'picture.color', 'red' );
        }
        else if ( $stat['time'] == $min )
        {
          $vtp->setVar( $sub, 'picture.color', 'green' );
        }
        $vtp->closeSession( $sub, 'picture' );
      }
      // general statistics
      $vtp->setVar( $sub, 'results.stats_nb', count( $stats ) );
      $vtp->setVar( $sub, 'results.stats_total',
                    number_format( array_sum( $times ), 2, '.', ' ').' ms' );
      $vtp->setVar( $sub, 'results.stats_max',
                    number_format( $max, 2, '.', ' ').' ms' );
      $vtp->setVar( $sub, 'results.stats_min',
                    number_format( $min, 2, '.', ' ').' ms' );
      $vtp->setVar( $sub, 'results.stats_mean',
                    number_format( array_avg( $times ), 2, '.', ' ').' ms' );
      $vtp->closeSession( $sub, 'results' );
    }
    else
    {
      $vtp->addSession( $sub, 'errors' );
      foreach ( $errors as $error ) {
        $vtp->addSession( $sub, 'li' );
        $vtp->setVar( $sub, 'li.li', $error );
        $vtp->closeSession( $sub, 'li' );
      }
      $vtp->closeSession( $sub, 'errors' );
    }
  }
//-------------------------------------------------- miniaturization parameters
  if ( sizeof( $pictures ) != 0 )
  {
    $vtp->addSession( $sub, 'params' );
    $url = './admin.php?page=thumbnail&amp;dir='.$_GET['dir'];
    $vtp->setVar( $sub, 'params.action', add_session_id( $url ) );
    // GD version selected...
    if ( $_POST['gd'] == 1 )
    {
      $vtp->setVar( $sub, 'params.gd1_checked', ' checked="checked"' );
    }
    else
    {
      $vtp->setVar( $sub, 'params.gd2_checked', ' checked="checked"' );
    }
    // width values
    if ( isset( $_POST['width'] ) )
    {
      $vtp->setVar( $sub, 'params.width_value', $_POST['width'] );
    }
    else
    {
      $vtp->setVar( $sub, 'params.width_value', '128' );
    }
    // height value
    if ( isset( $_POST['height'] ) )
    {
      $vtp->setVar( $sub, 'params.height_value', $_POST['height'] );
    }
    else
    {
      $vtp->setVar( $sub, 'params.height_value', '96' );
    }
    // options for the number of picture to miniaturize : "n"
    $options = array( 5,10,20,40 );
    foreach ( $options as $option ) {
      $vtp->addSession( $sub, 'n_option' );
      $vtp->setVar( $sub, 'n_option.option', $option );
      if ( $option == $_POST['n'] )
      {
        $vtp->setVar( $sub, 'n_option.selected', ' selected="selected"' );
      }
      $vtp->closeSession( $sub, 'n_option' );
    }
    $vtp->closeSession( $sub, 'params' );
//---------------------------------------------------------- remaining pictures
    $vtp->addSession( $sub, 'remainings' );
    $pictures = get_images_without_thumbnail( $_GET['dir'] );
    $vtp->setVar( $sub, 'remainings.total', count( $pictures ) );
    foreach ( $pictures as $i => $picture ) {
      $vtp->addSession( $sub, 'remaining' );
      if ( $i % 2 == 1 )
      {
        $vtp->setVar( $sub, 'remaining.class', 'row2' );
      }
      $vtp->setVar( $sub, 'remaining.num',      ($i+1) );
      $vtp->setVar( $sub, 'remaining.file',     $picture['name'] );
      $vtp->setVar( $sub, 'remaining.filesize', $picture['size'] );
      $vtp->setVar( $sub, 'remaining.width',    $picture['width'] );
      $vtp->setVar( $sub, 'remaining.height',   $picture['height'] );
      $vtp->closeSession( $sub, 'remaining' );
    }
    $vtp->closeSession( $sub, 'remainings' );
  }
}
//-------------------------------------------------------------- directory list
else
{
  $vtp->addSession( $sub, 'directory_list' );
  get_displayed_dirs( './galleries', 60 );
  $vtp->closeSession( $sub, 'directory_list' );
}
//----------------------------------------------------------- sending html code
$vtp->Parse( $handle , 'sub', $sub );
?>