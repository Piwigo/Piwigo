<?php
/*
 * This file is part of Smarty.
 *
 * (c) 2015 Uwe Tews
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Smarty Internal Plugin Compile Block Parent Class
 *
 * @author Uwe Tews <uwe.tews@googlemail.com>
 */
class Smarty_Internal_Compile_Block_Child extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $option_flags = array();

    /**
     * Saved compiler object
     *
     * @var Smarty_Internal_TemplateCompilerBase
     */
    public $compiler = null;

    /**
     * Compiles code for the {block_parent} tag
     *
     * @param  array                                 $args      array with attributes from parser
     * @param  \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                 $parameter array with compilation parameter
     *
     * @return bool true
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        if (!isset($compiler->_cache[ 'blockNesting' ])) {
            $compiler->trigger_template_error(' tag {$smarty.block.child} used outside {block} tags ',
                                              $compiler->parser->lex->taglineno);
        }
        $compiler->has_code = true;
        $compiler->suppressNocacheProcessing = true;
        $compiler->_cache[ 'blockParams' ][ $compiler->_cache[ 'blockNesting' ] ][ 'callsChild' ] = 'true';
        $output = "<?php \n\$_smarty_tpl->inheritance->callChild(\$_smarty_tpl, \$this);\n?>\n";
        return $output;
    }
}