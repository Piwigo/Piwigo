<?php
/**
 * Smarty Internal Compile Plugin Base
 * @author     Uwe Tews
 */
namespace Smarty\Compile;

use Smarty\Compiler\Template;
use Smarty\Data;
use Smarty\Exception;

/**
 * This class does extend all internal compile plugins
 *


 */
abstract class Base implements CompilerInterface {

	/**
	 * Array of names of required attribute required by tag
	 *
	 * @var array
	 */
	protected $required_attributes = [];

	/**
	 * Array of names of optional attribute required by tag
	 * use array('_any') if there is no restriction of attributes names
	 *
	 * @var array
	 */
	protected $optional_attributes = [];

	/**
	 * Shorttag attribute order defined by its names
	 *
	 * @var array
	 */
	protected $shorttag_order = [];

	/**
	 * Array of names of valid option flags
	 *
	 * @var array
	 */
	protected $option_flags = ['nocache'];
	/**
	 * @var bool
	 */
	protected $cacheable = true;

	public function isCacheable(): bool {
		return $this->cacheable;
	}

	/**
	 * Converts attributes into parameter array strings
	 *
	 * @param array $_attr
	 *
	 * @return array
	 */
	protected function formatParamsArray(array $_attr): array {
		$_paramsArray = [];
		foreach ($_attr as $_key => $_value) {
			$_paramsArray[] = var_export($_key, true) . "=>" . $_value;
		}
		return $_paramsArray;
	}

	/**
	 * This function checks if the attributes passed are valid
	 * The attributes passed for the tag to compile are checked against the list of required and
	 * optional attributes. Required attributes must be present. Optional attributes are check against
	 * the corresponding list. The keyword '_any' specifies that any attribute will be accepted
	 * as valid
	 *
	 * @param object $compiler compiler object
	 * @param array $attributes attributes applied to the tag
	 *
	 * @return array  of mapped attributes for further processing
	 */
	protected function getAttributes($compiler, $attributes) {
		$_indexed_attr = [];
		$options = array_fill_keys($this->option_flags, true);
		foreach ($attributes as $key => $mixed) {
			// shorthand ?
			if (!is_array($mixed)) {
				// options flag ?
				if (isset($options[trim($mixed, '\'"')])) {
					$_indexed_attr[trim($mixed, '\'"')] = true;
					// shorthand attribute ?
				} elseif (isset($this->shorttag_order[$key])) {
					$_indexed_attr[$this->shorttag_order[$key]] = $mixed;
				} else {
					// too many shorthands
					$compiler->trigger_template_error('too many shorthand attributes', null, true);
				}
				// named attribute
			} else {
				foreach ($mixed as $k => $v) {
					// options flag?
					if (isset($options[$k])) {
						if (is_bool($v)) {
							$_indexed_attr[$k] = $v;
						} else {
							if (is_string($v)) {
								$v = trim($v, '\'" ');
							}

							// Mapping array for boolean option value
							static $optionMap = [1 => true, 0 => false, 'true' => true, 'false' => false];

							if (isset($optionMap[$v])) {
								$_indexed_attr[$k] = $optionMap[$v];
							} else {
								$compiler->trigger_template_error(
									"illegal value '" . var_export($v, true) .
									"' for options flag '{$k}'",
									null,
									true
								);
							}
						}
						// must be named attribute
					} else {
						$_indexed_attr[$k] = $v;
					}
				}
			}
		}
		// check if all required attributes present
		foreach ($this->required_attributes as $attr) {
			if (!isset($_indexed_attr[$attr])) {
				$compiler->trigger_template_error("missing '{$attr}' attribute", null, true);
			}
		}
		// check for not allowed attributes
		if ($this->optional_attributes !== ['_any']) {
			$allowedAttributes = array_fill_keys(
				array_merge(
					$this->required_attributes,
					$this->optional_attributes,
					$this->option_flags
				),
				true
			);
			foreach ($_indexed_attr as $key => $dummy) {
				if (!isset($allowedAttributes[$key]) && $key !== 0) {
					$compiler->trigger_template_error("unexpected '{$key}' attribute", null, true);
				}
			}
		}
		// default 'false' for all options flags not set
		foreach ($this->option_flags as $flag) {
			if (!isset($_indexed_attr[$flag])) {
				$_indexed_attr[$flag] = false;
			}
		}

		return $_indexed_attr;
	}

	/**
	 * Push opening tag name on stack
	 * Optionally additional data can be saved on stack
	 *
	 * @param Template $compiler compiler object
	 * @param string $openTag the opening tag's name
	 * @param mixed $data optional data saved
	 */
	protected function openTag(Template $compiler, $openTag, $data = null) {
		$compiler->openTag($openTag, $data);
	}

	/**
	 * Pop closing tag
	 * Raise an error if this stack-top doesn't match with expected opening tags
	 *
	 * @param Template $compiler compiler object
	 * @param array|string $expectedTag the expected opening tag names
	 *
	 * @return mixed        any type the opening tag's name or saved data
	 */
	protected function closeTag(Template $compiler, $expectedTag) {
		return $compiler->closeTag($expectedTag);
	}

	/**
	 * @param mixed $scope
	 * @param array $invalidScopes
	 *
	 * @return int
	 * @throws Exception
	 */
	protected function convertScope($scope): int {

		static $scopes = [
			'local'    => Data::SCOPE_LOCAL,    // current scope
			'parent' => Data::SCOPE_PARENT,     // parent scope (definition unclear)
			'tpl_root' => Data::SCOPE_TPL_ROOT, // highest template (keep going up until parent is not a template)
			'root'     => Data::SCOPE_ROOT,     // highest scope (definition unclear)
			'global' => Data::SCOPE_GLOBAL,     // smarty object

			'smarty' => Data::SCOPE_SMARTY,     // @deprecated alias of 'global'
		];

		$_scopeName = trim($scope, '\'"');
		if (is_numeric($_scopeName) && in_array($_scopeName, $scopes)) {
			return (int) $_scopeName;
		}

		if (isset($scopes[$_scopeName])) {
			return $scopes[$_scopeName];
		}

		$err = var_export($_scopeName, true);
		throw new Exception("illegal value '{$err}' for \"scope\" attribute");
	}

	/**
	 * Compiles code for the tag
	 *
	 * @param array                                 $args      array with attributes from parser
	 * @param Template $compiler  compiler object
	 * @param array                                 $parameter array with compilation parameter
	 *
	 * @return string compiled code as a string
	 * @throws \Smarty\CompilerException
	 */
	abstract public function compile($args, Template $compiler, $parameter = array(), $tag = null, $function = null): string;
}
