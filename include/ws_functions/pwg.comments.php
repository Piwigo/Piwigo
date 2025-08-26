<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Get comments
 * @since 16
 * @param mixed[] $params
 * 
 */
function ws_userComments_getList($params, &$service)
{
  global $conf;

  if (!$conf['activate_comments'])
  {
    return new PwgError(403, 'Comments are disabled');
  }

  // accepted status values
  $accepted_status = array('all', 'pending', 'validated');
  if (!in_array($params['status'], $accepted_status))
  {
    return new PwgError(401, 'Status must be: all, pending or validated');
  }

  // accepted values must match pagination options (5,10,25,50)
  $items_number = array(5, 10, 25, 50);
  if (!in_array($params['per_page'], $items_number))
  {
    return new PwgError(401, 'Per page must be: 5, 10, 25 or 50');
  }

  $where_clauses = array('1=1');

  if (isset($params['author_id']) and !empty($params['author_id']))
  {
    $where_clauses['author_id'] = 'author_id = \''. pwg_db_real_escape_string($params['author_id']) .'\'';
  }

  if (isset($params['image_id']) and !empty($params['image_id']))
  {
    $where_clauses[] = 'image_id = \''. pwg_db_real_escape_string($params['image_id']) .'\'';
  }

  if (!empty($params['f_min_date']))
  {
    $min = date_format(date_create($params['f_min_date']), "Y-m-d 00:00:00");
    $where_clauses[] = 'date >= \''. $min .'\'';
  }

  if (!empty($params['f_max_date']))
  {
    $max = date_format(date_create($params['f_max_date']), "Y-m-d 23:59:59");
    $where_clauses[] = 'date <= \''. $max .'\'';
  }

  // reset all filters during search
  if (!empty($params['search']))
  {
    $where_clauses = array('1=1');
    $where_clauses[] = 'content LIKE "%'. pwg_db_real_escape_string($params['search']) .'%"';
  }

  // summary
  $query = '
SELECT
  count(*) as all_comments,
  sum(validated = \'true\') as validated,
  sum(validated = \'false\') as pending
FROM '.COMMENTS_TABLE.'
WHERE '.implode(' AND ', $where_clauses).'
;';

  $summary = pwg_db_fetch_assoc(pwg_query($query));
  $total_comments = $summary['all_comments'];

  switch($params['status'])
  {
    case 'pending':
      $where_clauses[] = 'validated = \'false\'';
      $total_comments = $summary['pending'];
      break;
    
    case 'validated':
      $where_clauses[] = 'validated = \'true\'';
      $total_comments = $summary['validated'];
      break;
  }

  // comments
  $query = '
SELECT
    c.id,
    c.image_id,
    c.date,
    c.author,
    c.author_id,
    '.$conf['user_fields']['username'].' AS username,
    ui.status,
    c.content,
    i.path,
    i.representative_ext,
    i.file,
    i.date_available,
    validated,
    c.anonymous_id
  FROM '.COMMENTS_TABLE.' AS c
    INNER JOIN '.IMAGES_TABLE.' AS i
      ON i.id = c.image_id
    LEFT JOIN '.USERS_TABLE.' AS u
      ON u.'.$conf['user_fields']['id'].' = c.author_id
    LEFT JOIN '.USER_INFOS_TABLE.' AS ui
      ON ui.user_id = c.author_id
  WHERE '.implode(' AND ', $where_clauses).'
  ORDER BY c.date DESC
  LIMIT '.$params['per_page'] * $params['page'].', '.$params['per_page'].'
;';
  $result = pwg_query($query);

  $list = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    
    $medium = DerivativeImage::get_one(
      IMG_MEDIUM, 
      array(
        'id' => $row['image_id'],
        'path' => $row['path'],
        'representative_ext' => $row['representative_ext'],
      )
    )->get_url();
    
    if (empty($row['author_id']) or $row['author_id'] == $conf['guest_id'])
    {
      $author_name = $row['author'];
    }
    else
    {
      $author_name = stripslashes($row['username']);
    }

    $list[] = array(
      'id' => $row['id'],
      'admin_link' => get_root_url().'admin.php?page=photo-'.$row['image_id'],
      'medium_url' => $medium,
      'file' => $row['file'],
      'image_date_available' => format_date($row['date_available'], array('day_name','day','month','year','time')),
      'author' => trigger_change('render_comment_author', $author_name),
      'author_status' => $conf['webmaster_id'] == $row['author_id'] ? 'main_user' : $row['status'],
      'date' => format_date($row['date'], array('day_name','day','month','year','time')),
      'content' => trigger_change('render_comment_content', $row['content']),
      'raw_content' => $row['content'],
      'is_pending' => ('false' == $row['validated']),
    );
  }

  // filters
  $query = '
SELECT
  MIN(date) AS started_at,
  MAX(date) AS ended_at
FROM '.COMMENTS_TABLE.'
WHERE '.implode(' AND ', $where_clauses).'
;';

  $dates = pwg_db_fetch_assoc(pwg_query($query));

  unset($where_clauses['author_id']);
  $query = '
SELECT 
  author,
  author_id,
  count(*) as nb_authors
FROM '.COMMENTS_TABLE.' 
WHERE '.implode(' AND ', $where_clauses).'
GROUP BY author_id
;';

  $nb_authors_in = query2array($query);

  return array(
    'summary' => $summary,
    'comments' => $list,
    'filters' => array(
      'nb_authors' => $nb_authors_in,
      'started_at' => $dates['started_at'],
      'ended_at' => $dates['ended_at']
    ),
    'paging' => array(
      'page' => $params['page'],
      'per_page' => $params['per_page'],
      'total_pages' => max(0, ceil($total_comments / $params['per_page']) - 1),
    ),
  );
}

/**
 * API method
 * Delete comments
 * @since 16
 * @param mixed[] $params
 * 
 */
function ws_userComments_delete($params, &$service)
{
  include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, l10n('Invalid security token'));
  }

  $params['comment_id'] = array_unique($params['comment_id']);
  delete_user_comment($params['comment_id']);
  return 'Comment successfully deleted';
}

/**
 * API method
 * Validate comments
 * @since 16
 * @param mixed[] $params
 * 
 */
function ws_userComments_validate($params, &$service)
{
  include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');

  if (get_pwg_token() != $params['pwg_token'])
  {
    return new PwgError(403, l10n('Invalid security token'));
  }

  $params['comment_id'] = array_unique($params['comment_id']);
  validate_user_comment($params['comment_id']);
  return 'Comment successfully validated';
}