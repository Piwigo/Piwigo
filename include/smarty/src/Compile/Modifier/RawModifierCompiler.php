<?php
namespace Smarty\Compile\Modifier;

use Smarty\Exception;

/**
 * Smarty raw modifier plugin
 * Type:     modifier
 * Name:     raw
 * Purpose:  when escaping is enabled by default, generates a raw output of a variable
 *
 * @author Amaury Bouchard
 */

class RawModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		$compiler->setRawOutput(true);
		return ($params[0]);
	}
}
