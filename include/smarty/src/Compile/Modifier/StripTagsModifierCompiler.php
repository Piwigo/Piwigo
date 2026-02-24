<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty strip_tags modifier plugin
 * Type:     modifier
 * Name:     strip_tags
 * Purpose:  strip html tags from text
 *
 * @author Uwe Tews
 */

class StripTagsModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		if (!isset($params[ 1 ]) || $params[ 1 ] === true || trim($params[ 1 ], '"') === 'true') {
			return "preg_replace('!<[^>]*?>!', ' ', (string) {$params[0]})";
		} else {
			return 'strip_tags((string) ' . $params[ 0 ] . ')';
		}
	}

}
