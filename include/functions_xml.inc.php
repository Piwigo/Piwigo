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
define( 'ATT_REG', '\w+' );
define( 'VAL_REG', '[^"]*' );

//------------------------------------------------------------------- functions
// getContent returns the content of a tag
//
// example : getContent( "<name>Joe</name>" ) returns "Joe"
//
// It also works with strings containing themself sub-tags :
// <perso><name>Jean</name><firstname>Billie</fisrtname></perso> ->
// <name>Jean</name><firstname>Billie</firstname>
function getContent( $element )
{
  // deleting start of the tag
  $content = preg_replace( '/^<[^>]+>/', '', $element );
  // deleting end of the tag
  $content = preg_replace( '/<\/[^>]+>$/', '', $content );
  // replacing multiple instance of space character
  $content = preg_replace( '/\s+/', ' ', $content );

  return $content;
}

// The function get Attribute returns the value corresponding to the
// attribute $attribute for the tag $element.
function getAttribute( $element, $attribute )
{
//  echo htmlentities($element).'<br /><br />';
  $regex = '/^<\w+[^>]*'.$attribute.'\s*=\s*"('.VAL_REG.')"/i';
  if ( preg_match( $regex, $element, $out ) ) return $out[1];
  else return '';
}

function deprecated_getAttribute( $element, $attribute )
{
  // Retrieving string with tag name and all attributes
  $regex = '/^<\w+( '.ATT_REG.'="'.VAL_REG.'")*/';
  preg_match( $regex, $element, $out );

  // Splitting string for retrieving separately attributes
  // and corresponding values
  $regex = '/('.ATT_REG.')="('.VAL_REG.')"/';
  preg_match_all( $regex, $out[0], $out );

  // Searching and returning the value of the requested attribute
  for ( $i = 0; $i < sizeof( $out[0] ); $i++ )
  {
    if ( $out[1][$i] == $attribute )
    {
      return $out[2][$i];
    }
  }
  return '';
}
	
// The function getChild returns the first child
// exemple : getChild( "<table><tr>XXX</tr><tr>YYY</tr></table>", "tr" )
//           returns "<tr>XXX</tr>"
function getChild( $document, $node )
{
  $regex = '/<'.$node.'(\s+'.ATT_REG.'="'.VAL_REG.'")*';
  $regex.= '(\s*\/>|>.*<\/'.$node.'>)/U';

  preg_match( $regex, $document, $out );
  return $out[0];
}

// getChildren returns a list of the children identified by the $node
// example : 
//     getChild( "<table><tr>XXX</tr><tr>YYY</tr></table>", "tr" )
//     returns an array with :
//          $array[0] equals "<tr>XXX</tr>"
//          $array[1] equals "<tr>YYY</tr>"
function getChildren( $document, $node )
{
  $regex = '/<'.$node.'(\s+'.ATT_REG.'="'.VAL_REG.'")*';
  $regex.= '(\s*\/>|>.*<\/'.$node.'>)/U';

  preg_match_all( $regex, $document, $out );
  return $out[0];
}
	
// get_CodeXML places the content of a text file in a PHP variable and
// return it. If the file can't be opened, returns false.
function getXmlCode( $filename )
{
  $file = fopen( $filename, 'r' );
  if ( !$file )
  {
    return false;
  }

  $xml_content = '';
  while ( !feof( $file ) )
  {
    $xml_content .= fgets( $file, 1024 );
  }
  fclose( $file );
  $xml_content = str_replace( "\n", '', $xml_content );
  $xml_content = str_replace( "\t", '', $xml_content );

  return $xml_content;
}
?>
