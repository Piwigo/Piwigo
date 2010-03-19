<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 Piwigo team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

/*
Plugin Name: Event tracer
Version: 2.0.3
Description: For developers. Shows all calls to trigger_event.
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=288
Author: Piwigo team
Author URI: http://piwigo.org
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

class EventTracer
{
  var $my_config;
  var $trigger_counts = array();

  function EventTracer()
  {
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
      $this->my_config['show_registered'] = true;
      $this->save_config();
    }
  }

  function save_config()
  {
    $dir = $this->get_config_file_dir();
    @mkgetdir($dir);
    $file = fopen( $dir.$this->get_config_file_name(), 'w' );
    fwrite($file, serialize($this->my_config) );
    fclose( $file );
  }

  function on_page_tail()
  {
    global $debug;
    if (@$this->my_config['show_registered'])
    {
      global $pwg_event_handlers;
      $out = '';
      foreach ($pwg_event_handlers as $event => $prio_array)
      {
        $out .= $event.' '.intval(@$this->trigger_counts[$event])." calls\n";
        foreach ($prio_array as $prio => $handlers)
        {
          foreach ($handlers as $handler)
          {
            $out .= "\t$prio ";
            if ( is_array($handler['function']) )
            {
              if ( is_string($handler['function'][0]) )
                $out .= $handler['function'][0].'::';
              else
                $out .= @get_class($handler['function'][0]).'->';
              $out .= $handler['function'][1];
            }
            else
             $out .= $handler['function'];
            $out .= "\n";
          }
        }
        $out .= "\n";
      }
      $debug .= '<pre>'.$out.'</pre>';
    }
    if (@$this->my_config['show_included_files'])
    {
      $debug .= "<pre><em>Included files</em>\n".var_export( get_included_files(), true ).'</pre>';
    }
  }

  function on_trigger($event_info)
  {
    if ($event_info['type']!='post_event')
      @$this->trigger_counts[$event_info['event']]++;

    foreach( $this->my_config['filters'] as $filter)
    {
      if ( preg_match( '/'.$filter.'/', $event_info['event'] ) )
      {
        if (@$this->my_config['show_args'])
        {
          $s = '<pre>';
          $s .= htmlspecialchars( var_export( $event_info['data'], true ) );
          $s .= '</pre>';
        }
        else
          $s = '';
        pwg_debug($event_info['type'].' "'.$event_info['event'].'" '.($this->trigger_counts[$event_info['event']]).' calls '.($s) );
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
add_event_handler('loc_begin_page_tail', array(&$obj, 'on_page_tail') );
add_event_handler('trigger', array(&$obj, 'on_trigger') );
set_plugin_data($plugin['id'], $obj);
?>
