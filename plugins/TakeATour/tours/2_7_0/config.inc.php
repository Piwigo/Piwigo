<?php
/**********************************
 * REQUIRED PATH TO THE TPL FILE */

$TOUR_PATH = PHPWG_PLUGINS_PATH.'TakeATour/tours/2_7_0/tour.tpl';

/*********************************/


/**********************
 *    Preparse part   *
 **********************/
  $template->assign('TAT_index', make_index_url(array('section' => 'categories')));
  $template->assign('TAT_search', get_root_url().'search.php');

  //picture id
  if (isset($_GET['page']) and preg_match('/^photo-(\d+)(?:-(.*))?$/', $_GET['page'], $matches))
  {
    $_GET['image_id'] = $matches[1];
  }
  check_input_parameter('image_id', $_GET, false, PATTERN_ID);
  if (isset($_GET['image_id']) and pwg_get_session_var('TAT_image_id')==null)
  {
    $template->assign('TAT_image_id', $_GET['image_id']);
    pwg_set_session_var('TAT_image_id', $_GET['image_id']);
  }
  elseif (is_numeric(pwg_get_session_var('TAT_image_id')))
  {
    $template->assign('TAT_image_id', pwg_get_session_var('TAT_image_id'));
  }
  else
  {
    $query = '
    SELECT id
      FROM '.IMAGES_TABLE.'
      ORDER BY RAND()
      LIMIT 1  
    ;';
    $row = pwg_db_fetch_assoc(pwg_query($query));
    $template->assign('TAT_image_id', $row['id']);
  }
?>