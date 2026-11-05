<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * API method
 * Searches albums, photos, users, people, plugins and pages for the
 * administration global search
 * @param mixed[] $params
 *    @option string query
 *    @option int limit
 *    @option string type (optional)
 *    @option int page
 *    @option int per_page
 */
function ws_admin_globalSearch($params, &$service)
{
  $search = trim(stripslashes($params['query']));

  if (mb_strlen($search) < 2)
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'query must be at least 2 characters long');
  }

  $types = array(
    'albums' => l10n('Albums'),
    'photos' => l10n('Photos'),
    'users' => l10n('Users'),
    'people' => l10n('People'),
    'plugins' => l10n('Plugins'),
    'pages' => l10n('Pages'),
    );

  $type = $params['type'] ?? null;

  if (isset($type) and !isset($types[$type]))
  {
    return new PwgError(WS_ERR_INVALID_PARAM, 'Invalid input parameter type');
  }

  $like = '\'%'.pwg_db_real_escape_string(addcslashes($search, '%_')).'%\'';
  $root_url = get_root_url();

  if (isset($type))
  {
    $per_page = $params['per_page'];
    $offset = $params['page'] * $per_page;
    $wanted = array($type => $types[$type]);
  }
  else
  {
    $per_page = $params['limit'];
    $offset = 0;
    $wanted = $types;
  }

  $groups = array();
  foreach ($wanted as $id => $label)
  {
    $function = 'ws_admin_global_search_'.$id;
    $found = $function($search, $like, $per_page, $offset, $root_url);

    $groups[] = array(
      'id' => $id,
      'label' => $label,
      'total' => $found['total'],
      'items' => $found['items'],
      );
  }

  $result = array(
    'query' => $search,
    'groups' => trigger_change('ws_admin_global_search', $groups, $search, $per_page, $type),
    );

  if (isset($type))
  {
    $result['paging'] = array(
      'page' => $params['page'],
      'per_page' => $per_page,
      'total' => $groups[0]['total'],
      );
  }

  return $result;
}

function ws_admin_global_search_albums($search, $like, $per_page, $offset, $root_url)
{
  $where = '
  WHERE name LIKE '.$like;

  $query = '
SELECT id, name, uppercats
  FROM '.CATEGORIES_TABLE.$where.'
  ORDER BY global_rank
  LIMIT '.$per_page.'
  OFFSET '.$offset.'
;';
  $items = array();
  $result = pwg_query($query);

  $query = '
SELECT COUNT(*)
  FROM '.CATEGORIES_TABLE.$where.'
;';
  list($total) = pwg_db_fetch_row(pwg_query($query));

  while ($row = pwg_db_fetch_assoc($result))
  {
    $items[] = array(
      'id' => $row['id'],
      'label' => strip_tags(trigger_change('render_category_name', $row['name'], __FUNCTION__) ?? ''),
      'sub' => strip_tags(get_cat_display_name_cache($row['uppercats'], null)),
      'url' => $root_url.'admin.php?page=album-'.$row['id'],
      );
  }

  return array('items' => $items, 'total' => $total);
}

function ws_admin_global_search_photos($search, $like, $per_page, $offset, $root_url)
{
  $where = '
  WHERE name LIKE '.$like.'
    OR file LIKE '.$like;

  $query = '
SELECT *
  FROM '.IMAGES_TABLE.$where.'
  ORDER BY id DESC
  LIMIT '.$per_page.'
  OFFSET '.$offset.'
;';
  $items = array();
  $result = pwg_query($query);

  $query = '
SELECT COUNT(*)
  FROM '.IMAGES_TABLE.$where.'
;';
  list($total) = pwg_db_fetch_row(pwg_query($query));

  while ($row = pwg_db_fetch_assoc($result))
  {
    $name = strip_tags(trigger_change('render_element_name', $row['name'], __FUNCTION__) ?? '');

    $items[] = array(
      'id' => $row['id'],
      'label' => '' != $name ? $name : $row['file'],
      'sub' => $row['file'],
      'thumb' => DerivativeImage::url(ImageStdParams::get_by_type(IMG_SQUARE), $row),
      'url' => $root_url.'admin.php?page=photo-'.$row['id'],
      );
  }

  return array('items' => $items, 'total' => $total);
}

function ws_admin_global_search_users($search, $like, $per_page, $offset, $root_url)
{
  global $conf;

  $from = '
  FROM '.USERS_TABLE.' AS u
    INNER JOIN '.USER_INFOS_TABLE.' AS ui
    ON ui.user_id = u.'.$conf['user_fields']['id'].'
  WHERE u.'.$conf['user_fields']['username'].' LIKE '.$like.'
    OR u.'.$conf['user_fields']['email'].' LIKE '.$like;

  $query = '
SELECT
    u.'.$conf['user_fields']['id'].' AS id,
    u.'.$conf['user_fields']['username'].' AS username,
    u.'.$conf['user_fields']['email'].' AS email,
    ui.status'.$from.'
  ORDER BY u.'.$conf['user_fields']['username'].'
  LIMIT '.$per_page.'
  OFFSET '.$offset.'
;';
  $items = array();
  $result = pwg_query($query);

  $query = '
SELECT COUNT(*)'.$from.'
;';
  list($total) = pwg_db_fetch_row(pwg_query($query));

  while ($row = pwg_db_fetch_assoc($result))
  {
    $items[] = array(
      'id' => $row['id'],
      'label' => stripslashes($row['username']),
      'sub' => $row['email'] ?? '',
      'status' => $row['status'],
      'url' => $root_url.'admin.php?page=user_list&user_id='.$row['id'],
      );
  }

  return array('items' => $items, 'total' => $total);
}

function ws_admin_global_search_people($search, $like, $per_page, $offset, $root_url)
{
  $where = '
  WHERE name LIKE '.$like;

  $query = '
SELECT id, name, cover_image_person_id
  FROM '.PERSONS_TABLE.$where.'
  ORDER BY name
  LIMIT '.$per_page.'
  OFFSET '.$offset.'
;';
  $people = query2array($query);

  $query = '
SELECT COUNT(*)
  FROM '.PERSONS_TABLE.$where.'
;';
  list($total) = pwg_db_fetch_row(pwg_query($query));

  $covers = get_people_covers(array_column($people, 'cover_image_person_id'));

  $items = array();
  foreach ($people as $person)
  {
    $cover = $covers[ $person['cover_image_person_id'] ] ?? null;

    $items[] = array(
      'id' => $person['id'],
      'label' => $person['name'],
      'sub' => '',
      'thumb' => $cover['src'] ?? null,
      'face' => $cover ? get_person_face_background($cover) : null,
      'url' => $root_url.'admin.php?page=people&search='.urlencode($person['name']),
      );
  }

  return array('items' => $items, 'total' => $total);
}

function ws_admin_global_search_plugins($search, $like, $per_page, $offset, $root_url)
{
  include_once(PHPWG_ROOT_PATH.'admin/include/plugins.class.php');

  $plugins = new plugins();
  $plugins->sort_fs_plugins('name');

  $matching = array();
  foreach ($plugins->fs_plugins as $plugin_id => $fs_plugin)
  {
    if (false === mb_stripos($fs_plugin['name'], $search) and false === mb_stripos($plugin_id, $search))
    {
      continue;
    }

    $state = $plugins->db_plugins_by_id[$plugin_id]['state'] ?? 'uninstalled';

    $url = $root_url.'admin.php?page=plugins_installed&search='.urlencode($fs_plugin['name']);
    if ('active' == $state and $fs_plugin['hasSettings'])
    {
      $url = $root_url.'admin.php?page=plugin-'.$plugin_id;
    }

    $matching[] = array(
      'id' => $plugin_id,
      'label' => $fs_plugin['name'],
      'sub' => $fs_plugin['version'],
      'status' => $state,
      'url' => $url,
      );
  }

  return array(
    'items' => array_slice($matching, $offset, $per_page),
    'total' => count($matching),
    );
}

function ws_admin_global_search_pages($search, $like, $per_page, $offset, $root_url)
{
  global $conf;

  $pages = array(
    array('path' => array(l10n('Dashboard')), 'page' => ''),

    array('path' => array(l10n('Photos'), l10n('Add')), 'page' => 'photos_add'),
    array('path' => array(l10n('Photos'), l10n('Rating')), 'page' => 'rating', 'if' => $conf['rate']),
    array('path' => array(l10n('Photos'), l10n('Tags')), 'page' => 'tags'),
    array('path' => array(l10n('Photos'), l10n('People')), 'page' => 'people'),
    array('path' => array(l10n('Photos'), l10n('Recent photos')), 'page' => 'batch_manager&filter=prefilter-last_import'),
    array('path' => array(l10n('Photos'), l10n('Batch Manager')), 'page' => 'batch_manager'),
    array('path' => array(l10n('Photos'), l10n('Caddie')), 'page' => 'batch_manager&filter=prefilter-caddie'),
    array('path' => array(l10n('Photos'), l10n('Orphans')), 'page' => 'batch_manager&filter=prefilter-no_album'),

    array('path' => array(l10n('Albums'), l10n('Manage')), 'page' => 'albums'),
    array('path' => array(l10n('Albums'), l10n('Properties')), 'page' => 'cat_options'),

    array('path' => array(l10n('Users'), l10n('Manage')), 'page' => 'user_list'),
    array('path' => array(l10n('Users'), l10n('Groups')), 'page' => 'group_list'),
    array('path' => array(l10n('Users'), l10n('Notification')), 'page' => 'notification_by_mail'),

    array('path' => array(l10n('Plugins')), 'page' => 'plugins'),

    array('path' => array(l10n('Tools'), l10n('Synchronize')), 'page' => 'site_update&site=1', 'if' => $conf['enable_synchronization']),
    array('path' => array(l10n('Tools'), l10n('History')), 'page' => 'stats&year='.date('Y').'&month='.date('n')),
    array('path' => array(l10n('Tools'), l10n('Maintenance')), 'page' => 'maintenance'),
    array('path' => array(l10n('Tools'), l10n('Maintenance'), l10n('Environment')), 'page' => 'maintenance&tab=env'),
    array('path' => array(l10n('Tools'), l10n('Maintenance'), l10n('System Activities')), 'page' => 'maintenance&tab=sys'),
    array('path' => array(l10n('Tools'), l10n('Comments')), 'page' => 'comments', 'if' => $conf['activate_comments']),
    array('path' => array(l10n('Tools'), l10n('Updates')), 'page' => 'updates', 'if' => $conf['enable_core_update']),

    array('path' => array(l10n('Configuration'), l10n('Options')), 'page' => 'configuration'),
    array('path' => array(l10n('Configuration'), l10n('Options'), l10n('General')), 'page' => 'configuration&section=main'),
    array('path' => array(l10n('Configuration'), l10n('Options'), l10n('Photo sizes')), 'page' => 'configuration&section=sizes'),
    array('path' => array(l10n('Configuration'), l10n('Options'), l10n('Watermark')), 'page' => 'configuration&section=watermark'),
    array('path' => array(l10n('Configuration'), l10n('Options'), l10n('Display')), 'page' => 'configuration&section=display'),
    array('path' => array(l10n('Configuration'), l10n('Options'), l10n('Comments')), 'page' => 'configuration&section=comments'),
    array('path' => array(l10n('Configuration'), l10n('Options'), l10n('Search')), 'page' => 'configuration&section=search'),
    array('path' => array(l10n('Configuration'), l10n('Menu Management')), 'page' => 'menubar'),
    array('path' => array(l10n('Configuration'), l10n('Templates')), 'page' => 'extend_for_templates', 'if' => $conf['show_template_in_side_menu']),
    array('path' => array(l10n('Configuration'), l10n('Languages')), 'page' => 'languages'),
    array('path' => array(l10n('Configuration'), l10n('Themes')), 'page' => 'themes'),

    array('path' => array(l10n('Help Me')), 'page' => 'help'),
    );

  foreach ($pages as $i => $page)
  {
    if (isset($page['if']) and !$page['if'])
    {
      unset($pages[$i]);
    }
  }

  $matching = array();
  foreach ($pages as $page)
  {
    $path = implode(' > ', $page['path']);

    if (false === mb_stripos($path, $search) and false === mb_stripos($page['page'], $search))
    {
      continue;
    }

    $matching[] = array(
      'id' => $page['page'],
      'label' => array_pop($page['path']),
      'sub' => implode(' > ', $page['path']),
      'url' => $root_url.'admin.php'.('' != $page['page'] ? '?page='.$page['page'] : ''),
      );
  }

  return array(
    'items' => array_slice($matching, $offset, $per_page),
    'total' => count($matching),
    );
}

?>
