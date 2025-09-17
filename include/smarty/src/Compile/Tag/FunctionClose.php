<?php
/**
 * Smarty Internal Plugin Compile Function
 * Compiles the {function} {/function} tags
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Functionclose Class
 *


 */
class FunctionClose extends Base {

	/**
	 * Compiler object
	 *
	 * @var object
	 */
	private $compiler = null;

	/**
	 * Compiles code for the {/function} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param object|\Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$this->compiler = $compiler;
		$saved_data = $this->closeTag($compiler, ['function']);
		$_attr = $saved_data[0];
		$_name = trim($_attr['name'], '\'"');
		$parentCompiler = $compiler->getParentCompiler();
		$parentCompiler->tpl_function[$_name]['compiled_filepath'] =
			$parentCompiler->getTemplate()->getCompiled()->filepath;
		$parentCompiler->tpl_function[$_name]['uid'] = $compiler->getTemplate()->getSource()->uid;
		$_parameter = $_attr;
		unset($_parameter['name']);
		// default parameter
		$_paramsArray = $this->formatParamsArray($_attr);

		$_paramsCode = (new \Smarty\Compiler\CodeFrame($compiler->getTemplate()))->insertLocalVariables();

		if (!empty($_paramsArray)) {
			$_params = 'array(' . implode(',', $_paramsArray) . ')';
			$_paramsCode .= "\$params = array_merge($_params, \$params);\n";
		}
		$_functionCode = $compiler->getParser()->current_buffer;
		// setup buffer for template function code
		$compiler->getParser()->current_buffer = new \Smarty\ParseTree\Template();

		$_funcName = "smarty_template_function_{$_name}_{$compiler->getTemplate()->getCompiled()->nocache_hash}";
		$_funcNameCaching = $_funcName . '_nocache';

		if ($compiler->getTemplate()->getCompiled()->getNocacheCode()) {
			$parentCompiler->tpl_function[$_name]['call_name_caching'] = $_funcNameCaching;
			$output = "<?php\n";
			$output .= $compiler->cStyleComment(" {$_funcNameCaching} ") . "\n";
			$output .= "if (!function_exists('{$_funcNameCaching}')) {\n";
			$output .= "function {$_funcNameCaching} (\\Smarty\\Template \$_smarty_tpl,\$params) {\n";

			$output .= "ob_start();\n";
			$output .= "\$_smarty_tpl->getCompiled()->setNocacheCode(true);\n";
			$output .= $_paramsCode;
			$output .= "foreach (\$params as \$key => \$value) {\n\$_smarty_tpl->assign(\$key, \$value);\n}\n";
			$output .= "\$params = var_export(\$params, true);\n";
			$output .= "echo \"/*%%SmartyNocache:{$compiler->getTemplate()->getCompiled()->nocache_hash}%%*/<?php ";
			$output .= "\\\$_smarty_tpl->pushStack();\nforeach (\$params as \\\$key => \\\$value) {\n\\\$_smarty_tpl->assign(\\\$key, \\\$value);\n}\n?>";
			$output .= "/*/%%SmartyNocache:{$compiler->getTemplate()->getCompiled()->nocache_hash}%%*/\";?>";
			$compiler->getParser()->current_buffer->append_subtree(
				$compiler->getParser(),
				new \Smarty\ParseTree\Tag(
					$compiler->getParser(),
					$output
				)
			);
			$compiler->getParser()->current_buffer->append_subtree($compiler->getParser(), $_functionCode);
			$output = "<?php echo \"/*%%SmartyNocache:{$compiler->getTemplate()->getCompiled()->nocache_hash}%%*/<?php ";
			$output .= "\\\$_smarty_tpl->popStack();?>\n";
			$output .= "/*/%%SmartyNocache:{$compiler->getTemplate()->getCompiled()->nocache_hash}%%*/\";\n?>";
			$output .= "<?php echo str_replace('{$compiler->getTemplate()->getCompiled()->nocache_hash}', \$_smarty_tpl->getCompiled()->nocache_hash ?? '', ob_get_clean());\n";
			$output .= "}\n}\n";
			$output .= $compiler->cStyleComment("/ {$_funcName}_nocache ") . "\n\n";
			$output .= "?>\n";
			$compiler->getParser()->current_buffer->append_subtree(
				$compiler->getParser(),
				new \Smarty\ParseTree\Tag(
					$compiler->getParser(),
					$output
				)
			);
			$_functionCode = new \Smarty\ParseTree\Tag(
				$compiler->getParser(),
				preg_replace_callback(
					"/((<\?php )?echo '\/\*%%SmartyNocache:{$compiler->getTemplate()->getCompiled()->nocache_hash}%%\*\/([\S\s]*?)\/\*\/%%SmartyNocache:{$compiler->getTemplate()->getCompiled()->nocache_hash}%%\*\/';(\?>\n)?)/",
					[$this, 'removeNocache'],
					$_functionCode->to_smarty_php($compiler->getParser())
				)
			);
		}
		$parentCompiler->tpl_function[$_name]['call_name'] = $_funcName;
		$output = "<?php\n";
		$output .= $compiler->cStyleComment(" {$_funcName} ") . "\n";
		$output .= "if (!function_exists('{$_funcName}')) {\n";
		$output .= "function {$_funcName}(\\Smarty\\Template \$_smarty_tpl,\$params) {\n";
		$output .= $_paramsCode;
		$output .= "foreach (\$params as \$key => \$value) {\n\$_smarty_tpl->assign(\$key, \$value);\n}\n";
		$output .= "?>\n";
		$compiler->getParser()->current_buffer->append_subtree(
			$compiler->getParser(),
			new \Smarty\ParseTree\Tag(
				$compiler->getParser(),
				$output
			)
		);
		$compiler->getParser()->current_buffer->append_subtree($compiler->getParser(), $_functionCode);
		$output = "<?php\n}}\n";
		$output .= $compiler->cStyleComment("/ {$_funcName} ") . "\n\n";
		$output .= "?>\n";
		$compiler->getParser()->current_buffer->append_subtree(
			$compiler->getParser(),
			new \Smarty\ParseTree\Tag(
				$compiler->getParser(),
				$output
			)
		);
		$parentCompiler->blockOrFunctionCode .= $compiler->getParser()->current_buffer->to_smarty_php($compiler->getParser());
		// restore old buffer
		$compiler->getParser()->current_buffer = $saved_data[1];
		// restore old status
		$compiler->getTemplate()->getCompiled()->setNocacheCode($saved_data[2]);
		$compiler->getTemplate()->caching = $saved_data[3];
		return '';
	}

	/**
	 * Remove nocache code
	 *
	 * @param $match
	 *
	 * @return string
	 */
	public function removeNocache($match) {
		$hash = $this->compiler->getTemplate()->getCompiled()->nocache_hash;
		$code =
			preg_replace(
				"/((<\?php )?echo '\/\*%%SmartyNocache:{$hash}%%\*\/)|(\/\*\/%%SmartyNocache:{$hash}%%\*\/';(\?>\n)?)/",
				'',
				$match[0]
			);
		return str_replace(['\\\'', '\\\\\''], ['\'', '\\\''], $code);
	}
}
