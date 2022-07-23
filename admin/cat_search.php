<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_ADMINISTRATOR);


// +-----------------------------------------------------------------------+
// | tabs                                                                  |
// +-----------------------------------------------------------------------+

$page['tab'] = 'search';
include(PHPWG_ROOT_PATH.'admin/include/albums_tab.inc.php');

// +-----------------------------------------------------------------------+
// | Get Categories                                                        |
// +-----------------------------------------------------------------------+

$categories = array();

$query = '
SELECT id, name, status, uppercats
  FROM '.CATEGORIES_TABLE;

$result = query2array($query);

foreach ($result as $cat) 
{
  $cat['name'] = trigger_change('render_category_name', $cat['name'], 'admin_cat_list');

  $private = ($cat['status'] == 'private')? 1:0;

  $parents = explode(',', $cat['uppercats']);
  
  $content = array($cat['name'], $parents, $private);
  $categories[$cat['id']] = $content;
}

// +-----------------------------------------------------------------------+
// |                       template initialization                         |
// +-----------------------------------------------------------------------+

// let's find a custom placeholder
$query = '
SELECT
    name
  FROM '.CATEGORIES_TABLE.'
  ORDER BY RAND()
  LIMIT 1
;';
$lines = query2array($query);
$placeholder = null;
foreach ($lines as $line)
{
  $name = trigger_change('render_category_name', $line['name']);

  if (mb_strlen($name) > 25)
  {
    $name = mb_substr($name, 0, 25).'...';
  }

  $placeholder = $name;
  break;
}

if (empty($placeholder))
{
  $placeholder = l10n('Portraits');
}

$template->set_filename('cat_search', 'cat_search.tpl');

$template->assign(
  array(
    'data_cat' => $categories,
    'ADMIN_PAGE_TITLE' => l10n('Albums'),
    'placeholder' => $placeholder,
  )
);

// +-----------------------------------------------------------------------+
// |                          sending html code                            |
// +-----------------------------------------------------------------------+
$template->assign_var_from_handle('ADMIN_CONTENT', 'cat_search');
?>
