<?php
$prefixe_thumbnail = 'TN-';
	
$conf['picture_ext'] = array ( 'jpg', 'gif', 'png', 'JPG', 'GIF', 'PNG' );

$listing = '';

$end = strrpos( $_SERVER['PHP_SELF'], '/' ) + 1;
$local_folder = substr( $_SERVER['PHP_SELF'], 0, $end );
$url = 'http://'.$_SERVER['HTTP_HOST'].$local_folder;

$listing.= "<url>$url</url>";
	
// get_dirs retourne un tableau contenant tous les sous-répertoires d'un
// répertoire
function get_dirs( $rep, $indent, $level )
{
  $sub_rep = array();
  $i = 0;
  $dirs = "";
  if ( $opendir = opendir ( $rep ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( $file != "."
           and $file != ".."
           and is_dir ( $rep."/".$file )
           and $file != "thumbnail" )
      {
        $sub_rep[$i++] = $file;
      }
    }
  }
  // write of the dirs
  for ( $i = 0; $i < sizeof( $sub_rep ); $i++ )
  {
    $dirs.= "\n".$indent.'<dir'.$level.' name="'.$sub_rep[$i].'">';
    $dirs.= get_pictures( $rep.'/'.$sub_rep[$i], $indent.'  ' );
    $dirs.= get_dirs( $rep.'/'.$sub_rep[$i], $indent.'  ', $level + 1 );
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

function is_image( $filename )
{
  global $conf;

  if ( !is_dir( $filename )
       and in_array( get_extension( $filename ), $conf['picture_ext'] ) )
  {
    return true;
  }
  return false;
}

function TN_exists( $dir, $file )
{
  global $conf, $prefixe_thumbnail;

  $titre = get_filename_wo_extension( $file );

  for ( $i = 0; $i < sizeof ( $conf['picture_ext'] ); $i++ )
  {
    $base_tn_name = $dir.'/thumbnail/'.$prefixe_thumbnail.$titre.'.';
    $ext = $conf['picture_ext'][$i];
    if ( is_file( $base_tn_name.$ext ) )
    {
      return $ext;
    }
  }
  echo 'The thumbnail is missing for '.$dir.'/'.$file;
  echo '-> '.$dir.'/thumbnail/'.$prefixe_thumbnail.$titre.'.xxx';
  echo ' ("xxx" can be : ';
  for ( $i = 0; $i < sizeof ( $conf['picture_ext'] ); $i++ )
  {
    if ( $i > 0 )
    {
      echo ', ';
    }
    echo '"'.$conf['picture_ext'][$i].'"';
  }
  echo ')<br />';
  return false;
}

function get_pictures( $rep, $indent )
{
  $pictures = array();		

  $tn_ext = '';
  $root = '';
  if ( $opendir = opendir ( $rep ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( is_image( $file ) and $tn_ext = TN_exists( $rep, $file ) )
      {
        $picture = array();

        $picture['file']     = $file;
        $picture['tn_ext']   = $tn_ext;
        $picture['date']     = date('Y-m-d',filemtime( $rep.'/'.$file ) );
        $picture['filesize'] = floor( filesize( $rep."/".$file ) / 1024 );
        $image_size = @getimagesize( $rep."/".$file );
        $picture['width']    = $image_size[0];
        $picture['height']   = $image_size[1];

        array_push( $pictures, $picture );
      }
    }
  }
  // write of the node <root> with all the pictures at the root of the
  // directory
  $root.= "\n".$indent."<root>";
  if ( sizeof( $pictures ) > 0 )
  {
    for( $i = 0; $i < sizeof( $pictures ); $i++ )
    {
      $root.= "\n".$indent.'  ';
      $root.= '<picture';
      $root.= ' file="'.     $pictures[$i]['file'].     '"';
      $root.= ' tn_ext="'.   $pictures[$i]['tn_ext'].   '"';
      $root.= ' date="'.     $pictures[$i]['date'].     '"';
      $root.= ' filesize="'. $pictures[$i]['filesize']. '"';
      $root.= ' width="'.    $pictures[$i]['width'].    '"';
      $root.= ' height="'.   $pictures[$i]['height'].   '"';
      $root.= ' />';
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
}
else
{
  echo "I can't write the file listing.xml";
}

echo "listing.xml created";
?>