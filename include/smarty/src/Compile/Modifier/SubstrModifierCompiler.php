<?php

namespace Smarty\Compile\Modifier;

/**
 * Smarty substr modifier plugin
 */
class SubstrModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return 'substr((string) ' . $params[0] . ', (int) ' . $params[1] .
			(isset($params[2]) ? ', (int) ' . $params[2] : '') . ')';
	}

}