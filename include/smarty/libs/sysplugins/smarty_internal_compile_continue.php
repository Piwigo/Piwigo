<?php
/**
 * Smarty Internal Plugin Compile Continue
 * Compiles the {continue} tag
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Continue Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Continue extends Smarty_Internal_Compile_Break
{

    /**
     * Compiles code for the {continue} tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        list($levels, $foreachLevels) = $this->checkLevels($args, $compiler, 'continue');
        $output = "<?php\n";
        if ($foreachLevels > 1) {
            /* @var Smarty_Internal_Compile_Foreach $foreachCompiler */
            $foreachCompiler = $compiler->getTagCompiler('foreach');
            $output .= $foreachCompiler->compileRestore($foreachLevels - 1);
        }
        $output .= "continue {$levels};?>";
        return $output;
    }
}
