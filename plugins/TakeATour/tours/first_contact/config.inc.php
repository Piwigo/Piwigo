<?php
/**********************************
 * REQUIRED PATH TO THE TPL FILE */

$TOUR_PATH = PHPWG_PLUGINS_PATH.'TakeATour/tours/first_contact/tour.tpl';

/*********************************/

if ( defined('IN_ADMIN') and IN_ADMIN )
{
/* first contact */
add_event_handler('loc_end_element_set_global', 'TAT_FC_14');
add_event_handler('loc_end_picture_modify', 'TAT_FC_16');
add_event_handler('loc_end_picture_modify', 'TAT_FC_17');
add_event_handler('loc_end_cat_modify', 'TAT_FC_23');
add_event_handler('loc_end_themes_installed', 'TAT_FC_35');
}

function TAT_FC_14()
{
  global $template;
  $template->set_prefilter('batch_manager_global', 'TAT_FC_14_prefilter');
}
function TAT_FC_14_prefilter ($content, &$smarty)
{
  $search = '<span class="wrap2';
  $replacement = '{counter print=false assign=TAT_FC_14}<span {if $TAT_FC_14==1}id="TAT_FC_14"{/if} class="wrap2';
  $content = str_replace($search, $replacement, $content);
  $search = 'target="_blank">{\'Edit\'';
  $replacement = '>{\'Edit\'';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_16()
{
  global $template;
  $template->set_prefilter('picture_modify', 'TAT_FC_16_prefilter');
}
function TAT_FC_16_prefilter ($content, &$smarty)
{
  $search = '<strong>{\'Linked albums\'|@translate}</strong>';
  $replacement = '<span id="TAT_FC_16"><strong>{\'Linked albums\'|@translate}</strong></span>';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_17()
{
  global $template;
  $template->set_prefilter('picture_modify', 'TAT_FC_17_prefilter');
}
function TAT_FC_17_prefilter ($content, &$smarty)
{
  $search = '<strong>{\'Representation of albums\'|@translate}</strong>';
  $replacement = '<span id="TAT_FC_17"><strong>{\'Representation of albums\'|@translate}</strong></span>';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_23()
{
  global $template;
  $template->set_prefilter('album_properties', 'TAT_FC_23_prefilter');
}
function TAT_FC_23_prefilter ($content, &$smarty)
{
  $search = '<strong>{\'Lock\'|@translate}</strong>';
  $replacement = '<span id="TAT_FC_23"><strong>{\'Lock\'|@translate}</strong></span>';
  return str_replace($search, $replacement, $content);
}
function TAT_FC_35()
{
  global $template;
  $template->set_prefilter('themes', 'TAT_FC_35_prefilter');
}
function TAT_FC_35_prefilter ($content, &$smarty)
{
  $search = '<a href="{$set_default_baseurl}{$theme.ID}" class="tiptip"';
  $replacement = '{counter print=false assign=TAT_FC_35}<a href="{$set_default_baseurl}{$theme.ID}" class="tiptip" {if $TAT_FC_35==1}id="TAT_FC_35"{/if}';
  return str_replace($search, $replacement, $content);
}

/**********************
 *    Preparse part   *
 **********************/
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
  //album id
  if (isset($_GET['page']) and preg_match('/^album-(\d+)(?:-(.*))?$/', $_GET['page'], $matches))
  {
    $_GET['cat_id'] = $matches[1];
  }
  check_input_parameter('cat_id', $_GET, false, PATTERN_ID);
  if (isset($_GET['cat_id']) and pwg_get_session_var('TAT_cat_id')==null)
  {
    $template->assign('TAT_cat_id', $_GET['cat_id']);
    pwg_set_session_var('TAT_cat_id', $_GET['cat_id']);
  }
  elseif (is_numeric(pwg_get_session_var('TAT_cat_id')))
  {
    $template->assign('TAT_cat_id', pwg_get_session_var('TAT_cat_id'));
  }
  else
  {
    $query = '
    SELECT id
      FROM '.CATEGORIES_TABLE.'
      ORDER BY RAND()
      LIMIT 1  
    ;';
    $row = pwg_db_fetch_assoc(pwg_query($query));
    $template->assign('TAT_cat_id', $row['id']);
  }
  global $conf;
  if ( isset($conf['enable_synchronization']) )
  {
    $template->assign('TAT_FTP', $conf['enable_synchronization']);
  }

?>