<?php
/**
 * Smarty Internal Plugin Compile Registered Function
 * Compiles code for the execution of a registered function
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile;

use Smarty\Compiler\Template;
use Smarty\CompilerException;

/**
 * Smarty Internal Plugin Compile Registered Function Class
 *


 */
class FunctionCallCompiler extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	public $optional_attributes = ['_any'];

	/**
	 * Shorttag attribute order defined by its names
	 *
	 * @var array
	 */
	protected $shorttag_order = [];

	/**
	 * Compiles code for the execution of a registered function
	 *
	 * @param array $args array with attributes from parser
	 * @param Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 * @param string $tag name of tag
	 * @param string $function name of function
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 * @throws \Smarty\Exception
	 */
	public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null): string
	{

		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		unset($_attr['nocache']);

		$_paramsArray = $this->formatParamsArray($_attr);
		$_params = 'array(' . implode(',', $_paramsArray) . ')';


		if ($functionHandler = $compiler->getSmarty()->getFunctionHandler($function)) {

			// not cacheable?
			$compiler->tag_nocache = $compiler->tag_nocache || !$functionHandler->isCacheable();
			$output = "\$_smarty_tpl->getSmarty()->getFunctionHandler(" . var_export($function, true) . ")";
			$output .= "->handle($_params, \$_smarty_tpl)";
		} else {
			$compiler->trigger_template_error("unknown function '{$function}'", null, true);
		}

		if (!empty($parameter['modifierlist'])) {
			$output = $compiler->compileModifier($parameter['modifierlist'], $output);
		}

		return $output;
	}
}
