<?php
/**
 * Smarty Internal Plugin Compile Print Expression
 * Compiles any tag which will output an expression or variable
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile;

use Smarty\Compile\Base;
use Smarty\Compiler\BaseCompiler;

/**
 * Smarty Internal Plugin Compile Print Expression Class
 *


 */
class PrintExpressionCompiler extends Base {

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
	protected $option_flags = ['nocache', 'nofilter'];

	/**
	 * Compiles code for generating output from any expression
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string
	 * @throws \Smarty\Exception
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{

		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		$output = $parameter['value'];
		// tag modifier
		if (!empty($parameter['modifierlist'])) {
			$output = $compiler->compileModifier($parameter['modifierlist'], $output);
		}
		if (isset($_attr['assign'])) {
			// assign output to variable
			return "<?php \$_smarty_tpl->assign({$_attr['assign']},{$output});?>";
		} else {
			// display value
			if (!$_attr['nofilter']) {
				// default modifier
				if ($compiler->getSmarty()->getDefaultModifiers()) {
					$modifierlist = [];
					foreach ($compiler->getSmarty()->getDefaultModifiers() as $key => $single_default_modifier) {
						preg_match_all(
							'/(\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|:|[^:]+)/',
							$single_default_modifier,
							$mod_array
						);
						for ($i = 0, $count = count($mod_array[0]); $i < $count; $i++) {
							if ($mod_array[0][$i] !== ':') {
								$modifierlist[$key][] = $mod_array[0][$i];
							}
						}
					}

					$output = $compiler->compileModifier($modifierlist, $output);
				}

				if ($compiler->getTemplate()->getSmarty()->escape_html && !$compiler->isRawOutput()) {
					$output = "htmlspecialchars((string) ({$output}), ENT_QUOTES, '" . addslashes(\Smarty\Smarty::$_CHARSET) . "')";
				}

			}
			$output = "<?php echo {$output};?>\n";
			$compiler->setRawOutput(false);
		}
		return $output;
	}

}
