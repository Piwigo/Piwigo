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
  $page_size = 100000; //We will fetch X lines in database =/= lines displayed due to line concatenation
  $page_offset = $param['page']*$page_size;

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
    details,
    user_agent
  FROM '.ACTIVITY_TABLE.'
  WHERE object != \'system\'';

  if (isset($param['uid']))
  {
    $query.= '
    AND performed_by = '.$param['uid'];
  }
  elseif ('none' == $conf['activity_display_connections'])
  {
    $query.= '
    AND action NOT IN (\'login\', \'logout\')';
  }
  elseif ('admins_only' == $conf['activity_display_connections'])
  {
    include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
    $query.= '
    AND NOT (action IN (\'login\', \'logout\') AND object_id NOT IN ('.implode(',', get_admins()).'))';
  }

  $query.= '
  ORDER BY activity_id DESC
  LIMIT '.$page_size.' OFFSET '.$page_offset.'
;';

  $line_id = 0;
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['details'] = str_replace('`groups`', 'groups', $row['details']);
    $row['details'] = str_replace('`rank`', 'rank', $row['details']);
    $details = @unserialize($row['details']);

    if (isset($row['user_agent']))
    {
      $details['agent'] = $row['user_agent'];
    }

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
      // I increment the counter of the previous line
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

      $user_ids[ $row['performed_by'] ] = 1;
      if ('user' == $row['object'])
      {
        $user_ids[ $row['object_id'] ] = 1;
      }

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

  if (isset($param['uid'])) {
    $query = '
  SELECT
      count(*)
    FROM '.ACTIVITY_TABLE.'
    WHERE performed_by = '.$param['uid'].'
  ;';
  } else {
    $query = '
  SELECT
      count(*)
    FROM '.ACTIVITY_TABLE.'
  ;';
  }

  $result = (pwg_db_fetch_row(pwg_query($query))[0])/$page_size;

  return array(
    'result_lines' => $output_lines,
    'max_page' => floor($result),
    'params' => $param,
  );
}

/**
 * API method
 * Log a new line in visit history
 * @since 13
 */
function ws_history_log($params, &$service)
{
  global $logger, $page;

  if (!empty($params['section']) and in_array($params['section'], get_enums(HISTORY_TABLE, 'section')))
  {
    $page['section'] = $params['section'];
  }

  if (!empty($params['cat_id']))
  {
    $page['category'] = array('id' => $params['cat_id']);
  }

  if (!empty($params['tags_string']) and preg_match('/^\d+(,\d+)*$/', $params['tags_string']))
  {
    $page['tag_ids'] = explode(',', $params['tags_string']);
  }

  pwg_log($params['image_id'], 'picture');
}

/**
 * API method
 * Returns lines of an history search
 * @since 13
 */
function ws_history_search($param, &$service)
{

  include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
  include_once(PHPWG_ROOT_PATH.'admin/include/functions_history.inc.php');

  global $conf;

  if (isset($_GET['start']) and is_numeric($_GET['start']))
  {
    $page['start'] = $_GET['start'];
  }
  else
  {
    $page['start'] = 0;
  }

  $types = array_merge(array('none'), get_enums(HISTORY_TABLE, 'image_type'));

  $display_thumbnails = array('no_display_thumbnail' => l10n('No display'),
                              'display_thumbnail_classic' => l10n('Classic display'),
                              'display_thumbnail_hoverbox' => l10n('Hoverbox display')
    );

  // +-----------------------------------------------------------------------+
  // | Build search criteria and redirect to results                         |
  // +-----------------------------------------------------------------------+

  $page['errors'] = array();
  $search = array();

  // date start
  if (!empty($param['start']))
  {
    check_input_parameter('start', $param, false, '/^\d{4}-\d{2}-\d{2}$/');
    $search['fields']['date-after'] = $param['start'];
  }

  // date end
  if (!empty($param['end']))
  {
    check_input_parameter('end', $param, false, '/^\d{4}-\d{2}-\d{2}$/');
    $search['fields']['date-before'] = $param['end'];
  }

  // types
  if (empty($param['types']))
  {
    $search['fields']['types'] = $types;
  }
  else
  {
    check_input_parameter('types', $param, true, '/^('.implode('|', $types).')$/');
    $search['fields']['types'] = $param['types'];
  }

  // user
  $search['fields']['user'] = intval($param['user_id']);

  // image
  if (!empty($param['image_id']))
  {
    $search['fields']['image_id'] = intval($param['image_id']);
  }

  // filename
  if (!empty($param['filename']))
  {
    $search['fields']['filename'] = str_replace(
      '*',
      '%',
      pwg_db_real_escape_string($param['filename'])
      );
  }

  // ip
  if (!empty($param['ip']))
  {
    $search['fields']['ip'] = str_replace(
      '*',
      '%',
      pwg_db_real_escape_string($param['ip'])
      );
  }

  // thumbnails
  check_input_parameter('display_thumbnail', $param, false, '/^('.implode('|', array_keys($display_thumbnails)).')$/');

  $search['fields']['display_thumbnail'] = $param['display_thumbnail'];
  // Display choise are also save to one cookie
  if (!empty($param['display_thumbnail'])
      and isset($display_thumbnails[$param['display_thumbnail']]))
  {
    $cookie_val = $param['display_thumbnail'];
  }
  else
  {
    $cookie_val = null;
  }

  pwg_set_cookie_var('display_thumbnail', $cookie_val, strtotime('+1 month') );

  // TODO manage inconsistency of having $_POST['image_id'] and
  // $_POST['filename'] simultaneously

  // store seach in database
  if (!empty($search))
  {
    // register search rules in database, then they will be available on
    // thumbnails page and picture page.
    $query ='
  INSERT INTO '.SEARCH_TABLE.'
  (rules)
  VALUES
  (\''.pwg_db_real_escape_string(serialize($search)).'\')
  ;';

    pwg_query($query);

    $search_id = pwg_db_insert_id(SEARCH_TABLE);

    // Remove redirect for ajax //
    // redirect(
    //   PHPWG_ROOT_PATH.'admin.php?page=history&search_id='.$search_id
    //   );
  }
  else
  {
    $page['errors'][] = l10n('Empty query. No criteria has been entered.');
  }

  // what are the lines to display in reality ?
  $query = '
SELECT rules
  FROM '.SEARCH_TABLE.'
  WHERE id = '.$search_id.'
;';
  list($serialized_rules) = pwg_db_fetch_row(pwg_query($query));

  $page['search'] = unserialize($serialized_rules);


  /*TODO - no need to get a huge number of rows from db (should take only what needed for display + SQL_CALC_FOUND_ROWS*/
  $data = trigger_change('get_history', array(), $page['search'], $types);
  usort($data, 'history_compare');

  $page['nb_lines'] = count($data);

  //Number of ids of each kind
  $history_lines = array();
  $user_ids = array();
  $username_of = array();
  $category_ids = array();
  $image_ids = array();
  $has_tags = false;

  foreach ($data as $row)
  {
    $user_ids[$row['user_id']] = 1;

    if (isset($row['category_id']))
    {
      array_push($category_ids, $row['category_id'] );
    }

    if (isset($row['image_id']))
    {
      $image_ids[$row['image_id']] = 1;
    }

    if (isset($row['tag_ids']))
    {
      $has_tags = true;
    }

    $history_lines[] = $row;
  }

  // prepare reference data (users, tags, categories...)
  if (count($user_ids) > 0)
  {
    $query = '
SELECT '.$conf['user_fields']['id'].' AS id
     , '.$conf['user_fields']['username'].' AS username
  FROM '.USERS_TABLE.'
  WHERE id IN ('.implode(',', array_keys($user_ids)).')
;';
    $result = pwg_query($query);

    $username_of = array();
    while ($row = pwg_db_fetch_assoc($result))
    {
      $username_of[$row['id']] = stripslashes($row['username']);
    }
  }

  if (count($category_ids) > 0)
  {
    $query = '
SELECT id, uppercats
  FROM '.CATEGORIES_TABLE.'
  WHERE id IN ('.implode(',', array_values($category_ids)).')
;';
    $uppercats_of = query2array($query, 'id', 'uppercats');

    $full_cat_path = array();
    $name_of_category = array();

    foreach ($uppercats_of as $category_id => $uppercats)
    {
      $full_cat_path[$category_id] = get_cat_display_name_cache(
        $uppercats,
        'admin.php?page=album-'
      );
      
      $uppercats = explode(",", $uppercats);
      $name_of_category[$category_id] = get_cat_display_name_cache(
        end($uppercats),
        'admin.php?page=album-'
      );
    }
  }

  if (count($image_ids) > 0)
  {
    $query = '
SELECT
    id,
    IF(name IS NULL, file, name) AS label,
    filesize,
    file,
    path,
    representative_ext
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', array_keys($image_ids)).')
;';
    $image_infos = query2array($query, 'id');
  }

  if ($has_tags > 0)
  {
    $query = '
SELECT
    id,
    name, url_name
  FROM '.TAGS_TABLE;

    global $name_of_tag; // used for preg_replace
    $name_of_tag = array();
    $result = pwg_query($query);
    while ($row=pwg_db_fetch_assoc($result))
    {
      $name_of_tag[ $row['id'] ] = trigger_change("render_tag_name", $row["name"], $row);
    }
  }

  $i = 0;
  $first_line = $page['start'] + 1;
  $last_line = $page['start'] + $conf['nb_logs_page'];

  $summary['total_filesize'] = 0;
  $summary['guests_IP'] = array();

  $result = array();
  $sorted_members = array();

  foreach ($history_lines as $line)
  {
    if (isset($line['image_type']) and $line['image_type'] == 'high')
    {
      $summary['total_filesize'] += @intval($image_infos[$line['image_id']]['filesize']);
    }

    if ($line['user_id'] == $conf['guest_id'])
    {
      if (!isset($summary['guests_IP'][ $line['IP'] ]))
      {
        $summary['guests_IP'][ $line['IP'] ] = 0;
      }

      $summary['guests_IP'][ $line['IP'] ]++;
    }

    $i++;

    if ($i <= $first_line and $i >= $last_line)
    {
      continue;
    }

    $user_string = '';
    if (isset($username_of[$line['user_id']]))
    {
      $user_name = $username_of[$line['user_id']];
      $user_string.= $username_of[$line['user_id']];
    }
    else
    {
      $user_string.= $line['user_id'];
    }
    $user_string.= '&nbsp;<a href="';
    $user_string.= PHPWG_ROOT_PATH.'admin.php?page=history';
    $user_string.= '&amp;search_id='.$search_id;
    $user_string.= '&amp;user_id='.$line['user_id'];
    $user_string.= '">+</a>';

    $tag_names = '';
    $tag_ids = '';
    if (isset($line['tag_ids']))
    {
      $tag_names = preg_replace_callback(
        '/(\d+)/',
        function($m) use ($name_of_tag) { return isset($name_of_tag[$m[1]]) ? $name_of_tag[$m[1]] : $m[1];} ,
          $line['tag_ids']
        );
      $tag_ids = $line['tag_ids'];
    }

    $image_string = '';
    $image_title = '';
    $image_edit_string = '';
    $image_id = '';
    $cat_name = '';
    if (isset($line['image_id']))
    {
      $image_edit_string = PHPWG_ROOT_PATH.'admin.php?page=photo-'.$line['image_id'];
      $picture_url = make_picture_url(
        array(
          'image_id' => $line['image_id'],
          )
        );

      if (isset($image_infos[$line['image_id']]))
      {
        $element = array(
          'id' => $line['image_id'],
          'file' => $image_infos[$line['image_id']]['file'],
          'path' => $image_infos[$line['image_id']]['path'],
          'representative_ext' => $image_infos[$line['image_id']]['representative_ext'],
          );
        $thumbnail_display = $page['search']['fields']['display_thumbnail'];
      }
      else
      {
        $thumbnail_display = 'no_display_thumbnail';
      }

      $image_title = '';

      if (isset($image_infos[$line['image_id']]['label']))
      {
        $image_title.= ' '.trigger_change('render_element_description', $image_infos[$line['image_id']]['label']);
      }
      else
      {
        $image_edit_string = '';
        $image_title.= ' unknown filename';
      }

      $image_string = '';
      $image_id = $line['image_id'];

      $image_string =
      '<span><img src="'.@DerivativeImage::url(ImageStdParams::get_by_type(IMG_SQUARE), $element)
      .'" alt="'.$image_title.'" title="'.$image_title.'">';
    }

    @$sorted_members[$user_name] += 1;

    array_push( 
      $result,
      array(
        'DATE'       => format_date($line['date']),
        'TIME'       => $line['time'],
        'USER'       => $user_string,
        'USERNAME'   => $user_name,
        'USERID'     => $line['user_id'],
        'IP'         => $line['IP'],
        'IMAGE'      => $image_string,
        'IMAGENAME'  => $image_title,
        'IMAGEID'    => $image_id,
        'EDIT_IMAGE' => $image_edit_string,
        'TYPE'       => $line['image_type'],
        'SECTION'    => $line['section'],
        'FULL_CATEGORY_PATH'   => isset($full_cat_path[$line['category_id']]) ? strip_tags($full_cat_path[$line['category_id']]) : l10n('Root').$line['category_id'],
        'CATEGORY'   => isset($name_of_category[$line['category_id']]) ? $name_of_category[$line['category_id']] : l10n('Root').$line['category_id'],
        'TAGS'       => explode(",",$tag_names),
        'TAGIDS'     => explode(",",$tag_ids),
      )
    );
  }

  $max_page = ceil(count($result)/300);
  $result = array_reverse($result, true);
  $result = array_slice($result, $param['pageNumber']*300, 300);

  $summary['nb_guests'] = 0;
  if (count(array_keys($summary['guests_IP'])) > 0)
  {
    $summary['nb_guests'] = count(array_keys($summary['guests_IP']));

    // we delete the "guest" from the $username_of hash so that it is
    // avoided in next steps
    unset($username_of[ $conf['guest_id'] ]);
  }

  $summary['nb_members'] = count($username_of);

  $member_strings = array();
  foreach ($username_of as $user_id => $user_name)
  {
    $member_string = $user_name;
    $member_strings[] = array($member_string => $user_id);
  }

  arsort($sorted_members);
  unset($sorted_members['guest']);

  $search_summary = 
  array(
    'NB_LINES' => l10n_dec(
      '%d line filtered', '%d lines filtered',
      $page['nb_lines']
      ),
    'FILESIZE' => $summary['total_filesize'] != 0 ? ceil($summary['total_filesize']/1024) : 0,
    'USERS' => l10n_dec(
      '%d user', '%d users',
      $summary['nb_members'] + $summary['nb_guests']
      ),
    'MEMBERS' => $member_strings,
    'SORTED_MEMBERS' => $sorted_members,
    'GUESTS' => l10n_dec(
      '%d guest', '%d guests',
      $summary['nb_guests']
      ),
    );

  unset($name_of_tag);

  return array(
    'lines'   => $result,
    'params'  => $param,
    'maxPage' => ($max_page == 0) ? 1 : $max_page,
    'summary' => $search_summary
  );
}
?>
