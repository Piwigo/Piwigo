<?php

namespace Smarty\BlockHandler;

use Smarty\Template;

class BlockPluginWrapper extends Base {

	private $callback;

	public function __construct($callback, bool $cacheable = true) {
		$this->callback = $callback;
		$this->cacheable = $cacheable;
	}

	public function handle($params, $content, Template $template, &$repeat) {
		return \call_user_func_array($this->callback, [$params, $content, &$template, &$repeat]);
	}
}