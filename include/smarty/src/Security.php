<?php
/**
 * Smarty plugin
 *


 * @author     Uwe Tews
 */

/**
 * FIXME: \Smarty\Security API
 *      - getter and setter instead of public properties would allow cultivating an internal cache properly
 *      - current implementation of isTrustedResourceDir() assumes that Smarty::$template_dir and Smarty::$config_dir
 *      are immutable the cache is killed every time either of the variables change. That means that two distinct
 *      Smarty objects with differing
 *        $template_dir or $config_dir should NOT share the same \Smarty\Security instance,
 *        as this would lead to (severe) performance penalty! how should this be handled?
 */

namespace Smarty;

use Smarty\Exception;

/**
 * This class does contain the security settings
 */
#[\AllowDynamicProperties]
class Security {

	/**
	 * This is the list of template directories that are considered secure.
	 * $template_dir is in this list implicitly.
	 *
	 * @var array
	 */
	public $secure_dir = [];

	/**
	 * List of regular expressions (PCRE) that include trusted URIs
	 *
	 * @var array
	 */
	public $trusted_uri = [];

	/**
	 * List of trusted constants names
	 *
	 * @var array
	 */
	public $trusted_constants = [];

	/**
	 * This is an array of trusted static classes.
	 * If empty access to all static classes is allowed.
	 * If set to 'none' none is allowed.
	 *
	 * @var array
	 */
	public $static_classes = [];

	/**
	 * This is an nested array of trusted classes and static methods.
	 * If empty access to all static classes and methods is allowed.
	 * Format:
	 * array (
	 *         'class_1' => array('method_1', 'method_2'), // allowed methods listed
	 *         'class_2' => array(),                       // all methods of class allowed
	 *       )
	 * If set to null none is allowed.
	 *
	 * @var array
	 */
	public $trusted_static_methods = [];

	/**
	 * This is an array of trusted static properties.
	 * If empty access to all static classes and properties is allowed.
	 * Format:
	 * array (
	 *         'class_1' => array('prop_1', 'prop_2'), // allowed properties listed
	 *         'class_2' => array(),                   // all properties of class allowed
	 *       )
	 * If set to null none is allowed.
	 *
	 * @var array
	 */
	public $trusted_static_properties = [];

	/**
	 * This is an array of allowed tags.
	 * If empty no restriction by allowed_tags.
	 *
	 * @var array
	 */
	public $allowed_tags = [];

	/**
	 * This is an array of disabled tags.
	 * If empty no restriction by disabled_tags.
	 *
	 * @var array
	 */
	public $disabled_tags = [];

	/**
	 * This is an array of allowed modifier plugins.
	 * If empty no restriction by allowed_modifiers.
	 *
	 * @var array
	 */
	public $allowed_modifiers = [];

	/**
	 * This is an array of disabled modifier plugins.
	 * If empty no restriction by disabled_modifiers.
	 *
	 * @var array
	 */
	public $disabled_modifiers = [];

	/**
	 * This is an array of disabled special $smarty variables.
	 *
	 * @var array
	 */
	public $disabled_special_smarty_vars = [];

	/**
	 * This is an array of trusted streams.
	 * If empty all streams are allowed.
	 * To disable all streams set $streams = null.
	 *
	 * @var array
	 */
	public $streams = ['file'];

	/**
	 * + flag if constants can be accessed from template
	 *
	 * @var boolean
	 */
	public $allow_constants = true;

	/**
	 * + flag if super globals can be accessed from template
	 *
	 * @var boolean
	 */
	public $allow_super_globals = true;

	/**
	 * max template nesting level
	 *
	 * @var int
	 */
	public $max_template_nesting = 0;

	/**
	 * current template nesting level
	 *
	 * @var int
	 */
	private $_current_template_nesting = 0;

	/**
	 * Cache for $resource_dir lookup
	 *
	 * @var array
	 */
	protected $_resource_dir = [];

	/**
	 * Cache for $template_dir lookup
	 *
	 * @var array
	 */
	protected $_template_dir = [];

	/**
	 * Cache for $config_dir lookup
	 *
	 * @var array
	 */
	protected $_config_dir = [];

	/**
	 * Cache for $secure_dir lookup
	 *
	 * @var array
	 */
	protected $_secure_dir = [];

	/**
	 * @param Smarty $smarty
	 */
	public function __construct(Smarty $smarty) {
		$this->smarty = $smarty;
	}

	/**
	 * Check if static class is trusted.
	 *
	 * @param string $class_name
	 * @param object $compiler compiler object
	 *
	 * @return boolean                 true if class is trusted
	 */
	public function isTrustedStaticClass($class_name, $compiler) {
		if (isset($this->static_classes)
			&& (empty($this->static_classes) || in_array($class_name, $this->static_classes))
		) {
			return true;
		}
		$compiler->trigger_template_error("access to static class '{$class_name}' not allowed by security setting");
		return false; // should not, but who knows what happens to the compiler in the future?
	}

	/**
	 * Check if static class method/property is trusted.
	 *
	 * @param string $class_name
	 * @param string $params
	 * @param object $compiler compiler object
	 *
	 * @return boolean                 true if class method is trusted
	 */
	public function isTrustedStaticClassAccess($class_name, $params, $compiler) {
		if (!isset($params[2])) {
			// fall back
			return $this->isTrustedStaticClass($class_name, $compiler);
		}
		if ($params[2] === 'method') {
			$allowed = $this->trusted_static_methods;
			$name = substr($params[0], 0, strpos($params[0], '('));
		} else {
			$allowed = $this->trusted_static_properties;
			// strip '$'
			$name = substr($params[0], 1);
		}
		if (isset($allowed)) {
			if (empty($allowed)) {
				// fall back
				return $this->isTrustedStaticClass($class_name, $compiler);
			}
			if (isset($allowed[$class_name])
				&& (empty($allowed[$class_name]) || in_array($name, $allowed[$class_name]))
			) {
				return true;
			}
		}
		$compiler->trigger_template_error("access to static class '{$class_name}' {$params[2]} '{$name}' not allowed by security setting");
		return false; // should not, but who knows what happens to the compiler in the future?
	}

	/**
	 * Check if tag is trusted.
	 *
	 * @param string $tag_name
	 * @param object $compiler compiler object
	 *
	 * @return boolean                 true if tag is trusted
	 */
	public function isTrustedTag($tag_name, $compiler) {
		$tag_name = strtolower($tag_name);

		// check for internal always required tags
		if (in_array($tag_name,	['assign', 'call'])) {
			return true;
		}
		// check security settings
		if (empty($this->allowed_tags)) {
			if (empty($this->disabled_tags) || !in_array($tag_name, $this->disabled_tags)) {
				return true;
			} else {
				$compiler->trigger_template_error("tag '{$tag_name}' disabled by security setting", null, true);
			}
		} elseif (in_array($tag_name, $this->allowed_tags) && !in_array($tag_name, $this->disabled_tags)) {
			return true;
		} else {
			$compiler->trigger_template_error("tag '{$tag_name}' not allowed by security setting", null, true);
		}
		return false; // should not, but who knows what happens to the compiler in the future?
	}

	/**
	 * Check if special $smarty variable is trusted.
	 *
	 * @param string $var_name
	 * @param object $compiler compiler object
	 *
	 * @return boolean                 true if tag is trusted
	 */
	public function isTrustedSpecialSmartyVar($var_name, $compiler) {
		if (!in_array($var_name, $this->disabled_special_smarty_vars)) {
			return true;
		} else {
			$compiler->trigger_template_error(
				"special variable '\$smarty.{$var_name}' not allowed by security setting",
				null,
				true
			);
		}
		return false; // should not, but who knows what happens to the compiler in the future?
	}

	/**
	 * Check if modifier plugin is trusted.
	 *
	 * @param string $modifier_name
	 * @param object $compiler compiler object
	 *
	 * @return boolean                 true if tag is trusted
	 */
	public function isTrustedModifier($modifier_name, $compiler) {
		// check for internal always allowed modifier
		if (in_array($modifier_name, ['default'])) {
			return true;
		}
		// check security settings
		if (empty($this->allowed_modifiers)) {
			if (empty($this->disabled_modifiers) || !in_array($modifier_name, $this->disabled_modifiers)) {
				return true;
			} else {
				$compiler->trigger_template_error(
					"modifier '{$modifier_name}' disabled by security setting",
					null,
					true
				);
			}
		} elseif (in_array($modifier_name, $this->allowed_modifiers)
			&& !in_array($modifier_name, $this->disabled_modifiers)
		) {
			return true;
		} else {
			$compiler->trigger_template_error(
				"modifier '{$modifier_name}' not allowed by security setting",
				null,
				true
			);
		}
		return false; // should not, but who knows what happens to the compiler in the future?
	}

	/**
	 * Check if constants are enabled or trusted
	 *
	 * @param string $const constant name
	 * @param object $compiler compiler object
	 *
	 * @return bool
	 */
	public function isTrustedConstant($const, $compiler) {
		if (in_array($const, ['true', 'false', 'null'])) {
			return true;
		}
		if (!empty($this->trusted_constants)) {
			if (!in_array(strtolower($const), $this->trusted_constants)) {
				$compiler->trigger_template_error("Security: access to constant '{$const}' not permitted");
				return false;
			}
			return true;
		}
		if ($this->allow_constants) {
			return true;
		}
		$compiler->trigger_template_error("Security: access to constants not permitted");
		return false;
	}

	/**
	 * Check if stream is trusted.
	 *
	 * @param string $stream_name
	 *
	 * @return boolean         true if stream is trusted
	 * @throws Exception if stream is not trusted
	 */
	public function isTrustedStream($stream_name) {
		if (isset($this->streams) && (empty($this->streams) || in_array($stream_name, $this->streams))) {
			return true;
		}
		throw new Exception("stream '{$stream_name}' not allowed by security setting");
	}

	/**
	 * Check if directory of file resource is trusted.
	 *
	 * @param string $filepath
	 * @param null|bool $isConfig
	 *
	 * @return bool true if directory is trusted
	 * @throws \Smarty\Exception if directory is not trusted
	 */
	public function isTrustedResourceDir($filepath, $isConfig = null) {
		$_dir = $this->smarty->getTemplateDir();
		if ($this->_template_dir !== $_dir) {
			$this->_updateResourceDir($this->_template_dir, $_dir);
			$this->_template_dir = $_dir;
		}
		$_dir = $this->smarty->getConfigDir();
		if ($this->_config_dir !== $_dir) {
			$this->_updateResourceDir($this->_config_dir, $_dir);
			$this->_config_dir = $_dir;
		}
		if ($this->_secure_dir !== $this->secure_dir) {
			$this->secure_dir = (array)$this->secure_dir;
			foreach ($this->secure_dir as $k => $d) {
				$this->secure_dir[$k] = $this->smarty->_realpath($d . DIRECTORY_SEPARATOR, true);
			}
			$this->_updateResourceDir($this->_secure_dir, $this->secure_dir);
			$this->_secure_dir = $this->secure_dir;
		}
		$addPath = $this->_checkDir($filepath, $this->_resource_dir);
		if ($addPath !== false) {
			$this->_resource_dir = array_merge($this->_resource_dir, $addPath);
		}
		return true;
	}

	/**
	 * Check if URI (e.g. {fetch} or {html_image}) is trusted
	 * To simplify things, isTrustedUri() resolves all input to "{$PROTOCOL}://{$HOSTNAME}".
	 * So "http://username:password@hello.world.example.org:8080/some-path?some=query-string"
	 * is reduced to "http://hello.world.example.org" prior to applying the patters from {@link $trusted_uri}.
	 *
	 * @param string $uri
	 *
	 * @return boolean         true if URI is trusted
	 * @throws Exception if URI is not trusted
	 * @uses   $trusted_uri for list of patterns to match against $uri
	 */
	public function isTrustedUri($uri) {
		$_uri = parse_url($uri);
		if (!empty($_uri['scheme']) && !empty($_uri['host'])) {
			$_uri = $_uri['scheme'] . '://' . $_uri['host'];
			foreach ($this->trusted_uri as $pattern) {
				if (preg_match($pattern, $_uri)) {
					return true;
				}
			}
		}
		throw new Exception("URI '{$uri}' not allowed by security setting");
	}

	/**
	 * Remove old directories and its sub folders, add new directories
	 *
	 * @param array $oldDir
	 * @param array $newDir
	 */
	private function _updateResourceDir($oldDir, $newDir) {
		foreach ($oldDir as $directory) {
			//           $directory = $this->smarty->_realpath($directory, true);
			$length = strlen($directory);
			foreach ($this->_resource_dir as $dir) {
				if (substr($dir, 0, $length) === $directory) {
					unset($this->_resource_dir[$dir]);
				}
			}
		}
		foreach ($newDir as $directory) {
			//           $directory = $this->smarty->_realpath($directory, true);
			$this->_resource_dir[$directory] = true;
		}
	}

	/**
	 * Check if file is inside a valid directory
	 *
	 * @param string $filepath
	 * @param array $dirs valid directories
	 *
	 * @return array|bool
	 * @throws \Smarty\Exception
	 */
	private function _checkDir($filepath, $dirs) {
		$directory = dirname($this->smarty->_realpath($filepath, true)) . DIRECTORY_SEPARATOR;
		$_directory = [];
		if (!preg_match('#[\\\\/][.][.][\\\\/]#', $directory)) {
			while (true) {
				// test if the directory is trusted
				if (isset($dirs[$directory])) {
					return $_directory;
				}
				// abort if we've reached root
				if (!preg_match('#[\\\\/][^\\\\/]+[\\\\/]$#', $directory)) {
					// give up
					break;
				}
				// remember the directory to add it to _resource_dir in case we're successful
				$_directory[$directory] = true;
				// bubble up one level
				$directory = preg_replace('#[\\\\/][^\\\\/]+[\\\\/]$#', DIRECTORY_SEPARATOR, $directory);
			}
		}
		// give up
		throw new Exception(sprintf('Smarty Security: not trusted file path \'%s\' ', $filepath));
	}

	/**
	 * Loads security class and enables security
	 *
	 * @param \Smarty $smarty
	 * @param string|Security $security_class if a string is used, it must be class-name
	 *
	 * @return \Smarty current Smarty instance for chaining
	 * @throws \Smarty\Exception when an invalid class name is provided
	 */
	public static function enableSecurity(Smarty $smarty, $security_class) {
		if ($security_class instanceof Security) {
			$smarty->security_policy = $security_class;
			return $smarty;
		} elseif (is_object($security_class)) {
			throw new Exception("Class '" . get_class($security_class) . "' must extend \\Smarty\\Security.");
		}
		if ($security_class === null) {
			$security_class = $smarty->security_class;
		}
		if (!class_exists($security_class)) {
			throw new Exception("Security class '$security_class' is not defined");
		} elseif ($security_class !== Security::class && !is_subclass_of($security_class, Security::class)) {
			throw new Exception("Class '$security_class' must extend " . Security::class . ".");
		} else {
			$smarty->security_policy = new $security_class($smarty);
		}
		return $smarty;
	}

	/**
	 * Start template processing
	 *
	 * @param $template
	 *
	 * @throws Exception
	 */
	public function startTemplate($template) {
		if ($this->max_template_nesting > 0 && $this->_current_template_nesting++ >= $this->max_template_nesting) {
			throw new Exception("maximum template nesting level of '{$this->max_template_nesting}' exceeded when calling '{$template->template_resource}'");
		}
	}

	/**
	 * Exit template processing
	 */
	public function endTemplate() {
		if ($this->max_template_nesting > 0) {
			$this->_current_template_nesting--;
		}
	}

	/**
	 * Register callback functions call at start/end of template rendering
	 *
	 * @param \Smarty\Template $template
	 */
	public function registerCallBacks(Template $template) {
		$template->startRenderCallbacks[] = [$this, 'startTemplate'];
		$template->endRenderCallbacks[] = [$this, 'endTemplate'];
	}
}
