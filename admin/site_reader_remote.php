<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
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


// provides data for site synchronization from a remote listing.xml
class RemoteSiteReader
{

var $site_url;
var $listing_url;
var $site_dirs;
var $site_files;
var $insert_attributes;
var $metadata_attributes;

function RemoteSiteReader($url, $listing_url)
{
  $this->site_url = $url;
  $this->insert_attributes = array(
    'tn_ext',
    );
  $this->update_attributes = array(
    'tn_ext', 'representative_ext', 'has_high',
    );
  $this->metadata_attributes = array(
    'filesize', 'width', 'height', 'high_filesize'
    );

  if (!isset($listing_url))
  {
    $this->listing_url = $this->site_url.'/listing.xml';
  }
  else
  {
    $this->listing_url = $listing_url;
  }
}

/**
 * Is this remote site ok ?
 *
 * @return true on success, false otherwise
 */
function open()
{
  global $errors;

  if (@fopen($this->listing_url, 'r'))
  {
    $this->site_dirs = array();
    $this->site_files = array();
    $xml_content = getXmlCode($this->listing_url);
    $info_xml_element = getChild($xml_content, 'informations');
    if (getAttribute($info_xml_element , 'phpwg_version') != PHPWG_VERSION)
    {
      array_push(
        $errors,
        array(
          'path' => $this->listing_url,
          'type' => 'PWG-ERROR-VERSION'
          )
        );

      return false;
    }

    $additional_metadata = getAttribute($info_xml_element, 'metadata');

    if ($additional_metadata != '')
    {
      $this->metadata_attributes = array_merge(
        $this->metadata_attributes,
        explode(',', $additional_metadata)
        );
    }
    
    $this->build_structure($xml_content, '', 0);

    return true;
  }
  else
  {
    array_push(
      $errors,
      array(
        'path' => $this->listing_url,
        'type' => 'PWG-ERROR-NOLISTING'
        )
      );

    return false;
  }
}

// retrieve xml sub-directories fulldirs
function get_full_directories($basedir)
{
  $dirs = array();
  foreach ( array_keys($this->site_dirs) as $dir)
  {
    $full_dir = $this->site_url . $dir;
    if ($full_dir != $basedir
        and strpos($full_dir, $basedir) === 0
      )
    {
      array_push($dirs, $full_dir);
    }
  }
  return $dirs;
}

/**
 * Returns a hash with all elements (images and files) inside the full $path
 * according to listing.xml
 * @param string $path recurse in this directory only
 * @return array like "pic.jpg"=>array('tn_ext'=>'jpg' ... )
 */
function get_elements($path)
{
  $elements = array();
  foreach ( $this->site_dirs as $dir=>$files)
  {
    $full_dir = $this->site_url . $dir;
    if (strpos($full_dir, $path) === 0)
    {
      foreach ($files as $file)
      {
        $data = $this->get_element_attributes(
          $file,
          $this->insert_attributes
          );
        $elements[$file] = $data;
      }
    }
  }

  return $elements;
}

// returns the name of the attributes that are supported for
// files update/synchronization
function get_update_attributes()
{
  return $this->update_attributes;
}

function get_element_update_attributes($file)
{
  return $this->get_element_attributes(
    $file,
    $this->update_attributes
    );
}

// returns the name of the attributes that are supported for
// metadata update/synchronization according to listing.xml
function get_metadata_attributes()
{
  return $this->metadata_attributes;
}

// returns a hash of attributes (metadata+width,...) for file
function get_element_metadata($file, $has_high = false)
{
  return $this->get_element_attributes(
    $file,
    $this->metadata_attributes
    );
}

//-------------------------------------------------- private functions --------
/**
 * Returns a hash of image/file attributes
 * @param string $file fully qualified file name
 * @param array $attributes specifies which attributes to retrieve
 *  returned
*/
function get_element_attributes($file, $attributes)
{
  $xml_element = $this->site_files[$file];
  if (!isset($xml_element))
  {
    return null;
  }
  $data = array();
  foreach($attributes as $att)
  {
    if (getAttribute($xml_element, $att) != '')
    {
      $val = getAttribute($xml_element, $att);
      $data[$att] = addslashes($val);
    }
  }
  return $data;
}

// recursively parse the xml_content for later usage
function build_structure($xml_content, $basedir, $level)
{
  $temp_dirs = getChildren($xml_content, 'dir'.$level);
  foreach ($temp_dirs as $temp_dir)
  {
    $dir_name = $basedir;
    if ($dir_name != '' )
    {
      $dir_name .= '/';
    }
    $dir_name .= getAttribute($temp_dir, 'name');
    $this->site_dirs[ $dir_name ] = array();
    $this->build_structure($temp_dir, $dir_name, $level+1);
  }

  if ($basedir != '')
  {
    $xml_elements = getChildren(
      getChild($xml_content, 'root'),
      'element'
      );
    foreach ($xml_elements as $xml_element)
    {
      $path = getAttribute($xml_element, 'path');
      $this->site_files[$path] = $xml_element;
      array_push($this->site_dirs[$basedir], $path);
    }
  }
}

}

?>