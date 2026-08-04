<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty strip modifier plugin
 * Type:     modifier
 * Name:     strip
 * Purpose:  Replace all repeated spaces, newlines, tabs
 *              with a single space or supplied replacement string.
 * Example:  {$var|strip} {$var|strip:"&nbsp;"}
 * Date:     September 25th, 2002
 *
 * @author Uwe Tews
 */

class StripModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		if (!isset($params[ 1 ])) {
			$params[ 1 ] = "' '";
		}
		return "preg_replace('!\s+!" . \Smarty\Smarty::$_UTF8_MODIFIER . "', {$params[1]},{$params[0]})";
	}

}