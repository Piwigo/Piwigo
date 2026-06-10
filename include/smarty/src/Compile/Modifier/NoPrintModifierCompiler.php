<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty noprint modifier plugin
 * Type:     modifier
 * Name:     noprint
 * Purpose:  return an empty string
 *
 * @author Uwe Tews
 */

class NoPrintModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		return "''";
	}

}