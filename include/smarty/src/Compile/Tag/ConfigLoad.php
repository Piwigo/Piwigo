<?php
/**
 * Smarty Internal Plugin Compile Config Load
 * Compiles the {config load} tag
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
use Smarty\Smarty;

/**
 * Smarty Internal Plugin Compile Config Load Class
 *


 */
class ConfigLoad extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $required_attributes = ['file'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $shorttag_order = ['file', 'section'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $optional_attributes = ['section'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $option_flags = [];

	/**
	 * Compiles code for the {config_load} tag
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

		// save possible attributes
		$conf_file = $_attr['file'];
		$section = $_attr['section'] ?? 'null';

		// create config object
		return "<?php\n\$_smarty_tpl->configLoad({$conf_file}, {$section});\n?>\n";
	}
}
