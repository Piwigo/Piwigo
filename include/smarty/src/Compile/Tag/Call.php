<?php
/**
 * Smarty Internal Plugin Compile Function_Call
 * Compiles the calls of user defined tags defined by {function}
 *
 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Function_Call Class
 */
class Call extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	public $required_attributes = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	public $shorttag_order = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	public $optional_attributes = ['_any'];

	/**
	 * Compiles the calls of user defined tags defined by {function}
	 *
	 * @param array $args array with attributes from parser
	 * @param object $compiler compiler object
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		// save possible attributes
		if (isset($_attr['assign'])) {
			// output will be stored in a smarty variable instead of being displayed
			$_assign = $_attr['assign'];
		}
		//$_name = trim($_attr['name'], "''");
		$_name = $_attr['name'];
		unset($_attr['name'], $_attr['assign'], $_attr['nocache']);
		// set flag (compiled code of {function} must be included in cache file
		if (!$compiler->getTemplate()->caching || $compiler->isNocacheActive() || $compiler->tag_nocache) {
			$_nocache = 'true';
		} else {
			$_nocache = 'false';
		}
		$_paramsArray = $this->formatParamsArray($_attr);
		$_params = 'array(' . implode(',', $_paramsArray) . ')';
		//$compiler->suppressNocacheProcessing = true;
		// was there an assign attribute
		if (isset($_assign)) {
			$_output =
				"<?php ob_start();\n\$_smarty_tpl->getSmarty()->getRuntime('TplFunction')->callTemplateFunction(\$_smarty_tpl, {$_name}, {$_params}, {$_nocache});\n\$_smarty_tpl->assign({$_assign}, ob_get_clean());?>\n";
		} else {
			$_output =
				"<?php \$_smarty_tpl->getSmarty()->getRuntime('TplFunction')->callTemplateFunction(\$_smarty_tpl, {$_name}, {$_params}, {$_nocache});?>\n";
		}
		return $_output;
	}
}
