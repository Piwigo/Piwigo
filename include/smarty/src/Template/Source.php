<?php

namespace Smarty\Template;

use Smarty\Resource\FilePlugin;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Exception;

/**
 * Meta-data Container for Template source files
 * @author     Rodney Rehm
 */
class Source {

	/**
	 * Unique Template ID
	 *
	 * @var string|null
	 */
	public $uid = null;

	/**
	 * Template Resource (\Smarty\Template::$template_resource)
	 *
	 * @var string
	 */
	public $resource = null;

	/**
	 * Resource Type
	 *
	 * @var string
	 */
	public $type = null;

	/**
	 * Resource Name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Source Timestamp
	 *
	 * @var int
	 */
	public $timestamp = null;

	/**
	 * Source Existence
	 *
	 * @var boolean
	 */
	public $exists = false;

	/**
	 * Source File Base name
	 *
	 * @var string
	 */
	public $basename = null;

	/**
	 * The Components an extended template is made of
	 *
	 * @var \Smarty\Template\Source[]
	 */
	public $components = null;

	/**
	 * Resource Handler
	 *
	 * @var \Smarty\Resource\BasePlugin
	 */
	public $handler = null;

	/**
	 * Smarty instance
	 *
	 * @var Smarty
	 */
	protected $smarty = null;

	/**
	 * Resource is source
	 *
	 * @var bool
	 */
	public $isConfig = false;

	/**
	 * Template source content eventually set by default handler
	 *
	 * @var string
	 */
	public $content = null;

	/**
	 * @var array
	 */
	static protected $_incompatible_resources = [];

	/**
	 * create Source Object container
	 *
	 * @param Smarty $smarty Smarty instance this source object belongs to
	 * @param string $resource full template_resource
	 * @param string $type type of resource
	 * @param string $name resource name
	 *
	 * @throws   \Smarty\Exception
	 * @internal param \Smarty\Resource\Base $handler Resource Handler this source object communicates with
	 */
	public function __construct(Smarty $smarty, $type, $name) {
		$this->handler = \Smarty\Resource\BasePlugin::load($smarty, $type);

		$this->smarty = $smarty;
		$this->resource = $type . ':' . $name;
		$this->type = $type;
		$this->name = $name;
	}

	/**
	 * initialize Source Object for given resource
	 * Either [$_template] or [$smarty, $template_resource] must be specified
	 *
	 * @param Template|null $_template template object
	 * @param Smarty|null $smarty smarty object
	 * @param null $template_resource resource identifier
	 *
	 * @return Source Source Object
	 * @throws Exception
	 */
	public static function load(
		?Template $_template = null,
		?Smarty   $smarty = null,
		          $template_resource = null
	) {
		if ($_template) {
			$smarty = $_template->getSmarty();
			$template_resource = $_template->template_resource;
		}
		if (empty($template_resource)) {
			throw new Exception('Source: Missing  name');
		}
		// parse resource_name, load resource handler, identify unique resource name
		if (preg_match('/^([A-Za-z0-9_\-]{2,}):([\s\S]*)$/', $template_resource, $match)) {
			$type = $match[1];
			$name = $match[2];
		} else {
			// no resource given, use default
			// or single character before the colon is not a resource type, but part of the filepath
			$type = $smarty->default_resource_type;
			$name = $template_resource;
		}

		if (isset(self::$_incompatible_resources[$type])) {
			throw new Exception("Unable to use resource '{$type}' for " . __METHOD__);
		}

		// create new source object
		$source = new static($smarty, $type, $name);
		$source->handler->populate($source, $_template);
		if (!$source->exists && static::getDefaultHandlerFunc($smarty)) {
			$source->_getDefaultTemplate(static::getDefaultHandlerFunc($smarty));
			$source->handler->populate($source, $_template);
		}
		return $source;
	}

	protected static function getDefaultHandlerFunc(Smarty $smarty) {
		return $smarty->default_template_handler_func;
	}

	/**
	 * Get source time stamp
	 *
	 * @return int
	 */
	public function getTimeStamp() {
		if (!isset($this->timestamp)) {
			$this->handler->populateTimestamp($this);
		}
		return $this->timestamp;
	}

	/**
	 * Get source content
	 *
	 * @return string
	 * @throws \Smarty\Exception
	 */
	public function getContent() {
		return $this->content ?? $this->handler->getContent($this);
	}

	/**
	 * get default content from template or config resource handler
	 *
	 * @throws \Smarty\Exception
	 */
	public function _getDefaultTemplate($default_handler) {
		$_content = $_timestamp = null;
		$_return = \call_user_func_array(
			$default_handler,
			[$this->type, $this->name, &$_content, &$_timestamp, $this->smarty]
		);
		if (is_string($_return)) {
			$this->exists = is_file($_return);
			if ($this->exists) {
				$this->timestamp = filemtime($_return);
			} else {
				throw new Exception(
					'Default handler: Unable to load ' .
					"default file '{$_return}' for '{$this->type}:{$this->name}'"
				);
			}
			$this->name = $_return;
			$this->uid = sha1($_return);
		} elseif ($_return === true) {
			$this->content = $_content;
			$this->exists = true;
			$this->uid = $this->name = sha1($_content);
			$this->handler = \Smarty\Resource\BasePlugin::load($this->smarty, 'eval');
		} else {
			$this->exists = false;
			throw new Exception(
				'Default handler: No ' . ($this->isConfig ? 'config' : 'template') .
				" default content for '{$this->type}:{$this->name}'"
			);
		}
	}

	public function createCompiler(): \Smarty\Compiler\BaseCompiler {
		return new \Smarty\Compiler\Template($this->smarty);
	}

	public function getSmarty() {
		return $this->smarty;
	}

	/**
	 * Determine basename for compiled filename
	 *
	 * @return string                 resource's basename
	 */
	public function getBasename()
	{
		return $this->handler->getBasename($this);
	}

	/**
	 * Return source name
	 * e.g.: 'sub/index.tpl'
	 *
	 * @return string
	 */
	public function getResourceName(): string {
		return (string) $this->name;
	}

	/**
	 * Return source name, including the type prefix.
	 * e.g.: 'file:sub/index.tpl'
	 *
	 * @return string
	 */
	public function getFullResourceName(): string {
		return $this->type . ':' . $this->name;
	}

	public function getFilepath(): ?string {
		if ($this->handler instanceof FilePlugin) {
			return $this->handler->getFilePath($this->name, $this->smarty, $this->isConfig);
		}
		return null;
	}

	public function isConfig(): bool {
		return $this->isConfig;
	}

}
