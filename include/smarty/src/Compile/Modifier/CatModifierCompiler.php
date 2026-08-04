<?php

namespace Smarty\Compile\Modifier;

/**
 * Smarty cat modifier plugin
 * Type:     modifier
 * Name:     cat
 * Date:     Feb 24, 2003
 * Purpose:  catenate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|cat:"foo"}
 *
 * @author Uwe Tews
 */

class CatModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return '(' . implode(').(', $params) . ')';
	}

}


