<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2013 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
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
Events and event handlers are the core of Piwigo plugin management.
Plugins are addons that are found in plugins subdirectory. If activated, PWG
will include the index.php of each plugin.
Events are triggered by PWG core code. Plugins (or even PWG itself) can
register their functions to handle these events. An event is identified by a
string.
*/

define('PHPWG_PLUGINS_PATH', PHPWG_ROOT_PATH.'plugins/');

define('EVENT_HANDLER_PRIORITY_NEUTRAL', 50);

/* Register a event handler.
 * @param string $event the name of the event to listen to
 * @param mixed $func the function that will handle the event
 * @param int $priority optional priority (greater priority will
 * be executed at last)
*/
function add_event_handler($event, $func,
    $priority=EVENT_HANDLER_PRIORITY_NEUTRAL, $accepted_args=1)
{
  global $pwg_event_handlers;

  if ( isset($pwg_event_handlers[$event][$priority]) )
  {
    foreach($pwg_event_handlers[$event][$priority] as $handler)
    {
      if ( $handler['function'] == $func )
      {
        return false;
      }
    }
  }

  $pwg_event_handlers[$event][$priority][] =
    array(
      'function'=>$func,
      'accepted_args'=>$accepted_args);
  ksort( $pwg_event_handlers[$event] );
  return true;
}

/* Register a event handler.
 * @param string $event the name of the event to listen to
 * @param mixed $func the function that needs removal
 * @param int $priority optional priority (greater priority will
 * be executed at last)
*/
function remove_event_handler($event, $func,
   $priority=EVENT_HANDLER_PRIORITY_NEUTRAL)
{
  global $pwg_event_handlers;

  if (!isset( $pwg_event_handlers[$event][$priority] ) )
  {
    return false;
  }
  for ($i=0; $i<count($pwg_event_handlers[$event][$priority]); $i++)
  {
    if ($pwg_event_handlers[$event][$priority][$i]['function']==$func)
    {
      unset($pwg_event_handlers[$event][$priority][$i]);
      $pwg_event_handlers[$event][$priority] =
        array_values($pwg_event_handlers[$event][$priority]);

      if ( empty($pwg_event_handlers[$event][$priority]) )
      {
        unset( $pwg_event_handlers[$event][$priority] );
        if (empty( $pwg_event_handlers[$event] ) )
        {
          unset( $pwg_event_handlers[$event] );
        }
      }
      return true;
    }
  }
  return false;
}

/* Triggers an event and calls all registered event handlers
 * @param string $event name of the event
 * @param mixed $data data to pass to handlers
*/
function trigger_event($event, $data=null)
{
  global $pwg_event_handlers;

  if ( isset($pwg_event_handlers['trigger']) )
  {// just for debugging
    trigger_action('trigger',
        array('type'=>'event', 'event'=>$event, 'data'=>$data) );
  }

  if ( !isset($pwg_event_handlers[$event]) )
  {
    return $data;
  }
  $args = func_get_args();

  foreach ($pwg_event_handlers[$event] as $priority => $handlers)
  {
    foreach($handlers as $handler)
    {
      $function_name = $handler['function'];
      $accepted_args = $handler['accepted_args'];
      $args[1] = $data;
      $data = call_user_func_array($function_name, array_slice($args,1,$accepted_args) );
    }
  }
  trigger_action('trigger',
       array('type'=>'post_event', 'event'=>$event, 'data'=>$data) );
  return $data;
}

function trigger_action($event, $data=null)
{
  global $pwg_event_handlers;
  if ( isset($pwg_event_handlers['trigger']) and $event!='trigger' )
  {// special case for debugging - avoid recursive calls
    trigger_action('trigger',
        array('type'=>'action', 'event'=>$event, 'data'=>$data) );
  }

  if ( !isset($pwg_event_handlers[$event]) )
  {
    return;
  }
  $args = func_get_args();

  foreach ($pwg_event_handlers[$event] as $priority => $handlers)
  {
    foreach($handlers as $handler)
    {
      $function_name = $handler['function'];
      $accepted_args = $handler['accepted_args'];

      call_user_func_array($function_name, array_slice($args,1,$accepted_args) );
    }
  }
}

/** Saves some data with the associated plugim id. It can be retrieved later (
 * during this script lifetime) using get_plugin_data
 * @param string plugin_id
 * @param mixed data
 * returns true on success, false otherwise
 */
function set_plugin_data($plugin_id, &$data)
{
  global $pwg_loaded_plugins;
  if ( isset($pwg_loaded_plugins[$plugin_id]) )
  {
    $pwg_loaded_plugins[$plugin_id]['plugin_data'] = &$data;
    return true;
  }
  return false;
}

/** Retrieves plugin data saved previously with set_plugin_data
 * @param string plugin_id
 */
function &get_plugin_data($plugin_id)
{
  global $pwg_loaded_plugins;
  if ( isset($pwg_loaded_plugins[$plugin_id]) )
  {
    return $pwg_loaded_plugins[$plugin_id]['plugin_data'];
  }
  return null;
}

/* Returns an array of plugins defined in the database
 * @param string $state optional filter on this state
 * @param string $id optional returns only data about given plugin
*/
function get_db_plugins($state='', $id='')
{
  $query = '
SELECT * FROM '.PLUGINS_TABLE;
  $clauses = array();
  if (!empty($state))
  {
    $clauses[] = 'state=\''.$state.'\'';
  }
  if (!empty($id))
  {
    $clauses[] = 'id="'.$id.'"';
  }
  if (count($clauses))
  {
      $query .= '
  WHERE '. implode(' AND ', $clauses);
  }

  $result = pwg_query($query);
  $plugins = array();
  while ($row = pwg_db_fetch_assoc($result))
  {
    array_push($plugins, $row);
  }
  return $plugins;
}


function load_plugin($plugin)
{
  $file_name = PHPWG_PLUGINS_PATH.$plugin['id'].'/main.inc.php';
  if ( file_exists($file_name) )
  {
    global $pwg_loaded_plugins;
    $pwg_loaded_plugins[ $plugin['id'] ] = $plugin;
    include_once( $file_name );
  }
}

/*loads all the plugins on startup*/
function load_plugins()
{
  global $conf, $pwg_loaded_plugins;
  $pwg_loaded_plugins = array();
  if ($conf['enable_plugins'])
  {
    $plugins = get_db_plugins('active');
    foreach( $plugins as $plugin)
    {// include main from a function to avoid using same function context
      load_plugin($plugin);
    }
    trigger_action('plugins_loaded');
  }
}

/*
 * test if a plugin needs to be updated and call a update function
 * @param: string $plugin_id, id of the plugin as seen in PLUGINS_TABLE and $pwg_loaded_plugins
 * @param: string $version, version exposed by the plugin
 * @param: callable $on_update, function to call when and update is needed
 *          it receives the previous version as first parameter
 */
function request_plugin_update($plugin_id, $version, $on_update)
{
  global $pwg_loaded_plugins;
  
  if (
    $version == 'auto' or
    $pwg_loaded_plugins[$plugin_id]['version'] == 'auto' or
    version_compare($pwg_loaded_plugins[$plugin_id]['version'], $version, '<')
  )
  {
    // call update function
    if (!empty($on_update))
    {
      call_user_func($on_update, $pwg_loaded_plugins[$plugin_id]['version']);
    }
    
    // update plugin version in database
    if ($version != 'auto')
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. $version .'"
WHERE id = "'. $plugin_id .'"';
      pwg_query($query);
      
      $pwg_loaded_plugins[$plugin_id]['version'] = $version;
    }
  }
}

?>