<?php

namespace Smarty\Extension;

class CoreExtension extends Base {
	public function getTagCompiler(string $tag): ?\Smarty\Compile\CompilerInterface {
		switch ($tag) {
			case 'append': return new \Smarty\Compile\Tag\Append();
			case 'assign': return new \Smarty\Compile\Tag\Assign();
			case 'block': return new \Smarty\Compile\Tag\Block();
			case 'blockclose': return new \Smarty\Compile\Tag\BlockClose();
			case 'break': return new \Smarty\Compile\Tag\BreakTag();
			case 'call': return new \Smarty\Compile\Tag\Call();
			case 'capture': return new \Smarty\Compile\Tag\Capture();
			case 'captureclose': return new \Smarty\Compile\Tag\CaptureClose();
			case 'config_load': return new \Smarty\Compile\Tag\ConfigLoad();
			case 'continue': return new \Smarty\Compile\Tag\ContinueTag();
			case 'debug': return new \Smarty\Compile\Tag\Debug();
			case 'eval': return new \Smarty\Compile\Tag\EvalTag();
			case 'extends': return new \Smarty\Compile\Tag\ExtendsTag();
			case 'for': return new \Smarty\Compile\Tag\ForTag();
			case 'foreach': return new \Smarty\Compile\Tag\ForeachTag();
			case 'foreachelse': return new \Smarty\Compile\Tag\ForeachElse();
			case 'foreachclose': return new \Smarty\Compile\Tag\ForeachClose();
			case 'forelse': return new \Smarty\Compile\Tag\ForElse();
			case 'forclose': return new \Smarty\Compile\Tag\ForClose();
			case 'function': return new \Smarty\Compile\Tag\FunctionTag();
			case 'functionclose': return new \Smarty\Compile\Tag\FunctionClose();
			case 'if': return new \Smarty\Compile\Tag\IfTag();
			case 'else': return new \Smarty\Compile\Tag\ElseTag();
			case 'elseif': return new \Smarty\Compile\Tag\ElseIfTag();
			case 'ifclose': return new \Smarty\Compile\Tag\IfClose();
			case 'include': return new \Smarty\Compile\Tag\IncludeTag();
			case 'ldelim': return new \Smarty\Compile\Tag\Ldelim();
			case 'rdelim': return new \Smarty\Compile\Tag\Rdelim();
			case 'nocache': return new \Smarty\Compile\Tag\Nocache();
			case 'nocacheclose': return new \Smarty\Compile\Tag\NocacheClose();
			case 'section': return new \Smarty\Compile\Tag\Section();
			case 'sectionelse': return new \Smarty\Compile\Tag\SectionElse();
			case 'sectionclose': return new \Smarty\Compile\Tag\SectionClose();
			case 'setfilter': return new \Smarty\Compile\Tag\Setfilter();
			case 'setfilterclose': return new \Smarty\Compile\Tag\SetfilterClose();
			case 'while': return new \Smarty\Compile\Tag\WhileTag();
			case 'whileclose': return new \Smarty\Compile\Tag\WhileClose();
		}
		return null;
	}

}