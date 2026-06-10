<?php

namespace Smarty\FunctionHandler;

use Smarty\Template;

class Base implements FunctionHandlerInterface {

	/**
	 * @var bool
	 */
	protected $cacheable = true;

	public function isCacheable(): bool {
		return $this->cacheable;
	}

	public function handle($params, Template $template) {
		// TODO: Implement handle() method.
	}
}