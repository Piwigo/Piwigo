<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Returns a list of missing derivatives (not generated yet)
 * @param mixed[] $params
 *    @option string types (optional)
 *    @option int[] ids
 *    @option int max_urls
 *    @option int prev_page (optional)
 */
function ws_getMissingDerivatives($params, &$service)
{
  global $conf;

  if (empty($params['types']))
  {
    $types = array_keys(ImageStdParams::get_defined_type_map());
  }
  else
  {
    $types = array_intersect(array_keys(ImageStdParams::get_defined_type_map()), $params['types']);
    if (count($types)==0)
    {
      return new PwgError(WS_ERR_INVALID_PARAM, "Invalid types");
    }
  }

  $max_urls = $params['max_urls'];
  $query = 'SELECT MAX(id)+1, COUNT(*) FROM '. IMAGES_TABLE .';';
  list($max_id, $image_count) = pwg_db_fetch_row(pwg_query($query));

  if (0 == $image_count)
  {
    return array();
  }

  $start_id = $params['prev_page'];
  if ($start_id<=0)
  {
    $start_id = $max_id;
  }

  $uid = '&b='.time();

  $conf['question_mark_in_urls'] = $conf['php_extension_in_urls'] = true;
  $conf['derivative_url_style'] = 2; //script

  $qlimit = min(5000, ceil(max($image_count/500, $max_urls/count($types))));
  $where_clauses = ws_std_image_sql_filter( $params, '' );
  $where_clauses[] = 'id<start_id';

  if (!empty($params['ids']))
  {
    $where_clauses[] = 'id IN ('.implode(',',$params['ids']).')';
  }

  $query_model = '
SELECT id, path, representative_ext, width, height, rotation
  FROM '. IMAGES_TABLE .'
  WHERE '. implode(' AND ', $where_clauses) .'
  ORDER BY id DESC
  LIMIT '. $qlimit .'
;';

  $urls = array();
  do
  {
    $result = pwg_query(str_replace('start_id', $start_id, $query_model));
    $is_last = pwg_db_num_rows($result) < $qlimit;

    while ($row=pwg_db_fetch_assoc($result))
    {
      $start_id = $row['id'];
      $src_image = new SrcImage($row);
      if ($src_image->is_mimetype())
      {
        continue;
      }

      foreach($types as $type)
      {
        $derivative = new DerivativeImage($type, $src_image);
        if ($type != $derivative->get_type())
        {
          continue;
        }
        if (@filemtime($derivative->get_path())===false)
        {
          $urls[] = $derivative->get_url().$uid;
        }
      }

      if (count($urls)>=$max_urls and !$is_last)
      {
        break;
      }
    }
    if ($is_last)
    {
      $start_id = 0;
    }
  } while (count($urls)<$max_urls and $start_id);

  $ret = array();
  if ($start_id)
  {
    $ret['next_page'] = $start_id;
  }
  $ret['urls'] = $urls;
  return $ret;
}

/**
 * API method
 * Returns Piwigo version
 * @param mixed[] $params
 */
function ws_getVersion($params, &$service)
{
  return PHPWG_VERSION;
}

/**
 * API method
 * Returns general informations about the installation
 * @param mixed[] $params
 */
function ws_getInfos($params, &$service)
{
  $infos['version'] = PHPWG_VERSION;

  $query = 'SELECT COUNT(*) FROM '.IMAGES_TABLE.';';
  list($infos['nb_elements']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.CATEGORIES_TABLE.';';
  list($infos['nb_categories']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.CATEGORIES_TABLE.' WHERE dir IS NULL;';
  list($infos['nb_virtual']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.CATEGORIES_TABLE.' WHERE dir IS NOT NULL;';
  list($infos['nb_physical']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.IMAGE_CATEGORY_TABLE.';';
  list($infos['nb_image_category']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.TAGS_TABLE.';';
  list($infos['nb_tags']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.IMAGE_TAG_TABLE.';';
  list($infos['nb_image_tag']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.USERS_TABLE.';';
  list($infos['nb_users']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM `'.GROUPS_TABLE.'`;';
  list($infos['nb_groups']) = pwg_db_fetch_row(pwg_query($query));

  $query = 'SELECT COUNT(*) FROM '.COMMENTS_TABLE.';';
  list($infos['nb_comments']) = pwg_db_fetch_row(pwg_query($query));

  // first element
  if ($infos['nb_elements'] > 0)
  {
    $query = 'SELECT MIN(date_available) FROM '.IMAGES_TABLE.';';
    list($infos['first_date']) = pwg_db_fetch_row(pwg_query($query));
  }

  // unvalidated comments
  if ($infos['nb_comments'] > 0)
  {
    $query = 'SELECT COUNT(*) FROM '.COMMENTS_TABLE.' WHERE validated=\'false\';';
    list($infos['nb_unvalidated_comments']) = pwg_db_fetch_row(pwg_query($query));
  }

  // Cache size
  // TODO for real later
  $infos['cache_size'] = 4242;

  foreach ($infos as $name => $value)
  {
    $output[] = array(
      'name' => $name,
      'value' => $value,
    );
  }
  return array('infos' => new PwgNamedArray($output, 'item'));
}

/**
 * API method
 * Calculates and returns the size of the cache
 *
 * @since 12
 * @param mixed[] $params
 */
function ws_getCacheSize($params, &$service)
{
  global $conf;

  // Cache size
  $path_cache = $conf['data_location'];
  $infos['cache_size'] = null;
  if (function_exists('exec'))
  {
    @exec('du -sk '.$path_cache, $return_array_cache);
    if (
      is_array($return_array_cache)
      and !empty($return_array_cache[0])
      and preg_match('/^(\d+)\s/', $return_array_cache[0], $matches_cache)
    )
    {
      $infos['cache_size'] = $matches_cache[1] * 1024;
    }
  }

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  // Multiples sizes size
  $path_msizes = $conf['data_location'].'i';
  $msizes = get_cache_size_derivatives($path_msizes);

  $infos['msizes'] = array_fill_keys(array_keys(ImageStdParams::get_defined_type_map()), 0);
  $infos['msizes']['custom'] = 0;
  $all = 0;

  foreach(array_keys($infos['msizes']) as $size_type)
  {
    $infos['msizes'][$size_type] += @$msizes[derivative_to_url($size_type)];
    $all += $infos['msizes'][$size_type];
  }
  $infos['msizes']['all'] = $all;

  // Compiled templates size
  $path_template_c = $conf['data_location'].'templates_c';
  $infos['tsizes'] = null;
  if (function_exists('exec'))
  {
    @exec('du -sk '.$path_template_c, $return_array_template_c);
    if (
      is_array($return_array_template_c)
      and !empty($return_array_template_c[0])
      and preg_match('/^(\d+)\s/', $return_array_template_c[0], $matches_template_c)
    )
    {
      $infos['tsizes'] = $matches_template_c[1] * 1024;
    }
  }

  $infos['last_date_calc'] = date("Y-m-d H:i:s");

  foreach ($infos as $name => $value)
  {
    $output[] = array(
      'name' => $name,
      'value' => $value,
    );
  }

  conf_update_param("cache_sizes", $output, true);

  return array('infos' => new PwgNamedArray($output, 'item'));
}

/**
 * API method
 * Adds images to the caddie
 * @param mixed[] $params
 *    @option int[] image_id
 */
function ws_caddie_add($params, &$service)
{
  global $user;

  $query = '
SELECT id
  FROM '. IMAGES_TABLE .'
      LEFT JOIN '. CADDIE_TABLE .'
      ON id=element_id AND user_id='. $user['id'] .'
  WHERE id IN ('. implode(',',$params['image_id']) .')
    AND element_id IS NULL
;';
  $result = array_from_query($query, 'id');

  $datas = array();
  foreach ($result as $id)
  {
    $datas[] = array(
      'element_id' => $id,
      'user_id' => $user['id'],
      );
  }
  if (count($datas))
  {
    mass_inserts(
      CADDIE_TABLE,
      array('element_id','user_id'),
      $datas
      );
  }
  return count($datas);
}

/**
 * API method
 * Deletes rates of an user
 * @param mixed[] $params
 *    @option int user_id
 *    @option string anonymous_id (optional)
 */
function ws_rates_delete($params, &$service)
{
  $query = '
DELETE FROM '. RATE_TABLE .'
  WHERE user_id='. $params['user_id'];

  if (!empty($params['anonymous_id']))
  {
    $query .= ' AND anonymous_id=\''.$params['anonymous_id'].'\'';
  }
  if (!empty($params['image_id']))
  {
    $query .= ' AND element_id='.$params['image_id'];
  }

  $changes = pwg_db_changes(pwg_query($query));
  if ($changes)
  {
    include_once(PHPWG_ROOT_PATH.'include/functions_rate.inc.php');
    update_rating_score();
  }
  return $changes;
}

/**
 * API method
 * Performs a login
 * @param mixed[] $params
 *    @option string username
 *    @option string password
 */
function ws_session_login($params, &$service)
{
  if (try_log_user($params['username'], $params['password'], false))
  {
    return true;
  }
  return new PwgError(999, 'Invalid username/password');
}


/**
 * API method
 * Performs a logout
 * @param mixed[] $params
 */
function ws_session_logout($params, &$service)
{
  if (!is_a_guest())
  {
    logout_user();
  }
  return true;
}

/**
 * API method
 * Returns info about the current user
 * @param mixed[] $params
 */
function ws_session_getStatus($params, &$service)
{
  global $user, $conf;

  $res['username'] = is_a_guest() ? 'guest' : stripslashes($user['username']);
  foreach ( array('status', 'theme', 'language') as $k )
  {
    $res[$k] = $user[$k];
  }
  $res['pwg_token'] = get_pwg_token();
  $res['charset'] = get_pwg_charset();

  list($dbnow) = pwg_db_fetch_row(pwg_query('SELECT NOW();'));
  $res['current_datetime'] = $dbnow;
  $res['version'] = PHPWG_VERSION;

  // Piwigo Remote Sync does not support receiving the available sizes
  $piwigo_remote_sync_agent = 'Apache-HttpClient/';
  if (!isset($_SERVER['HTTP_USER_AGENT']) or substr($_SERVER['HTTP_USER_AGENT'], 0, strlen($piwigo_remote_sync_agent)) !== $piwigo_remote_sync_agent)
  {
    $res['available_sizes'] = array_keys(ImageStdParams::get_defined_type_map());
  }

  if (is_admin())
  {
    $res['upload_file_types'] = implode(
      ',',
      array_unique(
        array_map(
          'strtolower',
          $conf['upload_form_all_types'] ? $conf['file_ext'] : $conf['picture_ext']
          )
        )
      );

    $res['upload_form_chunk_size'] = $conf['upload_form_chunk_size'];
  }
  
  return $res;
}

/**
 * API method
 * Returns lines of users activity
 *  @since 12
 */
function ws_getActivityList($param, &$service)
{
  global $conf;

  /* Test Lantency */ 
  // sleep(1);
  
  $output_lines = array();
  $current_key = '';

  $user_ids = array();

  $query = '
SELECT
    activity_id,
    performed_by,
    object,
    object_id,
    action,
    session_idx,
    ip_address,
    occured_on,
    details
  FROM '.ACTIVITY_TABLE.'
  ORDER BY activity_id DESC LIMIT 100000
;'; //Limited to 100k before implementing pagination in v.13 (issue #1595)

  $line_id = 0;
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['details'] = str_replace('`groups`', 'groups', $row['details']);
    $row['details'] = str_replace('`rank`', 'rank', $row['details']);
    $details = @unserialize($row['details']);

    if (isset($details['method']))
    {
      $detailsType = 'method';
    }
    if (isset($details['script']))
    {
      $detailsType = 'script';
    }

    $line_key = $row['session_idx'].'~'.$row['object'].'~'.$row['action'].'~'; // idx~photo~add
  
    if ($line_key === $current_key)
    {
      // j'incrémente le counter de la ligne précédente
      $output_lines[count($output_lines)-1]['counter']++;
      $output_lines[count($output_lines)-1]['object_id'][] = $row['object_id'];
    }
    else
    {
      list($date, $hour) = explode(' ', $row['occured_on']);
      // New line
      $output_lines[] = array(
        'id' => $line_id,
        'object' => $row['object'],
        'object_id' => array($row['object_id']),
        'action' => $row['action'],
        'ip_address' => $row['ip_address'],
        'date' => format_date($date),
        'hour' => $hour,
        'user_id' => $row['performed_by'],
        'detailsType' => $detailsType,
        'details' => $details,
        'counter' => 1, 
      );

      @$user_ids[ $row['performed_by'] ]++;
      $current_key = $line_key;
      $line_id++;
    }
  }

  $username_of = array();
  $user_id_list = array();
  if (count($user_ids) > 0)
  {
    $query = '
SELECT
    `'.$conf['user_fields']['id'].'` AS user_id,
    `'.$conf['user_fields']['username'].'` AS username
  FROM '.USERS_TABLE.'
  WHERE `'.$conf['user_fields']['id'].'` IN ('.implode(',', array_keys($user_ids)).')
;';
    $username_of = query2array($query, 'user_id', 'username');
  }

  foreach ($output_lines as $idx => $output_line)
  {
    if ('user' == $output_line['object'])
    {
      foreach ($output_line['object_id'] as $user_id)
      {
        @$output_lines[$idx]['details']['users'][] = isset($username_of[$user_id]) ? $username_of[$user_id] : 'user#'.$user_id;
      }

      if (isset($output_lines[$idx]['details']['users']))
      {
        $output_lines[$idx]['details']['users_string'] = implode(', ', $output_lines[$idx]['details']['users']);
      }
    }

    $output_lines[$idx]['username'] = 'user#'.$output_lines[$idx]['user_id'];
    if (isset($username_of[ $output_lines[$idx]['user_id'] ]))
    {
      $output_lines[$idx]['username'] = $username_of[ $output_lines[$idx]['user_id'] ];
    }
  }

  $filterable_users = array();
  foreach ($user_ids as $key => $value)
  {
    if (isset($username_of[$key]))
    {
      array_push(
        $filterable_users, 
        array(
          'id' => $key,
          'username' => $username_of[$key],
          'nb_lines' => $value,
        )
      );
    }
    else
    {
      array_push(
        $filterable_users, 
        array(
          'id' => $key,
          'username' => 'user#'.$key,
          'nb_lines' => $value,
        )
      );
    }
  }

  //Multidimentionnal sorting
  usort($filterable_users, function ($a, $b) 
  {
    // compatible with PHP 7+ only
    // return strtolower($a['username']) <=> strtolower($b['username']);

    // still compatible with PHP 5
    return (strtolower($a['username']) >= strtolower($b['username']) ? 1 : 0);
  });

  // return $output_lines;
  return array(
    'result_lines' => $output_lines,
    'filterable_users' => $filterable_users,
  );
}

?>