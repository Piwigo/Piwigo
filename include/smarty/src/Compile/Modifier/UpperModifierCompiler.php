<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty upper modifier plugin
 * Type:     modifier
 * Name:     lower
 * Purpose:  convert string to uppercase
 *
 * @author Uwe Tews
 */

class UpperModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return 'mb_strtoupper((string) ' . $params[ 0 ] . ' ?? \'\', \'' . addslashes(\Smarty\Smarty::$_CHARSET) . '\')';
	}

}
