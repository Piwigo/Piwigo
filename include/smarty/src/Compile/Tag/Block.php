<?php
/**
 * This file is part of Smarty.
 *
 * (c) 2015 Uwe Tews
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Smarty\Compile\Tag;

use Smarty\ParseTree\Template;

/**
 * Smarty Internal Plugin Compile Block Class
 *
 * @author Uwe Tews <uwe.tews@googlemail.com>
 */
class Block extends Inheritance {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	public $required_attributes = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	public $shorttag_order = ['name'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	protected $option_flags = ['hide', 'nocache'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BasePlugin
	 */
	public $optional_attributes = ['assign'];

	/**
	 * Compiles code for the {block} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = array(), $tag = null, $function = null): string
	{
		if (!isset($compiler->_cache['blockNesting'])) {
			$compiler->_cache['blockNesting'] = 0;
		}
		if ($compiler->_cache['blockNesting'] === 0) {
			// make sure that inheritance gets initialized in template code
			$this->registerInit($compiler);
			$this->option_flags = ['hide', 'nocache', 'append', 'prepend'];
		} else {
			$this->option_flags = ['hide', 'nocache'];
		}
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		++$compiler->_cache['blockNesting'];
		$_className = 'Block_' . preg_replace('![^\w]+!', '_', uniqid(mt_rand(), true));

		$this->openTag(
			$compiler,
			'block',
			[
				$_attr, $compiler->tag_nocache, $compiler->getParser()->current_buffer,
				$compiler->getTemplate()->getCompiled()->getNocacheCode(), $_className
			]
		);

		$compiler->getParser()->current_buffer = new Template();
		$compiler->getTemplate()->getCompiled()->setNocacheCode(false);
		$compiler->suppressNocacheProcessing = true;
		return '';
	}
}
