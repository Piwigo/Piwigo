<?php
/**
 * Smarty Internal Plugin Compile Debug
 * Compiles the {debug} tag.
 * It opens a window the the Smarty Debugging Console.
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Debug Class
 *


 */
class Debug extends Base {

	/**
	 * Compiles code for the {debug} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param object $compiler compiler object
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		// check and get attributes, may trigger errors
		$this->getAttributes($compiler, $args);

		// compile always as nocache
		$compiler->tag_nocache = true;
		// display debug template
		$_output =
			"<?php \$_smarty_debug = new \\Smarty\\Debug;\n \$_smarty_debug->display_debug(\$_smarty_tpl);\n";
		$_output .= "unset(\$_smarty_debug);\n?>";
		return $_output;
	}
}
