<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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



/*
 * Returns the name of a photo according to its name and its filename.
 * @param name string
 * @param filename string
 * @return string
 */
function get_image_name($name, $filename)
{
  if (!empty($name))
  {
    return $name;
  }
  else
  {
    return get_name_from_file($filename);
  }
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
    $url = embellish_url(get_root_url().$url);
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



/*
 * get slideshow default params into array
 *
 * @param void
 *
 * @return slideshow default values into array
 */
function get_default_slideshow_params()
{
  global $conf;

  return array(
    'period' => $conf['slideshow_period'],
    'repeat' => $conf['slideshow_repeat'],
    'play' => true,
    );
}

/*
 * check and correct slideshow params from array
 *
 * @param array of params
 *
 * @return slideshow corrected values into array
 */
function correct_slideshow_params($params = array())
{
  global $conf;

  if ($params['period'] < $conf['slideshow_period_min'])
  {
    $params['period'] = $conf['slideshow_period_min'];
  }
  else if ($params['period'] > $conf['slideshow_period_max'])
  {
    $params['period'] = $conf['slideshow_period_max'];
  }

  return $params;
}

/*
 * Decode slideshow string params into array
 *
 * @param string params like ""
 *
 * @return slideshow values into array
 */
function decode_slideshow_params($encode_params = null)
{
  global $conf;

  $result = get_default_slideshow_params();

  if (is_numeric($encode_params))
  {
    $result['period'] = $encode_params;
  }
  else
  {
    $matches = array();
    if (preg_match_all('/([a-z]+)-(\d+)/', $encode_params, $matches))
    {
      $matchcount = count($matches[1]);
      for ($i = 0; $i < $matchcount; $i++)
      {
        $result[$matches[1][$i]] = $matches[2][$i];
      }
    }

    if (preg_match_all('/([a-z]+)-(true|false)/', $encode_params, $matches))
    {
      $matchcount = count($matches[1]);
      for ($i = 0; $i < $matchcount; $i++)
      {
        $result[$matches[1][$i]] = get_boolean($matches[2][$i]);
      }
    }
  }

  return correct_slideshow_params($result);
}

/*
 * Encode slideshow array params into array
 *
 * @param array params
 *
 * @return slideshow values into string
 */
function encode_slideshow_params($decode_params = array())
{
  global $conf;

  $params = array_diff_assoc(correct_slideshow_params($decode_params), get_default_slideshow_params());
  $result = '';

  foreach ($params as $name => $value)
  {
    // boolean_to_string return $value, if it's not a bool
    $result .= '+'.$name.'-'.boolean_to_string($value);
  }

  return $result;
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

?>
