<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

/**
 * This file is included by the main page to show thumbnails for the default
 * case
 *
 */

$pictures = array();

$selection = array_slice(
  $page['items'],
  $page['start'],
  $page['nb_image_page']
  );

$selection = trigger_change('loc_index_thumbnails_selection', $selection);

if (count($selection) > 0)
{
  $rank_of = array_flip($selection);

  $query = '
SELECT *
  FROM '.IMAGES_TABLE.'
  WHERE id IN ('.implode(',', $selection).')
;';
  $result = pwg_query($query);
  while ($row = pwg_db_fetch_assoc($result))
  {
    $row['rank'] = $rank_of[ $row['id'] ];
    $pictures[] = $row;
  }

  usort($pictures, 'rank_compare');
  unset($rank_of);
}

if (count($pictures) > 0)
{
  // define category slideshow url
  $row = reset($pictures);
  $page['cat_slideshow_url'] =
    add_url_params(
      duplicate_picture_url(
        array(
          'image_id' => $row['id'],
          'image_file' => $row['file']
        ),
        array('start')
      ),
      array('slideshow' =>
        (isset($_GET['slideshow']) ? $_GET['slideshow']
                                   : '' ))
    );

  if ($conf['activate_comments'] and $user['show_nb_comments'])
  {
    $query = '
SELECT image_id, COUNT(*) AS nb_comments
  FROM '.COMMENTS_TABLE.'
  WHERE validated = \'true\'
    AND image_id IN ('.implode(',', $selection).')
  GROUP BY image_id
;';
    $nb_comments_of = query2array($query, 'image_id', 'nb_comments');
  }
}

// template thumbnail initialization
$template->set_filenames( array( 'index_thumbnails' => 'thumbnails.tpl',));

trigger_notify('loc_begin_index_thumbnails', $pictures);
$tpl_thumbnails_var = array();

foreach ($pictures as $row)
{
  // link on picture.php page
  $url = duplicate_picture_url(
        array(
          'image_id' => $row['id'],
          'image_file' => $row['file']
        ),
        array('start')
      );

  if (isset($nb_comments_of))
  {
    $row['NB_COMMENTS'] = $row['nb_comments'] = (int)@$nb_comments_of[$row['id']];
  }

  $name = render_element_name($row);
  $desc = render_element_description($row, 'main_page_element_description');

  $tpl_var = array_merge( $row, array(
    'TN_ALT' => htmlspecialchars(strip_tags($name)),
    'TN_TITLE' => get_thumbnail_title($row, $name, $desc),
    'URL' => $url,
    'DESCRIPTION' => $desc,
    'src_image' => new SrcImage($row),
    'path_ext' => strtolower(get_extension($row['path'])),
    'file_ext' => strtolower(get_extension($row['file'])),
    ) );

  if ($conf['index_new_icon'])
  {
    $tpl_var['icon_ts'] = get_icon($row['date_available']);
  }

  if ($user['show_nb_hits'])
  {
    $tpl_var['NB_HITS'] = $row['hit'];
  }

  switch ($page['section'])
  {
    case 'best_rated' :
    {
      $name = '('.$row['rating_score'].') '.$name;
      break;
    }
    case 'most_visited' :
    {
      if ( !$user['show_nb_hits'])
      {
        $name = '('.$row['hit'].') '.$name;
      }
      break;
    }
  }
  $tpl_var['NAME'] = $name;
  $tpl_thumbnails_var[] = $tpl_var;
}

$template->assign( array(
  'derivative_params' => trigger_change('get_index_derivative_params', ImageStdParams::get_by_type( pwg_get_session_var('index_deriv', IMG_THUMB) ) ),
  'maxRequests' =>$conf['max_requests'],
  'SHOW_THUMBNAIL_CAPTION' =>$conf['show_thumbnail_caption'],
    ) );
$tpl_thumbnails_var = trigger_change('loc_end_index_thumbnails', $tpl_thumbnails_var, $pictures);
$template->assign('thumbnails', $tpl_thumbnails_var);

$template->assign_var_from_handle('THUMBNAILS', 'index_thumbnails');
unset($pictures, $selection, $tpl_thumbnails_var);
$template->clear_assign( 'thumbnails' );
pwg_debug('end include/category_default.inc.php');
?>