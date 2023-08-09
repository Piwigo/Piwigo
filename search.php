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

$search = array(
  'mode' => 'AND',
  'fields' => array(
    'allwords' => array(
      'words' => $words,
      'mode' => 'AND',
      'fields' => array('file', 'name', 'comment', 'tags', 'cat-title', 'cat-desc'),
    ),
    'cat' => array(
      'words' => array(),
      'sub_inc' => true,
    ),
  ),
);

if (count(get_available_tags()) > 0)
{
  $search['fields']['tags'] = array(
    'words' => array(),
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

save_search_and_redirect($search);
?>
