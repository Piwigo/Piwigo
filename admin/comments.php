<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

if (!defined('PHPWG_ROOT_PATH'))
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

if (isset($_GET['start']) and is_numeric($_GET['start']))
{
  $page['start'] = $_GET['start'];
}
else
{
  $page['start'] = 0;
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

if (!empty($_POST))
{
  if (empty($_POST['comments']))
  {
    $template->assign(
      array(
        'save_warning' => l10n('Select at least one comment')
      )
    );
  }
  else
  {
    include_once( PHPWG_ROOT_PATH .'include/functions_comment.inc.php' );
    check_input_parameter('comments', $_POST, true, PATTERN_ID);

    if (isset($_POST['validate']))
    {
      validate_user_comment($_POST['comments']);

      $template->assign(
        array(
          'save_success' => l10n_dec(
            '%d user comment validated', '%d user comments validated',
            count($_POST['comments'])
          )
        )
      );
    }

    if (isset($_POST['reject']))
    {
      delete_user_comment($_POST['comments']);

      $template->assign(
        array(
          'save_error' => l10n_dec(
            '%d user comment rejected', '%d user comments rejected',
            count($_POST['comments'])
          )
        )
      );
    }
  }
}

// +-----------------------------------------------------------------------+
// |                             template init                             |
// +-----------------------------------------------------------------------+

$template->set_filenames(array('comments'=>'comments.tpl'));

$template->assign(
  array(
    'F_ACTION' => get_root_url().'admin.php?page=comments'
    )
  );

// +-----------------------------------------------------------------------+
// | Tabs                                                                  |
// +-----------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');

$my_base_url = get_root_url().'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('comments');
$tabsheet->select('');
$tabsheet->assign();

// +-----------------------------------------------------------------------+
// |                           comments display                            |
// +-----------------------------------------------------------------------+

$nb_total = 0;
$nb_pending = 0;
$nb_validated = 0;

$query = '
SELECT
    COUNT(*) AS counter,
    validated
  FROM '.COMMENTS_TABLE.'
  GROUP BY validated
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $nb_total+= $row['counter'];

  if ('false' == $row['validated'])
  {
    $nb_pending = $row['counter'];
  }
}

$nb_validated = $nb_total - $nb_pending;

if (!isset($_GET['filter']) and $nb_pending > 0)
{
  $page['filter'] = 'pending';
}
else
{
  $page['filter'] = 'all';
}

if (isset($_GET['filter']) and ('pending' == $_GET['filter'] or 'validated' == $_GET['filter']))
{
  $page['filter'] = $_GET['filter'];
}

if (isset($_GET['status']))
{
  $displayed_status = $_GET['status'];
}
else
{
  $displayed_status = 'all';
}

if (isset($_GET['author']))
{
  $author = $_GET['author'];
}
else 
{
  $author = 'all';
}

// by default, no filter by date is active
$start = $end = "";

if (isset($_GET['start_date'])){
  $start = $_GET['start_date'];
}

if (isset($_GET['end_date'])){
  $end = $_GET['end_date'];
}

$template->assign(
  array(
    'nb_total' => $nb_total,
    'nb_pending' => $nb_pending,
    'nb_validated' => $nb_validated,
    'filter' => $page['filter'],
    'displayed_status' => $displayed_status,
    'displayed_author' => $author,
    'START' => $start,
    'END' => $end,
    )
  );

$where_clauses = array('1=1');

if ('pending' == $page['filter'])
{
  $where_clauses[] = 'validated=\'false\'';
}
if ('validated' == $page['filter'])
{
  $where_clauses[] = 'validated=\'true\'';
}

$query = '
SELECT
    c.id,
    c.image_id,
    c.date,
    c.author,
    '.$conf['user_fields']['username'].' AS username,
    ui.status,
    c.content,
    i.path,
    i.representative_ext,
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
  LIMIT '.$page['start'].', '.$conf['comments_page_nb_comments'].'
;';
$result = pwg_query($query);
while ($row = pwg_db_fetch_assoc($result))
{
  $thumb = DerivativeImage::thumb_url(
      array(
        'id'=>$row['image_id'],
        'path'=>$row['path'],
        'representative_ext'=>$row['representative_ext'],
        )
     );
  if (empty($row['author_id']))
  {
    $author_name = $row['author'];
  }
  else
  {
    $author_name = stripslashes($row['username']);
  }
  $template->append(
    'comments',
    array(
      'U_PICTURE' => get_root_url().'admin.php?page=photo-'.$row['image_id'],
      'ID' => $row['id'],
      'TN_SRC' => $thumb,
      'AUTHOR' => trigger_change('render_comment_author', $author_name),
      'AUTHOR_STATUS' => $row['status'],
      'DATE' => format_date($row['date'], array('day_name','day','month','year','time')),
      'CONTENT' => trigger_change('render_comment_content',$row['content']),
      'IS_PENDING' => ('false' == $row['validated']),
      'IP' => $row['anonymous_id'],
      )
    );

  $list[] = $row['id'];
}

// +-----------------------------------------------------------------------+
// |                            navigation bar                             |
// +-----------------------------------------------------------------------+

$navbar = create_navigation_bar(
  get_root_url().'admin.php'.get_query_string_diff(array('start')),
  ('pending' == $page['filter'] ? $nb_pending : $nb_total),
  $page['start'],
  $conf['comments_page_nb_comments']
  );

$template->assign('navbar', $navbar);
$template->assign('ADMIN_PAGE_TITLE', l10n('User comments'));

// +-----------------------------------------------------------------------+
// |                           sending html code                           |
// +-----------------------------------------------------------------------+

$template->assign_var_from_handle('ADMIN_CONTENT', 'comments');

?>
