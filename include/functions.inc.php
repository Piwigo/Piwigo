<?php
/***************************************************************************
 *                             functions.inc.php                           *
 *                            -------------------                          *
 *   application          : PhpWebGallery 1.3                              *
 *   author               : Pierrick LE GALL <pierrick@z0rglub.com>        *
 *                                                                         *
 ***************************************************************************

 ***************************************************************************
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation;                                         *
 *                                                                         *
 ***************************************************************************/
include( 'functions_user.inc.php' );
include( 'functions_session.inc.php' );
include( 'functions_category.inc.php' );

//----------------------------------------------------------- generic functions

// get_boolean transforms a string to a boolean value. If the string is
// "false" (case insensitive), then the boolean value false is returned. In
// any other case, true is returned.
function get_boolean( $string )
{
  $boolean = true;
  if ( preg_match( '/^false$/i', $string ) )
  {
    $boolean = false;
  }
  return $boolean;
}

// array_remove removes a value from the given array if the value existed in
// this array.
function array_remove( $array, $value )
{
  $i = 0;
  $output = array();
  foreach ( $array as $v )
    {
      if ( $v != $value )
      {
        $output[$i++] = $v;
      }
    }
  return implode( ',', $output );
}

// The function get_moment returns a float value coresponding to the number
// of seconds since the unix epoch (1st January 1970) and the microseconds
// are precised : e.g. 1052343429.89276600
function get_moment()
{
  $t1 = explode( " ", microtime() );
  $t2 = explode( ".", $t1[0] );
  $t2 = $t1[1].".".$t2[1];
  return $t2;
}

// The function get_elapsed_time returns the number of seconds (with 3
// decimals precision) between the start time and the end time given.
function get_elapsed_time( $start, $end )
{
  return number_format( $end - $start, 3, '.', ' ').' s';
}

// - The replace_space function replaces space and '-' characters
//   by their HTML equivalent  &nbsb; and &minus;
// - The function does not replace characters in HTML tags
// - This function was created because IE5 does not respect the
//   CSS "white-space: nowrap;" property unless space and minus
//   characters are replaced like this function does.
function replace_space( $string )
{
  //return $string;             
  $return_string = "";
  $remaining = $string;
                
  $start = 0;
  $end = 0;
  $start = strpos ( $remaining, '<' );
  $end   = strpos ( $remaining, '>' );
  while ( is_numeric( $start ) and is_numeric( $end ) )
  {
    $treatment = substr ( $remaining, 0, $start );
    $treatment = str_replace( ' ', '&nbsp;', $treatment );
    $treatment = str_replace( '-', '&minus;', $treatment );
    $return_string.= $treatment.substr ( $remaining, $start,
                                         $end - $start + 1 );
    $remaining = substr ( $remaining, $end + 1, strlen( $remaining ) );
    $start = strpos ( $remaining, '<' );
    $end   = strpos ( $remaining, '>' );
  }
  $treatment = str_replace( ' ', '&nbsp;', $remaining );
  $treatment = str_replace( '-', '&minus;', $treatment );
  $return_string.= $treatment;
                
  return $return_string;
}

// get_dirs retourne un tableau contenant tous les sous-répertoires d'un
// répertoire
function get_dirs( $rep )
{
  $sub_rep = array();

  if ( $opendir = opendir ( $rep ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( $file != "." and $file != ".." and is_dir ( $rep.$file ) )
      {
        array_push( $sub_rep, $file );
      }
    }
  }
  return $sub_rep;
}

// The get_picture_size function return an array containing :
//      - $picture_size[0] : final width
//      - $picture_size[1] : final height
// The final dimensions are calculated thanks to the original dimensions and
// the maximum dimensions given in parameters.  get_picture_size respects
// the width/height ratio
function get_picture_size( $original_width, $original_height,
                           $max_width, $max_height )
{
  $width = $original_width;
  $height = $original_height;
  $is_original_size = true;
                
  if ( $max_width != "" )
  {
    if ( $original_width > $max_width )
    {
      $width = $max_width;
      $height = floor( ( $width * $original_height ) / $original_width );
    }
  }
  if ( $max_height != "" )
  {
    if ( $original_height > $max_height )
    {
      $height = $max_height;
      $width = floor( ( $height * $original_width ) / $original_height );
      $is_original_size = false;
    }
  }
  if ( is_numeric( $max_width ) and is_numeric( $max_height )
       and $max_width != 0 and $max_height != 0 )
  {
    $ratioWidth = $original_width / $max_width;
    $ratioHeight = $original_height / $max_height;
    if ( ( $ratioWidth > 1 ) or ( $ratioHeight > 1 ) )
    {
      if ( $ratioWidth < $ratioHeight )
      { 
        $width = floor( $original_width / $ratioHeight );
        $height = $max_height;
      }
      else
      { 
        $width = $max_width; 
        $height = floor( $original_height / $ratioWidth );
      }
      $is_original_size = false;
    }
  }
  $picture_size = array();
  $picture_size[0] = $width;
  $picture_size[1] = $height;
  return $picture_size;
}

//-------------------------------------------- PhpWebGallery specific functions

// get_languages retourne un tableau contenant tous les languages
// disponibles pour PhpWebGallery
function get_languages( $rep_language )
{
  $languages = array();
  $i = 0;
  if ( $opendir = opendir ( $rep_language ) )
  {
    while ( $file = readdir ( $opendir ) )
    {
      if ( is_file ( $rep_language.$file )
           and $file != "index.php"
           and strrchr ( $file, "." ) == ".php" )
      {
        $languages[$i++] =
          substr ( $file, 0, strlen ( $file )
                   - strlen ( strrchr ( $file, "." ) ) );
      }
    }
  }
  return $languages;
}

// get_themes retourne un tableau contenant tous les "template - couleur"
function get_themes( $theme_dir )
{
  $themes = array();
  $main_themes = get_dirs( $theme_dir );
  for ( $i = 0; $i < sizeof( $main_themes ); $i++ )
  {
    $colors = get_dirs( $theme_dir.$main_themes[$i].'/' );
    for ( $j = 0; $j < sizeof( $colors ); $j++ )
    {
      array_push( $themes, $main_themes[$i].' - '.$colors[$j] );
    }
  }
  return $themes;
}

// - The replace_search function replaces a $search string by the search in
//   another color
// - The function does not replace characters in HTML tags
function replace_search( $string, $search )
{
  //return $string;
  $style_search = "background-color:white;color:red;";
  $return_string = "";
  $remaining = $string;

  $start = 0;
  $end = 0;
  $start = strpos ( $remaining, '<' );
  $end   = strpos ( $remaining, '>' );
  while ( is_numeric( $start ) and is_numeric( $end ) )
  {
    $treatment = substr ( $remaining, 0, $start );
    $treatment = str_replace( $search, '<span style="'.$style_search.'">'.
                              $search.'</span>', $treatment );
    $return_string.= $treatment.substr ( $remaining, $start,
                                         $end - $start + 1 );
    $remaining = substr ( $remaining, $end + 1, strlen( $remaining ) );
    $start = strpos ( $remaining, '<' );
    $end   = strpos ( $remaining, '>' );
  }
  $treatment = str_replace( $search, '<span style="'.$style_search.'">'.
                            $search.'</span>', $remaining );
  $return_string.= $treatment;
                
  return $return_string;
}

function database_connection()
{
  global $cfgHote,$cfgUser,$cfgPassword,$cfgBase;
  @mysql_connect( $cfgHote, $cfgUser, $cfgPassword )
    or die ( "Could not connect to server" );
  @mysql_select_db( $cfgBase )
    or die ( "Could not connect to database" );
}

function pwg_log( $file, $category, $picture = '' )
{
  global $conf, $user, $prefixeTable;

  if ( $conf['log'] )
  {
    $query = 'insert into '.$prefixeTable.'history';
    $query.= ' (date,login,IP,file,category,picture) values';
    $query.= " (".time().", '".$user['pseudo']."'";
    $query.= ",'".$_SERVER['REMOTE_ADDR']."'";
    $query.= ",'".$file."','".$category."','".$picture."');";
    mysql_query( $query );
  }
}

function templatize_array( $array, $global_array_name )
{
  global $vtp, $handle, $lang, $page, $user, $conf;

  for( $i = 0; $i < sizeof( $array ); $i++ )
  {
    $vtp->setGlobalVar( $handle, $array[$i],
                        ${$global_array_name}[$array[$i]] );
  }
}
?>