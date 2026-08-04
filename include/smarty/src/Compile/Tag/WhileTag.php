<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile While Class
 *


 */
class WhileTag extends Base {

	/**
	 * Compiles code for the {while} tag
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
		$compiler->loopNesting++;

		if ($compiler->tag_nocache) {
			// push a {nocache} tag onto the stack to prevent caching of this block
			$this->openTag($compiler, 'nocache');
		}

		$this->openTag($compiler, 'while', $compiler->tag_nocache);

		if (!array_key_exists('if condition', $parameter)) {
			$compiler->trigger_template_error('missing while condition', null, true);
		}

		if (is_array($parameter['if condition'])) {
			if ($compiler->isNocacheActive()) {
				// create nocache var to make it know for further compiling
				if (is_array($parameter['if condition']['var'])) {
					$var = $parameter['if condition']['var']['var'];
				} else {
					$var = $parameter['if condition']['var'];
				}
				$compiler->setNocacheInVariable($var);
			}
			$prefixVar = $compiler->getNewPrefixVariable();
			$assignCompiler = new Assign();
			$assignAttr = [];
			$assignAttr[]['value'] = $prefixVar;
			if (is_array($parameter['if condition']['var'])) {
				$assignAttr[]['var'] = $parameter['if condition']['var']['var'];
				$_output = "<?php while ({$prefixVar} = {$parameter[ 'if condition' ][ 'value' ]}) {?>";
				$_output .= $assignCompiler->compile(
					$assignAttr,
					$compiler,
					['smarty_internal_index' => $parameter['if condition']['var']['smarty_internal_index']]
				);
			} else {
				$assignAttr[]['var'] = $parameter['if condition']['var'];
				$_output = "<?php while ({$prefixVar} = {$parameter[ 'if condition' ][ 'value' ]}) {?>";
				$_output .= $assignCompiler->compile($assignAttr, $compiler, []);
			}
			return $_output;
		} else {
			return "<?php\n while ({$parameter['if condition']}) {?>";
		}
	}
}