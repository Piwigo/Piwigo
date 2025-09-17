<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty default modifier plugin
 * Type:     modifier
 * Name:     default
 * Purpose:  designate default value for empty variables
 *
 * @author Uwe Tews
 */

class DefaultModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		$output = $params[ 0 ];
		if (!isset($params[ 1 ])) {
			$params[ 1 ] = "''";
		}
		array_shift($params);
		foreach ($params as $param) {
			$output = '(($tmp = ' . $output . ' ?? null)===null||$tmp===\'\' ? ' . $param . ' ?? null : $tmp)';
		}
		return $output;
	}

}