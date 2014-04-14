<?php
/*
Plugin Name: Take A Tour of Your Piwigo
Version: 1.0
Description: Plugin Personnel
Plugin URI: http://piwigo.org
Author:Piwigo Team
Author URI:
*/
if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

$avalaible_tour = array('first_contact', 'privacy', 'picture_protection');

if ( isset($_POST['submited_tour']) and in_array($_POST['submited_tour'], $avalaible_tour) and defined('IN_ADMIN') and IN_ADMIN )
{
  check_pwg_token();
  pwg_set_session_var('tour_to_launch', $_POST['submited_tour']);
  global $TAT_restart;
  $TAT_restart=true;
}
elseif ( isset($_GET['tour_ended']) and in_array($_GET['tour_ended'], $avalaible_tour) and defined('IN_ADMIN') and IN_ADMIN )
{
  pwg_unset_session_var('tour_to_launch');
}

if (pwg_get_session_var('tour_to_launch') and isset($_GET['page']) and $_GET['page']=="plugin-TakeATour" )
{ 
  pwg_unset_session_var('tour_to_launch');
}
elseif ( pwg_get_session_var('tour_to_launch') )
{
  add_event_handler('init', 'TAT_add_js_css');
  include('tours/'.pwg_get_session_var('tour_to_launch').'/config.inc.php');
}

function TAT_add_js_css()
{
  global $template, $TAT_restart;
  $tour_to_launch=pwg_get_session_var('tour_to_launch');
  load_language('plugin.lang', PHPWG_PLUGINS_PATH .'TakeATour/');
  load_language('lang', PHPWG_ROOT_PATH.PWG_LOCAL_DIR, array('no_fallback'=>true, 'local'=>true) );
  $template->set_filename('TAT_js_css', PHPWG_PLUGINS_PATH.'TakeATour/tpl/js_css.tpl');
  $template->parse('TAT_js_css');//http://piwigo.org/forum/viewtopic.php?id=23248
  if (isset($TAT_restart) and $TAT_restart)
  {
    $TAT_restart=false;
    $template->assign('TAT_restart',true);
  }
  $tat_path=str_replace(basename($_SERVER['SCRIPT_NAME']),'', $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
  $template->assign('TAT_path', $tat_path);
  @include('tours/'.$tour_to_launch.'/config_preparse.inc.php');
  $template->set_filename('TAT_tour_tpl', PHPWG_PLUGINS_PATH.'TakeATour/tours/'.$tour_to_launch.'/tour.tpl');
  $template->parse('TAT_tour_tpl');
}


add_event_handler('loc_end_help','TAT_help');
function TAT_help()
{
  global $template;
  load_language('plugin.lang', PHPWG_PLUGINS_PATH .'TakeATour/');
  $template->set_prefilter('help', 'TAT_help_prefilter');
}
function TAT_help_prefilter($content, &$smarty)
{
  
  $search = '<div id="helpContent">';
  $replacement = '<div id="helpContent">
<fieldset>
<legend>{\'Visit your Piwigo!\'|@translate}</legend>
<p class="nextStepLink"><a href="admin.php?page=plugin-TakeATour">{\'Take a tour and discover the features of your Piwigo gallery » Go to the available tours\'|@translate}</a></p>
</fieldset>';
  return(str_replace($search, $replacement, $content));

}
add_event_handler('loc_end_no_photo_yet','TAT_no_photo_yet');
function TAT_no_photo_yet()
{
  global $template;
  load_language('plugin.lang', PHPWG_PLUGINS_PATH .'TakeATour/');
  $template->set_prefilter('no_photo_yet', 'TAT_no_photo_yet_prefilter');
  $template->assign(
  array(
    'F_ACTION' => get_root_url().'admin.php',
    'pwg_token' => get_pwg_token()
    )
  );
}
function TAT_no_photo_yet_prefilter($content, &$smarty)
{
  
  $search = '<div class="bigButton"><a href="{$next_step_url}">{\'I want to add photos\'|@translate}</a></div>';
  $replacement = '<form style="text-align:center" action="{$F_ACTION}" method="post">
  <input type="hidden" name="submited_tour" value="first_contact">
  <input type="hidden" name="pwg_token" value="{$pwg_token}">
  <input type="submit" name="button2" id="button2" value="{\'I want to discover my gallery and add photos\'|@translate}">
</form>
<div class="bigButton"><a href="{$next_step_url}">{\'I want to add photos\'|@translate}</a></div>';
  $content=str_replace($search, $replacement, $content);
  $search = '</style>';
  $replacement = '
form input[type="submit"] {
  font-size: 25px;
  letter-spacing: 2px;
  margin: 0 5px;
  padding: 20px;
  border:none;
  background-color:#666666;
  color:#fff;
  cursor:pointer;
}
form input[type="submit"]:hover {
  background-color:#ff7700;
  color:white;
}
</style>';
  return(str_replace($search, $replacement, $content));
}

add_event_handler('get_admin_plugin_menu_links', 'TAT_admin_menu' );
function TAT_admin_menu($menu)
{
  array_push($menu, array(
    'NAME' => 'Take a Tour',
    'URL' => get_root_url().'admin.php?page=plugin-TakeATour'
    )
  );
  return $menu;
}
?>