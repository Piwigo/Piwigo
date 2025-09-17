<?php

namespace Smarty\Runtime;

use Smarty\Exception;

class DefaultPluginHandlerRuntime {

	/**
	 * @var callable
	 */
	private $defaultPluginHandler;

	public function __construct(?callable $defaultPluginHandler = null) {
		$this->defaultPluginHandler = $defaultPluginHandler;
	}

	public function hasPlugin($tag, $plugin_type): bool {
		if ($this->defaultPluginHandler === null) {
			return false;
		}

		$callback = null;

		// these are not used here
		$script = null;
		$cacheable = null;

		return (\call_user_func_array(
				$this->defaultPluginHandler,
				[
					$tag,
					$plugin_type,
					null, // This used to pass $this->template, but this parameter has been removed in 5.0
					&$callback,
					&$script,
					&$cacheable,
				]
			) && $callback);
	}

	/**
	 * @throws Exception
	 */
	public function getCallback($tag, $plugin_type) {

		if ($this->defaultPluginHandler === null) {
			return false;
		}

		$callback = null;

		// these are not used here
		$script = null;
		$cacheable = null;

		if (\call_user_func_array(
				$this->defaultPluginHandler,
				[
					$tag,
					$plugin_type,
					null, // This used to pass $this->template, but this parameter has been removed in 5.0
					&$callback,
					&$script,
					&$cacheable,
				]
			) && $callback) {
			return $callback;
		}
		throw new Exception("Default plugin handler: Returned callback for '{$tag}' not callable at runtime");
	}

}