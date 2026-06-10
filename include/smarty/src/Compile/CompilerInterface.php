<?php

namespace Smarty\Compile;

/**
 * This class does extend all internal compile plugins
 *


 */
interface CompilerInterface {

	/**
	 * Compiles code for the tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code as a string
	 * @throws \Smarty\CompilerException
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string;

	public function isCacheable(): bool;
}