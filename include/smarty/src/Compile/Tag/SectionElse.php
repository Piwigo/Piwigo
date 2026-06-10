<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Sectionelse Class
 *


 */
class SectionElse extends Base {

	/**
	 * Compiles code for the {sectionelse} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		[$openTag, $nocache_pushed] = $this->closeTag($compiler, ['section']);
		$this->openTag($compiler, 'sectionelse', ['sectionelse', $nocache_pushed]);
		return "<?php }} else {\n ?>";
	}
}