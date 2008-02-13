<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2003-2007 PhpWebGallery Team - http://phpwebgallery.net |
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

/**
 * @param element_info array containing element information from db;
 * at least 'id', 'path' should be present
 */
function get_element_path($element_info)
{
  $path = get_element_location($element_info);
  if ( !url_is_remote($path) )
  {
    $path = PHPWG_ROOT_PATH.$path;
  }
  return $path;
}

/*
 * @param element_info array containing element information from db;
 * at least 'id', 'path' should be present
 */
function get_element_url($element_info)
{
  $url = get_element_location($element_info);
  if ( !url_is_remote($url) )
  {
    $url = get_root_url().$url;
  }
  // plugins want another url ?
  return trigger_event('get_element_url', $url, $element_info);
}

/**
 * Returns the relative path of the element with regards to to the root
 * of PWG (not the current page). This function is not intended to be
 * called directly from code.
 * @param element_info array containing element information from db;
 * at least 'id', 'path' should be present
 */
function get_element_location($element_info)
{
  // maybe a cached watermark ?
  return trigger_event('get_element_location',
    $element_info['path'], $element_info);
}


/**
 * Returns the PATH to the image to be displayed in the picture page. If the
 * element is not a picture, then the representative image or the default
 * mime image. The path can be used in the php script, but not sent to the
 * browser.
 * @param element_info array containing element information from db;
 * at least 'id', 'path', 'representative_ext' should be present
 */
function get_image_path($element_info)
{
  global $conf;
  $ext = get_extension($element_info['path']);
  if (in_array($ext, $conf['picture_ext']))
  {
    if (isset($element_info['element_path']) )
    {
      return $element_info['element_path'];
    }
    return get_element_path($element_info);
  }

  $path = get_image_location($element_info);
  if ( !url_is_remote($path) )
  {
    $path = PHPWG_ROOT_PATH.$path;
  }
  return $path;
}

/**
 * Returns the URL of the image to be displayed in the picture page. If the
 * element is not a picture, then the representative image or the default
 * mime image. The URL can't be used in the php script, but can be sent to the
 * browser.
 * @param element_info array containing element information from db;
 * at least 'id', 'path', 'representative_ext' should be present
 */
function get_image_url($element_info)
{
  global $conf;
  $ext = get_extension($element_info['path']);
  if (in_array($ext, $conf['picture_ext']))
  {
    if (isset($element_info['element_url']) )
    {
      return $element_info['element_url'];
    }
    return get_element_url($element_info);
  }

  $url = get_image_location($element_info);
  if ( !url_is_remote($url) )
  {
    $url = get_root_url().$url;
  }
  return $url;
}

/**
 * Returns the relative path of the image (element/representative/mimetype)
 * with regards to the root of PWG (not the current page). This function
 * is not intended to be called directly from code.
 * @param element_info array containing element information from db;
 * at least 'id', 'path', 'representative_ext' should be present
 */
function get_image_location($element_info)
{
    if (isset($element_info['representative_ext'])
          and $element_info['representative_ext'] != '')
    {
      $pi = pathinfo($element_info['path']);
      $file_wo_ext = get_filename_wo_extension($pi['basename']);
      $path =
        $pi['dirname'].'/pwg_representative/'
        .$file_wo_ext.'.'.$element_info['representative_ext'];
    }
    else
    {
      $ext = get_extension($element_info['path']);
      $path = get_themeconf('mime_icon_dir');
      $path.= strtolower($ext).'.png';
      if ( !file_exists(PHPWG_ROOT_PATH.$path)
          and !empty($element_info['tn_ext']) )
      {
        $path = get_thumbnail_location($element_info);
      }
    }

  // plugins want another location ?
  return trigger_event( 'get_image_location', $path, $element_info);
}


/*
 * @param element_info array containing element information from db;
 * at least 'id', 'path', 'has_high' should be present
 */
function get_high_path($element_info)
{
  $path = get_high_location($element_info);
  if (!empty($path) and !url_is_remote($path) )
  {
    $path = PHPWG_ROOT_PATH.$path;
  }
  return $path;
}

/**
 * @param element_info array containing element information from db;
 * at least 'id', 'path', 'has_high' should be present
 */
function get_high_url($element_info)
{
  $url = get_high_location($element_info);
  if (!empty($url) and !url_is_remote($url) )
  {
    $url = get_root_url().$url;
  }
  // plugins want another url ?
  return trigger_event('get_high_url', $url, $element_info);
}

/**
 * @param element_info array containing element information from db;
 * at least 'id', 'path', 'has_high' should be present
 */
function get_high_location($element_info)
{
  $location = '';
  if ($element_info['has_high'] == 'true')
  {
    $pi = pathinfo($element_info['path']);
    $location=$pi['dirname'].'/pwg_high/'.$pi['basename'];
  }
  return trigger_event( 'get_high_location', $location, $element_info);
}


/**
 * @param what_part string one of 't' (thumbnail), 'e' (element), 'i' (image),
 *   'h' (high resolution image)
 * @param element_info array containing element information from db;
 * at least 'id', 'path' should be present
 */
function get_download_url($what_part, $element_info)
{
  $url = get_root_url().'action.php';
  $url = add_url_params($url,
      array(
        'id' => $element_info['id'],
        'part' => $what_part,
      )
    );
  return trigger_event( 'get_download_url', $url, $element_info);
}

?>
