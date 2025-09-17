<?php

namespace Smarty;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Smarty\Cacheresource\File;
use Smarty\Extension\Base;
use Smarty\Extension\BCPluginsAdapter;
use Smarty\Extension\CallbackWrapper;
use Smarty\Extension\CoreExtension;
use Smarty\Extension\DefaultExtension;
use Smarty\Extension\ExtensionInterface;
use Smarty\Filter\Output\TrimWhitespace;
use Smarty\Runtime\CaptureRuntime;
use Smarty\Runtime\DefaultPluginHandlerRuntime;
use Smarty\Runtime\ForeachRuntime;
use Smarty\Runtime\InheritanceRuntime;
use Smarty\Runtime\TplFunctionRuntime;


/**
 * Project:     Smarty: the PHP compiling template engine
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * For questions, help, comments, discussion, etc., please join the
 * Smarty mailing list. Send a blank e-mail to
 * smarty-discussion-subscribe@googlegroups.com
 *
 * @author    Monte Ohrt <monte at ohrt dot com>
 * @author    Uwe Tews   <uwe dot tews at gmail dot com>
 * @author    Rodney Rehm
 * @author    Simon Wisselink
 */

/**
 * This is the main Smarty class
 */
class Smarty extends \Smarty\TemplateBase {

	/**
	 * smarty version
	 */
	const SMARTY_VERSION = '5.5.2';

	/**
	 * define caching modes
	 */
	const CACHING_OFF = 0;
	const CACHING_LIFETIME_CURRENT = 1;
	const CACHING_LIFETIME_SAVED = 2;
	/**
	 * define constant for clearing cache files be saved expiration dates
	 */
	const CLEAR_EXPIRED = -1;
	/**
	 * define compile check modes
	 */
	const COMPILECHECK_OFF = 0;
	const COMPILECHECK_ON = 1;
	/**
	 * filter types
	 */
	const FILTER_POST = 'post';
	const FILTER_PRE = 'pre';
	const FILTER_OUTPUT = 'output';
	const FILTER_VARIABLE = 'variable';
	/**
	 * plugin types
	 */
	const PLUGIN_FUNCTION = 'function';
	const PLUGIN_BLOCK = 'block';
	const PLUGIN_COMPILER = 'compiler';
	const PLUGIN_MODIFIER = 'modifier';
	const PLUGIN_MODIFIERCOMPILER = 'modifiercompiler';

	/**
	 * The character set to adhere to (defaults to "UTF-8")
	 */
	public static $_CHARSET = 'UTF-8';

	/**
	 * The date format to be used internally
	 * (accepts date() and strftime())
	 */
	public static $_DATE_FORMAT = '%b %e, %Y';

	/**
	 * Flag denoting if PCRE should run in UTF-8 mode
	 */
	public static $_UTF8_MODIFIER = 'u';

	/**
	 * Flag denoting if operating system is windows
	 */
	public static $_IS_WINDOWS = false;

	/**
	 * auto literal on delimiters with whitespace
	 *
	 * @var boolean
	 */
	public $auto_literal = true;

	/**
	 * display error on not assigned variables
	 *
	 * @var boolean
	 */
	public $error_unassigned = false;

	/**
	 * flag if template_dir is normalized
	 *
	 * @var bool
	 */
	public $_templateDirNormalized = false;

	/**
	 * joined template directory string used in cache keys
	 *
	 * @var string
	 */
	public $_joined_template_dir = null;

	/**
	 * flag if config_dir is normalized
	 *
	 * @var bool
	 */
	public $_configDirNormalized = false;

	/**
	 * joined config directory string used in cache keys
	 *
	 * @var string
	 */
	public $_joined_config_dir = null;

	/**
	 * default template handler
	 *
	 * @var callable
	 */
	public $default_template_handler_func = null;

	/**
	 * default config handler
	 *
	 * @var callable
	 */
	public $default_config_handler_func = null;

	/**
	 * default plugin handler
	 *
	 * @var callable
	 */
	private $default_plugin_handler_func = null;

	/**
	 * flag if template_dir is normalized
	 *
	 * @var bool
	 */
	public $_compileDirNormalized = false;

	/**
	 * flag if template_dir is normalized
	 *
	 * @var bool
	 */
	public $_cacheDirNormalized = false;

	/**
	 * force template compiling?
	 *
	 * @var boolean
	 */
	public $force_compile = false;

	/**
	 * use sub dirs for compiled/cached files?
	 *
	 * @var boolean
	 */
	public $use_sub_dirs = false;

	/**
	 * merge compiled includes
	 *
	 * @var boolean
	 */
	public $merge_compiled_includes = false;

	/**
	 * force cache file creation
	 *
	 * @var boolean
	 */
	public $force_cache = false;

	/**
	 * template left-delimiter
	 *
	 * @var string
	 */
	private $left_delimiter = "{";

	/**
	 * template right-delimiter
	 *
	 * @var string
	 */
	private $right_delimiter = "}";

	/**
	 * array of strings which shall be treated as literal by compiler
	 *
	 * @var array string
	 */
	public $literals = [];

	/**
	 * class name
	 * This should be instance of \Smarty\Security.
	 *
	 * @var string
	 * @see \Smarty\Security
	 */
	public $security_class = \Smarty\Security::class;

	/**
	 * implementation of security class
	 *
	 * @var \Smarty\Security
	 */
	public $security_policy = null;

	/**
	 * debug mode
	 * Setting this to true enables the debug-console. Setting it to 2 enables individual Debug Console window by
	 * template name.
	 *
	 * @var boolean|int
	 */
	public $debugging = false;

	/**
	 * This determines if debugging is enable-able from the browser.
	 * <ul>
	 *  <li>NONE => no debugging control allowed</li>
	 *  <li>URL => enable debugging when SMARTY_DEBUG is found in the URL.</li>
	 * </ul>
	 *
	 * @var string
	 */
	public $debugging_ctrl = 'NONE';

	/**
	 * Name of debugging URL-param.
	 * Only used when $debugging_ctrl is set to 'URL'.
	 * The name of the URL-parameter that activates debugging.
	 *
	 * @var string
	 */
	public $smarty_debug_id = 'SMARTY_DEBUG';

	/**
	 * Path of debug template.
	 *
	 * @var string
	 */
	public $debug_tpl = null;

	/**
	 * When set, smarty uses this value as error_reporting-level.
	 *
	 * @var int
	 */
	public $error_reporting = null;

	/**
	 * Controls whether variables with the same name overwrite each other.
	 *
	 * @var boolean
	 */
	public $config_overwrite = true;

	/**
	 * Controls whether config values of on/true/yes and off/false/no get converted to boolean.
	 *
	 * @var boolean
	 */
	public $config_booleanize = true;

	/**
	 * Controls whether hidden config sections/vars are read from the file.
	 *
	 * @var boolean
	 */
	public $config_read_hidden = false;

	/**
	 * locking concurrent compiles
	 *
	 * @var boolean
	 */
	public $compile_locking = true;

	/**
	 * Controls whether cache resources should use locking mechanism
	 *
	 * @var boolean
	 */
	public $cache_locking = false;

	/**
	 * seconds to wait for acquiring a lock before ignoring the write lock
	 *
	 * @var float
	 */
	public $locking_timeout = 10;

	/**
	 * resource type used if none given
	 * Must be a valid key of $registered_resources.
	 *
	 * @var string
	 */
	public $default_resource_type = 'file';

	/**
	 * cache resource
	 * Must be a subclass of \Smarty\Cacheresource\Base
	 *
	 * @var \Smarty\Cacheresource\Base
	 */
	private $cacheResource;

	/**
	 * config type
	 *
	 * @var string
	 */
	public $default_config_type = 'file';

	/**
	 * check If-Modified-Since headers
	 *
	 * @var boolean
	 */
	public $cache_modified_check = false;

	/**
	 * registered plugins
	 *
	 * @var array
	 */
	public $registered_plugins = [];

	/**
	 * registered objects
	 *
	 * @var array
	 */
	public $registered_objects = [];

	/**
	 * registered classes
	 *
	 * @var array
	 */
	public $registered_classes = [];

	/**
	 * registered resources
	 *
	 * @var array
	 */
	public $registered_resources = [];

	/**
	 * registered cache resources
	 *
	 * @var array
	 * @deprecated since 5.0
	 */
	private $registered_cache_resources = [];

	/**
	 * default modifier
	 *
	 * @var array
	 */
	public $default_modifiers = [];

	/**
	 * autoescape variable output
	 *
	 * @var boolean
	 */
	public $escape_html = false;

	/**
	 * start time for execution time calculation
	 *
	 * @var int
	 */
	public $start_time = 0;

	/**
	 * internal flag to enable parser debugging
	 *
	 * @var bool
	 */
	public $_parserdebug = false;

	/**
	 * Debug object
	 *
	 * @var \Smarty\Debug
	 */
	public $_debug = null;

	/**
	 * template directory
	 *
	 * @var array
	 */
	protected $template_dir = ['./templates/'];

	/**
	 * flags for normalized template directory entries
	 *
	 * @var array
	 */
	protected $_processedTemplateDir = [];

	/**
	 * config directory
	 *
	 * @var array
	 */
	protected $config_dir = ['./configs/'];

	/**
	 * flags for normalized template directory entries
	 *
	 * @var array
	 */
	protected $_processedConfigDir = [];

	/**
	 * compile directory
	 *
	 * @var string
	 */
	protected $compile_dir = './templates_c/';

	/**
	 * cache directory
	 *
	 * @var string
	 */
	protected $cache_dir = './cache/';

	/**
	 * PHP7 Compatibility mode
	 *
	 * @var bool
	 */
	private $isMutingUndefinedOrNullWarnings = false;

	/**
	 * Cache of loaded resource handlers.
	 *
	 * @var array
	 */
	public $_resource_handlers = [];

	/**
	 * Cache of loaded cacheresource handlers.
	 *
	 * @var array
	 */
	public $_cacheresource_handlers = [];

	/**
	 * List of extensions
	 *
	 * @var ExtensionInterface[]
	 */
	private $extensions = [];
	/**
	 * @var BCPluginsAdapter
	 */
	private $BCPluginsAdapter;

	/**
	 * Initialize new Smarty object
	 */
	public function __construct() {

		$this->start_time = microtime(true);
		// Check if we're running on Windows
		\Smarty\Smarty::$_IS_WINDOWS = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
		// let PCRE (preg_*) treat strings as ISO-8859-1 if we're not dealing with UTF-8
		if (\Smarty\Smarty::$_CHARSET !== 'UTF-8') {
			\Smarty\Smarty::$_UTF8_MODIFIER = '';
		}

		$this->BCPluginsAdapter = new BCPluginsAdapter($this);

		$this->extensions[] = new CoreExtension();
		$this->extensions[] = new DefaultExtension();
		$this->extensions[] = $this->BCPluginsAdapter;

		$this->cacheResource = new File();
	}

	/**
	 * Load an additional extension.
	 *
	 * @return void
	 */
	public function addExtension(ExtensionInterface $extension) {
		$this->extensions[] = $extension;
	}

	/**
	 * Returns all loaded extensions
	 *
	 * @return array|ExtensionInterface[]
	 */
	public function getExtensions(): array {
		return $this->extensions;
	}

	/**
	 * Replace the entire list extensions, allowing you to determine the exact order of the extensions.
	 *
	 * @param ExtensionInterface[] $extensions
	 *
	 * @return void
	 */
	public function setExtensions(array $extensions): void {
		$this->extensions = $extensions;
	}

	/**
	 * Check if a template resource exists
	 *
	 * @param string $resource_name template name
	 *
	 * @return bool status
	 * @throws \Smarty\Exception
	 */
	public function templateExists($resource_name) {
		// create source object
		$source = Template\Source::load(null, $this, $resource_name);
		return $source->exists;
	}

	/**
	 * Loads security class and enables security
	 *
	 * @param string|\Smarty\Security $security_class if a string is used, it must be class-name
	 *
	 * @return static                 current Smarty instance for chaining
	 * @throws \Smarty\Exception
	 */
	public function enableSecurity($security_class = null) {
		\Smarty\Security::enableSecurity($this, $security_class);
		return $this;
	}

	/**
	 * Disable security
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function disableSecurity() {
		$this->security_policy = null;
		return $this;
	}

	/**
	 * Add template directory(s)
	 *
	 * @param string|array $template_dir directory(s) of template sources
	 * @param string $key of the array element to assign the template dir to
	 * @param bool $isConfig true for config_dir
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function addTemplateDir($template_dir, $key = null, $isConfig = false) {
		if ($isConfig) {
			$processed = &$this->_processedConfigDir;
			$dir = &$this->config_dir;
			$this->_configDirNormalized = false;
		} else {
			$processed = &$this->_processedTemplateDir;
			$dir = &$this->template_dir;
			$this->_templateDirNormalized = false;
		}
		if (is_array($template_dir)) {
			foreach ($template_dir as $k => $v) {
				if (is_int($k)) {
					// indexes are not merged but appended
					$dir[] = $v;
				} else {
					// string indexes are overridden
					$dir[$k] = $v;
					unset($processed[$key]);
				}
			}
		} else {
			if ($key !== null) {
				// override directory at specified index
				$dir[$key] = $template_dir;
				unset($processed[$key]);
			} else {
				// append new directory
				$dir[] = $template_dir;
			}
		}
		return $this;
	}

	/**
	 * Get template directories
	 *
	 * @param mixed $index index of directory to get, null to get all
	 * @param bool $isConfig true for config_dir
	 *
	 * @return array|string list of template directories, or directory of $index
	 */
	public function getTemplateDir($index = null, $isConfig = false) {
		if ($isConfig) {
			$dir = &$this->config_dir;
		} else {
			$dir = &$this->template_dir;
		}
		if ($isConfig ? !$this->_configDirNormalized : !$this->_templateDirNormalized) {
			$this->_normalizeTemplateConfig($isConfig);
		}
		if ($index !== null) {
			return isset($dir[$index]) ? $dir[$index] : null;
		}
		return $dir;
	}

	/**
	 * Set template directory
	 *
	 * @param string|array $template_dir directory(s) of template sources
	 * @param bool $isConfig true for config_dir
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function setTemplateDir($template_dir, $isConfig = false) {
		if ($isConfig) {
			$this->config_dir = [];
			$this->_processedConfigDir = [];
		} else {
			$this->template_dir = [];
			$this->_processedTemplateDir = [];
		}
		$this->addTemplateDir($template_dir, null, $isConfig);
		return $this;
	}

	/**
	 * Adds a template directory before any existing directoires
	 *
	 * @param string $new_template_dir directory of template sources
	 * @param bool $is_config true for config_dir
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function prependTemplateDir($new_template_dir, $is_config = false) {
		$current_template_dirs = $is_config ? $this->config_dir : $this->template_dir;
		array_unshift($current_template_dirs, $new_template_dir);
		$this->setTemplateDir($current_template_dirs, $is_config);
		return $this;
	}

	/**
	 * Add config directory(s)
	 *
	 * @param string|array $config_dir directory(s) of config sources
	 * @param mixed $key key of the array element to assign the config dir to
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function addConfigDir($config_dir, $key = null) {
		return $this->addTemplateDir($config_dir, $key, true);
	}

	/**
	 * Get config directory
	 *
	 * @param mixed $index index of directory to get, null to get all
	 *
	 * @return array configuration directory
	 */
	public function getConfigDir($index = null) {
		return $this->getTemplateDir($index, true);
	}

	/**
	 * Set config directory
	 *
	 * @param $config_dir
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function setConfigDir($config_dir) {
		return $this->setTemplateDir($config_dir, true);
	}

	/**
	 * Registers plugin to be used in templates
	 *
	 * @param string $type plugin type
	 * @param string $name name of template tag
	 * @param callable $callback PHP callback to register
	 * @param bool $cacheable if true (default) this function is cache able
	 *
	 * @return $this
	 * @throws \Smarty\Exception
	 *
	 * @api  Smarty::registerPlugin()
	 */
	public function registerPlugin($type, $name, $callback, $cacheable = true) {
		if (isset($this->registered_plugins[$type][$name])) {
			throw new Exception("Plugin tag '{$name}' already registered");
		} elseif (!is_callable($callback) && !class_exists($callback)) {
			throw new Exception("Plugin '{$name}' not callable");
		} else {
			$this->registered_plugins[$type][$name] = [$callback, (bool)$cacheable];
		}
		return $this;
	}

	/**
	 * Returns plugin previously registered using ::registerPlugin as a numerical array as follows or null if not found:
	 * [
	 *  0 => the callback
	 *  1 => (bool) $cacheable
	 *  2 => (array) $cache_attr
	 * ]
	 *
	 * @param string $type plugin type
	 * @param string $name name of template tag
	 *
	 * @return array|null
	 *
	 * @api  Smarty::unregisterPlugin()
	 */
	public function getRegisteredPlugin($type, $name): ?array {
		if (isset($this->registered_plugins[$type][$name])) {
			return $this->registered_plugins[$type][$name];
		}
		return null;
	}

	/**
	 * Unregisters plugin previously registered using ::registerPlugin
	 *
	 * @param string $type plugin type
	 * @param string $name name of template tag
	 *
	 * @return $this
	 *
	 * @api  Smarty::unregisterPlugin()
	 */
	public function unregisterPlugin($type, $name) {
		if (isset($this->registered_plugins[$type][$name])) {
			unset($this->registered_plugins[$type][$name]);
		}
		return $this;
	}

	/**
	 * Adds directory of plugin files
	 *
	 * @param null|array|string $plugins_dir
	 *
	 * @return static current Smarty instance for chaining
	 * @deprecated since 5.0
	 */
	public function addPluginsDir($plugins_dir) {
		trigger_error('Using Smarty::addPluginsDir() to load plugins is deprecated and will be ' .
			'removed in a future release. Use Smarty::addExtension() to add an extension or Smarty::registerPlugin to ' .
			'quickly register a plugin using a callback function.', E_USER_DEPRECATED);

		foreach ((array)$plugins_dir as $v) {
			$path = $this->_realpath(rtrim($v ?? '', '/\\') . DIRECTORY_SEPARATOR, true);
			$this->BCPluginsAdapter->loadPluginsFromDir($path);
		}

		return $this;
	}

	/**
	 * Get plugin directories
	 *
	 * @return array list of plugin directories
	 * @deprecated since 5.0
	 */
	public function getPluginsDir() {
		trigger_error('Using Smarty::getPluginsDir() is deprecated and will be ' .
			'removed in a future release. It will always return an empty array.', E_USER_DEPRECATED);
		return [];
	}

	/**
	 * Set plugins directory
	 *
	 * @param string|array $plugins_dir directory(s) of plugins
	 *
	 * @return static current Smarty instance for chaining
	 * @deprecated since 5.0
	 */
	public function setPluginsDir($plugins_dir) {
		trigger_error('Using Smarty::getPluginsDir() is deprecated and will be ' .
			'removed in a future release. For now, it will remove the DefaultExtension from the extensions list and ' .
			'proceed to call Smartyy::addPluginsDir..', E_USER_DEPRECATED);

		$this->extensions = array_filter(
			$this->extensions,
			function ($extension) {
				return !($extension instanceof DefaultExtension);
			}
		);

		return $this->addPluginsDir($plugins_dir);
	}

	/**
	 * Registers a default plugin handler
	 *
	 * @param callable $callback class/method name
	 *
	 * @return $this
	 * @throws Exception              if $callback is not callable
	 *
	 * @api  Smarty::registerDefaultPluginHandler()
	 *
	 * @deprecated since 5.0
	 */
	public function registerDefaultPluginHandler($callback) {

		trigger_error('Using Smarty::registerDefaultPluginHandler() is deprecated and will be ' .
			'removed in a future release. Please rewrite your plugin handler as an extension.',
			E_USER_DEPRECATED);

		if (is_callable($callback)) {
			$this->default_plugin_handler_func = $callback;
		} else {
			throw new Exception("Default plugin handler '$callback' not callable");
		}
		return $this;
	}

	/**
	 * Get compiled directory
	 *
	 * @return string path to compiled templates
	 */
	public function getCompileDir() {
		if (!$this->_compileDirNormalized) {
			$this->_normalizeDir('compile_dir', $this->compile_dir);
			$this->_compileDirNormalized = true;
		}
		return $this->compile_dir;
	}

	/**
	 *
	 * @param string $compile_dir directory to store compiled templates in
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function setCompileDir($compile_dir) {
		$this->_normalizeDir('compile_dir', $compile_dir);
		$this->_compileDirNormalized = true;
		return $this;
	}

	/**
	 * Get cache directory
	 *
	 * @return string path of cache directory
	 */
	public function getCacheDir() {
		if (!$this->_cacheDirNormalized) {
			$this->_normalizeDir('cache_dir', $this->cache_dir);
			$this->_cacheDirNormalized = true;
		}
		return $this->cache_dir;
	}

	/**
	 * Set cache directory
	 *
	 * @param string $cache_dir directory to store cached templates in
	 *
	 * @return static current Smarty instance for chaining
	 */
	public function setCacheDir($cache_dir) {
		$this->_normalizeDir('cache_dir', $cache_dir);
		$this->_cacheDirNormalized = true;
		return $this;
	}

	private $templates = [];

	/**
	 * Creates a template object
	 *
	 * @param string $template_name
	 * @param mixed $cache_id cache id to be used with this template
	 * @param mixed $compile_id compile id to be used with this template
	 * @param null $parent next higher level of Smarty variables
	 *
	 * @return Template template object
	 * @throws Exception
	 */
	public function createTemplate($template_name, $cache_id = null, $compile_id = null, $parent = null): Template {

		$data = [];

		// Shuffle params for backward compatibility: if 2nd param is an object, it's the parent
		if (is_object($cache_id)) {
			$parent = $cache_id;
			$cache_id = null;
		}

		// Shuffle params for backward compatibility: if 2nd param is an array, it's data
		if (is_array($cache_id)) {
			$data = $cache_id;
			$cache_id = null;
		}

		return $this->doCreateTemplate($template_name, $cache_id, $compile_id, $parent, null, null, false, $data);
	}

	/**
	 * Get unique template id
	 *
	 * @param string $resource_name
	 * @param null|mixed $cache_id
	 * @param null|mixed $compile_id
	 * @param null $caching
	 *
	 * @return string
	 */
	private function generateUniqueTemplateId(
		$resource_name,
		$cache_id = null,
		$compile_id = null,
		$caching = null
	): string {
		// defaults for optional params
		$cache_id = $cache_id ?? $this->cache_id;
		$compile_id = $compile_id ?? $this->compile_id;
		$caching = (int)$caching ?? $this->caching;

		// Add default resource type to resource name if it is missing
		if (strpos($resource_name, ':') === false) {
			$resource_name = "{$this->default_resource_type}:{$resource_name}";
		}

		$_templateId = $resource_name . '#' . $cache_id . '#' . $compile_id . '#' . $caching;

		// hash very long IDs to prevent problems with filename length
		// do not hash shorter IDs, so they remain recognizable
		if (strlen($_templateId) > 150) {
			$_templateId = sha1($_templateId);
		}

		return $_templateId;
	}

	/**
	 * Normalize path
	 *  - remove /./ and /../
	 *  - make it absolute if required
	 *
	 * @param string $path file path
	 * @param bool $realpath if true - convert to absolute
	 *                         false - convert to relative
	 *                         null - keep as it is but
	 *                         remove /./ /../
	 *
	 * @return string
	 */
	public function _realpath($path, $realpath = null) {
		$nds = ['/' => '\\', '\\' => '/'];
		preg_match(
			'%^(?<root>(?:[[:alpha:]]:[\\\\/]|/|[\\\\]{2}[[:alpha:]]+|[[:print:]]{2,}:[/]{2}|[\\\\])?)(?<path>(.*))$%u',
			$path,
			$parts
		);
		$path = $parts['path'];
		if ($parts['root'] === '\\') {
			$parts['root'] = substr(getcwd(), 0, 2) . $parts['root'];
		} else {
			if ($realpath !== null && !$parts['root']) {
				$path = getcwd() . DIRECTORY_SEPARATOR . $path;
			}
		}
		// normalize DIRECTORY_SEPARATOR
		$path = str_replace($nds[DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $path);
		$parts['root'] = str_replace($nds[DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, $parts['root']);
		do {
			$path = preg_replace(
				['#[\\\\/]{2}#', '#[\\\\/][.][\\\\/]#', '#[\\\\/]([^\\\\/.]+)[\\\\/][.][.][\\\\/]#'],
				DIRECTORY_SEPARATOR,
				$path,
				-1,
				$count
			);
		} while ($count > 0);
		return $realpath !== false ? $parts['root'] . $path : str_ireplace(getcwd(), '.', $parts['root'] . $path);
	}

	/**
	 * @param boolean $use_sub_dirs
	 */
	public function setUseSubDirs($use_sub_dirs) {
		$this->use_sub_dirs = $use_sub_dirs;
	}

	/**
	 * @param int $error_reporting
	 */
	public function setErrorReporting($error_reporting) {
		$this->error_reporting = $error_reporting;
	}

	/**
	 * @param boolean $escape_html
	 */
	public function setEscapeHtml($escape_html) {
		$this->escape_html = $escape_html;
	}

	/**
	 * Return auto_literal flag
	 *
	 * @return boolean
	 */
	public function getAutoLiteral() {
		return $this->auto_literal;
	}

	/**
	 * Set auto_literal flag
	 *
	 * @param boolean $auto_literal
	 */
	public function setAutoLiteral($auto_literal = true) {
		$this->auto_literal = $auto_literal;
	}

	/**
	 * @param boolean $force_compile
	 */
	public function setForceCompile($force_compile) {
		$this->force_compile = $force_compile;
	}

	/**
	 * @param boolean $merge_compiled_includes
	 */
	public function setMergeCompiledIncludes($merge_compiled_includes) {
		$this->merge_compiled_includes = $merge_compiled_includes;
	}

	/**
	 * Get left delimiter
	 *
	 * @return string
	 */
	public function getLeftDelimiter() {
		return $this->left_delimiter;
	}

	/**
	 * Set left delimiter
	 *
	 * @param string $left_delimiter
	 */
	public function setLeftDelimiter($left_delimiter) {
		$this->left_delimiter = $left_delimiter;
	}

	/**
	 * Get right delimiter
	 *
	 * @return string $right_delimiter
	 */
	public function getRightDelimiter() {
		return $this->right_delimiter;
	}

	/**
	 * Set right delimiter
	 *
	 * @param string
	 */
	public function setRightDelimiter($right_delimiter) {
		$this->right_delimiter = $right_delimiter;
	}

	/**
	 * @param boolean $debugging
	 */
	public function setDebugging($debugging) {
		$this->debugging = $debugging;
	}

	/**
	 * @param boolean $config_overwrite
	 */
	public function setConfigOverwrite($config_overwrite) {
		$this->config_overwrite = $config_overwrite;
	}

	/**
	 * @param boolean $config_booleanize
	 */
	public function setConfigBooleanize($config_booleanize) {
		$this->config_booleanize = $config_booleanize;
	}

	/**
	 * @param boolean $config_read_hidden
	 */
	public function setConfigReadHidden($config_read_hidden) {
		$this->config_read_hidden = $config_read_hidden;
	}

	/**
	 * @param boolean $compile_locking
	 */
	public function setCompileLocking($compile_locking) {
		$this->compile_locking = $compile_locking;
	}

	/**
	 * @param string $default_resource_type
	 */
	public function setDefaultResourceType($default_resource_type) {
		$this->default_resource_type = $default_resource_type;
	}

	/**
	 * Test install
	 *
	 * @param null $errors
	 */
	public function testInstall(&$errors = null) {
		\Smarty\TestInstall::testInstall($this, $errors);
	}

	/**
	 * Get Smarty object
	 *
	 * @return static
	 */
	public function getSmarty() {
		return $this;
	}

	/**
	 * Normalize and set directory string
	 *
	 * @param string $dirName cache_dir or compile_dir
	 * @param string $dir filepath of folder
	 */
	private function _normalizeDir($dirName, $dir) {
		$this->{$dirName} = $this->_realpath(rtrim($dir ?? '', "/\\") . DIRECTORY_SEPARATOR, true);
	}

	/**
	 * Normalize template_dir or config_dir
	 *
	 * @param bool $isConfig true for config_dir
	 */
	private function _normalizeTemplateConfig($isConfig) {
		if ($isConfig) {
			$processed = &$this->_processedConfigDir;
			$dir = &$this->config_dir;
		} else {
			$processed = &$this->_processedTemplateDir;
			$dir = &$this->template_dir;
		}
		if (!is_array($dir)) {
			$dir = (array)$dir;
		}
		foreach ($dir as $k => $v) {
			if (!isset($processed[$k])) {
				$dir[$k] = $this->_realpath(rtrim($v ?? '', "/\\") . DIRECTORY_SEPARATOR, true);
				$processed[$k] = true;
			}
		}

		if ($isConfig) {
			$this->_configDirNormalized = true;
			$this->_joined_config_dir = join('#', $this->config_dir);
		} else {
			$this->_templateDirNormalized = true;
			$this->_joined_template_dir = join('#', $this->template_dir);
		}

	}

	/**
	 * Mutes errors for "undefined index", "undefined array key" and "trying to read property of null".
	 *
	 * @void
	 */
	public function muteUndefinedOrNullWarnings(): void {
		$this->isMutingUndefinedOrNullWarnings = true;
	}

	/**
	 * Indicates if Smarty will mute errors for "undefined index", "undefined array key" and "trying to read property of null".
	 *
	 * @return bool
	 */
	public function isMutingUndefinedOrNullWarnings(): bool {
		return $this->isMutingUndefinedOrNullWarnings;
	}

	/**
	 * Empty cache for a specific template
	 *
	 * @param string $template_name template name
	 * @param string $cache_id cache id
	 * @param string $compile_id compile id
	 * @param integer $exp_time expiration time
	 * @param string $type resource type
	 *
	 * @return int number of cache files deleted
	 * @throws \Smarty\Exception
	 *
	 * @api  Smarty::clearCache()
	 */
	public function clearCache(
		$template_name,
		$cache_id = null,
		$compile_id = null,
		$exp_time = null
	) {
		return $this->getCacheResource()->clear($this, $template_name, $cache_id, $compile_id, $exp_time);
	}

	/**
	 * Empty cache folder
	 *
	 * @param integer $exp_time expiration time
	 * @param string $type resource type
	 *
	 * @return int number of cache files deleted
	 *
	 * @api  Smarty::clearAllCache()
	 */
	public function clearAllCache($exp_time = null) {
		return $this->getCacheResource()->clearAll($this, $exp_time);
	}

	/**
	 * Delete compiled template file
	 *
	 * @param string $resource_name template name
	 * @param string $compile_id compile id
	 * @param integer $exp_time expiration time
	 *
	 * @return int number of template files deleted
	 * @throws \Smarty\Exception
	 *
	 * @api  Smarty::clearCompiledTemplate()
	 */
	public function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null) {
		$_compile_dir = $this->getCompileDir();
		if ($_compile_dir === '/') { //We should never want to delete this!
			return 0;
		}
		$_compile_id = isset($compile_id) ? preg_replace('![^\w]+!', '_', $compile_id) : null;
		$_dir_sep = $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
		if (isset($resource_name)) {
			$_save_stat = $this->caching;
			$this->caching = \Smarty\Smarty::CACHING_OFF;
			/* @var Template $tpl */
			$tpl = $this->doCreateTemplate($resource_name);
			$this->caching = $_save_stat;
			if (!$tpl->getSource()->handler->recompiled && $tpl->getSource()->exists) {
				$_resource_part_1 = basename(str_replace('^', DIRECTORY_SEPARATOR, $tpl->getCompiled()->filepath));
				$_resource_part_1_length = strlen($_resource_part_1);
			} else {
				return 0;
			}
			$_resource_part_2 = str_replace('.php', '.cache.php', $_resource_part_1);
			$_resource_part_2_length = strlen($_resource_part_2);
		}
		$_dir = $_compile_dir;
		if ($this->use_sub_dirs && isset($_compile_id)) {
			$_dir .= $_compile_id . $_dir_sep;
		}
		if (isset($_compile_id)) {
			$_compile_id_part = $_compile_dir . $_compile_id . $_dir_sep;
			$_compile_id_part_length = strlen($_compile_id_part);
		}
		$_count = 0;
		try {
			$_compileDirs = new RecursiveDirectoryIterator($_dir);
		} catch (\UnexpectedValueException $e) {
			// path not found / not a dir
			return 0;
		}
		$_compile = new RecursiveIteratorIterator($_compileDirs, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($_compile as $_file) {
			if (substr(basename($_file->getPathname()), 0, 1) === '.') {
				continue;
			}
			$_filepath = (string)$_file;
			if ($_file->isDir()) {
				if (!$_compile->isDot()) {
					// delete folder if empty
					@rmdir($_file->getPathname());
				}
			} else {
				// delete only php files
				if (substr($_filepath, -4) !== '.php') {
					continue;
				}
				$unlink = false;
				if ((!isset($_compile_id) ||
						(isset($_filepath[$_compile_id_part_length]) &&
							$a = !strncmp($_filepath, $_compile_id_part, $_compile_id_part_length)))
					&& (!isset($resource_name) || (isset($_filepath[$_resource_part_1_length])
							&& substr_compare(
								$_filepath,
								$_resource_part_1,
								-$_resource_part_1_length,
								$_resource_part_1_length
							) === 0) || (isset($_filepath[$_resource_part_2_length])
							&& substr_compare(
								$_filepath,
								$_resource_part_2,
								-$_resource_part_2_length,
								$_resource_part_2_length
							) === 0))
				) {
					if (isset($exp_time)) {
						if (is_file($_filepath) && time() - filemtime($_filepath) >= $exp_time) {
							$unlink = true;
						}
					} else {
						$unlink = true;
					}
				}
				if ($unlink && is_file($_filepath) && @unlink($_filepath)) {
					$_count++;
					if (function_exists('opcache_invalidate')
						&& (!function_exists('ini_get') || strlen(ini_get('opcache.restrict_api')) < 1)
					) {
						opcache_invalidate($_filepath, true);
					} elseif (function_exists('apc_delete_file')) {
						apc_delete_file($_filepath);
					}
				}
			}
		}
		return $_count;
	}

	/**
	 * Compile all template files
	 *
	 * @param string $extension file extension
	 * @param bool $force_compile force all to recompile
	 * @param int $time_limit
	 * @param int $max_errors
	 *
	 * @return integer number of template files recompiled
	 * @api Smarty::compileAllTemplates()
	 *
	 */
	public function compileAllTemplates(
		$extension = '.tpl',
		$force_compile = false,
		$time_limit = 0,
		$max_errors = null
	) {
		return $this->compileAll($extension, $force_compile, $time_limit, $max_errors);
	}

	/**
	 * Compile all config files
	 *
	 * @param string $extension file extension
	 * @param bool $force_compile force all to recompile
	 * @param int $time_limit
	 * @param int $max_errors
	 *
	 * @return int number of template files recompiled
	 * @api Smarty::compileAllConfig()
	 *
	 */
	public function compileAllConfig(
		$extension = '.conf',
		$force_compile = false,
		$time_limit = 0,
		$max_errors = null
	) {
		return $this->compileAll($extension, $force_compile, $time_limit, $max_errors, true);
	}

	/**
	 * Compile all template or config files
	 *
	 * @param string $extension template file name extension
	 * @param bool $force_compile force all to recompile
	 * @param int $time_limit set maximum execution time
	 * @param int $max_errors set maximum allowed errors
	 * @param bool $isConfig flag true if called for config files
	 *
	 * @return int number of template files compiled
	 */
	protected function compileAll(
		$extension,
		$force_compile,
		$time_limit,
		$max_errors,
		$isConfig = false
	) {
		// switch off time limit
		if (function_exists('set_time_limit')) {
			@set_time_limit($time_limit);
		}
		$_count = 0;
		$_error_count = 0;
		$sourceDir = $isConfig ? $this->getConfigDir() : $this->getTemplateDir();
		// loop over array of source directories
		foreach ($sourceDir as $_dir) {
			$_dir_1 = new RecursiveDirectoryIterator(
				$_dir,
				defined('FilesystemIterator::FOLLOW_SYMLINKS') ?
					FilesystemIterator::FOLLOW_SYMLINKS : 0
			);
			$_dir_2 = new RecursiveIteratorIterator($_dir_1);
			foreach ($_dir_2 as $_fileinfo) {
				$_file = $_fileinfo->getFilename();
				if (substr(basename($_fileinfo->getPathname()), 0, 1) === '.' || strpos($_file, '.svn') !== false) {
					continue;
				}
				if (substr_compare($_file, $extension, -strlen($extension)) !== 0) {
					continue;
				}
				if ($_fileinfo->getPath() !== substr($_dir, 0, -1)) {
					$_file = substr($_fileinfo->getPath(), strlen($_dir)) . DIRECTORY_SEPARATOR . $_file;
				}
				echo "\n", $_dir, '---', $_file;
				flush();
				$_start_time = microtime(true);
				$_smarty = clone $this;
				//
				$_smarty->force_compile = $force_compile;
				try {
					$_tpl = $this->doCreateTemplate($_file);
					$_tpl->caching = self::CACHING_OFF;
					$_tpl->setSource(
						$isConfig ? \Smarty\Template\Config::load($_tpl) : \Smarty\Template\Source::load($_tpl)
					);
					if ($_tpl->mustCompile()) {
						$_tpl->compileTemplateSource();
						$_count++;
						echo ' compiled in  ', microtime(true) - $_start_time, ' seconds';
						flush();
					} else {
						echo ' is up to date';
						flush();
					}
				} catch (\Exception $e) {
					echo "\n        ------>Error: ", $e->getMessage(), "\n";
					$_error_count++;
				}
				// free memory
				unset($_tpl);
				if ($max_errors !== null && $_error_count === $max_errors) {
					echo "\ntoo many errors\n";
					exit(1);
				}
			}
		}
		echo "\n";
		return $_count;
	}

	/**
	 * check client side cache
	 *
	 * @param \Smarty\Template\Cached $cached
	 * @param Template $_template
	 * @param string $content
	 *
	 * @throws \Exception
	 * @throws \Smarty\Exception
	 */
	public function cacheModifiedCheck(Template\Cached $cached, Template $_template, $content) {
		$_isCached = $_template->isCached() && !$_template->getCompiled()->getNocacheCode();
		$_last_modified_date =
			@substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
		if ($_isCached && $cached->timestamp <= strtotime($_last_modified_date)) {
			switch (PHP_SAPI) {
				case 'cgi': // php-cgi < 5.3
				case 'cgi-fcgi': // php-cgi >= 5.3
				case 'fpm-fcgi': // php-fpm >= 5.3.3
					header('Status: 304 Not Modified');
					break;
				case 'cli':
					if (/* ^phpunit */
					!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS']) /* phpunit$ */
					) {
						$_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
					}
					break;
				default:
					if (/* ^phpunit */
					!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS']) /* phpunit$ */
					) {
						$_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
					} else {
						header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
					}
					break;
			}
		} else {
			switch (PHP_SAPI) {
				case 'cli':
					if (/* ^phpunit */
					!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS']) /* phpunit$ */
					) {
						$_SERVER['SMARTY_PHPUNIT_HEADERS'][] =
							'Last-Modified: ' . gmdate('D, d M Y H:i:s', $cached->timestamp) . ' GMT';
					}
					break;
				default:
					header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cached->timestamp) . ' GMT');
					break;
			}
			echo $content;
		}
	}

	public function getModifierCallback(string $modifierName) {
		foreach ($this->getExtensions() as $extension) {
			if ($callback = $extension->getModifierCallback($modifierName)) {
				return [new CallbackWrapper($modifierName, $callback), 'handle'];
			}
		}
		return null;
	}

	public function getFunctionHandler(string $functionName): ?\Smarty\FunctionHandler\FunctionHandlerInterface {
		foreach ($this->getExtensions() as $extension) {
			if ($handler = $extension->getFunctionHandler($functionName)) {
				return $handler;
			}
		}
		return null;
	}

	public function getBlockHandler(string $blockTagName): ?\Smarty\BlockHandler\BlockHandlerInterface {
		foreach ($this->getExtensions() as $extension) {
			if ($handler = $extension->getBlockHandler($blockTagName)) {
				return $handler;
			}
		}
		return null;
	}

	public function getModifierCompiler(string $modifier): ?\Smarty\Compile\Modifier\ModifierCompilerInterface {
		foreach ($this->getExtensions() as $extension) {
			if ($handler = $extension->getModifierCompiler($modifier)) {
				return $handler;
			}
		}
		return null;
	}

	/**
	 * Run pre-filters over template source
	 *
	 * @param string $source the content which shall be processed by the filters
	 * @param Template $template template object
	 *
	 * @return string                   the filtered source
	 */
	public function runPreFilters($source, Template $template) {

		foreach ($this->getExtensions() as $extension) {
			/** @var \Smarty\Filter\FilterInterface $filter */
			foreach ($extension->getPreFilters() as $filter) {
				$source = $filter->filter($source, $template);
			}
		}

		// return filtered output
		return $source;
	}

	/**
	 * Run post-filters over template's compiled code
	 *
	 * @param string $code the content which shall be processed by the filters
	 * @param Template $template template object
	 *
	 * @return string                   the filtered code
	 */
	public function runPostFilters($code, Template $template) {

		foreach ($this->getExtensions() as $extension) {
			/** @var \Smarty\Filter\FilterInterface $filter */
			foreach ($extension->getPostFilters() as $filter) {
				$code = $filter->filter($code, $template);
			}
		}

		// return filtered output
		return $code;
	}

	/**
	 * Run filters over template output
	 *
	 * @param string $content the content which shall be processed by the filters
	 * @param Template $template template object
	 *
	 * @return string                   the filtered (modified) output
	 */
	public function runOutputFilters($content, Template $template) {

		foreach ($this->getExtensions() as $extension) {
			/** @var \Smarty\Filter\FilterInterface $filter */
			foreach ($extension->getOutputFilters() as $filter) {
				$content = $filter->filter($content, $template);
			}
		}

		// return filtered output
		return $content;
	}

	/**
	 * Writes file in a safe way to disk
	 *
	 * @param string $_filepath complete filepath
	 * @param string $_contents file content
	 *
	 * @return boolean true
	 * @throws Exception
	 */
	public function writeFile($_filepath, $_contents) {
		$_error_reporting = error_reporting();
		error_reporting($_error_reporting & ~E_NOTICE & ~E_WARNING);
		$_dirpath = dirname($_filepath);
		// if subdirs, create dir structure
		if ($_dirpath !== '.') {
			$i = 0;
			// loop if concurrency problem occurs
			// see https://bugs.php.net/bug.php?id=35326
			while (!is_dir($_dirpath)) {
				if (@mkdir($_dirpath, 0777, true)) {
					break;
				}
				clearstatcache();
				if (++$i === 3) {
					error_reporting($_error_reporting);
					throw new Exception("unable to create directory {$_dirpath}");
				}
				sleep(1);
			}
		}
		// write to tmp file, then move to overt file lock race condition
		$_tmp_file = $_dirpath . DIRECTORY_SEPARATOR . str_replace(['.', ','], '_', uniqid('wrt', true));
		if (!file_put_contents($_tmp_file, $_contents)) {
			error_reporting($_error_reporting);
			throw new Exception("unable to write file {$_tmp_file}");
		}
		/*
		 * Windows' rename() fails if the destination exists,
		 * Linux' rename() properly handles the overwrite.
		 * Simply unlink()ing a file might cause other processes
		 * currently reading that file to fail, but linux' rename()
		 * seems to be smart enough to handle that for us.
		 */
		if (\Smarty\Smarty::$_IS_WINDOWS) {
			// remove original file
			if (is_file($_filepath)) {
				@unlink($_filepath);
			}
			// rename tmp file
			$success = @rename($_tmp_file, $_filepath);
		} else {
			// rename tmp file
			$success = @rename($_tmp_file, $_filepath);
			if (!$success) {
				// remove original file
				if (is_file($_filepath)) {
					@unlink($_filepath);
				}
				// rename tmp file
				$success = @rename($_tmp_file, $_filepath);
			}
		}
		if (!$success) {
			error_reporting($_error_reporting);
			throw new Exception("unable to write file {$_filepath}");
		}
		// set file permissions
		@chmod($_filepath, 0666 & ~umask());
		error_reporting($_error_reporting);
		return true;
	}

	private $runtimes = [];

	/**
	 * Loads and returns a runtime extension or null if not found
	 *
	 * @param string $type
	 *
	 * @return object|null
	 */
	public function getRuntime(string $type) {

		if (isset($this->runtimes[$type])) {
			return $this->runtimes[$type];
		}

		// Lazy load runtimes when/if needed
		switch ($type) {
			case 'Capture':
				return $this->runtimes[$type] = new CaptureRuntime();
			case 'Foreach':
				return $this->runtimes[$type] = new ForeachRuntime();
			case 'Inheritance':
				return $this->runtimes[$type] = new InheritanceRuntime();
			case 'TplFunction':
				return $this->runtimes[$type] = new TplFunctionRuntime();
			case 'DefaultPluginHandler':
				return $this->runtimes[$type] = new DefaultPluginHandlerRuntime(
					$this->getDefaultPluginHandlerFunc()
				);
		}

		throw new \Smarty\Exception('Trying to load invalid runtime ' . $type);
	}

	/**
	 * Indicates if a runtime is available.
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function hasRuntime(string $type): bool {
		try {
			$this->getRuntime($type);
			return true;
		} catch (\Smarty\Exception $e) {
			return false;
		}
	}

	/**
	 * @return callable|null
	 */
	public function getDefaultPluginHandlerFunc(): ?callable {
		return $this->default_plugin_handler_func;
	}

	/**
	 * load a filter of specified type and name
	 *
	 * @param string $type filter type
	 * @param string $name filter name
	 *
	 * @return bool
	 * @throws \Smarty\Exception
	 * @api  Smarty::loadFilter()
	 *
	 * @deprecated since 5.0
	 */
	public function loadFilter($type, $name) {

		if ($type == \Smarty\Smarty::FILTER_VARIABLE) {
			foreach ($this->getExtensions() as $extension) {
				if ($extension->getModifierCallback($name)) {

					trigger_error('Using Smarty::loadFilter() to load variable filters is deprecated and will ' .
						'be removed in a future release. Use Smarty::addDefaultModifiers() to add a modifier.',
						E_USER_DEPRECATED);

					$this->addDefaultModifiers([$name]);
					return true;
				}
			}
		}

		trigger_error('Using Smarty::loadFilter() to load filters is deprecated and will be ' .
			'removed in a future release. Use Smarty::addExtension() to add an extension or Smarty::registerFilter to ' .
			'quickly register a filter using a callback function.', E_USER_DEPRECATED);

		if ($type == \Smarty\Smarty::FILTER_OUTPUT && $name == 'trimwhitespace') {
			$this->BCPluginsAdapter->addOutputFilter(new TrimWhitespace());
			return true;
		}

		$_plugin = "smarty_{$type}filter_{$name}";
		if (!is_callable($_plugin) && class_exists($_plugin, false)) {
			$_plugin = [$_plugin, 'execute'];
		}

		if (is_callable($_plugin)) {
			$this->registerFilter($type, $_plugin, $name);
			return true;
		}

		throw new Exception("{$type}filter '{$name}' not found or callable");
	}

	/**
	 * load a filter of specified type and name
	 *
	 * @param string $type filter type
	 * @param string $name filter name
	 *
	 * @return static
	 * @throws \Smarty\Exception
	 * @api  Smarty::unloadFilter()
	 *
	 *
	 * @deprecated since 5.0
	 */
	public function unloadFilter($type, $name) {
		trigger_error('Using Smarty::unloadFilter() to unload filters is deprecated and will be ' .
			'removed in a future release. Use Smarty::addExtension() to add an extension or Smarty::(un)registerFilter to ' .
			'quickly (un)register a filter using a callback function.', E_USER_DEPRECATED);

		return $this->unregisterFilter($type, $name);
	}

	private $_caching_type = 'file';

	/**
	 * @param $type
	 *
	 * @return void
	 * @deprecated since 5.0
	 */
	public function setCachingType($type) {
		trigger_error('Using Smarty::setCachingType() is deprecated and will be ' .
			'removed in a future release. Use Smarty::setCacheResource() instead.', E_USER_DEPRECATED);
		$this->_caching_type = $type;
		$this->activateBCCacheResource();
	}

	/**
	 * @return string
	 * @deprecated since 5.0
	 */
	public function getCachingType(): string {
		trigger_error('Using Smarty::getCachingType() is deprecated and will be ' .
			'removed in a future release.', E_USER_DEPRECATED);
		return $this->_caching_type;
	}

	/**
	 * Registers a resource to fetch a template
	 *
	 * @param string $name name of resource type
	 * @param Base $resource_handler
	 *
	 * @return static
	 *
	 * @api  Smarty::registerCacheResource()
	 *
	 * @deprecated since 5.0
	 */
	public function registerCacheResource($name, \Smarty\Cacheresource\Base $resource_handler) {

		trigger_error('Using Smarty::registerCacheResource() is deprecated and will be ' .
			'removed in a future release. Use Smarty::setCacheResource() instead.', E_USER_DEPRECATED);

		$this->registered_cache_resources[$name] = $resource_handler;
		$this->activateBCCacheResource();
		return $this;
	}

	/**
	 * Unregisters a resource to fetch a template
	 *
	 * @param                                                                 $name
	 *
	 * @return static
	 * @api  Smarty::unregisterCacheResource()
	 *
	 * @deprecated since 5.0
	 *
	 */
	public function unregisterCacheResource($name) {

		trigger_error('Using Smarty::unregisterCacheResource() is deprecated and will be ' .
			'removed in a future release.', E_USER_DEPRECATED);

		if (isset($this->registered_cache_resources[$name])) {
			unset($this->registered_cache_resources[$name]);
		}
		return $this;
	}

	private function activateBCCacheResource() {
		if ($this->_caching_type == 'file') {
			$this->setCacheResource(new File());
		}
		if (isset($this->registered_cache_resources[$this->_caching_type])) {
			$this->setCacheResource($this->registered_cache_resources[$this->_caching_type]);
		}
	}

	/**
	 * Registers a filter function
	 *
	 * @param string $type filter type
	 * @param callable $callback
	 * @param string|null $name optional filter name
	 *
	 * @return static
	 * @throws \Smarty\Exception
	 *
	 * @api  Smarty::registerFilter()
	 */
	public function registerFilter($type, $callback, $name = null) {
		$name = $name ?? $this->_getFilterName($callback);
		if (!is_callable($callback)) {
			throw new Exception("{$type}filter '{$name}' not callable");
		}
		switch ($type) {
			case 'variable':
				$this->registerPlugin(self::PLUGIN_MODIFIER, $name, $callback);
				trigger_error('Using Smarty::registerFilter() to register variable filters is deprecated and ' .
					'will be removed in a future release. Use Smarty::addDefaultModifiers() to add a modifier.',
					E_USER_DEPRECATED);

				$this->addDefaultModifiers([$name]);
				break;
			case 'output':
				$this->BCPluginsAdapter->addCallableAsOutputFilter($callback, $name);
				break;
			case 'pre':
				$this->BCPluginsAdapter->addCallableAsPreFilter($callback, $name);
				break;
			case 'post':
				$this->BCPluginsAdapter->addCallableAsPostFilter($callback, $name);
				break;
			default:
				throw new Exception("Illegal filter type '{$type}'");
		}

		return $this;
	}

	/**
	 * Return internal filter name
	 *
	 * @param callback $callable
	 *
	 * @return string|null   internal filter name or null if callable cannot be serialized
	 */
	private function _getFilterName($callable) {
		if (is_array($callable)) {
			$_class_name = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
			return $_class_name . '_' . $callable[1];
		} elseif (is_string($callable)) {
			return $callable;
		}
		return null;
	}

	/**
	 * Unregisters a filter function. Smarty cannot unregister closures/anonymous functions if
	 * no name was given in ::registerFilter.
	 *
	 * @param string $type filter type
	 * @param callback|string $name the name previously used in ::registerFilter
	 *
	 * @return static
	 * @throws \Smarty\Exception
	 * @api  Smarty::unregisterFilter()
	 *
	 *
	 */
	public function unregisterFilter($type, $name) {

		if (!is_string($name)) {
			$name = $this->_getFilterName($name);
		}

		if ($name) {
			switch ($type) {
				case 'output':
					$this->BCPluginsAdapter->removeOutputFilter($name);
					break;
				case 'pre':
					$this->BCPluginsAdapter->removePreFilter($name);
					break;
				case 'post':
					$this->BCPluginsAdapter->removePostFilter($name);
					break;
				default:
					throw new Exception("Illegal filter type '{$type}'");
			}
		}

		return $this;
	}

	/**
	 * Add default modifiers
	 *
	 * @param array|string $modifiers modifier or list of modifiers
	 *                                                                                   to add
	 *
	 * @return static
	 * @api Smarty::addDefaultModifiers()
	 *
	 */
	public function addDefaultModifiers($modifiers) {
		if (is_array($modifiers)) {
			$this->default_modifiers = array_merge($this->default_modifiers, $modifiers);
		} else {
			$this->default_modifiers[] = $modifiers;
		}
		return $this;
	}

	/**
	 * Get default modifiers
	 *
	 * @return array list of default modifiers
	 * @api Smarty::getDefaultModifiers()
	 *
	 */
	public function getDefaultModifiers() {
		return $this->default_modifiers;
	}

	/**
	 * Set default modifiers
	 *
	 * @param array|string $modifiers modifier or list of modifiers
	 *                                                                                   to set
	 *
	 * @return static
	 * @api Smarty::setDefaultModifiers()
	 *
	 */
	public function setDefaultModifiers($modifiers) {
		$this->default_modifiers = (array)$modifiers;
		return $this;
	}

	/**
	 * @return Cacheresource\Base
	 */
	public function getCacheResource(): Cacheresource\Base {
		return $this->cacheResource;
	}

	/**
	 * @param Cacheresource\Base $cacheResource
	 */
	public function setCacheResource(Cacheresource\Base $cacheResource): void {
		$this->cacheResource = $cacheResource;
	}

	/**
	 * fetches a rendered Smarty template
	 *
	 * @param string $template the resource handle of the template file or template object
	 * @param mixed $cache_id cache id to be used with this template
	 * @param mixed $compile_id compile id to be used with this template
	 *
	 * @return string rendered template output
	 * @throws Exception
	 * @throws Exception
	 */
	public function fetch($template = null, $cache_id = null, $compile_id = null) {
		return $this->returnOrCreateTemplate($template, $cache_id, $compile_id)->fetch();
	}

	/**
	 * displays a Smarty template
	 *
	 * @param string $template the resource handle of the template file or template object
	 * @param mixed $cache_id cache id to be used with this template
	 * @param mixed $compile_id compile id to be used with this template
	 *
	 * @throws \Exception
	 * @throws \Smarty\Exception
	 */
	public function display($template = null, $cache_id = null, $compile_id = null) {
		$this->returnOrCreateTemplate($template, $cache_id, $compile_id)->display();
	}

	/**
	 * @param $resource_name
	 * @param $cache_id
	 * @param $compile_id
	 * @param $parent
	 * @param $caching
	 * @param $cache_lifetime
	 * @param bool $isConfig
	 * @param array $data
	 *
	 * @return Template
	 * @throws Exception
	 */
	public function doCreateTemplate(
		$resource_name,
		$cache_id = null,
		$compile_id = null,
		$parent = null,
		$caching = null,
		$cache_lifetime = null,
		bool $isConfig = false,
		array $data = []): Template {

		if (!$this->_templateDirNormalized) {
			$this->_normalizeTemplateConfig(false);
		}

		$_templateId = $this->generateUniqueTemplateId($resource_name, $cache_id, $compile_id, $caching);

		if (!isset($this->templates[$_templateId])) {
			$newTemplate = new Template($resource_name, $this, $parent ?: $this, $cache_id, $compile_id, $caching, $isConfig);
			$newTemplate->templateId = $_templateId; // @TODO this could go in constructor ^?
			$this->templates[$_templateId] = $newTemplate;
		}

		$tpl = clone $this->templates[$_templateId];

		$tpl->setParent($parent ?: $this);

		if ($cache_lifetime) {
			$tpl->setCacheLifetime($cache_lifetime);
		}

		// fill data if present
		foreach ($data as $_key => $_val) {
			$tpl->assign($_key, $_val);
		}

		$tpl->tplFunctions = array_merge($parent->tplFunctions ?? [], $tpl->tplFunctions ?? []);

		if (!$this->debugging && $this->debugging_ctrl === 'URL') {
			$tpl->getSmarty()->getDebug()->debugUrl($tpl->getSmarty());
		}
		return $tpl;
	}

	/**
	 * test if cache is valid
	 *
	 * @param null|string|Template $template the resource handle of the template file or template
	 *                                                          object
	 * @param mixed $cache_id cache id to be used with this template
	 * @param mixed $compile_id compile id to be used with this template
	 *
	 * @return bool cache status
	 * @throws \Exception
	 * @throws \Smarty\Exception
	 *
	 * @api  Smarty::isCached()
	 */
	public function isCached($template = null, $cache_id = null, $compile_id = null) {
		return $this->returnOrCreateTemplate($template, $cache_id, $compile_id)->isCached();
	}

	/**
	 * @param $template
	 * @param $cache_id
	 * @param $compile_id
	 * @param $parent
	 *
	 * @return Template
	 * @throws Exception
	 */
	private function returnOrCreateTemplate($template, $cache_id = null, $compile_id = null) {
		if (!($template instanceof Template)) {
			$template = $this->createTemplate($template, $cache_id, $compile_id, $this);
			$template->caching = $this->caching;
		}
		return $template;
	}

	/**
	 * Sets if Smarty should check If-Modified-Since headers to determine cache validity.
	 * @param bool $cache_modified_check
	 * @return void
	 */
	public function setCacheModifiedCheck($cache_modified_check): void {
		$this->cache_modified_check = (bool) $cache_modified_check;
	}

}

