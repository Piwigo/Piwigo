<?php

namespace Smarty\Extension;

use Smarty\FunctionHandler\FunctionHandlerInterface;

class Base implements ExtensionInterface {

	public function getTagCompiler(string $tag): ?\Smarty\Compile\CompilerInterface {
		return null;
	}

	public function getModifierCompiler(string $modifier): ?\Smarty\Compile\Modifier\ModifierCompilerInterface {
		return null;
	}

	public function getFunctionHandler(string $functionName): ?\Smarty\FunctionHandler\FunctionHandlerInterface {
		return null;
	}

	public function getBlockHandler(string $blockTagName): ?\Smarty\BlockHandler\BlockHandlerInterface {
		return null;
	}

	public function getModifierCallback(string $modifierName) {
		return null;
	}

	public function getPreFilters(): array {
		return [];
	}

	public function getPostFilters(): array {
		return [];
	}

	public function getOutputFilters(): array {
		return [];
	}

}