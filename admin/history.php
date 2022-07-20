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

$template->assign(
  array(
    'IMAGE_ID' => @$form['image_id'],
    'FILENAME' => @$form['filename'],
    'IP' => @$form['ip'],
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
