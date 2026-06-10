<?php
namespace Smarty\Compile\Modifier;
use Smarty\CompilerException;

/**
 * Smarty empty modifier plugin
 */
class EmptyModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {

		if (count($params) !== 1) {
			throw new CompilerException("Invalid number of arguments for empty. empty expects exactly 1 parameter.");
		}

		return 'empty(' . $params[0] . ')';
	}

}