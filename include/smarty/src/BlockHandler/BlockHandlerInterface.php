<?php

namespace Smarty\BlockHandler;

use Smarty\Template;

interface BlockHandlerInterface {
	public function handle($params, $content, Template $template, &$repeat);
	public function isCacheable(): bool;
}