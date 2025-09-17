<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile If Class
 *


 */
class IfTag extends Base {

	/**
	 * Compiles code for the {if} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{

		if ($compiler->tag_nocache) {
			// push a {nocache} tag onto the stack to prevent caching of this block
			$this->openTag($compiler, 'nocache');
		}

		$this->openTag($compiler, 'if', [1, $compiler->tag_nocache]);

		if (!isset($parameter['if condition'])) {
			$compiler->trigger_template_error('missing if condition', null, true);
		}
		if (is_array($parameter['if condition'])) {
			if (is_array($parameter['if condition']['var'])) {
				$var = $parameter['if condition']['var']['var'];
			} else {
				$var = $parameter['if condition']['var'];
			}
			if ($compiler->isNocacheActive()) {
				// create nocache var to make it know for further compiling
				$compiler->setNocacheInVariable($var);
			}
			$prefixVar = $compiler->getNewPrefixVariable();
			$_output = "<?php {$prefixVar} = {$parameter[ 'if condition' ][ 'value' ]};?>\n";
			$assignAttr = [];
			$assignAttr[]['value'] = $prefixVar;
			$assignCompiler = new Assign();
			if (is_array($parameter['if condition']['var'])) {
				$assignAttr[]['var'] = $parameter['if condition']['var']['var'];
				$_output .= $assignCompiler->compile(
					$assignAttr,
					$compiler,
					['smarty_internal_index' => $parameter['if condition']['var']['smarty_internal_index']]
				);
			} else {
				$assignAttr[]['var'] = $parameter['if condition']['var'];
				$_output .= $assignCompiler->compile($assignAttr, $compiler, []);
			}
			$_output .= "<?php if ({$prefixVar}) {?>";
			return $_output;
		} else {
			return "<?php if ({$parameter['if condition']}) {?>";
		}
	}
}