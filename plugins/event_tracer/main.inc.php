<?php
/*
Plugin Name: Event tracer
Version: 1.0
Description: For developers. Shows all calls to trigger_event.
Plugin URI: http://www.phpwebgallery.net
*/
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

class EventTracer
{
  var $me_working;
  var $my_config;

  function EventTracer()
  {
    $this->me_working=0;
  }

  function load_config()
  {
    $x = @file_get_contents( dirname(__FILE__).'/data.dat' );
    if ($x!==false)
    {
      $c = unserialize($x);
      // do some more tests here
      $this->my_config = $c;
    }
    if ( !isset($this->my_config)
        or empty($this->my_config['filters']) )
    {
      $this->my_config['filters'] = array( '.*' );
      $this->my_config['show_args'] = false;
      $this->save_config();
    }
  }

  function save_config()
  {
    $file = fopen( dirname(__FILE__).'/data.dat', 'w' );
    fwrite($file, serialize($this->my_config) );
    fclose( $file );
  }

  function on_pre_trigger_event($event_info)
  {
    $this->dump('pre_trigger_event', $event_info);
  }
  function on_post_trigger_event($event_info)
  {
    $this->dump('post_trigger_event', $event_info);
  }

  function on_trigger_action($event_info)
  {
    $this->dump('trigger_action', $event_info);
  }

  function dump($event, $event_info)
  {
    foreach( $this->my_config['filters'] as $filter)
    {
      if ( preg_match( '/'.$filter.'/', $event_info['event'] ) )
      {
        if ($this->my_config['show_args'])
        {
          $s = '<pre>';
          $s .= htmlspecialchars( var_export( $event_info['data'], true ) );
          $s .= '</pre>';
        }
        else
          $s = '';
        pwg_debug($event.' "'.$event_info['event'].'" '.($s) );
        break;
      }
    }
  }

  function plugin_admin_menu()
  {
    add_plugin_admin_menu( "Event Tracer", array(&$this, 'do_admin') );
  }

  function do_admin($my_url)
  {
    include( dirname(__FILE__).'/tracer_admin.php' );
  }

}

$eventTracer = new EventTracer();
$eventTracer->load_config();

add_event_handler('plugin_admin_menu', array(&$eventTracer, 'plugin_admin_menu') );
add_event_handler('pre_trigger_event', array(&$eventTracer, 'on_pre_trigger_event') );
add_event_handler('post_trigger_event', array(&$eventTracer, 'on_post_trigger_event') );
add_event_handler('trigger_action', array(&$eventTracer, 'on_trigger_action') );
?>