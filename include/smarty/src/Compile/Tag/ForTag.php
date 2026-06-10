<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile For Class
 *


 */
class ForTag extends Base {

	/**
	 * Compiles code for the {for} tag
	 * Smarty supports two different syntax's:
	 * - {for $var in $array}
	 * For looping over arrays or iterators
	 * - {for $x=0; $x<$y; $x++}
	 * For general loops
	 * The parser is generating different sets of attribute by which this compiler can
	 * determine which syntax is used.
	 *
	 * @param array $args array with attributes from parser
	 * @param object $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$compiler->loopNesting++;
		if ($parameter === 0) {
			$this->required_attributes = ['start', 'to'];
			$this->optional_attributes = ['max', 'step'];
		} else {
			$this->required_attributes = ['start', 'ifexp', 'var', 'step'];
			$this->optional_attributes = [];
		}

		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		$output = "<?php\n";
		if ($parameter === 1) {
			foreach ($_attr['start'] as $_statement) {
				if (is_array($_statement['var'])) {
					$var = $_statement['var']['var'];
					$index = $_statement['var']['smarty_internal_index'];
				} else {
					$var = $_statement['var'];
					$index = '';
				}
				$output .= "\$_smarty_tpl->assign($var, null);\n";
				$output .= "\$_smarty_tpl->tpl_vars[$var]->value{$index} = {$_statement['value']};\n";
			}
			if (is_array($_attr['var'])) {
				$var = $_attr['var']['var'];
				$index = $_attr['var']['smarty_internal_index'];
			} else {
				$var = $_attr['var'];
				$index = '';
			}
			$output .= "if ($_attr[ifexp]) {\nfor (\$_foo=true;$_attr[ifexp]; \$_smarty_tpl->tpl_vars[$var]->value{$index}$_attr[step]) {\n";
		} else {
			$_statement = $_attr['start'];
			if (is_array($_statement['var'])) {
				$var = $_statement['var']['var'];
				$index = $_statement['var']['smarty_internal_index'];
			} else {
				$var = $_statement['var'];
				$index = '';
			}
			$output .= "\$_smarty_tpl->assign($var, null);";
			if (isset($_attr['step'])) {
				$output .= "\$_smarty_tpl->tpl_vars[$var]->step = $_attr[step];";
			} else {
				$output .= "\$_smarty_tpl->tpl_vars[$var]->step = 1;";
			}
			if (isset($_attr['max'])) {
				$output .= "\$_smarty_tpl->tpl_vars[$var]->total = (int) min(ceil((\$_smarty_tpl->tpl_vars[$var]->step > 0 ? $_attr[to]+1 - ($_statement[value]) : $_statement[value]-($_attr[to])+1)/abs(\$_smarty_tpl->tpl_vars[$var]->step)),$_attr[max]);\n";
			} else {
				$output .= "\$_smarty_tpl->tpl_vars[$var]->total = (int) ceil((\$_smarty_tpl->tpl_vars[$var]->step > 0 ? $_attr[to]+1 - ($_statement[value]) : $_statement[value]-($_attr[to])+1)/abs(\$_smarty_tpl->tpl_vars[$var]->step));\n";
			}
			$output .= "if (\$_smarty_tpl->tpl_vars[$var]->total > 0) {\n";
			$output .= "for (\$_smarty_tpl->tpl_vars[$var]->value{$index} = $_statement[value], \$_smarty_tpl->tpl_vars[$var]->iteration = 1;\$_smarty_tpl->tpl_vars[$var]->iteration <= \$_smarty_tpl->tpl_vars[$var]->total;\$_smarty_tpl->tpl_vars[$var]->value{$index} += \$_smarty_tpl->tpl_vars[$var]->step, \$_smarty_tpl->tpl_vars[$var]->iteration++) {\n";
			$output .= "\$_smarty_tpl->tpl_vars[$var]->first = \$_smarty_tpl->tpl_vars[$var]->iteration === 1;";
			$output .= "\$_smarty_tpl->tpl_vars[$var]->last = \$_smarty_tpl->tpl_vars[$var]->iteration === \$_smarty_tpl->tpl_vars[$var]->total;";
		}
		$output .= '?>';

		if ($compiler->tag_nocache) {
			// push a {nocache} tag onto the stack to prevent caching of this for loop
			$this->openTag($compiler, 'nocache');
		}

		$this->openTag($compiler, 'for', ['for', $compiler->tag_nocache]);

		return $output;
	}
}