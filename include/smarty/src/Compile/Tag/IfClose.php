<?php
/**
 * Smarty Internal Plugin Compile If
 * Compiles the {if} {else} {elseif} {/if} tags
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile Ifclose Class
 *


 */
class IfClose extends Base {

	/**
	 * Compiles code for the {/if} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 *
	 * @return string compiled code
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{

		[$nesting, $nocache_pushed] = $this->closeTag($compiler, ['if', 'else', 'elseif']);

		if ($nocache_pushed) {
			// pop the pushed virtual nocache tag
			$this->closeTag($compiler, 'nocache');
			$compiler->tag_nocache = true;
		}

		$tmp = '';
		for ($i = 0; $i < $nesting; $i++) {
			$tmp .= '}';
		}
		return "<?php {$tmp}?>";
	}
}
