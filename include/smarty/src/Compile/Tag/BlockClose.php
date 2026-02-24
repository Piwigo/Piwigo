<?php

namespace Smarty\Compile\Tag;

use Smarty\ParseTree\Template;

/**
 * Smarty Internal Plugin Compile BlockClose Class
 */
class BlockClose extends Inheritance {

	/**
	 * Compiles code for the {/block} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return bool true
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = array(), $tag = null, $function = null): string
	{
		[$_attr, $_nocache, $_buffer, $_has_nocache_code, $_className] = $this->closeTag($compiler, ['block']);

		$_block = [];
		if (isset($compiler->_cache['blockParams'])) {
			$_block = $compiler->_cache['blockParams'][$compiler->_cache['blockNesting']] ?? [];
			unset($compiler->_cache['blockParams'][$compiler->_cache['blockNesting']]);
		}

		$_name = $_attr['name'];
		$_assign = $_attr['assign'] ?? null;
		unset($_attr[ 'assign' ], $_attr[ 'name' ]);

		foreach ($_attr as $name => $stat) {
			if ((is_bool($stat) && $stat !== false) || (!is_bool($stat) && $stat !== 'false')) {
				$_block[ $name ] = 'true';
			}
		}

		// get compiled block code
		$_functionCode = $compiler->getParser()->current_buffer;
		// setup buffer for template function code
		$compiler->getParser()->current_buffer = new Template();
		$output = "<?php\n";
		$output .= $compiler->cStyleComment(" {block {$_name}} ") . "\n";
		$output .= "class {$_className} extends \\Smarty\\Runtime\\Block\n";
		$output .= "{\n";
		foreach ($_block as $property => $value) {
			$output .= "public \${$property} = " . var_export($value, true) . ";\n";
		}
		$output .= "public function callBlock(\\Smarty\\Template \$_smarty_tpl) {\n";

		$output .= (new \Smarty\Compiler\CodeFrame($compiler->getTemplate()))->insertLocalVariables();

		if ($compiler->getTemplate()->getCompiled()->getNocacheCode()) {
			$output .= "\$_smarty_tpl->getCached()->hashes['{$compiler->getTemplate()->getCompiled()->nocache_hash}'] = true;\n";
		}
		if (isset($_assign)) {
			$output .= "ob_start();\n";
		}
		$output .= "?>\n";
		$compiler->getParser()->current_buffer->append_subtree(
			$compiler->getParser(),
			new \Smarty\ParseTree\Tag(
				$compiler->getParser(),
				$output
			)
		);
		$compiler->getParser()->current_buffer->append_subtree($compiler->getParser(), $_functionCode);
		$output = "<?php\n";
		if (isset($_assign)) {
			$output .= "\$_smarty_tpl->assign({$_assign}, ob_get_clean());\n";
		}
		$output .= "}\n";
		$output .= "}\n";
		$output .= $compiler->cStyleComment(" {/block {$_name}} ") . "\n\n";
		$output .= "?>\n";
		$compiler->getParser()->current_buffer->append_subtree(
			$compiler->getParser(),
			new \Smarty\ParseTree\Tag(
				$compiler->getParser(),
				$output
			)
		);
		$compiler->blockOrFunctionCode .= $compiler->getParser()->current_buffer->to_smarty_php($compiler->getParser());

		$compiler->getParser()->current_buffer = new Template();

		// restore old status
		$compiler->getTemplate()->getCompiled()->setNocacheCode($_has_nocache_code);
		$compiler->tag_nocache = $_nocache;

		$compiler->getParser()->current_buffer = $_buffer;
		$output = "<?php \n";
		if ($compiler->_cache['blockNesting'] === 1) {
			$output .= "\$_smarty_tpl->getInheritance()->instanceBlock(\$_smarty_tpl, '$_className', $_name);\n";
		} else {
			$output .= "\$_smarty_tpl->getInheritance()->instanceBlock(\$_smarty_tpl, '$_className', $_name, \$this->tplIndex);\n";
		}
		$output .= "?>\n";
		--$compiler->_cache['blockNesting'];
		if ($compiler->_cache['blockNesting'] === 0) {
			unset($compiler->_cache['blockNesting']);
		}
		$compiler->suppressNocacheProcessing = true;
		return $output;
	}

}