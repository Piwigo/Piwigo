<?php
/**
 * Smarty Internal Plugin Compile For
 * Compiles the {for} {forelse} {/for} tags
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Forclose Class
 *


 */
class ForClose extends Base {

	/**
	 * Compiles code for the {/for} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param object $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$compiler->loopNesting--;

		[$openTag, $nocache_pushed] = $this->closeTag($compiler, ['for', 'forelse']);
		$output = "<?php }\n";
		if ($openTag !== 'forelse') {
			$output .= "}\n";
		}
		$output .= "?>";

		if ($nocache_pushed) {
			// pop the pushed virtual nocache tag
			$this->closeTag($compiler, 'nocache');
			$compiler->tag_nocache = true;
		}

		return $output;
	}
}
