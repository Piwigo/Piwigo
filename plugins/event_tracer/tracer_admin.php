<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $template;
$template->set_filenames( array('plugin_admin_content' => dirname(__FILE__).'/tracer_admin.tpl') );

if ( isset($_POST['eventTracer_filters']) )
{
  $v = $_POST['eventTracer_filters'];
  $v = str_replace( "\r\n", "\n", $v );
  $v = str_replace( "\n\n", "\n", $v );
  $this->my_config['filters'] = explode("\n", $v);
  $this->my_config['show_args'] = isset($_POST['eventTracer_show_args']);
  $this->save_config();
  global $page;
  array_push($page['infos'], 'event tracer options saved');
}
$template->assign_var('EVENT_TRACER_FILTERS', implode("\n", $this->my_config['filters'] ) );
$template->assign_var('EVENT_TRACER_SHOW_ARGS', $this->my_config['show_args'] ? 'checked="checked"' : '' );
$template->assign_var('EVENT_TRACER_F_ACTION', $my_url);

$template->assign_var_from_handle( 'PLUGIN_ADMIN_CONTENT', 'plugin_admin_content');
?>