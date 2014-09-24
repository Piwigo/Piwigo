<?php
/*
Plugin Name: Take A Tour of Your Piwigo
Version: 2.7.0
Description: Visit your Piwigo to discover its features. This plugin has multiple thematic tours for beginners and advanced users.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=776
Author:Piwigo Team
Author URI: http://piwigo.org
*/
if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

/** Tour sended via $_POST or $_GET**/
if ( isset($_REQUEST['submited_tour_path']) and defined('IN_ADMIN') and IN_ADMIN )
{
  check_pwg_token();
  pwg_set_session_var('tour_to_launch', $_REQUEST['submited_tour_path']);
  global $TAT_restart;
  $TAT_restart=true;
}
elseif ( isset($_GET['tour_ended']) and defined('IN_ADMIN') and IN_ADMIN )
{
  pwg_unset_session_var('tour_to_launch');
}

/** Setup the tour **/
/*
 * CHANGE FOR RELEASE
$version_=str_replace('.','_',PHPWG_VERSION);*/
$version_="2_7_0";
/***/
if (pwg_get_session_var('tour_to_launch')!='tours/'.$version_ and isset($_GET['page']) and $_GET['page']=="plugin-TakeATour")
{ 
  pwg_unset_session_var('tour_to_launch');
}
elseif ( pwg_get_session_var('tour_to_launch') )
{
  add_event_handler('init', 'TAT_tour_setup');
}

function TAT_tour_setup()
{
  global $template, $TAT_restart, $conf;
  $tour_to_launch=pwg_get_session_var('tour_to_launch');
  load_language('plugin.lang', PHPWG_PLUGINS_PATH .'TakeATour/', array('force_fallback'=>'en_UK'));
  
  list(, $tour_name) = explode('/', $tour_to_launch);
  load_language('tour_'.$tour_name.'.lang', PHPWG_PLUGINS_PATH .'TakeATour/', array('force_fallback'=>'en_UK'));

  $template->set_filename('TAT_js_css', PHPWG_PLUGINS_PATH.'TakeATour/tpl/js_css.tpl');
  $template->assign('ADMIN_THEME', $conf['admin_theme']);
  $template->parse('TAT_js_css');

  if (isset($TAT_restart) and $TAT_restart)
  {
    $TAT_restart=false;
    $template->assign('TAT_restart',true);
  }
  $tat_path=str_replace(basename($_SERVER['SCRIPT_NAME']),'', $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
  $template->assign('TAT_path', $tat_path);
  $template->assign('ABS_U_ADMIN', get_absolute_root_url());// absolute one due to public pages and $conf['question_mark_in_urls'] = false+$conf['php_extension_in_urls'] = false;
  include($tour_to_launch.'/config.inc.php');
  $template->set_filename('TAT_tour_tpl', $TOUR_PATH);
  $template->parse('TAT_tour_tpl');
}

/** Add link in Help pages **/
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

/** Add link in no_photo_yet **/
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
  $replacement = '<div class="bigButton"><a href="{$F_ACTION}?submited_tour_path=tours/first_contact&pwg_token={$pwg_token}">{\'Start the Tour\'|@translate}</a></div>';
  return(str_replace($search, $replacement, $content));
}

/** Add admin menu link **/
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