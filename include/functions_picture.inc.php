<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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