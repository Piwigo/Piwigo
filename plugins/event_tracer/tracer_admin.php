<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

$me = get_plugin_data($plugin_id);

global $template;
$template->set_filenames( array('plugin_admin_content' => dirname(__FILE__).'/tracer_admin.tpl') );

if ( isset($_POST['eventTracer_filters']) )
{
  $v = $_POST['eventTracer_filters'];
  $v = str_replace( "\r\n", "\n", $v );
  $v = str_replace( "\n\n", "\n", $v );
  $v = stripslashes($v);
  if (!empty($v))
    $me->my_config['filters'] = explode("\n", $v);
  else
    $me->my_config['filters'] = array();
  $me->my_config['show_args'] = isset($_POST['eventTracer_show_args']);
  $me->my_config['show_registered'] = isset($_POST['eventTracer_show_registered']);
  if (isset($_POST['eventTracer_show_included_files']) )
    $me->my_config['show_included_files'] = true;
  else
    unset($me->my_config['show_included_files']);
  $me->save_config();
  global $page;
  array_push($page['infos'], 'event tracer options saved');
}
$template->assign('EVENT_TRACER_FILTERS', implode("\n", $me->my_config['filters'] ) );
$template->assign('EVENT_TRACER_SHOW_ARGS', @$me->my_config['show_args'] ? 'checked="checked"' : '' );
$template->assign('U_LIST_EVENTS', get_admin_plugin_menu_link(dirname(__FILE__).'/event_list.php'));
$template->assign('EVENT_TRACER_SHOW_REGISTERED', @$me->my_config['show_registered'] ? 'checked="checked"' : '' );
$template->assign('EVENT_TRACER_SHOW_INCLUDED_FILES', @$me->my_config['show_included_files'] ? 'checked="checked"' : '' );

//$template->assign_var('EVENT_TRACER_F_ACTION', $my_url);

$template->assign_var_from_handle( 'ADMIN_CONTENT', 'plugin_admin_content');
?>
