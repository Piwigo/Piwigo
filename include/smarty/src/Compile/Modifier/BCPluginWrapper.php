<?php

namespace Smarty\Compile\Modifier;

class BCPluginWrapper extends Base {

	private $callback;

	public function __construct($callback) {
		$this->callback = $callback;
	}

	/**
	 * @inheritDoc
	 */
	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return call_user_func($this->callback, $params, $compiler);
	}
}