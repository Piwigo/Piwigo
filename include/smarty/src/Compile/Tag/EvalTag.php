<?php
/**
 * Smarty Internal Plugin Compile Eval
 * Compiles the {eval} tag.
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Eval Class
 *


 */
class EvalTag extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	public $required_attributes = ['var'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	public $optional_attributes = ['assign'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	public $shorttag_order = ['var', 'assign'];

	/**
	 * Compiles code for the {eval} tag
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
		if (isset($_attr['assign'])) {
			// output will be stored in a smarty variable instead of being displayed
			$_assign = $_attr['assign'];
		}
		// create template object
		$_output =
			"\$_template = new \\Smarty\\Template('eval:'.{$_attr[ 'var' ]}, \$_smarty_tpl->getSmarty(), \$_smarty_tpl);";
		//was there an assign attribute?
		if (isset($_assign)) {
			$_output .= "\$_smarty_tpl->assign($_assign,\$_template->fetch());";
		} else {
			$_output .= 'echo $_template->fetch();';
		}
		return "<?php $_output ?>";
	}
}
