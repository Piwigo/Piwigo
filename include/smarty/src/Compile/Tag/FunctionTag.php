<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Function Class
 *


 */
class FunctionTag extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $required_attributes = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $shorttag_order = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $optional_attributes = ['_any'];

	/**
	 * Compiles code for the {function} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		if ($_attr['nocache'] === true) {
			$compiler->trigger_template_error('nocache option not allowed', null, true);
		}
		unset($_attr['nocache']);
		$_name = trim($_attr['name'], '\'"');

		if (!preg_match('/^[a-zA-Z0-9_\x80-\xff]+$/', $_name)) {
			$compiler->trigger_template_error("Function name contains invalid characters: {$_name}", null, true);
		}

		$compiler->getParentCompiler()->tpl_function[$_name] = [];
		$save = [
			$_attr, $compiler->getParser()->current_buffer, $compiler->getTemplate()->getCompiled()->getNocacheCode(),
			$compiler->getTemplate()->caching,
		];
		$this->openTag($compiler, 'function', $save);
		// Init temporary context
		$compiler->getParser()->current_buffer = new \Smarty\ParseTree\Template();
		$compiler->getTemplate()->getCompiled()->setNocacheCode(false);
		return '';
	}
}