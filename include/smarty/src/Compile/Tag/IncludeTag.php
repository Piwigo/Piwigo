<?php
/**
 * Smarty Internal Plugin Compile Include
 * Compiles the {include} tag
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
use Smarty\Compiler\Template;
use Smarty\Data;
use Smarty\Smarty;
use Smarty\Template\Compiled;

/**
 * Smarty Internal Plugin Compile Include Class
 *


 */
class IncludeTag extends Base {

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	protected $required_attributes = ['file'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	protected $shorttag_order = ['file'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	protected $option_flags = ['nocache', 'inline', 'caching'];

	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see BaseCompiler
	 */
	protected $optional_attributes = ['_any'];

	/**
	 * Compiles code for the {include} tag
	 *
	 * @param array $args array with attributes from parser
	 * @param Template $compiler compiler object
	 *
	 * @return string
	 * @throws \Exception
	 * @throws \Smarty\CompilerException
	 * @throws \Smarty\Exception
	 */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = [], $tag = null, $function = null): string
	{
		$uid = $t_hash = null;
		// check and get attributes
		$_attr = $this->getAttributes($compiler, $args);
		$fullResourceName = $source_resource = $_attr['file'];
		$variable_template = false;
		// parse resource_name
		if (preg_match('/^([\'"])(([A-Za-z0-9_\-]{2,})[:])?(([^$()]+)|(.+))\1$/', $source_resource, $match)) {
			$type = !empty($match[3]) ? $match[3] : $compiler->getTemplate()->getSmarty()->default_resource_type;
			$name = !empty($match[5]) ? $match[5] : $match[6];
			$handler = \Smarty\Resource\BasePlugin::load($compiler->getSmarty(), $type);
			if ($handler->recompiled) {
				$variable_template = true;
			}
			if (!$variable_template) {
				if ($type !== 'string') {
					$fullResourceName = "{$type}:{$name}";
					$compiled = $compiler->getParentCompiler()->getTemplate()->getCompiled();
					if (isset($compiled->includes[$fullResourceName])) {
						$compiled->includes[$fullResourceName]++;
					} else {
						if ("{$compiler->getTemplate()->getSource()->type}:{$compiler->getTemplate()->getSource()->name}" ==
							$fullResourceName
						) {
							// recursive call of current template
							$compiled->includes[$fullResourceName] = 2;
						} else {
							$compiled->includes[$fullResourceName] = 1;
						}
					}
					$fullResourceName = $match[1] . $fullResourceName . $match[1];
				}
			}
		}
		// scope setup
		$_scope = isset($_attr['scope']) ? $this->convertScope($_attr['scope']) : 0;

		// assume caching is off
		$_caching = Smarty::CACHING_OFF;

		// caching was on and {include} is not in nocache mode
		if ($compiler->getTemplate()->caching && !$compiler->isNocacheActive()) {
			$_caching = \Smarty\Template::CACHING_NOCACHE_CODE;
		}

		/*
		* if the {include} tag provides individual parameter for caching or compile_id
		* the subtemplate must not be included into the common cache file and is treated like
		* a call in nocache mode.
		*
		*/

		$call_nocache = $compiler->isNocacheActive();
		if ($_attr['nocache'] !== true && $_attr['caching']) {
			$_caching = $_new_caching = (int)$_attr['caching'];
			$call_nocache = true;
		} else {
			$_new_caching = Smarty::CACHING_LIFETIME_CURRENT;
		}
		if (isset($_attr['cache_lifetime'])) {
			$_cache_lifetime = $_attr['cache_lifetime'];
			$call_nocache = true;
			$_caching = $_new_caching;
		} else {
			$_cache_lifetime = '$_smarty_tpl->cache_lifetime';
		}
		if (isset($_attr['cache_id'])) {
			$_cache_id = $_attr['cache_id'];
			$call_nocache = true;
			$_caching = $_new_caching;
		} else {
			$_cache_id = '$_smarty_tpl->cache_id';
		}

		// assign attribute
		if (isset($_attr['assign'])) {
			// output will be stored in a smarty variable instead of being displayed
			if ($_assign = $compiler->getId($_attr['assign'])) {
				$_assign = "'{$_assign}'";
				if ($call_nocache) {
					// create nocache var to make it know for further compiling
					$compiler->setNocacheInVariable($_attr['assign']);
				}
			} else {
				$_assign = $_attr['assign'];
			}
		}
		$has_compiled_template = false;

		// delete {include} standard attributes
		unset($_attr['file'], $_attr['assign'], $_attr['cache_id'], $_attr['cache_lifetime'], $_attr['nocache'], $_attr['caching'], $_attr['scope'], $_attr['inline']);
		// remaining attributes must be assigned as smarty variable
		$_vars = 'array()';
		if (!empty($_attr)) {
			$_pairs = [];
			// create variables
			foreach ($_attr as $key => $value) {
				$_pairs[] = "'$key'=>$value";
			}
			$_vars = 'array(' . join(',', $_pairs) . ')';
		}
		if ($call_nocache) {
			$compiler->tag_nocache = true;
		}
		$_output = "<?php ";
		// was there an assign attribute
		if (isset($_assign)) {
			$_output .= "ob_start();\n";
		}
		$_output .= "\$_smarty_tpl->renderSubTemplate({$fullResourceName}, $_cache_id, \$_smarty_tpl->compile_id, " .
			"$_caching, $_cache_lifetime, $_vars, (int) {$_scope}, \$_smarty_current_dir);\n";
		if (isset($_assign)) {
			$_output .= "\$_smarty_tpl->assign({$_assign}, ob_get_clean(), false, {$_scope});\n";
		}
		$_output .= "?>";
		return $_output;
	}

}
