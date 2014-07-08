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

/** Tour sended via $_POST or $_GET**/
if ( isset($_REQUEST['submited_tour']) and defined('IN_ADMIN') and IN_ADMIN )
{
  check_pwg_token();
  pwg_set_session_var('tour_to_launch', $_REQUEST['submited_tour']);
  global $TAT_restart;
  $TAT_restart=true;
}
elseif ( isset($_GET['tour_ended']) and defined('IN_ADMIN') and IN_ADMIN )
{
  pwg_unset_session_var('tour_to_launch');
}

/** Setup the tour **/
/*
 * REMOVE FOR RELEASE
$version_=str_replace('.','_',PHPWG_VERSION);
if (pwg_get_session_var('tour_to_launch')!=$version_ and isset($_GET['page']) and $_GET['page']=="plugin-TakeATour")
{ 
  pwg_unset_session_var('tour_to_launch');
}
else*/if ( pwg_get_session_var('tour_to_launch') )
{
  add_event_handler('init', 'TAT_tour_setup');
  include('tours/'.pwg_get_session_var('tour_to_launch').'/config.inc.php');
}

function TAT_tour_setup()
{
  global $template, $TAT_restart, $conf;
  $tour_to_launch=pwg_get_session_var('tour_to_launch');
  load_language('plugin.lang', PHPWG_PLUGINS_PATH .'TakeATour/', array('force_fallback'=>'en_UK'));
  $template->set_filename('TAT_js_css', PHPWG_PLUGINS_PATH.'TakeATour/tpl/js_css.tpl');
  $template->assign(
  array(
    'ADMIN_THEME'    => $conf['admin_theme'],
    )
  );
  $template->parse('TAT_js_css');
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
  $replacement = '<div class="bigButton"><a href="'.get_root_url().'admin.php?submited_tour=first_contact&pwg_token='.get_pwg_token().'">{\'I want to discover my gallery and add photos\'|@translate}</a></div>
<div class="bigButton"><a href="{$next_step_url}">{\'I want to add photos\'|@translate}</a></div>';
  return(str_replace($search, $replacement, $content));
}

/** After a Piwigo Update **/
add_event_handler('list_check_integrity', 'TAT_prompt'); 
function TAT_prompt($c13y) 
{ 
  global $page;
  $version_=str_replace('.','_',PHPWG_VERSION);
  if (file_exists('tours/'.$version_.'/config.inc.php'))
  {
    $page['infos'][] = '<a href="'.get_root_url().'admin.php?submited_tour='.$version_.'&pwg_token='.get_pwg_token().'">'.l10n('Discover what is new in the version %s of Piwigo', PHPWG_VERSION).'</a>';
  }
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