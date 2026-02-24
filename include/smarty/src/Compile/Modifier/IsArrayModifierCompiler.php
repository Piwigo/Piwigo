<?php
namespace Smarty\Compile\Modifier;
use Smarty\CompilerException;

/**
 * Smarty is_array modifier plugin
 */
class IsArrayModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {

		if (count($params) !== 1) {
			throw new CompilerException("Invalid number of arguments for is_array. is_array expects exactly 1 parameter.");
		}

		return 'is_array(' . $params[0] . ')';
	}

}