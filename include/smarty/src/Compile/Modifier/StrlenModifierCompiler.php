<?php

namespace Smarty\Compile\Modifier;
/**
 * Smarty strlen modifier plugin
 * Type:     modifier
 * Name:     strlen
 * Purpose:  return the length of the given string
 *
 */

class StrlenModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return 'strlen((string) ' . $params[0] . ')';
	}

}