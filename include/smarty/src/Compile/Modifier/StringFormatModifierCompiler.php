<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty string_format modifier plugin
 * Type:     modifier
 * Name:     string_format
 * Purpose:  format strings via sprintf
 *
 * @author Uwe Tews
 */

class StringFormatModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return 'sprintf(' . $params[ 1 ] . ',' . $params[ 0 ] . ')';
	}

}