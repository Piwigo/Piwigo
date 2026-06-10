<?php

namespace Smarty\Runtime;
use Smarty\Template;
use Smarty\Template\Source;
use Smarty\Exception;

/**
 * Inheritance Runtime Methods processBlock, endChild, init
 *


 * @author     Uwe Tews
 **/
class InheritanceRuntime {

	/**
	 * State machine
	 * - 0 idle next extends will create a new inheritance tree
	 * - 1 processing child template
	 * - 2 wait for next inheritance template
	 * - 3 assume parent template, if child will loaded goto state 1
	 *     a call to a sub template resets the state to 0
	 *
	 * @var int
	 */
	private $state = 0;

	/**
	 * Array of root child {block} objects
	 *
	 * @var \Smarty\Runtime\Block[]
	 */
	private $childRoot = [];

	/**
	 * inheritance template nesting level
	 *
	 * @var int
	 */
	private $inheritanceLevel = 0;

	/**
	 * inheritance template index
	 *
	 * @var int
	 */
	private $tplIndex = -1;

	/**
	 * Array of template source objects
	 *
	 * @var Source[]
	 */
	private $sources = [];

	/**
	 * Stack of source objects while executing block code
	 *
	 * @var Source[]
	 */
	private $sourceStack = [];

	/**
	 * Initialize inheritance
	 *
	 * @param \Smarty\Template $tpl template object of caller
	 * @param bool $initChild if true init for child template
	 * @param array $blockNames outer level block name
	 */
	public function init(Template $tpl, $initChild, $blockNames = []) {
		// if called while executing parent template it must be a sub-template with new inheritance root
		if ($initChild && $this->state === 3 && (strpos($tpl->template_resource, 'extendsall') === false)) {
			$tpl->setInheritance(clone $tpl->getSmarty()->getRuntime('Inheritance'));
			$tpl->getInheritance()->init($tpl, $initChild, $blockNames);
			return;
		}
		++$this->tplIndex;
		$this->sources[$this->tplIndex] = $tpl->getSource();
		// start of child sub template(s)
		if ($initChild) {
			$this->state = 1;
			if (!$this->inheritanceLevel) {
				//grab any output of child templates
				ob_start();
			}
			++$this->inheritanceLevel;
		}
		// if state was waiting for parent change state to parent
		if ($this->state === 2) {
			$this->state = 3;
		}
	}

	/**
	 * End of child template(s)
	 * - if outer level is reached flush output buffer and switch to wait for parent template state
	 *
	 * @param \Smarty\Template $tpl
	 * @param null|string $template optional name of inheritance parent template
	 *
	 * @throws \Exception
	 * @throws \Smarty\Exception
	 */
	public function endChild(Template $tpl, $template = null, ?string $currentDir = null) {
		--$this->inheritanceLevel;
		if (!$this->inheritanceLevel) {
			ob_end_clean();
			$this->state = 2;
		}
		if (isset($template)) {
			$tpl->renderSubTemplate(
				$template,
				$tpl->cache_id,
				$tpl->compile_id,
				$tpl->caching ? \Smarty\Template::CACHING_NOCACHE_CODE : 0,
				$tpl->cache_lifetime,
				[],
				null,
				$currentDir
			);
		}
	}

	/**
	 * \Smarty\Runtime\Block constructor.
	 * - if outer level {block} of child template ($state === 1) save it as child root block
	 * - otherwise process inheritance and render
	 *
	 * @param \Smarty\Template $tpl
	 * @param                           $className
	 * @param string $name
	 * @param int|null $tplIndex index of outer level {block} if nested
	 *
	 * @throws \Smarty\Exception
	 */
	public function instanceBlock(Template $tpl, $className, $name, $tplIndex = null) {
		$block = new $className($name, isset($tplIndex) ? $tplIndex : $this->tplIndex);
		if (isset($this->childRoot[$name])) {
			$block->child = $this->childRoot[$name];
		}
		if ($this->state === 1) {
			$this->childRoot[$name] = $block;
			return;
		}
		// make sure we got child block of child template of current block
		while ($block->child && $block->child->child && $block->tplIndex <= $block->child->tplIndex) {
			$block->child = $block->child->child;
		}
		$this->processBlock($tpl, $block);
	}

	/**
	 * Goto child block or render this
	 *
	 * @param Template $tpl
	 * @param \Smarty\Runtime\Block $block
	 * @param \Smarty\Runtime\Block|null $parent
	 *
	 * @throws Exception
	 */
	private function processBlock(
		Template              $tpl,
		\Smarty\Runtime\Block $block,
		?\Smarty\Runtime\Block $parent = null
	) {
		if ($block->hide && !isset($block->child)) {
			return;
		}
		if (isset($block->child) && $block->child->hide && !isset($block->child->child)) {
			$block->child = null;
		}
		$block->parent = $parent;
		if ($block->append && !$block->prepend && isset($parent)) {
			$this->callParent($tpl, $block, '\'{block append}\'');
		}
		if ($block->callsChild || !isset($block->child) || ($block->child->hide && !isset($block->child->child))) {
			$this->callBlock($block, $tpl);
		} else {
			$this->processBlock($tpl, $block->child, $block);
		}
		if ($block->prepend && isset($parent)) {
			$this->callParent($tpl, $block, '{block prepend}');
			if ($block->append) {
				if ($block->callsChild || !isset($block->child)
					|| ($block->child->hide && !isset($block->child->child))
				) {
					$this->callBlock($block, $tpl);
				} else {
					$this->processBlock($tpl, $block->child, $block);
				}
			}
		}
		$block->parent = null;
	}

	/**
	 * Render child on \$smarty.block.child
	 *
	 * @param Template $tpl
	 * @param \Smarty\Runtime\Block $block
	 *
	 * @return null|string block content
	 * @throws Exception
	 */
	public function callChild(Template $tpl, \Smarty\Runtime\Block $block) {
		if (isset($block->child)) {
			$this->processBlock($tpl, $block->child, $block);
		}
	}

	/**
	 * Render parent block on \$smarty.block.parent or {block append/prepend}
	 *
	 * @param Template $tpl
	 * @param \Smarty\Runtime\Block $block
	 * @param string $tag
	 *
	 * @return null|string  block content
	 * @throws Exception
	 */
	public function callParent(Template $tpl, \Smarty\Runtime\Block $block) {
		if (isset($block->parent)) {
			$this->callBlock($block->parent, $tpl);
		} else {
			throw new Exception("inheritance: illegal '{\$smarty.block.parent}' used in child template '" .
				"{$tpl->getInheritance()->sources[$block->tplIndex]->getResourceName()}' block '{$block->name}'");
		}
	}

	/**
	 * render block
	 *
	 * @param \Smarty\Runtime\Block $block
	 * @param Template $tpl
	 */
	public function callBlock(\Smarty\Runtime\Block $block, Template $tpl) {
		$this->sourceStack[] = $tpl->getSource();
		$tpl->setSource($this->sources[$block->tplIndex]);
		$block->callBlock($tpl);
		$tpl->setSource(array_pop($this->sourceStack));
	}
}
