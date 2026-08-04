<?php

namespace Smarty\Compile;

class DefaultHandlerBlockCompiler extends BlockCompiler {
	/**
	 * @inheritDoc
	 */
	protected function getIsCallableCode($tag, $function): string {
		return "\$_smarty_tpl->getSmarty()->getRuntime('DefaultPluginHandler')->hasPlugin(" .
			var_export($function, true) . ", 'block')";
	}

	/**
	 * @inheritDoc
	 */
	protected function getFullCallbackCode($tag, $function): string {
		return "\$_smarty_tpl->getSmarty()->getRuntime('DefaultPluginHandler')->getCallback(" .
			var_export($function, true) . ", 'block')";
	}

	/**
	 * @inheritDoc
	 */
	protected function blockIsCacheable(\Smarty\Smarty $smarty, $function): bool {
		return true;
	}

}