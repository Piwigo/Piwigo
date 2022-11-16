<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * Display filtered history lines
 */

// +-----------------------------------------------------------------------+
// |                              functions                                |
// +-----------------------------------------------------------------------+

// +-----------------------------------------------------------------------+
// |                           initialization                              |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_history.inc.php');

$types = array_merge(array('none'), get_enums(HISTORY_TABLE, 'image_type'));

$display_thumbnails = array('no_display_thumbnail' => l10n('No display'),
                            'display_thumbnail_classic' => l10n('Classic display'),
                            'display_thumbnail_hoverbox' => l10n('Hoverbox display')
  );

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filename('history', 'history.tpl');

// TabSheet initialization
history_tabsheet();

$template->assign(
  array(
    'F_ACTION' => get_root_url().'admin.php?page=history',
    'API_METHOD' => 'ws.php?format=json&method=pwg.history.search'
    )
  );

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

if (isset($page['search_id']))
{
  $navbar = create_navigation_bar(
    get_root_url().'admin.php'.get_query_string_diff(array('start')),
    $page['nb_lines'],
    $page['start'],
    $conf['nb_logs_page']
    );

  $template->assign('navbar', $navbar);
}

// +-----------------------------------------------------------------------+
// |                             filter form                               |
// +-----------------------------------------------------------------------+

$form = array();

if (isset($page['search']))
{
  if (isset($page['search']['fields']['date-after']))
  {
    $form['start'] = $page['search']['fields']['date-after'];
  }

  if (isset($page['search']['fields']['date-before']))
  {
    $form['end'] = $page['search']['fields']['date-before'];
  }
}
else
{
  // by default, at page load, we want the selected date to be the current
  // date
  $form['start'] = $form['end'] = date('Y-m-d');
  $form['types'] = $types;
  // Hoverbox by default
  $form['display_thumbnail'] =
    pwg_get_cookie_var('display_thumbnail', 'no_display_thumbnail');
}

$form_param['ip'] = isset($_GET['filter_ip']) ? $_GET['filter_ip'] : @$form['ip'];
$form_param['image_id'] = isset($_GET['filter_image_id']) ? $_GET['filter_image_id'] : @$form['image_id'];
$form_param['user_id'] = isset($_GET['filter_user_id']) ? $_GET['filter_user_id'] : "-1";

if (isset($_GET['filter_ip']) or isset($_GET['filter_image_id']) or isset($_GET['filter_user_id']))
{
  $form['start'] = '';
}

if ($form_param['user_id'] != "-1") {
  $query = '
  SELECT
      username
    FROM '.USERS_TABLE.'
    WHERE id = '.$form_param['user_id'].'
  ;';

  list($form_param['user_name']) = pwg_db_fetch_row(pwg_query($query));
  $form_param['user_id'] = empty(pwg_db_fetch_row(pwg_query($query))) ? "-1" : $form_param['user_id'];
}

$template->assign(
  array(
    'USER_ID' => $form_param['user_id'],
    'USER_NAME' => @$form_param['user_name'],
    'IMAGE_ID' => $form_param['image_id'],
    'FILENAME' => @$form['filename'],
    'IP' => $form_param['ip'],
    'START' => @$form['start'],
    'END' => @$form['end'],
    )
  );

$template->assign('display_thumbnails', $display_thumbnails);
$template->assign('display_thumbnail_selected', $form['display_thumbnail']);
$template->assign('guest_id', $conf['guest_id']);
$template->assign('ADMIN_PAGE_TITLE', l10n('History'));

// +-----------------------------------------------------------------------+
// |                           html code display                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'history');
?>
