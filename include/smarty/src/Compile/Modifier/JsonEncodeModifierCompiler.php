<?php

namespace Smarty\Compile\Modifier;

/**
 * Smarty json_encode modifier plugin
 */
class JsonEncodeModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return 'json_encode(' . $params[0] . (isset($params[1]) ? ', (int) ' . $params[1] : '') . ')';
	}

}