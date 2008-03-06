<?php /*
Plugin Name: Event tracer
Version: 1.8.a
Description: For developers. Shows all calls to trigger_event.
Plugin URI: http://www.phpwebgallery.net
Author: PhpWebGallery team
Author URI: http://www.phpwebgallery.net
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

  function get_config_file_dir()
  {
    global $conf;
    return $conf['local_data_dir'].'/plugins/';
  }

  function get_config_file_name()
  {
    return basename(dirname(__FILE__)).'.dat';
  }

  function load_config()
  {
    $x = @file_get_contents( $this->get_config_file_dir().$this->get_config_file_name() );
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
    $dir = $this->get_config_file_dir();
    @mkdir($dir);
    $file = fopen( $dir.$this->get_config_file_name(), 'w' );
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

  function plugin_admin_menu($menu)
  {
    array_push($menu,
        array(
          'NAME' => 'Event Tracer',
          'URL' => get_admin_plugin_menu_link(dirname(__FILE__).'/tracer_admin.php')
        )
      );
    return $menu;
  }
}

$obj = new EventTracer();
$obj->load_config();

add_event_handler('get_admin_plugin_menu_links', array(&$obj, 'plugin_admin_menu') );
add_event_handler('pre_trigger_event', array(&$obj, 'on_pre_trigger_event') );
add_event_handler('post_trigger_event', array(&$obj, 'on_post_trigger_event') );
add_event_handler('trigger_action', array(&$obj, 'on_trigger_action') );
set_plugin_data($plugin['id'], $obj);
?>
