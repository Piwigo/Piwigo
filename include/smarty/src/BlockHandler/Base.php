<?php

namespace Smarty\BlockHandler;

use Smarty\Template;

abstract class Base implements BlockHandlerInterface {

	/**
	 * @var bool
	 */
	protected $cacheable = true;

	abstract public function handle($params, $content, Template $template, &$repeat);

	public function isCacheable(): bool {
		return $this->cacheable;
	}
}