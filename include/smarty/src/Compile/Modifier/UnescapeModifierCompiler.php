<?php
namespace Smarty\Compile\Modifier;

/**
 * Smarty unescape modifier plugin
 * Type:     modifier
 * Name:     unescape
 * Purpose:  unescape html entities
 *
 * @author Rodney Rehm
 */

class UnescapeModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		$esc_type = $this->literal_compiler_param($params, 1, 'html');

		if (!isset($params[ 2 ])) {
			$params[ 2 ] = '\'' . addslashes(\Smarty\Smarty::$_CHARSET) . '\'';
		}

		switch ($esc_type) {
			case 'entity':
			case 'htmlall':
				return 'html_entity_decode(mb_convert_encoding(' . $params[ 0 ] . ', ' . $params[ 2 ] . ', \'UTF-8\'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, ' . $params[ 2 ] . ')';
			case 'html':
				return 'htmlspecialchars_decode(' . $params[ 0 ] . ', ENT_QUOTES)';
			case 'url':
				return 'rawurldecode(' . $params[ 0 ] . ')';
			default:
				return $params[ 0 ];
		}
	}
}