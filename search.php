<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

//--------------------------------------------------------------------- include
define('PHPWG_ROOT_PATH','./');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );
include_once(PHPWG_ROOT_PATH.'include/functions_search.inc.php');

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+
check_status(ACCESS_GUEST);

trigger_notify('loc_begin_search');

// +-----------------------------------------------------------------------+
// | Create a default search                                               |
// +-----------------------------------------------------------------------+

$words = array();
if (!empty($_GET['q']))
{
  $words = split_allwords($_GET['q']);
}

$cat_ids = array();
if (isset($_GET['cat_id']))
{
  check_input_parameter('cat_id', $_GET, false, PATTERN_ID);
  $cat_ids = array($_GET['cat_id']);
}

$search = array(
  'mode' => 'AND',
  'fields' => array(
    'allwords' => array(
      'words' => $words,
      'mode' => 'AND',
      'fields' => array('file', 'name', 'comment', 'tags', 'author', 'cat-title', 'cat-desc'),
    ),
    'cat' => array(
      'words' => $cat_ids,
      'sub_inc' => true,
    ),
  ),
);

if (count(get_available_tags()) > 0)
{
  $tag_ids = array();
  if (isset($_GET['tag_id']))
  {
    check_input_parameter('tag_id', $_GET, false, '/^\d+(,\d+)*$/');
    $tag_ids = explode(',', $_GET['tag_id']);
  }

  $search['fields']['tags'] = array(
    'words' => $tag_ids,
    'mode'  => 'AND',
  );
}

// does this Piwigo has authors for current user?
$query = '
SELECT
    id
  FROM '.IMAGES_TABLE.' AS i
    JOIN '.IMAGE_CATEGORY_TABLE.' AS ic ON ic.image_id = i.id
  '.get_sql_condition_FandF(
    array(
      'forbidden_categories' => 'category_id',
      'visible_categories' => 'category_id',
      'visible_images' => 'id'
      ),
    ' WHERE '
    ).'
    AND author IS NOT NULL
    LIMIT 1
;';
$first_author = query2array($query);

if (count($first_author) > 0)
{
  $search['fields']['author'] = array(
    'words' => array(),
    'mode' => 'OR',
  );
}

list($search_uuid, $search_url) = save_search($search);
redirect($search_url);
?>
