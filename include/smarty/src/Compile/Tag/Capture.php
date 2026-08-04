<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Capture Class
 *


 */
class Capture extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	public $shorttag_order = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	public $optional_attributes = ['name', 'assign', 'append'];

	/**
	 * Compiles code for the {$smarty.capture.xxx}
	 *
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 */
	public static function compileSpecialVariable(
		\Smarty\Compiler\Template $compiler,
		                                     $parameter = null
	) {
		return '$_smarty_tpl->getSmarty()->getRuntime(\'Capture\')->getBuffer($_smarty_tpl' .
			(isset($parameter[1]) ? ", {$parameter[ 1 ]})" : ')');
	}

	/**
	 * Compiles code for the {capture} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param null $parameter
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		$buffer = $_attr['name'] ?? "'default'";
		$assign = $_attr['assign'] ?? 'null';
		$append = $_attr['append'] ?? 'null';

		$compiler->_cache['capture_stack'][] = $compiler->tag_nocache;
		if ($compiler->tag_nocache) {
			// push a virtual {nocache} tag onto the stack.
			$compiler->openTag('nocache');
		}

		return "<?php \$_smarty_tpl->getSmarty()->getRuntime('Capture')->open(\$_smarty_tpl, $buffer, $assign, $append);?>";
	}
}