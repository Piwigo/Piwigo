<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

class BCPluginWrapper extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $optional_attributes = array('_any');

	private $callback;

	public function __construct($callback, bool $cacheable = true) {
		$this->callback = $callback;
		$this->cacheable = $cacheable;
	}

	/**
	 * @inheritDoc
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		return call_user_func($this->callback, $this->getAttributes($compiler, $args), $compiler->getSmarty());
	}
}