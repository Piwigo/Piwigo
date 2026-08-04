<?php

namespace Smarty\Compile\Tag;

/**
 * Smarty Internal Plugin Compile Foreach Class
 *


 */
class ForeachTag extends ForeachSection {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $required_attributes = ['from', 'item'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $optional_attributes = ['name', 'key', 'properties'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $shorttag_order = ['from', 'item', 'key', 'name'];

	/**
	 * counter
	 *
	 * @var int
	 */
	private static $counter = 0;

	/**
	 * Name of this tag
	 *
	 * @var string
	 */
	protected $tagName = 'foreach';

	/**
	 * Valid properties of $smarty.foreach.name.xxx variable
	 *
	 * @var array
	 */
	protected $nameProperties = ['first', 'last', 'index', 'iteration', 'show', 'total'];

	/**
	 * Valid properties of $item@xxx variable
	 *
	 * @var array
	 */
	protected $itemProperties = ['first', 'last', 'index', 'iteration', 'show', 'total', 'key'];

	/**
	 * Flag if tag had name attribute
	 *
	 * @var bool
	 */
	protected $isNamed = false;

	/**
	 * Compiles code for the {foreach} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 * @throws \Smarty\Exception
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$compiler->loopNesting++;
		// init
		$this->isNamed = false;
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		$from = $_attr['from'];
		$item = $compiler->getId($_attr['item']);
		if ($item === false) {
			$item = $this->getVariableName($_attr['item']);
		}
		$key = $name = null;
		$attributes = ['item' => $item];
		if (isset($_attr['key'])) {
			$key = $compiler->getId($_attr['key']);
			if ($key === false) {
				$key = $this->getVariableName($_attr['key']);
			}
			$attributes['key'] = $key;
		}
		if (isset($_attr['name'])) {
			$this->isNamed = true;
			$name = $attributes['name'] = $compiler->getId($_attr['name']);
		}
		foreach ($attributes as $a => $v) {
			if ($v === false) {
				$compiler->trigger_template_error("'{$a}' attribute/variable has illegal value", null, true);
			}
		}
		$fromName = $this->getVariableName($_attr['from']);
		if ($fromName) {
			foreach (['item', 'key'] as $a) {
				if (isset($attributes[$a]) && $attributes[$a] === $fromName) {
					$compiler->trigger_template_error(
						"'{$a}' and 'from' may not have same variable name '{$fromName}'",
						null,
						true
					);
				}
			}
		}

		$itemVar = "\$_smarty_tpl->getVariable('{$item}')";
		$localVariablePrefix = '$foreach' . self::$counter++;

		// search for used tag attributes
		$itemAttr = [];
		$namedAttr = [];
		$this->scanForProperties($attributes, $compiler);
		if (!empty($this->matchResults['item'])) {
			$itemAttr = $this->matchResults['item'];
		}
		if (!empty($this->matchResults['named'])) {
			$namedAttr = $this->matchResults['named'];
		}
		if (isset($_attr['properties']) && preg_match_all('/[\'](.*?)[\']/', $_attr['properties'], $match)) {
			foreach ($match[1] as $prop) {
				if (in_array($prop, $this->itemProperties)) {
					$itemAttr[$prop] = true;
				} else {
					$compiler->trigger_template_error("Invalid property '{$prop}'", null, true);
				}
			}
			if ($this->isNamed) {
				foreach ($match[1] as $prop) {
					if (in_array($prop, $this->nameProperties)) {
						$nameAttr[$prop] = true;
					} else {
						$compiler->trigger_template_error("Invalid property '{$prop}'", null, true);
					}
				}
			}
		}
		if (isset($itemAttr['first'])) {
			$itemAttr['index'] = true;
		}
		if (isset($namedAttr['first'])) {
			$namedAttr['index'] = true;
		}
		if (isset($namedAttr['last'])) {
			$namedAttr['iteration'] = true;
			$namedAttr['total'] = true;
		}
		if (isset($itemAttr['last'])) {
			$itemAttr['iteration'] = true;
			$itemAttr['total'] = true;
		}
		if (isset($namedAttr['show'])) {
			$namedAttr['total'] = true;
		}
		if (isset($itemAttr['show'])) {
			$itemAttr['total'] = true;
		}
		$keyTerm = '';
		if (isset($attributes['key'])) {
			$keyTerm = "\$_smarty_tpl->getVariable('{$key}')->value => ";
		}
		if (isset($itemAttr['key'])) {
			$keyTerm = "{$itemVar}->key => ";
		}
		if ($this->isNamed) {
			$foreachVar = "\$_smarty_tpl->tpl_vars['__smarty_foreach_{$attributes['name']}']";
		}
		$needTotal = isset($itemAttr['total']);

		if ($compiler->tag_nocache) {
			// push a {nocache} tag onto the stack to prevent caching of this block
			$this->openTag($compiler, 'nocache');
		}

		// Register tag
		$this->openTag(
			$compiler,
			'foreach',
			['foreach', $compiler->tag_nocache, $localVariablePrefix, $item, !empty($itemAttr)]
		);

		// generate output code
		$output = "<?php\n";
		$output .= "\$_from = \$_smarty_tpl->getSmarty()->getRuntime('Foreach')->init(\$_smarty_tpl, $from, " .
			var_export($item, true);
		if ($name || $needTotal || $key) {
			$output .= ', ' . var_export($needTotal, true);
		}
		if ($name || $key) {
			$output .= ', ' . var_export($key, true);
		}
		if ($name) {
			$output .= ', ' . var_export($name, true) . ', ' . var_export($namedAttr, true);
		}
		$output .= ");\n";
		if (isset($itemAttr['show'])) {
			$output .= "{$itemVar}->show = ({$itemVar}->total > 0);\n";
		}
		if (isset($itemAttr['iteration'])) {
			$output .= "{$itemVar}->iteration = 0;\n";
		}
		if (isset($itemAttr['index'])) {
			$output .= "{$itemVar}->index = -1;\n";
		}
		$output .= "{$localVariablePrefix}DoElse = true;\n";
		$output .= "foreach (\$_from ?? [] as {$keyTerm}{$itemVar}->value) {\n";
		$output .= "{$localVariablePrefix}DoElse = false;\n";
		if (isset($attributes['key']) && isset($itemAttr['key'])) {
			$output .= "\$_smarty_tpl->assign('{$key}', {$itemVar}->key);\n";
		}
		if (isset($itemAttr['iteration'])) {
			$output .= "{$itemVar}->iteration++;\n";
		}
		if (isset($itemAttr['index'])) {
			$output .= "{$itemVar}->index++;\n";
		}
		if (isset($itemAttr['first'])) {
			$output .= "{$itemVar}->first = !{$itemVar}->index;\n";
		}
		if (isset($itemAttr['last'])) {
			$output .= "{$itemVar}->last = {$itemVar}->iteration === {$itemVar}->total;\n";
		}
		if (isset($foreachVar)) {
			if (isset($namedAttr['iteration'])) {
				$output .= "{$foreachVar}->value['iteration']++;\n";
			}
			if (isset($namedAttr['index'])) {
				$output .= "{$foreachVar}->value['index']++;\n";
			}
			if (isset($namedAttr['first'])) {
				$output .= "{$foreachVar}->value['first'] = !{$foreachVar}->value['index'];\n";
			}
			if (isset($namedAttr['last'])) {
				$output .= "{$foreachVar}->value['last'] = {$foreachVar}->value['iteration'] === {$foreachVar}->value['total'];\n";
			}
		}
		if (!empty($itemAttr)) {
			$output .= "{$localVariablePrefix}Backup = clone \$_smarty_tpl->getVariable('{$item}');\n";
		}
		$output .= '?>';
		return $output;
	}

	/**
	 * Get variable name from string
	 *
	 * @param string $input
	 *
	 * @return bool|string
	 */
	private function getVariableName($input) {
		if (preg_match('~^[$]_smarty_tpl->getValue\([\'"]*([0-9]*[a-zA-Z_]\w*)[\'"]*\]\)$~', $input, $match)) {
			return $match[1];
		}
		return false;
	}

	/**
	 * Compiles code for to restore saved template variables
	 *
	 * @param int $levels number of levels to restore
	 *
	 * @return string compiled code
	 */
	public function compileRestore($levels) {
		return "\$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore(\$_smarty_tpl, {$levels});";
	}
}