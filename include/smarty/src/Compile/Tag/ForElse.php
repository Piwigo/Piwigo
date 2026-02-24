<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Forelse Class
 *


 */
class ForElse extends Base {

	/**
	 * Compiles code for the {forelse} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param object $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		[$tagName, $nocache_pushed] = $this->closeTag($compiler, ['for']);
		$this->openTag($compiler, 'forelse', ['forelse', $nocache_pushed]);
		return "<?php }} else { ?>";
	}
}