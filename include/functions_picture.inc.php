<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

/**
 * @package functions\picture
 */


/**
 * Returns slideshow default params.
 * - period
 * - repeat
 * - play
 *
 * @return array
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

/**
 * Checks and corrects slideshow params
 *
 * @param array $params
 * @return array
 */
function correct_slideshow_params($params=array())
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

/**
 * Decodes slideshow string params into array
 *
 * @param string $encode_params
 * @return array
 */
function decode_slideshow_params($encode_params=null)
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

/**
 * Encodes slideshow array params into a string
 *
 * @param array $decode_params
 * @return string
 */
function encode_slideshow_params($decode_params=array())
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

?>