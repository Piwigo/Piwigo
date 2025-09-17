<?php

namespace Smarty\Extension;

use Smarty\BlockHandler\BlockHandlerInterface;
use Smarty\Compile\CompilerInterface;
use Smarty\Compile\Modifier\ModifierCompilerInterface;
use Smarty\FunctionHandler\FunctionHandlerInterface;

interface ExtensionInterface {

	/**
	 * Either return \Smarty\Compile\CompilerInterface that will compile the given $tag or
	 * return null to indicate that you do not know how to handle this $tag. (Another Extension might.)
	 *
	 * @param string $tag
	 * @return CompilerInterface|null
	 */
	public function getTagCompiler(string $tag): ?CompilerInterface;

	/**
	 * Either return \Smarty\Compile\Modifier\ModifierCompilerInterface that will compile the given $modifier or
	 * return null to indicate that you do not know how to handle this $modifier. (Another Extension might.)
	 *
	 * @param string $modifier
	 * @return ModifierCompilerInterface|null
	 */
	public function getModifierCompiler(string $modifier): ?ModifierCompilerInterface;

	/**
	 * Either return \Smarty\FunctionHandler\FunctionHandlerInterface that will handle the given $functionName or
	 * return null to indicate that you do not know how to handle this $functionName. (Another Extension might.)
	 *
	 * @param string $functionName
	 * @return FunctionHandlerInterface|null
	 */
	public function getFunctionHandler(string $functionName): ?FunctionHandlerInterface;

	/**
	 * Either return \Smarty\BlockHandler\BlockHandlerInterface that will handle the given $blockTagName or return null
	 * to indicate that you do not know how to handle this $blockTagName. (Another Extension might.)
	 *
	 * @param string $blockTagName
	 * @return BlockHandlerInterface|null
	 */
	public function getBlockHandler(string $blockTagName): ?BlockHandlerInterface;

	/**
	 * Either return a callable that takes at least 1 parameter (a string) and returns a modified string or return null
	 * to indicate that you do not know how to handle this $modifierName. (Another Extension might.)
	 *
	 * The callable can accept additional optional parameters.
	 *
	 * @param string $modifierName
	 * @return callable|null
	 */
	public function getModifierCallback(string $modifierName);

	/**
	 * Return a list of prefilters that will all be applied, in sequence.
	 * Template prefilters can be used to preprocess templates before they are compiled.
	 *
	 * @return \Smarty\Filter\FilterInterface[]
	 */
	public function getPreFilters(): array;

	/**
	 * Return a list of postfilters that will all be applied, in sequence.
	 * Template postfilters can be used to process compiled template code (so, after the compilation).
	 *
	 * @return \Smarty\Filter\FilterInterface[]
	 */
	public function getPostFilters(): array;

	/**
	 * Return a list of outputfilters that will all be applied, in sequence.
	 * Template outputfilters can be used to change template output just before it is rendered.
	 *
	 * @return \Smarty\Filter\FilterInterface[]
	 */
	public function getOutputFilters(): array;

}