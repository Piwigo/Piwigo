<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
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
class PluginMaintain
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
   * @param array &$errors - used to return error messages
   */
  function install($plugin_version, &$errors=array()) {}

  /**
   * @param string $plugin_version
   * @param array &$errors - used to return error messages
   */
  function activate($plugin_version, &$errors=array()) {}

  function deactivate() {}

  function uninstall() {}

  /**
   * @param string $old_version
   * @param string $new_version
   * @param array &$errors - used to return error messages
   */
  function update($old_version, $new_version, &$errors=array()) {}
  
  /**
   * @removed 2.7
   */
  function autoUpdate()
  {
    if (is_admin() && !defined('IN_WS'))
    {
      trigger_error('Function PluginMaintain::autoUpdate deprecated', E_USER_WARNING);
    }
  }
}

/**
 * Used to declare maintenance methods of a theme.
 */
class ThemeMaintain
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
   * @param array &$errors - used to return error messages
   */
  function activate($theme_version, &$errors=array()) {}

  function deactivate() {}

  function delete() {}
}


/**
 * Register an event handler.
 *
 * @param string $event the name of the event to listen to
 * @param Callable $func the callback function
 * @param int $priority greater priority will be executed at last
 * @param string $include_path file to include before executing the callback
 * @return bool false is handler already exists
 */
function add_event_handler($event, $func,
    $priority=EVENT_HANDLER_PRIORITY_NEUTRAL, $include_path=null)
{
  global $pwg_event_handlers;

  if (isset($pwg_event_handlers[$event][$priority]))
  {
    foreach ($pwg_event_handlers[$event][$priority] as $handler)
    {
      if ($handler['function'] == $func)
      {
        return false;
      }
    }
  }

  $pwg_event_handlers[$event][$priority][] = array(
    'function' => $func,
    'include_path' => is_string($include_path) ? $include_path : null,
    );

  ksort($pwg_event_handlers[$event]);
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

  if (!isset($pwg_event_handlers[$event][$priority]))
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

      if (empty($pwg_event_handlers[$event][$priority]))
      {
        unset($pwg_event_handlers[$event][$priority]);
        if (empty($pwg_event_handlers[$event]))
        {
          unset($pwg_event_handlers[$event]);
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
 *
 * @param string $event
 * @param mixed $data data to transmit to all handlers
 * @param mixed $args,... optional arguments
 * @return mixed $data
 */
function trigger_change($event, $data=null)
{
  global $pwg_event_handlers;

  if (isset($pwg_event_handlers['trigger']))
  {// debugging
    trigger_notify('trigger',
      array('type'=>'event', 'event'=>$event, 'data'=>$data)
      );
  }

  if (!isset($pwg_event_handlers[$event]))
  {
    return $data;
  }
  $args = func_get_args();
  array_shift($args);

  foreach ($pwg_event_handlers[$event] as $priority => $handlers)
  {
    foreach ($handlers as $handler)
    {
      $args[0] = $data;

      if (!empty($handler['include_path']))
      {
        include_once($handler['include_path']);
      }

      $data = call_user_func_array($handler['function'], $args);
    }
  }

  if (isset($pwg_event_handlers['trigger']))
  {// debugging
    trigger_notify('trigger',
      array('type'=>'post_event', 'event'=>$event, 'data'=>$data)
      );
  }

  return $data;
}

/**
 * Triggers a notifier event and calls all registered event handlers.
 * trigger_notify() is only used as a notifier, no modification of data is possible
 *
 * @since 2.6
 *
 * @param string $event
 * @param mixed $args,... optional arguments
 */
function trigger_notify($event)
{
  global $pwg_event_handlers;

  if (isset($pwg_event_handlers['trigger']) and $event!='trigger')
  {// debugging - avoid recursive calls
    trigger_notify('trigger',
      array('type'=>'action', 'event'=>$event, 'data'=>null)
      );
  }

  if (!isset($pwg_event_handlers[$event]))
  {
    return;
  }
  $args = func_get_args();
  array_shift($args);

  foreach ($pwg_event_handlers[$event] as $priority => $handlers)
  {
    foreach ($handlers as $handler)
    {
      if (!empty($handler['include_path']))
      {
        include_once($handler['include_path']);
      }

      call_user_func_array($handler['function'], $args);
    }
  }
}

/**
 * Saves some data with the associated plugin id, data are only available
 * during script lifetime.
 * @depracted 2.6
 *
 * @param string $plugin_id
 * @param mixed &$data
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

  return query2array($query);
}

/**
 * Loads a plugin in memory.
 * It performs autoupdate, includes the main.inc.php file and updates *$pwg_loaded_plugins*.
 *
 * @param string $plugin
 */
function load_plugin($plugin)
{
  $file_name = PHPWG_PLUGINS_PATH.$plugin['id'].'/main.inc.php';
  if (file_exists($file_name))
  {
    autoupdate_plugin($plugin);
    global $pwg_loaded_plugins;
    $pwg_loaded_plugins[ $plugin['id'] ] = $plugin;
    include_once($file_name);
  }
}

/**
 * Performs update task of a plugin.
 * Autoupdate is only performed if the plugin has a maintain.class.php file.
 *
 * @since 2.7
 *
 * @param array &$plugin (id, version, state) will be updated if version changes
 */
function autoupdate_plugin(&$plugin)
{
  // try to find the filesystem version in lines 2 to 10 of main.inc.php
  $fh = fopen(PHPWG_PLUGINS_PATH.$plugin['id'].'/main.inc.php', 'r');
  $fs_version = null;
  $i = -1;

  while (($line = fgets($fh))!==false && $fs_version==null && $i<10)
  {
    $i++;
    if ($i < 2) continue; // first lines are typically "<?php" and "/*"

    if (preg_match('/Version:\\s*([\\w.-]+)/', $line, $matches))
    {
      $fs_version = $matches[1];
    }
  }

  fclose($fh);

  // if version is auto (dev) or superior
  if ($fs_version != null && (
        $fs_version == 'auto' || $plugin['version'] == 'auto' ||
        safe_version_compare($plugin['version'], $fs_version, '<')
      )
  ) {
    $old_version = $plugin['version'];
    $new_version = $fs_version;

    $plugin['version'] = $fs_version;

    $maintain_file = PHPWG_PLUGINS_PATH.$plugin['id'].'/maintain.class.php';

    // autoupdate is applicable only to plugins with 2.7 architecture
    if (file_exists($maintain_file))
    {
      global $page;

      // call update method
      include_once($maintain_file);

      $classname = $plugin['id'].'_maintain';

      // piwigo-videojs and piwigo-openstreetmap unfortunately have a "-" in their folder
      // name (=plugin_id) and a class name can't have a "-". So we have to replace with a "_"
      $classname = str_replace('-', '_', $classname);

      $plugin_maintain = new $classname($plugin['id']);
      $plugin_maintain->update($plugin['version'], $fs_version, $page['errors']);
    }

    // update database (only on production). We want to avoid registering an "auto" to "auto" update,
    // which happens for each "version=auto" plugin on each page load.
    if ($new_version != $old_version)
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
  SET version = "'. $plugin['version'] .'"
  WHERE id = "'. $plugin['id'] .'"
;';
      pwg_query($query);

      pwg_activity('system', ACTIVITY_SYSTEM_PLUGIN, 'autoupdate', array('plugin_id'=>$plugin['id'], 'from_version'=>$old_version, 'to_version'=>$new_version));
    }
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
    trigger_notify('plugins_loaded');
  }
}

?>