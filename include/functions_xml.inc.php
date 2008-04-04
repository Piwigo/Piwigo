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
// | branch        : BSF (Best So Far)
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
  $regex = '/^<\w+[^>]*\b'.$attribute.'\s*=\s*"('.VAL_REG.')"/i';
  if ( preg_match( $regex, $element, $out ) ) 
  {
    return html_entity_decode($out[1], ENT_QUOTES);
  }
  else return '';
}

// The function encode Attribute returns the xml attribute $attribute="$value" 
function encodeAttribute( $attribute, $value )
{
  return $attribute.'="'.htmlspecialchars($value, ENT_QUOTES).'" ';
}

// The function getChild returns the first child
// exemple : getChild( "<table><tr>XXX</tr><tr>YYY</tr></table>", "tr" )
//           returns "<tr>XXX</tr>"
function getChild( $document, $node )
{
  $regex = '/<'.$node.'(\s+'.ATT_REG.'="'.VAL_REG.'")*';
  $regex.= '(\s*\/>|>.*<\/'.$node.'>)/U';

  if
    (
      preg_match( $regex, $document, $out )
      or
      preg_last_error() == PREG_NO_ERROR
    )
  {
    return $out[0];
  }
  else
  {
    die('getChild: error ['.preg_last_error().'] with preg_match function');
  }
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

  if
    (
      preg_match_all( $regex, $document, $out )
      or
      preg_last_error() == PREG_NO_ERROR
    )
  {
    return $out[0];
  }
  else
  {
    die('getChild: error ['.preg_last_error().'] with preg_match_all function');
  }
}

// get_CodeXML places the content of a text file in a PHP variable and
// return it. If the file can't be opened, returns false.
function getXmlCode( $filename )
{
  if (function_exists('ini_set'))
  {
    // limit must be growed with php5 and "big" listing file
    ini_set("pcre.backtrack_limit", pow(2, 32));
  }

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
