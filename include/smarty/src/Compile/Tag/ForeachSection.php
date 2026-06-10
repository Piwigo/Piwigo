<?php
/**
 * Smarty Internal Plugin Compile ForeachSection
 * Shared methods for {foreach} {section} tags
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;

/**
 * Smarty Internal Plugin Compile ForeachSection Class
 *


 */
abstract class ForeachSection extends Base {

	/**
	 * Name of this tag
	 *
	 * @var string
	 */
	protected $tagName = '';

	/**
	 * Valid properties of $smarty.xxx variable
	 *
	 * @var array
	 */
	protected $nameProperties = [];

	/**
	 * {section} tag has no item properties
	 *
	 * @var array
	 */
	protected $itemProperties = null;

	/**
	 * {section} tag has always name attribute
	 *
	 * @var bool
	 */
	protected $isNamed = true;

	/**
	 * @var array
	 */
	protected $matchResults = [];

	/**
	 * Preg search pattern
	 *
	 * @var string
	 */
	private $propertyPreg = '';

	/**
	 * Offsets in preg match result
	 *
	 * @var array
	 */
	private $resultOffsets = [];

	/**
	 * Start offset
	 *
	 * @var int
	 */
	private $startOffset = 0;

	/**
	 * Scan sources for used tag attributes
	 *
	 * @param array $attributes
	 * @param \Smarty\Compiler\Template $compiler
	 *
	 * @throws \Smarty\Exception
	 */
	protected function scanForProperties($attributes, \Smarty\Compiler\Template $compiler) {
		$this->propertyPreg = '~(';
		$this->startOffset = 1;
		$this->resultOffsets = [];
		$this->matchResults = ['named' => [], 'item' => []];
		if (isset($attributes['name'])) {
			$this->buildPropertyPreg(true, $attributes);
		}
		if (isset($this->itemProperties)) {
			if ($this->isNamed) {
				$this->propertyPreg .= '|';
			}
			$this->buildPropertyPreg(false, $attributes);
		}
		$this->propertyPreg .= ')\W~i';
		// Template source
		$this->matchTemplateSource($compiler);
		// Parent template source
		$this->matchParentTemplateSource($compiler);
	}

	/**
	 * Build property preg string
	 *
	 * @param bool $named
	 * @param array $attributes
	 */
	private function buildPropertyPreg($named, $attributes) {
		if ($named) {
			$this->resultOffsets['named'] = $this->startOffset = $this->startOffset + 3;
			$this->propertyPreg .= "(([\$]smarty[.]{$this->tagName}[.]" .
				($this->tagName === 'section' ? "|[\[]\s*" : '') .
				"){$attributes['name']}[.](";
			$properties = $this->nameProperties;
		} else {
			$this->resultOffsets['item'] = $this->startOffset = $this->startOffset + 2;
			$this->propertyPreg .= "([\$]{$attributes['item']}[@](";
			$properties = $this->itemProperties;
		}
		$propName = reset($properties);
		while ($propName) {
			$this->propertyPreg .= "{$propName}";
			$propName = next($properties);
			if ($propName) {
				$this->propertyPreg .= '|';
			}
		}
		$this->propertyPreg .= '))';
	}

	/**
	 * Find matches in source string
	 *
	 * @param string $source
	 */
	private function matchProperty($source) {
		preg_match_all($this->propertyPreg, $source, $match);
		foreach ($this->resultOffsets as $key => $offset) {
			foreach ($match[$offset] as $m) {
				if (!empty($m)) {
					$this->matchResults[$key][smarty_strtolower_ascii($m)] = true;
				}
			}
		}
	}

	/**
	 * Find matches in template source
	 *
	 * @param \Smarty\Compiler\Template $compiler
	 */
	private function matchTemplateSource(\Smarty\Compiler\Template $compiler) {
		$this->matchProperty($compiler->getParser()->lex->data);
	}

	/**
	 * Find matches in all parent template source
	 *
	 * @param \Smarty\Compiler\Template $compiler
	 *
	 * @throws \Smarty\Exception
	 */
	private function matchParentTemplateSource(\Smarty\Compiler\Template $compiler) {
		// search parent compiler template source
		$nextCompiler = $compiler;
		while ($nextCompiler !== $nextCompiler->getParentCompiler()) {
			$nextCompiler = $nextCompiler->getParentCompiler();
			if ($compiler !== $nextCompiler) {
				// get template source
				$_content = $nextCompiler->getTemplate()->getSource()->getContent();
				if ($_content !== '') {
					// run pre filter if required
					$_content = $nextCompiler->getSmarty()->runPreFilters($_content,	$nextCompiler->getTemplate());
					$this->matchProperty($_content);
				}
			}
		}
	}

	/**
	 * Compiles code for the {$smarty.foreach.xxx} or {$smarty.section.xxx}tag
	 *
	 * @param \Smarty\Compiler\Template $compiler compiler object
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 * @throws \Smarty\CompilerException
	 */
	public function compileSpecialVariable(\Smarty\Compiler\Template $compiler, $parameter) {
		$tag = smarty_strtolower_ascii(trim($parameter[0], '"\''));
		$name = isset($parameter[1]) ? $compiler->getId($parameter[1]) : false;
		if (!$name) {
			$compiler->trigger_template_error("missing or illegal \$smarty.{$tag} name attribute", null, true);
		}
		$property = isset($parameter[2]) ? smarty_strtolower_ascii($compiler->getId($parameter[2])) : false;
		if (!$property || !in_array($property, $this->nameProperties)) {
			$compiler->trigger_template_error("missing or illegal \$smarty.{$tag} property attribute", null, true);
		}
		$tagVar = "'__smarty_{$tag}_{$name}'";
		return "(\$_smarty_tpl->getValue({$tagVar})['{$property}'] ?? null)";
	}
}
