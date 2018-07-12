<?php
/**
 * Smarty Internal Plugin Compile Break
 * Compiles the {break} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Break Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Break extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('levels');

    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array('levels');

    /**
     * Compiles code for the {break} tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        list($levels, $foreachLevels) = $this->checkLevels($args, $compiler);
        $output = "<?php\n";
        if ($foreachLevels) {
            /* @var Smarty_Internal_Compile_Foreach $foreachCompiler */
            $foreachCompiler = $compiler->getTagCompiler('foreach');
            $output .= $foreachCompiler->compileRestore($foreachLevels);
        }
        $output .= "break {$levels};?>";
        return $output;
    }

    /**
     * check attributes and return array of break and foreach levels
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     * @param  string                               $tag      tag name
     *
     * @return array
     * @throws \SmartyCompilerException
     */
    public function checkLevels($args, Smarty_Internal_TemplateCompilerBase $compiler, $tag = 'break')
    {
        static $_is_loopy = array('for' => true, 'foreach' => true, 'while' => true, 'section' => true);
        // check and get attributes
        $_attr = $this->getAttributes($compiler, $args);

        if ($_attr[ 'nocache' ] === true) {
            $compiler->trigger_template_error('nocache option not allowed', null, true);
        }

        if (isset($_attr[ 'levels' ])) {
            if (!is_numeric($_attr[ 'levels' ])) {
                $compiler->trigger_template_error('level attribute must be a numeric constant', null, true);
            }
            $levels = $_attr[ 'levels' ];
        } else {
            $levels = 1;
        }
        $level_count = $levels;
        $stack_count = count($compiler->_tag_stack) - 1;
        $foreachLevels = 0;
        $lastTag = '';
        while ($level_count >= 0 && $stack_count >= 0) {
            if (isset($_is_loopy[ $compiler->_tag_stack[ $stack_count ][ 0 ] ])) {
                $lastTag = $compiler->_tag_stack[ $stack_count ][ 0 ];
                if ($level_count === 0) {
                    break;
                }
                $level_count --;
                if ($compiler->_tag_stack[ $stack_count ][ 0 ] === 'foreach') {
                    $foreachLevels ++;
                }
            }
            $stack_count --;
        }
        if ($level_count != 0) {
            $compiler->trigger_template_error("cannot {$tag} {$levels} level(s)", null, true);
        }
        if ($lastTag === 'foreach' && $tag === 'break') {
            $foreachLevels --;
        }
        return array($levels, $foreachLevels);
    }
}
