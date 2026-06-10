<?php

namespace Smarty\Compile\Tag;

use Smarty\Compile\Base;
use Smarty\Smarty;

/**
 * Smarty Internal Plugin Compile Assign
 * Compiles the {assign} tag
 *


 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Assign Class
 *


 */
class Assign extends Base
{
	/**
	 * @inheritdoc
	 */
	protected $required_attributes = ['var', 'value'];

	/**
	 * @inheritdoc
	 */
	protected $optional_attributes = ['scope'];

	/**
	 * @inheritdoc
	 */
	protected $shorttag_order = ['var', 'value'];

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see BasePlugin
     */
    protected $option_flags = array('nocache', 'noscope');

    /**
     * Compiles code for the {assign} tag
     *
     * @param array                                 $args      array with attributes from parser
     * @param \Smarty\Compiler\Template $compiler  compiler object
     * @param array                                 $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \Smarty\CompilerException
     */
	public function compile($args, \Smarty\Compiler\Template $compiler, $parameter = array(), $tag = null, $function = null): string
	{

        $_nocache = false;
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        if ($_var = $compiler->getId($_attr[ 'var' ])) {
            $_var = "'{$_var}'";
        } else {
            $_var = $_attr[ 'var' ];
        }
        if ($compiler->tag_nocache || $compiler->isNocacheActive()) {
            $_nocache = true;
            // create nocache var to make it know for further compiling
            $compiler->setNocacheInVariable($_attr[ 'var' ]);
        }
        // scope setup
        if ($_attr[ 'noscope' ]) {
            $_scope = -1;
        } else {
            $_scope = isset($_attr['scope']) ? $this->convertScope($_attr['scope']) : null;
        }

        if (isset($parameter[ 'smarty_internal_index' ])) {
            $output =
                "<?php \$_tmp_array = \$_smarty_tpl->getValue({$_var}) ?? [];\n";
            $output .= "if (!(is_array(\$_tmp_array) || \$_tmp_array instanceof ArrayAccess)) {\n";
            $output .= "settype(\$_tmp_array, 'array');\n";
            $output .= "}\n";
            $output .= "\$_tmp_array{$parameter['smarty_internal_index']} = {$_attr['value']};\n";
            $output .= "\$_smarty_tpl->assign({$_var}, \$_tmp_array, " . var_export($_nocache, true) . ", " . var_export($_scope, true) . ");?>";
        } else {
            $output = "<?php \$_smarty_tpl->assign({$_var}, {$_attr['value']}, " . var_export($_nocache, true) . ", " . var_export($_scope, true) . ");?>";
        }
        return $output;
    }
}
