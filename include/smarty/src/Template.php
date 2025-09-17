<?php
/**
 * Smarty Internal Plugin Template
 * This file contains the Smarty template engine
 *


 * @author     Uwe Tews
 */

namespace Smarty;

use Smarty\Resource\BasePlugin;
use Smarty\Runtime\InheritanceRuntime;
use Smarty\Template\Source;
use Smarty\Template\Cached;
use Smarty\Template\Compiled;
use Smarty\Template\Config;

/**
 * Main class with template data structures and methods
 */
#[\AllowDynamicProperties]
class Template extends TemplateBase {

	/**
	 * caching mode to create nocache code but no cache file
	 */
	public const CACHING_NOCACHE_CODE = 9999;

	/**
	 * @var Compiled
	 */
	private $compiled = null;

	/**
	 * @var Cached
	 */
	private $cached = null;

	/**
	 * @var \Smarty\Compiler\Template
	 */
	private $compiler = null;

	/**
	 * Source instance
	 *
	 * @var Source|Config
	 */
	private $source = null;

	/**
	 * Template resource
	 *
	 * @var string
	 */
	public $template_resource = null;

	/**
	 * Template ID
	 *
	 * @var null|string
	 */
	public $templateId = null;

	/**
	 * Callbacks called before rendering template
	 *
	 * @var callback[]
	 */
	public $startRenderCallbacks = [];

	/**
	 * Callbacks called after rendering template
	 *
	 * @var callback[]
	 */
	public $endRenderCallbacks = [];

	/**
	 * Template left-delimiter. If null, defaults to $this->getSmarty()-getLeftDelimiter().
	 *
	 * @var string
	 */
	private $left_delimiter = null;

	/**
	 * Template right-delimiter. If null, defaults to $this->getSmarty()-getRightDelimiter().
	 *
	 * @var string
	 */
	private $right_delimiter = null;

	/**
	 * @var InheritanceRuntime|null
	 */
	private $inheritance;

	/**
	 * Create template data object
	 * Some of the global Smarty settings copied to template scope
	 * It load the required template resources and caching plugins
	 *
	 * @param string $template_resource template resource string
	 * @param Smarty $smarty Smarty instance
	 * @param \Smarty\Data|null $_parent back pointer to parent object with variables or null
	 * @param mixed $_cache_id cache   id or null
	 * @param mixed $_compile_id compile id or null
	 * @param bool|int|null $_caching use caching?
	 * @param bool $_isConfig
	 *
	 * @throws \Smarty\Exception
	 */
	public function __construct(
		$template_resource,
		Smarty $smarty,
		?\Smarty\Data $_parent = null,
		$_cache_id = null,
		$_compile_id = null,
		$_caching = null,
		$_isConfig = false
	) {
		$this->smarty = $smarty;
		// Smarty parameter
		$this->cache_id = $_cache_id === null ? $this->smarty->cache_id : $_cache_id;
		$this->compile_id = $_compile_id === null ? $this->smarty->compile_id : $_compile_id;
		$this->caching = (int)($_caching === null ? $this->smarty->caching : $_caching);
		$this->cache_lifetime = $this->smarty->cache_lifetime;
		$this->compile_check = (int)$smarty->compile_check;
		$this->parent = $_parent;
		// Template resource
		$this->template_resource = $template_resource;

		$this->source = $_isConfig ? Config::load($this) : Source::load($this);
		$this->compiled = Compiled::load($this);

		if ($smarty->security_policy) {
			$smarty->security_policy->registerCallBacks($this);
		}
	}

	/**
	 * render template
	 *
	 * @param bool $no_output_filter if true do not run output filter
	 * @param null|bool $display true: display, false: fetch null: sub-template
	 *
	 * @return string
	 * @throws \Exception
	 * @throws \Smarty\Exception
	 */
	private function render($no_output_filter = true, $display = null) {
		if ($this->smarty->debugging) {
			$this->smarty->getDebug()->start_template($this, $display);
		}
		// checks if template exists
		if ($this->compile_check && !$this->getSource()->exists) {
			throw new Exception(
				"Unable to load '{$this->getSource()->type}:{$this->getSource()->name}'" .
				($this->_isSubTpl() ? " in '{$this->parent->template_resource}'" : '')
			);
		}

		// disable caching for evaluated code
		if ($this->getSource()->handler->recompiled) {
			$this->caching = \Smarty\Smarty::CACHING_OFF;
		}

		foreach ($this->startRenderCallbacks as $callback) {
			call_user_func($callback, $this);
		}

		try {

			// read from cache or render
			if ($this->caching === \Smarty\Smarty::CACHING_LIFETIME_CURRENT || $this->caching === \Smarty\Smarty::CACHING_LIFETIME_SAVED) {
				$this->getCached()->render($this, $no_output_filter);
			} else {
				$this->getCompiled()->render($this);
			}

		} finally {
			foreach ($this->endRenderCallbacks as $callback) {
				call_user_func($callback, $this);
			}
		}

		// display or fetch
		if ($display) {
			if ($this->caching && $this->smarty->cache_modified_check) {
				$this->smarty->cacheModifiedCheck(
					$this->getCached(),
					$this,
					isset($content) ? $content : ob_get_clean()
				);
			} else {
				if ((!$this->caching || $this->getCached()->getNocacheCode() || $this->getSource()->handler->recompiled)
					&& !$no_output_filter
				) {
					echo $this->smarty->runOutputFilters(ob_get_clean(), $this);
				} else {
					echo ob_get_clean();
				}
			}
			if ($this->smarty->debugging) {
				$this->smarty->getDebug()->end_template($this);
				// debug output
				$this->smarty->getDebug()->display_debug($this, true);
			}
			return '';
		} else {
			if ($this->smarty->debugging) {
				$this->smarty->getDebug()->end_template($this);
				if ($this->smarty->debugging === 2 && $display === false) {
					$this->smarty->getDebug()->display_debug($this, true);
				}
			}
			if (
				!$no_output_filter
				&& (!$this->caching || $this->getCached()->getNocacheCode() || $this->getSource()->handler->recompiled)
			) {

				return $this->smarty->runOutputFilters(ob_get_clean(), $this);
			}
			// return cache content
			return null;
		}
	}

	/**
	 * Runtime function to render sub-template
	 *
	 * @param string $template_name template name
	 * @param mixed $cache_id cache id
	 * @param mixed $compile_id compile id
	 * @param integer $caching cache mode
	 * @param integer $cache_lifetime lifetime of cache data
	 * @param array $extra_vars passed parameter template variables
	 * @param int|null $scope
	 *
	 * @throws Exception
	 */
	public function renderSubTemplate(
		$template_name,
		$cache_id,
		$compile_id,
		$caching,
		$cache_lifetime,
		array $extra_vars = [],
		?int $scope = null,
		?string $currentDir = null
	) {

		$name = $this->parseResourceName($template_name);
		if ($currentDir && preg_match('/^\.{1,2}\//', $name)) {
			// relative template resource name, append it to current template name
			$template_name = $currentDir . DIRECTORY_SEPARATOR . $name;
		}

		$tpl = $this->smarty->doCreateTemplate($template_name, $cache_id, $compile_id, $this, $caching, $cache_lifetime);

		$tpl->inheritance = $this->getInheritance(); // re-use the same Inheritance object inside the inheritance tree

		if ($scope) {
			$tpl->defaultScope = $scope;
		}

		if ($caching) {
			if ($tpl->templateId !== $this->templateId && $caching !== \Smarty\Template::CACHING_NOCACHE_CODE) {
				$tpl->getCached(true);
			} else {
				// re-use the same Cache object across subtemplates to gather hashes and file dependencies.
				$tpl->setCached($this->getCached());
			}
		}

		foreach ($extra_vars as $_key => $_val) {
			$tpl->assign($_key, $_val);
		}
		if ($tpl->caching === \Smarty\Template::CACHING_NOCACHE_CODE) {
			if ($tpl->getCompiled()->getNocacheCode()) {
				$this->getCached()->hashes[$tpl->getCompiled()->nocache_hash] = true;
			}
		}

		$tpl->render();
	}

	/**
	 * Remove type indicator from resource name if present.
	 * E.g. $this->parseResourceName('file:template.tpl') returns 'template.tpl'
	 *
	 * @note "C:/foo.tpl" was forced to file resource up till Smarty 3.1.3 (including).
	 *
	 * @param string $resource_name    template_resource or config_resource to parse
	 *
	 * @return string
	 */
	private function parseResourceName($resource_name): string {
		if (preg_match('/^([A-Za-z0-9_\-]{2,}):/', $resource_name, $match)) {
			return substr($resource_name, strlen($match[0]));
		}
		return $resource_name;
	}

	/**
	 * Check if this is a sub template
	 *
	 * @return bool true is sub template
	 */
	public function _isSubTpl() {
		return isset($this->parent) && $this->parent instanceof Template;
	}

	public function assign($tpl_var, $value = null, $nocache = false, $scope = null) {
		return parent::assign($tpl_var, $value, $nocache, $scope);
	}

	/**
	 * Compiles the template
	 * If the template is not evaluated the compiled template is saved on disk
	 *
	 * @TODO only used in compileAll and 1 unit test: can we move this and make compileAndWrite private?
	 *
	 * @throws \Exception
	 */
	public function compileTemplateSource() {
		return $this->getCompiled()->compileAndWrite($this);
	}

	/**
	 * Return cached content
	 *
	 * @return null|string
	 * @throws Exception
	 */
	public function getCachedContent() {
		return $this->getCached()->getContent($this);
	}

	/**
	 * Writes the content to cache resource
	 *
	 * @param string $content
	 *
	 * @return bool
	 *
	 * @TODO this method is only used in unit tests that (mostly) try to test CacheResources.
	 */
	public function writeCachedContent($content) {
		if ($this->getSource()->handler->recompiled || !$this->caching
		) {
			// don't write cache file
			return false;
		}
		$codeframe = $this->createCodeFrame($content, '', true);
		return $this->getCached()->writeCache($this, $codeframe);
	}

	/**
	 * Get unique template id
	 *
	 * @return string
	 */
	public function getTemplateId() {
		return $this->templateId;
	}

	/**
	 * runtime error not matching capture tags
	 *
	 * @throws \Smarty\Exception
	 */
	public function capture_error() {
		throw new Exception("Not matching {capture} open/close in '{$this->template_resource}'");
	}

	/**
	 * Return Compiled object
	 *
	 * @param bool $forceNew force new compiled object
	 */
	public function getCompiled($forceNew = false) {
		if ($forceNew || !isset($this->compiled)) {
			$this->compiled = Compiled::load($this);
		}
		return $this->compiled;
	}

	/**
	 * Return Cached object
	 *
	 * @param bool $forceNew force new cached object
	 *
	 * @throws Exception
	 */
	public function getCached($forceNew = false): Cached {
		if ($forceNew || !isset($this->cached)) {
			$cacheResource = $this->smarty->getCacheResource();
			$this->cached = new Cached(
				$this->source,
				$cacheResource,
				$this->compile_id,
				$this->cache_id
			);
			if ($this->isCachingEnabled()) {
				$cacheResource->populate($this->cached, $this);
			} else {
				$this->cached->setValid(false);
			}
		}
		return $this->cached;
	}

	private function isCachingEnabled(): bool {
		return $this->caching && !$this->getSource()->handler->recompiled;
	}

	/**
	 * Helper function for InheritanceRuntime object
	 *
	 * @return InheritanceRuntime
	 * @throws Exception
	 */
	public function getInheritance(): InheritanceRuntime {
		if (is_null($this->inheritance)) {
			$this->inheritance = clone $this->getSmarty()->getRuntime('Inheritance');
		}
		return $this->inheritance;
	}

	/**
	 * Sets a new InheritanceRuntime object.
	 *
	 * @param InheritanceRuntime $inheritanceRuntime
	 *
	 * @return void
	 */
	public function setInheritance(InheritanceRuntime $inheritanceRuntime) {
		$this->inheritance = $inheritanceRuntime;
	}

	/**
	 * Return Compiler object
	 */
	public function getCompiler() {
		if (!isset($this->compiler)) {
			$this->compiler = $this->getSource()->createCompiler();
		}
		return $this->compiler;
	}

	/**
	 * Create code frame for compiled and cached templates
	 *
	 * @param string $content optional template content
	 * @param string $functions compiled template function and block code
	 * @param bool $cache flag for cache file
	 * @param Compiler\Template|null $compiler
	 *
	 * @return string
	 * @throws Exception
	 */
	public function createCodeFrame($content = '', $functions = '', $cache = false, ?\Smarty\Compiler\Template $compiler = null) {
		return $this->getCodeFrameCompiler()->create($content, $functions, $cache, $compiler);
	}

	/**
	 * Template data object destructor
	 */
	public function __destruct() {
		if ($this->smarty->cache_locking && $this->getCached()->is_locked) {
			$this->getCached()->handler->releaseLock($this->smarty, $this->getCached());
		}
	}

	/**
	 * Returns if the current template must be compiled by the Smarty compiler
	 * It does compare the timestamps of template source and the compiled templates and checks the force compile
	 * configuration
	 *
	 * @return bool
	 * @throws \Smarty\Exception
	 */
	public function mustCompile(): bool {
		if (!$this->getSource()->exists) {
			if ($this->_isSubTpl()) {
				$parent_resource = " in '{$this->parent->template_resource}'";
			} else {
				$parent_resource = '';
			}
			throw new Exception("Unable to load {$this->getSource()->type} '{$this->getSource()->name}'{$parent_resource}");
		}

		// @TODO move this logic to Compiled
		return $this->smarty->force_compile
			|| $this->getSource()->handler->recompiled
			|| !$this->getCompiled()->exists
			|| ($this->compile_check &&	$this->getCompiled()->getTimeStamp() < $this->getSource()->getTimeStamp());
	}

	private function getCodeFrameCompiler(): Compiler\CodeFrame {
		return new \Smarty\Compiler\CodeFrame($this);
	}

	/**
	 * Get left delimiter
	 *
	 * @return string
	 */
	public function getLeftDelimiter()
	{
		return $this->left_delimiter ?? $this->getSmarty()->getLeftDelimiter();
	}

	/**
	 * Set left delimiter
	 *
	 * @param string $left_delimiter
	 */
	public function setLeftDelimiter($left_delimiter)
	{
		$this->left_delimiter = $left_delimiter;
	}

	/**
	 * Get right delimiter
	 *
	 * @return string $right_delimiter
	 */
	public function getRightDelimiter()
	{
		return $this->right_delimiter ?? $this->getSmarty()->getRightDelimiter();;
	}

	/**
	 * Set right delimiter
	 *
	 * @param string
	 */
	public function setRightDelimiter($right_delimiter)
	{
		$this->right_delimiter = $right_delimiter;
	}

	/**
	 * gets  a stream variable
	 *
	 * @param string                                                  $variable the stream of the variable
	 *
	 * @return mixed
	 * @throws \Smarty\Exception
	 *
	 */
	public function getStreamVariable($variable)
	{

		trigger_error("Using stream variables (\`\{\$foo:bar\}\`)is deprecated.", E_USER_DEPRECATED);

		$_result = '';
		$fp = fopen($variable, 'r+');
		if ($fp) {
			while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
				$_result .= $current_line;
			}
			fclose($fp);
			return $_result;
		}
		if ($this->getSmarty()->error_unassigned) {
			throw new Exception('Undefined stream variable "' . $variable . '"');
		}
		return null;
	}
	/**
	 * @inheritdoc
	 */
	public function configLoad($config_file, $sections = null)
	{
		$confObj = parent::configLoad($config_file, $sections);

		$this->getCompiled()->file_dependency[ $confObj->getSource()->uid ] =
			array($confObj->getSource()->getResourceName(), $confObj->getSource()->getTimeStamp(), $confObj->getSource()->type);

		return $confObj;
	}

	public function fetch() {
		$result = $this->_execute(0);
		return $result === null ? ob_get_clean() : $result;
	}

	public function display() {
		$this->_execute(1);
	}

	/**
	 * test if cache is valid
	 *
	 * @param mixed $cache_id cache id to be used with this template
	 * @param mixed $compile_id compile id to be used with this template
	 * @param object $parent next higher level of Smarty variables
	 *
	 * @return bool cache status
	 * @throws \Exception
	 * @throws \Smarty\Exception
	 *
	 * @api  Smarty::isCached()
	 */
	public function isCached(): bool {
		return (bool) $this->_execute(2);
	}

	/**
	 * fetches a rendered Smarty template
	 *
	 * @param string $function function type 0 = fetch,  1 = display, 2 = isCache
	 *
	 * @return mixed
	 * @throws Exception
	 * @throws \Throwable
	 */
	private function _execute($function) {

		$smarty = $this->getSmarty();

		// make sure we have integer values
		$this->caching = (int)$this->caching;
		// fetch template content
		$level = ob_get_level();
		try {
			$_smarty_old_error_level =
				isset($smarty->error_reporting) ? error_reporting($smarty->error_reporting) : null;

			if ($smarty->isMutingUndefinedOrNullWarnings()) {
				$errorHandler = new \Smarty\ErrorHandler();
				$errorHandler->activate();
			}

			if ($function === 2) {
				if ($this->caching) {
					// return cache status of template
					$result = $this->getCached()->isCached($this);
				} else {
					return false;
				}
			} else {

				// After rendering a template, the tpl/config variables are reset, so the template can be re-used.
				$this->pushStack();

				// Start output-buffering.
				ob_start();

				$result = $this->render(false, $function);

				// Restore the template to its previous state
				$this->popStack();
			}

			if (isset($errorHandler)) {
				$errorHandler->deactivate();
			}

			if (isset($_smarty_old_error_level)) {
				error_reporting($_smarty_old_error_level);
			}
			return $result;
		} catch (\Throwable $e) {
			while (ob_get_level() > $level) {
				ob_end_clean();
			}
			if (isset($errorHandler)) {
				$errorHandler->deactivate();
			}

			if (isset($_smarty_old_error_level)) {
				error_reporting($_smarty_old_error_level);
			}
			throw $e;
		}
	}

	/**
	 * @return Config|Source|null
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @param Config|Source|null $source
	 */
	public function setSource($source): void {
		$this->source = $source;
	}

	/**
	 * Sets the Cached object, so subtemplates can share one Cached object to gather meta-data.
	 *
	 * @param Cached $cached
	 *
	 * @return void
	 */
	private function setCached(Cached $cached) {
		$this->cached = $cached;
	}

	/**
	 * @param string $compile_id
	 *
	 * @throws Exception
	 */
	public function setCompileId($compile_id) {
		parent::setCompileId($compile_id);
		$this->getCompiled(true);
		if ($this->caching) {
			$this->getCached(true);
		}
	}

	/**
	 * @param string $cache_id
	 *
	 * @throws Exception
	 */
	public function setCacheId($cache_id) {
		parent::setCacheId($cache_id);
		$this->getCached(true);
	}

}
