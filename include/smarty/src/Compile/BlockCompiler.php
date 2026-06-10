<?php
/**
 * Smarty Internal Plugin Compile Block Plugin
 * Compiles code for the execution of block plugin
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile;

use Smarty\Compiler\Template;
use Smarty\CompilerException;
use Smarty\Exception;
use Smarty\Smarty;

/**
 * Smarty Internal Plugin Compile Block Plugin Class
 *
 */
class BlockCompiler extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $optional_attributes = ['_any'];

	/**
	 * nesting level
	 *
	 * @var int
	 */
	private $nesting = 0;


	/**
	 * Compiles code for the execution of block plugin
	 *
	 * @param array $args array with attributes from parser
	 * @param Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 * @param string $tag name of block plugin
	 * @param string $function PHP function name
	 *
	 * @return string compiled code
	 * @throws CompilerException
	 * @throws Exception
	 */
	public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null): string
	{

		if (!isset($tag[5]) || substr($tag, -5) !== 'close') {
			$output = $this->compileOpeningTag($compiler, $args, $tag, $function);
		} else {
			$output = $this->compileClosingTag($compiler, $tag, $parameter, $function);
		}
		return $output;
	}

	/**
	 * Compiles code for the {$smarty.block.child} property
	 *
	 * @param Template $compiler compiler object
	 *
	 * @return string compiled code
	 * @throws CompilerException
	 */
	public function compileChild(\Smarty\Compiler\Template $compiler) {

		if (!isset($compiler->_cache['blockNesting'])) {
			$compiler->trigger_template_error(
				"'{\$smarty.block.child}' used outside {block} tags ",
				$compiler->getParser()->lex->taglineno
			);
		}
		$compiler->_cache['blockParams'][$compiler->_cache['blockNesting']]['callsChild'] = true;
		$compiler->suppressNocacheProcessing = true;

		$output = "<?php \n";
		$output .= '$_smarty_tpl->getInheritance()->callChild($_smarty_tpl, $this' . ");\n";
		$output .= "?>\n";
		return $output;
	}

	/**
	 * Compiles code for the {$smarty.block.parent} property
	 *
	 * @param Template $compiler compiler object
	 *
	 * @return string compiled code
	 * @throws CompilerException
	 */
	public function compileParent(\Smarty\Compiler\Template $compiler) {

		if (!isset($compiler->_cache['blockNesting'])) {
			$compiler->trigger_template_error(
				"'{\$smarty.block.parent}' used outside {block} tags ",
				$compiler->getParser()->lex->taglineno
			);
		}
		$compiler->suppressNocacheProcessing = true;

		$output = "<?php \n";
		$output .= '$_smarty_tpl->getInheritance()->callParent($_smarty_tpl, $this' . ");\n";
		$output .= "?>\n";
		return $output;
	}

	/**
	 * Returns true if this block is cacheable.
	 *
	 * @param Smarty $smarty
	 * @param $function
	 *
	 * @return bool
	 */
	protected function blockIsCacheable(\Smarty\Smarty $smarty, $function): bool {
		return $smarty->getBlockHandler($function)->isCacheable();
	}

	/**
	 * Returns the code used for the isset check
	 *
	 * @param string $tag tag name
	 * @param string $function base tag or method name
	 *
	 * @return string
	 */
	protected function getIsCallableCode($tag, $function): string {
		return "\$_smarty_tpl->getSmarty()->getBlockHandler(" . var_export($function, true) . ")";
	}

	/**
	 * Returns the full code used to call the callback
	 *
	 * @param string $tag tag name
	 * @param string $function base tag or method name
	 *
	 * @return string
	 */
	protected function getFullCallbackCode($tag, $function): string {
		return "\$_smarty_tpl->getSmarty()->getBlockHandler(" . var_export($function, true) . ")->handle";
	}

	/**
	 * @param Template $compiler
	 * @param array $args
	 * @param string|null $tag
	 * @param string|null $function
	 *
	 * @return string
	 */
	private function compileOpeningTag(Template $compiler, array $args, ?string $tag, ?string $function): string {

		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		$this->nesting++;
		unset($_attr['nocache']);
		$_params = 'array(' . implode(',', $this->formatParamsArray($_attr)) . ')';

		if (!$this->blockIsCacheable($compiler->getSmarty(), $function)) {
			$compiler->tag_nocache = true;
		}

		if ($compiler->tag_nocache) {
			// push a {nocache} tag onto the stack to prevent caching of this block
			$this->openTag($compiler, 'nocache');
		}

		$this->openTag($compiler, $tag, [$_params, $compiler->tag_nocache]);

		// compile code
		$output = "<?php \$_block_repeat=true;
if (!" . $this->getIsCallableCode($tag, $function) .") {\nthrow new \\Smarty\\Exception('block tag \'{$tag}\' not callable or registered');\n}\n
echo " . $this->getFullCallbackCode($tag, $function) . "({$_params}, null, \$_smarty_tpl, \$_block_repeat);
while (\$_block_repeat) {
  ob_start();
?>";

		return $output;
	}

	/**
	 * @param Template $compiler
	 * @param string $tag
	 * @param array $parameter
	 * @param string|null $function
	 *
	 * @return string
	 * @throws CompilerException
	 * @throws Exception
	 */
	private function compileClosingTag(Template $compiler, string $tag, array $parameter, ?string $function): string {

		// closing tag of block plugin, restore nocache
		$base_tag = substr($tag, 0, -5);
		[$_params, $nocache_pushed] = $this->closeTag($compiler, $base_tag);

		// compile code
		if (!isset($parameter['modifier_list'])) {
			$mod_pre = $mod_post = $mod_content = '';
			$mod_content2 = 'ob_get_clean()';
		} else {
			$mod_content2 = "\$_block_content{$this->nesting}";
			$mod_content = "\$_block_content{$this->nesting} = ob_get_clean();\n";
			$mod_pre = "ob_start();\n";
			$mod_post = 'echo ' . $compiler->compileModifier($parameter['modifier_list'], 'ob_get_clean()')
				. ";\n";
		}
		$output = "<?php {$mod_content}\$_block_repeat=false;\n{$mod_pre}";
		$callback = $this->getFullCallbackCode($base_tag, $function);
		$output .= "echo {$callback}({$_params}, {$mod_content2}, \$_smarty_tpl, \$_block_repeat);\n";
		$output .= "{$mod_post}}\n?>";

		if ($nocache_pushed) {
			// pop the pushed virtual nocache tag
			$this->closeTag($compiler, 'nocache');
			$compiler->tag_nocache = true;
		}

		return $output;
	}

}
