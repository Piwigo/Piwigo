<?php
/***************************************************************************
 *                          create_listing_file.php                        *
 *                            -------------------                          *
 *   application   : PhpWebGallery 1.3 <http://phpwebgallery.net>          *
 *   author        : Pierrick LE GALL <pierrick@z0rglub.com>               *
 *                                                                         *
 *   $Id$
 *                                                                         *
 ***************************************************************************/

$conf['prefix_thumbnail'] = 'TN-';
$conf['picture_ext'] = array ( 'jpg', 'gif', 'png', 'JPG', 'GIF', 'PNG' );

$listing = '';

$end = strrpos( $_SERVER['PHP_SELF'], '/' ) + 1;
$local_folder = substr( $_SERVER['PHP_SELF'], 0, $end );
$url = 'http://'.$_SERVER['HTTP_HOST'].$local_folder;

$listing.= '<url>'.$url.'</url>';

/**
 * returns an array with all picture files according to $conf['picture_ext']
 *
 * @param string $dir
 * @return array
 */
function get_picture_files( $dir )
{
  global $conf;

  $pictures = array();
  if ( $opendir = opendir( $dir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( in_array( get_extension( $file ), $conf['picture_ext'] ) )
      {
        array_push( $pictures, $file );
      }
    }
  }
  return $pictures;
}

/**
 * returns an array with all thumbnails according to $conf['picture_ext']
 * and $conf['prefix_thumbnail']
 *
 * @param string $dir
 * @return array
 */
function get_thumb_files( $dir )
{
  global $conf;

  $prefix_length = strlen( $conf['prefix_thumbnail'] );
  
  $thumbnails = array();
  if ( $opendir = @opendir( $dir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( in_array( get_extension( $file ), $conf['picture_ext'] )
           and substr($file,0,$prefix_length) == $conf['prefix_thumbnail'] )
      {
        array_push( $thumbnails, $file );
      }
    }
  }
  return $thumbnails;
}

// get_dirs retourne un tableau contenant tous les sous-répertoires d'un
// répertoire
function get_dirs( $basedir, $indent, $level )
{
  $fs_dirs = array();
  $dirs = "";

  if ( $opendir = opendir( $basedir ) )
  {
    while ( $file = readdir( $opendir ) )
    {
      if ( $file != '.'
           and $file != '..'
           and is_dir ( $basedir.'/'.$file )
           and $file != 'thumbnail' )
      {
        array_push( $fs_dirs, $file );
        if ( !preg_match( '/^[a-zA-Z0-9-_.]+$/', $file ) )
        {
          echo '<span style="color:red;">"'.$file.'" : ';
          echo 'The name of the directory should be composed of ';
          echo 'letters, figures, "-", "_" or "." ONLY';
          echo '</span><br />';
        }
      }
    }
  }
  // write of the dirs
  foreach ( $fs_dirs as $fs_dir ) {
    $dirs.= "\n".$indent.'<dir'.$level.' name="'.$fs_dir.'">';
    $dirs.= get_pictures( $basedir.'/'.$fs_dir, $indent.'  ' );
    $dirs.= get_dirs( $basedir.'/'.$fs_dir, $indent.'  ', $level + 1 );
    $dirs.= "\n".$indent.'</dir'.$level.'>';
  }
  return $dirs;		
}

// get_extension returns the part of the string after the last "."
function get_extension( $filename )
{
  return substr( strrchr( $filename, '.' ), 1, strlen ( $filename ) );
}

// get_filename_wo_extension returns the part of the string before the last
// ".".
// get_filename_wo_extension( 'test.tar.gz' ) -> 'test.tar'
function get_filename_wo_extension( $filename )
{
  return substr( $filename, 0, strrpos( $filename, '.' ) );
}

function get_pictures( $dir, $indent )
{
  global $conf;
  
  // fs means filesystem : $fs_pictures contains pictures in the filesystem
  // found in $dir, $fs_thumbnails contains thumbnails...
  $fs_pictures   = get_picture_files( $dir );
  $fs_thumbnails = get_thumb_files( $dir.'/thumbnail' );

  $root = "\n".$indent.'<root>';

  foreach ( $fs_pictures as $fs_picture ) {
    $file_wo_ext = get_filename_wo_extension( $fs_picture );
    $tn_ext = '';
    foreach ( $conf['picture_ext'] as $ext ) {
      $test = $conf['prefix_thumbnail'].$file_wo_ext.'.'.$ext;
      if ( !in_array( $test, $fs_thumbnails ) ) continue;
      else { $tn_ext = $ext; break; }
    }
    // if we found a thumnbnail corresponding to our picture...
    if ( $tn_ext != '' )
    {
      list( $width,$height ) = @getimagesize( $dir.'/'.$fs_picture );

      $root.= "\n".$indent.'  ';
      $root.= '<picture';
      $root.= ' file="'.    $fs_picture.'"';
      $root.= ' tn_ext="'.  $tn_ext.'"';
      $root.= ' filesize="'.floor(filesize($dir.'/'.$fs_picture)/1024).'"';
      $root.= ' width="'.   $width.'"';
      $root.= ' height="'.  $height.'"';
      $root.= ' />';
      
      if ( !preg_match( '/^[a-zA-Z0-9-_.]+$/', $fs_picture ) )
      {
        echo '<span style="color:red;">"'.$fs_picture.'" : ';
        echo 'The name of the picture should be composed of ';
        echo 'letters, figures, "-", "_" or "." ONLY';
        echo '</span><br />';
      }
    }
    else
    {
      echo 'The thumbnail is missing for '.$dir.'/'.$fs_picture;
      echo '-> '.$dir.'/thumbnail/';
      echo $conf['prefix_thumbnail'].$file_wo_ext.'.xxx';
      echo ' ("xxx" can be : ';
      echo implode( ', ', $conf['picture_ext'] );
      echo ')<br />';
    }
  }

  $root.= "\n".$indent.'</root>';

  return $root;
}

$listing.= get_dirs( '.', '', 0 );

if ( $fp = @fopen("./listing.xml","w") )
{
  fwrite( $fp, $listing );
  fclose( $fp );
  echo "listing.xml created";
}
else
{
  echo "I can't write the file listing.xml";
}
?>