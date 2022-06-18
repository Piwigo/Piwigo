<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * @package functions\rate
 */


/**
 * Rate a picture by the current user.
 *
 * @param int $image_id
 * @param float $rate
 * @return array as return by update_rating_score()
 */
function rate_picture($image_id, $rate)
{
  global $conf, $user;

  if (!isset($rate)
      or !$conf['rate']
      or !preg_match('/^[0-9]+$/', $rate)
      or !in_array($rate, $conf['rate_items']))
  {
    return false;
  }

  $user_anonymous = is_autorize_status(ACCESS_CLASSIC) ? false : true;

  if ($user_anonymous and !$conf['rate_anonymous'])
  {
    return false;
  }

  $ip_components = explode('.', $_SERVER["REMOTE_ADDR"]);
  if (count($ip_components) > 3)
  {
    array_pop($ip_components);
  }
  $anonymous_id = implode ('.', $ip_components);

  if ($user_anonymous)
  {
    $save_anonymous_id = pwg_get_cookie_var('anonymous_rater', $anonymous_id);

    if ($anonymous_id != $save_anonymous_id)
    { // client has changed his IP adress or he's trying to fool us
      $query = '
SELECT element_id
  FROM '.RATE_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \''.$anonymous_id.'\'
;';
      $already_there = array_from_query($query, 'element_id');

      if (count($already_there) > 0)
      {
        $query = '
DELETE
  FROM '.RATE_TABLE.'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \''.$save_anonymous_id.'\'
    AND element_id IN ('.implode(',', $already_there).')
;';
         pwg_query($query);
       }

       $query = '
UPDATE '.RATE_TABLE.'
  SET anonymous_id = \'' .$anonymous_id.'\'
  WHERE user_id = '.$user['id'].'
    AND anonymous_id = \'' . $save_anonymous_id.'\'
;';
       pwg_query($query);
    } // end client changed ip

    pwg_set_cookie_var('anonymous_rater', $anonymous_id);
  } // end anonymous user

  $query = '
DELETE
  FROM '.RATE_TABLE.'
  WHERE element_id = '.$image_id.'
    AND user_id = '.$user['id'].'
';
  if ($user_anonymous)
  {
    $query.= ' AND anonymous_id = \''.$anonymous_id.'\'';
  }
  pwg_query($query);
  $query = '
INSERT
  INTO '.RATE_TABLE.'
  (user_id,anonymous_id,element_id,rate,date)
  VALUES
  ('
    .$user['id'].','
    .'\''.$anonymous_id.'\','
    .$image_id.','
    .$rate
    .',NOW())
;';
  pwg_query($query);

  return update_rating_score($image_id);
}


/**
 * Update images.rating_score field.
 * We use a bayesian average (http://en.wikipedia.org/wiki/Bayesian_average) with
 *  C = average number of rates per item
 *  m = global average rate (all rates)
 *
 * @param int|false $element_id if false applies to all
 * @return array (score, average, count) values are null if $element_id is false
*/
function update_rating_score($element_id = false)
{
  if ( ($alt_result = trigger_change('update_rating_score', false, $element_id)) !== false)
  {
    return $alt_result;
  }

  $query = '
SELECT element_id,
    COUNT(rate) AS rcount,
    SUM(rate) AS rsum
  FROM '.RATE_TABLE.'
  GROUP by element_id';

  $all_rates_count = 0;
  $all_rates_avg = 0;
  $item_ratecount_avg = 0;
  $by_item = array();

  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $all_rates_count += $row['rcount'];
    $all_rates_avg += $row['rsum'];
    $by_item[$row['element_id']] = $row;
  }

  if ($all_rates_count>0)
  {
    $all_rates_avg /= $all_rates_count;
    $item_ratecount_avg = $all_rates_count / count($by_item);
  }

  $updates = array();
  foreach ($by_item as $id => $rate_summary )
  {
    $score = ( $item_ratecount_avg * $all_rates_avg + $rate_summary['rsum'] ) / ($item_ratecount_avg + $rate_summary['rcount']);
    $score = round($score,2);
    if ($id==$element_id)
    {
      $return = array(
        'score' => $score,
        'average' => round($rate_summary['rsum'] / $rate_summary['rcount'], 2),
        'count' => $rate_summary['rcount'],
        );
    }
    $updates[] = array( 'id'=>$id, 'rating_score'=>$score );
  }
  mass_updates(
    IMAGES_TABLE,
    array(
      'primary' => array('id'),
      'update' => array('rating_score')
      ),
    $updates
    );

  //set to null all items with no rate
  if ( !isset($by_item[$element_id]) )
  {
    $query='
SELECT id FROM '.IMAGES_TABLE .'
  LEFT JOIN '.RATE_TABLE.' ON id=element_id
  WHERE element_id IS NULL AND rating_score IS NOT NULL';

    $to_update = array_from_query( $query, 'id');

    if ( !empty($to_update) )
    {
      $query='
UPDATE '.IMAGES_TABLE .'
  SET rating_score=NULL
  WHERE id IN (' . implode(',',$to_update) . ')';
    pwg_query($query);
    }
  }

  return isset($return) ? $return : array('score'=>null, 'average'=>null, 'count'=>0 );
}

?>