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
    $x = @file_get_contents( dirname(__FILE__).'/tracer.dat' );
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
    $file = fopen( dirname(__FILE__).'/tracer.dat', 'w' );
    fwrite($file, serialize($this->my_config) );
    fclose( $file );
  }

  function pre_trigger_event($event_info)
  {
    if (!$this->me_working)
    {
      foreach( $this->my_config['filters'] as $filter)
      {
        if ( preg_match( '/'.$filter.'/', $event_info['event'] ) )
        {
          if ($this->my_config['show_args'])
            $s = var_export( $event_info['data'], true );
          else
            $s = '';
          pwg_debug('begin trigger_event "'.$event_info['event'].'" '.htmlspecialchars($s) );
          break;
        }
      }
    }
  }

  /*function post_trigger_event($filter_info)
  {
    if (!$this->me_working)
    {
      $s = var_export( $filter_info['data'], true );
      pwg_debug('end trigger_event '.$filter_info['event'].' '.$s );
    }
  }*/

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
add_event_handler('pre_trigger_event', array(&$eventTracer, 'pre_trigger_event') );
?>