<?php
/**
 * Smarty Internal Plugin Compile Rdelim
 * Compiles the {rdelim} tag
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

/**
 * Smarty Internal Plugin Compile Rdelim Class
 *


 */
class Rdelim extends Ldelim {

	/**
	 * Compiles code for the {rdelim} tag
	 * This tag does output the right delimiter.
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		parent::compile($args, $compiler);
		return $compiler->getTemplate()->getRightDelimiter();
	}
}
