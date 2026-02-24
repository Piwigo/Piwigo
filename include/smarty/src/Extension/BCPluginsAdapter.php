<?php

namespace Smarty\Extension;

use Smarty\BlockHandler\BlockPluginWrapper;
use Smarty\Compile\CompilerInterface;
use Smarty\Compile\Modifier\BCPluginWrapper as ModifierCompilerPluginWrapper;
use Smarty\Compile\Tag\BCPluginWrapper as TagPluginWrapper;
use Smarty\Filter\FilterPluginWrapper;
use Smarty\FunctionHandler\BCPluginWrapper as FunctionPluginWrapper;

class BCPluginsAdapter extends Base {

	/**
	 * @var \Smarty\Smarty
	 */
	private $smarty;

	public function __construct(\Smarty\Smarty $smarty) {
		$this->smarty = $smarty;
	}

	private function findPlugin($type, $name): ?array {
		if (null !== $plugin = $this->smarty->getRegisteredPlugin($type, $name)) {
			return $plugin;
		}

		return null;
	}

	public function getTagCompiler(string $tag): ?\Smarty\Compile\CompilerInterface {

		$plugin = $this->findPlugin(\Smarty\Smarty::PLUGIN_COMPILER, $tag);
		if ($plugin === null) {
			return null;
		}

		if (is_callable($plugin[0])) {
			$callback = $plugin[0];
			$cacheable = (bool) $plugin[1] ?? true;
			return new TagPluginWrapper($callback, $cacheable);
		} elseif (class_exists($plugin[0])) {
			$compiler = new $plugin[0];
			if ($compiler instanceof CompilerInterface) {
				return $compiler;
			}
		}

		return null;
	}

	public function getFunctionHandler(string $functionName): ?\Smarty\FunctionHandler\FunctionHandlerInterface {
		$plugin = $this->findPlugin(\Smarty\Smarty::PLUGIN_FUNCTION, $functionName);
		if ($plugin === null) {
			return null;
		}
		$callback = $plugin[0];
		$cacheable = (bool) $plugin[1] ?? true;

		return new FunctionPluginWrapper($callback, $cacheable);

	}

	public function getBlockHandler(string $blockTagName): ?\Smarty\BlockHandler\BlockHandlerInterface {
		$plugin = $this->findPlugin(\Smarty\Smarty::PLUGIN_BLOCK, $blockTagName);
		if ($plugin === null) {
			return null;
		}
		$callback = $plugin[0];
		$cacheable = (bool) $plugin[1] ?? true;

		return new BlockPluginWrapper($callback, $cacheable);
	}

	public function getModifierCallback(string $modifierName) {

		$plugin = $this->findPlugin(\Smarty\Smarty::PLUGIN_MODIFIER, $modifierName);
		if ($plugin === null) {
			return null;
		}
		return $plugin[0];
	}

	public function getModifierCompiler(string $modifier): ?\Smarty\Compile\Modifier\ModifierCompilerInterface {
		$plugin = $this->findPlugin(\Smarty\Smarty::PLUGIN_MODIFIERCOMPILER, $modifier);
		if ($plugin === null) {
			return null;
		}
		$callback = $plugin[0];

		return new ModifierCompilerPluginWrapper($callback);
	}

	/**
	 * @var array
	 */
	private $preFilters = [];

	public function getPreFilters(): array {
		return $this->preFilters;
	}

	public function addPreFilter(\Smarty\Filter\FilterInterface $filter) {
		$this->preFilters[] = $filter;
	}

	public function addCallableAsPreFilter(callable $callable, ?string $name = null) {
		if ($name === null) {
			$this->preFilters[] = new FilterPluginWrapper($callable);
		} else {
			$this->preFilters[$name] = new FilterPluginWrapper($callable);
		}
	}

	public function removePrefilter(string $name) {
		unset($this->preFilters[$name]);
	}

	/**
	 * @var array
	 */
	private $postFilters = [];

	public function getPostFilters(): array {
		return $this->postFilters;
	}

	public function addPostFilter(\Smarty\Filter\FilterInterface $filter) {
		$this->postFilters[] = $filter;
	}

	public function addCallableAsPostFilter(callable $callable, ?string $name = null) {
		if ($name === null) {
			$this->postFilters[] = new FilterPluginWrapper($callable);
		} else {
			$this->postFilters[$name] = new FilterPluginWrapper($callable);
		}
	}

	public function removePostFilter(string $name) {
		unset($this->postFilters[$name]);
	}


	/**
	 * @var array
	 */
	private $outputFilters = [];

	public function getOutputFilters(): array {
		return $this->outputFilters;
	}

	public function addOutputFilter(\Smarty\Filter\FilterInterface $filter) {
		$this->outputFilters[] = $filter;
	}

	public function addCallableAsOutputFilter(callable $callable, ?string $name = null) {
		if ($name === null) {
			$this->outputFilters[] = new FilterPluginWrapper($callable);
		} else {
			$this->outputFilters[$name] = new FilterPluginWrapper($callable);
		}
	}

	public function removeOutputFilter(string $name) {
		unset($this->outputFilters[$name]);
	}

	public function loadPluginsFromDir(string $path) {
		foreach([
			'function',
			'modifier',
		    'block',
		    'compiler',
		    'prefilter',
		    'postfilter',
		    'outputfilter',
		    'modifiercompiler',
		] as $type) {
			foreach (glob($path  . $type . '.?*.php') as $filename) {
				$pluginName = $this->getPluginNameFromFilename($filename);
				if ($pluginName !== null) {
					require_once $filename;
					$functionOrClassName = 'smarty_' . $type . '_' . $pluginName;
					if (function_exists($functionOrClassName) || class_exists($functionOrClassName)) {
						$this->smarty->registerPlugin($type, $pluginName, $functionOrClassName, true, []);
					}
				}
			}
		}

		$type = 'resource';
		foreach (glob($path  . $type . '.?*.php') as $filename) {
			$pluginName = $this->getPluginNameFromFilename($filename);
			if ($pluginName !== null) {
				require_once $filename;
				if (class_exists($className = 'smarty_' . $type . '_' . $pluginName)) {
					$this->smarty->registerResource($pluginName, new $className());
				}
			}
		}

		$type = 'cacheresource';
		foreach (glob($path  . $type . '.?*.php') as $filename) {
			$pluginName = $this->getPluginNameFromFilename($filename);
			if ($pluginName !== null) {
				require_once $filename;
				if (class_exists($className = 'smarty_' . $type . '_' . $pluginName)) {
					$this->smarty->registerCacheResource($pluginName, new $className());
				}
			}
		}

	}

	/**
	 * @param $filename
	 *
	 * @return string|null
	 */
	private function getPluginNameFromFilename($filename) {
		if (!preg_match('/.*\.([a-z_A-Z0-9]+)\.php$/',$filename,$matches)) {
			return null;
		}
		return $matches[1];
	}

}
