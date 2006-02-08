<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2006 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date: 2005-12-03 17:03:58 -0500 (Sat, 03 Dec 2005) $
// | last modifier : $Author: plg $
// | revision      : $Revision: 967 $
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

// provides data for site synchronization from the local file system
class LocalSiteReader
{

var $site_url;

function LocalSiteReader($url)
{
  $this->site_url = $url;
}

/**
 * Is this local site ok ?
 *
 * @return true on success, false otherwise
 */
function open()
{
  global $errors;
  if (!is_dir($this->site_url))
  {
    array_push($errors, array('path' => $this->site_url, 'type' => 'PWG-ERROR-NODIR'));
    return false;
  }
  return true;
}

// retrieve file system sub-directories fulldirs
function get_full_directories($basedir)
{
  $fs_fulldirs = get_fs_directories($basedir);
  return $fs_fulldirs;
}

/**
 * Returns an array with all file system files according to $conf['file_ext'] 
 * and $conf['picture_ext']
 * @param string $path recurse in this directory
 * @return array like "pic.jpg"=>array('tn_ext'=>'jpg' ... )
 */
function get_elements($path)
{
  global $conf;
  if (!isset($conf['flip_picture_ext']))
  {
    $conf['flip_picture_ext'] = array_flip($conf['picture_ext']);
  }
  if (!isset($conf['flip_file_ext']))
  {
    $conf['flip_file_ext'] = array_flip($conf['file_ext']);
  }

  $subdirs = array();
  $fs = array();
  if (is_dir($path) && $contents = opendir($path) )
  {
    while (($node = readdir($contents)) !== false)
    {
      if (is_file($path.'/'.$node))
      {
        $extension = get_extension($node);
        $filename_wo_ext = get_filename_wo_extension($node);

        // searching the thumbnail
        $tn_ext = $this->get_tn_ext($path, $filename_wo_ext);

        if (isset($conf['flip_picture_ext'][$extension]))
        {
          $fs[ $path.'/'.$node ] = array(
            'tn_ext' => $tn_ext,
            'has_high' => $this->get_has_high($path, $node)
            );
        }
        else if (isset($conf['flip_file_ext'][$extension]))
        { // file not a picture
          $representative_ext = $this->get_representative_ext($path, $filename_wo_ext);

          $fs[ $path.'/'.$node ] = array(
            'tn_ext' => $tn_ext,
            'representative_ext' => $representative_ext
            );
        }

      }
      elseif (is_dir($path.'/'.$node)
               and $node != '.'
               and $node != '..'
               and $node != 'pwg_high'
               and $node != 'pwg_representative'
               and $node != 'thumbnail' )
      {
        array_push($subdirs, $node);
      }
    } //end while readdir
    closedir($contents);

    foreach ($subdirs as $subdir)
    {
      $tmp_fs = $this->get_elements($path.'/'.$subdir);
      $fs = array_merge($fs, $tmp_fs);
    }
  } //end if is_dir
  return $fs;
}

// returns the name of the attributes that are supported for 
// update/synchronization according to configuration
function get_update_attributes()
{
  global $conf;
  $update_fields = array( 'has_high', 'representative_ext', 
      'filesize', 'width', 'height' );
  if ($conf['use_exif'])
  {
    $update_fields =
      array_merge(
        $update_fields,
        array_keys($conf['use_exif_mapping'])
        );
  }

  if ($conf['use_iptc'])
  {
    $update_fields =
      array_merge(
        $update_fields,
        array_keys($conf['use_iptc_mapping'])
        );
  }
  return $update_fields;
}

// returns a hash of attributes (metadata+filesize+width,...) for file
function get_element_update_attributes($file)
{
  global $conf;
  if (!is_file($file))
  {
    return null;
  }
  
  $data = array();
  
  $filename = basename($file);
  $data['has_high'] = $this->get_has_high( dirname($file), $filename );
  $data['representative_ext'] = $this->get_representative_ext( dirname($file), 
                                        get_filename_wo_extension($filename) );
  
  $data['filesize'] = floor(filesize($file)/1024);
  if ($image_size = @getimagesize($file))
  {
    $data['width'] = $image_size[0];
    $data['height'] = $image_size[1];
  }

  if ($conf['use_exif'])
  {
    $exif = get_sync_exif_data($file);

    if (count($exif) > 0)
    {
      foreach (array_keys($exif) as $key)
      {
        $data[$key] = addslashes($exif[$key]);
      }
    }
  }

  if ($conf['use_iptc'])
  {
    $iptc = get_sync_iptc_data($file);
    if (count($iptc) > 0)
    {
      foreach (array_keys($iptc) as $key)
      {
        $data[$key] = addslashes($iptc[$key]);
      }
    }
  }
  return $data;
}


//-------------------------------------------------- private functions --------
function get_representative_ext($path, $filename_wo_ext)
{
  global $conf;
  $base_test = $path.'/pwg_representative/';
  $base_test.= $filename_wo_ext.'.';
  foreach ($conf['picture_ext'] as $ext)
  {
    $test = $base_test.$ext;
    if (is_file($test))
    {
      return $ext;
    }
  }
  return null;
}

function get_tn_ext($path, $filename_wo_ext)
{
  global $conf;
  $base_test = $path.'/thumbnail/';
  $base_test.= $conf['prefix_thumbnail'].$filename_wo_ext.'.';
  foreach ($conf['picture_ext'] as $ext)
  {
    $test = $base_test.$ext;
    if (is_file($test))
    {
      return $ext;
    }
  }
  return null;
}

function get_has_high($path, $filename)
{
  if (is_file($path.'/pwg_high/'.$filename))
  {
    return 'true';
  }
  return null;
}

}
?>