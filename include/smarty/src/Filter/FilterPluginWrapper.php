<?php

namespace Smarty\Filter;

class FilterPluginWrapper implements FilterInterface {

	private $callback;

	public function __construct($callback) {
		$this->callback = $callback;
	}
	public function filter($code, \Smarty\Template $template) {
		return call_user_func($this->callback, $code, $template);
	}
}