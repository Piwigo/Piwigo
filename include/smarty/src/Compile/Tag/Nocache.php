<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Nocache Class
 *


 */
class Nocache extends Base {

	/**
	 * Array of names of valid option flags
	 *
	 * @var array
	 */
	protected $option_flags = [];

	/**
	 * Compiles code for the {nocache} tag
	 * This tag does not generate compiled output. It only sets a compiler flag.
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$this->openTag($compiler, 'nocache');
		return '';
	}
}