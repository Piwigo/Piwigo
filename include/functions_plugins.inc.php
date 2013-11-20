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

/**
 * @package functions\plugins
 */


/** base directory of plugins */
define('PHPWG_PLUGINS_PATH', PHPWG_ROOT_PATH.'plugins/');
/** default priority for plugins handlers */
define('EVENT_HANDLER_PRIORITY_NEUTRAL', 50);


/**
 * Used to declare maintenance methods of a plugin.
 */
abstract class PluginMaintain 
{
  /** @var string $plugin_id */
  protected $plugin_id;

  /**
   * @param string $id
   */
  function __construct($id)
  {
    $this->plugin_id = $id;
  }

  /**
   * @param string $plugin_version
   * @param array $errors - used to return error messages
   */
  abstract function install($plugin_version, &$errors=array());

  /**
   * @param string $plugin_version
   * @param array $errors - used to return error messages
   */
  abstract function activate($plugin_version, &$errors=array());

  abstract function deactivate();

  abstract function uninstall();

  /**
   * Tests if the plugin needs to be updated and call an update function
   *
   * @param string $version version exposed by the plugin (potentially new)
   * @param string $on_update name of a method to call when an update is needed
   *          it receives the previous version as first parameter
   */
  function autoUpdate($version, $on_update=null)
  {
    global $pwg_loaded_plugins;
    
    $current_version = $pwg_loaded_plugins[$this->plugin_id]['version'];
    
    if ( $version == 'auto' or $current_version == 'auto'
        or version_compare($current_version, $version, '<')
      )
    {
      if (!empty($on_update))
      {
        call_user_func(array(&$this, $on_update), $current_version);
      }
      
      if ($version != 'auto')
      {
        $query = '
UPDATE '. PLUGINS_TABLE .'
  SET version = "'. $version .'"
  WHERE id = "'. $this->plugin_id .'"
;';
        pwg_query($query);
        
        $pwg_loaded_plugins[$this->plugin_id]['version'] = $version;
      }
    }
  }
}

/**
 * Used to declare maintenance methods of a theme.
 */
abstract class ThemeMaintain 
{
  /** @var string $theme_id */
  protected $theme_id;

  /**
   * @param string $id
   */
  function __construct($id)
  {
    $this->theme_id = $id;
  }

  /**
   * @param string $theme_version
   * @param array $errors - used to return error messages
   */
  abstract function activate($theme_version, &$errors=array());

  abstract function deactivate();

  abstract function delete();
  
  /**
   * Tests if the theme needs to be updated and call an update function
   *
   * @param string $version version exposed by the theme (potentially new)
   * @param string $on_update name of a method to call when an update is needed
   *          it receives the previous version as first parameter
   */
  function autoUpdate($version, $on_update=null)
  {
    $query = '
SELECT version
  FROM '. THEMES_TABLE .'
  WHERE id = "'. $this->theme_id .'"
;';
    list($current_version) = pwg_db_fetch_row(pwg_query($query));
    
    if ( $version == 'auto' or $current_version == 'auto'
        or version_compare($current_version, $version, '<')
      )
    {
      if (!empty($on_update))
      {
        call_user_func(array(&$this, $on_update), $current_version);
      }
      
      if ($version != 'auto')
      {
        $query = '
UPDATE '. THEMES_TABLE .'
  SET version = "'. $version .'"
  WHERE id = "'. $this->theme_id .'"
;';
        pwg_query($query);
      }
    }
  }
}


/**
 * Register an event handler.
 *
 * @param string $event the name of the event to listen to
 * @param Callable $func the callback function
 * @param int $priority greater priority will be executed at last
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

/**
 * Removes an event handler.
 * @see add_event_handler()
 *
 * @param string $event
 * @param Callable $func
 * @param int $priority
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

/**
 * Triggers a modifier event and calls all registered event handlers.
 * trigger_change() is used as a modifier: it allows to transmit _$data_
 * through all handlers, thus each handler MUST return a value,
 * optional _$args_ are not transmitted.
 *
 * @since 2.6
 * @todo remove trigger_event()
 *
 * @param string $event
 * @param mixed $data data to transmit to all handlers
 * @param mixed $args,... optional arguments
 * @return mixed $data 
 */
function trigger_change($event, $data=null)
{
  return call_user_func_array('trigger_event', func_get_args());
}

/**
 * @deprecated 2.6
 * @see trigger_change
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

/**
 * Triggers a notifier event and calls all registered event handlers.
 * trigger_notify() is only used as a notifier, no modification of data is possible
 *
 * @since 2.6
 * @todo remove trigger_action()
 *
 * @param string $event
 * @param mixed $args,... optional arguments
 */
function trigger_notify($event)
{
  return call_user_func_array('trigger_action', func_get_args());
}

/**
 * @deprecated 2.6
 * @see trigger_notify
 */
function trigger_action($event)
{
  global $pwg_event_handlers;
  if ( isset($pwg_event_handlers['trigger']) and $event!='trigger' )
  {// special case for debugging - avoid recursive calls
    trigger_action('trigger',
        array('type'=>'action', 'event'=>$event, 'data'=>null) );
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

/**
 * Saves some data with the associated plugin id, data are only available
 * during script lifetime.
 * @depracted 2.6
 *
 * @param string $plugin_id
 * @param mixed $data
 * @return bool
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

/**
 * Retrieves plugin data saved previously with set_plugin_data.
 * @see set_plugin_data()
 * @depracted 2.6
 *
 * @param string $plugin_id
 * @return mixed
 */
function &get_plugin_data($plugin_id)
{
  global $pwg_loaded_plugins;
  if ( isset($pwg_loaded_plugins[$plugin_id]['plugin_data']) )
  {
    return $pwg_loaded_plugins[$plugin_id]['plugin_data'];
  }
  return null;
}

/**
 * Returns an array of plugins defined in the database.
 *
 * @param string $state optional filter
 * @param string $id returns only data about given plugin
 * @return array
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
    $plugins[] = $row;
  }
  return $plugins;
}

/**
 * Loads a plugin, it includes the main.inc.php file and updates _$pwg_loaded_plugins_.
 *
 * @param string $plugin
 */
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

/**
 * Loads all the registered plugins.
 */
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

?>