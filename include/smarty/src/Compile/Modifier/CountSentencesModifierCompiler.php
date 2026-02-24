<?php
namespace Smarty\Compile\Modifier;
/**
 * Smarty count_sentences modifier plugin
 * Type:     modifier
 * Name:     count_sentences
 * Purpose:  count the number of sentences in a text
 *
 * @author Uwe Tews
 */

class CountSentencesModifierCompiler extends Base {

	public function compile($params, \Smarty\Compiler\Template $compiler) {
		// find periods, question marks, exclamation marks with a word before but not after.
		return 'preg_match_all("#\w[\.\?\!](\W|$)#S' . \Smarty\Smarty::$_UTF8_MODIFIER . '", ' . $params[ 0 ] . ', $tmp)';
	}

}