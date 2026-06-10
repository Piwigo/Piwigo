<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty count_words modifier plugin
 * Type:     modifier
 * Name:     count_words
 * Purpose:  count the number of words in a text
 *
 * @author Uwe Tews
 */

class CountWordsModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		// expression taken from http://de.php.net/manual/en/function.str-word-count.php#85592
		return 'preg_match_all(\'/\p{L}[\p{L}\p{Mn}\p{Pd}\\\'\x{2019}]*/' . \Smarty\Smarty::$_UTF8_MODIFIER . '\', ' .
			$params[ 0 ] . ', $tmp)';
	}

}